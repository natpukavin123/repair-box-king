{{-- SEO Body partial — include at the start of <body> --}}
@php
    $gtmId = \App\Models\Setting::getValue('seo_google_tag_manager', '');
    $bodyScripts = \App\Models\Setting::getValue('seo_body_scripts', '');
@endphp

@if($gtmId)
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

@if($bodyScripts)
{!! $bodyScripts !!}
@endif
