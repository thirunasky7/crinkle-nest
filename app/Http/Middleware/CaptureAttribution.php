<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Remembers where a visitor came from (UTM tags, ad click IDs, external referrer)
 * so orders, inquiries and newsletter signups can be attributed to campaigns.
 * First touch wins; a new UTM-tagged visit overrides it.
 */
class CaptureAttribution
{
    public function handle(Request $request, Closure $next)
    {
        app()->forgetInstance(\App\Support\Seo::class); // fresh SEO state per request

        if ($request->isMethod('GET') && ! $request->is('admin*') && $request->hasSession()) {
            $q = $request->query();
            $hasUtm = isset($q['utm_source']) || isset($q['gclid']) || isset($q['fbclid']);
            $session = $request->session();

            if ($hasUtm || ! $session->has('attribution')) {
                $referrer = (string) $request->headers->get('referer');
                $refHost = parse_url($referrer, PHP_URL_HOST);
                $external = $refHost && $refHost !== $request->getHost();

                $source = $q['utm_source'] ?? (isset($q['gclid']) ? 'google' : (isset($q['fbclid']) ? 'facebook' : ($external ? $refHost : null)));
                $medium = $q['utm_medium'] ?? (isset($q['gclid']) ? 'cpc' : (isset($q['fbclid']) ? 'paid_social' : ($external ? 'referral' : null)));

                if ($hasUtm || $external || ! $session->has('attribution')) {
                    $session->put('attribution', array_map(fn ($v) => $v === null ? null : Str::limit((string) $v, 180, ''), [
                        'utm_source' => $source ?? 'direct',
                        'utm_medium' => $medium ?? 'none',
                        'utm_campaign' => $q['utm_campaign'] ?? null,
                        'referrer' => $external ? $referrer : null,
                        'landing_page' => '/'.ltrim($request->path(), '/'),
                    ]));
                }
            }
        }

        return $next($request);
    }
}
