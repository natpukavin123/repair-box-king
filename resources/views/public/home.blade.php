<!DOCTYPE html>
<html lang="en" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

{{-- ════════════════════════════════════════════
     PRIMARY SEO
════════════════════════════════════════════ --}}
@php
    $pageTitle       = ($landing['seo_title'] ?? '') ?: ($shopName . ' — ' . $shopSlogan . ' | Mobile Repair Shop');
    $pageDesc        = ($landing['seo_description'] ?? '') ?: ($shopName . ' is a professional mobile device repair shop offering screen replacement, battery repair, water damage fix, and more. ' . $shopSlogan . '. Fast turnaround with genuine parts and transparent pricing.');
    $pageKeywords    = ($landing['seo_keywords'] ?? '') ?: ('mobile repair, phone repair, screen replacement, battery replacement, ' . $shopName . ', smartphone repair near me, tablet repair');
    $canonicalUrl    = rtrim(config('app.url', url('/')), '/') . '/';
    $shopLogoUrl     = $shopIcon ? image_url($shopIcon) : '';
    $shopFullPhone   = $shopPhone ? '+' . preg_replace('/\D+/', '', $shopPhone) : '';
    $shopFullWa      = $shopWhatsapp ? preg_replace('/\D+/', '', $shopWhatsapp) : '';
    // Timings display string
    $timingDisplay   = '';
    if ($shopOpenDays && $shopOpenTime && $shopCloseTime) {
        $fmt = fn($t) => \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A');
        $timingDisplay = $shopOpenDays . ': ' . $fmt($shopOpenTime) . ' – ' . $fmt($shopCloseTime);
    } elseif ($shopOpenDays) {
        $timingDisplay = $shopOpenDays;
    }
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDesc }}">
<meta name="keywords" content="{{ $pageKeywords }}">
<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
<meta name="author" content="{{ $shopName }}">

{{-- ── Canonical ── --}}
<link rel="canonical" href="{{ $canonicalUrl }}">

{{-- ── Geo / Local SEO ── --}}
@if($shopAddress)
<meta name="geo.region" content="IN">
<meta name="geo.placename" content="{{ $shopAddress }}">
@endif

{{-- ── Open Graph (Facebook / WhatsApp previews) ── --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $shopName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDesc }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:locale" content="en_IN">
@if($shopLogoUrl)
<meta property="og:image" content="{{ $shopLogoUrl }}">
<meta property="og:image:alt" content="{{ $shopName }} logo">
<meta property="og:image:width" content="512">
<meta property="og:image:height" content="512">
@endif

{{-- ── Twitter / X Card ── --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDesc }}">
@if($shopLogoUrl)
<meta name="twitter:image" content="{{ $shopLogoUrl }}">
<meta name="twitter:image:alt" content="{{ $shopName }}">
@endif

{{-- ── Favicon ── --}}
@php $shopFaviconUrl = (!empty($shopFavicon)) ? image_url($shopFavicon) : ''; @endphp
@if($shopFaviconUrl)
<link rel="icon" type="image/png" href="{{ $shopFaviconUrl }}">
<link rel="shortcut icon" type="image/png" href="{{ $shopFaviconUrl }}">
@elseif($shopLogoUrl)
<link rel="icon" type="image/png" href="{{ $shopLogoUrl }}">
<link rel="shortcut icon" type="image/png" href="{{ $shopLogoUrl }}">
@else
<link rel="icon" href="/favicon.ico" type="image/x-icon">
@endif

{{-- ── Mobile / PWA hints ── --}}
<meta name="theme-color" content="#020617">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="{{ $shopName }}">
@if($shopFaviconUrl)
<link rel="apple-touch-icon" href="{{ $shopFaviconUrl }}">
@elseif($shopLogoUrl)
<link rel="apple-touch-icon" href="{{ $shopLogoUrl }}">
@endif

{{-- ── Performance: preconnect / DNS-prefetch ── --}}
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://fonts.googleapis.com">
<link rel="dns-prefetch" href="https://maps.google.com">

{{-- ════════════════════════════════════════════
     JSON-LD STRUCTURED DATA
════════════════════════════════════════════ --}}
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "LocalBusiness",
      "@id": "{{ $canonicalUrl }}#business",
      "name": "{{ $shopName }}",
      "description": "{{ $pageDesc }}",
      "url": "{{ $canonicalUrl }}",
      "logo": "{{ $shopLogoUrl }}",
      "image": "{{ $shopLogoUrl }}",
      "telephone": "{{ $shopFullPhone }}",
      "email": "{{ $shopEmail }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $shopAddress }}",
        "addressCountry": "IN"
      },
      "sameAs": [
        @if($shopFullWa)"https://wa.me/{{ $shopFullWa }}"@endif
      ],
      "openingHoursSpecification": [
        {
          "@type": "OpeningHoursSpecification",
          "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
          "opens": "{{ $shopOpenTime ?: '09:00' }}",
          "closes": "{{ $shopCloseTime ?: '20:00' }}"
        }
      ],
      "priceRange": "{{ $landing['price_range'] ?? '₹₹' }}",
      "currenciesAccepted": "INR",
      "paymentAccepted": "Cash, UPI, Card",
      "hasMap": "https://maps.google.com/?q={{ urlencode($shopAddress) }}",
      "knowsAbout": ["Screen Replacement","Battery Repair","Water Damage Repair","Charging Port Repair","Software Issues","Camera Repair"],
      "areaServed": {
        "@type": "City",
        "name": "{{ $landing['city'] ?? '' }}"
      }
    },
    {
      "@type": "WebSite",
      "@id": "{{ $canonicalUrl }}#website",
      "name": "{{ $shopName }}",
      "url": "{{ $canonicalUrl }}",
      "description": "{{ $pageDesc }}",
      "publisher": {
        "@id": "{{ $canonicalUrl }}#business"
      },
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "{{ rtrim(config('app.url', url('/')), '/') }}/track?code={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    },
    {
      "@type": "WebPage",
      "@id": "{{ $canonicalUrl }}#webpage",
      "url": "{{ $canonicalUrl }}",
      "name": "{{ $pageTitle }}",
      "description": "{{ $pageDesc }}",
      "isPartOf": { "@id": "{{ $canonicalUrl }}#website" },
      "about": { "@id": "{{ $canonicalUrl }}#business" },
      "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
          {
            "@type": "ListItem",
            "position": 1,
            "name": "Home",
            "item": "{{ $canonicalUrl }}"
          }
        ]
      }
    },
    @if($services && count($services))
    {
      "@type": "ItemList",
      "@id": "{{ $canonicalUrl }}#services",
      "name": "Repair Services",
      "itemListElement": [
        @foreach($services as $idx => $svc)
        {
          "@type": "ListItem",
          "position": {{ $idx + 1 }},
          "item": {
            "@type": "Service",
            "name": "{{ $svc->name }}",
            "description": "{{ $svc->description ?? $svc->name . ' repair service' }}",
            "provider": { "@id": "{{ $canonicalUrl }}#business" }
            @if($svc->default_price > 0)
            ,"offers": {
              "@type": "Offer",
              "price": "{{ $svc->default_price }}",
              "priceCurrency": "INR",
              "availability": "https://schema.org/InStock"
            }
            @endif
          }
        }{{ !$loop->last ? ',' : '' }}
        @endforeach
      ]
    },
    @endif
    {
      "@type": "FAQPage",
      "@id": "{{ $canonicalUrl }}#faq",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "How long does a mobile screen replacement take?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Most screen replacements are completed within 30–60 minutes at {{ $shopName }}. We stock screens for all major brands including Apple, Samsung, OnePlus, Vivo, Oppo, and Realme."
          }
        },
        {
          "@type": "Question",
          "name": "Do you use genuine parts and is pricing transparent?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "{{ $shopName }} uses genuine OEM-quality parts for all repairs. Pricing is transparent with no hidden charges — you get a quote before we begin any work."
          }
        },
        {
          "@type": "Question",
          "name": "What mobile brands do you repair?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "We repair all major brands including Apple iPhone, Samsung, OnePlus, Vivo, Oppo, Realme, Xiaomi/Redmi, Motorola, Nokia, and more."
          }
        },
        {
          "@type": "Question",
          "name": "How can I track my repair status?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "You can track your repair live using your repair code at {{ $canonicalUrl }}track. We also send updates via WhatsApp and email."
          }
        },
        {
          "@type": "Question",
          "name": "Do you use genuine parts?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Yes, {{ $shopName }} uses genuine OEM-quality parts for all repairs to ensure best performance and longevity of your device."
          }
        }
      ]
    }
  ]
}
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

{{-- ── PWA ── --}}
<link rel="manifest" href="/manifest.json">
<meta name="application-name" content="{{ $shopName }}">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;overflow-x:hidden;}
body{font-family:'Inter',system-ui,sans-serif;background:#030712;color:#e2e8f0;overflow-x:hidden;line-height:1.6;}
img{display:block;max-width:100%;}a{text-decoration:none;color:inherit;}
:root{--bg:#030712;--bg2:#080e1c;--bg3:#0f172a;--bl:#3b82f6;--border:rgba(255,255,255,.06);--glass:rgba(255,255,255,.025);}
.container{max-width:1160px;margin:0 auto;padding:0 24px;}.section{padding:96px 0;}.text-center{text-align:center;}
/* LOADER */
.page-loader{position:fixed;inset:0;z-index:10000;background:#030712;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .6s,visibility .6s;}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.pl-ring{width:68px;height:68px;position:relative;flex-shrink:0;}
.pl-ring::before{content:'';position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,#3b82f6,#8b5cf6,#06b6d4,#3b82f6);animation:plSpin 1.2s linear infinite;}
.pl-ring::after{content:'';position:absolute;inset:4px;border-radius:50%;background:#030712;}
.pl-icon{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:1;}
.pl-icon img,.pl-icon svg{width:30px;height:30px;object-fit:contain;color:#60a5fa;}
.pl-name{margin-top:18px;font-size:15px;font-weight:800;color:#fff;letter-spacing:2px;}
.pl-sub{font-size:10px;color:#475569;letter-spacing:4px;text-transform:uppercase;margin-top:4px;}
.pl-bar{width:100px;height:2px;background:rgba(255,255,255,.06);border-radius:10px;margin-top:16px;overflow:hidden;}
.pl-bar-fill{height:100%;background:linear-gradient(90deg,#3b82f6,#8b5cf6);border-radius:10px;animation:plFill 2s ease-out forwards;}
@keyframes plSpin{to{transform:rotate(360deg);}}@keyframes plFill{0%{width:0}100%{width:100%}}
/* SCROLL PROGRESS */
.scroll-progress{position:fixed;top:0;left:0;height:2px;z-index:9999;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#06b6d4);width:0;transition:width .1s linear;}
/* NAV */
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
.nav-link{color:#94a3b8;font-size:14px;font-weight:500;padding:7px 13px;border-radius:8px;transition:all .2s;}
.nav-link:hover{background:rgba(255,255,255,.06);color:#fff;}
.nav-cta{background:linear-gradient(135deg,#2563eb,#7c3aed)!important;color:#fff!important;font-weight:700;border-radius:10px;padding:9px 20px;box-shadow:0 4px 20px rgba(37,99,235,.3);}
.nav-cta:hover{transform:translateY(-1px);box-shadow:0 8px 28px rgba(37,99,235,.45)!important;background:linear-gradient(135deg,#1d4ed8,#6d28d9)!important;}
.mobile-menu-btn{display:none;background:none;border:none;color:#fff;cursor:pointer;padding:6px;}
.nav-mobile{display:none;position:fixed;inset:0;background:rgba(3,7,18,.98);z-index:9999;flex-direction:column;align-items:center;justify-content:center;gap:8px;}
.nav-mobile.open{display:flex;}
.nav-mobile a{font-size:20px;font-weight:600;color:#fff;padding:14px 40px;border-radius:12px;transition:background .2s;width:260px;text-align:center;}
.nav-mobile a:hover{background:var(--glass);}
.nav-mobile-close{position:absolute;top:20px;right:22px;background:none;border:none;color:#fff;cursor:pointer;}
/* HERO */
.hero{position:relative;min-height:100vh;display:flex;align-items:center;overflow:hidden;background:#030712;}
.hero-bg{position:absolute;inset:0;}
.hg1{position:absolute;width:700px;height:700px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.18),transparent 65%);top:-200px;left:-150px;}
.hg2{position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.15),transparent 65%);bottom:-140px;right:-120px;}
.hg3{position:absolute;width:380px;height:380px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.08),transparent 65%);top:35%;right:22%;}
.hero-grid-pat{position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,.025) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.025) 1px,transparent 1px);background-size:60px 60px;mask-image:radial-gradient(ellipse 80% 70% at 50% 50%,black,transparent);}
.hparticle{position:absolute;border-radius:50%;animation:hpFloat linear infinite;}
@keyframes hpFloat{0%{transform:translateY(100vh);opacity:0;}10%{opacity:1;}90%{opacity:.5;}100%{transform:translateY(-5vh);opacity:0;}}
.hero-content{position:relative;z-index:2;width:100%;padding:200px 0 80px;}
.hero-inner{display:grid;grid-template-columns:1fr 1fr;gap:56px;align-items:center;}
.hero-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 16px;border-radius:99px;background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.25);color:#93c5fd;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;margin-bottom:22px;}
.hero-badge-dot{width:6px;height:6px;border-radius:50%;background:#3b82f6;animation:dotBlink 2s ease-in-out infinite;}
@keyframes dotBlink{0%,100%{opacity:1;}50%{opacity:.25;}}
.hero-h1{font-size:clamp(36px,4.8vw,64px);font-weight:900;line-height:1.06;letter-spacing:-2px;color:#fff;margin-bottom:20px;}
.hero-h1 em{font-style:normal;background:linear-gradient(135deg,#60a5fa 0%,#a78bfa 50%,#22d3ee 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200% 200%;animation:gradShift 5s ease-in-out infinite;}
@keyframes gradShift{0%,100%{background-position:0% 50%;}50%{background-position:100% 50%;}}
.hero-p{font-size:16px;color:#94a3b8;line-height:1.78;max-width:480px;margin-bottom:32px;}
.hero-actions{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:40px;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:13px 22px;border-radius:12px;font-size:14px;font-weight:700;cursor:pointer;border:none;transition:all .3s;text-decoration:none;white-space:nowrap;font-family:inherit;}
.btn-primary{background:linear-gradient(135deg,#2563eb,#7c3aed);color:#fff;box-shadow:0 8px 28px rgba(37,99,235,.3);}
.btn-primary:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(37,99,235,.4);}
.btn-wa{background:#25d366;color:#fff;box-shadow:0 8px 24px rgba(37,211,102,.25);}
.btn-wa:hover{background:#22c55e;transform:translateY(-2px);box-shadow:0 12px 30px rgba(37,211,102,.35);}
.btn-ghost{background:rgba(255,255,255,.04);color:#fff;border:1px solid rgba(255,255,255,.12);}
.btn-ghost:hover{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.22);transform:translateY(-2px);}
.hero-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;}
.hero-stat{background:var(--glass);border:1px solid var(--border);border-radius:14px;padding:14px 10px;text-align:center;backdrop-filter:blur(10px);transition:all .3s;}
.hero-stat:hover{border-color:rgba(59,130,246,.22);background:rgba(59,130,246,.05);transform:translateY(-3px);}
.hero-stat-n{font-size:22px;font-weight:900;background:linear-gradient(135deg,#fff,#93c5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.hero-stat-l{font-size:10px;color:#64748b;margin-top:3px;font-weight:500;}
/* HERO VISUAL */
.hero-visual{position:relative;height:560px;display:flex;align-items:center;justify-content:center;}
.hero-ring{position:absolute;border-radius:50%;border:1px solid rgba(59,130,246,.1);animation:ringPulse 4s ease-in-out infinite;}
.hr1{width:240px;height:240px;top:50%;left:50%;transform:translate(-50%,-50%);}
.hr2{width:360px;height:360px;top:50%;left:50%;transform:translate(-50%,-50%);border-color:rgba(139,92,246,.07);animation-delay:.7s;}
.hr3{width:480px;height:480px;top:50%;left:50%;transform:translate(-50%,-50%);border-color:rgba(6,182,212,.04);animation-delay:1.4s;}
@keyframes ringPulse{0%,100%{opacity:.4;scale:1;}50%{opacity:1;scale:1.03;}}
.hero-glow-orb{position:absolute;width:210px;height:210px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.28),rgba(124,58,237,.15) 50%,transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation:orbBreath 4s ease-in-out infinite;}
@keyframes orbBreath{0%,100%{opacity:.7;transform:translate(-50%,-50%) scale(1);}50%{opacity:1;transform:translate(-50%,-50%) scale(1.1);}}
.hero-phone{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:162px;height:330px;border-radius:28px;border:2px solid rgba(255,255,255,.1);background:linear-gradient(145deg,#1a1a2e,#16213e);box-shadow:0 40px 80px rgba(0,0,0,.6),0 0 60px rgba(59,130,246,.14);animation:phoneFloat 6s ease-in-out infinite;overflow:hidden;}
@keyframes phoneFloat{0%,100%{transform:translate(-50%,-50%) translateY(0) rotate(-1.5deg);}50%{transform:translate(-50%,-50%) translateY(-18px) rotate(1.5deg);}}
.phone-scr{position:absolute;inset:10px;border-radius:18px;background:linear-gradient(180deg,#0c1426,#162447);overflow:hidden;}
.phone-notch{position:absolute;top:0;left:50%;transform:translateX(-50%);width:52px;height:16px;background:#080818;border-radius:0 0 9px 9px;z-index:2;}
.phone-st{display:flex;justify-content:space-between;padding:20px 10px 4px;font-size:8px;font-weight:600;color:rgba(255,255,255,.5);}
.phone-apps{display:grid;grid-template-columns:repeat(3,1fr);gap:7px;padding:7px 10px;}
.phone-app{display:flex;flex-direction:column;align-items:center;gap:3px;}
.pa-ic{width:34px;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:15px;}
.pa-lb{font-size:6px;color:rgba(255,255,255,.4);}
.phone-strip{position:absolute;bottom:0;left:0;right:0;padding:6px 12px 10px;background:rgba(0,0,0,.3);border-top:1px solid rgba(255,255,255,.05);font-size:7px;color:#10b981;font-weight:600;display:flex;align-items:center;gap:5px;}
.phone-strip-dot{width:5px;height:5px;border-radius:50%;background:#10b981;animation:dotBlink 2s ease-in-out infinite;}
.float-card{position:absolute;background:rgba(8,14,28,.92);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,.1);border-radius:13px;padding:10px 14px;display:flex;align-items:center;gap:9px;animation:fcFloat 5s ease-in-out infinite;box-shadow:0 16px 50px rgba(0,0,0,.4);white-space:nowrap;}
.fc1{top:13%;right:1%;animation-delay:0s;}
.fc2{bottom:20%;right:-3%;animation-delay:1.8s;}
.fc3{top:60%;left:-4%;animation-delay:3.3s;}
@keyframes fcFloat{0%,100%{transform:translateY(0);}50%{transform:translateY(-10px);}}
.fc-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;}
.fc-txt{font-size:11px;font-weight:600;color:#e2e8f0;}
.fc-sub{font-size:9px;color:#64748b;}
.hero-scroll{position:absolute;bottom:28px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:7px;animation:scrollBounce 2.5s ease-in-out infinite;}
.hero-scroll-line{width:1px;height:36px;background:linear-gradient(to bottom,rgba(59,130,246,.6),transparent);}
.hero-scroll-txt{font-size:9px;color:#475569;letter-spacing:2px;text-transform:uppercase;}
@keyframes scrollBounce{0%,100%{transform:translateX(-50%) translateY(0);}50%{transform:translateX(-50%) translateY(8px);}}
/* TRUST BAR */
.trust-bar{padding:16px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);background:rgba(8,14,28,.6);position:relative;overflow:hidden;}
.trust-bar::before,.trust-bar::after{content:'';position:absolute;top:0;bottom:0;width:80px;z-index:2;}
.trust-bar::before{left:0;background:linear-gradient(to right,#030712,transparent);}
.trust-bar::after{right:0;background:linear-gradient(to left,#030712,transparent);}
.trust-track{display:flex;width:max-content;animation:marquee 28s linear infinite;}
.trust-itm{display:inline-flex;align-items:center;gap:9px;padding:0 28px;font-size:12px;font-weight:600;color:#64748b;white-space:nowrap;}
.trust-itm svg{color:#3b82f6;flex-shrink:0;}
.trust-itm strong{color:#94a3b8;}
@keyframes marquee{0%{transform:translateX(0);}100%{transform:translateX(-50%);}}
/* SECTION HEADER */
.sec-eyebrow{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:99px;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.15);color:#60a5fa;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;margin-bottom:14px;}
.sec-h2{font-size:clamp(26px,3.8vw,44px);font-weight:900;color:#fff;line-height:1.15;letter-spacing:-.5px;margin-bottom:14px;}
.sec-sub{font-size:15px;color:#64748b;line-height:1.7;max-width:580px;}
.text-center .sec-sub{margin:0 auto;}
/* SERVICES */
.services-section{background:linear-gradient(180deg,#030712 0%,#080e1c 50%,#030712 100%);position:relative;overflow:hidden;}
.services-section::before{content:'';position:absolute;top:0;left:50%;transform:translateX(-50%);width:900px;height:1px;background:linear-gradient(90deg,transparent,rgba(59,130,246,.28),transparent);}
.svc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(252px,1fr));gap:18px;margin-top:48px;}
.svc-card{position:relative;background:var(--bg3);border:1px solid var(--border);border-radius:20px;padding:26px 22px;transition:all .4s cubic-bezier(.4,0,.2,1);overflow:hidden;}
.svc-card::before{content:'';position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(59,130,246,.5),transparent);transform:scaleX(0);transition:transform .4s;}
.svc-card::after{content:'';position:absolute;inset:0;background:radial-gradient(circle at var(--mx,50%) var(--my,50%),rgba(59,130,246,.07),transparent 55%);opacity:0;transition:opacity .3s;border-radius:20px;}
.svc-card:hover{border-color:rgba(59,130,246,.28);transform:translateY(-6px);box-shadow:0 20px 60px rgba(37,99,235,.13);}
.svc-card:hover::before{transform:scaleX(1);}
.svc-card:hover::after{opacity:1;}
.svc-ic{width:50px;height:50px;border-radius:14px;background:rgba(37,99,235,.1);border:1px solid rgba(37,99,235,.15);display:flex;align-items:center;justify-content:center;margin-bottom:16px;transition:all .3s;position:relative;z-index:1;}
.svc-ic img{width:100%;height:100%;object-fit:cover;border-radius:14px;}
.svc-ic svg{color:#60a5fa;}
.svc-card:hover .svc-ic{background:rgba(37,99,235,.2);transform:scale(1.08);box-shadow:0 8px 24px rgba(37,99,235,.22);}
.svc-name{font-size:15px;font-weight:700;color:#fff;margin-bottom:7px;position:relative;z-index:1;}
.svc-desc{font-size:12px;color:#64748b;line-height:1.65;position:relative;z-index:1;}
.svc-price{font-size:13px;font-weight:700;color:#60a5fa;margin-top:10px;position:relative;z-index:1;}
/* WHY US */
.why-section{background:#030712;position:relative;}
.why-section::before{content:'';position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:900px;height:1px;background:linear-gradient(90deg,transparent,rgba(139,92,246,.2),transparent);}
.why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(252px,1fr));gap:18px;margin-top:48px;}
.why-card{background:var(--bg2);border:1px solid var(--border);border-radius:18px;padding:26px 22px;transition:all .3s;}
.why-card:hover{border-color:rgba(139,92,246,.22);transform:translateY(-4px);box-shadow:0 16px 50px rgba(124,58,237,.1);}
.why-ic{width:50px;height:50px;border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:16px;}
.why-ic svg{color:#a78bfa;}
.why-ttl{font-size:15px;font-weight:700;color:#fff;margin-bottom:7px;}
.why-desc{font-size:12px;color:#64748b;line-height:1.65;}
/* TRACK */
.track-section{background:linear-gradient(135deg,var(--bg2),var(--bg3));border-top:1px solid var(--border);border-bottom:1px solid var(--border);position:relative;overflow:hidden;}
.track-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 55% 80% at 70% 50%,rgba(37,99,235,.08),transparent);}
.track-inner{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center;position:relative;z-index:1;}
.track-preview{background:var(--glass);border:1px solid var(--border);border-radius:18px;padding:20px;}
.track-preview-lbl{font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#475569;margin-bottom:14px;}
.track-steps{display:flex;flex-direction:column;gap:10px;}
.t-step{display:flex;align-items:center;gap:12px;padding:11px 14px;background:var(--glass);border:1px solid var(--border);border-radius:10px;transition:all .3s;}
.t-step.active{border-color:rgba(37,99,235,.3);background:rgba(37,99,235,.06);}
.t-num{width:24px;height:24px;border-radius:7px;background:linear-gradient(135deg,#2563eb,#7c3aed);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:800;color:#fff;flex-shrink:0;}
.t-txt{font-size:12px;color:#94a3b8;font-weight:500;}
.t-step.active .t-txt{color:#fff;}
.track-input-row{display:flex;gap:10px;margin-top:22px;}
.track-inp{flex:1;padding:13px 16px;background:var(--glass);border:1px solid rgba(255,255,255,.1);border-radius:11px;color:#fff;font-family:inherit;font-size:14px;outline:none;transition:all .3s;}
.track-inp::placeholder{color:#475569;}
.track-inp:focus{border-color:rgba(37,99,235,.5);background:rgba(37,99,235,.05);box-shadow:0 0 0 4px rgba(37,99,235,.1);}
.track-btn{padding:13px 22px;background:linear-gradient(135deg,#2563eb,#7c3aed);border:none;border-radius:11px;color:#fff;font-family:inherit;font-size:14px;font-weight:700;cursor:pointer;transition:all .3s;white-space:nowrap;box-shadow:0 6px 22px rgba(37,99,235,.3);}
.track-btn:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(37,99,235,.4);}
/* CONTACT */
.contact-section{background:#030712;}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:44px;margin-top:46px;}
.contact-cards{display:flex;flex-direction:column;gap:12px;}
.contact-card{display:flex;align-items:flex-start;gap:14px;padding:16px;background:var(--bg2);border:1px solid var(--border);border-radius:15px;transition:all .3s;}
.contact-card:hover{border-color:rgba(37,99,235,.2);background:rgba(37,99,235,.04);transform:translateX(4px);}
.cc-icon{width:42px;height:42px;border-radius:11px;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#60a5fa;}
.cc-label{font-size:10px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#475569;margin-bottom:3px;}
.cc-value{font-size:14px;font-weight:600;color:#e2e8f0;}
.cc-value a{color:#60a5fa;transition:color .2s;}
.cc-value a:hover{color:#93c5fd;}
.map-wrap{border-radius:20px;overflow:hidden;height:100%;min-height:360px;border:1px solid var(--border);background:var(--bg2);}
.map-wrap iframe{width:100%;height:100%;border:none;min-height:360px;filter:brightness(.85) contrast(1.1) saturate(.7);}
.map-placeholder{display:flex;flex-direction:column;align-items:center;justify-content:center;height:360px;gap:12px;color:#475569;}
/* CTA */
.cta-banner{background:linear-gradient(135deg,rgba(37,99,235,.13),rgba(124,58,237,.11));border-top:1px solid rgba(37,99,235,.14);border-bottom:1px solid rgba(37,99,235,.1);padding:76px 0;text-align:center;position:relative;overflow:hidden;}
.cta-banner::before{content:'';position:absolute;top:-80px;left:50%;transform:translateX(-50%);width:500px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.12),transparent 70%);}
.cta-h2{font-size:clamp(26px,3.8vw,46px);font-weight:900;color:#fff;margin-bottom:12px;position:relative;z-index:1;}
.cta-sub{font-size:16px;color:#94a3b8;margin-bottom:32px;position:relative;z-index:1;}
.cta-actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;}
.btn-light{background:#fff;color:#0f172a;}
.btn-light:hover{background:#f1f5f9;transform:translateY(-2px);box-shadow:0 12px 32px rgba(0,0,0,.2);}
.btn-outline-light{background:rgba(255,255,255,.06);color:#fff;border:1px solid rgba(255,255,255,.2);}
.btn-outline-light:hover{background:rgba(255,255,255,.1);transform:translateY(-2px);}
/* FOOTER */
.footer{background:#030712;border-top:1px solid var(--border);padding:52px 0 24px;}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:40px;margin-bottom:42px;}
.footer-brand-row{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
.footer-logo{width:34px;height:34px;border-radius:9px;border:1px solid var(--border);background:var(--glass);display:flex;align-items:center;justify-content:center;overflow:hidden;}
.footer-logo img{width:100%;height:100%;object-fit:cover;}
.footer-shop-name{font-size:15px;font-weight:800;color:#fff;}
.footer-desc{font-size:13px;color:#475569;line-height:1.7;max-width:270px;}
.footer-col-h{font-size:11px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:14px;}
.footer-links{display:flex;flex-direction:column;gap:8px;}
.footer-links a{font-size:13px;color:#475569;transition:color .2s;}
.footer-links a:hover{color:#94a3b8;}
.footer-ci{display:flex;flex-direction:column;gap:8px;}
.footer-c{display:flex;align-items:flex-start;gap:8px;font-size:12px;color:#475569;line-height:1.5;}
.footer-c svg{flex-shrink:0;margin-top:1px;color:#374151;}
.footer-bottom{display:flex;align-items:center;justify-content:space-between;padding-top:20px;border-top:1px solid var(--border);}
.footer-copy{font-size:12px;color:#374151;}
.footer-btm-links{display:flex;gap:18px;}
.footer-btm-links a{font-size:12px;color:#374151;transition:color .2s;}
.footer-btm-links a:hover{color:#64748b;}
/* WA FLOAT */
.wa-float{position:fixed;bottom:24px;right:24px;z-index:500;}
.wa-float a{width:52px;height:52px;border-radius:50%;background:#25d366;display:flex;align-items:center;justify-content:center;box-shadow:0 8px 30px rgba(37,211,102,.4);transition:all .3s;}
.wa-float a:hover{transform:scale(1.1) translateY(-2px);box-shadow:0 14px 40px rgba(37,211,102,.5);}
/* REVEAL */
.reveal{opacity:0;transform:translateY(28px);transition:opacity .7s cubic-bezier(.16,1,.3,1),transform .7s cubic-bezier(.16,1,.3,1);}
.reveal.in{opacity:1;transform:translateY(0);}
.reveal-l{opacity:0;transform:translateX(-36px);transition:opacity .7s cubic-bezier(.16,1,.3,1),transform .7s cubic-bezier(.16,1,.3,1);}
.reveal-l.in{opacity:1;transform:translateX(0);}
.reveal-r{opacity:0;transform:translateX(36px);transition:opacity .7s cubic-bezier(.16,1,.3,1),transform .7s cubic-bezier(.16,1,.3,1);}
.reveal-r.in{opacity:1;transform:translateX(0);}
.reveal-s{opacity:0;transform:scale(.92);transition:opacity .6s cubic-bezier(.16,1,.3,1),transform .6s cubic-bezier(.16,1,.3,1);}
.reveal-s.in{opacity:1;transform:scale(1);}
.d1{transition-delay:.05s;}.d2{transition-delay:.1s;}.d3{transition-delay:.15s;}
.d4{transition-delay:.2s;}.d5{transition-delay:.25s;}.d6{transition-delay:.3s;}
/* RESPONSIVE */
@media(max-width:1024px){
  .hero-inner{grid-template-columns:1fr;text-align:center;}
  .hero-visual{height:320px;}
  .hero-p{margin:0 auto 32px;}
  .hero-actions{justify-content:center;}
  .hero-stats{max-width:350px;margin:0 auto;}
  .track-inner{grid-template-columns:1fr;gap:36px;}
  .contact-grid{grid-template-columns:1fr;}
  .footer-grid{grid-template-columns:1fr 1fr;gap:24px;}
}
@media(max-width:768px){
  .section{padding:60px 0;}
  .nav-links{display:none;}
  .mobile-menu-btn{display:flex;align-items:center;}
  .hero-content{padding:150px 0 60px;}
  .hero-visual{height:270px;}
  .hero-phone{width:126px;height:256px;}
  .svc-grid{grid-template-columns:1fr 1fr;gap:12px;}
  .why-grid{grid-template-columns:1fr 1fr;gap:12px;}
  .footer-grid{grid-template-columns:1fr;}
}
@media(max-width:480px){
  .container{padding:0 16px;}
  .section{padding:44px 0;}
  .hero-content{padding:130px 0 50px;}
  .svc-grid,.why-grid{grid-template-columns:1fr;}
  .track-input-row{flex-direction:column;}
  .cta-actions{flex-direction:column;align-items:center;}
  .footer-bottom{flex-direction:column;gap:8px;text-align:center;}
}

</style>
</head>
<body>
{{-- PAGE LOADER --}}
<div class="page-loader" id="pageLoader">
  <div class="pl-ring">
    <div class="pl-icon">
      @if($shopIcon)<img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">@else<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>@endif
    </div>
  </div>
  <div class="pl-name">{{ Str::upper($shopName) }}</div>
  <div class="pl-sub">Loading</div>
  <div class="pl-bar"><div class="pl-bar-fill"></div></div>
</div>

<div class="scroll-progress" id="scrollProgress"></div>

{{-- NAVBAR --}}
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
      <a href="#services" class="nav-link">Services</a>
      <a href="#why" class="nav-link">Why Us</a>
      <a href="#track" class="nav-link">Track Repair</a>
      <a href="#contact" class="nav-link">Contact</a>
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
  <a href="#services" onclick="document.getElementById('navMobile').classList.remove('open')">Services</a>
  <a href="#why" onclick="document.getElementById('navMobile').classList.remove('open')">Why Us</a>
  <a href="#track" onclick="document.getElementById('navMobile').classList.remove('open')">Track Repair</a>
  <a href="#contact" onclick="document.getElementById('navMobile').classList.remove('open')">Contact</a>
  <a href="{{ route('track.landing') }}" style="color:#60a5fa;">Track My Repair &rarr;</a>
</div>

{{-- HERO --}}
<section class="hero" id="heroSection">
  <div class="hero-bg">
    <div class="hg1"></div><div class="hg2"></div><div class="hg3"></div>
    <div class="hero-grid-pat"></div>
    @for($i=0;$i<16;$i++)
    <div class="hparticle" style="left:{{ rand(2,98) }}%;width:{{ rand(1,3) }}px;height:{{ rand(1,3) }}px;animation-duration:{{ rand(9,18) }}s;animation-delay:-{{ rand(0,12) }}s;background:rgba({{ $i%2==0 ? '59,130,246' : '139,92,246' }},.5);"></div>
    @endfor
  </div>
  <div class="container hero-content">
    <div class="hero-inner">
      <div class="hero-text">
        <div class="hero-badge">
          <span class="hero-badge-dot"></span>
          {{ $landing['hero_chip'] ?? 'Certified Repair Experts' }}
        </div>
        <h1 class="hero-h1">{!! nl2br(e($landing['hero_title'] ?? "Fast & Reliable\nDevice Repairs")) !!}</h1>
        <p class="hero-p">{{ $landing['hero_subtitle'] ?? ($shopSlogan ?: 'Professional screen, battery & water damage repairs with genuine parts and transparent pricing.') }}</p>
        <div class="hero-actions">
          @if($shopWhatsapp)
          <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" class="btn btn-wa">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
            WhatsApp Us
          </a>
          @endif
          @if($shopPhone)
          <a href="tel:{{ $shopPhone }}" class="btn btn-ghost">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            {{ $shopPhone }}
          </a>
          @endif
        </div>
        <div class="hero-stats">
          <div class="hero-stat"><div class="hero-stat-n">{{ $landing['stat1_value'] ?? '30 Min' }}</div><div class="hero-stat-l">{{ $landing['stat1_label'] ?? 'Avg Fix Time' }}</div></div>
          <div class="hero-stat"><div class="hero-stat-n">{{ $landing['stat2_value'] ?? 'All' }}</div><div class="hero-stat-l">{{ $landing['stat2_label'] ?? 'Brands' }}</div></div>
          <div class="hero-stat"><div class="hero-stat-n">{{ $landing['stat3_value'] ?? 'Free' }}</div><div class="hero-stat-l">{{ $landing['stat3_label'] ?? 'Diagnosis' }}</div></div>
        </div>
      </div>
      {{-- HERO DEVICE VISUAL --}}
      <div class="hero-visual">
        <div class="hero-ring hr1"></div>
        <div class="hero-ring hr2"></div>
        <div class="hero-ring hr3"></div>
        <div class="hero-glow-orb"></div>
        <div class="hero-phone">
          <div class="phone-scr">
            <div class="phone-notch"></div>
            <div class="phone-st"><span>9:41</span><span>&#x1F50B; 5G</span></div>
            <div class="phone-apps">
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#22c55e,#16a34a)">&#x1F4F1;</div><div class="pa-lb">Repair</div></div>
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#3b82f6,#2563eb)">&#x1F50D;</div><div class="pa-lb">Track</div></div>
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#f59e0b,#d97706)">&#x26A1;</div><div class="pa-lb">Battery</div></div>
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)">&#x1F527;</div><div class="pa-lb">Service</div></div>
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#06b6d4,#0891b2)">&#x1F310;</div><div class="pa-lb">WiFi</div></div>
              <div class="phone-app"><div class="pa-ic" style="background:linear-gradient(135deg,#ec4899,#db2777)">&#x1F4F7;</div><div class="pa-lb">Camera</div></div>
            </div>
            <div class="phone-strip"><div class="phone-strip-dot"></div>{{ $shopName }}</div>
          </div>
        </div>
        <div class="float-card fc1">
          <div class="fc-icon" style="background:rgba(16,185,129,.15)">&#x2705;</div>
          <div><div class="fc-txt">Repair Complete</div><div class="fc-sub">Fixed in 25 min</div></div>
        </div>
        <div class="float-card fc2">
          <div class="fc-icon" style="background:rgba(59,130,246,.15)">&#x1F4CD;</div>
          <div><div class="fc-txt">Live Tracking</div><div class="fc-sub">Status updated</div></div>
        </div>
        <div class="float-card fc3">
          <div class="fc-icon" style="background:rgba(245,158,11,.15)">&#x2B50;</div>
          <div><div class="fc-txt">Free Diagnosis</div><div class="fc-sub">No fix, no charge</div></div>
        </div>
      </div>
    </div>
  </div>
  <div class="hero-scroll"><div class="hero-scroll-line"></div><div class="hero-scroll-txt">Scroll</div></div>
</section>

{{-- TRUST BAR --}}
<div class="trust-bar">
  <div class="trust-track" id="trustTrack">
    @php $tItems=[['Same-Day','Repairs'],['Free','Diagnosis'],['Live','Tracking'],['Genuine','Parts'],['All Major','Brands'],['Certified','Technicians'],['Easy','Payments'],['On-Time','Delivery']]; @endphp
    @foreach($tItems as $ti)
    <div class="trust-itm"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><strong>{{ $ti[0] }}</strong>{{ $ti[1] }}</div>
    @endforeach
    @foreach($tItems as $ti)
    <div class="trust-itm"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><strong>{{ $ti[0] }}</strong>{{ $ti[1] }}</div>
    @endforeach
  </div>
</div>

{{-- SERVICES --}}
<section class="section services-section" id="services">
  <div class="container text-center">
    <div class="sec-eyebrow reveal">Our Services</div>
    <h2 class="sec-h2 reveal">{{ $landing['services_title'] ?? 'Everything Your Device Needs' }}</h2>
    <p class="sec-sub reveal d1">{{ $landing['services_subtitle'] ?? 'Professional repair services for all major smartphone and tablet brands' }}</p>
    <div class="svc-grid">
      @php $svgW='<path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/>'; @endphp
      @if($services && count($services))
        @foreach($services as $idx => $svc)
        <div class="svc-card reveal d{{ ($idx%6)+1 }}" onmousemove="this.style.setProperty('--mx',(((event.clientX-this.getBoundingClientRect().left)/this.offsetWidth)*100)+'%');this.style.setProperty('--my',(((event.clientY-this.getBoundingClientRect().top)/this.offsetHeight)*100)+'%')">
          <div class="svc-ic">
            @if($svc->image)<img src="{{ image_url($svc->image) }}" alt="{{ $svc->name }}">@elseif($svc->thumbnail)<img src="{{ image_url($svc->thumbnail) }}" alt="{{ $svc->name }}">@else<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">{!! $svgW !!}</svg>@endif
          </div>
          <div class="svc-name">{{ $svc->name }}</div>
          @if($svc->description)<div class="svc-desc">{{ Str::limit($svc->description,90) }}</div>@endif
          @if($svc->default_price>0)<div class="svc-price">Starting {{ $landing['currency']??'₹' }}{{ number_format($svc->default_price,0) }}</div>@endif
        </div>
        @endforeach
      @else
        @php $dsvcs=[['Screen Replacement','High-quality OEM screen for all major brands. Vivid display guaranteed.','M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],['Battery Replacement','Restore full-day battery life with genuine replacement cells.','M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],['Water Damage','Ultrasonic cleaning and micro-soldering to save your soaked device.','M12 3v1m0 16v1m9-9h-1M4 12H3m3.343-5.657l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707'],['Charging Port Fix','Expert port repair or replacement, back in your hands in minutes.','M13 10V3L4 14h7v7l9-11h-7z'],['Camera Repair','Front, rear and wide-angle camera module repair and replacement.','M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'],['Software Fix','Boot loops, virus removal, data backup and OS reinstall — fast.','M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4']]; @endphp
        @foreach($dsvcs as $i=>$ds)
        <div class="svc-card reveal d{{ $i+1 }}" onmousemove="this.style.setProperty('--mx',(((event.clientX-this.getBoundingClientRect().left)/this.offsetWidth)*100)+'%');this.style.setProperty('--my',(((event.clientY-this.getBoundingClientRect().top)/this.offsetHeight)*100)+'%')">
          <div class="svc-ic"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ds[2] }}"/></svg></div>
          <div class="svc-name">{{ $ds[0] }}</div>
          <div class="svc-desc">{{ $ds[1] }}</div>
        </div>
        @endforeach
      @endif
    </div>
  </div>
</section>

{{-- WHY US --}}
<section class="section why-section" id="why">
  <div class="container text-center">
    <div class="sec-eyebrow reveal">Why Choose Us</div>
    <h2 class="sec-h2 reveal">{{ $landing['why_title'] ?? 'The Difference' }}</h2>
    <p class="sec-sub reveal d1">{{ $landing['why_subtitle'] ?? 'Trusted by thousands of customers for fast, reliable and transparent device repairs' }}</p>
    <div class="why-grid">
      @php $whyCards=[
        ['background:rgba(37,99,235,.1)','M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','Transparent Pricing','No hidden charges. You receive a clear quote before any work begins — pay only for what is agreed.'],
        ['background:rgba(245,158,11,.1)','M13 10V3L4 14h7v7l9-11h-7z','Same-Day Service','Most repairs completed within 30–60 minutes. Walk in, walk out with a working device.'],
        ['background:rgba(16,185,129,.1)','M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01','Live Tracking','Track your repair status in real-time. We notify you on WhatsApp and email.'],
        ['background:rgba(139,92,246,.1)','M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z','Genuine Parts','OEM-quality parts only. No compromises on quality or longevity of the repair.'],
      ]; @endphp
      @foreach($whyCards as $i=>$wc)
      <div class="why-card reveal d{{ $i+1 }}">
        <div class="why-ic" style="{{ $wc[0] }};border-radius:14px;">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $wc[1] }}"/></svg>
        </div>
        <div class="why-ttl">{{ $wc[2] }}</div>
        <div class="why-desc">{{ $wc[3] }}</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- TRACK --}}
<section class="section track-section" id="track">
  <div class="container track-inner">
    <div class="track-visual reveal-l">
      <div class="track-preview">
        <div class="track-preview-lbl">Repair Status</div>
        <div class="track-steps">
          @php $tsteps=[['Repair ticket created',true],['Diagnosis complete',true],['Parts ordered & ready',true],['Repair in progress',false],['Quality check & delivery',false]]; @endphp
          @foreach($tsteps as $ts)
          <div class="t-step {{ $ts[1] ? 'active' : '' }}">
            <div class="t-num">{{ $loop->iteration }}</div>
            <div class="t-txt">{{ $ts[0] }}@if($ts[1]) <span style="color:#10b981;font-size:10px;margin-left:6px;">&#x2713;</span>@endif</div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    <div class="track-form reveal-r" style="text-align:left;">
      <div class="sec-eyebrow">Real-Time Tracking</div>
      <h2 class="sec-h2">{{ $landing['track_title'] ?? 'Track Your Repair' }}</h2>
      <p class="sec-sub" style="text-align:left;font-size:14px;">{{ $landing['track_subtitle'] ?? 'Enter your repair code for instant status updates on your device.' }}</p>
      <div class="track-input-row">
        <input class="track-inp" id="trackInput" type="text" placeholder="e.g. REP-0042" aria-label="Repair code">
        <button class="track-btn" onclick="window.doTrack()">Track &rarr;</button>
      </div>
    </div>
  </div>
</section>

{{-- CONTACT --}}
<section class="section contact-section" id="contact">
  <div class="container text-center">
    <div class="sec-eyebrow reveal">Get In Touch</div>
    <h2 class="sec-h2 reveal">{{ $landing['contact_title'] ?? 'Find Us & Contact Us' }}</h2>
    <p class="sec-sub reveal d1">{{ $landing['contact_subtitle'] ?? "We're here to help. Visit us, call us, or chat on WhatsApp." }}</p>
    <div class="contact-grid">
      <div class="contact-cards" style="text-align:left;">
        @if($timingDisplay)
        <div class="contact-card reveal d1">
          <div class="cc-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
          <div><div class="cc-label">Opening Hours</div><div class="cc-value">{{ $timingDisplay }}@if($shopHoliday)<br><span style="font-size:12px;color:#64748b;font-weight:400;">{{ $shopHoliday }}</span>@endif</div></div>
        </div>
        @endif
        @if($shopAddress)
        <div class="contact-card reveal d2">
          <div class="cc-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
          <div><div class="cc-label">Address</div><div class="cc-value" style="font-size:13px;line-height:1.6;">{{ $shopAddress }}</div></div>
        </div>
        @endif
        @if($shopPhone)
        <div class="contact-card reveal d3">
          <div class="cc-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div>
          <div><div class="cc-label">Phone</div><div class="cc-value"><a href="tel:{{ $shopPhone }}">{{ $shopPhone }}</a></div></div>
        </div>
        @endif
        @if($shopEmail)
        <div class="contact-card reveal d4">
          <div class="cc-icon"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div>
          <div><div class="cc-label">Email</div><div class="cc-value"><a href="mailto:{{ $shopEmail }}">{{ $shopEmail }}</a></div></div>
        </div>
        @endif
        @if($shopWhatsapp)
        <div class="contact-card reveal d5">
          <div class="cc-icon" style="background:rgba(37,211,102,.08);border-color:rgba(37,211,102,.15);color:#25d366;"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></div>
          <div><div class="cc-label">WhatsApp</div><div class="cc-value"><a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" style="color:#25d366;">Chat on WhatsApp</a></div></div>
        </div>
        @endif
      </div>
      <div class="map-wrap reveal-r">
        @if(!empty($landing['map_embed']))
        <iframe src="{{ $landing['map_embed'] }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ $shopName }} Location"></iframe>
        @elseif($shopAddress)
        @php $mapZoom=!empty($landing['map_zoom'])?(int)$landing['map_zoom']:15; @endphp
        <iframe src="https://maps.google.com/maps?q={{ urlencode($shopAddress) }}&output=embed&z={{ $mapZoom }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="{{ $shopName }} Location"></iframe>
        @else
        <div class="map-placeholder"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg><p style="font-size:14px;margin-top:8px;color:#475569">Add your address in settings</p></div>
        @endif
      </div>
    </div>
  </div>
</section>

{{-- CTA BANNER --}}
<div class="cta-banner">
  <div class="container">
    <h2 class="cta-h2 reveal">{{ $landing['cta_title'] ?? 'Ready to Get Your Device Fixed?' }}</h2>
    <p class="cta-sub reveal d1">{{ $landing['cta_subtitle'] ?? 'Visit us today or message on WhatsApp. Fast, professional repair guaranteed.' }}</p>
    <div class="cta-actions reveal d2">
      <a href="{{ route('track.landing') }}" class="btn btn-light">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
        Track My Repair
      </a>
      @if($shopWhatsapp)
      <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" class="btn btn-outline-light">WhatsApp Us Now</a>
      @elseif($shopPhone)
      <a href="tel:{{ $shopPhone }}" class="btn btn-outline-light">Call {{ $shopPhone }}</a>
      @endif
    </div>
  </div>
</div>

{{-- FOOTER --}}
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
          <a href="#services">Our Services</a>
          <a href="{{ route('track.landing') }}">Track Repair</a>
          <a href="#contact">Find Us</a>
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
  <a href="https://wa.me/{{ $shopFullWa }}" target="_blank" rel="noopener" title="Chat on WhatsApp">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
  </a>
</div>
@endif

<script>
(function(){
  /* Page Loader */
  window.addEventListener('load',function(){
    setTimeout(function(){var l=document.getElementById('pageLoader');if(l)l.classList.add('hidden');},1600);
  });
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
  /* Scroll Reveal */
  var revEls=document.querySelectorAll('.reveal,.reveal-l,.reveal-r,.reveal-s');
  if('IntersectionObserver' in window){
    var ro=new IntersectionObserver(function(entries){entries.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');ro.unobserve(e.target);}});},{threshold:.07,rootMargin:'0px 0px -24px 0px'});
    revEls.forEach(function(el){ro.observe(el);});
  } else { revEls.forEach(function(el){el.classList.add('in');}); }
  /* Smooth scroll */
  document.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){var tgt=document.querySelector(this.getAttribute('href'));if(tgt){e.preventDefault();tgt.scrollIntoView({behavior:'smooth',block:'start'});}});});
  /* Track Widget */
  window.doTrack=function(){var v=document.getElementById('trackInput').value.trim().toUpperCase();if(!v){document.getElementById('trackInput').focus();return;}window.location.href='/track/'+encodeURIComponent(v);};
  document.getElementById('trackInput').addEventListener('keydown',function(e){if(e.key==='Enter')window.doTrack();});
  /* Service Worker (PWA) */
  if('serviceWorker' in navigator){window.addEventListener('load',function(){navigator.serviceWorker.register('/sw.js').catch(function(){});});}
})();
</script>
</body>
</html>
