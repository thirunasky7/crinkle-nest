{{-- $product, $variant: trending|category, $priceLabel (optional), $buttonText --}}
@php
    $isTrending = ($variant ?? 'category') === 'trending';
@endphp
@if($isTrending)
<div class="snap-start shrink-0 w-[280px] sm:w-[320px] rounded-2xl bg-surface-container-lowest p-4 shadow-[0_4px_20px_-2px_rgba(61,35,20,0.06)] flex flex-col justify-between hover:shadow-[0_12px_32px_-4px_rgba(61,35,20,0.12)] transition-all">
@else
<div class="snap-start shrink-0 w-[270px] sm:w-[300px] rounded-2xl bg-surface-container-low p-4 shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] flex flex-col justify-between hover:bg-surface-container-lowest hover:shadow-[0_10px_28px_-4px_rgba(61,35,20,0.1)] transition-all {{ $extraClass ?? '' }}">
@endif
    <div>
        <div class="relative rounded-xl overflow-hidden aspect-[4/3] mb-4 bg-surface-container">
            <img class="w-full h-full object-cover" alt="{{ $product->image_alt ?: $product->name }}" src="{{ media($product->image) }}" loading="lazy"/>
            @if($product->badge)
                <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full {{ $product->badgeClasses() }} font-label-sm text-label-sm shadow-sm">{{ $product->badge }}</span>
            @endif
        </div>
        <h3 class="font-headline-sm text-headline-sm text-primary">{{ $product->name }}</h3>
        @if($product->short_description)
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">{{ $product->short_description }}</p>
        @endif
    </div>
    <div class="pt-4 mt-4 flex items-center justify-between gap-2">
        <div>
            <span class="font-label-sm text-label-sm text-on-surface-variant block">{{ $product->price_label ?: ($priceLabel ?? 'Price') }}</span>
            <span class="font-title-md text-title-md text-primary font-bold">{{ money($product->price) }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            @if(setting('show_add_to_basket', '1') === '1')
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button type="submit" aria-label="Add {{ $product->name }} to basket" title="Add to basket" class="w-8 h-8 rounded-full bg-surface-container-high text-primary hover:bg-secondary hover:text-on-secondary transition-all flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-[17px]">add_shopping_cart</span></button>
            </form>
            @endif
            @if($isTrending)
                <a class="px-4 py-2 rounded-full bg-whatsapp text-white font-label-md text-label-md shadow-sm hover:bg-whatsapp-dark transition-all inline-flex items-center gap-1.5" href="{{ $product->whatsappLink() }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[16px]">chat</span> {{ $buttonText ?? 'Order' }}
                </a>
            @else
                <a class="px-3.5 py-1.5 rounded-full bg-whatsapp text-white font-label-md text-label-md shadow-sm hover:bg-whatsapp-dark transition-all inline-flex items-center gap-1" href="{{ $product->whatsappLink() }}" rel="noopener noreferrer" target="_blank">
                    <span class="material-symbols-outlined text-[15px]">chat</span> {{ $buttonText ?? 'WhatsApp' }}
                </a>
            @endif
        </div>
    </div>
</div>
