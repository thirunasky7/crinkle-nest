<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $logo = DatabaseSeeder::img('home', 0);

        // [key, label, value, type, help]
        $groups = [
            'general' => [
                ['site_name', 'Site name', 'Crinkle Nest'],
                ['site_tagline', 'Tagline (under the logo)', 'Artisanal Bakery'],
                ['logo', 'Logo', $logo, 'image'],
                ['favicon', 'Favicon', null, 'image'],
                ['header_cta_text', 'Header WhatsApp button text', 'WhatsApp Order', 'text', 'Leave empty to hide the button.'],
                ['show_add_to_basket', 'Show “add to basket” buttons on product cards', '1', 'boolean'],
                ['order_prefix', 'Order number prefix', 'CN-'],
                ['currency_symbol', 'Currency symbol', '₹'],
            ],
            'contact' => [
                ['phone', 'Phone number', '+91 98765 43210'],
                ['email', 'Email address', 'hello@crinklenest.com', 'email'],
                ['address_short', 'Address (short, footer)', '42 Artisan Lane, Heritage Quarter'],
                ['address_full', 'Address (full)', '42 Artisan Lane, Heritage Quarter, Bangalore — 560038', 'textarea'],
                ['opening_hours', 'Opening hours', 'Open Tue – Sun: 7:30 AM – 8:00 PM'],
                ['closed_note', 'Closed-day note', 'Mondays Closed for Hearth Cleanse & Slow Starters'],
                ['map_url', 'Google Maps link', 'https://maps.google.com/?q=42+Artisan+Lane+Bangalore', 'url'],
                ['map_embed_url', 'Google Maps embed URL (optional)', '', 'url', 'Paste the src of a Google Maps embed iframe to show a live map instead of the image on the Contact page.'],
                ['fssai_number', 'FSSAI licence number', '11224333000582'],
            ],
            'whatsapp' => [
                ['whatsapp_number', 'WhatsApp number (with country code)', '919876543210', 'text', 'Digits only, e.g. 919876543210'],
                ['whatsapp_display', 'WhatsApp number as displayed', '+91 98765 43210'],
                ['whatsapp_default_message', 'Default WhatsApp message', 'Hi Crinkle Nest! I would like to place an order. Please share the details.', 'textarea'],
                ['whatsapp_product_message', 'Product order message', 'Hi {site}! I would like to order {product}. Please share the details.', 'textarea', 'Use {product} and {site} placeholders.'],
                ['floating_whatsapp_enabled', 'Show floating WhatsApp button', '1', 'boolean'],
                ['floating_whatsapp_label', 'Floating button small label', 'Direct Hearth Orders'],
                ['floating_whatsapp_text', 'Floating button text', '+91 WhatsApp'],
            ],
            'social' => [
                ['social_instagram', 'Instagram URL', 'https://instagram.com/', 'url'],
                ['social_facebook', 'Facebook URL', '', 'url'],
                ['social_youtube', 'YouTube URL', '', 'url'],
            ],
            'footer' => [
                ['footer_about', 'Footer about text', 'Handcrafted slow-fermented breads, delicate lamination, and bespoke celebration cakes baked fresh every dawn in our hearth boutique.', 'textarea'],
                ['footer_col1_title', 'Column 1 title', 'Curated Bakes'],
                ['footer_col2_title', 'Column 2 title', 'Quick Links'],
                ['footer_col3_title', 'Column 3 title', 'The Bakehouse'],
                ['copyright_text', 'Copyright text', '© {year} Crinkle Nest Artisanal Bakery. All rights reserved.', 'text', '{year} is replaced with the current year.'],
            ],
            'checkout' => [
                ['delivery_fee', 'Delivery fee', '50', 'number'],
                ['free_delivery_threshold', 'Free delivery above (0 = always charge)', '1500', 'number'],
                ['delivery_label', 'Delivery progress label', 'Local Bicycle Courier'],
                ['delivery_fee_label', 'Delivery fee line label', 'Bicycle Courier Hearth Dispatch'],
                ['delivery_note', 'Delivery note', 'Bakes travel inside double-insulated, velvet-soft thermal crumb envelopes.'],
                ['packaging_label', 'Complimentary packaging line (empty to hide)', 'Artisanal Eco-Box & Parchment'],
                ['total_note', 'Note under grand total', 'Includes all taxes, thermal seal & bakery surcharge'],
                ['whatsapp_checkout_note', 'Note under WhatsApp checkout button', 'Instant receipt, UPI link, and live baker chat pinged immediately'],
                ['online_payment_enabled', 'Show “Pay Online” checkout button', '1', 'boolean'],
                ['online_payment_label', 'Pay Online button text', 'Pay Online via UPI / Card / Netbanking'],
                ['online_payment_message', 'Message after a Pay Online order', 'Order #{order} placed! We will share a secure UPI / card payment link on WhatsApp shortly.', 'textarea', '{order} is replaced with the order number.'],
            ],
            'rewards' => [
                ['points_per_100', 'Points earned per ₹100 (on delivery)', '10', 'number'],
                ['points_name', 'Points name', 'Nest Points'],
                ['rewards_goal_points', 'Reward goal (points)', '500', 'number'],
                ['rewards_goal_title', 'Reward goal title', 'Free Hearth Brownie Box'],
                ['rewards_default_tier', 'Default loyalty tier', 'Crinkle Connoisseur Tier'],
                ['signup_bonus_points', 'Sign-up bonus points', '50', 'number'],
            ],
            'theme' => [
                ['color_primary', 'Primary (dark chocolate)', '#250f03', 'color'],
                ['color_primary_container', 'Primary container', '#3d2314', 'color'],
                ['color_secondary', 'Secondary (caramel)', '#8e4e08', 'color'],
                ['color_secondary_container', 'Secondary container', '#fda95f', 'color'],
                ['color_surface', 'Page background (cream)', '#fef9f1', 'color'],
                ['color_blush', 'Blush accent', '#fcdbd5', 'color'],
                ['color_whatsapp', 'WhatsApp button', '#1e7e48', 'color'],
                ['color_whatsapp_hover', 'WhatsApp button hover', '#155d34', 'color'],
            ],
            'seo' => [
                ['meta_title', 'Default page title', 'Crinkle Nest | Homemade Artisanal Bakery'],
                ['meta_description', 'Meta description', 'Freshly baked homemade crinkle cookies, fudgy brownies, coin biscuits and celebration boxes. Order on WhatsApp.', 'textarea'],
                ['meta_keywords', 'Meta keywords', 'bakery, brownies, crinkle cookies, coin biscuits, homemade, eggless'],
                ['og_image', 'Social share image', DatabaseSeeder::img('home', 1), 'image'],
                ['head_scripts', 'Extra code in <head> (analytics etc.)', '', 'code'],
                ['body_scripts', 'Extra code before </body>', '', 'code'],
            ],
        ];

        foreach ($groups as $group => $rows) {
            foreach ($rows as $i => $r) {
                Setting::updateOrCreate(['key' => $r[0]], [
                    'label' => $r[1],
                    'value' => $r[2],
                    'type' => $r[3] ?? 'text',
                    'help' => $r[4] ?? null,
                    'group' => $group,
                    'sort' => $i,
                ]);
            }
        }
        Setting::flush();
    }
}
