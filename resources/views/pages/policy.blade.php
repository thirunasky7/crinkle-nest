@extends('layouts.app')

@section('title', (string) ($page->meta_title ?: $page->title))
@section('meta_description', (string) $page->meta_description)

@section('content')
@php
    $summary = section('policy.summary');
    $help = section('policy.help');
    $acc = section('policy.accreditations');
    $sections = $page->sections->where('is_active', true)->values();
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[720px] h-[360px] bg-secondary-fixed/40 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute top-80 right-[-10%] w-[420px] h-[420px] bg-tertiary-fixed/30 rounded-full blur-[120px] pointer-events-none -z-10"></div>

    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-space-lg md:pt-space-xl pb-space-lg">
        <div class="max-w-3xl flex flex-col items-start gap-space-sm">
            @if($page->status_chip)
            <div class="inline-flex items-center gap-space-xs px-space-md py-space-xs bg-surface-container rounded-full shadow-[0_2px_8px_rgba(61,35,20,0.04)]">
                <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                <span class="font-label-sm text-label-sm text-secondary tracking-widest uppercase">{{ $page->status_chip }}</span>
            </div>
            @endif
            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                {{ $page->title }} @if($page->title_highlight)<span class="italic font-normal text-secondary font-headline-lg">{{ $page->title_highlight }}</span>@endif
            </h1>
            @if($page->subtitle)<p class="font-body-lg text-body-lg text-on-surface-variant leading-relaxed">{{ $page->subtitle }}</p>@endif
            @if($summary->is_active && $summary->activeItems()->count())
            <div class="w-full mt-space-md grid grid-cols-2 sm:grid-cols-4 gap-space-sm">
                @foreach($summary->activeItems() as $chip)
                <div class="bg-surface-container-low p-space-sm rounded-xl flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-secondary text-[22px]">{{ $chip->icon }}</span>
                    <div class="flex flex-col min-w-0">
                        <span class="font-label-sm text-label-sm text-on-surface-variant truncate">{{ $chip->subtitle }}</span>
                        <span class="font-label-md text-label-md text-primary font-semibold truncate">{{ $chip->title }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </section>

    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin pb-space-xl">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
            <aside class="lg:col-span-4 sticky top-28 hidden lg:flex flex-col gap-space-md">
                @if($sections->count())
                <div class="bg-surface-container-low p-space-md rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)]">
                    <div class="flex items-center justify-between pb-space-xs mb-space-xs">
                        <span class="font-label-md text-label-md text-secondary uppercase tracking-widest">{{ $page->directory_label ?: 'Policy Directory' }}</span>
                        <span class="font-label-sm text-label-sm bg-surface-container px-2 py-0.5 rounded-full text-on-surface-variant">{{ $sections->count() }} Clauses</span>
                    </div>
                    <nav class="flex flex-col gap-1" id="policy-nav">
                        @foreach($sections as $i => $s)
                        <a class="group flex items-center gap-space-xs p-space-xs rounded-xl font-label-lg text-label-lg {{ $i === 0 ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} hover:bg-surface-container-high transition-colors" href="#section-{{ $i + 1 }}">
                            <span class="w-6 h-6 rounded-full bg-surface-container flex items-center justify-center font-label-sm text-label-sm text-secondary group-hover:bg-secondary group-hover:text-on-secondary transition-colors">{{ $i + 1 }}</span>
                            <span class="truncate">{{ $s->nav_title ?: $s->title }}</span>
                        </a>
                        @endforeach
                    </nav>
                </div>
                @endif
                @if($help->is_active)
                <div class="bg-primary text-on-primary p-space-md rounded-2xl shadow-[0_8px_24px_rgba(37,15,3,0.15)] flex flex-col gap-space-xs relative overflow-hidden">
                    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full bg-secondary/30 blur-xl pointer-events-none"></div>
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-secondary-fixed text-[20px]">{{ $help->eyebrow_icon ?: 'support_agent' }}</span>
                        <span class="font-label-sm text-label-sm uppercase tracking-widest text-secondary-fixed">{{ $help->eyebrow }}</span>
                    </div>
                    <p class="font-title-md text-title-md font-semibold">{{ $help->title }}</p>
                    <p class="font-body-sm text-body-sm text-surface-container-highest">{{ $help->subtitle }}</p>
                    @if($help->button_text)
                    <a class="mt-space-xs inline-flex items-center justify-center gap-space-xs px-space-md py-space-xs bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg hover:bg-secondary-container hover:text-on-secondary-container transition-all" href="{{ link_to_url($help->button_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                        <span class="material-symbols-outlined text-[16px]">chat</span>
                        <span>{{ $help->button_text }}</span>
                    </a>
                    @endif
                </div>
                @endif
                @if($acc->is_active && $acc->activeItems()->count())
                <div class="p-space-md bg-surface-container-lowest rounded-2xl flex flex-col gap-space-xs shadow-[0_2px_8px_rgba(61,35,20,0.03)]">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">{{ $acc->title }}</span>
                    @foreach($acc->activeItems() as $a)
                    <div class="flex items-center gap-space-xs text-primary font-body-sm text-body-sm">
                        <span class="material-symbols-outlined text-secondary text-[18px]">{{ $a->icon }}</span>
                        <span>{{ $a->title }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </aside>

            <div class="lg:col-span-8 flex flex-col gap-space-lg">
                @if($page->content)
                <article class="bg-surface-container-low rounded-2xl p-space-md md:p-space-lg shadow-[0_4px_20px_-2px_rgba(61,35,20,0.04)] flex flex-col gap-space-sm policy-content font-body-md text-body-md text-on-surface-variant leading-relaxed">
                    {!! $page->content !!}
                </article>
                @endif
                @foreach($sections as $i => $s)
                <article class="scroll-mt-28 bg-surface-container-low rounded-2xl p-space-md md:p-space-lg shadow-[0_4px_20px_-2px_rgba(61,35,20,0.04)] flex flex-col gap-space-sm" id="section-{{ $i + 1 }}">
                    <div class="flex items-start justify-between gap-space-sm">
                        <div class="flex items-center gap-space-xs">
                            <span class="w-8 h-8 rounded-full bg-surface-container-high text-secondary flex items-center justify-center font-label-md text-label-md font-bold">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            @if($s->eyebrow)<span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold">{{ $s->eyebrow }}</span>@endif
                        </div>
                        @if($s->icon)<span class="material-symbols-outlined text-secondary/60 text-[24px]">{{ $s->icon }}</span>@endif
                    </div>
                    <h2 class="font-headline-sm text-headline-sm text-primary">{{ $s->title }}</h2>
                    {!! $s->content !!}
                </article>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('head')
<style>
    .policy-content p { margin-bottom: .75rem; }
    .policy-content h2, .policy-content h3 { font-family: 'Playfair Display'; color: #250f03; font-size: 22px; line-height: 30px; font-weight: 600; margin: 1rem 0 .5rem; }
    .policy-content ul { list-style: disc; padding-left: 1.25rem; }
    .policy-content a { color: #8e4e08; text-decoration: underline; }
</style>
@endpush
