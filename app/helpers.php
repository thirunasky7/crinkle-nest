<?php

use App\Models\Section;
use App\Models\Setting;

if (! function_exists('setting')) {
    /** Read a site setting managed from Admin → Settings. */
    function setting(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('section')) {
    /** Fetch an editable content section (Admin → Page Content). */
    function section(string $key): Section
    {
        return Section::byKey($key);
    }
}

if (! function_exists('media')) {
    /** Resolve an uploaded file path or remote URL to a public URL. */
    function media(?string $path, ?string $fallback = null): ?string
    {
        if (! $path) {
            return $fallback;
        }
        if (preg_match('~^(https?:)?//~', $path) || str_starts_with($path, 'data:')) {
            return $path;
        }
        if (str_starts_with($path, '/')) {
            return url($path);
        }

        return asset('storage/'.$path);
    }
}

if (! function_exists('link_to_url')) {
    /** Turn an admin-entered link (relative path, #anchor, absolute) into an href. */
    function link_to_url(?string $url): string
    {
        $url = trim((string) $url);
        if ($url === '' || $url === '#') {
            return '#';
        }
        if (preg_match('~^(https?:|mailto:|tel:|#|//)~', $url)) {
            return $url;
        }
        // {whatsapp} or {whatsapp:Custom message} → wa.me link to the number in Settings
        if (preg_match('/^\{whatsapp(?::(.*))?\}$/s', $url, $m)) {
            return wa_link(isset($m[1]) ? $m[1] : null);
        }

        return url($url);
    }
}

if (! function_exists('wa_number')) {
    function wa_number(): string
    {
        return preg_replace('/\D+/', '', (string) setting('whatsapp_number', ''));
    }
}

if (! function_exists('wa_link')) {
    /** Build a wa.me link to the bakery's WhatsApp number with an optional message. */
    function wa_link(?string $message = null): string
    {
        $message ??= setting('whatsapp_default_message', 'Hi Crinkle Nest! I would like to place an order. Please share the details.');
        $url = 'https://wa.me/'.wa_number();

        return $message !== '' ? $url.'?text='.rawurlencode($message) : $url;
    }
}

if (! function_exists('money')) {
    function money($amount): string
    {
        $symbol = setting('currency_symbol', '₹');
        $amount = (float) $amount;
        $decimals = floor($amount) == $amount ? 0 : 2;

        return $symbol.number_format($amount, $decimals);
    }
}

if (! function_exists('seo')) {
    /** Request-scoped SEO data for the current page. */
    function seo(): \App\Support\Seo
    {
        return app(\App\Support\Seo::class);
    }
}

if (! function_exists('track_event')) {
    /**
     * Queue a marketing event (GA4 / GTM dataLayer / Meta Pixel) to fire on the next page view.
     * Names follow GA4: add_to_cart, begin_checkout, purchase, generate_lead, sign_up …
     */
    function track_event(string $name, array $params = []): void
    {
        $events = session('track_events', []);
        $events[] = ['name' => $name, 'params' => $params];
        session()->flash('track_events', $events);
    }
}

if (! function_exists('attribution')) {
    /** First-touch campaign attribution captured from UTM parameters / referrer. */
    function attribution(): array
    {
        return array_intersect_key((array) session('attribution', []), array_flip(['utm_source', 'utm_medium', 'utm_campaign', 'referrer', 'landing_page']));
    }
}

if (! function_exists('tel_link')) {
    function tel_link(?string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', (string) $phone);
    }
}
