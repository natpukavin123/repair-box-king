<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
    $pageTitle   = 'Frequently Asked Questions' . ($titleSuffix ? ' | ' . $titleSuffix : '');
    $pageDesc    = 'Find answers to common questions about mobile repair services, screen replacement, battery replacement, and more at ' . $shopName . '.';
    $canonicalUrl = rtrim(config('app.url', url('/')), '/') . '/faq';
    $homeUrl      = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl  = $shopIcon ? image_url($shopIcon) : '';
    $shopFullWa   = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';

    // Collect all FAQs for structured data
    $allFaqs = collect();
    foreach($categories as $cat) {
        $allFaqs = $allFaqs->merge($cat->faqs);
    }
    $allFaqs = $allFaqs->merge($uncategorized);
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
@if($shopLogoUrl)
<meta property="og:image" content="{{ $shopLogoUrl }}">
@endif

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">

@php $shopFaviconUrl = (!empty($shopFavicon)) ? image_url($shopFavicon) : ''; @endphp
@if($shopFaviconUrl)
<link rel="icon" type="image/png" href="{{ $shopFaviconUrl }}?v=2">
<link rel="shortcut icon" type="image/png" href="{{ $shopFaviconUrl }}?v=2">
@else
<link rel="icon" href="/favicon.ico?v=2" type="image/x-icon">
<link rel="shortcut icon" href="/favicon.ico?v=2" type="image/x-icon">
@endif

<meta name="theme-color" content="#020617">
@include('components.seo-head')

{{-- FAQ Schema.org Structured Data --}}
@if($allFaqs->isNotEmpty())
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($allFaqs as $i => $faq)
        {
          "@type": "Question",
          "name": @json($faq->question),
          "acceptedAnswer": {
            "@type": "Answer",
            "text": @json(strip_tags($faq->answer))
          }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
      ]
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ $homeUrl }}" },
        { "@type": "ListItem", "position": 2, "name": "FAQ", "item": "{{ $canonicalUrl }}" }
      ]
    }
  ]
}
</script>
@endif

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
.hero{padding:60px 0 40px;text-align:center;}
h1{font-size:clamp(28px,5vw,42px);font-weight:800;line-height:1.2;background:linear-gradient(135deg,#fff 30%,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:12px;}
.hero p{color:#94a3b8;font-size:15px;max-width:600px;margin:0 auto;}
.faq-section{padding:0 0 40px;}
.faq-category-title{font-size:18px;font-weight:700;color:#f1f5f9;margin:32px 0 16px;padding-bottom:8px;border-bottom:1px solid rgba(255,255,255,.06);}
.faq-item{border:1px solid rgba(255,255,255,.06);border-radius:12px;margin-bottom:12px;overflow:hidden;background:rgba(255,255,255,.02);}
.faq-item details{cursor:pointer;}
.faq-item summary{padding:16px 20px;font-size:15px;font-weight:600;color:#e2e8f0;list-style:none;display:flex;align-items:center;justify-content:space-between;}
.faq-item summary::-webkit-details-marker{display:none;}
.faq-item summary::after{content:'+';font-size:20px;color:#64748b;transition:transform .2s;}
.faq-item details[open] summary::after{content:'−';}
.faq-answer{padding:0 20px 16px;font-size:14px;color:#94a3b8;line-height:1.8;}
.cta-sec{padding:60px 0;text-align:center;background:linear-gradient(135deg,rgba(37,99,235,.08),rgba(124,58,237,.08));border-radius:16px;margin:0 20px 60px;}
.cta-sec h2{font-size:22px;font-weight:700;color:#f1f5f9;margin-bottom:12px;}
.cta-sec p{color:#94a3b8;margin-bottom:20px;font-size:15px;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:12px 28px;border-radius:50px;font-size:14px;font-weight:600;text-decoration:none !important;transition:transform .2s,box-shadow .2s;}
.btn-primary{background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(37,99,235,.3);}
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
      <a href="/faq" style="color:#fff;">FAQ</a>
      <a href="{{ route('track.landing') }}">Track Repair</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div style="font-size:13px;color:#64748b;margin-bottom:16px;"><a href="/">Home</a> &rsaquo; FAQ</div>
    <h1>Frequently Asked Questions</h1>
    <p>{{ $pageDesc }}</p>
  </div>
</section>

<section class="faq-section">
  <div class="container">
    @foreach($categories as $category)
      @if($category->faqs->isNotEmpty())
      <h2 class="faq-category-title">{{ $category->name }}</h2>
      @foreach($category->faqs as $faq)
      <div class="faq-item">
        <details>
          <summary>{{ $faq->question }}</summary>
          <div class="faq-answer">{!! $faq->answer !!}</div>
        </details>
      </div>
      @endforeach
      @endif
    @endforeach

    @if($uncategorized->isNotEmpty())
      @if($categories->isNotEmpty())
      <h2 class="faq-category-title">General</h2>
      @endif
      @foreach($uncategorized as $faq)
      <div class="faq-item">
        <details>
          <summary>{{ $faq->question }}</summary>
          <div class="faq-answer">{!! $faq->answer !!}</div>
        </details>
      </div>
      @endforeach
    @endif
  </div>
</section>

@if($shopWhatsapp)
<section class="cta-sec">
  <div class="container">
    <h2>Still Have Questions?</h2>
    <p>Contact {{ $shopName }} directly — we're happy to help!</p>
    <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" class="btn btn-primary">Ask on WhatsApp</a>
  </div>
</section>
@endif

<footer class="footer">
  <div class="container">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
</footer>

</body>
</html>
