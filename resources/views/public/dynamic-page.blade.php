<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
    $metaTitle    = ($page->meta_title ?: $page->title) . ' | ' . $shopName;
    $metaDesc     = $page->meta_description ?: Str::limit(strip_tags($page->content), 160);
    $canonicalUrl = $page->canonical_url ?: (rtrim(config('app.url', url('/')), '/') . '/page/' . $page->slug);
    $homeUrl      = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl  = $shopIcon ? image_url($shopIcon) : '';
    $shopFullPhone = $shopPhone ? '+' . preg_replace('/\D+/', '', $shopPhone) : '';
    $shopFullWa    = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';

    // Replace {city} in content
    $contentHtml = str_replace('{city}', $cityName, $page->content);
    $metaTitle   = str_replace('{city}', $cityName, $metaTitle);
    $metaDesc    = str_replace('{city}', $cityName, $metaDesc);
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDesc }}">
@if($page->meta_keywords)
<meta name="keywords" content="{{ str_replace('{city}', $cityName, $page->meta_keywords) }}">
@endif
<meta name="robots" content="{{ $page->robots ?? 'index, follow' }}">
<meta name="author" content="{{ $shopName }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if($shopAddress)
<meta name="geo.region" content="IN">
<meta name="geo.placename" content="{{ $shopAddress }}">
@endif

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $page->og_title ? str_replace('{city}', $cityName, $page->og_title) : $metaTitle }}">
<meta property="og:description" content="{{ $page->og_description ? str_replace('{city}', $cityName, $page->og_description) : $metaDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
@if($shopLogoUrl)
<meta property="og:image" content="{{ $shopLogoUrl }}">
@endif

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDesc }}">

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
@include('components.seo-head')

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "{{ $page->schema_type ?? 'WebPage' }}",
      "name": "{{ str_replace('{city}', $cityName, $page->title) }}",
      "description": "{{ $metaDesc }}",
      "url": "{{ $canonicalUrl }}",
      @if($page->schema_type === 'Service')
      "provider": {
        "@type": "LocalBusiness",
        "name": "{{ $shopName }}",
        "url": "{{ $homeUrl }}",
        "telephone": "{{ $shopFullPhone }}",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "{{ $shopAddress }}",
          "addressCountry": "IN"
        }
      },
      "areaServed": {
        "@type": "City",
        "name": "{{ $cityName }}"
      },
      @endif
      "mainEntityOfPage": "{{ $canonicalUrl }}"
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ $homeUrl }}" },
        { "@type": "ListItem", "position": 2, "name": "{{ str_replace('{city}', $cityName, $page->title) }}", "item": "{{ $canonicalUrl }}" }
      ]
    }
    @if($faqs->isNotEmpty())
    ,{
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($faqs as $i => $faq)
        {
          "@type": "Question",
          "name": @json(str_replace('{city}', $cityName, $faq->question)),
          "acceptedAnswer": {
            "@type": "Answer",
            "text": @json(str_replace('{city}', $cityName, strip_tags($faq->answer)))
          }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
      ]
    }
    @endif
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
.faq-item{border:1px solid rgba(255,255,255,.06);border-radius:12px;margin-bottom:12px;overflow:hidden;background:rgba(255,255,255,.02);}
.faq-item details summary{padding:16px 20px;font-size:15px;font-weight:600;color:#e2e8f0;cursor:pointer;list-style:none;display:flex;align-items:center;justify-content:space-between;}
.faq-item details summary::-webkit-details-marker{display:none;}
.faq-item details summary::after{content:'+';font-size:20px;color:#64748b;}
.faq-item details[open] summary::after{content:'−';}
.faq-answer{padding:0 20px 16px;font-size:14px;color:#94a3b8;line-height:1.8;}
.cta-sec{padding:60px 0;text-align:center;background:linear-gradient(135deg,rgba(37,99,235,.08),rgba(124,58,237,.08));border-radius:16px;margin:0 20px 60px;}
.cta-sec h2{font-size:24px;font-weight:700;color:#f1f5f9;margin-bottom:12px;}
.cta-sec p{color:#94a3b8;margin-bottom:24px;font-size:15px;}
.footer{border-top:1px solid rgba(255,255,255,.06);padding:24px 0;text-align:center;font-size:13px;color:#475569;}
</style>
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
      <a href="/">Home</a>
      <a href="/blog">Blog</a>
      <a href="/faq">FAQ</a>
      <a href="{{ route('track.landing') }}">Track Repair</a>
    </div>
  </div>
</nav>

<section class="hero-sec">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a> &rsaquo; {{ str_replace('{city}', $cityName, $page->title) }}</div>
    <h1>{{ str_replace('{city}', $cityName, $page->title) }}</h1>
    <p class="hero-desc">{{ $metaDesc }}</p>
    <div class="cta-row">
      @if($shopWhatsapp)
      <a href="https://wa.me/{{ $shopFullWa }}?text={{ urlencode('Hi, I need more info about ' . str_replace('{city}', $cityName, $page->title)) }}" target="_blank" rel="noopener" class="btn btn-primary">WhatsApp Us</a>
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

@if($faqs->isNotEmpty())
<section class="faq-sec">
  <div class="container">
    <h2>Frequently Asked Questions</h2>
    @foreach($faqs as $faq)
    <div class="faq-item">
      <details>
        <summary>{{ str_replace('{city}', $cityName, $faq->question) }}</summary>
        <div class="faq-answer">{!! str_replace('{city}', $cityName, $faq->answer) !!}</div>
      </details>
    </div>
    @endforeach
  </div>
</section>
@endif

<section class="cta-sec">
  <div class="container">
    <h2>Need {{ str_replace('{city}', $cityName, $page->title) }}?</h2>
    <p>Visit {{ $shopName }} for fast, reliable service with genuine parts.</p>
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
  <div class="container">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
</footer>

</body>
</html>
