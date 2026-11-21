@extends('layouts.app')

@section('title', section('about.hero')->x('meta_title', 'About Us'))

@section('content')
@php
    $hero = section('about.hero');
    $mosaic = section('about.mosaic');
    $pillars = section('about.pillars');
    $founders = section('about.founders');
    $ritual = section('about.ritual');
    $reviews = section('about.testimonials');
    $cta = section('about.cta');
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[720px] h-[340px] bg-secondary-fixed-dim/20 rounded-full blur-[100px] pointer-events-none"></div>
    <section class="relative z-10 max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-8 md:pt-16 pb-12">
        @if($hero->is_active)
        <div class="flex flex-col items-center text-center max-w-4xl mx-auto">
            @if($hero->eyebrow)
            <div class="inline-flex items-center gap-space-xs px-space-md py-1 rounded-full bg-surface-container-high text-secondary mb-space-md shadow-sm">
                <span class="material-symbols-outlined text-[16px]">{{ $hero->eyebrow_icon ?: 'cookie' }}</span>
                <span class="font-label-sm text-label-sm uppercase tracking-widest font-semibold">{{ $hero->eyebrow }}</span>
            </div>
            @endif
            <h1 class="font-display text-display text-primary tracking-tight leading-[1.08] mb-space-md">
                {{ $hero->title }} <br class="hidden sm:inline"/>
                @if($hero->title_highlight)<span class="italic font-normal text-secondary">{{ $hero->title_highlight }}</span>@endif {{ $hero->x('title_suffix') }}
            </h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto leading-relaxed mb-space-lg">{{ $hero->subtitle }}</p>
            @if($hero->activeItems()->count())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-space-sm w-full pt-space-xs">
                @foreach($hero->activeItems() as $m)
                <div class="flex flex-col items-center justify-center p-space-md rounded-2xl bg-surface-container-low shadow-[0_4px_20px_-2px_rgba(61,35,20,0.04)]">
                    @if($m->icon)
                        <span class="font-headline-sm text-headline-sm text-secondary font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[20px]">{{ $m->icon }}</span> {{ $m->title }}</span>
                    @else
                        <span class="font-headline-sm text-headline-sm text-primary font-bold">{{ $m->title }}</span>
                    @endif
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider mt-0.5">{{ $m->subtitle }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        @if($mosaic->is_active)
        <div class="grid grid-cols-1 md:grid-cols-12 gap-gutter mt-space-xl items-end">
            <div class="md:col-span-7 flex flex-col gap-gutter">
                <div class="relative overflow-hidden rounded-2xl aspect-[16/10] shadow-[0_12px_32px_-4px_rgba(61,35,20,0.08)] group">
                    <img alt="{{ $mosaic->image_alt }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="{{ media($mosaic->image) }}"/>
                    @if($mosaic->x('main_caption'))
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between p-space-sm bg-surface/90 backdrop-blur-md rounded-xl">
                        <div class="flex items-center gap-space-xs">
                            <span class="material-symbols-outlined text-secondary text-[20px]">{{ $mosaic->x('main_caption_icon', 'oven_gen') }}</span>
                            <span class="font-label-md text-label-md text-primary">{{ $mosaic->x('main_caption') }}</span>
                        </div>
                        <span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-widest">{{ $mosaic->x('main_label') }}</span>
                    </div>
                    @endif
                </div>
                <div class="grid grid-cols-2 gap-gutter">
                    <div class="rounded-2xl overflow-hidden aspect-square shadow-sm bg-surface-container group">
                        <img alt="{{ $mosaic->x('image2_alt') }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ media($mosaic->image2) }}"/>
                    </div>
                    <div class="p-space-lg rounded-2xl bg-tertiary-fixed flex flex-col justify-between">
                        <span class="material-symbols-outlined text-tertiary text-[28px]">{{ $mosaic->x('card_icon', 'spa') }}</span>
                        <div>
                            <p class="font-headline-sm text-headline-sm text-tertiary mb-1">{{ $mosaic->x('card_title') }}</p>
                            <p class="font-body-sm text-body-sm text-on-tertiary-fixed-variant">{{ $mosaic->x('card_text') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:col-span-5 flex flex-col gap-gutter md:-mt-12">
                @if($mosaic->x('quote'))
                <div class="p-space-lg rounded-2xl bg-surface-container-high shadow-sm relative">
                    <span class="font-label-sm text-label-sm text-secondary tracking-widest uppercase font-bold block mb-space-xs">{{ $mosaic->x('quote_label') }}</span>
                    <blockquote class="font-headline-sm text-headline-sm text-primary italic leading-snug">“{{ $mosaic->x('quote') }}”</blockquote>
                    <span class="block mt-space-sm font-label-md text-label-md text-on-surface-variant font-semibold">— {{ $mosaic->x('quote_author') }}</span>
                </div>
                @endif
                <div class="relative overflow-hidden rounded-2xl aspect-[4/5] shadow-[0_12px_32px_-4px_rgba(61,35,20,0.08)] group">
                    <img alt="{{ $mosaic->x('image3_alt') }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" src="{{ media($mosaic->image3) }}"/>
                    @if($mosaic->x('image3_badge'))
                    <div class="absolute top-4 left-4">
                        <span class="px-space-sm py-1 bg-surface-container-lowest/90 backdrop-blur-md rounded-full font-label-sm text-label-sm text-secondary font-bold shadow-sm">{{ $mosaic->x('image3_badge') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </section>
</div>

@if($pillars->is_active)
<section class="w-full bg-surface-container-low py-space-xl my-space-lg m-tight">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-space-md md:mb-space-xl gap-space-sm md:gap-space-md">
            <div class="max-w-xl">
                <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold block mb-2">{{ $pillars->eyebrow }}</span>
                <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight leading-tight">{{ $pillars->title }}</h2>
            </div>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-md">{{ $pillars->subtitle }}</p>
        </div>
        <div class="m-rail grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
            @foreach($pillars->activeItems() as $i => $p)
            <div class="p-space-lg rounded-2xl bg-surface-container-lowest shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] hover:shadow-[0_12px_32px_-4px_rgba(61,35,20,0.1)] transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="w-12 h-12 rounded-xl {{ $i % 2 ? 'bg-primary-fixed text-primary' : 'bg-secondary-fixed text-secondary' }} flex items-center justify-center mb-space-md group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[24px]">{{ $p->icon }}</span>
                    </div>
                    <span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">{{ $p->label }}</span>
                    <h3 class="font-title-md text-title-md text-primary mt-1 mb-space-xs font-semibold">{{ $p->title }}</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $p->description }}</p>
                </div>
                @if($p->value)
                <div class="pt-space-md mt-space-md flex items-center gap-2 text-primary font-label-md text-label-md">
                    <span class="material-symbols-outlined text-[18px] text-secondary">check_circle</span>
                    <span>{{ $p->value }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($founders->is_active)
<section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-space-xl">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-center">
        <div class="lg:col-span-6 relative">
            <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-[0_16px_40px_-6px_rgba(61,35,20,0.12)]">
                <img alt="{{ $founders->image_alt }}" class="w-full h-full object-cover" src="{{ media($founders->image) }}"/>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/70 via-transparent to-transparent"></div>
                <div class="absolute bottom-6 left-6 right-6 text-on-primary">
                    <span class="font-label-sm text-label-sm uppercase tracking-widest text-secondary-fixed font-bold">{{ $founders->x('image_label') }}</span>
                    <h4 class="font-headline-md text-headline-md leading-tight mt-1">{{ $founders->x('image_title') }}</h4>
                    <p class="font-body-sm text-body-sm opacity-90 mt-1">{{ $founders->x('image_text') }}</p>
                </div>
            </div>
            @if($founders->x('badge_title'))
            <div class="hidden sm:flex absolute -bottom-6 -right-6 p-space-md bg-surface-container rounded-2xl shadow-lg max-w-xs items-center gap-space-sm">
                <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-on-secondary shrink-0">
                    <span class="material-symbols-outlined text-[20px]">{{ $founders->x('badge_icon', 'favorite') }}</span>
                </div>
                <div>
                    <p class="font-title-md text-title-md text-primary text-sm font-semibold">{{ $founders->x('badge_title') }}</p>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $founders->x('badge_text') }}</p>
                </div>
            </div>
            @endif
        </div>
        <div class="lg:col-span-6 flex flex-col gap-space-md lg:pl-space-md">
            <div class="inline-flex items-center gap-2 text-secondary font-label-md text-label-md font-semibold tracking-wider uppercase">
                <span class="w-6 h-[1.5px] bg-secondary"></span>
                <span>{{ $founders->eyebrow }}</span>
            </div>
            <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight leading-tight">“{{ $founders->title }}”</h2>
            <p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">{{ $founders->subtitle }}</p>
            @if($founders->body)<p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">{{ $founders->body }}</p>@endif
            @if($founders->activeItems()->count())
            <div class="p-space-md rounded-2xl bg-surface-container mt-space-xs">
                <div class="flex items-center justify-between border-b-0 pb-2">
                    <span class="font-title-md text-title-md text-primary font-semibold">{{ $founders->x('list_title') }}</span>
                </div>
                <ul class="flex flex-col gap-2 pt-2 font-body-sm text-body-sm text-on-surface-variant">
                    @foreach($founders->activeItems() as $rule)
                    <li class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-secondary">{{ $rule->icon ?: 'flare' }}</span>
                        <span>{{ $rule->title }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</section>
@endif

@if($ritual->is_active)
<section class="w-full bg-surface-container-high/40 py-space-xl my-space-lg m-tight">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin">
        <div class="text-center max-w-2xl mx-auto mb-space-md md:mb-space-xl">
            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold block mb-2">{{ $ritual->eyebrow }}</span>
            <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $ritual->title }}</h2>
            <p class="font-body-md text-body-md text-on-surface-variant mt-2">{{ $ritual->subtitle }}</p>
        </div>
        <div class="m-rail grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
            @foreach($ritual->activeItems() as $step)
            <div class="flex flex-col bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="relative aspect-[4/3] overflow-hidden group">
                    <img alt="{{ $step->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="{{ media($step->image) }}" loading="lazy"/>
                    @if($step->label)<span class="absolute top-3 left-3 bg-primary text-on-primary font-label-sm text-label-sm px-2.5 py-1 rounded-full font-bold">{{ $step->label }}</span>@endif
                </div>
                <div class="p-space-md flex flex-col flex-1 justify-between">
                    <div>
                        <h4 class="font-title-md text-title-md text-primary font-semibold mb-1">{{ $step->title }}</h4>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $step->description }}</p>
                    </div>
                    @if($step->value)
                    <div class="pt-space-sm mt-space-sm flex items-center text-secondary font-label-sm text-label-sm uppercase tracking-wider font-bold"><span>{{ $step->value }}</span></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($reviews->is_active && $testimonials->count())
<section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-space-xl">
    <div class="flex flex-col items-center text-center max-w-2xl mx-auto mb-space-md md:mb-space-xl">
        @if($reviews->eyebrow)
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold mb-2">
            <span class="material-symbols-outlined text-[16px]">{{ $reviews->eyebrow_icon ?: 'sentiment_satisfied' }}</span>
            <span>{{ $reviews->eyebrow }}</span>
        </div>
        @endif
        <h2 class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $reviews->title }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant mt-2">{{ $reviews->subtitle }}</p>
    </div>
    <div class="m-rail grid grid-cols-1 md:grid-cols-3 gap-gutter">
        @foreach($testimonials as $t)
        <div class="p-space-lg rounded-2xl bg-surface-container-low shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] flex flex-col justify-between relative group hover:bg-surface-container transition-colors">
            <div>
                <div class="flex items-center gap-1 text-secondary mb-space-md">
                    @for($s = 1; $s <= 5; $s++)
                        <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' {{ $s <= $t->rating ? 1 : 0 }};">star</span>
                    @endfor
                </div>
                <blockquote class="font-body-lg text-body-lg text-primary leading-relaxed italic mb-space-md">“{{ $t->quote }}”</blockquote>
            </div>
            <div class="flex items-center gap-space-sm pt-space-sm">
                <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 bg-surface-container-high flex items-center justify-center">
                    @if($t->avatar)
                        <img alt="Portrait of {{ $t->name }}" class="w-full h-full object-cover" src="{{ media($t->avatar) }}" loading="lazy"/>
                    @else
                        <span class="font-title-md text-title-md text-primary">{{ mb_substr($t->name, 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <span class="font-title-md text-title-md text-primary font-semibold block text-sm">{{ $t->name }}</span>
                    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $t->role }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif

@if($cta->is_active)
<section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin mb-space-xl">
    <div class="relative overflow-hidden rounded-2xl bg-primary text-on-primary p-space-lg md:p-space-xl shadow-[0_20px_48px_-8px_rgba(37,15,3,0.3)]">
        <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-secondary/30 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 w-80 h-80 rounded-full bg-secondary-container/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl flex flex-col gap-space-md">
            @if($cta->eyebrow)
            <div class="inline-flex items-center gap-2 text-secondary-fixed font-label-md text-label-md uppercase tracking-widest font-bold">
                <span class="material-symbols-outlined text-[18px]">{{ $cta->eyebrow_icon ?: 'local_cafe' }}</span>
                <span>{{ $cta->eyebrow }}</span>
            </div>
            @endif
            <h2 class="font-display text-headline-lg md:text-display leading-tight tracking-tight">{{ $cta->title }}</h2>
            <p class="font-body-lg text-body-lg text-primary-fixed-dim max-w-xl leading-relaxed">{{ $cta->subtitle }}</p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-sm pt-space-xs">
                @if($cta->button_text)
                <a class="inline-flex items-center justify-center gap-space-xs px-space-lg py-3 rounded-full bg-secondary text-on-secondary font-label-lg text-label-lg font-semibold shadow-[0_4px_16px_rgba(142,78,8,0.3)] hover:bg-secondary-container hover:text-on-secondary-container transition-all transform hover:-translate-y-0.5" href="{{ link_to_url($cta->button_link) }}">
                    <span class="material-symbols-outlined text-[20px]">restaurant_menu</span>
                    <span>{{ $cta->button_text }}</span>
                </a>
                @endif
                @if($cta->button2_text)
                <a class="inline-flex items-center justify-center gap-space-xs px-space-lg py-3 rounded-full bg-surface-container-low text-primary font-label-lg text-label-lg font-semibold shadow-sm hover:bg-surface-container-highest transition-all transform hover:-translate-y-0.5" href="{{ link_to_url($cta->button2_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[20px] text-secondary">chat</span>
                    <span>{{ str_replace('{phone}', setting('whatsapp_display', setting('phone')), $cta->button2_text) }}</span>
                </a>
                @endif
            </div>
        </div>
    </div>
</section>
@endif
</div>
@endsection
