<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * SEO + digital marketing: per-page SEO, category meta, newsletter subscribers,
 * campaign attribution (UTM) on orders/inquiries, and the related admin settings.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_metas', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('name');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('keywords')->nullable();
            $table->text('og_image')->nullable();
            $table->text('canonical')->nullable();
            $table->boolean('noindex')->default(false);
            $table->timestamps();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('source')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        foreach (['orders', 'contact_messages'] as $t) {
            Schema::table($t, function (Blueprint $table) {
                $table->string('utm_source')->nullable();
                $table->string('utm_medium')->nullable();
                $table->string('utm_campaign')->nullable();
                $table->string('referrer')->nullable();
                $table->string('landing_page')->nullable();
            });
        }

        $now = now();
        foreach ([
            ['home', 'Home page', 'Crinkle Nest | Homemade Artisanal Bakery in Bangalore', 'Freshly baked homemade crinkle cookies, fudgy brownies, coin biscuits and celebration dessert boxes in Bangalore. 100% eggless options, zero preservatives. Order on WhatsApp.', 'bakery bangalore, homemade brownies, crinkle cookies, coin biscuits, eggless bakery, dessert boxes, cake delivery bangalore', 0],
            ['about', 'About Us', 'Our Story | Crinkle Nest Artisanal Bakery', 'From a cozy home kitchen to a neighbourhood favourite — meet the bakers behind Crinkle Nest and our small-batch, pure-butter, slow-fermented philosophy.', null, 0],
            ['contact', 'Contact', 'Contact & Custom Orders | Crinkle Nest Bakery', 'Order fresh bakes, custom celebration cakes and corporate hampers. Chat on WhatsApp, call, or visit our Heritage Quarter bakehouse in Bangalore.', null, 0],
            ['menu', 'Menu', 'Menu — Brownies, Cookies & Coin Biscuits | Crinkle Nest', 'Browse our full menu of fudgy brownies, crinkle cookies, coin biscuits and celebration boxes, baked fresh every morning. Order online or on WhatsApp.', null, 0],
            ['cart', 'Cart & Checkout', 'Your Basket', null, null, 1],
            ['orders', 'My Orders', 'My Orders', null, null, 1],
            ['profile', 'My Profile', 'My Profile', null, null, 1],
            ['login', 'Sign in', 'Sign In', null, null, 1],
            ['register', 'Create account', 'Create Account', null, null, 1],
        ] as [$key, $name, $title, $desc, $kw, $noindex]) {
            DB::table('seo_metas')->insertOrIgnore(['page_key' => $key, 'name' => $name, 'title' => $title, 'description' => $desc, 'keywords' => $kw, 'noindex' => $noindex, 'created_at' => $now, 'updated_at' => $now]);
        }

        $robots = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /cart\nDisallow: /checkout\nDisallow: /orders\nDisallow: /profile\nDisallow: /login\nDisallow: /register";
        $settings = [
            // SEO
            ['seo', 'site_url', 'Canonical site URL', 'https://crinklenest.com', 'url', 'Used for canonical links and the sitemap. No trailing slash.', 20],
            ['seo', 'seo_noindex_site', 'Hide the whole site from search engines (staging only!)', '0', 'boolean', 'Keep OFF on the live site.', 21],
            ['seo', 'robots_txt', 'robots.txt rules', $robots, 'code', 'The Sitemap line is added automatically.', 22],
            ['seo', 'google_site_verification', 'Google Search Console verification code', '', 'text', 'Only the content="…" value of the google-site-verification meta tag.', 23],
            ['seo', 'bing_site_verification', 'Bing Webmaster verification code', '', 'text', null, 24],
            ['seo', 'facebook_domain_verification', 'Facebook domain verification code', '', 'text', null, 25],
            ['seo', 'pinterest_verification', 'Pinterest verification code', '', 'text', null, 26],
            ['seo', 'twitter_handle', 'X / Twitter handle', '', 'text', 'e.g. @crinklenest', 27],
            ['seo', 'business_type', 'Business type (schema.org)', 'Bakery', 'text', 'Bakery, CafeOrCoffeeShop, FoodEstablishment…', 28],
            ['seo', 'price_range', 'Price range', '₹₹', 'text', null, 29],
            ['seo', 'currency_code', 'Currency code', 'INR', 'text', null, 30],
            ['seo', 'address_street', 'Street address (schema)', '42 Artisan Lane, Heritage Quarter', 'text', null, 31],
            ['seo', 'address_city', 'City', 'Bangalore', 'text', null, 32],
            ['seo', 'address_region', 'State / region', 'Karnataka', 'text', null, 33],
            ['seo', 'address_postcode', 'Postal code', '560038', 'text', null, 34],
            ['seo', 'address_country', 'Country code', 'IN', 'text', null, 35],
            ['seo', 'geo_lat', 'Latitude', '', 'text', 'From Google Maps (right-click → coordinates).', 36],
            ['seo', 'geo_lng', 'Longitude', '', 'text', null, 37],
            ['seo', 'opening_hours_schema', 'Opening hours (schema format)', 'Tu-Su 07:30-20:00', 'text', 'e.g. Tu-Su 07:30-20:00 or Mo-Fr 09:00-18:00, Sa 10:00-14:00', 38],
            ['seo', 'delivery_area', 'Delivery area', 'Bangalore', 'text', null, 39],
            // Marketing
            ['marketing', 'gtm_id', 'Google Tag Manager container ID', '', 'text', 'GTM-XXXXXXX. If you use GTM you can leave GA4 below empty and configure it inside GTM.', 0],
            ['marketing', 'ga4_id', 'Google Analytics 4 measurement ID', '', 'text', 'G-XXXXXXXXXX', 1],
            ['marketing', 'google_ads_id', 'Google Ads conversion ID', '', 'text', 'AW-XXXXXXXXX', 2],
            ['marketing', 'google_ads_purchase_label', 'Google Ads purchase conversion label', '', 'text', null, 3],
            ['marketing', 'meta_pixel_id', 'Meta (Facebook/Instagram) Pixel ID', '', 'text', null, 4],
            ['marketing', 'clarity_id', 'Microsoft Clarity project ID', '', 'text', 'Free heatmaps and session recordings.', 5],
            ['marketing', 'cookie_consent_enabled', 'Ask visitors for cookie consent before tracking', '1', 'boolean', 'Uses Google Consent Mode v2. Analytics/ads cookies stay off until the visitor accepts.', 6],
            ['marketing', 'cookie_consent_text', 'Cookie banner text', 'We use cookies to understand what you love and show you relevant offers.', 'text', null, 7],
            ['marketing', 'promo_bar_enabled', 'Show announcement bar at the top', '1', 'boolean', null, 10],
            ['marketing', 'promo_bar_text', 'Announcement text', 'Free delivery on orders above ₹1,500 • Use code FLOURPOWER for 15% off', 'text', null, 11],
            ['marketing', 'promo_bar_link', 'Announcement link', '/menu', 'text', 'Optional. Path, URL or {whatsapp}.', 12],
            ['marketing', 'newsletter_enabled', 'Show newsletter signup', '1', 'boolean', null, 20],
            ['marketing', 'newsletter_title', 'Newsletter title', 'Get fresh batch alerts & secret offers', 'text', null, 21],
            ['marketing', 'newsletter_text', 'Newsletter text', 'Be first to know when weekend specials come out of the oven. No spam — just warm bakes.', 'text', null, 22],
            ['marketing', 'newsletter_success', 'Newsletter thank-you message', 'You are on the list! Watch your inbox for warm news.', 'text', null, 23],
        ];
        foreach ($settings as [$group, $key, $label, $value, $type, $help, $sort]) {
            DB::table('settings')->insertOrIgnore(compact('group', 'key', 'label', 'value', 'type', 'help', 'sort') + ['created_at' => $now, 'updated_at' => $now]);
        }
        Cache::forget('site_settings');
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_metas');
        Schema::dropIfExists('subscribers');
        Schema::table('categories', fn (Blueprint $t) => $t->dropColumn(['meta_title', 'meta_description']));
        foreach (['orders', 'contact_messages'] as $t) {
            Schema::table($t, fn (Blueprint $table) => $table->dropColumn(['utm_source', 'utm_medium', 'utm_campaign', 'referrer', 'landing_page']));
        }
        DB::table('settings')->whereIn('group', ['marketing'])->delete();
        Cache::forget('site_settings');
    }
};
