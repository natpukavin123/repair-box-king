<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $shopName }} &mdash; {{ $shopSlogan }}</title>
<meta name="description" content="{{ $shopName }} — {{ $shopSlogan }}. Professional mobile device repair services. Track your repair online.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;font-size:16px;}
body{font-family:'Inter',system-ui,sans-serif;color:#1e293b;background:#fff;line-height:1.6;}
img{display:block;max-width:100%;}
a{text-decoration:none;}

/* ── Variables ── */
:root{
  --navy:#0f172a;
  --navy2:#1e293b;
  --blue:#2563eb;
  --blue-light:#eff6ff;
  --accent:#f59e0b;
  --green:#10b981;
  --light:#f8fafc;
  --border:#e2e8f0;
  --text:#1e293b;
  --muted:#64748b;
}

/* ── Utility ── */
.container{max-width:1100px;margin:0 auto;padding:0 24px;}
.section{padding:80px 0;}
.section-sm{padding:56px 0;}
.text-center{text-align:center;}
.flex{display:flex;}
.items-center{align-items:center;}
.gap-2{gap:8px;}
.gap-3{gap:12px;}
.gap-4{gap:16px;}

/* ── Navbar ── */
.navbar{background:var(--navy);position:sticky;top:0;z-index:100;border-bottom:1px solid rgba(255,255,255,.07);}
.navbar-inner{display:flex;align-items:center;justify-content:space-between;height:68px;}
.nav-brand{display:flex;align-items:center;gap:12px;}
.nav-logo{width:42px;height:42px;border-radius:10px;overflow:hidden;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.nav-logo img{width:100%;height:100%;object-fit:cover;}
.nav-logo-letters{font-size:11px;font-weight:800;color:#fff;line-height:1.2;text-align:center;}
.nav-shop-name{font-size:18px;font-weight:800;color:#fff;line-height:1;}
.nav-slogan{font-size:11px;color:#94a3b8;margin-top:1px;}
.nav-links{display:flex;align-items:center;gap:4px;}
.nav-link{color:#cbd5e1;font-size:14px;font-weight:500;padding:8px 14px;border-radius:8px;transition:all .15s;}
.nav-link:hover{background:rgba(255,255,255,.1);color:#fff;}
.nav-link.highlight{background:var(--blue);color:#fff;font-weight:600;}
.nav-link.highlight:hover{background:#1d4ed8;}
.nav-login{color:#94a3b8;font-size:13px;padding:7px 14px;border:1px solid rgba(255,255,255,.15);border-radius:8px;transition:all .15s;}
.nav-login:hover{background:rgba(255,255,255,.1);color:#fff;border-color:rgba(255,255,255,.3);}
.mobile-menu-btn{display:none;background:none;border:none;cursor:pointer;color:#fff;padding:8px;}
@media(max-width:768px){
  .nav-links{display:none;}
  .nav-login{display:none;}
  .mobile-menu-btn{display:flex;align-items:center;}
  .nav-mobile{display:block!important;}
}
.nav-mobile{display:none;background:var(--navy2);border-top:1px solid rgba(255,255,255,.05);padding:12px 24px 16px;}
.nav-mobile a{display:block;padding:10px 0;color:#cbd5e1;font-size:15px;font-weight:500;border-bottom:1px solid rgba(255,255,255,.05);}
.nav-mobile a:last-child{border-bottom:none;}

/* ── Hero ── */
.hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 60%,#0f172a 100%);padding:80px 0 72px;position:relative;overflow:hidden;}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 60% at 60% 40%,rgba(37,99,235,.18),transparent);pointer-events:none;}
.hero-grid{display:grid;grid-template-columns:1fr 440px;gap:56px;align-items:center;}
@media(max-width:900px){.hero-grid{grid-template-columns:1fr;gap:40px;text-align:center;}}
.hero-chip{display:inline-flex;align-items:center;gap:6px;background:rgba(37,99,235,.2);border:1px solid rgba(37,99,235,.4);color:#93c5fd;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;padding:5px 14px;border-radius:99px;margin-bottom:18px;}
.hero-title{font-size:clamp(36px,5vw,56px);font-weight:900;color:#fff;line-height:1.1;margin-bottom:16px;letter-spacing:-.5px;}
.hero-title span{color:#60a5fa;}
.hero-sub{font-size:17px;color:#94a3b8;line-height:1.7;margin-bottom:32px;max-width:500px;}
@media(max-width:900px){.hero-sub{margin:0 auto 32px;}}
.hero-btns{display:flex;gap:12px;flex-wrap:wrap;}
@media(max-width:900px){.hero-btns{justify-content:center;}}
.btn{display:inline-flex;align-items:center;gap:8px;padding:14px 28px;border-radius:10px;font-size:15px;font-weight:700;cursor:pointer;border:none;transition:all .15s;white-space:nowrap;}
.btn-primary{background:var(--blue);color:#fff;}
.btn-primary:hover{background:#1d4ed8;transform:translateY(-1px);}
.btn-outline{background:rgba(255,255,255,.07);color:#fff;border:1px solid rgba(255,255,255,.2);}
.btn-outline:hover{background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.4);}
.btn-whatsapp{background:#25d366;color:#fff;}
.btn-whatsapp:hover{background:#22c55e;}

/* Hero card */
.hero-card{background:rgba(255,255,255,.06);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.12);border-radius:20px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.hero-card-title{font-size:13px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#94a3b8;margin-bottom:16px;}
.hero-card-input-row{display:flex;gap:8px;margin-bottom:12px;}
.hero-card-input{flex:1;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);color:#fff;font-family:inherit;font-size:15px;font-weight:600;padding:12px 16px;border-radius:10px;outline:none;letter-spacing:.5px;}
.hero-card-input::placeholder{color:rgba(255,255,255,.35);font-weight:400;letter-spacing:0;}
.hero-card-input:focus{border-color:rgba(59,130,246,.6);background:rgba(255,255,255,.1);}
.hero-card-btn{background:var(--blue);color:#fff;border:none;font-family:inherit;font-size:14px;font-weight:700;padding:12px 20px;border-radius:10px;cursor:pointer;white-space:nowrap;transition:background .15s;}
.hero-card-btn:hover{background:#1d4ed8;}
.hero-card-hint{font-size:12px;color:#64748b;}
.hero-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:20px;}
.hero-stat{text-align:center;padding:14px 8px;background:rgba(255,255,255,.04);border-radius:12px;border:1px solid rgba(255,255,255,.07);}
.hero-stat-num{font-size:22px;font-weight:900;color:#fff;line-height:1;}
.hero-stat-lbl{font-size:11px;color:#94a3b8;margin-top:3px;}

/* ── Trust bar ── */
.trust-bar{background:#f1f5f9;border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:20px 0;}
.trust-items{display:flex;align-items:center;justify-content:center;gap:40px;flex-wrap:wrap;}
.trust-item{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;color:#475569;}
.trust-item svg{color:var(--blue);}

/* ── Section heading ── */
.section-tag{display:inline-block;background:var(--blue-light);color:var(--blue);font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:5px 14px;border-radius:99px;margin-bottom:12px;}
.section-title{font-size:clamp(26px,3.5vw,38px);font-weight:900;color:var(--navy);line-height:1.2;letter-spacing:-.3px;margin-bottom:12px;}
.section-sub{font-size:16px;color:var(--muted);max-width:560px;margin:0 auto;}

/* ── Services ── */
.services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:20px;margin-top:48px;}
.service-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:24px;transition:all .2s;cursor:default;}
.service-card:hover{border-color:#93c5fd;box-shadow:0 8px 30px rgba(37,99,235,.1);transform:translateY(-2px);}
.service-icon{width:52px;height:52px;background:var(--blue-light);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;}
.service-icon img{width:100%;height:100%;object-fit:cover;border-radius:12px;}
.service-icon svg{color:var(--blue);}
.service-name{font-size:15px;font-weight:700;color:var(--navy);margin-bottom:6px;}
.service-desc{font-size:13px;color:var(--muted);line-height:1.6;}
.service-price{font-size:13px;font-weight:700;color:var(--blue);margin-top:10px;}

/* ── Why Us ── */
.why-bg{background:var(--navy);}
.why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:24px;margin-top:48px;}
.why-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:24px;}
.why-icon{width:48px;height:48px;background:rgba(37,99,235,.2);border-radius:12px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:#60a5fa;}
.why-title{font-size:15px;font-weight:700;color:#fff;margin-bottom:6px;}
.why-desc{font-size:13px;color:#94a3b8;line-height:1.6;}

/* ── Track section ── */
.track-section{background:linear-gradient(135deg,#eff6ff,#f0fdf4);}
.track-inner{display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;}
@media(max-width:768px){.track-inner{grid-template-columns:1fr;gap:36px;}}
.track-big-title{font-size:clamp(28px,4vw,42px);font-weight:900;color:var(--navy);line-height:1.15;margin-bottom:14px;}
.track-steps{margin-top:28px;display:flex;flex-direction:column;gap:14px;}
.track-step{display:flex;align-items:flex-start;gap:14px;}
.track-step-num{width:32px;height:32px;border-radius:50%;background:var(--navy);color:#fff;font-size:13px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.track-step-text strong{display:block;font-size:14px;font-weight:700;color:var(--navy);}
.track-step-text span{font-size:13px;color:var(--muted);}
.track-widget{background:#fff;border-radius:20px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.08);border:1px solid var(--border);}
.track-widget-title{font-size:16px;font-weight:800;color:var(--navy);margin-bottom:6px;}
.track-widget-sub{font-size:13px;color:var(--muted);margin-bottom:20px;}
.track-widget-input{width:100%;border:2px solid var(--border);border-radius:10px;font-family:inherit;font-size:15px;font-weight:600;padding:13px 16px;color:var(--navy);outline:none;letter-spacing:.5px;transition:border-color .15s;}
.track-widget-input::placeholder{color:#cbd5e1;font-weight:400;letter-spacing:0;}
.track-widget-input:focus{border-color:var(--blue);}
.track-widget-btn{width:100%;margin-top:12px;background:var(--navy);color:#fff;border:none;font-family:inherit;font-size:15px;font-weight:700;padding:14px;border-radius:10px;cursor:pointer;transition:background .15s;}
.track-widget-btn:hover{background:var(--navy2);}
.track-widget-hint{font-size:12px;color:#94a3b8;text-align:center;margin-top:10px;}

/* ── Contact ── */
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:48px;margin-top:48px;}
@media(max-width:768px){.contact-grid{grid-template-columns:1fr;gap:32px;}}
.contact-items{display:flex;flex-direction:column;gap:24px;}
.contact-item{display:flex;align-items:flex-start;gap:16px;}
.contact-icon{width:48px;height:48px;background:var(--blue-light);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--blue);}
.contact-label{font-size:12px;font-weight:700;letter-spacing:.6px;text-transform:uppercase;color:var(--muted);margin-bottom:4px;}
.contact-value{font-size:15px;font-weight:600;color:var(--navy);}
.contact-value a{color:var(--blue);}
.contact-value a:hover{text-decoration:underline;}
.map-frame{border-radius:20px;overflow:hidden;height:340px;border:1px solid var(--border);background:#f1f5f9;}
.map-frame iframe{width:100%;height:100%;border:none;}
.map-placeholder{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:var(--muted);}
.map-placeholder svg{opacity:.4;}

/* ── CTA Banner ── */
.cta-banner{background:var(--blue);padding:60px 0;text-align:center;}
.cta-banner h2{font-size:clamp(24px,3.5vw,36px);font-weight:900;color:#fff;margin-bottom:10px;}
.cta-banner p{font-size:16px;color:#bfdbfe;margin-bottom:32px;}
.cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
.btn-white{background:#fff;color:var(--blue);font-weight:700;}
.btn-white:hover{background:#eff6ff;}
.btn-outline-white{background:transparent;color:#fff;border:2px solid rgba(255,255,255,.4);}
.btn-outline-white:hover{background:rgba(255,255,255,.1);}

/* ── Footer ── */
.footer{background:var(--navy);color:#94a3b8;padding:48px 0 24px;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:40px;}
@media(max-width:768px){.footer-grid{grid-template-columns:1fr;gap:28px;}}
.footer-brand{display:flex;align-items:center;gap:12px;margin-bottom:14px;}
.footer-brand-logo{width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,.1);overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.footer-brand-logo img{width:100%;height:100%;object-fit:cover;}
.footer-brand-name{font-size:18px;font-weight:800;color:#fff;}
.footer-desc{font-size:13px;line-height:1.7;max-width:300px;}
.footer-col-title{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#fff;margin-bottom:14px;}
.footer-links{display:flex;flex-direction:column;gap:8px;}
.footer-links a{font-size:14px;color:#94a3b8;transition:color .15s;}
.footer-links a:hover{color:#fff;}
.footer-contact-items{display:flex;flex-direction:column;gap:10px;}
.footer-contact-item{font-size:13px;display:flex;align-items:flex-start;gap:8px;}
.footer-contact-item svg{flex-shrink:0;margin-top:2px;color:#64748b;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.07);padding-top:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;}
.footer-copy{font-size:13px;}
.footer-bottom-links{display:flex;gap:20px;}
.footer-bottom-links a{font-size:13px;color:#64748b;}
.footer-bottom-links a:hover{color:#94a3b8;}

/* ── WhatsApp float ── */
.wa-float{position:fixed;bottom:28px;right:28px;z-index:999;}
.wa-float a{width:56px;height:56px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,.4);transition:transform .15s;}
.wa-float a:hover{transform:scale(1.1);}
.wa-float svg{color:#fff;}

@media(max-width:600px){
  .section{padding:56px 0;}
  .hero{padding:56px 0 48px;}
}
</style>
</head>
<body>

{{-- ── NAVBAR ── --}}
<nav class="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="nav-logo">
                @if($shopIcon)
                    <img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}">
                @else
                    <div class="nav-logo-letters">{{ strtoupper(substr($shopName,0,2)) }}</div>
                @endif
            </div>
            <div>
                <div class="nav-shop-name">{{ $shopName }}</div>
                <div class="nav-slogan">{{ $shopSlogan }}</div>
            </div>
        </a>
        <div class="nav-links">
            <a href="#services" class="nav-link">Services</a>
            <a href="#track" class="nav-link">Track Repair</a>
            <a href="#contact" class="nav-link">Contact</a>
            <a href="{{ route('track.landing') }}" class="nav-link highlight">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                Track Now
            </a>
        </div>
        <button class="mobile-menu-btn" onclick="document.getElementById('navMobile').style.display = document.getElementById('navMobile').style.display === 'block' ? 'none' : 'block'">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
    <div id="navMobile" class="nav-mobile" style="display:none;">
        <a href="#services" onclick="document.getElementById('navMobile').style.display='none'">Services</a>
        <a href="#track" onclick="document.getElementById('navMobile').style.display='none'">Track Repair</a>
        <a href="#contact" onclick="document.getElementById('navMobile').style.display='none'">Contact</a>
        <a href="{{ route('track.landing') }}" style="color:#60a5fa;font-weight:700;">Track Your Repair &rarr;</a>
    </div>
</nav>

{{-- ── HERO ── --}}
<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-chip">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Trusted Repair Service
                </div>
                <h1 class="hero-title">
                    Fast & Reliable<br>
                    <span>Mobile Repairs</span>
                </h1>
                <p class="hero-sub">
                    {{ $shopSlogan }}. We fix all major brands — screen replacements, battery issues, water damage, software problems, and more.
                </p>
                <div class="hero-btns">
                    @if($shopWhatsapp)
                    <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" class="btn btn-whatsapp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                        WhatsApp Us
                    </a>
                    @endif
                    @if($shopPhone)
                    <a href="tel:{{ $shopPhone }}" class="btn btn-outline">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $shopPhone }}
                    </a>
                    @endif
                </div>
            </div>

            {{-- Hero Tracking Card --}}
            <div>
                <div class="hero-card">
                    <div class="hero-card-title">Track Your Repair</div>
                    <div class="hero-card-input-row">
                        <input type="text" id="heroTrackInput" class="hero-card-input" placeholder="e.g. TRK-C06C030E" autocomplete="off" spellcheck="false" maxlength="20" style="text-transform:uppercase;">
                        <button class="hero-card-btn" onclick="heroTrack()">Go</button>
                    </div>
                    <div class="hero-card-hint">Enter the Tracking ID from your repair receipt</div>
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="hero-stat-num">30 Min</div>
                            <div class="hero-stat-lbl">Avg Fix Time</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">All</div>
                            <div class="hero-stat-lbl">Brands</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">100%</div>
                            <div class="hero-stat-lbl">Warranty</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── TRUST BAR ── --}}
<div class="trust-bar">
    <div class="container">
        <div class="trust-items">
            <div class="trust-item">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                90-Day Warranty
            </div>
            <div class="trust-item">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Fast Turnaround
            </div>
            <div class="trust-item">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Live Repair Tracking
            </div>
            <div class="trust-item">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Genuine Parts
            </div>
            <div class="trust-item">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Same-Day Service
            </div>
        </div>
    </div>
</div>

{{-- ── SERVICES ── --}}
<section class="section" id="services">
    <div class="container text-center">
        <div class="section-tag">Our Services</div>
        <h2 class="section-title">Everything Your Device Needs</h2>
        <p class="section-sub">Professional repair services for all major smartphone and tablet brands</p>

        <div class="services-grid">
            @if($services && $services->count())
                @foreach($services as $svc)
                <div class="service-card">
                    <div class="service-icon">
                        @if($svc->image)
                            <img src="{{ asset('storage/'.$svc->image) }}" alt="{{ $svc->name }}">
                        @elseif($svc->thumbnail)
                            <img src="{{ asset('storage/'.$svc->thumbnail) }}" alt="{{ $svc->name }}">
                        @else
                            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        @endif
                    </div>
                    <div class="service-name">{{ $svc->name }}</div>
                    @if($svc->description)
                    <div class="service-desc">{{ Str::limit($svc->description, 80) }}</div>
                    @endif
                    @if($svc->default_price > 0)
                    <div class="service-price">Starting ₹{{ number_format($svc->default_price, 0) }}</div>
                    @endif
                </div>
                @endforeach
            @else
                {{-- Default services if none configured --}}
                @php
                $defaultServices = [
                    ['Screen Replacement','Cracked or broken display? We replace screens for all major brands.',  'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['Battery Replacement','Battery draining fast? Get a genuine replacement battery.', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['Water Damage Repair','Dropped in water? Our ultrasonic cleaning can save your phone.', 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707'],
                    ['Charging Port Fix','Phone not charging? We repair or replace faulty charging ports.', 'M13 10V3L4 14h7v7l9-11h-7z'],
                    ['Speaker / Mic Repair','Can\'t hear calls or people can\'t hear you? We fix audio issues.', 'M15.536 8.464a5 5 0 010 7.072M12 6a7 7 0 010 12M8.464 8.464a5 5 0 000 7.072'],
                    ['Software & Unlocking','Forgotten password, factory reset, or IMEI unlock services.', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['Back Glass Repair','Shattered back glass replaced with OEM quality parts.', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['Camera Repair','Blurry photos or broken camera? We restore it to perfect condition.', 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'],
                ];
                @endphp
                @foreach($defaultServices as [$name,$desc,$icon])
                <div class="service-card">
                    <div class="service-icon">
                        <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg>
                    </div>
                    <div class="service-name">{{ $name }}</div>
                    <div class="service-desc">{{ $desc }}</div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

{{-- ── WHY CHOOSE US ── --}}
<section class="section why-bg">
    <div class="container text-center">
        <div class="section-tag" style="background:rgba(37,99,235,.2);color:#93c5fd;">Why Choose Us</div>
        <h2 class="section-title" style="color:#fff;">Your Device Is In Good Hands</h2>
        <p class="section-sub" style="color:#94a3b8;">{{ $shopName }} — trusted by hundreds of customers for quality repairs</p>
        <div class="why-grid">
            <div class="why-card">
                <div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
                <div class="why-title">Genuine Parts</div>
                <div class="why-desc">We use only OEM and high-quality parts so your device performs like new.</div>
            </div>
            <div class="why-card">
                <div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
                <div class="why-title">Quick Turnaround</div>
                <div class="why-desc">Most repairs done in under an hour. We respect your time.</div>
            </div>
            <div class="why-card">
                <div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div>
                <div class="why-title">Live Repair Tracking</div>
                <div class="why-desc">Check your repair status anytime with your unique Tracking ID.</div>
            </div>
            <div class="why-card">
                <div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                <div class="why-title">Transparent Pricing</div>
                <div class="why-desc">No surprises. We give you an estimate before we start any work.</div>
            </div>
        </div>
    </div>
</section>

{{-- ── TRACK SECTION ── --}}
<section class="section track-section" id="track">
    <div class="container">
        <div class="track-inner">
            <div>
                <div class="section-tag">Repair Tracker</div>
                <h2 class="track-big-title">Know Exactly<br>Where Your Device Is</h2>
                <p style="font-size:15px;color:var(--muted);line-height:1.7;margin-bottom:8px;">
                    Every repair gets a unique Tracking ID printed on your receipt. Use it to instantly check the status of your device — no login needed.
                </p>
                <div class="track-steps">
                    <div class="track-step">
                        <div class="track-step-num">1</div>
                        <div class="track-step-text">
                            <strong>Drop off your device</strong>
                            <span>Hand in your device at our shop and get a printed receipt.</span>
                        </div>
                    </div>
                    <div class="track-step">
                        <div class="track-step-num">2</div>
                        <div class="track-step-text">
                            <strong>Note the Tracking ID</strong>
                            <span>Your receipt shows a Tracking ID like <code style="background:#e2e8f0;padding:1px 6px;border-radius:4px;font-size:12px;">TRK-XXXXXXXX</code>.</span>
                        </div>
                    </div>
                    <div class="track-step">
                        <div class="track-step-num">3</div>
                        <div class="track-step-text">
                            <strong>Track anytime</strong>
                            <span>Enter your Tracking ID here or visit /track to see live status.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="track-widget">
                    <div class="track-widget-title">Track Your Repair</div>
                    <div class="track-widget-sub">Enter your Tracking ID from the receipt</div>
                    <input type="text" id="trackWidgetInput" class="track-widget-input" placeholder="e.g. TRK-C06C030E" autocomplete="off" spellcheck="false" maxlength="20" style="text-transform:uppercase;" onkeydown="if(event.key==='Enter')widgetTrack()">
                    <button class="track-widget-btn" onclick="widgetTrack()">
                        Check Status &rarr;
                    </button>
                    <div class="track-widget-hint">Free &bull; No login required &bull; Live status</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CONTACT ── --}}
<section class="section" id="contact">
    <div class="container text-center">
        <div class="section-tag">Get In Touch</div>
        <h2 class="section-title">Find Us &amp; Contact Us</h2>
        <p class="section-sub">We're here to help. Visit us, call us, or drop a message on WhatsApp.</p>
        <div class="contact-grid">
            <div class="contact-items" style="text-align:left;">
                @if($shopAddress)
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <div class="contact-label">Address</div>
                        <div class="contact-value" style="font-size:14px;line-height:1.6;">{{ $shopAddress }}</div>
                    </div>
                </div>
                @endif
                @if($shopPhone)
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div>
                        <div class="contact-label">Phone</div>
                        <div class="contact-value"><a href="tel:{{ $shopPhone }}">{{ $shopPhone }}</a></div>
                    </div>
                </div>
                @endif
                @if($shopEmail)
                <div class="contact-item">
                    <div class="contact-icon">
                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="contact-label">Email</div>
                        <div class="contact-value"><a href="mailto:{{ $shopEmail }}">{{ $shopEmail }}</a></div>
                    </div>
                </div>
                @endif
                @if($shopWhatsapp)
                <div class="contact-item">
                    <div class="contact-icon" style="background:#dcfce7;color:#16a34a;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </div>
                    <div>
                        <div class="contact-label">WhatsApp</div>
                        <div class="contact-value">
                            <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" style="color:#16a34a;">Chat on WhatsApp</a>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Map --}}
            <div class="map-frame">
                @if($shopAddress)
                <iframe
                    src="https://maps.google.com/maps?q={{ urlencode($shopAddress) }}&output=embed&z=15"
                    allowfullscreen
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="Shop Location"
                ></iframe>
                @else
                <div class="map-placeholder">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <p style="font-size:14px;">No address configured yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ── CTA BANNER ── --}}
<div class="cta-banner">
    <div class="container">
        <h2>Ready to Get Your Device Fixed?</h2>
        <p>Visit us today or reach out via WhatsApp. Fast, professional repair service.</p>
        <div class="cta-btns">
            <a href="{{ route('track.landing') }}" class="btn btn-white">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                Track My Repair
            </a>
            @if($shopWhatsapp)
            <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" class="btn btn-outline-white">
                WhatsApp Us Now
            </a>
            @elseif($shopPhone)
            <a href="tel:{{ $shopPhone }}" class="btn btn-outline-white">
                Call {{ $shopPhone }}
            </a>
            @endif
        </div>
    </div>
</div>

{{-- ── FOOTER ── --}}
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <div class="footer-brand-logo">
                        @if($shopIcon)
                            <img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}">
                        @else
                            <span style="font-size:11px;font-weight:800;color:#fff;text-align:center;line-height:1.2;">{{ strtoupper(substr($shopName,0,2)) }}</span>
                        @endif
                    </div>
                    <div class="footer-brand-name">{{ $shopName }}</div>
                </div>
                <div class="footer-desc">{{ $shopSlogan }}. Professional mobile device repair services with genuine parts and transparent pricing.</div>
            </div>
            <div>
                <div class="footer-col-title">Quick Links</div>
                <div class="footer-links">
                    <a href="#services">Our Services</a>
                    <a href="{{ route('track.landing') }}">Track Repair</a>
                    <a href="#contact">Find Us</a>
                </div>
            </div>
            <div>
                <div class="footer-col-title">Contact</div>
                <div class="footer-contact-items">
                    @if($shopPhone)
                    <div class="footer-contact-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $shopPhone }}
                    </div>
                    @endif
                    @if($shopEmail)
                    <div class="footer-contact-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $shopEmail }}
                    </div>
                    @endif
                    @if($shopAddress)
                    <div class="footer-contact-item">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $shopAddress }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
            <div class="footer-bottom-links">
                <a href="{{ route('track.landing') }}">Track Repair</a>
                <a href="/login">Admin</a>
            </div>
        </div>
    </div>
</footer>

{{-- ── WhatsApp Float Button ── --}}
@if($shopWhatsapp)
<div class="wa-float">
    <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" title="Chat on WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
    </a>
</div>
@endif

<script>
function heroTrack() {
    var val = document.getElementById('heroTrackInput').value.trim().toUpperCase();
    if (!val) { document.getElementById('heroTrackInput').focus(); return; }
    window.location.href = '/track/' + encodeURIComponent(val);
}
function widgetTrack() {
    var val = document.getElementById('trackWidgetInput').value.trim().toUpperCase();
    if (!val) { document.getElementById('trackWidgetInput').focus(); return; }
    window.location.href = '/track/' + encodeURIComponent(val);
}
// Auto-uppercase inputs
['heroTrackInput','trackWidgetInput'].forEach(function(id) {
    var el = document.getElementById(id);
    if(el) el.addEventListener('input', function() {
        var p = this.selectionStart;
        this.value = this.value.toUpperCase();
        this.setSelectionRange(p, p);
    });
});
document.getElementById('heroTrackInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') heroTrack();
});
// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(function(a) {
    a.addEventListener('click', function(e) {
        var target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
</body>
</html>
