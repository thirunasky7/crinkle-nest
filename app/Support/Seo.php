<?php

namespace App\Support;

use App\Models\Faq;
use App\Models\Product;
use App\Models\SeoMeta;

/**
 * Request-scoped SEO state: title, description, canonical, robots, social image
 * and JSON-LD structured data. Pages fill it in; layouts/partials/seo-head renders it.
 */
class Seo
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $keywords = null;
    public ?string $image = null;
    public ?string $canonical = null;
    public bool $noindex = false;
    public string $type = 'website';
    public array $breadcrumbs = [];
    public array $schemas = [];

    /** Load admin-managed defaults for a page (Admin → SEO → Page SEO). */
    public function page(string $key): static
    {
        try {
            $meta = SeoMeta::where('page_key', $key)->first();
        } catch (\Throwable $e) {
            $meta = null;
        }
        if ($meta) {
            $this->title ??= $meta->title;
            $this->description ??= $meta->description;
            $this->keywords ??= $meta->keywords;
            $this->image ??= $meta->og_image;
            $this->canonical ??= $meta->canonical;
            $this->noindex = $this->noindex || $meta->noindex;
        }

        return $this;
    }

    public function set(array $values): static
    {
        foreach ($values as $k => $v) {
            if ($v !== null && $v !== '' && property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }

        return $this;
    }

    public function noindex(bool $on = true): static
    {
        $this->noindex = $on;

        return $this;
    }

    /** @param array<array{0:string,1:string}> $crumbs [name, url] pairs, Home is added automatically */
    public function breadcrumbs(array $crumbs): static
    {
        $this->breadcrumbs = $crumbs;

        return $this;
    }

    public function schema(array $data): static
    {
        $this->schemas[] = $data;

        return $this;
    }

    public static function baseUrl(): string
    {
        return rtrim((string) setting('site_url', config('app.url')), '/');
    }

    /** Absolute URL on the canonical domain. */
    public static function abs(?string $url): ?string
    {
        if (! $url) {
            return null;
        }
        $url = media($url);
        if (preg_match('~^https?://~', $url)) {
            // Re-host local URLs on the canonical domain.
            $local = rtrim(url('/'), '/');

            return str_starts_with($url, $local) ? self::baseUrl().substr($url, strlen($local)) : $url;
        }

        return self::baseUrl().'/'.ltrim($url, '/');
    }

    public function canonicalUrl(): string
    {
        if ($this->canonical) {
            return self::abs($this->canonical);
        }
        $path = request()->path();

        return self::baseUrl().($path === '/' ? '/' : '/'.$path);
    }

    public function robots(): string
    {
        if ($this->noindex || setting('seo_noindex_site', '0') === '1') {
            return 'noindex, nofollow';
        }

        return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    }

    public function fullTitle(): string
    {
        $site = setting('site_name', 'Crinkle Nest');
        $t = trim((string) $this->title);
        if ($t === '') {
            return setting('meta_title', $site);
        }

        return str_contains($t, $site) ? $t : $t.' | '.$site;
    }

    /* ---------------- Structured data (schema.org JSON-LD) ---------------- */

    public function businessSchema(): array
    {
        $base = self::baseUrl();
        $sameAs = array_values(array_filter([setting('social_instagram'), setting('social_facebook'), setting('social_youtube'), setting('map_url')], fn ($u) => $u && preg_match('~^https?://~', $u) && ! preg_match('~^https?://(www\.)?(instagram|facebook|youtube)\.com/?$~', $u)));

        $schema = [
            '@type' => setting('business_type', 'Bakery'),
            '@id' => $base.'/#business',
            'name' => setting('site_name', 'Crinkle Nest'),
            'url' => $base.'/',
            'description' => setting('meta_description'),
            'telephone' => setting('phone'),
            'email' => setting('email'),
            'priceRange' => setting('price_range', '₹₹'),
            'servesCuisine' => 'Bakery, Desserts',
            'currenciesAccepted' => setting('currency_code', 'INR'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => setting('address_street', setting('address_short')),
                'addressLocality' => setting('address_city'),
                'addressRegion' => setting('address_region'),
                'postalCode' => setting('address_postcode'),
                'addressCountry' => setting('address_country', 'IN'),
            ],
            'areaServed' => setting('delivery_area'),
            'hasMenu' => $base.'/menu',
            'sameAs' => $sameAs ?: null,
        ];
        if ($logo = setting('logo')) {
            $schema['logo'] = self::abs($logo);
        }
        if ($img = setting('og_image', setting('logo'))) {
            $schema['image'] = self::abs($img);
        }
        if (setting('geo_lat') && setting('geo_lng')) {
            $schema['geo'] = ['@type' => 'GeoCoordinates', 'latitude' => (float) setting('geo_lat'), 'longitude' => (float) setting('geo_lng')];
        }
        if ($hours = $this->openingHours()) {
            $schema['openingHoursSpecification'] = $hours;
        }

        return array_filter($schema, fn ($v) => $v !== null && $v !== '');
    }

    /** Parse "Tu-Su 07:30-20:00, Sa 10:00-14:00" into OpeningHoursSpecification. */
    protected function openingHours(): array
    {
        $map = ['Mo' => 'Monday', 'Tu' => 'Tuesday', 'We' => 'Wednesday', 'Th' => 'Thursday', 'Fr' => 'Friday', 'Sa' => 'Saturday', 'Su' => 'Sunday'];
        $keys = array_keys($map);
        $out = [];
        foreach (array_filter(array_map('trim', explode(',', (string) setting('opening_hours_schema')))) as $part) {
            if (! preg_match('/^([A-Z][a-z])(?:-([A-Z][a-z]))?\s+(\d{2}:\d{2})-(\d{2}:\d{2})$/', $part, $m)) {
                continue;
            }
            $from = array_search($m[1], $keys, true);
            $to = $m[2] ? array_search($m[2], $keys, true) : $from;
            if ($from === false || $to === false) {
                continue;
            }
            $days = [];
            for ($i = $from; ; $i = ($i + 1) % 7) {
                $days[] = $map[$keys[$i]];
                if ($i === $to) {
                    break;
                }
            }
            $out[] = ['@type' => 'OpeningHoursSpecification', 'dayOfWeek' => $days, 'opens' => $m[3], 'closes' => $m[4]];
        }

        return $out;
    }

    public static function productSchema(Product $p): array
    {
        return array_filter([
            '@type' => 'Product',
            'name' => $p->name,
            'description' => $p->short_description ?: $p->detail_line,
            'image' => self::abs($p->image),
            'category' => optional($p->category)->name,
            'brand' => ['@type' => 'Brand', 'name' => setting('site_name', 'Crinkle Nest')],
            'offers' => [
                '@type' => 'Offer',
                'price' => number_format((float) $p->price, 2, '.', ''),
                'priceCurrency' => setting('currency_code', 'INR'),
                'availability' => 'https://schema.org/InStock',
                'url' => self::baseUrl().($p->category ? '/menu/'.$p->category->slug : '/menu'),
                'seller' => ['@id' => self::baseUrl().'/#business'],
            ],
        ]);
    }

    public function productList(iterable $products, string $name): static
    {
        $items = [];
        $i = 1;
        foreach ($products as $p) {
            $items[] = ['@type' => 'ListItem', 'position' => $i++, 'item' => self::productSchema($p)];
        }

        return $items ? $this->schema(['@type' => 'ItemList', 'name' => $name, 'itemListElement' => $items]) : $this;
    }

    public function faqList(iterable $faqs): static
    {
        $q = [];
        foreach ($faqs as $f) {
            /** @var Faq $f */
            $q[] = ['@type' => 'Question', 'name' => $f->question, 'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f->answer]];
        }

        return $q ? $this->schema(['@type' => 'FAQPage', 'mainEntity' => $q]) : $this;
    }

    /** Full JSON-LD @graph for the current page. */
    public function graph(): array
    {
        $base = self::baseUrl();
        $graph = [
            ['@type' => 'WebSite', '@id' => $base.'/#website', 'url' => $base.'/', 'name' => setting('site_name', 'Crinkle Nest'), 'inLanguage' => 'en-IN', 'publisher' => ['@id' => $base.'/#business']],
            $this->businessSchema(),
            ['@type' => 'WebPage', '@id' => $this->canonicalUrl().'#webpage', 'url' => $this->canonicalUrl(), 'name' => $this->fullTitle(), 'description' => $this->description ?: setting('meta_description'), 'isPartOf' => ['@id' => $base.'/#website'], 'about' => ['@id' => $base.'/#business'], 'inLanguage' => 'en-IN'],
        ];
        if ($this->breadcrumbs) {
            $items = [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => $base.'/']];
            foreach ($this->breadcrumbs as $i => [$name, $url]) {
                $items[] = ['@type' => 'ListItem', 'position' => $i + 2, 'name' => $name, 'item' => self::abs($url)];
            }
            $graph[] = ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
        }

        return ['@context' => 'https://schema.org', '@graph' => array_merge($graph, $this->schemas)];
    }
}
