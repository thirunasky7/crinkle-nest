{{-- Carousel section heading: $eyebrow, $icon, $title, $subtitle, $track (id) --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-4 sm:mb-6">
    <div>
        @if($eyebrow)
        <div class="inline-flex items-center gap-1.5 text-secondary font-label-md text-label-md uppercase tracking-wider mb-1">
            @if($icon)<span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>@endif {{ $eyebrow }}
        </div>
        @endif
        <h2 class="font-headline-lg text-headline-lg text-primary">{{ $title }}</h2>
        @if($subtitle)<p class="font-body-md text-body-md text-on-surface-variant mt-1">{{ $subtitle }}</p>@endif
    </div>
    @if($track ?? null)
    <div class="hidden sm:flex items-center gap-2">
        <button aria-label="Previous" class="w-10 h-10 rounded-full bg-surface-container-high text-primary hover:bg-secondary hover:text-on-secondary transition-all flex items-center justify-center shadow-sm" onclick="scrollSection('{{ $track }}', -{{ $step ?? 300 }})">
            <span class="material-symbols-outlined text-[20px]">chevron_left</span>
        </button>
        <button aria-label="Next" class="w-10 h-10 rounded-full bg-surface-container-high text-primary hover:bg-secondary hover:text-on-secondary transition-all flex items-center justify-center shadow-sm" onclick="scrollSection('{{ $track }}', {{ $step ?? 300 }})">
            <span class="material-symbols-outlined text-[20px]">chevron_right</span>
        </button>
    </div>
    @endif
</div>
