@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
@php
    $u = auth()->user();
    $dietary = section('profile.dietary');
    $sweet = section('profile.sweetness');
    $packaging = section('profile.packaging');
    $notif = section('profile.notifications');
    $rewards = section('profile.rewards');
    $help = section('profile.help');
    $personal = section('profile.personal');
    $selectedDiet = collect($u->pref('dietary', []))->map(fn ($v) => (int) $v)->all();
    $selectedNotif = collect($u->pref('notifications', $notif->activeItems()->filter(fn ($i) => $i->value === 'on')->pluck('id')->all()))->map(fn ($v) => (int) $v)->all();
    $sweetLevels = $sweet->activeItems();
    $sweetIdx = (int) $u->pref('sweetness', min(2, max(1, $sweetLevels->count())));
    $goal = (int) setting('rewards_goal_points', 500);
    $tier = $u->tier ?: setting('rewards_default_tier', 'Crinkle Connoisseur Tier');
    $inputCls = 'w-full px-4 py-3 rounded-xl bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface focus:outline-none focus:ring-2 focus:ring-secondary/50 transition-all placeholder:text-outline';
    $firstDiet = $dietary->activeItems()->firstWhere('id', $selectedDiet[0] ?? null);
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full max-w-[1280px] mx-auto px-margin-mobile md:px-margin pb-space-xl">
    <section class="relative mt-6 mb-8 rounded-2xl bg-gradient-to-r from-surface-container-high via-surface-container to-surface-container-low p-6 md:p-8 shadow-[0_4px_24px_-4px_rgba(61,35,20,0.06)] overflow-hidden">
        <div class="absolute -right-16 -top-16 w-72 h-72 rounded-full bg-secondary-fixed/30 blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-80 h-80 rounded-full bg-primary-fixed/20 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                <div class="relative group">
                    <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl overflow-hidden shadow-[0_8px_20px_rgba(61,35,20,0.12)] bg-surface-container-highest flex items-center justify-center">
                        @if($u->avatar)
                            <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" alt="{{ $u->name }}" src="{{ media($u->avatar) }}"/>
                        @else
                            <span class="font-display text-display-mobile text-primary">{{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                        @endif
                    </div>
                    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
                        @csrf
                        <label aria-label="Edit Profile Picture" class="cursor-pointer absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-secondary text-on-secondary flex items-center justify-center shadow-md hover:bg-primary transition-colors">
                            <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                            <input type="file" name="avatar" accept="image/*" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
                <div class="flex flex-col">
                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                        <h1 class="font-headline-md text-headline-md text-primary tracking-tight">{{ $u->fullName() }}</h1>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm shadow-sm"><span class="material-symbols-outlined text-[14px] text-secondary">verified</span>{{ $tier }}</span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant flex items-center gap-2"><span class="material-symbols-outlined text-[18px] text-secondary">cake</span>{{ $personal->x('member_since_text', 'Member since') }} {{ $u->created_at->format('F Y') }}</p>
                    <div class="flex flex-wrap items-center gap-2 mt-3">
                        @if($firstDiet)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface font-label-sm text-label-sm"><span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>{{ $firstDiet->label ?: $firstDiet->title }}</span>
                        @endif
                        @if($u->location_label)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-surface-container-highest text-on-surface font-label-sm text-label-sm">{{ $u->location_label }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 sm:gap-4 bg-surface/90 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-[0_2px_12px_rgba(61,35,20,0.04)]">
                <div class="flex flex-col text-center px-2 sm:px-4"><span class="font-display-mobile text-display-mobile text-primary font-bold leading-none">{{ $stats['orders'] }}</span><span class="font-label-md text-label-md text-on-surface-variant mt-1">{{ $personal->x('stat_orders', 'Orders') }}</span></div>
                <div class="h-10 w-[1px] bg-outline-variant/40"></div>
                <div class="flex flex-col text-center px-2 sm:px-4"><span class="font-display-mobile text-display-mobile text-secondary font-bold leading-none">{{ number_format($u->points) }}</span><span class="font-label-md text-label-md text-on-surface-variant mt-1">{{ $personal->x('stat_points', 'Sweet Points') }}</span></div>
                <div class="h-10 w-[1px] bg-outline-variant/40"></div>
                <div class="flex flex-col text-center px-2 sm:px-4"><span class="font-display-mobile text-display-mobile text-primary font-bold leading-none">{{ $stats['saved'] }}</span><span class="font-label-md text-label-md text-on-surface-variant mt-1">{{ $personal->x('stat_saved', 'Bakes Tried') }}</span></div>
            </div>
        </div>
    </section>

    <nav class="flex items-center gap-2 overflow-x-auto pb-3 mb-8 no-scrollbar" id="profileTabs">
        @foreach(['tab-personal' => 'Personal Details', 'tab-dietary' => 'Dietary & Baking Preferences', 'tab-addresses' => 'Saved Addresses ('.$u->addresses->count().')', 'tab-rewards' => 'Rewards', 'tab-whatsapp' => 'WhatsApp Notifications'] as $id => $label)
        <button class="tab-btn shrink-0 px-5 py-2.5 rounded-full font-label-lg text-label-lg transition-all {{ $loop->first ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:text-primary hover:bg-surface-container' }}" data-target="{{ $id }}">{{ $label }}</button>
        @endforeach
        <form method="POST" action="{{ route('logout') }}" class="shrink-0 ml-auto">@csrf<button class="px-5 py-2.5 rounded-full font-label-lg text-label-lg bg-surface-container-low text-on-surface-variant hover:text-error transition-all inline-flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">logout</span>Sign out</button></form>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-8 flex flex-col gap-8">
            <section class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]" id="tab-personal">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-surface-container">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary-fixed/50 flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-[20px]">badge</span></div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-primary">{{ $personal->title ?: 'Personal Details' }}</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $personal->subtitle }}</p>
                        </div>
                    </div>
                    @if($personal->eyebrow)<span class="font-label-sm text-label-sm text-secondary bg-secondary-fixed/40 px-3 py-1 rounded-full uppercase tracking-wider">{{ $personal->eyebrow }}</span>@endif
                </div>
                <form class="flex flex-col gap-5" method="POST" action="{{ route('profile.update') }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="firstName">First Name</label>
                            <div class="relative"><input class="{{ $inputCls }}" id="firstName" name="name" required type="text" value="{{ old('name', $u->name) }}"/><span class="material-symbols-outlined absolute right-3.5 top-3.5 text-outline text-[18px]">person</span></div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="lastName">Last Name</label>
                            <div class="relative"><input class="{{ $inputCls }}" id="lastName" name="last_name" type="text" value="{{ old('last_name', $u->last_name) }}"/></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="email">Email Address</label>
                            <div class="relative"><input class="{{ $inputCls }}" id="email" name="email" required type="email" value="{{ old('email', $u->email) }}"/><span class="material-symbols-outlined absolute right-3.5 top-3.5 text-outline text-[18px]">mail</span></div>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="phone">Phone / WhatsApp</label>
                            <div class="relative"><input class="{{ $inputCls }}" id="phone" name="phone" type="tel" value="{{ old('phone', $u->phone) }}"/><span class="material-symbols-outlined absolute right-3.5 top-3.5 text-outline text-[18px]">call</span></div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider" for="location">Neighbourhood / Area</label>
                        <input class="{{ $inputCls }}" id="location" name="location_label" type="text" value="{{ old('location_label', $u->location_label) }}" placeholder="e.g. Bangalore Heritage Quarter"/>
                    </div>
                    @if($personal->x('birthday_title'))
                    <div class="p-4 rounded-xl bg-surface-container-low/70 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-secondary-container/40 flex items-center justify-center text-on-secondary-container"><span class="material-symbols-outlined text-[20px]">celebration</span></div>
                            <div>
                                <span class="font-title-md text-title-md text-primary block">{{ $personal->x('birthday_title') }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $personal->x('birthday_text') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 w-full sm:w-auto"><input class="w-full sm:w-40 px-3.5 py-2 rounded-lg bg-surface text-on-surface font-label-lg text-label-lg focus:outline-none focus:ring-2 focus:ring-secondary text-center" name="birthday" type="text" placeholder="e.g. 14th August" value="{{ old('birthday', $u->birthday) }}"/></div>
                    </div>
                    @endif
                    <details class="rounded-xl bg-surface-container-low/70 p-4">
                        <summary class="cursor-pointer font-label-lg text-label-lg text-primary">Change password</summary>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                            <input class="{{ $inputCls }}" name="password" type="password" placeholder="New password" autocomplete="new-password">
                            <input class="{{ $inputCls }}" name="password_confirmation" type="password" placeholder="Confirm new password" autocomplete="new-password">
                        </div>
                    </details>
                    <div class="flex items-center justify-end pt-2">
                        <button class="px-7 py-3 rounded-full bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2" type="submit"><span class="material-symbols-outlined text-[18px]">check_circle</span>Save Changes</button>
                    </div>
                </form>
            </section>

            @if($dietary->is_active)
            <section class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]" id="tab-dietary">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-surface-container">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary-fixed/50 flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-[20px]">restaurant_menu</span></div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-primary">{{ $dietary->title }}</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $dietary->subtitle }}</p>
                        </div>
                    </div>
                    @if($dietary->eyebrow)<span class="font-label-sm text-label-sm text-secondary bg-surface-container px-3 py-1 rounded-full">{{ $dietary->eyebrow }}</span>@endif
                </div>
                <form method="POST" action="{{ route('profile.preferences') }}" class="flex flex-col gap-6">
                    @csrf @method('PUT')
                    <div>
                        <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider block mb-3">{{ $dietary->x('list_title', 'Kitchen Dietary Profiles') }}</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($dietary->activeItems() as $opt)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="dietary[]" value="{{ $opt->id }}" class="peer sr-only" @checked(in_array($opt->id, $selectedDiet, true))>
                                <div class="flex items-start gap-3 p-4 rounded-xl text-left transition-all bg-surface-container-low border-2 border-transparent peer-checked:bg-surface-container peer-checked:border-secondary [&_.check-icon]:bg-surface-container-highest [&_.check-icon]:text-transparent peer-checked:[&_.check-icon]:bg-secondary peer-checked:[&_.check-icon]:text-on-secondary">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5 check-icon"><span class="material-symbols-outlined text-[16px]">check</span></div>
                                    <div class="flex flex-col"><span class="font-title-md text-title-md text-primary">{{ $opt->title }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $opt->description }}</span></div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @if($sweetLevels->count())
                    <div class="p-5 rounded-xl bg-surface-container-low">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-label-md text-label-md text-on-surface-variant uppercase tracking-wider">{{ $sweet->title ?: 'Favorite Sweetness' }}</span>
                            <span class="font-label-sm text-label-sm text-secondary bg-surface px-2.5 py-0.5 rounded-full font-semibold" id="sweetnessDisplay">{{ optional($sweetLevels->get($sweetIdx - 1))->title }}</span>
                        </div>
                        <input class="w-full accent-secondary cursor-pointer h-2 bg-surface-container-highest rounded-lg appearance-none" id="sweetnessSlider" name="sweetness" max="{{ $sweetLevels->count() }}" min="1" oninput="updateSweetness(this.value)" type="range" value="{{ $sweetIdx }}"/>
                        <div class="flex justify-between items-center text-xs text-on-surface-variant mt-2 font-body-sm">
                            @foreach($sweetLevels as $lvl)<span>{{ $lvl->title }}</span>@endforeach
                        </div>
                    </div>
                    @endif
                    @if($packaging->is_active && $packaging->activeItems()->count())
                    <div class="p-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-primary"><span class="material-symbols-outlined text-[20px]">inventory_2</span></div>
                            <div>
                                <span class="font-title-md text-title-md text-primary block">{{ $packaging->title }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">{{ $packaging->subtitle }}</span>
                            </div>
                        </div>
                        <select name="packaging" class="shrink-0 px-4 py-2 rounded-full bg-surface font-label-md text-label-md text-secondary focus:outline-none focus:ring-2 focus:ring-secondary/40 max-w-full">
                            @foreach($packaging->activeItems() as $pk)
                            <option value="{{ $pk->id }}" @selected((int) $u->pref('packaging') === $pk->id)>{{ $pk->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex justify-end">
                        <button class="px-7 py-3 rounded-full bg-primary text-on-primary font-label-lg text-label-lg shadow-md hover:bg-primary-container transition-all flex items-center gap-2" type="submit"><span class="material-symbols-outlined text-[18px]">check_circle</span>Save Preferences</button>
                    </div>
                </form>
            </section>
            @endif

            <section class="bg-surface-container-lowest p-6 sm:p-8 rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]" id="tab-addresses">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-surface-container gap-3 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-secondary-fixed/50 flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-[20px]">local_shipping</span></div>
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-primary">{{ $personal->x('addresses_title', 'Saved Deliveries') }}</h2>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $personal->x('addresses_subtitle', 'Where our fresh bakes should be dispatched.') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="document.getElementById('addr-new').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm hover:bg-secondary hover:text-on-secondary transition-all"><span class="material-symbols-outlined text-[16px]">add_location_alt</span>Add New Address</button>
                </div>
                <div id="addr-new" class="hidden mb-6">
                    @include('partials.address-form', ['address' => null])
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($u->addresses as $a)
                    <div class="relative p-5 rounded-xl bg-surface-container-low {{ $a->is_default ? 'border-2 border-secondary/60' : '' }} flex flex-col justify-between group hover:shadow-md transition-all">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2"><span class="material-symbols-outlined {{ $a->is_default ? 'text-secondary' : 'text-outline' }} text-[20px]">{{ $a->icon }}</span><span class="font-title-md text-title-md text-primary">{{ $a->label }}{{ $a->is_default ? ' (Default)' : '' }}</span></div>
                                @if($a->is_default)<span class="px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-label-sm text-[10px] uppercase font-bold tracking-wider">Primary</span>@endif
                            </div>
                            <p class="font-body-md text-body-md text-on-surface-variant mb-4 leading-relaxed">{{ $a->line1 }}@if($a->line2)<br/>{{ $a->line2 }}@endif</p>
                            @if($a->recipient || $a->note)
                            <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-[14px]">{{ $a->recipient ? 'phone_iphone' : 'notifications_active' }}</span>
                                <span>{{ $a->recipient ? 'Recipient: '.$a->recipient.($a->phone ? ' ('.$a->phone.')' : '') : $a->note }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 pt-4 mt-4 border-t border-surface-container flex-wrap">
                            <button type="button" onclick="document.getElementById('addr-{{ $a->id }}').classList.toggle('hidden')" class="font-label-md text-label-md text-secondary hover:text-primary transition-colors flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">edit</span>Edit</button>
                            <span class="text-outline-variant">•</span>
                            @if($a->is_default)
                            <form method="POST" action="{{ route('addresses.destroy', $a) }}" onsubmit="return confirm('Remove this address?')">@csrf @method('DELETE')<button class="font-label-md text-label-md text-error hover:opacity-80 transition-opacity flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">delete</span>Remove</button></form>
                            @else
                            <form method="POST" action="{{ route('addresses.default', $a) }}">@csrf<button class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">Set as Default</button></form>
                            <span class="text-outline-variant">•</span>
                            <form method="POST" action="{{ route('addresses.destroy', $a) }}" onsubmit="return confirm('Remove this address?')">@csrf @method('DELETE')<button class="font-label-md text-label-md text-error hover:opacity-80">Remove</button></form>
                            @endif
                        </div>
                        <div id="addr-{{ $a->id }}" class="hidden mt-4">@include('partials.address-form', ['address' => $a])</div>
                    </div>
                    @empty
                    <p class="font-body-md text-body-md text-on-surface-variant">No saved addresses yet. Add one for faster checkout.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="lg:col-span-4 flex flex-col gap-8">
            <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)] relative overflow-hidden" id="tab-rewards">
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-secondary-fixed/40 blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 rounded-full bg-secondary-container/40 flex items-center justify-center text-on-secondary-container"><span class="material-symbols-outlined text-[20px]">stars</span></div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">{{ $rewards->title ?: 'Sweet Rewards' }}</h2>
                        <span class="font-label-sm text-label-sm text-secondary">{{ $tier }}</span>
                    </div>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low mb-6">
                    <div class="flex justify-between items-center mb-2 gap-2">
                        <span class="font-title-md text-title-md text-primary">{{ setting('rewards_goal_title', 'Free Brownie Box') }}</span>
                        <span class="font-label-md text-label-md text-secondary font-bold whitespace-nowrap">{{ number_format(min($u->points, $goal)) }} / {{ number_format($goal) }} Pts</span>
                    </div>
                    <div class="w-full h-3 rounded-full bg-surface-container-highest overflow-hidden p-0.5"><div class="h-full rounded-full bg-secondary transition-all duration-700" style="width: {{ $goal > 0 ? min(100, round($u->points / $goal * 100)) : 0 }}%;"></div></div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-2.5 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-secondary">eco</span>
                        @if($u->points >= $goal) {{ $rewards->x('unlocked_text', 'Reward unlocked! Mention it on WhatsApp with your next order.') }}
                        @else {{ str_replace('{points}', number_format($goal - $u->points), $rewards->x('progress_text', 'Just {points} points away from unlocking your gift box!')) }} @endif
                    </p>
                </div>
                @if($vouchers->count())
                <h3 class="font-title-md text-title-md text-primary mb-3">{{ $rewards->x('vouchers_title', 'Available Vouchers') }}</h3>
                <div class="flex flex-col gap-3">
                    @foreach($vouchers as $v)
                    <div class="p-3.5 rounded-xl bg-surface-container-low border-dashed border-2 border-secondary/40 flex items-center justify-between gap-3 group hover:bg-surface-container transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-surface flex items-center justify-center text-secondary"><span class="material-symbols-outlined text-[18px]">{{ $v->icon }}</span></div>
                            <div class="flex flex-col"><span class="font-label-lg text-label-lg text-primary tracking-wide">{{ $v->code }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $v->description }}</span></div>
                        </div>
                        <form method="POST" action="{{ route('cart.coupon') }}">@csrf<input type="hidden" name="code" value="{{ $v->code }}"><input type="hidden" name="redirect" value="cart"><button class="px-3 py-1 rounded-full bg-surface-container-highest hover:bg-secondary hover:text-on-secondary text-primary font-label-sm text-label-sm transition-colors">Apply</button></form>
                    </div>
                    @endforeach
                </div>
                @endif
            </section>

            @if($notif->is_active)
            <section class="bg-surface-container-lowest p-6 rounded-2xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]" id="tab-whatsapp">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-[#25D366]/15 flex items-center justify-center text-[#128C7E]"><span class="material-symbols-outlined text-[20px]">chat</span></div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">{{ $notif->title }}</h2>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">{{ $notif->eyebrow }}</span>
                    </div>
                </div>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-4">{{ $notif->subtitle }}</p>
                <form method="POST" action="{{ route('profile.notifications') }}" class="flex flex-col gap-3">
                    @csrf @method('PUT')
                    <input type="hidden" name="notifications[]" value="0">
                    @foreach($notif->activeItems() as $n)
                    <label class="flex items-start gap-3 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container transition-colors cursor-pointer">
                        <input @checked(in_array($n->id, $selectedNotif, true)) name="notifications[]" value="{{ $n->id }}" class="mt-1 w-4 h-4 accent-secondary rounded" type="checkbox" onchange="this.form.submit()"/>
                        <div class="flex flex-col"><span class="font-label-lg text-label-lg text-primary">{{ $n->title }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $n->description }}</span></div>
                    </label>
                    @endforeach
                </form>
            </section>
            @endif

            @if($help->is_active)
            <div class="p-5 rounded-2xl bg-surface-container text-on-surface flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[24px] text-secondary">{{ $help->eyebrow_icon ?: 'support_agent' }}</span>
                    <div class="flex flex-col"><span class="font-label-lg text-label-lg text-primary">{{ $help->title }}</span><span class="font-body-sm text-body-sm text-on-surface-variant">{{ $help->subtitle }}</span></div>
                </div>
                <a class="shrink-0 w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center hover:bg-secondary transition-colors" href="{{ link_to_url($help->button_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank"><span class="material-symbols-outlined text-[16px]">arrow_forward</span></a>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm'); b.classList.add('bg-surface-container-low', 'text-on-surface-variant'); });
            btn.classList.add('bg-primary', 'text-on-primary'); btn.classList.remove('bg-surface-container-low', 'text-on-surface-variant');
            const t = document.getElementById(btn.dataset.target);
            if (t) t.scrollIntoView({behavior: 'smooth', block: 'center'});
        });
    });
    const sweetLabels = @json($sweetLevels->pluck('title'));
    function updateSweetness(v) { document.getElementById('sweetnessDisplay').textContent = sweetLabels[v - 1] || ''; }
</script>
@endpush
