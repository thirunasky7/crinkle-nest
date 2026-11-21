@php
    $tabs = \App\Models\Menu::at('mobile_tabbar')->take(5);
    $cartCount = \App\Support\Cart::count();
@endphp
@if($tabs->count())
<nav class="lg:hidden fixed bottom-0 inset-x-0 z-50 bg-surface/95 backdrop-blur-xl shadow-[0_-4px_20px_-2px_rgba(61,35,20,0.08)] pb-safe" aria-label="App navigation">
    <div class="grid h-16" style="grid-template-columns: repeat({{ $tabs->count() }}, minmax(0, 1fr));">
        @foreach($tabs as $tab)
            @php
                $active = $tab->isCurrent();
                $isCart = str_contains((string) $tab->url, 'cart');
            @endphp
            <a href="{{ $tab->href() }}" @if($tab->new_tab) target="_blank" rel="noopener noreferrer" @endif
               class="relative flex flex-col items-center justify-center gap-0.5 {{ $active ? 'text-primary' : 'text-on-surface-variant' }}">
                <span class="relative flex items-center justify-center w-12 h-7 rounded-full transition-colors {{ $active ? 'bg-secondary-fixed' : '' }}">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings: 'FILL' {{ $active ? 1 : 0 }};">{{ $tab->icon ?: 'circle' }}</span>
                    @if($isCart && $cartCount)
                        <span class="absolute -top-1 right-1.5 min-w-[16px] h-[16px] px-1 rounded-full bg-secondary text-on-secondary text-[9px] font-bold flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </span>
                <span class="text-[10.5px] leading-none {{ $active ? 'font-bold' : 'font-medium' }}">{{ $tab->label }}</span>
            </a>
        @endforeach
    </div>
</nav>
@endif
