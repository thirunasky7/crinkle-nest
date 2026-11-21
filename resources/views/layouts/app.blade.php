<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $siteName = setting('site_name', 'Crinkle Nest');
        $pageTitle = html_entity_decode(trim($__env->yieldContent('title')), ENT_QUOTES);
        $metaDescription = html_entity_decode(trim($__env->yieldContent('meta_description')), ENT_QUOTES) ?: setting('meta_description');
    @endphp
    <title>{{ $pageTitle ? $pageTitle.' | '.$siteName : setting('meta_title', $siteName) }}</title>
    @if($metaDescription)<meta name="description" content="{{ $metaDescription }}">@endif
    @if(setting('meta_keywords'))<meta name="keywords" content="{{ setting('meta_keywords') }}">@endif
    <meta property="og:title" content="{{ $pageTitle ?: setting('meta_title', $siteName) }}">
    @if($metaDescription)<meta property="og:description" content="{{ $metaDescription }}">@endif
    @if(setting('og_image'))<meta property="og:image" content="{{ media(setting('og_image')) }}">@endif
    @if(setting('favicon'))<link rel="icon" href="{{ media(setting('favicon')) }}">@endif
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&amp;family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}.no-scrollbar{scrollbar-width:none;}[x-cloak]{display:none!important}</style>
    @include('partials.tailwind-config')
    @stack('head')
    {!! setting('head_scripts') !!}
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased selection:bg-secondary-fixed selection:text-on-secondary-fixed">
@include('partials.header')

<main class="w-full pt-20 bg-surface min-h-[calc(100vh-80px)]">
    @include('partials.flash')
    @yield('content')
</main>

@include('partials.floating-whatsapp')
@include('partials.footer')

<script>
    function scrollSection(containerId, distance) {
        const c = document.getElementById(containerId);
        if (c) c.scrollBy({left: distance, behavior: 'smooth'});
    }
    (function () {
        const btn = document.getElementById('mobileMenuToggle');
        const menu = document.getElementById('mobileMenu');
        if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
        document.querySelectorAll('[data-dismiss-flash]').forEach(el => setTimeout(() => el.remove(), 6000));
    })();
</script>
@stack('scripts')
{!! setting('body_scripts') !!}
</body>
</html>
