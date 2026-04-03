<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
    $metaTitle   = $post->getEffectiveMetaTitle() . ' | ' . $shopName;
    $metaDesc    = $post->getEffectiveMetaDescription();
    $canonicalUrl = $post->canonical_url ?: (rtrim(config('app.url', url('/')), '/') . '/blog/' . $post->slug);
    $homeUrl      = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl  = $shopIcon ? image_url($shopIcon) : '';
    $ogImage      = $post->og_image ?: ($post->featured_image ? image_url($post->featured_image) : $shopLogoUrl);
    $shopFullWa   = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDesc }}">
@if($post->meta_keywords)
<meta name="keywords" content="{{ $post->meta_keywords }}">
@endif
<meta name="robots" content="{{ $post->robots ?? 'index, follow' }}">
<meta name="author" content="{{ $post->author?->name ?? $shopName }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:type" content="article">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $post->og_title ?: $metaTitle }}">
<meta property="og:description" content="{{ $post->og_description ?: $metaDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
@if($post->published_at)
<meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
<meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $post->og_title ?: $metaTitle }}">
<meta name="twitter:description" content="{{ $post->og_description ?: $metaDesc }}">
@if($ogImage)
<meta name="twitter:image" content="{{ $ogImage }}">
@endif

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

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "headline": "{{ $post->title }}",
      "description": "{{ $metaDesc }}",
      "url": "{{ $canonicalUrl }}",
      @if($ogImage)"image": "{{ $ogImage }}",@endif
      @if($post->published_at)"datePublished": "{{ $post->published_at->toIso8601String() }}",@endif
      "dateModified": "{{ $post->updated_at->toIso8601String() }}",
      "author": {
        "@type": "Person",
        "name": "{{ $post->author?->name ?? $shopName }}"
      },
      "publisher": {
        "@type": "Organization",
        "name": "{{ $shopName }}",
        "url": "{{ $homeUrl }}"
      }
    },
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": "{{ $homeUrl }}" },
        { "@type": "ListItem", "position": 2, "name": "Blog", "item": "{{ $homeUrl }}blog" },
        { "@type": "ListItem", "position": 3, "name": "{{ $post->title }}", "item": "{{ $canonicalUrl }}" }
      ]
    }
  ]
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#020617;color:#e2e8f0;line-height:1.8;}
a{color:#3b82f6;text-decoration:none;}
a:hover{text-decoration:underline;}
.container{max-width:800px;margin:0 auto;padding:0 20px;}
.nav{background:rgba(2,6,23,.95);border-bottom:1px solid rgba(255,255,255,.06);padding:16px 0;position:sticky;top:0;z-index:100;backdrop-filter:blur(12px);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;max-width:800px;margin:0 auto;padding:0 20px;}
.nav-brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:15px;color:#fff;text-decoration:none;}
.nav-brand img{width:32px;height:32px;border-radius:8px;object-fit:cover;}
.nav-links a{color:#94a3b8;font-size:13px;margin-left:20px;transition:color .2s;}
.nav-links a:hover{color:#fff;text-decoration:none;}
.article-header{padding:60px 0 30px;text-align:center;}
.breadcrumb{font-size:13px;color:#64748b;margin-bottom:20px;}
.breadcrumb a{color:#3b82f6;}
h1{font-size:clamp(26px,4vw,38px);font-weight:800;line-height:1.3;color:#f1f5f9;margin-bottom:16px;}
.article-meta{font-size:13px;color:#64748b;display:flex;gap:16px;justify-content:center;margin-bottom:20px;}
.featured-img{width:100%;max-height:400px;object-fit:cover;border-radius:16px;margin-bottom:40px;}
.article-content{padding:0 0 40px;}
.article-content h2{font-size:22px;font-weight:700;color:#f1f5f9;margin:32px 0 12px;}
.article-content h3{font-size:18px;font-weight:600;color:#e2e8f0;margin:24px 0 10px;}
.article-content p{font-size:16px;color:#94a3b8;margin-bottom:16px;}
.article-content ul,.article-content ol{padding-left:24px;margin-bottom:16px;}
.article-content li{font-size:15px;color:#94a3b8;margin-bottom:6px;}
.article-content img{max-width:100%;border-radius:12px;margin:20px 0;}
.article-content blockquote{border-left:4px solid #3b82f6;padding:12px 20px;margin:20px 0;background:rgba(59,130,246,.05);border-radius:0 8px 8px 0;color:#cbd5e1;}
.related-sec{padding:40px 0 60px;border-top:1px solid rgba(255,255,255,.06);}
.related-sec h2{font-size:20px;font-weight:700;color:#f1f5f9;margin-bottom:20px;}
.related-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;}
.related-card{border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:16px;background:rgba(255,255,255,.02);}
.related-card h3{font-size:15px;font-weight:600;color:#e2e8f0;margin-bottom:6px;}
.related-card h3 a{color:inherit;}
.related-card h3 a:hover{color:#3b82f6;}
.related-card p{font-size:13px;color:#64748b;}
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
      <a href="/faq">FAQ</a>
      <a href="{{ route('track.landing') }}">Track Repair</a>
    </div>
  </div>
</nav>

<article>
  <header class="article-header">
    <div class="container">
      <div class="breadcrumb"><a href="/">Home</a> &rsaquo; <a href="/blog">Blog</a> &rsaquo; {{ $post->title }}</div>
      <h1>{{ $post->title }}</h1>
      <div class="article-meta">
        @if($post->author)<span>By {{ $post->author->name }}</span>@endif
        @if($post->published_at)<span>{{ $post->published_at->format('F d, Y') }}</span>@endif
      </div>
    </div>
  </header>

  <div class="container">
    @if($post->featured_image)
    <img src="{{ image_url($post->featured_image) }}" alt="{{ $post->image_alt ?: $post->title }}" class="featured-img" loading="lazy">
    @endif

    <div class="article-content">
      {!! $post->content !!}
    </div>
  </div>
</article>

@if($related->isNotEmpty())
<section class="related-sec">
  <div class="container">
    <h2>Related Posts</h2>
    <div class="related-grid">
      @foreach($related as $rel)
      <div class="related-card">
        <h3><a href="/blog/{{ $rel->slug }}">{{ $rel->title }}</a></h3>
        <p>{{ Str::limit(strip_tags($rel->excerpt ?: $rel->content), 100) }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

@if($shopWhatsapp)
<section class="cta-sec">
  <div class="container">
    <h2>Need Help With Your Device?</h2>
    <p>Contact {{ $shopName }} for fast, reliable mobile repair service.</p>
    <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" class="btn btn-primary">WhatsApp Us</a>
  </div>
</section>
@endif

<footer class="footer">
  <div class="container">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
</footer>

</body>
</html>
