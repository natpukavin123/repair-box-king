{{-- SEO Head partial — include in all public <head> sections --}}
@php
    $gaId    = \App\Models\Setting::getValue('seo_google_analytics', '');
    $gtmId   = \App\Models\Setting::getValue('seo_google_tag_manager', '');
    $gVerify = \App\Models\Setting::getValue('seo_google_verification', '');
    $bVerify = \App\Models\Setting::getValue('seo_bing_verification', '');
    $headScripts = \App\Models\Setting::getValue('seo_head_scripts', '');
@endphp

@if($gVerify)
<meta name="google-site-verification" content="{{ $gVerify }}">
@endif
@if($bVerify)
<meta name="msvalidate.01" content="{{ $bVerify }}">
@endif

@if($gtmId)
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','{{ $gtmId }}');</script>
@elseif($gaId)
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ $gaId }}');</script>
@endif

@if($headScripts)
{!! $headScripts !!}
@endif
