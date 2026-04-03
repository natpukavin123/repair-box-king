<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
    $pageTitle   = 'Blog' . ($titleSuffix ? ' | ' . $titleSuffix : '');
    $pageDesc    = 'Read the latest tips, guides, and news about mobile repair, screen replacement, battery care and more from ' . $shopName . '.';
    $canonicalUrl = rtrim(config('app.url', url('/')), '/') . '/blog';
    $homeUrl      = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl  = $shopIcon ? image_url($shopIcon) : '';
    $shopFullWa   = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';
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

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">

@php $shopFaviconUrl = (!empty($shopFavicon)) ? image_url($shopFavicon) : ''; @endphp
@if($shopFaviconUrl)
<link rel="icon" type="image/png" href="{{ $shopFaviconUrl }}">
@else
<link rel="icon" href="/favicon.ico" type="image/x-icon">
@endif

<meta name="theme-color" content="#020617">
@include('components.seo-head')

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "{{ $shopName }} Blog",
  "url": "{{ $canonicalUrl }}",
  "publisher": {
    "@type": "Organization",
    "name": "{{ $shopName }}",
    "url": "{{ $homeUrl }}"
  }
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#020617;color:#e2e8f0;line-height:1.7;}
a{color:#3b82f6;text-decoration:none;}
a:hover{text-decoration:underline;}
.container{max-width:900px;margin:0 auto;padding:0 20px;}
.nav{background:rgba(2,6,23,.95);border-bottom:1px solid rgba(255,255,255,.06);padding:16px 0;position:sticky;top:0;z-index:100;backdrop-filter:blur(12px);}
.nav-inner{display:flex;align-items:center;justify-content:space-between;max-width:900px;margin:0 auto;padding:0 20px;}
.nav-brand{display:flex;align-items:center;gap:10px;font-weight:700;font-size:15px;color:#fff;text-decoration:none;}
.nav-brand img{width:32px;height:32px;border-radius:8px;object-fit:cover;}
.nav-links a{color:#94a3b8;font-size:13px;margin-left:20px;transition:color .2s;}
.nav-links a:hover{color:#fff;text-decoration:none;}
.hero{padding:60px 0 40px;text-align:center;}
h1{font-size:clamp(28px,5vw,42px);font-weight:800;line-height:1.2;background:linear-gradient(135deg,#fff 30%,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;margin-bottom:12px;}
.hero p{color:#94a3b8;font-size:15px;max-width:600px;margin:0 auto;}
.posts-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;padding:0 0 60px;}
.post-card{border:1px solid rgba(255,255,255,.06);border-radius:16px;overflow:hidden;background:rgba(255,255,255,.02);transition:border-color .3s,transform .2s;}
.post-card:hover{border-color:rgba(59,130,246,.3);transform:translateY(-2px);}
.post-img{width:100%;height:180px;object-fit:cover;background:#0f172a;}
.post-body{padding:20px;}
.post-body h2{font-size:17px;font-weight:700;color:#f1f5f9;margin-bottom:8px;line-height:1.3;}
.post-body h2 a{color:inherit;text-decoration:none;}
.post-body h2 a:hover{color:#3b82f6;}
.post-excerpt{font-size:14px;color:#94a3b8;margin-bottom:12px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.post-meta{font-size:12px;color:#64748b;display:flex;gap:12px;}
.pagination-wrap{text-align:center;padding:0 0 60px;}
.pagination-wrap a,.pagination-wrap span{display:inline-block;padding:8px 14px;margin:0 2px;border-radius:8px;font-size:13px;border:1px solid rgba(255,255,255,.1);color:#94a3b8;transition:all .2s;}
.pagination-wrap a:hover{background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.3);color:#fff;text-decoration:none;}
.pagination-wrap .active{background:rgba(59,130,246,.2);border-color:#3b82f6;color:#fff;}
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
      <a href="/blog" style="color:#fff;">Blog</a>
      <a href="/faq">FAQ</a>
      <a href="{{ route('track.landing') }}">Track Repair</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div style="font-size:13px;color:#64748b;margin-bottom:16px;"><a href="/">Home</a> &rsaquo; Blog</div>
    <h1>Our Blog</h1>
    <p>Tips, guides, and updates about mobile repair and technology</p>
  </div>
</section>

<section class="container">
  <div class="posts-grid">
    @forelse($posts as $post)
    <article class="post-card">
      @if($post->featured_image)
      <img src="{{ image_url($post->featured_image) }}" alt="{{ $post->image_alt ?: $post->title }}" class="post-img" loading="lazy">
      @else
      <div class="post-img" style="display:flex;align-items:center;justify-content:center;">
        <svg style="width:48px;height:48px;color:#1e293b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
      </div>
      @endif
      <div class="post-body">
        <h2><a href="/blog/{{ $post->slug }}">{{ $post->title }}</a></h2>
        <p class="post-excerpt">{{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}</p>
        <div class="post-meta">
          @if($post->author)<span>By {{ $post->author->name }}</span>@endif
          @if($post->published_at)<span>{{ $post->published_at->format('M d, Y') }}</span>@endif
        </div>
      </div>
    </article>
    @empty
    <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#475569;">
      <p>No blog posts published yet. Check back soon!</p>
    </div>
    @endforelse
  </div>

  @if($posts->hasPages())
  <div class="pagination-wrap">
    @if($posts->onFirstPage())
      <span>&laquo;</span>
    @else
      <a href="{{ $posts->previousPageUrl() }}">&laquo;</a>
    @endif

    @foreach($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
      @if($page == $posts->currentPage())
        <span class="active">{{ $page }}</span>
      @else
        <a href="{{ $url }}">{{ $page }}</a>
      @endif
    @endforeach

    @if($posts->hasMorePages())
      <a href="{{ $posts->nextPageUrl() }}">&raquo;</a>
    @else
      <span>&raquo;</span>
    @endif
  </div>
  @endif
</section>

<footer class="footer">
  <div class="container">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
</footer>

</body>
</html>
