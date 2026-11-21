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
            $this->get("/admin/$res/".$model::first()->getKey().'/edit')->assertOk();
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
