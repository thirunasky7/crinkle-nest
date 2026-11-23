@php
    $col1 = \App\Models\Menu::at('footer_bakes');
    $col2 = \App\Models\Menu::at('footer_quick');
    $bottom = \App\Models\Menu::at('footer_bottom');
@endphp
<footer class="w-full bg-surface-container-low mt-space-lg md:mt-space-xl pt-space-lg md:pt-space-xl pb-space-lg shadow-[0_-2px_16px_rgba(61,35,20,0.03)]">
    <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin">
        @if(setting('newsletter_enabled', '0') === '1')
        <div class="mb-space-lg md:mb-space-xl rounded-2xl bg-surface-container p-5 md:p-8 flex flex-col lg:flex-row lg:items-center justify-between gap-4 md:gap-6" id="newsletter">
            <div class="flex items-start gap-3 max-w-xl">
                <div class="w-11 h-11 shrink-0 rounded-full bg-secondary-fixed text-on-secondary-fixed flex items-center justify-center"><span class="material-symbols-outlined">mark_email_unread</span></div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-primary">{{ setting('newsletter_title', 'Get fresh batch alerts') }}</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">{{ setting('newsletter_text') }}</p>
                </div>
            </div>
            @if(session('subscribed'))
                <p class="font-label-lg text-label-lg text-emerald-800 bg-emerald-50 px-4 py-3 rounded-full flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check_circle</span>{{ setting('newsletter_success', 'You are on the list!') }}</p>
            @else
            <form method="POST" action="{{ route('subscribe') }}" class="flex flex-col sm:flex-row gap-2 w-full lg:w-auto lg:min-w-[420px]">
                @csrf
                <input type="hidden" name="source" value="footer">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <label for="nl-email" class="sr-only">Email address</label>
                <input id="nl-email" type="email" name="email" required placeholder="Your email address" class="flex-1 h-12 px-5 rounded-full bg-surface-container-lowest text-on-surface font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/40">
                <button class="h-12 px-6 rounded-full bg-primary text-on-primary font-label-lg text-label-lg hover:bg-secondary transition-all whitespace-nowrap">Subscribe</button>
            </form>
            @endif
        </div>
        @endif
        <div class="grid grid-cols-2 lg:grid-cols-12 gap-space-md md:gap-space-lg mb-space-lg md:mb-space-xl">
            <div class="col-span-2 lg:col-span-4 flex flex-col gap-space-sm">
                <div class="flex items-center gap-space-xs"><span class="font-headline-md text-headline-md text-primary">{{ setting('site_name', 'Crinkle Nest') }}</span></div>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-sm">{{ setting('footer_about') }}</p>
                <div class="flex items-center gap-space-sm pt-space-xs">
                    @if(setting('social_instagram'))
                        <a aria-label="Instagram" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-secondary hover:text-on-secondary transition-all" href="{{ setting('social_instagram') }}"><span class="material-symbols-outlined text-[18px]">photo_camera</span></a>
                    @endif
                    @if(setting('social_facebook'))
                        <a aria-label="Facebook" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-secondary hover:text-on-secondary transition-all" href="{{ setting('social_facebook') }}"><span class="material-symbols-outlined text-[18px]">thumb_up</span></a>
                    @endif
                    @if(setting('social_youtube'))
                        <a aria-label="YouTube" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-secondary hover:text-on-secondary transition-all" href="{{ setting('social_youtube') }}"><span class="material-symbols-outlined text-[18px]">smart_display</span></a>
                    @endif
                    @if(setting('map_url'))
                        <a aria-label="Store Location" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-secondary hover:text-on-secondary transition-all" href="{{ setting('map_url') }}"><span class="material-symbols-outlined text-[18px]">location_on</span></a>
                    @endif
                    @if(setting('email'))
                        <a aria-label="Email Inquiries" class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant hover:bg-secondary hover:text-on-secondary transition-all" href="mailto:{{ setting('email') }}"><span class="material-symbols-outlined text-[18px]">mail</span></a>
                    @endif
                </div>
            </div>
            <div class="lg:col-span-3 flex flex-col gap-space-xs">
                <h4 class="font-title-md text-title-md text-primary mb-space-xs">{{ setting('footer_col1_title', 'Curated Bakes') }}</h4>
                <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                    @foreach($col1 as $link)
                        <li class="hover:text-primary transition-colors"><a href="{{ $link->href() }}" @if($link->new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="lg:col-span-2 flex flex-col gap-space-xs">
                <h4 class="font-title-md text-title-md text-primary mb-space-xs">{{ setting('footer_col2_title', 'Quick Links') }}</h4>
                <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface-variant">
                    @foreach($col2 as $link)
                        <li class="hover:text-primary transition-colors"><a href="{{ $link->href() }}" @if($link->new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="col-span-2 lg:col-span-3 flex flex-col gap-space-xs">
                <h4 class="font-title-md text-title-md text-primary mb-space-xs">{{ setting('footer_col3_title', 'The Bakehouse') }}</h4>
                <p class="font-body-md text-body-md text-on-surface-variant">{{ setting('address_short') }}<br/>{{ setting('opening_hours') }}</p>
                <p class="font-body-md text-body-md text-on-surface-variant pt-2">@if(setting('phone'))Direct line: <a href="{{ tel_link(setting('phone')) }}" class="hover:text-primary">{{ setting('phone') }}</a>@endif<br/>@if(setting('email'))<a href="mailto:{{ setting('email') }}" class="hover:text-primary">{{ setting('email') }}</a>@endif</p>
            </div>
        </div>
        <div class="pt-space-md flex flex-col sm:flex-row items-center justify-between gap-space-sm font-body-sm text-body-sm text-on-surface-variant/80 bg-surface-container/40 p-space-md rounded-xl">
            <p>{{ str_replace('{year}', date('Y'), setting('copyright_text', '© {year} Crinkle Nest Artisanal Bakery. All rights reserved.')) }}</p>
            <div class="flex items-center gap-space-md flex-wrap justify-center">
                @foreach($bottom as $link)
                    <a class="hover:text-primary transition-colors" href="{{ $link->href() }}" @if($link->new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
