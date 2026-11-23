<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Section;
use App\Models\Subscriber;
use App\Support\Seo;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    /** Dynamic robots.txt (editable in Admin → Settings → SEO & Search). */
    public function robots()
    {
        $base = Seo::baseUrl();
        if (setting('seo_noindex_site', '0') === '1') {
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $body = trim((string) setting('robots_txt', "User-agent: *\nAllow: /\nDisallow: /admin"))."\n\nSitemap: {$base}/sitemap.xml\n";
        }

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8', 'Cache-Control' => 'public, max-age=3600']);
    }

    /** XML sitemap with image entries, built from the database. */
    public function sitemap()
    {
        $base = Seo::baseUrl();
        $latest = fn ($q) => optional($q)->toAtomString();
        $hero = Section::where('key', 'home.hero')->first();
        $urls = [];

        $urls[] = ['loc' => $base.'/', 'lastmod' => $latest(Section::max('updated_at') ? now()->parse(Section::max('updated_at')) : null), 'priority' => '1.0', 'changefreq' => 'daily',
            'images' => array_filter([$hero?->image ? ['loc' => Seo::abs($hero->image), 'title' => setting('site_name')] : null])];
        $urls[] = ['loc' => $base.'/menu', 'priority' => '0.9', 'changefreq' => 'daily', 'lastmod' => $latest(now()->parse(Product::max('updated_at') ?? now())),
            'images' => Product::active()->take(50)->get()->filter(fn ($p) => $p->image)->map(fn ($p) => ['loc' => Seo::abs($p->image), 'title' => $p->name])->values()->all()];
        foreach (Category::active()->whereHas('products', fn ($q) => $q->where('is_active', true))->get() as $c) {
            $urls[] = ['loc' => $base.'/menu/'.$c->slug, 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => $latest($c->updated_at),
                'images' => $c->image ? [['loc' => Seo::abs($c->image), 'title' => $c->name]] : []];
        }
        $urls[] = ['loc' => $base.'/about', 'priority' => '0.7', 'changefreq' => 'monthly'];
        $urls[] = ['loc' => $base.'/contact', 'priority' => '0.7', 'changefreq' => 'monthly'];
        foreach (Page::where('is_active', true)->get() as $p) {
            $urls[] = ['loc' => $base.'/page/'.$p->slug, 'priority' => '0.3', 'changefreq' => 'yearly', 'lastmod' => $latest($p->updated_at)];
        }

        return response()->view('seo.sitemap', compact('urls'), 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /** Web app manifest — lets phones "Add to Home Screen" like an app. */
    public function manifest()
    {
        $icon = setting('favicon') ?: setting('logo');
        $icons = $icon ? [['src' => Seo::abs($icon), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable']] : [];

        return response()->json([
            'name' => setting('site_name', 'Crinkle Nest').' '.setting('site_tagline', ''),
            'short_name' => setting('site_name', 'Crinkle Nest'),
            'description' => setting('meta_description'),
            'start_url' => '/?utm_source=pwa&utm_medium=homescreen',
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => setting('color_surface', '#fef9f1'),
            'theme_color' => setting('color_surface', '#fef9f1'),
            'icons' => $icons,
        ], 200, ['Content-Type' => 'application/manifest+json']);
    }

    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:190',
            'name' => 'nullable|string|max:120',
            'source' => 'nullable|string|max:60',
            'website' => 'nullable|max:0', // honeypot: bots fill this
        ]);
        $att = attribution();
        $sub = Subscriber::firstOrNew(['email' => strtolower($data['email'])]);
        $isNew = ! $sub->exists;
        $sub->fill(array_filter([
            'name' => $data['name'] ?? $sub->name,
            'source' => $sub->source ?: ($data['source'] ?? 'footer'),
            'utm_source' => $sub->utm_source ?: ($att['utm_source'] ?? null),
            'utm_campaign' => $sub->utm_campaign ?: ($att['utm_campaign'] ?? null),
        ]) + ['is_active' => true])->save();

        if ($isNew) {
            track_event('sign_up', ['method' => 'newsletter']);
        }

        return back()->with('success', setting('newsletter_success', 'You are on the list!'))->with('subscribed', true);
    }
}
