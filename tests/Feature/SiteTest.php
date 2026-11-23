<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\DeliverySlot;
use App\Models\Order;
use App\Models\Product;
use App\Models\Section;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function admin(): User
    {
        return User::where('email', 'admin@crinklenest.com')->firstOrFail();
    }

    public function test_public_pages_render_with_database_content(): void
    {
        foreach (['/', '/about', '/contact', '/menu', '/menu/brownie', '/cart', '/orders', '/login', '/register', '/page/terms-and-conditions', '/page/privacy-policy'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/')->assertSee('Served With Happiness')->assertSee('Red Velvet Brownie')->assertSee('Fresh From Our Oven');
        $this->get('/page/terms-and-conditions')->assertSee('Perishable Courier Protocol');
        $this->get('/page/missing')->assertNotFound();
    }

    public function test_settings_and_sections_drive_the_frontend(): void
    {
        Setting::where('key', 'site_name')->update(['value' => 'Nest Test Bakery']);
        Setting::flush();
        Section::where('key', 'home.hero')->update(['title' => 'Totally New Hero Title']);

        $this->get('/')->assertSee('Nest Test Bakery')->assertSee('Totally New Hero Title');

        Section::where('key', 'home.promo')->update(['is_active' => false]);
        $this->get('/')->assertDontSee('A Little Box Full of Happiness');
    }

    public function test_contact_form_stores_message(): void
    {
        $this->post('/contact', [
            'name' => 'Maya', 'phone' => '+91 90000 00000', 'message' => 'Need 20 brownies',
            'inquiry_type' => 'Corporate Hampers', 'whatsapp_copy' => 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('contact_messages', ['name' => 'Maya', 'inquiry_type' => 'Corporate Hampers', 'whatsapp_copy' => 1]);
    }

    public function test_guest_can_add_to_cart_apply_coupon_and_checkout(): void
    {
        $product = Product::where('name', 'Fudgy Walnut Brownie Slab')->firstOrFail();
        $this->post(route('cart.add', $product))->assertRedirect();
        $this->patch(route('cart.update', $product), ['qty' => 2]);
        $this->post(route('cart.coupon'), ['code' => 'sweetbirthday'])->assertSessionHas('success');

        $this->get('/cart')->assertSee('Fudgy Walnut Brownie Slab')->assertSee('SWEETBIRTHDAY');

        $slot = DeliverySlot::where('is_custom', false)->first();
        $this->post('/checkout', [
            'name' => 'Guest Buyer', 'phone' => '9999999999', 'address' => '1 Lane', 'city' => 'Bangalore',
            'slot_id' => $slot->id, 'payment_method' => 'whatsapp',
        ])->assertRedirect(route('orders.index'))->assertSessionHas('whatsapp_url');

        $order = Order::where('name', 'Guest Buyer')->firstOrFail();
        $this->assertEquals(840, (float) $order->subtotal);
        $this->assertEquals(100, (float) $order->discount);
        $this->assertEquals(790, (float) $order->total); // 840 + 50 delivery - 100
        $this->assertStringStartsWith('CN-', $order->order_number);
        $this->assertCount(1, $order->items);

        // Guest can see and print their own order, the basket is cleared.
        $this->get('/orders')->assertSee($order->order_number);
        $this->get(route('orders.invoice', $order->order_number))->assertOk();
        $this->get('/cart')->assertSee('Your basket is waiting');
    }

    public function test_other_users_cannot_view_someone_elses_invoice(): void
    {
        $order = Order::firstOrFail();
        $this->get(route('orders.invoice', $order->order_number))->assertForbidden();
    }

    public function test_customer_registration_profile_and_addresses(): void
    {
        $this->post('/register', [
            'name' => 'Riya', 'email' => 'riya@example.com', 'password' => 'secret123', 'password_confirmation' => 'secret123',
        ])->assertRedirect(route('profile.index'));

        $user = User::where('email', 'riya@example.com')->firstOrFail();
        $this->assertEquals(50, $user->points); // signup bonus from settings
        $this->get('/profile')->assertOk()->assertSee('Riya');

        $this->put('/profile', ['name' => 'Riya', 'last_name' => 'K', 'email' => 'riya@example.com', 'phone' => '123'])->assertSessionHas('success');
        $this->post('/profile/addresses', ['label' => 'Home', 'line1' => 'Street 1'])->assertSessionHas('success');
        $this->assertTrue($user->addresses()->first()->is_default);

        $diet = Section::where('key', 'profile.dietary')->first()->items->first();
        $this->put('/profile/preferences', ['dietary' => [$diet->id], 'sweetness' => 3]);
        $this->assertEquals([$diet->id], $user->fresh()->pref('dietary'));
    }

    public function test_admin_area_requires_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $customer = User::where('email', 'ananya@example.com')->first();
        $this->actingAs($customer)->get('/admin')->assertForbidden();

        $this->post('/admin/login', ['email' => 'admin@crinklenest.com', 'password' => 'admin12345'])->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_screens_load(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin')->assertOk();
        $this->get('/admin/orders')->assertOk();
        $this->get('/admin/orders/'.Order::first()->id)->assertOk();
        $this->get('/admin/messages')->assertOk();
        foreach (array_keys(\App\Models\Setting::GROUPS) as $g) {
            $this->get('/admin/settings?group='.$g)->assertOk();
        }
        foreach (array_keys(config('admin.resources')) as $res) {
            $this->get("/admin/$res")->assertOk();
            $model = config("admin.resources.$res.model");
            if ($first = $model::first()) {
                $this->get("/admin/$res/".$first->getKey().'/edit')->assertOk();
            }
            if (config("admin.resources.$res.can_create", true)) {
                $this->get("/admin/$res/create")->assertOk();
            }
        }
    }

    public function test_admin_can_create_product_with_uploaded_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->admin());

        $this->post('/admin/products', [
            'name' => 'Lotus Biscoff Brownie', 'price' => '199', 'badge_style' => 'secondary',
            'image_file' => UploadedFile::fake()->image('b.jpg'), 'is_active' => 1, 'is_trending' => 1,
        ])->assertRedirect();

        $p = Product::where('name', 'Lotus Biscoff Brownie')->firstOrFail();
        $this->assertSame('lotus-biscoff-brownie', $p->slug);
        Storage::disk('public')->assertExists($p->image);
        $this->get('/')->assertSee('Lotus Biscoff Brownie');
    }

    public function test_admin_can_edit_section_with_items_and_extra_texts(): void
    {
        $this->actingAs($this->admin());
        $section = Section::where('key', 'home.hero')->with('items')->firstOrFail();
        $first = $section->items->first();
        $second = $section->items->get(1);

        $this->put('/admin/sections/'.$section->id, [
            'name' => $section->name, 'title' => 'Edited Hero', 'is_active' => 1,
            'extra' => [['key' => 'rating_text', 'value' => '★ 5/5'], ['key' => 'pill_title', 'value' => 'Hot Batch']],
            'rows' => [
                ['id' => $first->id, 'icon' => 'cake', 'title' => 'Renamed Chip', 'is_active' => 1],
                ['id' => $second->id, '_delete' => 1],
                ['id' => '', 'icon' => 'star', 'title' => 'Brand New Chip', 'is_active' => 1],
            ],
        ])->assertRedirect();

        $this->get('/')->assertSee('Edited Hero')->assertSee('★ 5/5')->assertSee('Renamed Chip')->assertSee('Brand New Chip')->assertDontSee('Eggless Options');
    }

    public function test_admin_settings_save_and_order_status_awards_points(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/settings', ['group' => 'contact', 'values' => ['phone' => '+91 11111 22222']])->assertRedirect();
        $this->get('/contact')->assertSee('+91 11111 22222');

        $order = Order::where('status', 'baking')->firstOrFail();
        $before = $order->user->points;
        $this->put('/admin/orders/'.$order->id, ['status' => 'delivered', 'payment_status' => 'paid'])->assertRedirect();
        $this->assertEquals($before + (int) floor($order->total / 100 * 10), $order->user->fresh()->points);
    }

    public function test_customer_can_cancel_only_before_preparation_starts(): void
    {
        $customer = User::where('email', 'ananya@example.com')->firstOrFail();
        $this->actingAs($customer);

        // Seeded active order is already "baking" → locked.
        $baking = $customer->orders()->where('status', 'baking')->firstOrFail();
        $this->get('/orders')->assertSee('Cancel unavailable')->assertDontSee('Yes, cancel this order');
        $this->post(route('orders.cancel', $baking->order_number))->assertSessionHas('error');
        $this->assertSame('baking', $baking->fresh()->status);

        // A freshly confirmed order can be cancelled.
        $baking->update(['status' => 'confirmed']);
        $this->get('/orders')->assertSee('Yes, cancel this order');
        $this->get('/profile')->assertSee('Order History')->assertSee('Yes, cancel this order');
        $this->post(route('orders.cancel', $baking->order_number), ['reason' => 'Changed plans'])->assertSessionHas('success');
        $this->assertSame('cancelled', $baking->fresh()->status);
        $this->assertStringContainsString('Changed plans', $baking->fresh()->status_note);
        $this->get('/orders?filter=cancelled')->assertSee($baking->order_number);

        // Delivered orders show no cancel control; other customers can't cancel.
        $delivered = $customer->orders()->where('status', 'delivered')->first();
        $this->post(route('orders.cancel', $delivered->order_number))->assertSessionHas('error');
        $this->actingAs(User::where('email', 'admin@crinklenest.com')->first());
        $other = User::create(['name' => 'Other', 'email' => 'o@example.com', 'password' => 'secret123']);
        $this->actingAs($other)->post(route('orders.cancel', $delivered->order_number))->assertForbidden();
    }

    public function test_admin_can_switch_off_customer_cancellation(): void
    {
        $customer = User::where('email', 'ananya@example.com')->firstOrFail();
        $order = $customer->orders()->where('status', 'baking')->firstOrFail();
        $order->update(['status' => 'confirmed']);
        Setting::where('key', 'allow_customer_cancel')->update(['value' => '0']);
        Setting::flush();

        $this->actingAs($customer)->post(route('orders.cancel', $order->order_number))->assertSessionHas('error');
        $this->assertSame('confirmed', $order->fresh()->status);
    }

    public function test_robots_and_sitemap_are_served_dynamically(): void
    {
        $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')->assertSee('Sitemap: https://crinklenest.com/sitemap.xml');

        $xml = $this->get('/sitemap.xml')->assertOk()->getContent();
        $this->assertStringContainsString('<loc>https://crinklenest.com/</loc>', $xml);
        $this->assertStringContainsString('<loc>https://crinklenest.com/menu/brownie</loc>', $xml);
        $this->assertStringContainsString('<loc>https://crinklenest.com/page/terms-and-conditions</loc>', $xml);
        $this->assertStringContainsString('<image:loc>', $xml);
        $this->assertStringNotContainsString('/cart', $xml);
        libxml_use_internal_errors(true);
        $this->assertNotFalse(simplexml_load_string($xml), 'sitemap must be valid XML');

        Setting::where('key', 'seo_noindex_site')->update(['value' => '1']);
        Setting::flush();
        $this->get('/robots.txt')->assertSee("Disallow: /\n", false);
        $this->get('/')->assertSee('noindex, nofollow', false);
    }

    public function test_pages_have_full_seo_head_and_structured_data(): void
    {
        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('<link rel="canonical" href="https://crinklenest.com/">', $html);
        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertStringContainsString('name="twitter:card" content="summary_large_image"', $html);
        $this->assertStringContainsString('index, follow', $html);
        preg_match('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
        $graph = json_decode($m[1], true);
        $types = array_column($graph['@graph'], '@type');
        $this->assertContains('Bakery', $types);
        $this->assertContains('WebSite', $types);
        $this->assertContains('ItemList', $types);
        $bakery = collect($graph['@graph'])->firstWhere('@type', 'Bakery');
        $this->assertSame('Bangalore', $bakery['address']['addressLocality']);
        $this->assertNotEmpty($bakery['openingHoursSpecification']);

        $menu = $this->get('/menu/brownie')->getContent();
        $this->assertStringContainsString('<title>Brownie — Order Online | Crinkle Nest</title>', $menu);
        $this->assertStringContainsString('BreadcrumbList', $menu);
        $this->assertStringContainsString('FAQPage', $this->get('/contact')->getContent());

        // Private pages are kept out of Google.
        foreach (['/cart', '/orders', '/login', '/register'] as $url) {
            $this->get($url)->assertSee('<meta name="robots" content="noindex, nofollow">', false);
        }
    }

    public function test_tracking_scripts_events_and_attribution(): void
    {
        foreach (['ga4_id' => 'G-TEST123', 'meta_pixel_id' => '999888777', 'gtm_id' => 'GTM-ABC123'] as $k => $v) {
            Setting::where('key', $k)->update(['value' => $v]);
        }
        Setting::flush();

        $this->get('/?utm_source=instagram&utm_medium=social&utm_campaign=diwali')
            ->assertSee('gtag/js?id=G-TEST123', false)->assertSee("fbq('init', \"999888777\")", false)->assertSee('GTM-ABC123', false)
            ->assertSee('cnConsent', false);

        $product = Product::where('is_active', true)->first();
        $this->post(route('cart.add', $product));
        $this->get('/cart')->assertSee("cnTrack(\"add_to_cart\"", false)->assertSee('begin_checkout', false);

        $slot = DeliverySlot::first();
        $this->post('/checkout', ['name' => 'Ad Buyer', 'phone' => '9', 'address' => 'x', 'city' => 'y', 'slot_id' => $slot->id, 'payment_method' => 'online']);
        $order = Order::where('name', 'Ad Buyer')->firstOrFail();
        $this->assertSame('instagram', $order->utm_source);
        $this->assertSame('diwali', $order->utm_campaign);
        $this->get('/orders')->assertSee('cnTrack("purchase"', false)->assertSee($order->order_number);
    }

    public function test_newsletter_signup_and_admin_export(): void
    {
        $this->get('/?utm_source=facebook');
        $this->post('/subscribe', ['email' => 'Fan@Example.com', 'source' => 'footer'])->assertSessionHas('subscribed');
        $this->post('/subscribe', ['email' => 'fan@example.com']); // duplicate is merged
        $this->assertDatabaseCount('subscribers', 1);
        $this->assertDatabaseHas('subscribers', ['email' => 'fan@example.com', 'utm_source' => 'facebook']);

        $this->post('/subscribe', ['email' => 'bot@example.com', 'website' => 'spam'])->assertSessionHasErrors('website');

        $this->actingAs($this->admin());
        $csv = $this->get('/admin/subscribers/export')->assertOk()->streamedContent();
        $this->assertStringContainsString('fan@example.com', $csv);
        $this->get('/admin')->assertSee('Orders by marketing source');
        $this->get('/admin/seo-pages')->assertOk()->assertSee('Home page');
    }

    public function test_admin_can_manage_policy_page_clauses(): void
    {
        $this->actingAs($this->admin());
        $this->post('/admin/pages', [
            'title' => 'Refund Policy', 'is_active' => 1,
            'rows' => [['title' => 'Refund window', 'content' => '<p>Within 24 hours.</p>', 'is_active' => 1]],
        ])->assertRedirect();

        $this->get('/page/refund-policy')->assertOk()->assertSee('Refund window')->assertSee('Within 24 hours.', false);
    }
}
