<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn ($p, $i) => DatabaseSeeder::img($p, $i);

        Testimonial::query()->delete();
        foreach ([
            ['Ananya Verma', 'Heritage Quarter Resident', 'The walnut fudge brownies reminded me of my grandmother’s kitchen in Normandy. You can taste the authenticity of the butter immediately—it never feels heavy or oily.', $img('about', 9)],
            ['Rohan & Priya Mehta', 'Custom Celebration Client', "Ordered their eggless botanic celebration cake for my daughter's first birthday. The floral piping and delicate raspberry curd layer had everyone asking for Tara's contact.", $img('about', 10)],
            ['Meera Nambiar', 'Creative Director, Studio 9', 'The crinkle cookies arrived still warm in their parchment wrap! That signature powdered crust gave way to an impossibly molten center. Friday afternoon office staple now.', $img('about', 11)],
        ] as $i => [$name, $role, $quote, $avatar]) {
            Testimonial::create(compact('name', 'role', 'quote', 'avatar') + ['rating' => 5, 'sort' => $i]);
        }

        Faq::query()->delete();
        foreach ([
            ['Do you deliver warm across Bangalore?', 'Yes! We arrange temperature-controlled doorstep delivery for celebration cakes and morning breakfast bakes within a 25km radius from our Heritage Quarter hearth.'],
            ['How much notice is needed for tiered cakes?', 'We recommend 48 to 72 hours for bespoke multi-tiered floral cakes so our bakers can ferment sponges slowly and source edible garden blooms fresh.'],
            ['Can you craft 100% eggless or vegan bakes?', 'Over 75% of our menu—including our signature chocolate crinkles, salted caramel tea cakes, and sourdoughs—is crafted 100% eggless with organic churned dairy or oat milk.'],
        ] as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'sort' => $i]);
        }

        Page::query()->delete();
        $terms = Page::create([
            'slug' => 'terms-and-conditions',
            'title' => 'Terms & Conditions',
            'title_highlight' => 'of the Hearth',
            'status_chip' => 'Crinkle Nest Hearth Policies • Last Updated: October 2025',
            'subtitle' => 'Please read our bakehouse terms, perishable order guidelines, and dispatch commitments crafted with care for every customer. Each batch is baked to order with untamed hearth butter, slow fermentation, and honest artisan hands.',
            'directory_label' => 'Policy Directory',
            'meta_title' => 'Terms & Conditions',
            'meta_description' => 'Ordering, payment, delivery, cancellation and allergen terms for Crinkle Nest Artisanal Bakery.',
        ]);
        foreach (require __DIR__.'/data/terms.php' as $i => $clause) {
            $terms->sections()->create($clause + ['sort' => $i]);
        }

        $p = 'font-body-md text-body-md text-on-surface-variant leading-relaxed';
        $privacy = Page::create([
            'slug' => 'privacy-policy',
            'title' => 'Privacy Policy',
            'title_highlight' => 'at the Hearth',
            'status_chip' => 'Crinkle Nest Privacy • Last Updated: October 2025',
            'subtitle' => 'We collect only what we need to bake and deliver your order, and we never sell your details.',
            'directory_label' => 'Privacy Directory',
            'meta_title' => 'Privacy Policy',
        ]);
        foreach ([
            ['What We Collect', 'badge', 'Information We Collect', 'Data we collect', "<p class=\"$p\">When you place an order, contact us or create an account we store your name, phone/WhatsApp number, email, delivery address and order details. We also remember baking preferences you choose in your profile.</p>"],
            ['Purpose', 'task_alt', 'How We Use Your Information', 'How we use it', "<p class=\"$p\">Your details are used to confirm and deliver orders, send order updates on WhatsApp (according to your notification preferences), respond to inquiries and manage loyalty points. We do not sell or rent personal data.</p>"],
            ['Sharing', 'two_wheeler', 'Delivery Partners & Payments', 'Sharing', "<p class=\"$p\">We share only the name, phone and address needed with our local delivery couriers. Online payments are handled by our payment provider; we never store card details.</p>"],
            ['Your Choices', 'manage_accounts', 'Your Rights & Contact', 'Your rights', "<p class=\"$p\">You can update or delete your profile details at any time from My Profile, or ask us on WhatsApp to remove your data. For any privacy question, write to us using the details on our Contact page.</p>"],
        ] as $i => [$eyebrow, $icon, $title, $nav, $content]) {
            $privacy->sections()->create(['eyebrow' => $eyebrow, 'icon' => $icon, 'title' => $title, 'nav_title' => $nav, 'content' => $content, 'sort' => $i]);
        }
    }
}
