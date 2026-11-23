<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ setting('site_name', 'Crinkle Nest') }} Admin</title>
    @if(setting('favicon'))<link rel="icon" href="{{ media(setting('favicon')) }}">@endif
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:wght@300..800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    @include('partials.tailwind-config')
    <style>
        .material-symbols-outlined{font-size:20px;vertical-align:middle}
        .adm-input{width:100%;padding:.65rem .9rem;border-radius:.75rem;background:#fff;border:1px solid #e7e2da;font-size:14px;color:#1d1c17}
        .adm-input:focus{outline:none;border-color:#8e4e08;box-shadow:0 0 0 3px rgba(142,78,8,.12)}
        .adm-label{display:block;font-size:12px;font-weight:700;letter-spacing:.03em;color:#50443f;margin-bottom:.35rem;text-transform:uppercase}
        .adm-help{font-size:12px;color:#82746e;margin-top:.3rem}
        .adm-card{background:#fff;border-radius:1.25rem;box-shadow:0 4px 20px -2px rgba(61,35,20,.06);padding:1.5rem}
        .adm-btn{display:inline-flex;align-items:center;gap:.4rem;padding:.6rem 1.1rem;border-radius:999px;font-weight:600;font-size:14px;transition:all .2s}
        .adm-btn-primary{background:#250f03;color:#fff}.adm-btn-primary:hover{background:#8e4e08}
        .adm-btn-soft{background:#f2ede5;color:#250f03}.adm-btn-soft:hover{background:#e7e2da}
        .adm-btn-danger{background:#ffdad6;color:#93000a}.adm-btn-danger:hover{background:#ba1a1a;color:#fff}
        table.adm-table{width:100%;font-size:14px}
        table.adm-table th{font-size:11px;text-transform:uppercase;letter-spacing:.06em;color:#8e4e08;text-align:left;padding:.75rem;font-weight:700;background:#f8f3eb}
        table.adm-table td{padding:.75rem;border-top:1px solid #f2ede5;vertical-align:middle}
        table.adm-table tr:hover td{background:#fefcf8}
    </style>
    @stack('head')
</head>
<body class="bg-surface-container-low font-body-md text-on-surface antialiased">
@php
    $groups = config('admin.groups');
    $resources = config('admin.resources');
    $unread = \App\Models\ContactMessage::where('is_read', false)->count();
    $navItem = fn ($active) => 'flex items-center gap-3 px-3 py-2 rounded-xl text-[14px] transition-all '.($active ? 'bg-primary text-on-primary font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container hover:text-primary');
@endphp
<div class="flex min-h-screen">
    <aside id="admSidebar" class="fixed lg:sticky top-0 left-0 z-40 h-screen w-64 shrink-0 bg-surface overflow-y-auto border-r border-surface-container -translate-x-full lg:translate-x-0 transition-transform">
        <div class="px-5 py-5 flex items-center gap-2">
            <div class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center"><span class="material-symbols-outlined">cookie</span></div>
            <div class="leading-tight">
                <div class="font-headline-sm text-[18px] text-primary">{{ setting('site_name', 'Crinkle Nest') }}</div>
                <div class="text-[11px] uppercase tracking-widest text-secondary font-bold">Admin Panel</div>
            </div>
        </div>
        <nav class="px-3 pb-8 flex flex-col gap-1">
            <a href="{{ route('admin.dashboard') }}" class="{{ $navItem(request()->routeIs('admin.dashboard')) }}"><span class="material-symbols-outlined">space_dashboard</span>Dashboard</a>
            <a href="{{ route('admin.orders.index') }}" class="{{ $navItem(request()->routeIs('admin.orders.*')) }}"><span class="material-symbols-outlined">receipt_long</span>Orders</a>
            <a href="{{ route('admin.messages.index') }}" class="{{ $navItem(request()->routeIs('admin.messages.*')) }}"><span class="material-symbols-outlined">mail</span>Messages @if($unread)<span class="ml-auto text-[11px] px-2 py-0.5 rounded-full bg-secondary text-on-secondary">{{ $unread }}</span>@endif</a>
            @foreach($groups as $gname => $keys)
                <div class="mt-4 mb-1 px-3 text-[11px] uppercase tracking-widest text-outline font-bold">{{ $gname }}</div>
                @foreach($keys as $key)
                    @php $r = $resources[$key]; $active = request()->route('resource') === $key; @endphp
                    <a href="{{ route('admin.resource.index', $key) }}" class="{{ $navItem($active) }}"><span class="material-symbols-outlined">{{ $r['icon'] }}</span>{{ $r['title'] }}</a>
                @endforeach
            @endforeach
            <div class="mt-4 mb-1 px-3 text-[11px] uppercase tracking-widest text-outline font-bold">Configuration</div>
            @foreach(\App\Models\Setting::GROUPS as $gk => $gl)
                <a href="{{ route('admin.settings', ['group' => $gk]) }}" class="{{ $navItem(request()->routeIs('admin.settings') && request('group', 'general') === $gk) }}"><span class="material-symbols-outlined">tune</span>{{ $gl }}</a>
            @endforeach
        </nav>
    </aside>
    <div class="flex-1 min-w-0 flex flex-col">
        <header class="sticky top-0 z-30 bg-surface/90 backdrop-blur border-b border-surface-container">
            <div class="h-16 px-4 lg:px-8 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <button type="button" class="lg:hidden adm-btn adm-btn-soft !p-2" onclick="document.getElementById('admSidebar').classList.toggle('-translate-x-full')"><span class="material-symbols-outlined">menu</span></button>
                    <h1 class="font-headline-sm text-[20px] text-primary truncate">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" target="_blank" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">open_in_new</span><span class="hidden sm:inline">View site</span></a>
                    <a href="{{ route('admin.account') }}" class="adm-btn adm-btn-soft"><span class="material-symbols-outlined">account_circle</span><span class="hidden sm:inline">{{ auth()->user()->name }}</span></a>
                    <form method="POST" action="{{ route('admin.logout') }}">@csrf<button class="adm-btn adm-btn-soft" title="Sign out"><span class="material-symbols-outlined">logout</span></button></form>
                </div>
            </div>
        </header>
        <main class="p-4 lg:p-8 flex-1">
            @if(session('success'))<div class="mb-5 p-3.5 rounded-xl bg-emerald-50 text-emerald-900 flex items-center gap-2"><span class="material-symbols-outlined">check_circle</span>{{ session('success') }}</div>@endif
            @if(session('error'))<div class="mb-5 p-3.5 rounded-xl bg-error-container text-on-error-container flex items-center gap-2"><span class="material-symbols-outlined">error</span>{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-error-container text-on-error-container">
                    <div class="font-semibold mb-1">Please fix the following:</div>
                    <ul class="list-disc pl-5 text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>
