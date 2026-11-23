@php
    $seo = seo();
    $legacyTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES);
    $legacyDesc = html_entity_decode(trim($__env->yieldContent('meta_description')), ENT_QUOTES);
    if (! $seo->title && $legacyTitle) { $seo->title = $legacyTitle; }
    if (! $seo->description && $legacyDesc) { $seo->description = $legacyDesc; }
    $title = $seo->fullTitle();
    $desc = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', (string) ($seo->description ?: setting('meta_description')))), 160, '…');
    $image = \App\Support\Seo::abs($seo->image ?: setting('og_image', setting('logo')));
    $canonical = $seo->canonicalUrl();
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $desc }}">
@if($seo->keywords ?: setting('meta_keywords'))<meta name="keywords" content="{{ $seo->keywords ?: setting('meta_keywords') }}">@endif
<meta name="robots" content="{{ $seo->robots() }}">
<link rel="canonical" href="{{ $canonical }}">
<link rel="alternate" hreflang="en-IN" href="{{ $canonical }}">
<meta name="theme-color" content="{{ setting('color_surface', '#fef9f1') }}">
<meta name="format-detection" content="telephone=no">
<link rel="manifest" href="{{ route('manifest') }}">
@if(setting('favicon'))
<link rel="icon" href="{{ media(setting('favicon')) }}">
<link rel="apple-touch-icon" href="{{ media(setting('favicon')) }}">
@endif
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ setting('site_name', 'Crinkle Nest') }}">

{{-- Open Graph (Facebook, WhatsApp, Instagram, LinkedIn) --}}
<meta property="og:site_name" content="{{ setting('site_name', 'Crinkle Nest') }}">
<meta property="og:type" content="{{ $seo->type }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $desc }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:locale" content="en_IN">
@if($image)
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:alt" content="{{ $title }}">
@endif
{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $desc }}">
@if($image)<meta name="twitter:image" content="{{ $image }}">@endif
@if(setting('twitter_handle'))<meta name="twitter:site" content="{{ setting('twitter_handle') }}">@endif

{{-- Search engine / platform verification --}}
@if(setting('google_site_verification'))<meta name="google-site-verification" content="{{ setting('google_site_verification') }}">@endif
@if(setting('bing_site_verification'))<meta name="msvalidate.01" content="{{ setting('bing_site_verification') }}">@endif
@if(setting('facebook_domain_verification'))<meta name="facebook-domain-verification" content="{{ setting('facebook_domain_verification') }}">@endif
@if(setting('pinterest_verification'))<meta name="p:domain_verify" content="{{ setting('pinterest_verification') }}">@endif

{{-- Local SEO --}}
@if(setting('address_city'))<meta name="geo.placename" content="{{ setting('address_city') }}">@endif
@if(setting('address_country'))<meta name="geo.region" content="{{ setting('address_country') }}{{ setting('address_region') ? '-'.strtoupper(substr(setting('address_region'), 0, 2)) : '' }}">@endif
@if(setting('geo_lat') && setting('geo_lng'))<meta name="geo.position" content="{{ setting('geo_lat') }};{{ setting('geo_lng') }}"><meta name="ICBM" content="{{ setting('geo_lat') }}, {{ setting('geo_lng') }}">@endif

<script type="application/ld+json">{!! json_encode($seo->graph(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
