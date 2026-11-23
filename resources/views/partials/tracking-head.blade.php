@php
    $gtm = trim((string) setting('gtm_id'));
    $ga4 = trim((string) setting('ga4_id'));
    $ads = trim((string) setting('google_ads_id'));
    $pixel = trim((string) setting('meta_pixel_id'));
    $clarity = trim((string) setting('clarity_id'));
    $consent = setting('cookie_consent_enabled', '1') === '1';
    $gtagId = $ga4 ?: $ads;
@endphp
@if($gtm || $gtagId || $pixel || $clarity)
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    window.cnConsent = 'granted';
    @if($consent)
    try { window.cnConsent = localStorage.getItem('cn_consent'); } catch (e) { window.cnConsent = null; }
    @endif
    @if($consent)
    gtag('consent', 'default', {ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied', analytics_storage: 'denied', wait_for_update: 500});
    if (window.cnConsent === 'granted') gtag('consent', 'update', {ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted', analytics_storage: 'granted'});
    @endif
</script>
@endif
@if($gtm)
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer',@json($gtm));</script>
@endif
@if($gtagId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
<script>
    gtag('js', new Date());
    @if($ga4) gtag('config', @json($ga4)); @endif
    @if($ads) gtag('config', @json($ads)); @endif
</script>
@endif
@if($pixel)
<script>
    !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
    @if($consent) if (window.cnConsent !== 'granted') fbq('consent', 'revoke'); @endif
    fbq('init', @json($pixel));
    fbq('track', 'PageView');
</script>
@endif
@if($clarity)
<script>
    window.cnLoadClarity = function () {
        if (window.clarity) return;
        (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,"clarity","script",@json($clarity));
    };
    if (window.cnConsent === 'granted') window.cnLoadClarity();
</script>
@endif
