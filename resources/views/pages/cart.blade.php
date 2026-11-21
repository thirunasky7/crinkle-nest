@extends('layouts.app')

@section('title', 'Cart & Checkout')

@section('content')
@php
    $head = section('cart.header');
    $guar = section('cart.guarantees');
    $chef = section('cart.chef');
    $reco = section('cart.recommend');
    $s = $summary;
    $user = auth()->user();
    $addr = $user ? $user->addresses->first() : null;
    $inputCls = 'w-full h-12 pl-11 pr-4 bg-surface-container-low text-primary rounded-xl font-body-md text-body-md focus:outline-none focus:bg-surface-container-lowest focus:ring-1 focus:ring-secondary transition-all';
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-space-md pb-space-xl">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm mb-space-md w-full">
        <nav class="flex items-center gap-space-xs font-label-md text-label-md text-on-surface-variant flex-wrap">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('menu') }}"><span class="material-symbols-outlined text-[16px]">storefront</span><span>{{ $head->x('breadcrumb_root', 'Bakehouse Store') }}</span></a>
            <span class="text-outline-variant">/</span>
            <span class="text-secondary font-semibold">{{ $head->x('breadcrumb', 'Cart & Checkout') }}</span>
        </nav>
        @if($head->x('status_text'))
        <div class="inline-flex items-center gap-2 bg-surface-container px-3 py-1.5 rounded-full shadow-sm w-fit max-w-full">
            <span class="relative flex h-2 w-2 flex-shrink-0"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span></span>
            <span class="font-label-sm text-label-sm text-on-surface tracking-wider uppercase truncate">{{ $head->x('status_text') }}</span>
        </div>
        @endif
    </div>
    <div class="mb-space-lg">
        <span class="font-label-sm text-label-sm uppercase tracking-widest text-secondary font-bold">{{ $head->eyebrow }}</span>
        <h1 class="font-headline-lg text-headline-lg text-primary mt-1 tracking-tight">{{ $head->title ?: 'Your Basket & Checkout' }}</h1>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl mt-1">{{ $head->subtitle }}</p>
    </div>

    @if($s['lines']->isEmpty())
    <div class="bg-surface-container-lowest rounded-2xl p-space-lg md:p-space-xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] flex flex-col items-center text-center gap-space-sm">
        <div class="w-16 h-16 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed"><span class="material-symbols-outlined text-[32px]">shopping_basket</span></div>
        <h2 class="font-headline-sm text-headline-sm text-primary">{{ $head->x('empty_title', 'Your basket is waiting for warm bakes') }}</h2>
        <p class="font-body-md text-body-md text-on-surface-variant max-w-md">{{ $head->x('empty_text', 'Add treats from our menu to start a fresh batch order.') }}</p>
        <a href="{{ route('menu') }}" class="mt-2 px-7 py-3 rounded-full bg-primary text-on-primary font-label-lg text-label-lg hover:bg-secondary transition-all inline-flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">restaurant_menu</span>Explore the Menu</a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        <div class="lg:col-span-7 flex flex-col gap-space-lg">
            <div class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]">
                <div class="flex items-center justify-between pb-space-sm border-none mb-space-sm gap-2 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[22px]">cookie</span>
                        <h2 class="font-title-md text-title-md text-primary font-semibold">{{ $head->x('items_title', 'Selected Bakehouse Goods') }} ({{ $s['count'] }} {{ \Illuminate\Support\Str::plural('item', $s['count']) }})</h2>
                    </div>
                    @if($head->x('items_badge'))<span class="font-label-md text-label-md text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-full">{{ $head->x('items_badge') }}</span>@endif
                </div>
                @foreach($s['lines'] as $line)
                @php $p = $line->product; @endphp
                <div class="py-space-md flex flex-col sm:flex-row gap-space-md items-start sm:items-center bg-surface-container-low/50 rounded-xl p-space-sm {{ $loop->last ? '' : 'mb-space-sm' }} transition-all hover:bg-surface-container-low">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-lg overflow-hidden flex-shrink-0 bg-surface-container">
                        <img class="w-full h-full object-cover" alt="{{ $p->name }}" src="{{ media($p->image) }}">
                        @if($p->image_tag)<span class="absolute bottom-1 left-1 bg-primary/90 text-on-primary font-label-sm text-[10px] px-1.5 py-0.5 rounded-full backdrop-blur-sm">{{ $p->image_tag }}</span>@endif
                    </div>
                    <div class="flex-1 min-w-0 w-full">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-title-md text-[16px] leading-snug text-primary font-semibold">{{ $p->name }}</h3>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">{{ $p->detail_line ?: $p->short_description }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="font-title-md text-title-md text-primary block">{{ money($line->total) }}</span>
                                <span class="font-label-sm text-[11px] text-secondary">{{ $line->qty > 1 ? money($p->price).' × '.$line->qty : money($p->price).' each' }}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center justify-between gap-space-xs mt-space-xs pt-2">
                            <div class="flex items-center gap-2">
                                @if($p->tag)
                                <span class="inline-flex items-center gap-1 font-label-sm text-[11px] bg-tertiary-fixed text-primary px-2 py-0.5 rounded-full font-semibold"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span> {{ $p->tag }}</span>
                                @endif
                                @if($p->tag_note)<span class="font-label-sm text-[11px] text-on-surface-variant/80 hidden sm:inline">{{ $p->tag_note }}</span>@endif
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $p) }}" class="inline-flex items-center bg-surface-container rounded-full p-1 shadow-inner">
                                    @csrf @method('PATCH')
                                    <button aria-label="Decrease quantity" name="qty" value="{{ $line->qty - 1 }}" class="w-7 h-7 rounded-full bg-surface-container-lowest text-primary flex items-center justify-center hover:bg-secondary-fixed hover:text-on-secondary-fixed transition-colors" type="submit"><span class="material-symbols-outlined text-[16px]">{{ $line->qty > 1 ? 'remove' : 'delete' }}</span></button>
                                    <span class="w-8 text-center font-title-md text-[14px] text-primary font-semibold">{{ $line->qty }}</span>
                                    <button aria-label="Increase quantity" name="qty" value="{{ $line->qty + 1 }}" class="w-7 h-7 rounded-full bg-surface-container-lowest text-primary flex items-center justify-center hover:bg-secondary-fixed hover:text-on-secondary-fixed transition-colors" type="submit"><span class="material-symbols-outlined text-[16px]">add</span></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                @if($upsell && ! $s['lines']->contains(fn ($l) => $l->product->id === $upsell->id))
                <div class="mt-space-md p-space-sm bg-secondary-fixed/30 rounded-xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-space-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary text-on-secondary flex items-center justify-center flex-shrink-0 shadow-sm"><span class="material-symbols-outlined text-[20px]">{{ $upsell->icon ?: 'liquor' }}</span></div>
                        <div>
                            <p class="font-title-md text-[14px] text-primary font-semibold">Pair with {{ $upsell->name }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $upsell->short_description }} (+{{ money($upsell->price) }})</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('cart.add', $upsell) }}" class="self-end sm:self-auto">
                        @csrf
                        <button class="px-3.5 py-1.5 bg-primary text-on-primary rounded-full font-label-sm text-label-sm hover:bg-secondary transition-all shadow-sm flex-shrink-0 flex items-center gap-1" type="submit"><span class="material-symbols-outlined text-[14px]">add</span><span>Add</span></button>
                    </form>
                </div>
                @endif

                <div class="mt-space-md pt-space-xs">
                    <details class="group bg-surface-container-low rounded-xl p-space-sm transition-all open:bg-surface-container" @if(old('gift_note')) open @endif>
                        <summary class="flex items-center justify-between cursor-pointer list-none select-none">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-[20px]">history_edu</span>
                                <span class="font-title-md text-[14px] text-primary font-medium">{{ $head->x('gift_note_title', 'Add Complimentary Handwritten Parchment Note') }}</span>
                            </div>
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px] transition-transform duration-200 group-open:rotate-180">expand_more</span>
                        </summary>
                        <div class="mt-space-sm pt-space-xs flex flex-col gap-2">
                            <label class="font-label-sm text-label-sm text-on-surface-variant" for="gift-note-text">{{ $head->x('gift_note_help', 'We hand-pen this on vintage botanical paper:') }}</label>
                            <textarea form="checkoutForm" name="gift_note" class="w-full p-space-sm bg-surface-container-lowest text-primary rounded-xl font-body-sm text-body-sm focus:outline-none focus:ring-1 focus:ring-secondary placeholder:text-outline/60 resize-none" id="gift-note-text" placeholder="e.g. Wishing you warm comfort on your special day!" rows="2">{{ old('gift_note') }}</textarea>
                        </div>
                    </details>
                </div>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]">
                <div class="flex items-center justify-between pb-space-sm mb-space-sm">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[22px]">local_shipping</span>
                        <h2 class="font-title-md text-title-md text-primary font-semibold">Delivery &amp; Customer Information</h2>
                    </div>
                    <span class="font-label-sm text-label-sm text-secondary font-bold uppercase tracking-wider">Step 2 of 2</span>
                </div>
                <form id="checkoutForm" method="POST" action="{{ route('checkout') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm mb-space-md">
                        <div class="sm:col-span-2">
                            <label class="block font-label-md text-label-md text-primary mb-1" for="cust-name">Full Recipient Name *</label>
                            <div class="relative">
                                <input class="{{ $inputCls }}" id="cust-name" name="name" required placeholder="Enter recipient full name" type="text" value="{{ old('name', $addr->recipient ?? optional($user)->fullName()) }}">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px]">person</span>
                            </div>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-primary mb-1" for="cust-phone">WhatsApp &amp; Mobile Number *</label>
                            <div class="relative">
                                <input class="{{ $inputCls }}" id="cust-phone" name="phone" required placeholder="+91 XXXXX XXXXX" type="tel" value="{{ old('phone', $addr->phone ?? optional($user)->phone) }}">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-secondary text-[20px]">chat</span>
                            </div>
                            <p class="font-body-sm text-[11px] text-on-surface-variant mt-1">Live oven-to-courier updates are pinged directly here.</p>
                        </div>
                        <div>
                            <label class="block font-label-md text-label-md text-primary mb-1" for="cust-email">Email (For Printable Bill)</label>
                            <div class="relative">
                                <input class="{{ $inputCls }}" id="cust-email" name="email" placeholder="name@domain.com" type="email" value="{{ old('email', optional($user)->email) }}">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px]">mail</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-space-sm mb-space-md">
                        <div>
                            <label class="block font-label-md text-label-md text-primary mb-1" for="cust-address">Street Address, Apartment &amp; Landmark *</label>
                            <div class="relative">
                                <input class="{{ $inputCls }}" id="cust-address" name="address" required placeholder="House/Flat number, building name, street" type="text" value="{{ old('address', $addr->line1 ?? '') }}">
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px]">home_pin</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                            <div>
                                <label class="block font-label-md text-label-md text-primary mb-1" for="cust-city">City &amp; Postal Pincode *</label>
                                <div class="relative">
                                    <input class="{{ $inputCls }}" id="cust-city" name="city" required placeholder="City - Pincode" type="text" value="{{ old('city', $addr->line2 ?? '') }}">
                                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px]">location_city</span>
                                </div>
                            </div>
                            <div>
                                <label class="block font-label-md text-label-md text-primary mb-1" for="cust-instructions">Delivery Instruction</label>
                                <div class="relative">
                                    <input class="{{ $inputCls }}" id="cust-instructions" name="instructions" placeholder="Gate instructions, contactless drop" type="text" value="{{ old('instructions', $addr->note ?? '') }}">
                                    <span class="material-symbols-outlined absolute left-3.5 top-3 text-outline text-[20px]">notifications_active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($slots->count())
                    <div class="pt-space-xs">
                        <label class="block font-label-md text-label-md text-primary mb-space-xs flex items-center justify-between">
                            <span>Select Dispatch Slot *</span>
                            @if($head->x('slot_note'))<span class="font-body-sm text-[12px] text-secondary font-normal flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">local_fire_department</span>{{ $head->x('slot_note') }}</span>@endif
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-xs w-full">
                            @foreach($slots as $slot)
                            <label class="cursor-pointer block">
                                <input @checked(old('slot_id', $slots->first()->id) == $slot->id) class="peer sr-only" name="slot_id" type="radio" value="{{ $slot->id }}" data-custom="{{ $slot->is_custom ? 1 : 0 }}" onchange="toggleCustomDate()">
                                <div class="h-full p-space-sm bg-surface-container-low rounded-xl border-none transition-all peer-checked:bg-secondary-fixed peer-checked:text-on-secondary-fixed peer-checked:shadow-sm hover:bg-surface-container flex flex-col justify-between">
                                    <div class="flex items-center justify-between">
                                        <span class="font-label-sm text-label-sm uppercase tracking-wide font-bold">{{ $slot->day_label }}</span>
                                        <span class="material-symbols-outlined text-[16px] {{ $slot->is_custom ? 'text-outline' : 'text-secondary' }}">{{ $slot->is_custom ? 'calendar_today' : 'check_circle' }}</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="font-title-md text-[14px] block font-semibold leading-tight">{{ $slot->title }}</span>
                                        <span class="font-body-sm text-[12px] opacity-80 block mt-0.5">{{ $slot->time_range }}</span>
                                    </div>
                                    @if($slot->badge)<span class="font-label-sm text-[10px] mt-2 inline-block bg-primary/10 px-2 py-0.5 rounded-full w-fit">{{ $slot->badge }}</span>@endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <div id="customDateWrap" class="hidden mt-space-sm">
                            <label class="block font-label-md text-label-md text-primary mb-1" for="custom-date">Choose your date</label>
                            <input class="w-full h-12 px-4 bg-surface-container-low text-primary rounded-xl font-body-md text-body-md focus:outline-none focus:ring-1 focus:ring-secondary" id="custom-date" name="custom_date" type="date" min="{{ now()->addDay()->toDateString() }}" value="{{ old('custom_date') }}">
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="lg:col-span-5 flex flex-col gap-space-md lg:sticky lg:top-24">
            <div class="bg-surface-container-lowest rounded-2xl p-space-md md:p-space-lg shadow-[0_12px_32px_-4px_rgba(61,35,20,0.08),0_4px_12px_-2px_rgba(200,125,56,0.06)] relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-32 h-32 bg-secondary-fixed/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="flex items-center justify-between pb-space-sm mb-space-sm">
                    <h2 class="font-title-md text-title-md text-primary font-semibold">Order Summary</h2>
                    <span class="font-label-sm text-label-sm bg-surface-container text-on-surface-variant px-2.5 py-1 rounded-full font-mono">#{{ $nextNumber }}</span>
                </div>
                @if($s['threshold'] > 0)
                <div class="bg-surface-container-low p-space-sm rounded-xl mb-space-md">
                    <div class="flex items-center justify-between text-body-sm font-label-md mb-1.5">
                        <span class="text-primary font-semibold flex items-center gap-1.5"><span class="material-symbols-outlined text-secondary text-[16px]">pedal_bike</span>{{ setting('delivery_label', 'Local Courier') }}</span>
                        <span class="text-secondary font-bold">{{ $s['remaining_for_free'] > 0 ? 'Add '.money($s['remaining_for_free']).' for FREE' : 'FREE delivery unlocked' }}</span>
                    </div>
                    <div class="w-full bg-surface-container-highest rounded-full h-2 overflow-hidden"><div class="bg-secondary h-2 rounded-full transition-all duration-500" style="width: {{ $s['progress'] }}%;"></div></div>
                    @if(setting('delivery_note'))<p class="font-body-sm text-[11px] text-on-surface-variant mt-1.5">{{ setting('delivery_note') }}</p>@endif
                </div>
                @endif
                <div class="space-y-space-xs font-body-md text-body-md text-on-surface-variant">
                    <div class="flex items-center justify-between"><span>Items Subtotal</span><span class="font-semibold text-primary">{{ money($s['subtotal']) }}</span></div>
                    @if(setting('packaging_label'))
                    <div class="flex items-center justify-between"><span>{{ setting('packaging_label') }}</span><span class="text-secondary font-semibold">Complimentary</span></div>
                    @endif
                    <div class="flex items-center justify-between"><span>{{ setting('delivery_fee_label', 'Delivery') }}</span><span class="font-semibold text-primary">{{ $s['delivery_fee'] > 0 ? money($s['delivery_fee']) : 'FREE' }}</span></div>
                    @if($s['coupon'])
                    <div class="flex items-center justify-between text-secondary">
                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">loyalty</span><span>Coupon ({{ $s['coupon']->code }})</span></span>
                        <span class="font-semibold">-{{ money($s['discount']) }}</span>
                    </div>
                    <div class="pt-1 pb-1">
                        <form method="POST" action="{{ route('cart.coupon.remove') }}" class="inline-flex items-center gap-2 bg-secondary-fixed/40 text-on-secondary-fixed-variant px-3 py-1 rounded-full font-label-sm text-label-sm">
                            @csrf @method('DELETE')
                            <span class="font-mono font-bold tracking-wider">{{ $s['coupon']->code }}</span>
                            <span>(−{{ money($s['discount']) }} applied)</span>
                            <button aria-label="Remove promo code" class="text-on-secondary-fixed-variant/70 hover:text-on-secondary-fixed transition-colors" type="submit"><span class="material-symbols-outlined text-[14px]">close</span></button>
                        </form>
                    </div>
                    @else
                    <form method="POST" action="{{ route('cart.coupon') }}" class="flex items-center gap-2 pt-2">
                        @csrf
                        <input name="code" value="{{ $s['coupon_code'] }}" placeholder="Promo code" class="flex-1 h-10 px-4 bg-surface-container-low rounded-full font-label-md text-label-md uppercase tracking-wider focus:outline-none focus:ring-1 focus:ring-secondary">
                        <button class="h-10 px-4 rounded-full bg-surface-container text-primary font-label-md text-label-md hover:bg-secondary hover:text-on-secondary transition-all" type="submit">Apply</button>
                    </form>
                    @if($s['coupon_error'])<p class="text-error font-body-sm text-body-sm">{{ $s['coupon_error'] }}</p>@endif
                    @endif
                </div>
                <div class="h-px bg-surface-container my-space-md"></div>
                <div class="flex items-baseline justify-between mb-space-md">
                    <div>
                        <span class="font-title-md text-title-md text-primary font-bold block">Grand Total</span>
                        <span class="font-body-sm text-[12px] text-on-surface-variant">{{ setting('total_note', 'Includes all taxes') }}</span>
                    </div>
                    <div class="text-right"><span class="font-headline-md text-headline-md text-primary font-bold">{{ money($s['total']) }}</span></div>
                </div>
                <div class="space-y-space-xs">
                    <button form="checkoutForm" name="payment_method" value="whatsapp" type="submit" class="group w-full py-space-sm px-space-md bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg flex items-center justify-center gap-2 shadow-[0_8px_20px_rgba(142,78,8,0.28)] hover:bg-primary hover:text-on-primary transition-all duration-300 transform hover:-translate-y-0.5">
                        <span class="material-symbols-outlined text-[20px]">chat</span>
                        <span class="tracking-wide">Confirm &amp; Order via WhatsApp</span>
                    </button>
                    <p class="font-body-sm text-[11px] text-center text-on-surface-variant">{{ setting('whatsapp_checkout_note', 'Instant receipt, UPI link, and live baker chat pinged immediately') }}</p>
                    @if(setting('online_payment_enabled', '1') === '1')
                    <button form="checkoutForm" name="payment_method" value="online" type="submit" class="w-full py-space-sm px-space-md bg-surface-container text-primary rounded-full font-label-lg text-label-lg flex items-center justify-center gap-2 hover:bg-surface-container-high transition-all duration-200">
                        <span class="material-symbols-outlined text-[18px] text-secondary">credit_card</span>
                        <span>{{ setting('online_payment_label', 'Pay Online via UPI / Card / Netbanking') }}</span>
                    </button>
                    @endif
                </div>
                @if($guar->is_active && $guar->activeItems()->count())
                <div class="mt-space-md pt-space-md bg-surface-container-low/60 rounded-xl p-space-sm space-y-2.5">
                    @foreach($guar->activeItems() as $g)
                    <div class="flex items-center gap-2.5">
                        <div class="w-6 h-6 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed flex-shrink-0"><span class="material-symbols-outlined text-[14px]">{{ $g->icon }}</span></div>
                        <p class="font-body-sm text-[12px] text-on-surface leading-tight"><strong>{{ $g->title }}:</strong> {{ $g->description }}</p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @if($chef->is_active)
            <div class="bg-surface-container-low rounded-2xl p-space-md shadow-sm flex items-center gap-space-sm">
                <div class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 bg-surface-container"><img class="w-full h-full object-cover" alt="{{ $chef->subtitle }}" src="{{ media($chef->image) }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-title-md text-[13px] text-primary font-semibold truncate">“{{ $chef->title }}”</p>
                    <p class="font-body-sm text-[11px] text-on-surface-variant">{{ $chef->subtitle }}</p>
                </div>
                @if(setting('phone'))<a aria-label="Call Bakehouse" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-primary hover:bg-secondary hover:text-on-secondary transition-colors" href="{{ tel_link(setting('phone')) }}"><span class="material-symbols-outlined text-[16px]">call</span></a>@endif
            </div>
            @endif
        </div>
    </div>
    @endif

    @if($reco->is_active && $recommended->count())
    <div class="mt-space-xl pt-space-lg">
        <div class="flex items-end justify-between mb-space-md">
            <div>
                <span class="font-label-sm text-label-sm uppercase tracking-widest text-secondary font-bold">{{ $reco->eyebrow }}</span>
                <h2 class="font-headline-md text-headline-md text-primary mt-1">{{ $reco->title }}</h2>
            </div>
            @if($reco->button_text)
            <a class="hidden sm:inline-flex items-center gap-1 font-label-md text-label-md text-secondary hover:text-primary transition-colors" href="{{ link_to_url($reco->button_link ?: '/menu') }}"><span>{{ $reco->button_text }}</span><span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
            @endif
        </div>
        <div class="m-rail m-rail-sm grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-space-md">
            @foreach($recommended as $r)
            <div class="bg-surface-container-lowest rounded-2xl p-space-sm transition-all duration-300 hover:shadow-[0_12px_28px_-4px_rgba(61,35,20,0.1)] group">
                <div class="relative w-full h-32 sm:h-44 rounded-xl overflow-hidden bg-surface-container mb-space-sm">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $r->name }}" src="{{ media($r->image) }}" loading="lazy">
                    @if($r->badge)<span class="absolute top-2 left-2 {{ $r->badgeClasses() }} font-label-sm text-[10px] px-2 py-0.5 rounded-full font-bold">{{ $r->badge }}</span>@endif
                </div>
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <h3 class="font-title-md text-[15px] text-primary font-semibold">{{ $r->name }}</h3>
                        <p class="font-body-sm text-[12px] text-on-surface-variant">{{ $r->detail_line }}</p>
                    </div>
                    <span class="font-title-md text-[15px] text-primary font-semibold">{{ money($r->price) }}</span>
                </div>
                <div class="mt-space-sm pt-2 flex items-center justify-between">
                    <span class="font-label-sm text-[11px] text-secondary font-medium">{{ $r->reco_note }}</span>
                    <form method="POST" action="{{ route('cart.add', $r) }}">
                        @csrf
                        <button class="px-3 py-1 bg-surface-container text-primary rounded-full font-label-sm text-label-sm hover:bg-secondary hover:text-on-secondary transition-all flex items-center gap-1" type="submit"><span class="material-symbols-outlined text-[14px]">add</span><span>{{ $reco->x('add_button_text', 'Add to Tray') }}</span></button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
</div>
@endsection

@push('scripts')
<script>
    function toggleCustomDate() {
        const sel = document.querySelector('input[name="slot_id"]:checked');
        const wrap = document.getElementById('customDateWrap');
        if (!wrap) return;
        const on = sel && sel.dataset.custom === '1';
        wrap.classList.toggle('hidden', !on);
        document.getElementById('custom-date').required = on;
    }
    toggleCustomDate();
</script>
@endpush
