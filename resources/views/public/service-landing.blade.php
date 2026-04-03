<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
    $canonicalUrl = rtrim(config('app.url', url('/')), '/') . '/' . $slug;
    $homeUrl      = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl  = $shopIcon ? image_url($shopIcon) : '';
    $shopFullPhone = $shopPhone ? '+' . preg_replace('/\D+/', '', $shopPhone) : '';
    $shopFullWa    = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';
@endphp

<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="keywords" content="{{ $seoKeywords }}">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<meta name="author" content="{{ $shopName }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if($shopAddress)
<meta name="geo.region" content="IN">
<meta name="geo.placename" content="{{ $shopAddress }}">
@endif

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDescription }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
@if($shopLogoUrl)
<meta property="og:image" content="{{ $shopLogoUrl }}">
@endif

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDescription }}">

@php $shopFaviconUrl = (!empty($shopFavicon)) ? image_url($shopFavicon) : ''; @endphp
@if($shopFaviconUrl)
<link rel="icon" type="image/png" href="{{ $shopFaviconUrl }}?v=2">
<link rel="shortcut icon" type="image/png" href="{{ $shopFaviconUrl }}?v=2">
@elseif($shopLogoUrl)
<link rel="icon" type="image/png" href="{{ $shopLogoUrl }}?v=2">
<link rel="shortcut icon" type="image/png" href="{{ $shopLogoUrl }}?v=2">
@else
<link rel="icon" href="/favicon.ico?v=2" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">
@endif

<meta name="theme-color" content="#020617">

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "LocalBusiness",
      "@id": "{{ $homeUrl }}#business",
      "name": "{{ $shopName }}",
      "url": "{{ $homeUrl }}",
      "telephone": "{{ $shopFullPhone }}",
      "email": "{{ $shopEmail }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $shopAddress }}",
        "addressCountry": "IN"
      },
      "areaServed": {
        "@type": "City",
        "name": "{{ $cityName }}"
      }
    },
    {
      "@type": "Service",
      "name": "{{ $serviceTitle }}",
      "description": "{{ $seoDescription }}",
      "provider": { "@id": "{{ $homeUrl }}#business" },
      "areaServed": {
        "@type": "City",
        "name": "{{ $cityName }}"
      }
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ $homeUrl }}" },
        { "@type": "ListItem", "position": 2, "name": "{{ $serviceTitle }}", "item": "{{ $canonicalUrl }}" }
      ]
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
          "@type": "Question",
          "name": "{{ $faq['q'] }}",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "{{ $faq['a'] }}"
          }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
      ]
    }
  ]
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#020617;color:#e2e8f0;line-height:1.7;}
a{color:#3b82f6;text-decoration:none;}
a:hover{text-decoration:underline;}
.container{max-width:800px;margin:0 auto;padding:0 20px;}
.nav{background:rgba(2,6,23,.95);border-bottom:1px solid rgba(255,255,255,.06);padding:16px 0;position:sticky;top:0;z-index:100;backdrop-filter:blur(12px);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;max-width:800px;margin:0 auto;padding:0 20px;}
.nav-brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:15px;color:#fff;text-decoration:none;}
.nav-brand img{width:32px;height:32px;border-radius:8px;object-fit:cover;}
.nav-links a{color:#94a3b8;font-size:13px;margin-left:20px;transition:color .2s;}
.nav-links a:hover{color:#fff;text-decoration:none;}
.hero-sec{padding:80px 0 60px;text-align:center;}
.breadcrumb{font-size:13px;color:#64748b;margin-bottom:24px;}
.breadcrumb a{color:#3b82f6;}
h1{font-size:clamp(28px,5vw,42px);font-weight:800;line-height:1.2;background:linear-gradient(135deg,#fff 30%,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:16px;}
.hero-desc{font-size:16px;color:#94a3b8;max-width:600px;margin:0 auto 32px;}
.cta-row{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:50px;font-size:14px;font-weight:600;text-decoration:none !important;transition:transform .2s,box-shadow .2s;}
.btn-primary{background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(37,99,235,.3);}
.btn-outline{border:1.5px solid rgba(255,255,255,.15);color:#e2e8f0;background:transparent;}
.btn-outline:hover{border-color:rgba(255,255,255,.3);background:rgba(255,255,255,.03);}
.content-sec{padding:40px 0 60px;}
.content-sec h2{font-size:24px;font-weight:700;color:#f1f5f9;margin:40px 0 16px;}
.content-sec h2:first-child{margin-top:0;}
.content-sec p,.content-sec li{font-size:15px;color:#94a3b8;margin-bottom:12px;}
.content-sec ul{padding-left:20px;}
.content-sec li{margin-bottom:8px;}
.faq-sec{padding:40px 0 60px;}
.faq-sec h2{font-size:24px;font-weight:700;color:#f1f5f9;text-align:center;margin-bottom:32px;}
.faq-item{border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:20px 24px;margin-bottom:12px;background:rgba(255,255,255,.02);}
.faq-q{font-size:15px;font-weight:600;color:#e2e8f0;margin-bottom:8px;}
.faq-a{font-size:14px;color:#94a3b8;line-height:1.7;}
.cta-sec{padding:60px 0;text-align:center;background:linear-gradient(135deg,rgba(37,99,235,.08),rgba(124,58,237,.08));border-radius:16px;margin:0 20px 60px;}
.cta-sec h2{font-size:24px;font-weight:700;color:#f1f5f9;margin-bottom:12px;}
.cta-sec p{color:#94a3b8;margin-bottom:24px;font-size:15px;}
.footer{border-top:1px solid rgba(255,255,255,.06);padding:24px 0;text-align:center;font-size:13px;color:#475569;}
</style>
@include('components.seo-head')
</head>
<body>
@include('components.seo-body')

<nav class="nav">
  <div class="nav-inner">
    <a href="/" class="nav-brand">
      @if($shopLogoUrl)<img src="{{ $shopLogoUrl }}" alt="{{ $shopName }}">@endif
      {{ $shopName }}
    </a>
    <div class="nav-links">
      <a href="/#services">Services</a>
      <a href="{{ route('track.landing') }}">Track Repair</a>
      <a href="/#contact">Contact</a>
    </div>
  </div>
</nav>

<section class="hero-sec">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> &rsaquo; {{ $serviceTitle }}</div>
    <h1>{{ $serviceTitle }} in {{ $cityName }}</h1>
    <p class="hero-desc">{{ $seoDescription }}</p>
    <div class="cta-row">
      @if($shopWhatsapp)
      <a href="https://wa.me/{{ $shopFullWa }}?text={{ urlencode('Hi, I need ' . $serviceTitle . ' service') }}" target="_blank" rel="noopener" class="btn btn-primary">WhatsApp Us</a>
      @endif
      <a href="{{ route('track.landing') }}" class="btn btn-outline">Track Your Repair</a>
    </div>
  </div>
</section>

<section class="content-sec">
  <div class="container">
    {!! $contentHtml !!}
  </div>
</section>

@if(count($faqs))
<section class="faq-sec">
  <div class="container">
    <h2>Frequently Asked Questions</h2>
    @foreach($faqs as $faq)
    <div class="faq-item">
      <div class="faq-q">{{ $faq['q'] }}</div>
      <div class="faq-a">{{ $faq['a'] }}</div>
    </div>
    @endforeach
  </div>
</section>
@endif

<section class="cta-sec">
  <div class="container">
    <h2>Need {{ $serviceTitle }}?</h2>
    <p>Visit {{ $shopName }} in {{ $cityName }} for fast, reliable service with genuine parts.</p>
    <div class="cta-row">
      @if($shopWhatsapp)
      <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" class="btn btn-primary">Get a Quote on WhatsApp</a>
      @elseif($shopPhone)
      <a href="tel:{{ $shopPhone }}" class="btn btn-primary">Call Now</a>
      @endif
      <a href="/" class="btn btn-outline">Back to Home</a>
    </div>
  </div>
</section>

<footer class="footer">
  <div class="container">
    &copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.
  </div>
</footer>

</body>
</html>
