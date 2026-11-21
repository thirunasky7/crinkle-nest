@php
    $navItems = \App\Models\Menu::at('header');
    $cartCount = \App\Support\Cart::count();
    $activeCls = 'px-space-md py-space-xs rounded-full transition-all bg-surface-container-high text-primary font-bold shadow-sm';
    $idleCls = 'px-space-md py-space-xs rounded-full font-label-lg text-label-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-all';
@endphp
<header class="fixed top-0 inset-x-0 z-50 bg-surface/85 backdrop-blur-xl shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)]">
    <div class="h-20 max-w-[1280px] mx-auto px-margin-mobile md:px-margin flex items-center justify-between gap-space-md">
        <div class="flex items-center gap-space-sm">
            @if(setting('logo'))
                <a href="{{ route('home') }}"><img alt="{{ setting('site_name', 'Crinkle Nest') }} Logo" class="h-8 w-auto object-contain" src="{{ media(setting('logo')) }}"/></a>
            @endif
            <a class="flex flex-col" href="{{ route('home') }}">
                <span class="font-headline-sm text-headline-sm text-primary leading-none tracking-tight whitespace-nowrap">{{ setting('site_name', 'Crinkle Nest') }}</span>
                @if(setting('site_tagline'))<span class="font-label-sm text-label-sm text-secondary tracking-widest uppercase mt-0.5 whitespace-nowrap">{{ setting('site_tagline') }}</span>@endif
            </a>
        </div>
        <nav class="hidden lg:flex items-center gap-space-xs bg-surface-container-low/70 p-1.5 rounded-full shadow-[0_2px_8px_rgba(61,35,20,0.03)]">
            @foreach($navItems as $item)
                <a @if($item->isCurrent()) aria-current="page" @endif class="{{ $item->isCurrent() ? $activeCls : $idleCls }}" href="{{ $item->href() }}" @if($item->new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $item->label }}</a>
            @endforeach
        </nav>
        <div class="flex items-center gap-space-sm">
            @if(setting('header_cta_text'))
                <a class="hidden sm:inline-flex items-center gap-space-xs px-space-md py-space-xs bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg shadow-[0_4px_16px_rgba(142,78,8,0.25)] hover:bg-primary hover:text-on-primary transition-all duration-300 transform hover:-translate-y-0.5" href="{{ wa_link() }}" rel="noopener noreferrer" target="_blank"><span class="material-symbols-outlined text-[18px]">chat</span><span>{{ setting('header_cta_text') }}</span></a>
            @endif
            <a aria-label="Basket" class="hidden lg:flex relative w-9 h-9 rounded-full bg-surface-container-high text-primary flex items-center justify-center hover:bg-secondary hover:text-on-secondary transition-all" href="{{ route('cart.index') }}">
                <span class="material-symbols-outlined text-[20px]">shopping_basket</span>
                @if($cartCount)
                    <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-secondary text-on-secondary text-[10px] font-bold flex items-center justify-center">{{ $cartCount }}</span>
                @endif
            </a>
            <a aria-label="User Profile &amp; Preferences" class="hidden lg:flex group relative items-center justify-center p-0.5 rounded-full hover:ring-2 hover:ring-secondary/40 transition-all" href="{{ auth()->check() ? route('profile.index') : route('login') }}">
                @if(auth()->check() && auth()->user()->avatar)
                    <img class="w-9 h-9 rounded-full object-cover" src="{{ media(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                @else
                    <div class="w-9 h-9 rounded-full bg-primary flex items-center justify-center shadow-sm group-hover:bg-primary-container transition-colors"><span class="material-symbols-outlined text-on-primary text-[20px]">person</span></div>
                @endif
            </a>
            <button aria-label="Toggle Mobile Menu" id="mobileMenuToggle" class="lg:hidden p-2 text-on-surface-variant hover:text-on-surface hover:bg-surface-container rounded-full transition-colors" type="button"><span class="material-symbols-outlined text-[24px]">menu</span></button>
        </div>
    </div>
    <div id="mobileMenu" class="hidden lg:hidden px-margin-mobile pb-4">
        <nav class="flex flex-col gap-1 bg-surface-container-low rounded-2xl p-2 shadow-sm">
            @foreach($navItems as $item)
                <a class="{{ $item->isCurrent() ? $activeCls : $idleCls }} py-2.5" href="{{ $item->href() }}">{{ $item->label }}</a>
            @endforeach
            @if(setting('header_cta_text'))
                <a class="mt-1 inline-flex items-center justify-center gap-space-xs px-space-md py-2.5 bg-secondary text-on-secondary rounded-full font-label-lg text-label-lg" href="{{ wa_link() }}" target="_blank" rel="noopener noreferrer"><span class="material-symbols-outlined text-[18px]">chat</span>{{ setting('header_cta_text') }}</a>
            @endif
        </nav>
    </div>
</header>
