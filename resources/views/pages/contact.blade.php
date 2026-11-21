@extends('layouts.app')

@section('title', 'Contact')

@section('content')
@php
    $hero = section('contact.hero');
    $cards = section('contact.cards');
    $form = section('contact.form');
    $loc = section('contact.location');
    $faqSec = section('contact.faqs');
    $gallery = section('contact.gallery');
    $inquiryTypes = $form->activeItems();
    $selectedType = old('inquiry_type', optional($inquiryTypes->first())->title);
    $inputCls = 'w-full px-4 py-3.5 rounded-xl bg-surface text-on-surface placeholder:text-on-surface-variant/50 focus:outline-none focus:ring-2 focus:ring-secondary/40 transition-all font-body-md text-body-md';
@endphp
<div class="flex flex-col w-full">
<div class="relative w-full overflow-hidden">
    <div class="absolute -top-32 -left-20 w-96 h-96 rounded-full bg-secondary-fixed/40 blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute top-1/3 -right-24 w-[30rem] h-[30rem] rounded-full bg-tertiary-fixed/30 blur-3xl pointer-events-none -z-10"></div>

    @if($hero->is_active)
    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin pt-8 md:pt-12 pb-10 md:pb-14">
        <div class="flex items-center gap-space-xs text-on-surface-variant font-label-md text-label-md mb-6">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('home') }}"><span class="material-symbols-outlined text-[16px]">cottage</span> Home</a>
            <span class="text-outline-variant">/</span>
            <span class="text-secondary font-semibold">{{ $hero->x('breadcrumb', 'Contact') }}</span>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-end">
            <div class="lg:col-span-8 flex flex-col gap-3">
                @if($hero->eyebrow)
                <div class="inline-flex items-center gap-2 self-start px-3.5 py-1.5 rounded-full bg-surface-container-high text-secondary font-label-sm text-label-sm uppercase tracking-wider shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-secondary animate-pulse"></span>
                    {{ $hero->eyebrow }}
                </div>
                @endif
                <h1 class="font-display text-display text-primary tracking-tight">{{ $hero->title }}</h1>
                <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">{{ $hero->subtitle }}</p>
            </div>
            @if($hero->x('status_title'))
            <div class="lg:col-span-4 flex lg:justify-end">
                <div class="p-4 rounded-2xl bg-surface-container-low shadow-[0_4px_20px_-2px_rgba(61,35,20,0.06)] flex items-center gap-4 w-full sm:w-auto">
                    <div class="w-12 h-12 rounded-full bg-secondary-fixed flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-[24px]">{{ $hero->x('status_icon', 'local_fire_department') }}</span>
                    </div>
                    <div>
                        <p class="font-label-sm text-label-sm text-secondary uppercase tracking-wider">{{ $hero->x('status_label') }}</p>
                        <p class="font-title-md text-title-md text-primary font-semibold">{{ $hero->x('status_title') }}</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $hero->x('status_text') }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
    @endif

    @if($cards->is_active)
    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin mb-16 md:mb-20">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($cards->activeItems() as $i => $card)
            <div class="group relative rounded-2xl bg-surface-container-low p-6 md:p-8 flex flex-col justify-between shadow-[0_4px_20px_-2px_rgba(61,35,20,0.05),0_2px_6px_-1px_rgba(200,125,56,0.04)] hover:shadow-[0_12px_32px_-4px_rgba(61,35,20,0.10)] transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-xl bg-surface-container-high flex items-center justify-center text-secondary">
                            <span class="material-symbols-outlined text-[26px]">{{ $card->icon }}</span>
                        </div>
                        @if($card->label)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-lowest font-label-sm text-label-sm {{ $i === 1 ? 'text-on-surface-variant' : 'text-secondary font-semibold' }} shadow-sm">
                            @if($i === 0)<span class="w-2 h-2 rounded-full bg-emerald-600"></span>@endif {{ $card->label }}
                        </span>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-primary mb-2">{{ $card->title }}</h3>
                        <p class="font-body-md text-body-md text-on-surface-variant">{{ $i === 1 ? setting('address_full', $card->description) : $card->description }}</p>
                        @if($i === 1)
                        <div class="mt-3 p-3 rounded-xl bg-surface-container text-on-surface-variant font-body-sm text-body-sm">
                            <p class="font-semibold text-primary">{{ setting('opening_hours') }}</p>
                            @if(setting('closed_note'))<p class="text-secondary mt-0.5">{{ setting('closed_note') }}</p>@endif
                        </div>
                        @elseif($i === 2)
                        <div class="mt-3 flex flex-col gap-2 font-body-md text-body-md">
                            @if(setting('phone'))
                            <a class="flex items-center gap-2 text-primary font-semibold hover:text-secondary transition-colors" href="{{ tel_link(setting('phone')) }}"><span class="material-symbols-outlined text-[18px] text-secondary">phone_iphone</span>{{ setting('phone') }}</a>
                            @endif
                            @if(setting('email'))
                            <a class="flex items-center gap-2 text-primary font-semibold hover:text-secondary transition-colors" href="mailto:{{ setting('email') }}"><span class="material-symbols-outlined text-[18px] text-secondary">mail</span>{{ setting('email') }}</a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @if($card->value)
                <div class="mt-8 pt-4">
                    @php
                        $href = $card->link ? str_replace(['{email}', '{phone}'], [setting('email'), preg_replace('/[^\d+]/', '', (string) setting('phone'))], $card->link) : '#';
                        $href = link_to_url($href);
                        $external = str_starts_with($href, 'https://wa.me') || str_starts_with($href, 'http');
                    @endphp
                    <a class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 rounded-full {{ $i === 0 ? 'bg-primary text-on-primary shadow-md hover:bg-secondary duration-300' : 'bg-surface-container-high text-primary hover:bg-surface-container-highest' }} font-label-lg text-label-lg transition-all" href="{{ $href }}" @if($external) rel="noopener noreferrer" target="_blank" @endif>
                        <span class="material-symbols-outlined text-[18px]">{{ $card->subtitle ?: 'arrow_forward' }}</span>
                        <span>{{ str_replace('{phone}', setting('whatsapp_display', setting('phone')), $card->value) }}</span>
                    </a>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <section class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            @if($form->is_active)
            <div class="lg:col-span-7 bg-surface-container-low rounded-3xl p-6 sm:p-10 shadow-[0_8px_30px_rgba(61,35,20,0.06)] relative overflow-hidden" id="contact-form">
                <div class="flex items-center justify-between pb-6 mb-6">
                    <div>
                        <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold">{{ $form->eyebrow }}</span>
                        <h2 class="font-headline-md text-headline-md text-primary mt-1">{{ $form->title }}</h2>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-[24px]">{{ $form->eyebrow_icon ?: 'drafts' }}</span>
                    </div>
                </div>
                @if(session('contact_sent'))
                <div class="p-4 rounded-xl bg-emerald-50 text-emerald-900 flex items-center gap-3 mb-6">
                    <span class="material-symbols-outlined text-[24px] text-emerald-700">check_circle</span>
                    <p class="font-body-md text-body-md">{{ $form->x('success_message', 'Thank you! Your note has reached our bakers.') }}</p>
                </div>
                @endif
                <form class="flex flex-col gap-6" method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="font-label-lg text-label-lg text-primary" for="contact-name">Full Name *</label>
                            <input class="{{ $inputCls }}" id="contact-name" name="name" value="{{ old('name', optional(auth()->user())->fullName()) }}" placeholder="e.g. Maya Sharma" required type="text"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-label-lg text-label-lg text-primary" for="contact-phone">WhatsApp / Phone *</label>
                            <input class="{{ $inputCls }}" id="contact-phone" name="phone" value="{{ old('phone', optional(auth()->user())->phone) }}" placeholder="+91 98765 00000" required type="tel"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-lg text-label-lg text-primary" for="contact-email">Email Address</label>
                        <input class="{{ $inputCls }}" id="contact-email" name="email" value="{{ old('email', optional(auth()->user())->email) }}" placeholder="maya@example.com (optional for tracking receipts)" type="email"/>
                    </div>
                    @if($inquiryTypes->count())
                    <div class="flex flex-col gap-2.5">
                        <label class="font-label-lg text-label-lg text-primary">{{ $form->x('types_label', 'How can our bakers craft for you? *') }}</label>
                        <div class="flex flex-wrap gap-2" id="inquiryPills">
                            @foreach($inquiryTypes as $type)
                            <button class="inquiry-btn px-4 py-2 rounded-full font-label-md text-label-md transition-all {{ $selectedType === $type->title ? 'bg-primary text-on-primary shadow-sm' : 'bg-surface hover:bg-surface-container text-on-surface' }}" onclick="selectInquiry(this, @js($type->title))" type="button">{{ $type->subtitle ?: $type->title }}</button>
                            @endforeach
                        </div>
                        <input id="selectedInquiryType" name="inquiry_type" type="hidden" value="{{ $selectedType }}"/>
                    </div>
                    @endif
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="font-label-lg text-label-lg text-primary" for="event-date">Event or Pickup Date (Optional)</label>
                            <input class="{{ $inputCls }}" id="event-date" name="event_date" value="{{ old('event_date') }}" type="date"/>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="font-label-lg text-label-lg text-primary" for="guest-count">Estimated Guests / Boxes</label>
                            <input class="{{ $inputCls }}" id="guest-count" name="guests" value="{{ old('guests') }}" min="1" placeholder="e.g. 15 guests or 4 boxes" type="number"/>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <label class="font-label-lg text-label-lg text-primary" for="contact-message">{{ $form->x('message_label', 'Your Cake Vision or Tasting Inquiries *') }}</label>
                        <textarea class="{{ $inputCls }} resize-y" id="contact-message" name="message" placeholder="{{ $form->x('message_placeholder', 'Tell us your flavor cravings, dietary restrictions, or packaging preferences...') }}" required rows="4">{{ old('message') }}</textarea>
                    </div>
                    <div class="flex items-center gap-3 p-3.5 rounded-xl bg-surface">
                        <input @checked(old('whatsapp_copy', true)) class="w-5 h-5 accent-secondary cursor-pointer rounded" id="whatsapp-toggle" name="whatsapp_copy" value="1" type="checkbox"/>
                        <label class="cursor-pointer font-body-md text-body-md text-on-surface select-none" for="whatsapp-toggle">{{ $form->x('whatsapp_toggle_text', 'Send a copy to my WhatsApp with instant baker queue confirmation') }}</label>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                        <button class="w-full sm:flex-1 py-4 px-6 rounded-full bg-primary hover:bg-secondary text-on-primary font-label-lg text-label-lg shadow-[0_4px_16px_rgba(37,15,3,0.25)] transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center gap-2" type="submit">
                            <span class="material-symbols-outlined text-[20px]">local_fire_department</span>
                            <span>{{ $form->button_text ?: 'Send Message' }}</span>
                        </button>
                        @if($form->button2_text)
                        <a class="w-full sm:w-auto py-4 px-6 rounded-full bg-surface-container hover:bg-surface-container-high text-primary font-label-lg text-label-lg transition-all flex items-center justify-center gap-2" href="{{ link_to_url($form->button2_link ?: '{whatsapp}') }}" rel="noopener noreferrer" target="_blank">
                            <span class="material-symbols-outlined text-[20px] text-secondary">chat</span>
                            <span>{{ $form->button2_text }}</span>
                        </a>
                        @endif
                    </div>
                </form>
            </div>
            @endif

            <div class="lg:col-span-5 flex flex-col gap-6" id="location-map">
                @if($loc->is_active)
                <div class="bg-surface-container-low rounded-3xl p-6 sm:p-7 shadow-[0_8px_30px_rgba(61,35,20,0.06)] flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold">{{ $loc->eyebrow }}</span>
                            <h3 class="font-headline-sm text-headline-sm text-primary mt-0.5">{{ $loc->title }}</h3>
                        </div>
                        @if($loc->x('badge'))<span class="px-3 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-sm text-label-sm font-semibold">{{ $loc->x('badge') }}</span>@endif
                    </div>
                    <div class="relative w-full h-56 rounded-2xl overflow-hidden shadow-inner group">
                        @if(setting('map_embed_url'))
                            <iframe src="{{ setting('map_embed_url') }}" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                        @else
                            <div class="w-full h-full bg-cover bg-center transition-transform duration-700 group-hover:scale-105" style="background-image: url('{{ media($loc->image) }}')"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-primary/80 via-transparent to-transparent pointer-events-none"></div>
                            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-on-primary">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[20px] text-secondary-container">pin_drop</span>
                                    <span class="font-label-sm text-label-sm font-medium">{{ $loc->x('pin_text', setting('address_short')) }}</span>
                                </div>
                                <a class="px-3 py-1 rounded-full bg-surface-container-lowest/90 backdrop-blur text-primary text-label-sm font-semibold hover:bg-surface-container-lowest transition-colors" href="{{ setting('map_url', 'https://maps.google.com') }}" rel="noopener noreferrer" target="_blank">Open in Maps</a>
                            </div>
                        @endif
                    </div>
                    @if($loc->x('pickup_title'))
                    <div class="p-4 rounded-2xl bg-surface flex items-start gap-3.5">
                        <span class="material-symbols-outlined text-[22px] text-secondary mt-0.5">{{ $loc->x('pickup_icon', 'pedal_bike') }}</span>
                        <div class="flex flex-col">
                            <h4 class="font-title-md text-title-md text-primary font-semibold">{{ $loc->x('pickup_title') }}</h4>
                            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ $loc->x('pickup_text') }}</p>
                        </div>
                    </div>
                    @endif
                    @if($loc->x('live_text'))
                    <div class="p-4 rounded-2xl bg-tertiary-fixed/40 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-emerald-600 animate-ping"></div>
                            <div>
                                <p class="font-label-sm text-label-sm text-on-tertiary-fixed-variant uppercase tracking-wider font-semibold">{{ $loc->x('live_label') }}</p>
                                <p class="font-body-md text-body-md text-primary font-semibold">{{ $loc->x('live_text') }}</p>
                            </div>
                        </div>
                        @if($loc->x('live_badge'))<span class="font-label-sm text-label-sm px-2.5 py-1 rounded-full bg-surface-container-lowest text-primary shadow-sm font-medium">{{ $loc->x('live_badge') }}</span>@endif
                    </div>
                    @endif
                </div>
                @endif

                @if($faqSec->is_active && $faqs->count())
                <div class="bg-surface-container-low rounded-3xl p-6 sm:p-7 shadow-[0_8px_30px_rgba(61,35,20,0.06)] flex flex-col gap-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="material-symbols-outlined text-[22px] text-secondary">{{ $faqSec->eyebrow_icon ?: 'help' }}</span>
                        <h3 class="font-headline-sm text-headline-sm text-primary">{{ $faqSec->title }}</h3>
                    </div>
                    <div class="flex flex-col gap-2.5">
                        @foreach($faqs as $faq)
                        <div class="rounded-xl bg-surface p-3.5 cursor-pointer transition-all hover:bg-surface-container" onclick="toggleFaq(this)">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-title-md text-title-md text-primary font-medium">{{ $faq->question }}</span>
                                <span class="material-symbols-outlined text-[20px] text-secondary faq-icon transition-transform">expand_more</span>
                            </div>
                            <div class="faq-content hidden pt-2.5 text-on-surface-variant font-body-sm text-body-sm">{{ $faq->answer }}</div>
                        </div>
                        @endforeach
                    </div>
                    @if($faqSec->button_text)
                    <a class="inline-flex items-center gap-1.5 font-label-md text-label-md text-secondary hover:text-primary transition-colors mt-2 self-start" href="{{ link_to_url($faqSec->button_link) }}">
                        <span>{{ $faqSec->button_text }}</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </section>

    @if($gallery->is_active && $gallery->activeItems()->count())
    <section class="w-full bg-surface-container-low py-12 mb-8">
        <div class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                @foreach($gallery->activeItems() as $img)
                <div class="rounded-2xl overflow-hidden h-44 sm:h-56 shadow-sm group">
                    <div class="w-full h-full bg-cover bg-center transition-transform duration-500 group-hover:scale-105" role="img" aria-label="{{ $img->title }}" style="background-image: url('{{ media($img->image) }}')"></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
</div>
@endsection

@push('scripts')
<script>
    function selectInquiry(button, type) {
        document.querySelectorAll('#inquiryPills .inquiry-btn').forEach(btn => {
            btn.classList.remove('bg-primary', 'text-on-primary', 'shadow-sm');
            btn.classList.add('bg-surface', 'text-on-surface');
        });
        button.classList.remove('bg-surface', 'text-on-surface');
        button.classList.add('bg-primary', 'text-on-primary', 'shadow-sm');
        document.getElementById('selectedInquiryType').value = type;
    }
    function toggleFaq(el) {
        const c = el.querySelector('.faq-content'), i = el.querySelector('.faq-icon');
        const hidden = c.classList.toggle('hidden');
        i.style.transform = hidden ? 'rotate(0deg)' : 'rotate(180deg)';
    }
</script>
@endpush
