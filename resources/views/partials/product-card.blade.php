{{-- $product, $variant: trending|category, $priceLabel (optional), $buttonText, $extraClass --}}
@php
    $isTrending = ($variant ?? 'category') === 'trending';
    $outer = $isTrending
        ? 'w-[200px] sm:w-[280px] md:w-[320px] bg-surface-container-lowest shadow-[0_4px_20px_-2px_rgba(61,35,20,0.06)] hover:shadow-[0_12px_32px_-4px_rgba(61,35,20,0.12)]'
        : 'w-[165px] sm:w-[270px] md:w-[300px] bg-surface-container-low shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05)] hover:bg-surface-container-lowest hover:shadow-[0_10px_28px_-4px_rgba(61,35,20,0.1)]';
@endphp
<div class="snap-start shrink-0 {{ $outer }} rounded-2xl p-2.5 sm:p-4 flex flex-col justify-between transition-all {{ $extraClass ?? '' }}">
    <div>
        <div class="relative rounded-xl overflow-hidden {{ $isTrending ? 'aspect-[4/3]' : 'aspect-square sm:aspect-[4/3]' }} mb-2.5 sm:mb-4 bg-surface-container">
            <img class="w-full h-full object-cover" alt="{{ $product->image_alt ?: $product->name }}" src="{{ media($product->image) }}" loading="lazy"/>
            @if($product->badge)
                <span class="absolute top-2 left-2 sm:top-2.5 sm:left-2.5 px-2 sm:px-2.5 py-0.5 rounded-full {{ $product->badgeClasses() }} font-label-sm text-[10px] sm:text-label-sm shadow-sm">{{ $product->badge }}</span>
            @endif
        </div>
        <h3 class="font-headline-sm text-headline-sm text-primary line-clamp-2">{{ $product->name }}</h3>
        @if($product->short_description)
            <p class="font-body-sm text-[12px] leading-[17px] sm:text-body-sm text-on-surface-variant mt-1 sm:mt-2 line-clamp-2 sm:line-clamp-none">{{ $product->short_description }}</p>
        @endif
    </div>
    <div class="pt-2.5 mt-2 sm:pt-4 sm:mt-4 flex items-end sm:items-center justify-between gap-1.5">
        <div class="min-w-0">
            <span class="font-label-sm text-label-sm text-on-surface-variant hidden sm:block">{{ $product->price_label ?: ($priceLabel ?? 'Price') }}</span>
            <span class="font-title-md text-[16px] sm:text-title-md text-primary font-bold">{{ money($product->price) }}</span>
        </div>
        <div class="flex items-center gap-1.5 shrink-0">
            @if(setting('show_add_to_basket', '1') === '1')
            <form method="POST" action="{{ route('cart.add', $product) }}">
                @csrf
                <button type="submit" aria-label="Add {{ $product->name }} to basket" title="Add to basket" class="w-8 h-8 rounded-full bg-surface-container-high text-primary hover:bg-secondary hover:text-on-secondary transition-all flex items-center justify-center shadow-sm"><span class="material-symbols-outlined text-[17px]">add_shopping_cart</span></button>
            </form>
            @endif
            <a aria-label="Order {{ $product->name }} on WhatsApp" class="w-8 h-8 sm:w-auto sm:h-auto justify-center {{ $isTrending ? 'sm:px-4 sm:py-2 gap-1.5' : 'sm:px-3.5 sm:py-1.5 gap-1' }} rounded-full bg-whatsapp text-white font-label-md text-label-md shadow-sm hover:bg-whatsapp-dark transition-all inline-flex items-center" href="{{ $product->whatsappLink() }}" rel="noopener noreferrer" target="_blank">
                <span class="material-symbols-outlined text-[16px]">chat</span><span class="hidden sm:inline">{{ $buttonText ?? ($isTrending ? 'Order' : 'WhatsApp') }}</span>
            </a>
        </div>
    </div>
</div>
