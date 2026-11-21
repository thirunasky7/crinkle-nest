@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
@php
    $head = section('orders.header');
    $tracker = section('orders.tracker');
    $banner = section('orders.whatsapp_banner');
    $assure = section('orders.assurance');
    $steps = $tracker->activeItems();
    $tabs = [
        'all' => 'All Orders ('.$counts['all'].')',
        'active' => 'In Oven ('.$counts['active'].')',
        'delivered' => 'Delivered ('.$counts['delivered'].')',
        'custom' => 'Custom Bakes',
    ];
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-24 right-1/4 w-96 h-96 bg-secondary-fixed/40 rounded-full blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-12 left-10 w-72 h-72 bg-tertiary-fixed/30 rounded-full blur-2xl pointer-events-none -z-10"></div>
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-6 md:pt-10 pb-space-xl">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md mb-space-lg">
            <div class="space-y-space-xs max-w-2xl">
                <div class="flex items-center gap-space-xs font-label-md text-label-md text-secondary">
                    <span>{{ $head->x('breadcrumb_root', 'Bakehouse Account') }}</span><span class="text-outline-variant">/</span><span class="text-on-surface-variant font-medium">{{ $head->x('breadcrumb', 'Hearth Track') }}</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight font-serif">{{ $head->title ?: 'My Orders' }}</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant">{{ $head->subtitle }}</p>
            </div>
            @auth
            <div class="flex items-center gap-space-sm bg-surface-container-low px-space-md py-space-sm rounded-xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.04)]">
                <div class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed"><span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">cookie</span></div>
                <div>
                    <div class="font-label-sm text-label-sm uppercase tracking-wider text-secondary">{{ $head->x('points_label', 'Reward Crumbs') }}</div>
                    <div class="font-title-md text-title-md text-primary">{{ number_format(auth()->user()->points) }} {{ setting('points_name', 'Nest Points') }}</div>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-space-md py-space-sm rounded-full bg-primary text-on-primary font-label-lg text-label-lg hover:bg-secondary transition-all"><span class="material-symbols-outlined text-[18px]">login</span>Sign in to see all orders</a>
            @endauth
        </div>

        @if(session('whatsapp_url'))
        <div class="mb-space-lg p-space-md rounded-2xl bg-emerald-50 text-emerald-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3"><span class="material-symbols-outlined text-emerald-700">check_circle</span><p class="font-body-md text-body-md">Your order is saved. Send it on WhatsApp so our bakers can confirm your slot.</p></div>
            <a href="{{ session('whatsapp_url') }}" target="_blank" rel="noopener noreferrer" class="shrink-0 px-5 py-2.5 rounded-full bg-whatsapp text-white font-label-lg text-label-lg hover:bg-whatsapp-dark inline-flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">chat</span>Send on WhatsApp</a>
        </div>
        @push('scripts')<script>setTimeout(() => { window.location.href = @json(session('whatsapp_url')); }, 1500);</script>@endpush
        @endif

        @if(! $activeOrder && $orders->isEmpty())
        <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] flex flex-col items-center text-center gap-space-sm mb-space-xl">
            <div class="w-16 h-16 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed"><span class="material-symbols-outlined text-[32px]">receipt_long</span></div>
            <h2 class="font-headline-sm text-headline-sm text-primary">{{ $head->x('empty_title', 'No orders yet') }}</h2>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-md">{{ $head->x('empty_text', 'Your fresh batches will appear here once you place an order.') }}</p>
            <a href="{{ route('menu') }}" class="mt-2 px-7 py-3 rounded-full bg-primary text-on-primary font-label-lg text-label-lg hover:bg-secondary transition-all inline-flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">restaurant_menu</span>Explore the Menu</a>
        </div>
        @endif

        @if($activeOrder)
        @php $o = $activeOrder; $step = $o->stepIndex(); @endphp
        <div class="relative w-full bg-surface-container-lowest rounded-lg p-space-md md:p-space-lg shadow-[0_12px_32px_-4px_rgba(61,35,20,0.08),0_4px_12px_-2px_rgba(200,125,56,0.06)] overflow-hidden mb-space-xl">
            <div class="flex flex-wrap items-center justify-between gap-space-sm pb-space-md">
                <div class="flex items-center gap-space-sm flex-wrap">
                    <div class="px-space-sm py-1 bg-tertiary-fixed text-on-tertiary-fixed font-label-sm text-label-sm uppercase tracking-wider rounded-full flex items-center gap-1.5 shadow-sm">
                        <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span></span>
                        {{ optional($steps->get($step))->label ?: $o->statusLabel() }}
                    </div>
                    <span class="font-title-md text-title-md text-primary font-bold">Order #{{ $o->order_number }}</span>
                    <span class="text-on-surface-variant font-body-sm text-body-sm hidden sm:inline">• Placed {{ $o->created_at->isToday() ? 'today' : $o->created_at->format('d M') }} at {{ $o->created_at->format('g:i A') }}</span>
                </div>
                @if($o->target_arrival || $o->slot_label)
                <div class="text-right">
                    <span class="font-label-sm text-label-sm text-on-surface-variant block uppercase tracking-wide">Target Arrival</span>
                    <span class="font-title-md text-title-md text-secondary font-bold">{{ $o->target_arrival ?: $o->slot_label }}</span>
                </div>
                @endif
            </div>
            @if($steps->count())
            <div class="my-space-md bg-surface-container-low p-space-md md:p-space-lg rounded-DEFAULT overflow-x-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-space-md relative min-w-0">
                    @foreach($steps as $i => $st)
                    @if($i < $step)
                    <div class="flex items-center md:items-start gap-space-sm"><div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center shadow-md flex-shrink-0"><span class="material-symbols-outlined text-[20px]">check</span></div><div><span class="font-title-md text-title-md text-primary font-semibold block text-sm">{{ $st->title }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $i === 0 ? $o->created_at->format('g:i A').' • ' : '' }}{{ $st->description }}</span></div></div>
                    @elseif($i === $step)
                    <div class="flex items-center md:items-start gap-space-sm"><div class="w-10 h-10 rounded-full bg-secondary text-on-secondary flex items-center justify-center shadow-[0_0_16px_rgba(142,78,8,0.45)] ring-4 ring-secondary-fixed flex-shrink-0"><span class="material-symbols-outlined text-[20px] animate-pulse">{{ $st->icon }}</span></div><div><div class="flex items-center gap-1.5"><span class="font-title-md text-title-md text-secondary font-bold block text-sm">{{ $st->title }}</span><span class="px-1.5 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed text-[10px] font-bold uppercase tracking-wider">Active</span></div><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $o->status_note ?: $st->description }}</span></div></div>
                    @else
                    <div class="flex items-center md:items-start gap-space-sm opacity-60"><div class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface-variant flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-[20px]">{{ $st->icon }}</span></div><div><span class="font-title-md text-title-md text-on-surface font-semibold block text-sm">{{ $st->title }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $st->description }}</span></div></div>
                    @endif
                    @endforeach
                </div>
                <div class="hidden md:block w-full bg-surface-container-highest h-1.5 rounded-full mt-6 overflow-hidden"><div class="bg-secondary h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ min(100, (int) round(($step + 0.9) / max(1, $steps->count()) * 100)) }}%;"></div></div>
            </div>
            @endif
            <div class="pt-space-sm pb-space-md">
                <h2 class="font-label-lg text-label-lg text-primary uppercase tracking-wider mb-space-sm">Items In This Batch</h2>
                <div class="m-rail grid grid-cols-1 md:grid-cols-3 gap-space-md">
                    @foreach($o->items as $it)
                    <div class="flex items-center gap-space-sm bg-surface-container-low/70 p-space-sm rounded-DEFAULT hover:bg-surface-container transition-colors">
                        <img class="w-16 h-16 rounded-xl object-cover shadow-sm flex-shrink-0" alt="{{ $it->name }}" src="{{ media($it->image) }}">
                        <div class="min-w-0 flex-1">
                            <span class="font-title-md text-title-md text-primary text-sm font-semibold truncate block">{{ $it->name }}</span>
                            <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $it->detail }}</span>
                            <div class="font-label-md text-label-md text-secondary font-bold mt-0.5">{{ $it->qty }} × {{ money($it->price) }}@if($it->qty > 1) ({{ money($it->total) }})@endif</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="pt-space-md flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-surface-container-lowest">
                <div class="flex items-center gap-space-md">
                    <div>
                        <span class="font-body-sm text-body-sm text-on-surface-variant block">Total Order Value</span>
                        <div class="flex items-baseline gap-space-xs">
                            <span class="font-headline-md text-headline-md text-primary font-bold">{{ money($o->total) }}</span>
                            <span class="font-label-sm text-label-sm text-on-surface-variant">({{ $o->payment_status === 'paid' ? 'Paid' : 'Payment pending' }}{{ $o->payment_note ? ' • '.$o->payment_note : '' }})</span>
                        </div>
                    </div>
                    @if($head->x('certified_text'))
                    <div class="h-8 w-px bg-surface-variant hidden sm:block"></div>
                    <div class="hidden sm:flex items-center gap-1.5 text-secondary font-label-md text-label-md"><span class="material-symbols-outlined text-[18px]">verified</span><span>{{ $head->x('certified_text') }}</span></div>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-space-xs sm:gap-space-sm w-full lg:w-auto">
                    @if($o->tracking_url)
                    <a href="{{ $o->tracking_url }}" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-primary text-on-primary rounded-full font-label-lg text-label-lg shadow-md hover:bg-primary-container transition-all flex items-center gap-2 transform hover:-translate-y-0.5"><span class="material-symbols-outlined text-[18px]">near_me</span><span>Track Live Delivery</span></a>
                    @endif
                    <a class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg shadow-sm hover:bg-secondary/90 transition-all flex items-center gap-2 transform hover:-translate-y-0.5" href="{{ wa_link('Hi '.setting('site_name', 'Crinkle Nest').'! Checking in on Order #'.$o->order_number) }}" rel="noopener noreferrer" target="_blank"><span class="material-symbols-outlined text-[18px]">chat</span><span>Chat with Baker</span></a>
                    <a href="{{ route('orders.invoice', $o->order_number) }}" target="_blank" class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-surface-container text-on-surface-variant rounded-full font-label-lg text-label-lg hover:bg-surface-container-high transition-all flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">download</span><span>Bake Slip</span></a>
                </div>
            </div>
        </div>
        @endif

        @if($banner->is_active)
        <div class="relative bg-gradient-to-r from-primary-container via-primary to-primary-container text-on-primary rounded-lg p-space-md md:p-space-lg mb-space-xl overflow-hidden shadow-lg">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-space-md">
                <div class="flex items-center gap-space-md">
                    <div class="w-14 h-14 rounded-full bg-secondary-container/20 flex items-center justify-center flex-shrink-0"><span class="material-symbols-outlined text-secondary-container text-[32px]">{{ $banner->eyebrow_icon ?: 'soup_kitchen' }}</span></div>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-primary font-serif">{{ $banner->title }}</h3>
                        <p class="font-body-md text-body-md text-on-primary-container mt-0.5">{{ $banner->subtitle }}</p>
                    </div>
                </div>
                @if($banner->button_text)
                <a class="px-space-lg py-space-xs bg-secondary-container text-on-secondary-container rounded-full font-label-lg text-label-lg font-bold flex items-center gap-space-xs shadow-md hover:bg-secondary-fixed transition-all flex-shrink-0" href="{{ link_to_url($banner->button_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[20px]">chat</span>
                    <span>{{ str_replace('{phone}', setting('whatsapp_display', setting('phone')), $banner->button_text) }}</span>
                </a>
                @endif
            </div>
        </div>
        @endif

        @if($counts['all'] > 0)
        <div class="flex flex-col gap-space-md">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                <div>
                    <h2 class="font-headline-md text-headline-md text-primary font-serif">{{ $head->x('history_title', 'Past Bakes') }}</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">{{ $head->x('history_subtitle') }}</p>
                </div>
                <div class="flex items-center gap-1 bg-surface-container-low p-1 rounded-full overflow-x-auto max-w-full">
                    @foreach($tabs as $key => $label)
                    <a href="{{ route('orders.index', ['filter' => $key]) }}" class="px-space-md py-space-xs rounded-full font-label-md text-label-md whitespace-nowrap {{ $filter === $key ? 'bg-surface-container-high text-primary font-bold shadow-sm' : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <div class="flex flex-col gap-space-md">
                @forelse($orders as $o)
                <div class="bg-surface-container-low rounded-lg p-space-md md:p-space-lg shadow-[0_4px_20px_-2px_rgba(61,35,20,0.04)] hover:shadow-[0_8px_28px_-4px_rgba(61,35,20,0.08)] transition-all">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md pb-space-sm">
                        <div class="flex flex-wrap items-center gap-space-sm">
                            <span class="font-title-md text-title-md text-primary font-bold">Order #{{ $o->order_number }}</span>
                            <span class="px-2.5 py-0.5 rounded-full {{ $o->label ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-surface-container-high text-on-surface-variant' }} font-label-sm text-label-sm font-semibold">
                                {{ $o->status === 'delivered' ? 'Delivered '.optional($o->delivered_at ?? $o->updated_at)->format('d M') : $o->statusLabel().' • '.$o->created_at->format('d M') }}{{ $o->label ? ' • '.$o->label : '' }}
                            </span>
                            @if($o->status === 'delivered')<span class="flex items-center gap-1 font-body-sm text-body-sm text-secondary"><span class="material-symbols-outlined text-[16px]">verified</span>{{ $head->x('delivered_note', 'Hand-delivered with care') }}</span>@endif
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-space-sm w-full sm:w-auto">
                            <span class="font-title-md text-title-md text-primary font-bold">{{ money($o->total) }} Total</span>
                            <a href="{{ route('orders.invoice', $o->order_number) }}" target="_blank" class="px-space-sm py-1 bg-surface-container text-on-surface-variant rounded-full font-label-sm text-label-sm hover:bg-surface-container-highest transition-colors">View Invoice</a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-sm my-space-sm">
                        @foreach($o->items as $it)
                        <div class="flex items-center gap-space-sm p-space-xs rounded-xl bg-surface-container-lowest">
                            <img class="w-14 h-14 rounded-lg object-cover flex-shrink-0" alt="{{ $it->name }}" src="{{ media($it->image) }}">
                            <div class="min-w-0 flex-1">
                                <h4 class="font-title-md text-title-md text-primary text-sm font-semibold truncate">{{ $it->name }}</h4>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $it->detail }}</span>
                                <div class="font-label-md text-label-md text-secondary font-bold">{{ $it->qty }} × {{ money($it->price) }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="pt-space-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-space-sm">
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($o->rating)
                            <span class="font-body-sm text-body-sm text-on-surface-variant">Your rating:</span>
                            <div class="flex text-secondary">@for($s = 1; $s <= 5; $s++)<span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' {{ $s <= $o->rating ? 1 : 0 }};">star</span>@endfor</div>
                            @if($o->review)<span class="font-label-sm text-label-sm text-on-surface-variant ml-1">"{{ $o->review }}"</span>@endif
                            @endif
                        </div>
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-space-xs w-full sm:w-auto">
                            <form method="POST" action="{{ route('orders.reorder', $o->order_number) }}">
                                @csrf
                                <button class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg shadow-sm hover:bg-primary transition-all flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">restart_alt</span><span>Reorder with 1-Click</span></button>
                            </form>
                            @if($o->status === 'delivered' && ! $o->rating)
                            <button type="button" onclick="document.getElementById('rate-{{ $o->id }}').classList.toggle('hidden')" class="w-full sm:w-auto justify-center px-space-sm py-space-xs bg-surface-container text-on-surface-variant rounded-full font-label-md text-label-md hover:bg-surface-container-high transition-colors">Rate Bakes</button>
                            @endif
                        </div>
                    </div>
                    @if($o->status === 'delivered' && ! $o->rating)
                    <form id="rate-{{ $o->id }}" method="POST" action="{{ route('orders.rate', $o->order_number) }}" class="hidden mt-space-sm p-space-sm rounded-xl bg-surface-container-lowest flex flex-col sm:flex-row gap-2 items-stretch sm:items-center">
                        @csrf
                        <select name="rating" class="h-10 px-3 rounded-full bg-surface-container-low font-label-md text-label-md">@for($s = 5; $s >= 1; $s--)<option value="{{ $s }}">{{ str_repeat('★', $s) }}</option>@endfor</select>
                        <input name="review" maxlength="180" placeholder="A short note for our bakers" class="flex-1 h-10 px-4 rounded-full bg-surface-container-low font-body-sm text-body-sm focus:outline-none focus:ring-1 focus:ring-secondary">
                        <button class="h-10 px-4 rounded-full bg-primary text-on-primary font-label-md text-label-md hover:bg-secondary">Submit</button>
                    </form>
                    @endif
                </div>
                @empty
                <p class="font-body-md text-body-md text-on-surface-variant p-space-md bg-surface-container-low rounded-lg">No orders in this view yet.</p>
                @endforelse
            </div>
        </div>
        @endif

        @if($assure->is_active && $assure->activeItems()->count())
        <div class="m-rail mt-space-xl grid grid-cols-1 sm:grid-cols-3 gap-space-md pt-space-md">
            @foreach($assure->activeItems() as $a)
            <div class="flex items-start gap-space-sm p-space-sm rounded-xl bg-surface-container-lowest shadow-sm">
                <div class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed flex-shrink-0"><span class="material-symbols-outlined text-[20px]">{{ $a->icon }}</span></div>
                <div>
                    <h5 class="font-title-md text-title-md text-primary text-sm font-semibold">{{ $a->title }}</h5>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $a->description }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
</div>
@endsection
