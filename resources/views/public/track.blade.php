@php
    $statusOrder  = ['received','in_progress','completed','payment','closed'];
    $statusLabels = [
        'received'    => 'Received',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'payment'     => 'Payment',
        'closed'      => 'Closed',
        'cancelled'   => 'Cancelled',
    ];
    $statusColors = [
        'received'    => ['bg'=>'#dbeafe','text'=>'#1d4ed8','border'=>'#93c5fd','dot'=>'#3b82f6'],
        'in_progress' => ['bg'=>'#fef3c7','text'=>'#b45309','border'=>'#fcd34d','dot'=>'#f59e0b'],
        'completed'   => ['bg'=>'#d1fae5','text'=>'#065f46','border'=>'#6ee7b7','dot'=>'#10b981'],
        'payment'     => ['bg'=>'#ede9fe','text'=>'#6d28d9','border'=>'#c4b5fd','dot'=>'#8b5cf6'],
        'closed'      => ['bg'=>'#dcfce7','text'=>'#166534','border'=>'#86efac','dot'=>'#22c55e'],
        'cancelled'   => ['bg'=>'#fee2e2','text'=>'#991b1b','border'=>'#fca5a5','dot'=>'#ef4444'],
    ];
    $currentRepair   = $repair ?? null;
    $currentStatus   = $currentRepair ? ($currentRepair->status ?? 'received') : null;
    $currentStepIdx  = $currentStatus ? array_search($currentStatus, $statusOrder) : -1;
    $isCancelled     = $currentStatus === 'cancelled';
    $totalPaid       = $currentRepair ? ($currentRepair->payments->where('direction','IN')->sum('amount') ?? 0) : 0;
    $balance         = $currentRepair ? max(0, ($currentRepair->estimated_cost + ($currentRepair->service_charge ?? 0)) - $totalPaid) : 0;
@endphp
<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
    $trackTitle   = 'Track Your Repair — ' . $shopName;
    $trackDesc    = 'Check the real-time repair status of your device at ' . $shopName . '. Enter your tracking ID to get live updates on your repair.';
    $trackUrl     = rtrim(config('app.url', url('/')), '/') . '/track';
    $shopLogoUrl  = !empty($shopIcon) ? image_url($shopIcon) : '';
    $shopFavUrl   = !empty($shopFavicon) ? image_url($shopFavicon) : '';
@endphp
<title>{{ $trackTitle }}</title>
<meta name="description" content="{{ $trackDesc }}">
<meta name="robots" content="index, follow">
<meta name="author" content="{{ $shopName }}">
<link rel="canonical" href="{{ $trackUrl }}">

{{-- Open Graph --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $trackTitle }}">
<meta property="og:description" content="{{ $trackDesc }}">
<meta property="og:url" content="{{ $trackUrl }}">
@if($shopLogoUrl)
<meta property="og:image" content="{{ $shopLogoUrl }}">
<meta property="og:image:alt" content="{{ $shopName }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ $trackTitle }}">
<meta name="twitter:description" content="{{ $trackDesc }}">
@if($shopLogoUrl)
<meta name="twitter:image" content="{{ $shopLogoUrl }}">
@endif

{{-- Favicon --}}
@if($shopFavUrl)
<link rel="icon" type="image/png" href="{{ $shopFavUrl }}">
<link rel="shortcut icon" type="image/png" href="{{ $shopFavUrl }}">
<link rel="apple-touch-icon" href="{{ $shopFavUrl }}">
@elseif($shopLogoUrl)
<link rel="icon" type="image/png" href="{{ $shopLogoUrl }}">
<link rel="shortcut icon" type="image/png" href="{{ $shopLogoUrl }}">
<link rel="apple-touch-icon" href="{{ $shopLogoUrl }}">
@else
<link rel="icon" href="/favicon.ico" type="image/x-icon">
@endif

<meta name="theme-color" content="#020617">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $shopName }}">

{{-- JSON-LD --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "{{ $trackTitle }}",
  "description": "{{ $trackDesc }}",
  "url": "{{ $trackUrl }}",
  "isPartOf": { "@type": "WebSite", "name": "{{ $shopName }}", "url": "{{ rtrim(config('app.url', url('/')), '/') }}/" }
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html,body{height:100%;}
body{font-family:'Inter',system-ui,sans-serif;background:#030712;color:#e2e8f0;display:flex;flex-direction:column;min-height:100vh;}

/* ── Container ── */
.container{max-width:1160px;margin:0 auto;padding:0 24px;}
/* ── Navbar ── */
:root{--bg:#030712;--bg2:#080e1c;--bg3:#0f172a;--bl:#3b82f6;--border:rgba(255,255,255,.06);--glass:rgba(255,255,255,.025);}
.scroll-progress{position:fixed;top:0;left:0;height:2px;z-index:9999;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#06b6d4);width:0;transition:width .1s linear;}
.navbar{position:fixed;top:0;left:0;right:0;z-index:1000;padding:12px 0;background:rgba(3,7,18,.88);backdrop-filter:blur(20px);border-bottom:1px solid rgba(255,255,255,.05);transition:transform .4s cubic-bezier(.4,0,.2,1),background .4s,padding .3s,box-shadow .3s;}
.navbar.scrolled{background:rgba(3,7,18,.97);backdrop-filter:blur(28px);border-bottom:1px solid var(--border);padding:8px 0;box-shadow:0 4px 30px rgba(0,0,0,.5);}
.navbar.nav-hidden{transform:translateY(-110%);}
.navbar-inner{display:flex;align-items:center;justify-content:space-between;}
.nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;}
.nav-logo{width:36px;height:36px;border-radius:10px;border:1px solid var(--border);background:var(--glass);display:flex;align-items:center;justify-content:center;overflow:hidden;transition:border-color .2s;flex-shrink:0;}
.nav-logo:hover{border-color:rgba(59,130,246,.3);}
.nav-logo img{width:100%;height:100%;object-fit:cover;}
.nav-logo-fb{font-size:11px;font-weight:800;color:#fff;}
.nav-brand-text{display:flex;flex-direction:column;gap:1px;}
.nav-name{font-size:15px;font-weight:800;color:#fff;line-height:1.2;}
.nav-certified{font-size:9px;font-weight:700;color:#60a5fa;letter-spacing:1.2px;text-transform:uppercase;display:flex;align-items:center;gap:4px;}
.nav-certified-dot{width:4px;height:4px;border-radius:50%;background:#3b82f6;display:inline-block;}
.nav-links{display:flex;align-items:center;gap:4px;}
.nav-link{color:#94a3b8;font-size:14px;font-weight:500;padding:7px 13px;border-radius:8px;transition:all .2s;text-decoration:none;}
.nav-link:hover{background:rgba(255,255,255,.06);color:#fff;}
.nav-cta{background:linear-gradient(135deg,#2563eb,#7c3aed)!important;color:#fff!important;font-weight:700;border-radius:10px;padding:9px 20px;box-shadow:0 4px 20px rgba(37,99,235,.3);}
.nav-cta:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(37,99,235,.45)!important;background:linear-gradient(135deg,#1d4ed8,#6d28d9)!important;}
.mobile-menu-btn{display:none;background:none;border:none;color:#fff;cursor:pointer;padding:6px;}
.nav-mobile{display:none;position:fixed;inset:0;background:rgba(3,7,18,.98);z-index:9999;flex-direction:column;align-items:center;justify-content:center;gap:8px;}
.nav-mobile.open{display:flex;}
.nav-mobile a{font-size:20px;font-weight:600;color:#fff;padding:14px 40px;border-radius:12px;transition:background .2s;width:260px;text-align:center;text-decoration:none;}
.nav-mobile a:hover{background:var(--glass);}
.nav-mobile-close{position:absolute;top:20px;right:22px;background:none;border:none;color:#fff;cursor:pointer;}

/* ── Hero ── */
.hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0f172a 100%);padding:120px 20px 60px;flex:1;display:flex;align-items:center;}
.hero-inner{max-width:600px;margin:0 auto;text-align:center;width:100%;}
.hero-chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#93c5fd;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;padding:5px 14px;border-radius:99px;margin-bottom:20px;}
.hero-title{font-size:36px;font-weight:900;color:#fff;line-height:1.15;margin-bottom:10px;}
.hero-sub{font-size:15px;color:#94a3b8;margin-bottom:36px;line-height:1.6;}

/* ── Search box ── */
.search-card{background:#fff;border-radius:16px;padding:8px;display:flex;gap:8px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.search-input{flex:1;border:none;outline:none;font-family:inherit;font-size:16px;font-weight:600;padding:12px 16px;color:#0f172a;letter-spacing:.5px;background:transparent;}
.search-input::placeholder{font-weight:400;color:#94a3b8;letter-spacing:0;}
.search-btn{background:#0f172a;color:#fff;border:none;font-family:inherit;font-size:14px;font-weight:700;padding:12px 24px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:8px;white-space:nowrap;transition:background .15s;}
.search-btn:hover{background:#1e293b;}
.search-hint{font-size:12px;color:#64748b;margin-top:12px;}

/* ── Content ── */
.results-wrap{background:#030712;}
.content{max-width:900px;margin:0 auto;padding:36px 24px 56px;}

/* ── Alert ── */
.alert{background:rgba(255,255,255,.04);border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:12px;padding:20px 24px;display:flex;align-items:flex-start;gap:14px;}
.alert-icon{width:24px;height:24px;flex-shrink:0;color:#ef4444;margin-top:1px;}
.alert-title{font-size:15px;font-weight:700;color:#fca5a5;}
.alert-sub{font-size:13px;color:#f87171;margin-top:3px;}

/* ── Status Hero card ── */
.result-header{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:20px;}
.repair-id-block .label{font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;}
.repair-id-block .ticket{font-size:22px;font-weight:900;color:#f1f5f9;margin-top:4px;letter-spacing:-.5px;}
.repair-id-block .tracking{font-size:13px;color:#64748b;margin-top:4px;font-family:monospace;letter-spacing:.5px;}
.status-hero{text-align:right;}
.status-pill{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:99px;font-size:14px;font-weight:700;border:2px solid;}
.status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}

/* ── Grid layout ── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
@media(max-width:640px){.grid-2{grid-template-columns:1fr;}}

/* ── Card ── */
.card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:24px;margin-bottom:20px;}
.card-title{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.card-title svg{opacity:.7;}

/* ── Info rows ── */
.info-row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,.06);}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-row:first-child{padding-top:0;}
.info-label{font-size:13px;color:#64748b;flex-shrink:0;}
.info-value{font-size:13px;font-weight:600;color:#e2e8f0;text-align:right;}

/* ── Progress stepper ── */
.stepper{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:24px 28px;margin-bottom:20px;}
.stepper-title{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:24px;}
.steps{display:flex;align-items:flex-start;position:relative;}
.step{flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:1;}
.step-line{position:absolute;top:18px;left:50%;right:-50%;height:2px;background:rgba(255,255,255,.1);z-index:0;}
.step:last-child .step-line{display:none;}
.step-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;border:2px solid;transition:all .3s;position:relative;z-index:1;background:rgba(255,255,255,.05);}
.step-circle.done{background:#0f172a;border-color:#3b82f6;color:#fff;}
.step-circle.active{border-color:#3b82f6;background:rgba(59,130,246,.15);color:#60a5fa;box-shadow:0 0 0 4px rgba(59,130,246,.2);}
.step-circle.inactive{border-color:rgba(255,255,255,.1);color:#475569;}
.step-label{font-size:11px;font-weight:600;color:#475569;margin-top:10px;text-align:center;line-height:1.4;}
.step-label.done{color:#94a3b8;}
.step-label.active{color:#60a5fa;}
.step-line.done{background:#3b82f6;}

/* cancelled banner */
.cancelled-banner{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;margin-bottom:20px;}
.cancelled-banner svg{color:#ef4444;flex-shrink:0;}
.cancelled-banner p{font-size:14px;font-weight:600;color:#fca5a5;}
.cancelled-banner span{font-size:13px;color:#f87171;font-weight:400;}

/* ── Timeline ── */
.timeline{position:relative;padding-left:28px;}
.timeline::before{content:'';position:absolute;left:7px;top:8px;bottom:8px;width:2px;background:rgba(255,255,255,.08);border-radius:1px;}
.timeline-item{position:relative;margin-bottom:20px;}
.timeline-item:last-child{margin-bottom:0;}
.timeline-dot{position:absolute;left:-28px;top:5px;width:14px;height:14px;border-radius:50%;border:2px solid rgba(3,7,18,1);box-shadow:0 0 0 2px rgba(255,255,255,.15);}
.timeline-dot.dot-active{box-shadow:0 0 0 3px;}
.timeline-meta{font-size:12px;color:#475569;margin-bottom:3px;}
.timeline-status{font-size:13px;font-weight:700;color:#e2e8f0;}
.timeline-note{font-size:12px;color:#64748b;margin-top:2px;}

/* ── cost card ── */
.cost-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid rgba(255,255,255,.06);}
.cost-row:last-child{border-bottom:none;}
.cost-row-label{font-size:13px;color:#64748b;}
.cost-row-value{font-size:14px;font-weight:700;color:#e2e8f0;}
.cost-row-value.green{color:#34d399;}
.cost-row-value.amber{color:#fbbf24;}
.cost-row-value.muted{color:#475569;font-weight:500;}
.cost-total-row{padding-top:16px;margin-top:4px;border-top:2px solid rgba(255,255,255,.12);}
.cost-total-label{font-size:14px;font-weight:700;color:#e2e8f0;}
.cost-total-value{font-size:20px;font-weight:900;color:#e2e8f0;}

/* ── Footer ── */
.footer{background:#030712;border-top:1px solid rgba(255,255,255,.06);padding:48px 0 24px;color:#94a3b8;margin-top:auto;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:42px;}
.footer-brand-row{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.footer-logo{width:34px;height:34px;border-radius:9px;border:1px solid var(--border);background:var(--glass);display:flex;align-items:center;justify-content:center;overflow:hidden;}
.footer-logo img{width:100%;height:100%;object-fit:cover;}
.footer-shop-name{font-size:15px;font-weight:800;color:#fff;}
.footer-desc{font-size:13px;color:#475569;line-height:1.7;max-width:270px;}
.footer-col-h{font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:14px;}
.footer-links{display:flex;flex-direction:column;gap:8px;}
.footer-links a{font-size:13px;color:#475569;transition:color .2s;text-decoration:none;}
.footer-links a:hover{color:#94a3b8;}
.footer-ci{display:flex;flex-direction:column;gap:8px;}
.footer-c{display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#475569;line-height:1.5;}
.footer-c svg{flex-shrink:0;margin-top:1px;color:#374151;}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1px solid var(--border);}
.footer-copy{font-size:12px;color:#374151;}
.footer-btm-links{display:flex;gap:18px;}
.footer-btm-links a{font-size:12px;color:#374151;transition:color .2s;text-decoration:none;}
.footer-btm-links a:hover{color:#64748b;}
/* WA Float */
.wa-float{position:fixed;bottom:24px;right:24px;z-index:500;}
.wa-float a{width:52px;height:52px;border-radius:50%;background:#25d366;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 30px rgba(37,211,102,.4);transition:all .3s;}
.wa-float a:hover{transform:scale(1.1) translateY(-2px);box-shadow:0 14px 40px rgba(37,211,102,.5);}
/* Responsive */
@media(max-width:768px){
  .nav-links{display:none;}
  .mobile-menu-btn{display:flex;align-items:center;}
  .hero{padding:90px 16px 48px;}
  .hero-title{font-size:28px;}
  .hero-sub{font-size:14px;}
  .grid-2{grid-template-columns:1fr;}
  .result-header{flex-direction:column;align-items:flex-start;gap:12px;padding:20px;}
  .status-hero{text-align:left;}
  .stepper{padding:20px 16px;}
  .steps{flex-wrap:nowrap;overflow-x:auto;}
  .step-label{font-size:9px;}
  .footer-grid{grid-template-columns:1fr;gap:24px;}
  .footer-bottom{flex-direction:column;gap:8px;text-align:center;}
  .footer-btm-links{justify-content:center;}
  .content{padding:20px 16px 40px;}
  .card{padding:16px;margin-bottom:14px;}
}
@media(max-width:480px){
  .container{padding:0 16px;}
  .hero{padding:80px 12px 44px;}
  .hero-title{font-size:22px;}
  .hero-sub{font-size:13px;}
  .search-card{flex-direction:column;padding:10px;gap:8px;}
  .search-btn{width:100%;justify-content:center;}
  .card{padding:14px;}
  .stepper{padding:14px 10px;}
  .step-circle{width:28px;height:28px;font-size:11px;}
  .step-line{top:14px;}
  .result-header{padding:16px;}
  .footer-grid{grid-template-columns:1fr;}
  .cost-total-value{font-size:17px;}
}
</style>
</head>
<body>

{{-- ── Navbar ── --}}
<div class="scroll-progress" id="scrollProgress"></div>
<nav class="navbar" id="navbar">
  <div class="container navbar-inner">
    <a href="{{ route('home') }}" class="nav-brand">
      <div class="nav-logo">
        @if($shopIcon)<img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">@else<div class="nav-logo-fb">{{ strtoupper(substr($shopName,0,2)) }}</div>@endif
      </div>
      <div class="nav-brand-text">
        <span class="nav-name">{{ $shopName }}</span>
        <span class="nav-certified"><span class="nav-certified-dot"></span>Certified Repair Experts</span>
      </div>
    </a>
    <div class="nav-links">
      <a href="{{ route('home') }}" class="nav-link">Home</a>
      <a href="{{ route('home') }}#services" class="nav-link">Services</a>
      <a href="{{ route('home') }}#contact" class="nav-link">Contact</a>
      <a href="{{ route('track.landing') }}" class="nav-link nav-cta">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
        Track Now
      </a>
    </div>
    <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </div>
</nav>
<div id="navMobile" class="nav-mobile">
  <button class="nav-mobile-close" onclick="document.getElementById('navMobile').classList.remove('open')" aria-label="Close menu">
    <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
  </button>
  <a href="{{ route('home') }}" onclick="document.getElementById('navMobile').classList.remove('open')">Home</a>
  <a href="{{ route('home') }}#services" onclick="document.getElementById('navMobile').classList.remove('open')">Services</a>
  <a href="{{ route('home') }}#contact" onclick="document.getElementById('navMobile').classList.remove('open')">Contact</a>
  <a href="{{ route('track.landing') }}" style="color:#60a5fa;">Track My Repair &rarr;</a>
</div>

{{-- ── Hero / Search ── --}}
<div class="hero">
    <div class="hero-inner">
        <div class="hero-chip">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Repair Status
        </div>
        <h1 class="hero-title">Track Your Repair</h1>
        <p class="hero-sub">Enter the Tracking ID from your repair receipt to check the latest status of your device.</p>

        <form method="GET" action="" id="trackForm" onsubmit="submitTrack(event)">
            <div class="search-card">
                <input
                    type="text"
                    name="q"
                    id="trackInput"
                    class="search-input"
                    placeholder="e.g. TRK-C06C030E"
                    value="{{ !empty($repair) ? $repair->tracking_id : (isset($notFound) && $notFound ? request()->segment(2) : '') }}"
                    autocomplete="off"
                    spellcheck="false"
                    maxlength="20"
                    style="text-transform:uppercase;"
                >
                <button type="submit" class="search-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    Track
                </button>
            </div>
        </form>
        <p class="search-hint">Your Tracking ID is printed on your repair receipt</p>
    </div>
</div>

{{-- ── Results Section ── --}}
@if((isset($notFound) && $notFound) || !empty($repair))
<div class="results-wrap">
<div class="content">

    @if(isset($notFound) && $notFound)
    {{-- Not found --}}
    <div class="alert">
        <svg class="alert-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <div class="alert-title">Tracking ID not found</div>
            <div class="alert-sub">We couldn't find a repair with that tracking ID. Please check the ID on your receipt and try again.</div>
        </div>
    </div>

    @elseif(!empty($repair))
    @php $sc = $statusColors[$currentStatus] ?? $statusColors['received']; @endphp

    {{-- ── Status Header ── --}}
    <div class="result-header">
        <div class="repair-id-block">
            <div class="label">Repair Ticket</div>
            <div class="ticket">#{{ $repair->ticket_number }}</div>
            <div class="tracking">{{ $repair->tracking_id }}</div>
        </div>
        <div class="status-hero">
            <div class="label" style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Current Status</div>
            <div class="status-pill" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }};">
                <div class="status-dot" style="background:{{ $sc['dot'] }};"></div>
                {{ $statusLabels[$currentStatus] ?? ucfirst(str_replace('_',' ',$currentStatus)) }}
            </div>
        </div>
    </div>

    {{-- ── Cancelled Banner ── --}}
    @if($isCancelled)
    <div class="cancelled-banner">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <div>
            <p>This repair has been cancelled.
            @if($repair->cancel_reason)
                <span>&nbsp;&mdash;&nbsp;{{ $repair->cancel_reason }}</span>
            @endif
            </p>
        </div>
    </div>

    @else
    {{-- ── Progress Stepper ── --}}
    <div class="stepper">
        <div class="stepper-title">Repair Progress</div>
        <div class="steps">
            @foreach($statusOrder as $si => $stepKey)
            @php
                $isDone   = $currentStepIdx > $si;
                $isActive = $currentStepIdx === $si;
                $lineClass = $isDone ? 'done' : '';
            @endphp
            <div class="step">
                {{-- connector line to next step --}}
                @if(!$loop->last)
                <div class="step-line {{ $lineClass }}"></div>
                @endif

                <div class="step-circle {{ $isDone ? 'done' : ($isActive ? 'active' : 'inactive') }}">
                    @if($isDone)
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    @elseif($isActive)
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
                    @else
                        <span style="font-size:12px;font-weight:800;">{{ $si + 1 }}</span>
                    @endif
                </div>
                <div class="step-label {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                    {{ $statusLabels[$stepKey] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Device + Customer Grid ── --}}
    <div class="grid-2">
        {{-- Device Info --}}
        <div class="card">
            <div class="card-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" stroke-linecap="round"/></svg>
                Device Details
            </div>
            <div class="info-row">
                <span class="info-label">Brand / Model</span>
                <span class="info-value">{{ $repair->device_brand }} {{ $repair->device_model }}</span>
            </div>
            @if($repair->imei)
            <div class="info-row">
                <span class="info-label">IMEI</span>
                <span class="info-value" style="font-family:monospace;">{{ $repair->imei }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Received On</span>
                <span class="info-value">{{ $repair->created_at->format('d M Y') }}</span>
            </div>
            @if($repair->expected_delivery_date)
            <div class="info-row">
                <span class="info-label">Expected Delivery</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d M Y') }}</span>
            </div>
            @endif
            @if($repair->completed_at)
            <div class="info-row">
                <span class="info-label">Completed On</span>
                <span class="info-value">{{ $repair->completed_at->format('d M Y') }}</span>
            </div>
            @endif
        </div>

        {{-- Payment Summary --}}
        <div class="card">
            <div class="card-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Cost Summary
            </div>
            <div class="cost-row" style="padding-top:0;">
                <span class="cost-row-label">Estimated Cost</span>
                <span class="cost-row-value">&#8377;{{ number_format($repair->estimated_cost, 2) }}</span>
            </div>
            @if($repair->service_charge > 0)
            <div class="cost-row">
                <span class="cost-row-label">Service Charge</span>
                <span class="cost-row-value">&#8377;{{ number_format($repair->service_charge, 2) }}</span>
            </div>
            @endif
            <div class="cost-row">
                <span class="cost-row-label">Advance Paid</span>
                <span class="cost-row-value green">
                    @if($totalPaid > 0)
                        &minus; &#8377;{{ number_format($totalPaid, 2) }}
                    @else
                        <span class="muted">&#8377;0.00</span>
                    @endif
                </span>
            </div>
            <div class="cost-row cost-total-row" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="cost-total-label">Balance Due</span>
                <span class="cost-total-value {{ $balance > 0 ? 'amber' : 'green' }}" style="color:{{ $balance > 0 ? '#d97706' : '#059669' }};">
                    &#8377;{{ number_format($balance, 2) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Problem Description ── --}}
    @if($repair->problem_description)
    <div class="card" style="margin-bottom:20px;">
        <div class="card-title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Problem Reported
        </div>
        <p style="font-size:14px;color:#94a3b8;line-height:1.7;white-space:pre-line;">{{ $repair->problem_description }}</p>
    </div>
    @endif

    {{-- ── Status History ── --}}
    @if($repair->statusHistory && $repair->statusHistory->count())
    <div class="card">
        <div class="card-title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Status History
        </div>
        <div class="timeline">
            @foreach($repair->statusHistory->sortByDesc('created_at') as $hist)
            @php $hsc = $statusColors[$hist->status] ?? $statusColors['received']; @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $loop->first ? 'dot-active' : '' }}"
                     style="background:{{ $hsc['dot'] }};{{ $loop->first ? 'box-shadow:0 0 0 3px '.$hsc['border'].';' : '' }}"></div>
                <div class="timeline-meta">{{ \Carbon\Carbon::parse($hist->created_at)->format('d M Y, g:i A') }}</div>
                <div class="timeline-status">
                    <span style="display:inline-block;background:{{ $hsc['bg'] }};color:{{ $hsc['text'] }};border:1px solid {{ $hsc['border'] }};padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;">
                        {{ $statusLabels[$hist->status] ?? ucfirst(str_replace('_',' ',$hist->status)) }}
                    </span>
                </div>
                @if($hist->notes)
                <div class="timeline-note">{{ $hist->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif {{-- end repair found --}}
</div>
</div>{{-- /.results-wrap --}}
@endif {{-- end results section --}}

{{-- ── Footer ── --}}
<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand-row">
          <div class="footer-logo">
            @if($shopIcon)<img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">@else<span style="font-size:10px;font-weight:800;color:#fff;">{{ strtoupper(substr($shopName,0,2)) }}</span>@endif
          </div>
          <span class="footer-shop-name">{{ $shopName }}</span>
        </div>
        <div class="footer-desc">{{ $shopSlogan ?: 'Professional device repairs with genuine parts and transparent pricing.' }}</div>
      </div>
      <div>
        <div class="footer-col-h">Quick Links</div>
        <div class="footer-links">
          <a href="{{ route('home') }}#services">Our Services</a>
          <a href="{{ route('track.landing') }}">Track Repair</a>
          <a href="{{ route('home') }}#contact">Find Us</a>
          <a href="/login">Admin Panel</a>
        </div>
      </div>
      <div>
        <div class="footer-col-h">Contact</div>
        <div class="footer-ci">
          @if($shopPhone)<div class="footer-c"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $shopPhone }}</div>@endif
          @if($shopEmail)<div class="footer-c"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $shopEmail }}</div>@endif
          @if($shopAddress)<div class="footer-c"><svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ $shopAddress }}</div>@endif
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <div class="footer-copy">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
      <div class="footer-btm-links">
        <a href="{{ route('track.landing') }}">Track Repair</a>
        <a href="/login">Admin</a>
      </div>
    </div>
  </div>
</footer>

@if($shopWhatsapp)
<div class="wa-float">
  <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" rel="noopener" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
  </a>
</div>
@endif

<script>
(function(){
  /* Scroll Progress + Navbar hide-on-scroll-down */
  var sp=document.getElementById('scrollProgress'),nb=document.getElementById('navbar'),t=false,lastY=0;
  function us(){
    var y=window.scrollY,h=document.documentElement.scrollHeight-window.innerHeight;
    if(sp&&h>0)sp.style.width=((y/h)*100)+'%';
    if(nb){
      nb.classList.toggle('scrolled',y>60);
      if(y>120){nb.classList.toggle('nav-hidden',y>lastY);}else{nb.classList.remove('nav-hidden');}
    }
    lastY=y;
  }
  window.addEventListener('scroll',function(){if(!t){requestAnimationFrame(function(){us();t=false;});t=true;}},{passive:true});
  us();
  /* Mobile Menu */
  var mmBtn=document.getElementById('mobileMenuBtn'),nmMobile=document.getElementById('navMobile');
  if(mmBtn&&nmMobile)mmBtn.addEventListener('click',function(){nmMobile.classList.toggle('open');});
})();

function submitTrack(e) {
    e.preventDefault();
    var val = document.getElementById('trackInput').value.trim().toUpperCase();
    if (!val) return;
    window.location.href = '/track/' + encodeURIComponent(val);
}
// Auto-uppercase input
document.getElementById('trackInput').addEventListener('input', function() {
    var pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});
</script>
</body>
</html>
