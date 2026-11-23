@php
    $gtm = trim((string) setting('gtm_id'));
    $ads = trim((string) setting('google_ads_id'));
    $adsLabel = trim((string) setting('google_ads_purchase_label'));
    $anyTracking = $gtm || setting('ga4_id') || $ads || setting('meta_pixel_id') || setting('clarity_id');
    $consent = setting('cookie_consent_enabled', '1') === '1';
    $events = session('track_events', []);
@endphp
@if($gtm)
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif
<script>
    /* Sends one event to GA4 / GTM dataLayer / Meta Pixel / Google Ads. */
    window.cnTrack = function (name, params) {
        params = params || {};
        try {
            window.dataLayer = window.dataLayer || [];
            if (params.items) window.dataLayer.push({ecommerce: null});
            window.dataLayer.push(Object.assign({event: name}, params.items ? {ecommerce: params} : params));
            if (typeof gtag === 'function') gtag('event', name, params);
            var meta = {add_to_cart: 'AddToCart', begin_checkout: 'InitiateCheckout', purchase: 'Purchase', generate_lead: 'Lead', sign_up: 'CompleteRegistration', view_item_list: 'ViewContent', contact: 'Contact'}[name];
            if (meta && typeof fbq === 'function') fbq('track', meta, {value: params.value, currency: params.currency, content_ids: (params.items || []).map(function (i) { return i.item_id; }), content_type: 'product'});
            @if($ads && $adsLabel)
            if (name === 'purchase' && typeof gtag === 'function') gtag('event', 'conversion', {send_to: @json($ads.'/'.$adsLabel), value: params.value, currency: params.currency, transaction_id: params.transaction_id});
            @endif
        } catch (e) {}
    };
    (window.cnQueue || []).forEach(function (e) { cnTrack(e[0], e[1]); });
    @foreach($events as $e)
    cnTrack(@json($e['name']), @json((object) $e['params']));
    @endforeach
    /* Every WhatsApp / phone click is a lead. */
    document.addEventListener('click', function (ev) {
        var a = ev.target.closest && ev.target.closest('a[href]');
        if (!a) return;
        var h = a.getAttribute('href') || '';
        if (h.indexOf('wa.me') > -1) cnTrack('contact', {method: 'whatsapp', link_text: (a.textContent || '').trim().slice(0, 80), page_path: location.pathname});
        else if (h.indexOf('tel:') === 0) cnTrack('contact', {method: 'phone', page_path: location.pathname});
    }, true);
</script>
@if($anyTracking && $consent)
<div id="cnConsent" class="hidden fixed bottom-20 lg:bottom-6 left-4 right-4 lg:left-6 lg:right-auto lg:max-w-md z-[60] p-4 rounded-2xl bg-surface-container-lowest shadow-[0_24px_48px_-12px_rgba(42,23,12,0.25)] flex flex-col sm:flex-row items-start sm:items-center gap-3">
    <div class="flex items-start gap-2.5">
        <span class="material-symbols-outlined text-secondary text-[22px]">cookie</span>
        <p class="font-body-sm text-body-sm text-on-surface-variant">{{ setting('cookie_consent_text', 'We use cookies to improve your experience.') }} @if(\App\Models\Page::where('slug', 'privacy-policy')->exists())<a href="{{ url('/page/privacy-policy') }}" class="text-secondary underline">Privacy</a>@endif</p>
    </div>
    <div class="flex gap-2 shrink-0 self-end sm:self-auto">
        <button type="button" onclick="cnSetConsent('denied')" class="px-4 py-2 rounded-full bg-surface-container text-primary font-label-md text-label-md hover:bg-surface-container-high">Decline</button>
        <button type="button" onclick="cnSetConsent('granted')" class="px-4 py-2 rounded-full bg-primary text-on-primary font-label-md text-label-md hover:bg-secondary">Accept</button>
    </div>
</div>
<script>
    function cnSetConsent(v) {
        try { localStorage.setItem('cn_consent', v); } catch (e) {}
        window.cnConsent = v;
        document.getElementById('cnConsent').classList.add('hidden');
        if (v === 'granted') {
            if (typeof gtag === 'function') gtag('consent', 'update', {ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted', analytics_storage: 'granted'});
            if (typeof fbq === 'function') fbq('consent', 'grant');
            if (window.cnLoadClarity) window.cnLoadClarity();
        }
    }
    if (!window.cnConsent) document.getElementById('cnConsent').classList.remove('hidden');
</script>
@endif
