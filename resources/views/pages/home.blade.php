@extends('layouts.app')


@section('content')
@php
    $hero = section('home.hero');
    $shorts = section('home.shorts');
    $trending = section('home.trending');
    $collections = section('home.collections');
    $promo = section('home.promo');
    $about = section('home.about');
    $cta = section('home.custom_cta');
@endphp
<div class="flex flex-col w-full selection:bg-secondary-fixed selection:text-on-secondary-fixed">

@if($hero->is_active)
<!-- HERO BANNER -->
<section class="relative w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-4 pb-12">
    <div class="relative overflow-hidden rounded-3xl bg-surface-container-low shadow-[0_12px_40px_-8px_rgba(61,35,20,0.08)]">
        <div class="absolute -top-32 -left-20 w-96 h-96 rounded-full bg-secondary-container/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-16 w-80 h-80 rounded-full bg-tertiary-fixed/30 blur-2xl pointer-events-none"></div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-8 items-center p-4 sm:p-10 md:p-14 relative z-10">
            <div class="lg:col-span-7 flex flex-col items-start gap-4">
                @if($hero->x('rating_text'))
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-surface-container-highest/80 backdrop-blur-md text-primary font-label-md text-label-md shadow-sm">
                    <span class="text-secondary font-bold">{{ $hero->x('rating_text') }}</span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    <span class="text-on-surface-variant">{{ $hero->x('rating_note') }}</span>
                </div>
                @endif
                <h1 class="font-display text-display text-primary tracking-tight leading-[1.08] max-w-xl">
                    {{ $hero->title }} @if($hero->title_highlight)<span class="italic font-normal text-secondary">{{ $hero->title_highlight }}</span>@endif
                </h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-lg">{{ $hero->subtitle }}</p>
                @if($hero->activeItems()->count())
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @foreach($hero->activeItems() as $chip)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface text-on-surface font-label-sm text-label-sm shadow-sm">
                        <span class="material-symbols-outlined text-secondary text-[16px]">{{ $chip->icon }}</span> {{ $chip->title }}
                    </span>
                    @endforeach
                </div>
                @endif
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap items-center gap-2 sm:gap-3 pt-3 w-full sm:w-auto">
                    @if($hero->button_text)
                    <a class="px-4 sm:px-7 py-3.5 rounded-full bg-primary text-on-primary font-label-lg text-label-lg shadow-[0_6px_20px_-2px_rgba(37,15,3,0.3)] hover:bg-secondary transition-all transform hover:-translate-y-0.5 inline-flex items-center justify-center gap-2" href="{{ link_to_url($hero->button_link) }}">
                        <span class="whitespace-nowrap text-[13px] sm:text-label-lg">{{ $hero->button_text }}</span>
                        <span class="material-symbols-outlined text-[18px] hidden sm:inline">arrow_downward</span>
                    </a>
                    @endif
                    @if($hero->button2_text)
                    <a class="px-4 sm:px-7 py-3.5 rounded-full justify-center bg-whatsapp text-white font-label-lg text-label-lg shadow-[0_6px_20px_-2px_rgba(30,126,72,0.35)] hover:bg-whatsapp-dark transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2" href="{{ link_to_url($hero->button2_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        <span class="whitespace-nowrap text-[13px] sm:text-label-lg">{{ $hero->button2_text }}</span>
                    </a>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-5 relative order-first lg:order-none">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <div class="relative overflow-hidden rounded-2xl shadow-[0_16px_36px_-6px_rgba(61,35,20,0.18)] aspect-[16/11] sm:aspect-square">
                        <img alt="{{ $hero->image_alt }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700 ease-out" src="{{ media($hero->image) }}"/>
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/60 via-transparent to-transparent"></div>
                        @if($hero->x('pill_title'))
                        <div class="absolute bottom-2.5 left-2.5 right-2.5 sm:bottom-4 sm:left-4 sm:right-4 flex items-center justify-between gap-2 p-2 sm:p-3 rounded-xl bg-surface/90 backdrop-blur-md shadow-md">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 sm:w-9 sm:h-9 shrink-0 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                                    <span class="material-symbols-outlined text-[16px] sm:text-[20px]">{{ $hero->x('pill_icon', 'cookie') }}</span>
                                </div>
                                <div>
                                    <p class="font-label-md text-label-md text-primary leading-tight line-clamp-1">{{ $hero->x('pill_title') }}</p>
                                    <p class="hidden sm:block font-body-sm text-body-sm text-on-surface-variant">{{ $hero->x('pill_text') }}</p>
                                </div>
                            </div>
                            @if($hero->x('pill_badge'))<span class="font-label-sm text-label-sm px-2.5 py-1 rounded-full bg-secondary-container text-on-secondary-container font-semibold">{{ $hero->x('pill_badge') }}</span>@endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@if($shorts->is_active && $videos->count())
<!-- SHORTS SECTION -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-8 m-tight">
    @include('partials.section-heading', ['eyebrow' => $shorts->eyebrow, 'icon' => $shorts->eyebrow_icon, 'title' => $shorts->title, 'subtitle' => $shorts->subtitle, 'track' => 'shorts-track', 'step' => 200, 'hideArrowsMobile' => true])
    <div class="m-bleed flex gap-3 sm:gap-4 overflow-x-auto pb-4 sm:pb-4 pt-1 sm:pt-2 snap-x scroll-smooth no-scrollbar" id="shorts-track">
        @foreach($videos as $video)
        @php [$vType, $vSrc] = $video->playable(); @endphp
        <div class="snap-start shrink-0 w-[118px] sm:w-[160px] rounded-2xl overflow-hidden relative group shadow-[0_4px_20px_-2px_rgba(61,35,20,0.06)] bg-surface-container {{ $vType ? 'cursor-pointer' : '' }}"
             @if($vType) onclick="openVideo(@js($vType), @js($vSrc), @js($video->title))" @endif>
            <div class="aspect-[9/16] relative overflow-hidden">
                @if($video->thumbnail)
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $video->title }}" src="{{ media($video->thumbnail) }}" loading="lazy"/>
                @elseif($vType === 'file')
                    <video class="w-full h-full object-cover" src="{{ $vSrc }}#t=0.5" muted preload="metadata"></video>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/20 to-black/20"></div>
                @if($video->duration)<span class="absolute top-3 right-3 px-2 py-0.5 rounded-full bg-black/60 text-white font-label-sm text-label-sm backdrop-blur-sm">{{ $video->duration }}</span>@endif
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-9 h-9 rounded-full bg-surface/90 text-primary flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[20px] ml-0.5" style="font-variation-settings: 'FILL' 1;">play_arrow</span>
                    </div>
                </div>
                <div class="absolute bottom-3 left-3 right-3 text-white">
                    <p class="font-title-md text-[12px] leading-snug line-clamp-2 drop-shadow-sm font-semibold">{{ $video->title }}</p>
                    @if($video->views_label)
                    <div class="flex items-center gap-1.5 mt-1 text-[11px] text-surface-variant font-label-sm">
                        <span class="material-symbols-outlined text-[14px]">visibility</span> {{ $video->views_label }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

@if($trending->is_active && $trendingProducts->count())
<!-- TRENDING NOW SECTION -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-10 m-tight" id="trending-section">
    @include('partials.section-heading', ['eyebrow' => $trending->eyebrow, 'icon' => $trending->eyebrow_icon, 'title' => $trending->title, 'subtitle' => $trending->subtitle, 'track' => 'trending-track', 'step' => 340])
    <div class="m-bleed flex gap-3 sm:gap-6 overflow-x-auto pb-4 sm:pb-6 pt-1 sm:pt-2 snap-x scroll-smooth no-scrollbar" id="trending-track">
        @foreach($trendingProducts as $product)
            @include('partials.product-card', ['product' => $product, 'variant' => 'trending', 'buttonText' => $trending->x('button_text', 'Order')])
        @endforeach
    </div>
</section>
@endif

@foreach($homeCategories as $category)
@if($category->activeProducts->count())
<!-- CATEGORY SECTION: {{ $category->name }} -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-10 m-tight" id="{{ $category->slug }}">
    @include('partials.section-heading', ['eyebrow' => $category->eyebrow, 'icon' => $category->eyebrow_icon, 'title' => $category->name, 'subtitle' => $category->subtitle, 'track' => 'track-'.$category->slug, 'step' => 320])
    <div class="m-bleed flex gap-3 sm:gap-5 overflow-x-auto pb-4 sm:pb-6 pt-1 sm:pt-2 snap-x scroll-smooth no-scrollbar" id="track-{{ $category->slug }}">
        @foreach($category->activeProducts as $product)
            @include('partials.product-card', ['product' => $product, 'variant' => 'category', 'priceLabel' => $category->price_label ?: 'Price', 'buttonText' => $collections->x('product_button_text', 'WhatsApp')])
        @endforeach
    </div>
</section>
@endif
@endforeach

@if($collections->is_active && $collectionCategories->count())
<!-- MORE BAKERY COLLECTIONS -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-10 m-tight" id="menu-catalog">
    @include('partials.section-heading', ['eyebrow' => $collections->eyebrow, 'icon' => $collections->eyebrow_icon, 'title' => $collections->title, 'subtitle' => $collections->subtitle, 'track' => 'collections-track', 'step' => 300])
    <div class="m-bleed flex gap-3 sm:gap-5 overflow-x-auto pb-4 sm:pb-6 pt-1 sm:pt-2 snap-x scroll-smooth no-scrollbar" id="collections-track">
        @foreach($collectionCategories as $category)
        <a class="group snap-start shrink-0 w-[150px] sm:w-[240px] md:w-[270px] rounded-2xl overflow-hidden relative shadow-[0_4px_20px_-2px_rgba(61,35,20,0.06)] bg-surface-container block" href="{{ $category->href() }}" @if(str_starts_with((string) $category->link, 'http')) target="_blank" rel="noopener noreferrer" @endif>
            <div class="aspect-[3/4] relative overflow-hidden">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $category->name }}" src="{{ media($category->image) }}" loading="lazy"/>
                <div class="absolute inset-0 bg-gradient-to-t from-primary via-primary/40 to-transparent"></div>
                @if($category->badge)<span class="absolute top-2 right-2 sm:top-3 sm:right-3 px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full bg-surface/90 backdrop-blur-sm text-primary font-label-sm text-[10px] sm:text-label-sm font-semibold">{{ $category->badge }}</span>@endif
                <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 text-white">
                    <h3 class="font-headline-sm text-headline-sm text-white">{{ $category->name }}</h3>
                    <p class="hidden sm:block font-body-sm text-body-sm text-surface-container-highest mt-1 line-clamp-2">{{ $category->card_description }}</p>
                    <span class="inline-flex items-center gap-1 font-label-md text-label-md text-secondary-fixed mt-2">
                        {{ $collections->x('card_cta', 'Browse & Order') }} <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

@if($promo->is_active)
<!-- SPECIAL PROMOTIONAL BANNER -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-8 m-tight">
    <div class="relative overflow-hidden rounded-3xl bg-tertiary-fixed/40 p-5 sm:p-12 md:p-14 shadow-[0_8px_30px_rgba(61,35,20,0.06)]">
        @if($promo->image)
            <img src="{{ media($promo->image) }}" alt="{{ $promo->image_alt }}" class="absolute inset-y-0 right-0 w-1/3 h-full object-cover opacity-90 hidden lg:block [mask-image:linear-gradient(to_right,transparent,black_40%)]">
        @endif
        <div class="absolute -top-16 -right-16 w-80 h-80 rounded-full bg-secondary-container/30 blur-3xl pointer-events-none"></div>
        <div class="max-w-2xl relative z-10 flex flex-col items-start gap-4">
            @if($promo->eyebrow)
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface/80 text-primary font-label-sm text-label-sm shadow-sm">
                <span class="material-symbols-outlined text-secondary text-[16px]">{{ $promo->eyebrow_icon ?: 'redeem' }}</span> {{ $promo->eyebrow }}
            </div>
            @endif
            <h2 class="font-display text-display text-primary tracking-tight leading-tight">{{ $promo->title }}</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">{{ $promo->subtitle }}</p>
            @if($promo->activeItems()->count())
            <div class="flex flex-wrap items-center gap-2 pt-2 text-primary font-label-md text-label-md">
                @foreach($promo->activeItems() as $perk)
                <span class="inline-flex items-center gap-1 bg-surface/80 px-3 py-1.5 rounded-full shadow-sm">
                    <span class="material-symbols-outlined text-[16px] text-secondary">{{ $perk->icon }}</span> {{ $perk->title }}
                </span>
                @endforeach
            </div>
            @endif
            @if($promo->button_text)
            <div class="pt-3">
                <a class="px-8 py-3.5 rounded-full bg-primary text-on-primary font-label-lg text-label-lg shadow-[0_6px_20px_-2px_rgba(37,15,3,0.3)] hover:bg-secondary transition-all transform hover:-translate-y-0.5 inline-flex items-center gap-2.5" href="{{ link_to_url($promo->button_link ?: '{whatsapp}') }}" @if(! $promo->button_link || str_starts_with($promo->button_link, '{whatsapp') || str_starts_with($promo->button_link, 'http')) rel="noopener noreferrer" target="_blank" @endif>
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    <span>{{ $promo->button_text }}</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@if($about->is_active)
<!-- ABOUT US SECTION -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-12 m-tight" id="about-us">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
        <div class="lg:col-span-6 relative">
            <div class="relative overflow-hidden rounded-3xl shadow-[0_16px_36px_-6px_rgba(61,35,20,0.14)] aspect-[4/3] sm:aspect-[16/11]">
                <img alt="{{ $about->image_alt }}" class="w-full h-full object-cover" src="{{ media($about->image) }}" loading="lazy"/>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/40 via-transparent to-transparent"></div>
            </div>
            @if($about->x('badge_title'))
            <div class="absolute -bottom-5 -right-2 sm:right-6 bg-surface-container-lowest px-4 py-3 rounded-2xl shadow-[0_10px_25px_-5px_rgba(61,35,20,0.12)] flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                    <span class="material-symbols-outlined text-[22px]">{{ $about->x('badge_icon', 'favorite') }}</span>
                </div>
                <div>
                    <p class="font-title-md text-[14px] text-primary font-bold leading-none">{{ $about->x('badge_title') }}</p>
                    <p class="font-body-sm text-[12px] text-on-surface-variant mt-0.5">{{ $about->x('badge_text') }}</p>
                </div>
            </div>
            @endif
        </div>
        <div class="lg:col-span-6 flex flex-col items-start gap-4">
            @if($about->eyebrow)
            <div class="inline-flex items-center gap-1.5 text-secondary font-label-md text-label-md uppercase tracking-wider">
                <span class="material-symbols-outlined text-[16px]">{{ $about->eyebrow_icon }}</span> {{ $about->eyebrow }}
            </div>
            @endif
            <h2 class="font-headline-lg text-headline-lg text-primary leading-tight">{{ $about->title }}</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant">{{ $about->subtitle }}</p>
            @if($about->activeItems()->count())
            <div class="grid grid-cols-3 gap-2 sm:gap-3 w-full pt-2">
                @foreach($about->activeItems() as $hl)
                <div class="p-2.5 sm:p-3.5 rounded-xl bg-surface-container-low flex flex-col items-start gap-1">
                    <span class="material-symbols-outlined text-secondary text-[22px]">{{ $hl->icon }}</span>
                    <span class="font-title-md text-[14px] text-primary font-bold">{{ $hl->title }}</span>
                    <span class="font-body-sm text-[12px] text-on-surface-variant">{{ $hl->subtitle }}</span>
                </div>
                @endforeach
            </div>
            @endif
            @if($about->button_text)
            <div class="pt-2">
                <a class="px-6 py-3 rounded-full bg-secondary text-on-secondary font-label-md text-label-md shadow-sm hover:bg-primary transition-all inline-flex items-center gap-2" href="{{ link_to_url($about->button_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                    <span>{{ $about->button_text }}</span>
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@if($cta->is_active)
<!-- QUICK ORDER CTA -->
<section class="w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-8 mb-4">
    <div class="bg-surface-container-low rounded-2xl p-5 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed shrink-0">
                <span class="material-symbols-outlined text-[26px]">{{ $cta->eyebrow_icon ?: 'chat_bubble' }}</span>
            </div>
            <div>
                <h3 class="font-title-md text-title-md text-primary">{{ $cta->title }}</h3>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ $cta->subtitle }}</p>
            </div>
        </div>
        @if($cta->button_text)
        <a class="shrink-0 px-6 py-3 rounded-full bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:bg-secondary transition-all inline-flex items-center gap-2" href="{{ link_to_url($cta->button_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
            <span class="material-symbols-outlined text-[18px]">send</span> {{ $cta->button_text }}
        </a>
        @endif
    </div>
</section>
@endif
</div>

@include('partials.video-modal')
@endsection
