<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    protected int $sort = 0;

    protected function s(string $page, string $key, string $name, array $data, array $items = [], ?string $itemsLabel = null): void
    {
        $section = Section::updateOrCreate(['key' => $key], array_merge([
            'page' => $page,
            'name' => $name,
            'items_label' => $itemsLabel,
            'sort' => $this->sort++,
            'is_active' => true,
        ], $data));
        $section->items()->delete();
        foreach ($items as $i => $item) {
            $section->items()->create($item + ['sort' => $i]);
        }
    }

    public function run(): void
    {
        $img = fn ($p, $i) => DatabaseSeeder::img($p, $i);

        /* ============================== HOME ============================== */
        $this->s('home', 'home.hero', 'Hero banner', [
            'title' => 'Baked With Love,',
            'title_highlight' => 'Served With Happiness',
            'subtitle' => 'Freshly baked homemade treats made with love. Slow-whipped batters, crackly sugar tops, and wholesome hearth warmth baked fresh every dawn.',
            'image' => $img('home', 1),
            'image_alt' => 'Freshly baked crinkle cookies, fudge brownies, and warm cappuccino on rustic wooden table',
            'button_text' => 'Explore Our Menu',
            'button_link' => '#menu-catalog',
            'button2_text' => 'Order on WhatsApp',
            'button2_link' => '{whatsapp}',
            'extra' => [
                'rating_text' => '★ 4.9/5',
                'rating_note' => 'from 1,200+ happy homes',
                'pill_icon' => 'cookie',
                'pill_title' => 'Fresh Scent of Cocoa & Spice',
                'pill_text' => 'Morning Hearth Batch Ready',
                'pill_badge' => 'Ready',
            ],
        ], [
            ['icon' => 'local_fire_department', 'title' => 'Fresh Batch Daily'],
            ['icon' => 'eco', 'title' => 'Eggless Options'],
            ['icon' => 'format_image_left', 'title' => 'Zero Preservatives'],
        ], 'Highlight chips|icon,title');

        $this->s('home', 'home.shorts', '“Fresh From Our Oven” videos heading', [
            'eyebrow' => 'Behind The Scenes', 'eyebrow_icon' => 'videocam',
            'title' => 'Fresh From Our Oven',
            'subtitle' => 'Watch our daily baking rituals & behind the scenes magic unfold in small batches.',
        ]);

        $this->s('home', 'home.trending', '“Trending Now” heading', [
            'eyebrow' => 'Best Sellers', 'eyebrow_icon' => 'trending_up',
            'title' => 'Trending Now',
            'subtitle' => 'Most loved by our neighborhood foodies and repeated week after week.',
            'extra' => ['button_text' => 'Order'],
        ]);

        $this->s('home', 'home.collections', '“More Bakery Collections” heading', [
            'eyebrow' => 'Explore Categories', 'eyebrow_icon' => 'menu_book',
            'title' => 'More Bakery Collections',
            'subtitle' => 'Discover handcrafted sweets baked every morning with artisanal finesse.',
            'extra' => ['card_cta' => 'Browse & Order', 'product_button_text' => 'WhatsApp'],
        ]);

        $this->s('home', 'home.promo', 'Gifting promo banner', [
            'eyebrow' => 'Gifting & Celebrations', 'eyebrow_icon' => 'redeem',
            'title' => 'A Little Box Full of Happiness!',
            'subtitle' => 'Make every celebration sweeter with Crinkle Nest. Freshly custom-packed for birthdays, anniversaries, and sweet cravings.',
            'button_text' => 'Order Your Treats',
            'button_link' => '{whatsapp:Hi Crinkle Nest! I would like to order a Little Box Full of Happiness! Please share the details.}',
        ], [
            ['icon' => 'local_shipping', 'title' => 'Same-Day Local Delivery'],
            ['icon' => 'oven_gen', 'title' => 'Freshly Baked On Order'],
            ['icon' => 'verified', 'title' => 'Zero Preservatives'],
        ], 'Perk chips|icon,title');

        $this->s('home', 'home.about', '“A Little About Us” block', [
            'eyebrow' => 'Our Story', 'eyebrow_icon' => 'nest_eco_leaf',
            'title' => 'A Little About Crinkle Nest',
            'subtitle' => 'Crinkle Nest is a homemade bakery that believes every bite should feel special. We bake fresh, delicious treats with love and care, bringing homemade happiness straight to your doorstep.',
            'image' => $img('home', 27),
            'image_alt' => 'Smiling baker holding a freshly baked tray of golden cookies and brownies in a cozy rustic bakery',
            'button_text' => 'Chat With Our Baker',
            'button_link' => "{whatsapp:Hi Crinkle Nest! I'd love to know more about your baking schedule and specials.}",
            'extra' => ['badge_icon' => 'favorite', 'badge_title' => 'Crafted by Hand', 'badge_text' => 'Every single morning'],
        ], [
            ['icon' => 'spa', 'title' => '100% Eggless', 'subtitle' => 'Options available'],
            ['icon' => 'check_circle', 'title' => 'No Artificial', 'subtitle' => 'Pure wholesome ingredients'],
            ['icon' => 'bakery_dining', 'title' => 'Small-Batch', 'subtitle' => 'Slow artisanal process'],
        ], 'Brand highlights|icon,title,subtitle');

        $this->s('home', 'home.custom_cta', 'Custom order call-to-action', [
            'eyebrow_icon' => 'chat_bubble',
            'title' => 'Need a custom box or party order?',
            'subtitle' => 'We customize batch sizes, eggless variations, and customized notes on request.',
            'button_text' => 'Send Custom Inquiry',
            'button_link' => '{whatsapp:Hi Crinkle Nest! I have a custom order inquiry.}',
        ]);

        /* ============================== ABOUT ============================== */
        $this->s('about', 'about.hero', 'Hero & milestones', [
            'eyebrow' => 'Our Homemade Chronicle', 'eyebrow_icon' => 'cookie',
            'title' => 'The Crinkle Nest Story:',
            'title_highlight' => 'Baked With Love,',
            'subtitle' => "How an oven in a cozy home kitchen blossomed into a neighborhood sanctuary for slow-fermented, small-batch artisanal treats, dusted with confectioner's sugar and hearth warmth.",
            'extra' => ['title_suffix' => 'Folded With Care', 'meta_title' => 'About Us'],
        ], [
            ['title' => 'Est. 2021', 'subtitle' => 'Kitchen Hearth Born'],
            ['title' => '1,200+', 'subtitle' => 'Homes Nourished'],
            ['title' => '100% Pure', 'subtitle' => 'Cultured Butter'],
            ['icon' => 'verified', 'title' => 'FSSAI', 'subtitle' => 'Certified Hearth'],
        ], 'Milestones (icon optional)|icon,title,subtitle');

        $this->s('about', 'about.mosaic', 'Editorial photo mosaic', [
            'image' => $img('about', 1),
            'image_alt' => 'Hands rolling signature chocolate crinkle cookies in powdered sugar',
            'image2' => $img('about', 2),
            'image3' => $img('about', 3),
            'extra' => [
                'main_caption_icon' => 'oven_gen',
                'main_caption' => 'Micro-batch deck oven prep at 350°F',
                'main_label' => 'Ritual No. 01',
                'image2_alt' => 'Slow-churned cultured creamery butter and vanilla pods',
                'card_icon' => 'spa',
                'card_title' => 'Slow Hearth Ferment',
                'card_text' => 'Never accelerated by artificial yeast boosters or commercial improvers.',
                'quote_label' => "From Tara's Journal",
                'quote' => 'Crinkle cookies aren’t merely pastry; they are tiny geologic wonders. The powdered sugar crags open only when the heart of the dough expands with honest heat.',
                'quote_author' => 'Tara Sen, Co-Founder',
                'image3_alt' => 'Warm cracked chocolate crinkles cooling on a brass rack',
                'image3_badge' => 'Single-Origin 70% Cocoa',
            ],
        ]);

        $this->s('about', 'about.pillars', 'Philosophy & four pillars', [
            'eyebrow' => 'Purity Over Volume',
            'title' => 'Our Hearth Philosophy & Four Uncompromising Pillars',
            'subtitle' => 'Commercial bakeries prioritize shelf life and fast turnover. We measure our success by the quiet crunch of delicate crusts and the lingering finish of real butter.',
        ], [
            ['icon' => 'soup_kitchen', 'label' => 'Pillar I', 'title' => 'Small-Batch Dedication', 'description' => 'We never mass-produce. Each batch of brownies and cookies is folded in micro-bowls and baked in deck ovens for the optimal, crackled crinkle top.', 'value' => 'Micro-yield: 24 trays daily'],
            ['icon' => 'nutrition', 'label' => 'Pillar II', 'title' => 'Pure Butter & Single-Origin Cocoa', 'description' => 'We exclusively use slow-churned cultured creamery butter and authentic Valrhona & Callebaut chocolate. No palm oils, emulsifiers, or artificial extracts.', 'value' => 'Zero hydrogenated fats'],
            ['icon' => 'hourglass_top', 'label' => 'Pillar III', 'title' => 'The 36-Hour Cold Rest', 'description' => 'Our signature doughs age in chilling chambers for up to 36 hours. Sugars hydrolyze and melt into deep, toffee-toned caramel undertones before hearth baking.', 'value' => 'Natural enzyme maturation'],
            ['icon' => 'pedal_bike', 'label' => 'Pillar IV', 'title' => 'Parchment & Cyclist Dispatch', 'description' => 'Packed in unbleached virgin parchment and keepsake embossed botanical tins, dispatched via local couriers within hours of pulling from the hot stone ovens.', 'value' => 'Plastic-free breathable tins'],
        ], 'Pillars|icon,label,title,description,value');

        $this->s('about', 'about.founders', 'Meet the founders', [
            'eyebrow' => 'From Our Hands to Your Table',
            'title' => 'A homemade bake carries a piece of the baker’s heart. We fold warmth into every single tin.',
            'subtitle' => 'What began in winter 2021 as a weekend therapeutic baking ritual in Tara’s tiny apartment kitchen soon took over every tabletop. Madeleine brought classical French lamination precision, while Tara perfected the slow-fermentation crinkle technique that defines our signatures today.',
            'body' => 'We reject the industrial shortcuts common to modern confectionery. There are no premixes, no stabilizers to make our cookies sit on store shelves for months, and no compromise on single-estate butter. When an order lands at our hearth, it is prepared specifically for your tea time.',
            'image' => $img('about', 4),
            'image_alt' => 'Co-founders Tara Sen and Madeleine Roy',
            'extra' => [
                'image_label' => 'Founding Pâtissiers',
                'image_title' => 'Tara Sen & Madeleine Roy',
                'image_text' => 'Self-taught fermenters, lovers of crusty loaves & melt-in-mouth crinkles',
                'badge_icon' => 'favorite',
                'badge_title' => 'Hand-folded with love',
                'badge_text' => 'Over 4,500 hours spent testing flours',
                'list_title' => "Tara & Madeleine's Personal Pantry Rules",
            ],
        ], [
            ['icon' => 'flare', 'title' => 'Only unprocessed sea salt crystals to balance sweet chocolate batter'],
            ['icon' => 'flare', 'title' => 'Raw country brown eggs sourced within 40km of our hearth'],
            ['icon' => 'flare', 'title' => 'Bourbon vanilla pods scraped by hand every morning at 6:00 AM'],
        ], 'Pantry rules|icon,title');

        $this->s('about', 'about.ritual', 'Daily ritual steps', [
            'eyebrow' => 'The Bakehouse Clock',
            'title' => 'From Raw Flour to Fragrant Hearth',
            'subtitle' => 'An intimate look at the daily morning choreography that fills the lane with aromas of browned butter, roasted walnuts, and caramelized sourdough.',
        ], [
            ['image' => $img('about', 5), 'label' => '05:30 AM', 'title' => 'Flour Sifting & Churning', 'description' => 'Unbleached heritage wheat is aerated with bronze sieves while cultured cream butter is creamed to pale velvety ribbons.', 'value' => 'Step 01 / Dawn Light'],
            ['image' => $img('about', 6), 'label' => '08:00 AM', 'title' => 'Hand-Rolling Crinkles', 'description' => "Chilled dough is measured into precise 65-gram portions and double-dusted in confectioner's snow for maximum crackle.", 'value' => 'Step 02 / Gentle Shaping'],
            ['image' => $img('about', 7), 'label' => '11:30 AM', 'title' => 'First Oven Pull', 'description' => 'The bakery fills with vanilla and toasted cocoa. Trays are tapped gently to set the fudgy centers before wire rack resting.', 'value' => 'Step 03 / Hearth Peak'],
            ['image' => $img('about', 8), 'label' => '02:00 PM', 'title' => 'Parchment & Courier', 'description' => 'Every tin is sealed with botanical stickers, wrapped in raw jute twine, and delivered to nearby porches in time for afternoon tea.', 'value' => 'Step 04 / Porch Delivery'],
        ], 'Ritual steps|image,label,title,description,value');

        $this->s('about', 'about.testimonials', 'Testimonials heading', [
            'eyebrow' => 'Heard at the Neighborhood Counter', 'eyebrow_icon' => 'sentiment_satisfied',
            'title' => 'Kind Words From Our Patrons',
            'subtitle' => 'Nothing fills our hearts more than discovering how our warm tins sweeten your family milestones and rainy afternoon cravings.',
        ]);

        $this->s('about', 'about.cta', 'Closing call-to-action', [
            'eyebrow' => 'Baking Daily in Limited Batches', 'eyebrow_icon' => 'local_cafe',
            'title' => 'Taste the Homemade Difference This Morning.',
            'subtitle' => 'Our trays come out of the hearth at 11:30 AM and 3:30 PM. Reserve your box early or chat with Tara directly to plan a custom weekend celebration spread.',
            'button_text' => 'Explore Today’s Fresh Bakes',
            'button_link' => '/menu',
            'button2_text' => 'Chat on WhatsApp ({phone})',
            'button2_link' => '{whatsapp}',
        ]);

        /* ============================== CONTACT ============================== */
        $this->s('contact', 'contact.hero', 'Header', [
            'eyebrow' => 'Hearth Inquiry & Direct Counter',
            'title' => 'Get in Touch with Our Bakehouse',
            'subtitle' => 'Have a bespoke celebration cake inquiry, special dietary question, or want to reserve a fresh batch warm from our morning bake? We would love to chat with you.',
            'extra' => [
                'breadcrumb' => 'Bakehouse Support & Contact',
                'status_icon' => 'local_fire_department',
                'status_label' => 'Hearth Today',
                'status_title' => 'Morning Sourdough & Crinkles',
                'status_text' => 'Ovens running hot till 8:00 PM',
            ],
        ]);

        $this->s('contact', 'contact.cards', 'Contact cards (WhatsApp · Visit · Call)', [], [
            ['icon' => 'chat', 'label' => 'Fastest Channel', 'title' => 'WhatsApp Bakery Counter', 'description' => 'Instant answers for same-day batch availability, live pastry reservation, and real-time custom cake design consultations.', 'subtitle' => 'forum', 'value' => 'Chat on WhatsApp ({phone})', 'link' => '{whatsapp}'],
            ['icon' => 'storefront', 'label' => 'Heritage Quarter', 'title' => 'Visit Our Hearth Boutique', 'description' => 'Address and hours come from Settings → Contact.', 'subtitle' => 'directions', 'value' => 'Get Hearth Directions', 'link' => '#location-map'],
            ['icon' => 'call', 'label' => '2-Hour Reply', 'title' => 'Direct Call & Email', 'description' => 'For corporate hampers, editorial tastings, or custom bulk orders:', 'subtitle' => 'outgoing_mail', 'value' => 'Send Email Inquiries', 'link' => 'mailto:{email}'],
        ], 'Cards (1st = WhatsApp, 2nd = Visit, 3rd = Call/Email). Subtitle = button icon, Value = button text|icon,label,title,description,subtitle,value,link');

        $this->s('contact', 'contact.form', 'Inquiry form', [
            'eyebrow' => 'Direct Kitchen Mail', 'eyebrow_icon' => 'drafts',
            'title' => 'Send a Message to the Hearth',
            'button_text' => 'Send Hearth Note',
            'button2_text' => 'Direct WhatsApp Chat',
            'button2_link' => '{whatsapp}',
            'extra' => [
                'types_label' => 'How can our bakers craft for you? *',
                'message_label' => 'Your Cake Vision or Tasting Inquiries *',
                'message_placeholder' => 'Tell us your flavor cravings, dietary restrictions (eggless, refined-sugar free), custom floral accents, or packaging preferences...',
                'whatsapp_toggle_text' => 'Send a copy to my WhatsApp with instant baker queue confirmation',
                'success_message' => 'Thank you! Your note has reached our head baker. We will connect shortly on WhatsApp.',
            ],
        ], [
            ['title' => 'Fresh Batch Order', 'subtitle' => 'Fresh Batch Order'],
            ['title' => 'Bespoke Celebration Cake', 'subtitle' => 'Bespoke Celebration Cake'],
            ['title' => 'Corporate Hampers', 'subtitle' => 'Corporate Gifting & Hampers'],
            ['title' => 'Dietary Consultation', 'subtitle' => 'Dietary / Eggless Consultation'],
            ['title' => 'Feedback & Praise', 'subtitle' => 'Feedback & Praise'],
        ], 'Inquiry types (Title = saved value, Subtitle = button text)|title,subtitle');

        $this->s('contact', 'contact.location', 'Location & map card', [
            'eyebrow' => 'Boutique & Kitchen',
            'title' => 'Bangalore Heritage Quarter',
            'image' => $img('contact', 1),
            'extra' => [
                'badge' => 'Deck #02',
                'pin_text' => '42 Artisan Lane, Bangalore',
                'pickup_icon' => 'pedal_bike',
                'pickup_title' => 'Drive-By & Warm Curbside Pickup',
                'pickup_text' => 'Drive-by counter available on Artisan Lane. Simply message our bicycle runner 15 mins prior for warm-from-the-oven handover right to your car window.',
                'live_label' => 'Live Deck Status',
                'live_text' => 'Crinkle Cookies & Sea Salt Brownies Fresh Now',
                'live_badge' => 'Fresh Bake',
            ],
        ]);

        $this->s('contact', 'contact.faqs', 'FAQ box heading (questions live in FAQs)', [
            'eyebrow_icon' => 'help',
            'title' => 'Bakehouse FAQs',
            'button_text' => 'Read all Bakehouse terms & storage guide',
            'button_link' => '/page/terms-and-conditions',
        ]);

        $this->s('contact', 'contact.gallery', 'Tabletop photo strip', [], [
            ['image' => $img('contact', 2), 'title' => 'Bakery spread'],
            ['image' => $img('contact', 3), 'title' => 'Fresh bakes'],
            ['image' => $img('contact', 4), 'title' => 'Hearth tray'],
            ['image' => $img('contact', 5), 'title' => 'Gift boxes'],
        ], 'Photos|image,title');

        /* ============================== MENU ============================== */
        $this->s('menu', 'menu.header', 'Menu page header', [
            'eyebrow' => 'Baked Fresh Every Morning', 'eyebrow_icon' => 'menu_book',
            'title' => 'Our Hearth Menu',
            'subtitle' => 'Small-batch brownies, crinkle cookies, coin biscuits and celebration boxes — order on WhatsApp or add to your basket.',
            'extra' => ['breadcrumb' => 'Menu', 'meta_title' => 'Menu', 'empty_text' => "Fresh bakes for this collection are coming soon. Message us on WhatsApp for today's batch."],
        ]);

        /* ============================== CART ============================== */
        $this->s('cart', 'cart.header', 'Cart header & labels', [
            'eyebrow' => 'Artisanal Hearth Dispatch',
            'title' => 'Your Hearth Basket & Checkout',
            'subtitle' => 'Review your freshly pulled batch, nominate your delivery hearth slot, and let our cycle couriers glide it straight to your doorstep warm and fragrant.',
            'extra' => [
                'breadcrumb_root' => 'Bakehouse Store',
                'breadcrumb' => 'Cart & Hearth Checkout',
                'status_text' => 'Ovens Active • Fresh Batch Reserved',
                'items_title' => 'Selected Bakehouse Goods',
                'items_badge' => 'Slow-fermented & Pure Butter',
                'gift_note_title' => 'Add Complimentary Handwritten Parchment Note',
                'gift_note_help' => 'We hand-pen this in black archival ink on vintage botanical paper:',
                'slot_note' => 'Fresh Warm Cycle Guaranteed',
                'empty_title' => 'Your basket is waiting for warm bakes',
                'empty_text' => 'Add treats from our menu to start a fresh batch order.',
            ],
        ]);

        $this->s('cart', 'cart.guarantees', 'Guarantees under the summary', [], [
            ['icon' => 'egg_alt', 'title' => '100% Pure Butter & Dedicated Eggless', 'description' => 'Prepared in certified sanitized small-batch vessels.'],
            ['icon' => 'nest_clock_farsight_analog', 'title' => 'Baked Dawn-to-Dawn', 'description' => 'Never frozen, no chemical dough conditioners or preservatives.'],
            ['icon' => 'format_image_left', 'title' => 'Crumb & Freshness Guarantee', 'description' => 'If anything arrives crushed, we re-bake or instantly refund.'],
        ], 'Guarantees|icon,title,description');

        $this->s('cart', 'cart.chef', 'Baker’s live window card', [
            'image' => $img('cart', 4),
            'title' => 'Oven 3 is pulling fresh cookies right now.',
            'subtitle' => 'Chef Tara • Head Pâtissier at Crinkle Nest',
        ]);

        $this->s('cart', 'cart.recommend', '“Frequently Paired” heading', [
            'eyebrow' => 'From The Cooling Racks',
            'title' => 'Frequently Paired with Your Batch',
            'button_text' => 'Explore Whole Hearth Menu',
            'button_link' => '/menu',
            'extra' => ['add_button_text' => 'Add to Tray'],
        ]);

        /* ============================== ORDERS ============================== */
        $this->s('orders', 'orders.header', 'Header & labels', [
            'title' => 'My Orders & Fresh Batch Tracker',
            'subtitle' => 'Track your slow-fermented, hand-crafted bakes directly from the stone hearth to your doorstep.',
            'extra' => [
                'breadcrumb_root' => 'Bakehouse Account',
                'breadcrumb' => 'Hearth Track',
                'points_label' => 'Reward Crumbs',
                'certified_text' => 'Hearth Temperature Certified',
                'history_title' => 'Past Hearth Bakes',
                'history_subtitle' => 'Archived orders, recipe notes, and quick 1-click reorders.',
                'delivered_note' => 'Hand-delivered by Nest Courier',
                'empty_title' => 'No orders yet',
                'empty_text' => 'Your fresh batches will appear here once you place an order.',
            ],
        ]);

        $this->s('orders', 'orders.tracker', 'Live order tracker steps', [], [
            ['icon' => 'check', 'title' => 'Order Confirmed', 'description' => 'Hearth queued', 'label' => 'Order Confirmed'],
            ['icon' => 'local_fire_department', 'title' => 'Baking in Hearth', 'description' => 'Deck 02 at 220°C • Crust blooming', 'label' => 'Freshly Baking Now'],
            ['icon' => 'inventory_2', 'title' => 'Butterbox Packing', 'description' => 'Awaiting gentle cooling', 'label' => 'Packing With Care'],
            ['icon' => 'moped', 'title' => 'Dispatched', 'description' => 'Heated insulation pouch', 'label' => 'Out for Delivery'],
        ], 'Steps in order: Confirmed → Baking → Packing → Dispatched (Label = status chip)|icon,title,description,label');

        $this->s('orders', 'orders.whatsapp_banner', 'WhatsApp assist banner', [
            'eyebrow_icon' => 'soup_kitchen',
            'title' => 'Need to modify timing or sugar glaze?',
            'subtitle' => 'Our pastry master is actively folding the dough. Ping our oven counter directly on WhatsApp before dispatch.',
            'button_text' => 'WhatsApp Kitchen ({phone})',
            'button_link' => '{whatsapp}',
        ]);

        $this->s('orders', 'orders.assurance', 'Assurance badges', [], [
            ['icon' => 'timer', 'title' => 'Zero-Preservative Window', 'description' => 'Baked with pure French butter; best relished within 48-72 hours.'],
            ['icon' => 'box', 'title' => 'Biodegradable Crumb Box', 'description' => 'Insulated virgin paperboard boxes with thermal crust guards.'],
            ['icon' => 'handshake', 'title' => 'Hand-Delivered by Nest', 'description' => 'Handled upright with care to preserve delicate pastry laminations.'],
        ], 'Badges|icon,title,description');

        /* ============================== PROFILE ============================== */
        $this->s('profile', 'profile.personal', 'Personal details & labels', [
            'eyebrow' => 'Hearth Guest',
            'title' => 'Personal Details',
            'subtitle' => 'Manage your contact information and celebratory moments.',
            'extra' => [
                'member_since_text' => 'Member of the Hearth since',
                'stat_orders' => 'Hearth Orders',
                'stat_points' => 'Sweet Points',
                'stat_saved' => 'Bakes Tried',
                'birthday_title' => 'Complimentary Birthday Bakes',
                'birthday_text' => 'We send a gift box of fresh crinkle cookies during your birthday week.',
                'addresses_title' => 'Saved Hearth Deliveries',
                'addresses_subtitle' => 'Where our fresh dawn bakes should be carefully dispatched.',
            ],
        ]);

        $this->s('profile', 'profile.dietary', 'Dietary preferences', [
            'eyebrow' => 'Custom Crafted',
            'title' => 'Dietary & Baking Preferences',
            'subtitle' => 'Tailor your slow-fermented crusts, glazes, and bakehouse packaging.',
            'extra' => ['list_title' => 'Kitchen Dietary Profiles'],
        ], [
            ['title' => '100% Pure Eggless Bakes', 'description' => 'Prepared with curd, buttermilk, and organic chia seed binders.', 'label' => '100% Eggless Patron'],
            ['title' => 'Low Sugar / Palm Jaggery', 'description' => 'Subtle, unrefined sweetness preserving the natural grain notes.', 'label' => 'Low Sugar Lover'],
            ['title' => 'Nut-Free Kitchen Batching', 'description' => 'Exclude almond flour, hazelnut pralines, and walnut flakes.', 'label' => 'Nut-Free'],
            ['title' => '36h Cold Fermentation', 'description' => 'Deep sour tang and open airy crumb structure for morning loaves.', 'label' => 'Sourdough Devotee'],
        ], 'Options (Label = profile badge)|title,description,label');

        $this->s('profile', 'profile.sweetness', 'Sweetness levels', [
            'title' => 'Favorite Hearth Sweetness',
        ], [
            ['title' => 'Whisper of Honey (30%)'],
            ['title' => 'Classic Hearth Richness (70%)'],
            ['title' => 'Decadent Belgian Ganache (100%)'],
        ], 'Levels (low → high)|title');

        $this->s('profile', 'profile.packaging', 'Packaging preference', [
            'title' => 'Signature Packaging Routine',
            'subtitle' => 'Choose how we wrap your bakes.',
        ], [
            ['title' => 'Eco Paper Gift Tin with Jute Twine & Dried Lavender'],
            ['title' => 'Blush Pink Boutique Box with Satin Ribbon'],
            ['title' => 'Minimal Kraft Box (zero-waste)'],
        ], 'Packaging options|title');

        $this->s('profile', 'profile.notifications', 'WhatsApp notification options', [
            'eyebrow' => 'Instant baker updates on chat',
            'title' => 'WhatsApp Hearth Dispatch',
            'subtitle' => 'Since Crinkle Nest operates on a direct hearth model, we keep phone notifications intimate and real-time.',
        ], [
            ['title' => 'Fresh Oven Batch Alerts', 'description' => 'When weekend croissants or brioche loaves come out hot at 7:30 AM.', 'value' => 'on'],
            ['title' => 'Live Dispatch & Location Map', 'description' => 'Receive automated live delivery tracking link when our cyclist departs.', 'value' => 'on'],
            ['title' => 'Bespoke Cake Chat Inquiries', 'description' => 'Permit direct pastry chef consultation on custom botanical themes.', 'value' => 'off'],
        ], 'Options (Value “on” = enabled by default)|title,description,value');

        $this->s('profile', 'profile.rewards', 'Rewards card', [
            'title' => 'Sweet Rewards',
            'extra' => [
                'progress_text' => 'Just {points} points away from unlocking your warm walnut brownie gift box!',
                'unlocked_text' => 'Reward unlocked! Mention it on WhatsApp with your next order.',
                'vouchers_title' => 'Available Vouchers',
            ],
        ]);

        $this->s('profile', 'profile.help', 'Help prompt', [
            'eyebrow_icon' => 'support_agent',
            'title' => 'Need a custom tier cake?',
            'subtitle' => 'Speak with Chef Madeleine directly.',
            'button_link' => '{whatsapp}',
        ]);

        /* ============================== POLICY ============================== */
        $this->s('policy', 'policy.summary', 'Quick summary chips', [], [
            ['icon' => 'cookie', 'subtitle' => 'Small-Batch', 'title' => 'Fresh Daily'],
            ['icon' => 'two_wheeler', 'subtitle' => 'Delivery', 'title' => 'Bangalore Only'],
            ['icon' => 'format_image_left', 'subtitle' => 'Certification', 'title' => 'FSSAI Licensed'],
            ['icon' => 'chat', 'subtitle' => 'Hearth Hotline', 'title' => 'Instant WhatsApp'],
        ], 'Chips (Subtitle = small label, Title = value)|icon,subtitle,title');

        $this->s('policy', 'policy.help', 'Sidebar help box', [
            'eyebrow' => 'Bakehouse Clarifications', 'eyebrow_icon' => 'support_agent',
            'title' => 'Questions on custom wedding cakes or dietary bakes?',
            'subtitle' => 'Our head baker directly reviews requests daily between 08:00 and 19:00 IST.',
            'button_text' => 'Ask on WhatsApp',
            'button_link' => '{whatsapp}',
        ]);

        $this->s('policy', 'policy.accreditations', 'Sidebar accreditations', [
            'title' => 'Kitchen Accreditations',
        ], [
            ['icon' => 'verified', 'title' => 'Central FSSAI Lic. #11224333000582'],
            ['icon' => 'eco', 'title' => 'Plastic-Free Botanical Kraft Packaging'],
        ], 'Accreditations|icon,title');
    }
}
