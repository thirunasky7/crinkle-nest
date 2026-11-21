@extends('layouts.app')

@section('title', $current ? $current->name : section('menu.header')->x('meta_title', 'Menu'))

@section('content')
@php $head = section('menu.header'); @endphp
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[720px] h-[340px] bg-secondary-fixed-dim/20 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-8 md:pt-12 pb-6">
        <div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md mb-6">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('home') }}"><span class="material-symbols-outlined text-[16px]">cottage</span> Home</a>
            <span class="text-outline-variant">/</span>
            @if($current)
                <a class="hover:text-primary" href="{{ route('menu') }}">{{ $head->x('breadcrumb', 'Menu') }}</a><span class="text-outline-variant">/</span>
                <span class="text-secondary font-semibold">{{ $current->name }}</span>
            @else
                <span class="text-secondary font-semibold">{{ $head->x('breadcrumb', 'Menu') }}</span>
            @endif
        </div>
        <div class="max-w-3xl flex flex-col gap-3">
            @if($head->eyebrow)
            <div class="inline-flex items-center gap-1.5 text-secondary font-label-md text-label-md uppercase tracking-wider"><span class="material-symbols-outlined text-[16px]">{{ $current->eyebrow_icon ?? $head->eyebrow_icon ?? 'menu_book' }}</span> {{ $current->eyebrow ?? $head->eyebrow }}</div>
            @endif
            <h1 class="font-display text-display-mobile md:text-display text-primary tracking-tight">{{ $current->name ?? $head->title }}</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant">{{ $current->subtitle ?? $head->subtitle }}</p>
        </div>
        <div class="flex gap-2 overflow-x-auto no-scrollbar pt-6">
            <a href="{{ route('menu') }}" class="shrink-0 px-5 py-2.5 rounded-full font-label-lg text-label-lg transition-all {{ ! $current ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:text-primary hover:bg-surface-container' }}">All</a>
            @foreach($categories as $cat)
            <a href="{{ route('menu.category', $cat->slug) }}" class="shrink-0 px-5 py-2.5 rounded-full font-label-lg text-label-lg transition-all {{ optional($current)->id === $cat->id ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:text-primary hover:bg-surface-container' }}">{{ $cat->name }}</a>
            @endforeach
        </div>
    </section>

    @foreach($groups as $group)
    @if($group['products']->count())
    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-8" id="{{ $group['slug'] }}">
        @if(! $current)
        <div class="mb-6">
            @if($group['eyebrow'])<div class="inline-flex items-center gap-1.5 text-secondary font-label-md text-label-md uppercase tracking-wider mb-1"><span class="material-symbols-outlined text-[16px]">{{ $group['icon'] }}</span> {{ $group['eyebrow'] }}</div>@endif
            <h2 class="font-headline-lg text-headline-lg text-primary">{{ $group['title'] }}</h2>
            @if($group['subtitle'])<p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $group['subtitle'] }}</p>@endif
        </div>
        @endif
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($group['products'] as $product)
                @include('partials.product-card', ['product' => $product, 'variant' => 'category', 'priceLabel' => $group['price_label'], 'buttonText' => section('home.collections')->x('product_button_text', 'WhatsApp'), 'extraClass' => '!w-auto'])
            @endforeach
        </div>
    </section>
    @endif
    @endforeach

    @if(collect($groups)->sum(fn ($g) => $g['products']->count()) === 0)
    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin py-12">
        <div class="bg-surface-container-low rounded-2xl p-space-lg text-center">
            <p class="font-body-lg text-body-lg text-on-surface-variant">{{ $head->x('empty_text', 'Fresh bakes for this collection are coming soon. Message us on WhatsApp for today\'s batch.') }}</p>
            <a href="{{ wa_link() }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-whatsapp text-white font-label-lg text-label-lg hover:bg-whatsapp-dark"><span class="material-symbols-outlined text-[18px]">chat</span>Ask on WhatsApp</a>
        </div>
    </section>
    @endif
</div>
</div>
@endsection
