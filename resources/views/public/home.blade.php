<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $shopName }} &mdash; {{ $shopSlogan }}</title>
<meta name="description" content="{{ $shopName }} — {{ $shopSlogan }}. Professional mobile device repair services.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;font-size:16px;cursor:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='36' viewBox='0 0 24 36'%3E%3Cpath d='M4 2l-2 6 3-2 2 4 2-4 3 2-2-6z' fill='%23f59e0b' stroke='%23b45309' stroke-width='.6' stroke-linejoin='round'/%3E%3Ccircle cx='4' cy='2' r='1' fill='%23fbbf24'/%3E%3Ccircle cx='7' cy='1.5' r='1.2' fill='%23fbbf24'/%3E%3Ccircle cx='10' cy='2' r='1' fill='%23fbbf24'/%3E%3Ccircle cx='7' cy='.8' r='.6' fill='%23fde68a'/%3E%3Cpath d='M2 8L2 34' stroke='none' fill='none'/%3E%3Cpath d='M2 8l0 22 5.5-5.5 3.5 10.5 3-1-3.5-10.5H18L2 8z' fill='%23fff' stroke='%23111827' stroke-width='1.2' stroke-linejoin='round'/%3E%3C/svg%3E") 2 8, auto;}
body{font-family:'Inter',system-ui,sans-serif;color:#e2e8f0;background:#020617;line-height:1.6;overflow-x:hidden;-webkit-overflow-scrolling:touch;}
html{overflow-x:hidden;}
img{display:block;max-width:100%;}
a{text-decoration:none;color:inherit;cursor:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='36' viewBox='0 0 24 36'%3E%3Cpath d='M4 2l-2 6 3-2 2 4 2-4 3 2-2-6z' fill='%23fbbf24' stroke='%23b45309' stroke-width='.6' stroke-linejoin='round'/%3E%3Ccircle cx='4' cy='2' r='1' fill='%23fde68a'/%3E%3Ccircle cx='7' cy='1.5' r='1.2' fill='%23fde68a'/%3E%3Ccircle cx='10' cy='2' r='1' fill='%23fde68a'/%3E%3Ccircle cx='7' cy='.8' r='.6' fill='%23fff'/%3E%3Cpath d='M2 8l0 18 4-4 3 9 2.5-.8-3-9H15L2 8z' fill='%23fff' stroke='%23111827' stroke-width='1.2' stroke-linejoin='round'/%3E%3Cpath d='M5 25l-1.5 1.5' stroke='%23111827' stroke-width='1' stroke-linecap='round'/%3E%3C/svg%3E") 2 8, pointer;}

:root{
  --navy:#0f172a;--navy2:#1e293b;--blue:#2563eb;--blue-light:#eff6ff;
  --accent:#8b5cf6;--green:#10b981;--light:#f8fafc;--border:#e2e8f0;--text:#1e293b;--muted:#64748b;
  --glow-blue:rgba(59,130,246,.4);--glow-purple:rgba(139,92,246,.35);--glow-cyan:rgba(6,182,212,.3);
}

.container{max-width:1140px;margin:0 auto;padding:0 24px;}
.section{padding:100px 0;position:relative;}
.text-center{text-align:center;}

/* ── Scroll Progress Bar ── */
.scroll-progress{position:fixed;top:0;left:0;height:3px;z-index:10001;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#06b6d4,#f472b6);transition:width .1s linear;border-radius:0 2px 2px 0;}

/* ── Page Loader ── */
.page-loader{position:fixed;inset:0;z-index:10000;background:#020617;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .6s,visibility .6s;}
.page-loader.hidden{opacity:0;visibility:hidden;pointer-events:none;}
.pl-ring{width:80px;height:80px;position:relative;}
.pl-ring-arc{position:absolute;inset:0;border:3px solid transparent;border-radius:50%;}
.pl-ring-arc:nth-child(1){border-top-color:#3b82f6;animation:plSpin .9s linear infinite;}
.pl-ring-arc:nth-child(2){inset:8px;border-right-color:#8b5cf6;animation:plSpin 1.2s linear infinite reverse;}
.pl-ring-arc:nth-child(3){inset:16px;border-bottom-color:#06b6d4;animation:plSpin 1.5s linear infinite;}
.pl-text{margin-top:24px;font-size:12px;color:#475569;letter-spacing:3px;text-transform:uppercase;}
@keyframes plSpin{to{transform:rotate(360deg);}}

/* ── Navbar ── */
.navbar{position:fixed;top:0;left:0;right:0;z-index:1000;padding:16px 0;transition:all .4s cubic-bezier(.4,0,.2,1);}
.navbar.scrolled{background:rgba(2,6,23,.88);backdrop-filter:blur(24px) saturate(1.8);border-bottom:1px solid rgba(255,255,255,.04);padding:8px 0;}
.navbar-inner{display:flex;align-items:center;justify-content:space-between;}
.nav-brand{display:flex;align-items:center;gap:12px;}
.nav-logo{width:42px;height:42px;border-radius:12px;overflow:hidden;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s;}
.nav-logo:hover{transform:scale(1.08);border-color:rgba(59,130,246,.3);}
.nav-logo img{width:100%;height:100%;object-fit:cover;}
.nav-logo-letters{font-size:11px;font-weight:800;color:#fff;line-height:1.2;text-align:center;}
.nav-shop-name{font-size:18px;font-weight:800;color:#fff;line-height:1;}
.nav-slogan{font-size:11px;color:#64748b;margin-top:2px;}
.nav-links{display:flex;align-items:center;gap:6px;}
.nav-link{color:#94a3b8;font-size:14px;font-weight:500;padding:8px 16px;border-radius:10px;transition:all .2s;}
.nav-link:hover{background:rgba(255,255,255,.06);color:#fff;}
.nav-link.highlight{background:var(--blue);color:#fff;font-weight:600;}
.nav-link.highlight:hover{background:#1d4ed8;}
.mobile-menu-btn{display:none;background:none;border:none;cursor:pointer;color:#fff;padding:8px;}
.nav-mobile{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(2,6,23,.98);z-index:9999;flex-direction:column;align-items:center;justify-content:center;gap:8px;}
.nav-mobile.active{display:flex;}
.nav-mobile a{font-size:20px;color:#fff;font-weight:600;padding:16px 32px;border-radius:12px;transition:background .2s;}
.nav-mobile a:hover{background:rgba(255,255,255,.06);}
.nav-mobile-close{position:absolute;top:20px;right:20px;background:none;border:none;color:#fff;cursor:pointer;}

/* ══════════════════════════════════════
   HERO
   ══════════════════════════════════════ */
.hero{position:relative;min-height:100vh;display:flex;align-items:center;overflow:hidden;background:#020617;}
.hero-mesh{position:absolute;inset:0;overflow:hidden;}
.hero-mesh::before{content:'';position:absolute;inset:-50%;width:200%;height:200%;
  background:conic-gradient(from 0deg at 50% 50%,rgba(59,130,246,.12) 0deg,transparent 60deg,rgba(139,92,246,.1) 120deg,transparent 180deg,rgba(6,182,212,.08) 240deg,transparent 300deg,rgba(59,130,246,.12) 360deg);
  animation:meshRotate 20s linear infinite;}
@keyframes meshRotate{to{transform:rotate(360deg);}}
.hero-grid-pattern{position:absolute;inset:0;
  background-image:linear-gradient(rgba(59,130,246,.03) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.03) 1px,transparent 1px);
  background-size:60px 60px;mask-image:radial-gradient(ellipse 60% 60% at 50% 50%,black,transparent);}
.particle{position:absolute;border-radius:50%;pointer-events:none;}
.particle-dot{background:rgba(59,130,246,.5);animation:particleFloat linear infinite;}
.particle-ring{border:1px solid rgba(139,92,246,.2);animation:particleFloat linear infinite;}
@keyframes particleFloat{0%{transform:translateY(0) translateX(0) scale(1);opacity:0;}10%{opacity:1;}90%{opacity:.6;}100%{transform:translateY(-100vh) translateX(40px) scale(.5);opacity:0;}}
.hero-content{position:relative;z-index:2;padding:140px 0 100px;width:100%;}
.hero-flex{display:flex;align-items:center;gap:60px;}
.hero-text{flex:1;min-width:0;}
.hero-3d-area{flex:0 0 520px;position:relative;height:580px;perspective:1200px;}
.hero-chip{display:inline-flex;align-items:center;gap:8px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);color:#93c5fd;font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:7px 18px;border-radius:99px;margin-bottom:24px;animation:chipGlow 3s ease-in-out infinite;}
@keyframes chipGlow{0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,.2),0 0 20px rgba(59,130,246,0);}50%{box-shadow:0 0 0 8px rgba(59,130,246,0),0 0 30px rgba(59,130,246,.15);}}
.hero-title{font-size:clamp(38px,5.5vw,64px);font-weight:900;color:#fff;line-height:1.05;margin-bottom:20px;letter-spacing:-1.5px;}
.hero-title span{background:linear-gradient(135deg,#60a5fa,#a78bfa,#22d3ee);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-size:200% 200%;animation:gradShift 4s ease-in-out infinite;}
@keyframes gradShift{0%,100%{background-position:0% 50%;}50%{background-position:100% 50%;}}
.hero-sub{font-size:17px;color:#94a3b8;line-height:1.75;margin-bottom:36px;max-width:520px;}
.hero-btns{display:flex;gap:14px;flex-wrap:wrap;}
.btn{display:inline-flex;align-items:center;gap:8px;padding:15px 28px;border-radius:14px;font-size:15px;font-weight:700;cursor:pointer;border:none;transition:all .3s cubic-bezier(.4,0,.2,1);white-space:nowrap;}
.btn-whatsapp{background:#25d366;color:#fff;box-shadow:0 8px 24px rgba(37,211,102,.25);}
.btn-whatsapp:hover{background:#22c55e;transform:translateY(-2px);box-shadow:0 12px 32px rgba(37,211,102,.35);}
.btn-outline{background:rgba(255,255,255,.04);color:#fff;border:1px solid rgba(255,255,255,.12);}
.btn-outline:hover{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.25);transform:translateY(-2px);}

/* ── 3D Scene ── */
.scene-3d{position:relative;width:100%;height:100%;transform-style:preserve-3d;animation:sceneIdle 8s ease-in-out infinite;}
@keyframes sceneIdle{0%,100%{transform:rotateX(2deg) rotateY(-3deg);}50%{transform:rotateX(-1deg) rotateY(3deg);}}
.phone-3d{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:200px;height:400px;transform-style:preserve-3d;transition:all 1.2s cubic-bezier(.4,0,.2,1);}
.phone-front{position:absolute;inset:0;background:linear-gradient(145deg,#1a1a2e,#16213e);border-radius:32px;border:2px solid rgba(255,255,255,.08);
  box-shadow:0 40px 80px rgba(0,0,0,.5),0 0 80px rgba(59,130,246,.12),inset 0 1px 0 rgba(255,255,255,.08);overflow:hidden;backface-visibility:hidden;}
.phone-screen{position:absolute;inset:10px;border-radius:22px;overflow:hidden;background:linear-gradient(180deg,#0c1426,#162447);}
.phone-notch{position:absolute;top:0;left:50%;transform:translateX(-50%);width:72px;height:22px;background:#0a0a1a;border-radius:0 0 12px 12px;z-index:2;}
.phone-status{position:relative;z-index:1;padding:28px 14px 6px;display:flex;justify-content:space-between;font-size:10px;font-weight:600;color:rgba(255,255,255,.6);}
.phone-app-grid{padding:14px;display:grid;grid-template-columns:repeat(4,1fr);gap:10px;}
.phone-app{display:flex;flex-direction:column;align-items:center;gap:3px;}
.phone-app-icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;}
.phone-app-label{font-size:7px;color:rgba(255,255,255,.5);}
.phone-crack{position:absolute;inset:0;border-radius:32px;pointer-events:none;opacity:0;transition:opacity .8s;z-index:5;overflow:hidden;}
.phone-crack.active{opacity:1;}
.crack-line{position:absolute;background:rgba(255,255,255,.6);transform-origin:top left;}
.crack-line-1{top:15%;left:20%;width:2px;height:0;transform:rotate(35deg);transition:height .5s .1s;background:linear-gradient(180deg,rgba(255,255,255,.8),transparent);}
.crack-line-2{top:10%;left:55%;width:2px;height:0;transform:rotate(-25deg);transition:height .5s .3s;background:linear-gradient(180deg,rgba(255,255,255,.7),transparent);}
.crack-line-3{top:40%;left:30%;width:1.5px;height:0;transform:rotate(55deg);transition:height .5s .5s;background:linear-gradient(180deg,rgba(255,255,255,.6),transparent);}
.crack-line-4{top:25%;left:65%;width:1px;height:0;transform:rotate(-40deg);transition:height .5s .2s;background:linear-gradient(180deg,rgba(255,255,255,.5),transparent);}
.crack-line-5{top:55%;left:15%;width:1.5px;height:0;transform:rotate(20deg);transition:height .5s .4s;background:linear-gradient(180deg,rgba(255,255,255,.6),transparent);}
.phone-crack.active .crack-line-1{height:180px;}
.phone-crack.active .crack-line-2{height:220px;}
.phone-crack.active .crack-line-3{height:150px;}
.phone-crack.active .crack-line-4{height:170px;}
.phone-crack.active .crack-line-5{height:140px;}
.crack-shard{position:absolute;background:rgba(200,220,255,.15);clip-path:polygon(0 0,100% 30%,80% 100%,10% 80%);opacity:0;transition:all .6s;}
.phone-crack.active .crack-shard{opacity:1;}
.cs1{top:8%;left:25%;width:40px;height:50px;transition-delay:.3s;}
.cs2{top:30%;left:55%;width:35px;height:40px;transition-delay:.5s;clip-path:polygon(20% 0,100% 20%,70% 100%,0 70%);}
.cs3{top:60%;left:35%;width:30px;height:45px;transition-delay:.7s;clip-path:polygon(0 10%,100% 0,90% 100%,15% 85%);}
.phone-crack.active .cs1{transform:translate(-10px,-8px) rotate(-5deg);}
.phone-crack.active .cs2{transform:translate(8px,-5px) rotate(3deg);}
.phone-crack.active .cs3{transform:translate(-5px,8px) rotate(-3deg);}
.screen-glitch{position:absolute;inset:0;pointer-events:none;z-index:4;opacity:0;transition:opacity .3s;}
.screen-glitch.active{opacity:1;animation:glitchFlicker 0.15s linear 3;}
@keyframes glitchFlicker{0%{opacity:1;transform:translateX(0);}25%{opacity:.8;transform:translateX(-2px);}50%{opacity:1;transform:translateX(2px);}75%{opacity:.7;transform:translateX(-1px);}100%{opacity:1;transform:translateX(0);}}
.laptop-3d{position:absolute;right:-20px;top:20px;width:200px;transform-style:preserve-3d;transition:all 1.2s cubic-bezier(.4,0,.2,1);}
.laptop-lid{width:200px;height:130px;background:linear-gradient(145deg,#1a1a2e,#16213e);border-radius:8px 8px 0 0;border:2px solid rgba(255,255,255,.06);overflow:hidden;position:relative;
  box-shadow:0 20px 50px rgba(0,0,0,.4),0 0 40px rgba(139,92,246,.08);transform-origin:bottom center;}
.laptop-screen-inner{position:absolute;inset:6px;border-radius:4px;background:linear-gradient(135deg,#0c1426,#1e3a5f);display:flex;align-items:center;justify-content:center;}
.laptop-screen-content{display:flex;flex-direction:column;align-items:center;gap:4px;opacity:.7;}
.laptop-screen-content .line{height:2px;border-radius:1px;background:rgba(96,165,250,.4);}
.laptop-base{width:220px;height:12px;margin-left:-10px;background:linear-gradient(180deg,rgba(255,255,255,.1),rgba(255,255,255,.03));border-radius:0 0 4px 4px;position:relative;}
.laptop-base::after{content:'';position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:30px;height:3px;background:rgba(255,255,255,.08);border-radius:2px;}
.laptop-crack{position:absolute;inset:0;pointer-events:none;opacity:0;transition:opacity .6s;z-index:3;}
.laptop-crack.active{opacity:1;}
.laptop-crack-line{position:absolute;background:rgba(255,255,255,.5);transform-origin:top left;}
.lcl1{top:25%;left:30%;width:1.5px;height:0;transform:rotate(30deg);transition:height .5s .2s;background:linear-gradient(180deg,rgba(255,255,255,.6),transparent);}
.lcl2{top:20%;left:60%;width:1px;height:0;transform:rotate(-35deg);transition:height .5s .4s;background:linear-gradient(180deg,rgba(255,255,255,.5),transparent);}
.laptop-crack.active .lcl1{height:80px;}
.laptop-crack.active .lcl2{height:70px;}
.tablet-3d{position:absolute;left:-30px;bottom:40px;width:140px;height:200px;transform-style:preserve-3d;transition:all 1.2s cubic-bezier(.4,0,.2,1);}
.tablet-body{width:100%;height:100%;background:linear-gradient(145deg,#1a1a2e,#16213e);border-radius:18px;border:2px solid rgba(255,255,255,.06);overflow:hidden;position:relative;
  box-shadow:0 25px 50px rgba(0,0,0,.35),0 0 40px rgba(6,182,212,.08);}
.tablet-screen{position:absolute;inset:8px;border-radius:10px;background:linear-gradient(180deg,#0c1426,#162447);display:flex;align-items:center;justify-content:center;}
.tablet-screen svg{opacity:.4;}
.float-element{position:absolute;pointer-events:none;z-index:1;}
.globe-3d{width:80px;height:80px;position:relative;}
.globe-sphere{width:80px;height:80px;border-radius:50%;border:1.5px solid rgba(59,130,246,.25);position:relative;animation:globeSpin 12s linear infinite;}
.globe-sphere::before{content:'';position:absolute;inset:0;border-radius:50%;border:1.5px solid rgba(59,130,246,.15);transform:rotateY(60deg);}
.globe-sphere::after{content:'';position:absolute;top:50%;left:0;right:0;height:1.5px;background:rgba(59,130,246,.2);}
.globe-meridian{position:absolute;inset:0;border-radius:50%;border:1px solid rgba(59,130,246,.12);transform:rotateY(30deg);}
.globe-glow{position:absolute;inset:-10px;border-radius:50%;background:radial-gradient(circle,rgba(59,130,246,.12),transparent 70%);animation:pulseGlow 3s ease-in-out infinite;}
@keyframes globeSpin{to{transform:rotateY(360deg);}}
@keyframes pulseGlow{0%,100%{opacity:.6;transform:scale(1);}50%{opacity:1;transform:scale(1.05);}}
.wifi-3d{width:60px;height:60px;position:relative;display:flex;align-items:center;justify-content:center;}
.wifi-arc{position:absolute;border:2px solid transparent;border-top-color:rgba(34,211,238,.4);border-radius:50%;animation:wifiPulse 2s ease-out infinite;}
.wifi-arc:nth-child(1){width:20px;height:20px;animation-delay:0s;}
.wifi-arc:nth-child(2){width:36px;height:36px;animation-delay:.3s;}
.wifi-arc:nth-child(3){width:52px;height:52px;animation-delay:.6s;}
.wifi-dot{width:6px;height:6px;background:#22d3ee;border-radius:50%;position:relative;z-index:2;}
@keyframes wifiPulse{0%{opacity:1;transform:scale(.8);}100%{opacity:0;transform:scale(1.3);}}
.crown-3d{position:relative;width:80px;height:80px;display:flex;align-items:center;justify-content:center;}
.crown-glow{position:absolute;inset:-18px;border-radius:50%;background:radial-gradient(circle,rgba(245,158,11,.2),rgba(251,191,36,.08) 50%,transparent 70%);animation:crownGlow 3s ease-in-out infinite;pointer-events:none;}
@keyframes crownGlow{0%,100%{opacity:.6;transform:scale(1);}50%{opacity:1;transform:scale(1.15);}}
.crown-3d svg.crown-svg{filter:drop-shadow(0 4px 18px rgba(245,158,11,.45)) drop-shadow(0 0 8px rgba(251,191,36,.3));animation:crownFloat 5s ease-in-out infinite;position:relative;z-index:2;}
@keyframes crownFloat{0%,100%{transform:translateY(0) rotate(-3deg) scale(1);}25%{transform:translateY(-10px) rotate(2deg) scale(1.04);}50%{transform:translateY(-6px) rotate(-2deg) scale(1.02);}75%{transform:translateY(-12px) rotate(3deg) scale(1.05);}}
.crown-sparkle{position:absolute;width:4px;height:4px;background:#fbbf24;border-radius:50%;z-index:3;animation:sparkle 2s ease-in-out infinite;}
.crown-sparkle:nth-child(3){top:12px;left:18px;animation-delay:0s;}
.crown-sparkle:nth-child(4){top:8px;left:50%;animation-delay:.5s;width:5px;height:5px;background:#f59e0b;}
.crown-sparkle:nth-child(5){top:14px;right:16px;animation-delay:1s;}
@keyframes sparkle{0%,100%{opacity:0;transform:scale(0);}50%{opacity:1;transform:scale(1.5);}}
.circuit-node{position:absolute;width:6px;height:6px;background:rgba(59,130,246,.3);border-radius:50%;box-shadow:0 0 10px rgba(59,130,246,.2);animation:nodeGlow 3s ease-in-out infinite alternate;}
@keyframes nodeGlow{0%{opacity:.3;box-shadow:0 0 5px rgba(59,130,246,.1);}100%{opacity:1;box-shadow:0 0 15px rgba(59,130,246,.4);}}
.tool-float{animation:toolDrift 7s ease-in-out infinite;}
@keyframes toolDrift{0%,100%{transform:translateY(0) rotate(0deg);}25%{transform:translateY(-12px) rotate(8deg);}75%{transform:translateY(6px) rotate(-5deg);}}

/* Hero Stats */
.hero-stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:40px;}
.hero-stat{text-align:center;padding:16px 12px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:16px;backdrop-filter:blur(10px);transition:all .3s;}
.hero-stat:hover{background:rgba(255,255,255,.06);border-color:rgba(59,130,246,.2);transform:translateY(-3px);box-shadow:0 12px 30px rgba(59,130,246,.1);}
.hero-stat-num{font-size:26px;font-weight:900;color:#fff;line-height:1;background:linear-gradient(135deg,#fff,#93c5fd);-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.hero-stat-lbl{font-size:11px;color:#64748b;margin-top:4px;font-weight:500;}
.scroll-indicator{position:absolute;bottom:32px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:8px;animation:scrollBounce 2s ease-in-out infinite;z-index:4;}
@keyframes scrollBounce{0%,100%{transform:translateX(-50%) translateY(0);}50%{transform:translateX(-50%) translateY(8px);}}
.scroll-indicator span{font-size:11px;color:#475569;letter-spacing:2px;text-transform:uppercase;}
.scroll-mouse{width:24px;height:38px;border:2px solid rgba(255,255,255,.12);border-radius:12px;position:relative;}
.scroll-mouse::after{content:'';position:absolute;top:6px;left:50%;transform:translateX(-50%);width:3px;height:8px;background:#3b82f6;border-radius:3px;animation:mouseScroll 2s ease-in-out infinite;}
@keyframes mouseScroll{0%{opacity:1;transform:translateX(-50%) translateY(0);}100%{opacity:0;transform:translateX(-50%) translateY(12px);}}

/* ══════════════════════════════════════
   6 CREATIVE SCROLL ANIMATION CONCEPTS
   ══════════════════════════════════════ */

/* Concept 1: SLIDE FROM RIGHT with slight rotation — Trust bar / basic */
.anim-slide-r{opacity:0;transform:translateX(80px) rotate(3deg);transition:all .9s cubic-bezier(.16,1,.3,1);}
.anim-slide-r.in-view{opacity:1;transform:translateX(0) rotate(0);}

/* Concept 2: SLIDE FROM LEFT with skew — Services heading */
.anim-slide-l{opacity:0;transform:translateX(-80px) skewX(3deg);transition:all .9s cubic-bezier(.16,1,.3,1);}
.anim-slide-l.in-view{opacity:1;transform:translateX(0) skewX(0);}

/* Concept 3: 3D FLIP UP — Service cards, Why cards */
.anim-flip{opacity:0;transform:perspective(800px) rotateX(25deg) translateY(40px);transform-origin:center bottom;transition:all .8s cubic-bezier(.16,1,.3,1);}
.anim-flip.in-view{opacity:1;transform:perspective(800px) rotateX(0) translateY(0);}

/* Concept 4: ZOOM + SPIN IN — Section tags, stats */
.anim-zoom{opacity:0;transform:scale(.6) rotate(-8deg);transition:all .7s cubic-bezier(.16,1,.3,1);}
.anim-zoom.in-view{opacity:1;transform:scale(1) rotate(0);}

/* Concept 5: BLUR REVEAL + RISE — Contact, Track */
.anim-blur{opacity:0;filter:blur(12px);transform:translateY(30px);transition:all .9s cubic-bezier(.16,1,.3,1);}
.anim-blur.in-view{opacity:1;filter:blur(0);transform:translateY(0);}

/* Concept 6: SWING FROM SIDE (3D perspective door swing) — CTA, cards */
.anim-swing-l{opacity:0;transform:perspective(600px) rotateY(25deg) translateX(-40px);transform-origin:right center;transition:all .9s cubic-bezier(.16,1,.3,1);}
.anim-swing-l.in-view{opacity:1;transform:perspective(600px) rotateY(0) translateX(0);}
.anim-swing-r{opacity:0;transform:perspective(600px) rotateY(-25deg) translateX(40px);transform-origin:left center;transition:all .9s cubic-bezier(.16,1,.3,1);}
.anim-swing-r.in-view{opacity:1;transform:perspective(600px) rotateY(0) translateX(0);}

/* Stagger delays */
.delay-1{transition-delay:.08s;}
.delay-2{transition-delay:.16s;}
.delay-3{transition-delay:.24s;}
.delay-4{transition-delay:.32s;}
.delay-5{transition-delay:.40s;}
.delay-6{transition-delay:.48s;}
.delay-7{transition-delay:.56s;}
.delay-8{transition-delay:.64s;}

/* Section dividers */
.section-divider{height:1px;background:linear-gradient(90deg,transparent,rgba(59,130,246,.15),rgba(139,92,246,.15),transparent);margin:0;border:none;}

/* ══════════════════════════════════════
   TRUST BAR
   ══════════════════════════════════════ */
.trust-bar{background:rgba(255,255,255,.02);border-top:1px solid rgba(255,255,255,.04);border-bottom:1px solid rgba(255,255,255,.04);padding:24px 0;position:relative;overflow:hidden;}
.trust-bar::before{content:'';position:absolute;inset:0;background:linear-gradient(90deg,transparent,rgba(59,130,246,.03),transparent);animation:trustShimmer 8s linear infinite;}
@keyframes trustShimmer{0%{transform:translateX(-100%);}100%{transform:translateX(100%);}}
.trust-items{display:flex;align-items:center;justify-content:center;gap:48px;flex-wrap:wrap;}
.trust-item{display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600;color:#94a3b8;transition:all .3s;}
.trust-item:hover{color:#fff;transform:translateY(-2px);}
.trust-item svg{color:#3b82f6;}

/* ══════════════════════════════════════
   SERVICES — 3D TILT CARDS
   ══════════════════════════════════════ */
.services-section{background:linear-gradient(180deg,#020617 0%,#0f172a 50%,#020617 100%);position:relative;overflow:hidden;}
.services-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 80% 50% at 50% 0%,rgba(59,130,246,.06),transparent);}
.section-tag{display:inline-block;background:rgba(59,130,246,.1);color:#60a5fa;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:6px 18px;border-radius:99px;margin-bottom:16px;border:1px solid rgba(59,130,246,.15);}
.section-title{font-size:clamp(28px,4vw,44px);font-weight:900;line-height:1.15;letter-spacing:-.5px;margin-bottom:14px;}
.section-title-white{color:#fff;}
.section-sub{font-size:16px;max-width:600px;margin:0 auto;line-height:1.7;}
.section-sub-white{color:#94a3b8;}
.services-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:24px;margin-top:48px;}
.service-card{
  background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;
  transition:all .4s cubic-bezier(.2,1,.3,1);position:relative;overflow:hidden;
  transform-style:preserve-3d;perspective:600px;cursor:default;
}
.service-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,rgba(59,130,246,.05),rgba(139,92,246,.05));opacity:0;transition:opacity .3s;border-radius:20px;}
.service-card::after{content:'';position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(circle at var(--mx,50%) var(--my,50%),rgba(59,130,246,.08),transparent 50%);opacity:0;transition:opacity .3s;pointer-events:none;}
.service-card:hover{border-color:rgba(59,130,246,.2);box-shadow:0 24px 60px rgba(59,130,246,.12),0 0 40px rgba(59,130,246,.05);transform:translateY(-8px);}
.service-card:hover::before,.service-card:hover::after{opacity:1;}
.service-icon{width:56px;height:56px;background:rgba(59,130,246,.1);border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;position:relative;z-index:1;transition:all .3s;}
.service-card:hover .service-icon{transform:scale(1.1) translateZ(20px);box-shadow:0 8px 24px rgba(59,130,246,.2);}
.service-icon img{width:100%;height:100%;object-fit:cover;border-radius:16px;}
.service-icon svg{color:#60a5fa;}
.service-name{font-size:16px;font-weight:700;color:#fff;margin-bottom:8px;position:relative;z-index:1;}
.service-desc{font-size:13px;color:#94a3b8;line-height:1.7;position:relative;z-index:1;}
.service-price{font-size:14px;font-weight:700;color:#60a5fa;margin-top:12px;position:relative;z-index:1;}

/* ══════════════════════════════════════
   WHY US
   ══════════════════════════════════════ */
.why-section{background:#020617;position:relative;overflow:hidden;}
.why-section::before{content:'';position:absolute;inset:0;
  background:radial-gradient(ellipse 50% 50% at 20% 80%,rgba(59,130,246,.06),transparent),
              radial-gradient(ellipse 50% 50% at 80% 20%,rgba(139,92,246,.05),transparent);}
.why-float-icons{position:absolute;inset:0;pointer-events:none;overflow:hidden;}
.why-float-icon{position:absolute;opacity:.15;animation:whyFloat 10s ease-in-out infinite alternate;}
@keyframes whyFloat{0%{transform:translateY(0) rotate(0deg);}100%{transform:translateY(-30px) rotate(10deg);}}
.why-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:24px;margin-top:48px;position:relative;z-index:1;}
.why-card{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.05);border-radius:20px;padding:28px;transition:all .4s;position:relative;overflow:hidden;}
.why-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#06b6d4);transform:scaleX(0);transition:transform .4s;transform-origin:left;}
.why-card:hover{background:rgba(255,255,255,.06);border-color:rgba(59,130,246,.15);transform:translateY(-6px);box-shadow:0 20px 50px rgba(59,130,246,.08);}
.why-card:hover::before{transform:scaleX(1);}
.why-icon{width:52px;height:52px;background:rgba(59,130,246,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:18px;color:#60a5fa;transition:all .3s;}
.why-card:hover .why-icon{transform:scale(1.1) rotate(5deg);background:rgba(59,130,246,.15);box-shadow:0 8px 20px rgba(59,130,246,.15);}
.why-title-text{font-size:16px;font-weight:700;color:#fff;margin-bottom:8px;}
.why-desc{font-size:13px;color:#94a3b8;line-height:1.7;}

/* ══════════════════════════════════════
   TRACK
   ══════════════════════════════════════ */
.track-section{background:linear-gradient(180deg,#0f172a,#020617);position:relative;}
.track-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 50% 40% at 70% 50%,rgba(59,130,246,.05),transparent);}
.track-inner{display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center;position:relative;z-index:1;}
.track-big-title{font-size:clamp(30px,4vw,46px);font-weight:900;color:#fff;line-height:1.12;margin-bottom:16px;}
.track-steps{margin-top:32px;display:flex;flex-direction:column;gap:18px;}
.track-step{display:flex;align-items:flex-start;gap:16px;padding:16px;border-radius:16px;transition:all .3s;cursor:default;border:1px solid transparent;}
.track-step:hover{background:rgba(59,130,246,.05);border-color:rgba(59,130,246,.1);}
.track-step-num{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--blue),var(--accent));color:#fff;font-size:14px;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.track-step-text strong{display:block;font-size:15px;font-weight:700;color:#fff;}
.track-step-text span{font-size:13px;color:#94a3b8;line-height:1.6;}
.track-widget{background:rgba(255,255,255,.03);border-radius:24px;padding:36px;border:1px solid rgba(255,255,255,.06);position:relative;overflow:hidden;backdrop-filter:blur(10px);}
.track-widget::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#3b82f6,#8b5cf6,#06b6d4);}
.track-widget-title{font-size:18px;font-weight:800;color:#fff;margin-bottom:8px;margin-top:8px;}
.track-widget-sub{font-size:14px;color:#94a3b8;margin-bottom:24px;}
.track-widget-input{width:100%;border:1px solid rgba(255,255,255,.1);border-radius:14px;font-family:inherit;font-size:16px;font-weight:600;padding:16px 18px;color:#fff;background:rgba(255,255,255,.04);outline:none;letter-spacing:.5px;transition:all .3s;}
.track-widget-input::placeholder{color:#475569;font-weight:400;letter-spacing:0;}
.track-widget-input:focus{border-color:rgba(59,130,246,.4);background:rgba(59,130,246,.05);box-shadow:0 0 0 4px rgba(59,130,246,.08);}
.track-widget-btn{width:100%;margin-top:14px;background:linear-gradient(135deg,var(--blue),var(--accent));color:#fff;border:none;font-family:inherit;font-size:15px;font-weight:700;padding:16px;border-radius:14px;cursor:pointer;transition:all .3s;box-shadow:0 8px 28px rgba(37,99,235,.25);}
.track-widget-btn:hover{transform:translateY(-2px);box-shadow:0 14px 40px rgba(37,99,235,.35);}
.track-widget-hint{font-size:12px;color:#475569;text-align:center;margin-top:12px;}

/* ══════════════════════════════════════
   CONTACT
   ══════════════════════════════════════ */
.contact-section{background:linear-gradient(180deg,#020617,#0f172a);}
.contact-grid{display:grid;grid-template-columns:1fr 1fr;gap:56px;margin-top:48px;}
.contact-items{display:flex;flex-direction:column;gap:20px;}
.contact-item{display:flex;align-items:flex-start;gap:18px;padding:18px;border-radius:16px;transition:all .3s;border:1px solid rgba(255,255,255,.04);background:rgba(255,255,255,.02);}
.contact-item:hover{background:rgba(59,130,246,.05);border-color:rgba(59,130,246,.12);transform:translateX(4px);}
.contact-icon{width:52px;height:52px;background:rgba(59,130,246,.08);border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#60a5fa;}
.contact-label{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:4px;}
.contact-value{font-size:15px;font-weight:600;color:#e2e8f0;}
.contact-value a{color:#60a5fa;transition:color .2s;}
.contact-value a:hover{color:#93c5fd;}
.map-frame{border-radius:20px;overflow:hidden;height:380px;border:1px solid rgba(255,255,255,.06);background:#0f172a;}
.map-frame iframe{width:100%;height:100%;border:none;filter:brightness(.8) contrast(1.1);}
.map-placeholder{width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;color:#64748b;}

/* ══════════════════════════════════════
   CTA
   ══════════════════════════════════════ */
.cta-banner{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0f172a 100%);padding:100px 0;text-align:center;position:relative;overflow:hidden;}
.cta-banner::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 60% at 50% 50%,rgba(59,130,246,.1),transparent);}
.cta-banner::after{content:'';position:absolute;inset:0;
  background-image:linear-gradient(rgba(59,130,246,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.02) 1px,transparent 1px);
  background-size:40px 40px;}
.cta-banner h2{font-size:clamp(26px,4vw,42px);font-weight:900;color:#fff;margin-bottom:12px;position:relative;z-index:1;}
.cta-banner p{font-size:17px;color:#94a3b8;margin-bottom:36px;position:relative;z-index:1;}
.cta-btns{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;position:relative;z-index:1;}
.btn-white{background:#fff;color:var(--blue);font-weight:700;box-shadow:0 8px 28px rgba(0,0,0,.2);}
.btn-white:hover{background:#f0f4ff;transform:translateY(-3px);box-shadow:0 14px 40px rgba(0,0,0,.25);}
.btn-outline-white{background:transparent;color:#fff;border:2px solid rgba(255,255,255,.15);}
.btn-outline-white:hover{background:rgba(255,255,255,.06);border-color:rgba(255,255,255,.3);}

/* ══════════════════════════════════════
   FOOTER
   ══════════════════════════════════════ */
.footer{background:#020617;color:#94a3b8;padding:60px 0 28px;border-top:1px solid rgba(255,255,255,.04);}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:48px;margin-bottom:48px;}
.footer-brand{display:flex;align-items:center;gap:12px;margin-bottom:16px;}
.footer-brand-logo{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.06);overflow:hidden;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.footer-brand-logo img{width:100%;height:100%;object-fit:cover;}
.footer-brand-name{font-size:20px;font-weight:800;color:#fff;}
.footer-desc{font-size:14px;line-height:1.8;max-width:320px;color:#64748b;}
.footer-col-title{font-size:12px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;margin-bottom:18px;}
.footer-links{display:flex;flex-direction:column;gap:10px;}
.footer-links a{font-size:14px;color:#64748b;transition:all .2s;padding:2px 0;}
.footer-links a:hover{color:#fff;transform:translateX(4px);}
.footer-contact-items{display:flex;flex-direction:column;gap:12px;}
.footer-contact-item{font-size:13px;display:flex;align-items:flex-start;gap:10px;color:#64748b;}
.footer-contact-item svg{flex-shrink:0;margin-top:2px;color:#475569;}
.footer-bottom{border-top:1px solid rgba(255,255,255,.04);padding-top:24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;}
.footer-copy{font-size:13px;color:#475569;}
.footer-bottom-links{display:flex;gap:24px;}
.footer-bottom-links a{font-size:13px;color:#475569;transition:color .2s;}
.footer-bottom-links a:hover{color:#94a3b8;}

/* WhatsApp float */
.wa-float{position:fixed;bottom:28px;right:28px;z-index:999;overflow:hidden;}
.wa-float a{width:60px;height:60px;background:#25d366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 16px rgba(37,211,102,.35);transition:all .3s;}
.wa-float a:hover{transform:scale(1.08) translateY(-3px);box-shadow:0 8px 24px rgba(37,211,102,.45);}

/* ══════════════════════════════════════
   TABLET — 768px–1024px
   ══════════════════════════════════════ */
@media(max-width:1024px){
  .container{padding:0 20px;}
  .section{padding:80px 0;}
  .hero-flex{flex-direction:column;text-align:center;gap:40px;}
  .hero-3d-area{flex:none;width:100%;max-width:460px;height:420px;}
  .hero-sub{margin:0 auto 36px;max-width:500px;}
  .hero-btns{justify-content:center;}
  .hero-stats-row{max-width:420px;margin:36px auto 0;}
  .services-grid{grid-template-columns:repeat(2,1fr);gap:20px;}
  .why-grid{grid-template-columns:repeat(2,1fr);gap:20px;}
  .track-inner{gap:48px;}
  .contact-grid{gap:40px;}
  .cta-banner{padding:80px 0;}
  .footer-grid{grid-template-columns:1fr 1fr;gap:36px;}
  .trust-items{gap:24px;}
  .trust-item{font-size:13px;}
  .float-element{transform:scale(.8);}
  .phone-3d{width:170px;height:340px;}
  .laptop-3d{width:170px;right:-10px;}
  .laptop-lid{width:170px;height:110px;}
  .laptop-base{width:190px;margin-left:-10px;}
  .tablet-3d{width:120px;height:170px;left:-20px;}
}

/* ══════════════════════════════════════
   SMALL TABLET / LARGE MOBILE — 600px–768px
   ══════════════════════════════════════ */
@media(max-width:768px){
  .container{padding:0 16px;}
  .section{padding:64px 0;}
  .nav-links{display:none;}
  .mobile-menu-btn{display:flex;align-items:center;}
  .nav-shop-name{font-size:16px;}
  .nav-slogan{font-size:10px;}
  .nav-logo{width:36px;height:36px;border-radius:10px;}
  .hero{min-height:auto;}
  .hero-content{padding:100px 0 60px;}
  .hero-3d-area{max-width:380px;height:380px;}
  .hero-title{letter-spacing:-1px;}
  .hero-sub{font-size:15px;}
  .hero-stats-row{grid-template-columns:repeat(3,1fr);gap:8px;max-width:360px;margin:32px auto 0;}
  .hero-stat{padding:12px 8px;}
  .hero-stat-num{font-size:22px;}
  .hero-stat-lbl{font-size:10px;}
  .btn{padding:13px 22px;font-size:14px;}
  .services-grid{grid-template-columns:repeat(2,1fr);gap:16px;margin-top:36px;}
  .service-card{padding:22px;}
  .service-icon{width:48px;height:48px;border-radius:14px;margin-bottom:14px;}
  .service-name{font-size:14px;}
  .service-desc{font-size:12px;}
  .service-price{font-size:13px;}
  .why-grid{grid-template-columns:repeat(2,1fr);gap:16px;margin-top:36px;}
  .why-card{padding:22px;}
  .why-icon{width:44px;height:44px;border-radius:12px;margin-bottom:14px;}
  .why-title-text{font-size:15px;}
  .why-desc{font-size:12px;}
  .track-inner{grid-template-columns:1fr;gap:32px;}
  .track-big-title{text-align:center;}
  .track-section p{text-align:center;}
  .track-steps{gap:14px;}
  .track-widget{padding:28px;}
  .contact-grid{grid-template-columns:1fr;gap:32px;}
  .map-frame{height:280px;}
  .contact-icon{width:44px;height:44px;border-radius:12px;}
  .cta-banner{padding:64px 0;}
  .cta-banner p{font-size:15px;}
  .footer-grid{grid-template-columns:1fr;gap:28px;}
  .footer-bottom{flex-direction:column;align-items:center;text-align:center;gap:8px;}
  .trust-items{gap:16px 28px;}
  .trust-item{font-size:12px;gap:6px;}
  .trust-item svg{width:14px;height:14px;}
  .section-title{margin-bottom:10px;}
  .section-sub{font-size:14px;}
  .section-tag{font-size:11px;padding:5px 14px;}
  .laptop-3d{width:150px;right:0;top:30px;}
  .laptop-lid{width:150px;height:100px;}
  .laptop-base{width:170px;margin-left:-10px;}
  .tablet-3d{width:100px;height:145px;left:-10px;bottom:30px;}
  .phone-3d{width:150px;height:300px;}
  .float-element{transform:scale(.7);}
  .globe-3d{width:60px;height:60px;}
  .globe-sphere{width:60px;height:60px;}
  .crown-3d{width:60px;height:60px;}
  .crown-3d svg.crown-svg{width:48px;height:48px;}
  .wa-float{bottom:16px;right:16px;}
  .wa-float a{width:52px;height:52px;}
  .wa-float a svg{width:24px;height:24px;}
  .scroll-indicator{display:none;}
}

/* ══════════════════════════════════════
   MOBILE — 480px and below
   ══════════════════════════════════════ */
@media(max-width:480px){
  .container{padding:0 14px;}
  .section{padding:52px 0;}
  .navbar{padding:12px 0;}
  .navbar.scrolled{padding:6px 0;}
  .nav-shop-name{font-size:14px;}
  .nav-slogan{display:none;}
  .nav-logo{width:32px;height:32px;border-radius:8px;}
  .hero-content{padding:80px 0 48px;}
  .hero-3d-area{max-width:300px;height:300px;}
  .hero-chip{font-size:10px;padding:5px 14px;margin-bottom:16px;}
  .hero-title{margin-bottom:14px;letter-spacing:-.5px;}
  .hero-sub{font-size:14px;margin:0 auto 28px;line-height:1.65;}
  .hero-btns{flex-direction:column;align-items:stretch;gap:10px;max-width:280px;margin-left:auto;margin-right:auto;}
  .btn{justify-content:center;padding:14px 20px;font-size:14px;width:auto;}
  .hero-stats-row{grid-template-columns:repeat(3,1fr);gap:6px;max-width:300px;margin:28px auto 0;}
  .hero-stat{padding:10px 6px;border-radius:12px;}
  .hero-stat-num{font-size:18px;}
  .hero-stat-lbl{font-size:9px;}
  .phone-3d{width:120px;height:240px;}
  .phone-front{border-radius:22px;}
  .phone-screen{inset:7px;border-radius:16px;}
  .phone-notch{width:52px;height:16px;border-radius:0 0 8px 8px;}
  .phone-status{padding:20px 10px 4px;font-size:8px;}
  .phone-app-grid{padding:8px;gap:6px;}
  .phone-app-icon{width:28px;height:28px;border-radius:8px;font-size:12px;}
  .phone-app-label{font-size:5.5px;}
  .laptop-3d{width:120px;right:0;top:10px;}
  .laptop-lid{width:120px;height:80px;border-radius:6px 6px 0 0;}
  .laptop-base{width:138px;margin-left:-9px;height:8px;}
  .tablet-3d{width:80px;height:115px;left:-5px;bottom:15px;}
  .tablet-body{border-radius:12px;}
  .tablet-screen{inset:5px;border-radius:8px;}
  .tablet-screen svg{width:28px;height:28px;}
  .float-element{transform:scale(.55);}
  .globe-3d{width:50px;height:50px;}
  .globe-sphere{width:50px;height:50px;}
  .crown-3d{width:50px;height:50px;}
  .crown-3d svg.crown-svg{width:38px;height:38px;}
  .wifi-3d{width:40px;height:40px;}
  .trust-bar{padding:16px 0;}
  .trust-items{gap:10px 18px;justify-content:center;}
  .trust-item{font-size:11px;gap:5px;}
  .trust-item svg{width:12px;height:12px;}
  .services-grid{grid-template-columns:1fr 1fr;gap:12px;margin-top:28px;}
  .service-card{padding:18px;border-radius:16px;}
  .service-icon{width:42px;height:42px;border-radius:12px;margin-bottom:12px;}
  .service-name{font-size:13px;margin-bottom:4px;}
  .service-desc{font-size:11px;line-height:1.5;}
  .service-price{font-size:12px;margin-top:8px;}
  .why-grid{grid-template-columns:1fr;gap:14px;margin-top:28px;}
  .why-card{padding:22px;display:flex;align-items:flex-start;gap:16px;flex-direction:row;}
  .why-icon{width:42px;height:42px;border-radius:12px;margin-bottom:0;flex-shrink:0;}
  .why-title-text{font-size:14px;margin-bottom:4px;}
  .why-desc{font-size:12px;}
  .track-big-title{text-align:center;}
  .track-section .section-tag{display:block;text-align:center;}
  .track-section p{text-align:center;}
  .track-steps{gap:10px;}
  .track-step{padding:12px;gap:12px;}
  .track-step-num{width:30px;height:30px;font-size:12px;}
  .track-step-text strong{font-size:13px;}
  .track-step-text span{font-size:12px;}
  .track-widget{padding:22px;border-radius:18px;}
  .track-widget-title{font-size:16px;}
  .track-widget-sub{font-size:13px;margin-bottom:18px;}
  .track-widget-input{padding:14px;font-size:14px;border-radius:12px;}
  .track-widget-btn{padding:14px;font-size:14px;border-radius:12px;}
  .contact-grid{margin-top:32px;gap:28px;}
  .contact-item{padding:14px;gap:14px;border-radius:14px;}
  .contact-icon{width:40px;height:40px;border-radius:12px;}
  .contact-label{font-size:11px;}
  .contact-value{font-size:13px;}
  .map-frame{height:220px;border-radius:16px;}
  .cta-banner{padding:52px 0;}
  .cta-banner p{font-size:14px;margin-bottom:28px;}
  .cta-btns{flex-direction:column;align-items:center;gap:10px;}
  .cta-btns .btn{width:100%;max-width:300px;}
  .footer{padding:40px 0 20px;}
  .footer-grid{gap:24px;margin-bottom:32px;}
  .footer-brand-name{font-size:18px;}
  .footer-desc{font-size:13px;}
  .footer-col-title{font-size:11px;margin-bottom:14px;}
  .footer-links a{font-size:13px;}
  .footer-contact-item{font-size:12px;}
  .footer-bottom{gap:6px;}
  .footer-copy{font-size:12px;}
  .footer-bottom-links a{font-size:12px;}
  .footer-bottom-links{gap:16px;}
  .wa-float{bottom:14px;right:14px;}
  .wa-float a{width:48px;height:48px;}
  .wa-float a svg{width:22px;height:22px;}
  .nav-mobile a{font-size:18px;padding:14px 28px;}
  /* Reduce animation transforms on mobile for smoother perf */
  .anim-slide-r,.anim-slide-l{transform:translateX(40px);opacity:0;}
  .anim-slide-l{transform:translateX(-40px);}
  .anim-flip{transform:perspective(800px) rotateX(12deg) translateY(20px);}
  .anim-swing-l{transform:perspective(600px) rotateY(12deg) translateX(-20px);}
  .anim-swing-r{transform:perspective(600px) rotateY(-12deg) translateX(20px);}
}

/* ══════════════════════════════════════
   EXTRA SMALL — 360px and below
   ══════════════════════════════════════ */
@media(max-width:360px){
  .container{padding:0 12px;}
  .hero-3d-area{max-width:260px;height:260px;}
  .phone-3d{width:100px;height:200px;}
  .phone-front{border-radius:18px;}
  .phone-screen{inset:5px;border-radius:12px;}
  .phone-notch{width:42px;height:14px;}
  .phone-app-grid{grid-template-columns:repeat(3,1fr);gap:5px;padding:6px;}
  .phone-app-icon{width:24px;height:24px;font-size:10px;}
  .laptop-3d{width:100px;}
  .laptop-lid{width:100px;height:65px;}
  .laptop-base{width:115px;}
  .tablet-3d{width:65px;height:95px;}
  .hero-stat-num{font-size:16px;}
  .hero-stat-lbl{font-size:8px;}
  .hero-stat{padding:8px 4px;}
  .services-grid{grid-template-columns:1fr;gap:12px;}
  .trust-items{flex-direction:column;gap:8px;align-items:flex-start;padding-left:28px;}
  .cta-btns .btn{max-width:260px;}
}
</style>
</head>
<body>

<!-- Scroll Progress Bar -->
<div class="scroll-progress" id="scrollProgress"></div>

<!-- Page Loader -->
<div class="page-loader" id="pageLoader">
    <div class="pl-ring"><div class="pl-ring-arc"></div><div class="pl-ring-arc"></div><div class="pl-ring-arc"></div></div>
    <div class="pl-text">Loading</div>
</div>

<!-- Navbar -->
<nav class="navbar" id="navbar">
    <div class="container navbar-inner">
        <a href="{{ route('home') }}" class="nav-brand">
            <div class="nav-logo">
                @if($shopIcon)<img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}">
                @else<div class="nav-logo-letters">{{ strtoupper(substr($shopName,0,2)) }}</div>@endif
            </div>
            <div><div class="nav-shop-name">{{ $shopName }}</div><div class="nav-slogan">{{ $shopSlogan }}</div></div>
        </a>
        <div class="nav-links">
            <a href="#services" class="nav-link">Services</a>
            <a href="#why" class="nav-link">Why Us</a>
            <a href="#track" class="nav-link">Track Repair</a>
            <a href="#contact" class="nav-link">Contact</a>
            <a href="{{ route('track.landing') }}" class="nav-link highlight">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                Track Now
            </a>
        </div>
        <button class="mobile-menu-btn" onclick="document.getElementById('navMobile').classList.toggle('active')">
            <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>
</nav>
<div id="navMobile" class="nav-mobile">
    <button class="nav-mobile-close" onclick="document.getElementById('navMobile').classList.remove('active')">
        <svg width="28" height="28" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
    <a href="#services" onclick="document.getElementById('navMobile').classList.remove('active')">Services</a>
    <a href="#why" onclick="document.getElementById('navMobile').classList.remove('active')">Why Us</a>
    <a href="#track" onclick="document.getElementById('navMobile').classList.remove('active')">Track Repair</a>
    <a href="#contact" onclick="document.getElementById('navMobile').classList.remove('active')">Contact</a>
    <a href="{{ route('track.landing') }}" style="color:#60a5fa;">Track Your Repair &rarr;</a>
</div>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero" id="heroSection">
    <div class="hero-mesh">
        <div class="hero-grid-pattern"></div>
        @for($i = 0; $i < 25; $i++)
        <div class="particle {{ $i % 3 === 0 ? 'particle-ring' : 'particle-dot' }}" style="left:{{ rand(2,98) }}%;width:{{ rand(3,8) }}px;height:{{ rand(3,8) }}px;animation-duration:{{ rand(10,22) }}s;animation-delay:{{ rand(0,12) }}s;top:{{ rand(60,100) }}%;"></div>
        @endfor
        @for($i = 0; $i < 8; $i++)
        <div class="circuit-node" style="left:{{ rand(5,95) }}%;top:{{ rand(10,90) }}%;animation-delay:{{ $i * 0.7 }}s;"></div>
        @endfor
    </div>
    <div class="container hero-content">
        <div class="hero-flex">
            <div class="hero-text">
                <div class="hero-chip">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $landing['hero_chip'] ?? 'Trusted Repair Service' }}
                </div>
                <h1 class="hero-title">
                    {!! $landing['hero_title'] ?? 'Fast & Reliable<br><span>Device Repairs</span>' !!}
                </h1>
                <p class="hero-sub">
                    {{ $landing['hero_subtitle'] ?? ($shopSlogan . '. We fix all major brands — screen replacements, battery issues, water damage, software problems, and more.') }}
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
                <div class="hero-stats-row">
                    <div class="hero-stat"><div class="hero-stat-num">{{ $landing['stat1_value'] ?? '30 Min' }}</div><div class="hero-stat-lbl">{{ $landing['stat1_label'] ?? 'Avg Fix Time' }}</div></div>
                    <div class="hero-stat"><div class="hero-stat-num">{{ $landing['stat2_value'] ?? 'All' }}</div><div class="hero-stat-lbl">{{ $landing['stat2_label'] ?? 'Brands' }}</div></div>
                    <div class="hero-stat"><div class="hero-stat-num">{{ $landing['stat3_value'] ?? '100%' }}</div><div class="hero-stat-lbl">{{ $landing['stat3_label'] ?? 'Warranty' }}</div></div>
                </div>
            </div>

            <!-- 3D DEVICE SCENE -->
            <div class="hero-3d-area" id="hero3dArea">
                <div class="scene-3d" id="scene3d">
                    <div class="phone-3d" id="phone3d">
                        <div class="phone-front">
                            <div class="phone-screen">
                                <div class="phone-notch"></div>
                                <div class="phone-status"><span>9:41</span><span>&#x1F4F6; &#x1F50B;</span></div>
                                <div class="phone-app-grid">
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">&#x1F4F1;</div><div class="phone-app-label">Repair</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">&#x1F50D;</div><div class="phone-app-label">Track</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">&#x26A1;</div><div class="phone-app-label">Battery</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">&#x1F527;</div><div class="phone-app-label">Service</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#06b6d4,#0891b2);">&#x1F310;</div><div class="phone-app-label">WiFi</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#ec4899,#db2777);">&#x1F4F7;</div><div class="phone-app-label">Camera</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">&#x1F3AE;</div><div class="phone-app-label">Games</div></div>
                                    <div class="phone-app"><div class="phone-app-icon" style="background:linear-gradient(135deg,#14b8a6,#0d9488);">&#x1F4AC;</div><div class="phone-app-label">Chat</div></div>
                                </div>
                            </div>
                            <div class="phone-crack" id="phoneCrack">
                                <div class="crack-line crack-line-1"></div>
                                <div class="crack-line crack-line-2"></div>
                                <div class="crack-line crack-line-3"></div>
                                <div class="crack-line crack-line-4"></div>
                                <div class="crack-line crack-line-5"></div>
                                <div class="crack-shard cs1"></div>
                                <div class="crack-shard cs2"></div>
                                <div class="crack-shard cs3"></div>
                            </div>
                            <div class="screen-glitch" id="screenGlitch"></div>
                        </div>
                    </div>
                    <div class="laptop-3d" id="laptop3d">
                        <div class="laptop-lid">
                            <div class="laptop-screen-inner">
                                <div class="laptop-screen-content">
                                    <div class="line" style="width:50px;"></div>
                                    <div class="line" style="width:35px;"></div>
                                    <div class="line" style="width:42px;"></div>
                                </div>
                            </div>
                            <div class="laptop-crack" id="laptopCrack">
                                <div class="laptop-crack-line lcl1"></div>
                                <div class="laptop-crack-line lcl2"></div>
                            </div>
                        </div>
                        <div class="laptop-base"></div>
                    </div>
                    <div class="tablet-3d" id="tablet3d">
                        <div class="tablet-body">
                            <div class="tablet-screen">
                                <svg width="40" height="40" fill="none" stroke="rgba(96,165,250,.5)" stroke-width="1.5" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="float-element" style="top:5%;right:10%;">
                        <div class="globe-3d"><div class="globe-glow"></div><div class="globe-sphere"><div class="globe-meridian"></div></div></div>
                    </div>
                    <div class="float-element" style="top:60%;right:-10%;">
                        <div class="wifi-3d"><div class="wifi-arc"></div><div class="wifi-arc"></div><div class="wifi-arc"></div><div class="wifi-dot"></div></div>
                    </div>
                    <div class="float-element" style="top:-5%;left:15%;">
                        <div class="crown-3d">
                            <div class="crown-glow"></div>
                            <svg class="crown-svg" width="64" height="64" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Crown body -->
                                <path d="M8 50h48l-4-28-10 12-8-16-8 16-10-12-4 28z" fill="url(#crownGrad)" stroke="#d97706" stroke-width="1.5" stroke-linejoin="round"/>
                                <!-- Crown base band -->
                                <rect x="8" y="48" width="48" height="6" rx="2" fill="url(#bandGrad)" stroke="#b45309" stroke-width="1"/>
                                <!-- Center gem -->
                                <circle cx="32" cy="51" r="3" fill="#ef4444" stroke="#fbbf24" stroke-width="1"/>
                                <circle cx="32" cy="51" r="1.2" fill="#fca5a5" opacity=".7"/>
                                <!-- Side gems -->
                                <circle cx="20" cy="51" r="2" fill="#3b82f6" stroke="#fbbf24" stroke-width=".8"/>
                                <circle cx="44" cy="51" r="2" fill="#3b82f6" stroke="#fbbf24" stroke-width=".8"/>
                                <!-- Crown tips -->
                                <circle cx="14" cy="22" r="3" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
                                <circle cx="26" cy="18" r="3" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
                                <circle cx="38" cy="18" r="3" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
                                <circle cx="50" cy="22" r="3" fill="#fbbf24" stroke="#d97706" stroke-width="1"/>
                                <circle cx="32" cy="14" r="4" fill="#f59e0b" stroke="#b45309" stroke-width="1"/>
                                <!-- Top gem on center tip -->
                                <circle cx="32" cy="14" r="2" fill="#fde68a"/>
                                <!-- Shine lines -->
                                <line x1="32" y1="6" x2="32" y2="9" stroke="#fde68a" stroke-width="1.5" stroke-linecap="round" opacity=".8"/>
                                <line x1="27" y1="8" x2="29" y2="10" stroke="#fde68a" stroke-width="1" stroke-linecap="round" opacity=".6"/>
                                <line x1="37" y1="8" x2="35" y2="10" stroke="#fde68a" stroke-width="1" stroke-linecap="round" opacity=".6"/>
                                <defs>
                                    <linearGradient id="crownGrad" x1="32" y1="18" x2="32" y2="50" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#fbbf24"/>
                                        <stop offset="50%" stop-color="#f59e0b"/>
                                        <stop offset="100%" stop-color="#d97706"/>
                                    </linearGradient>
                                    <linearGradient id="bandGrad" x1="8" y1="48" x2="56" y2="54" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#d97706"/>
                                        <stop offset="50%" stop-color="#f59e0b"/>
                                        <stop offset="100%" stop-color="#d97706"/>
                                    </linearGradient>
                                </defs>
                            </svg>
                            <div class="crown-sparkle"></div>
                            <div class="crown-sparkle"></div>
                            <div class="crown-sparkle"></div>
                        </div>
                    </div>
                    <div class="float-element tool-float" style="bottom:5%;right:0;">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#60a5fa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div class="float-element tool-float" style="bottom:30%;left:-20px;animation-delay:-3s;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L8 6h3v8H8l4 4 4-4h-3V6h3L12 2z"/><path d="M12 18v4"/></svg>
                    </div>
                    <div class="float-element" style="top:40%;left:-35px;">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="rgba(34,211,238,.4)" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M1 9h3M1 15h3M20 9h3M20 15h3"/><circle cx="9" cy="9" r="1" fill="rgba(34,211,238,.4)"/><circle cx="15" cy="15" r="1" fill="rgba(34,211,238,.4)"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="scroll-indicator"><div class="scroll-mouse"></div><span>Scroll to explore</span></div>
</section>

<!-- ═══════════════ TRUST BAR ═══════════════ -->
<!-- CONCEPT 1: Slide from Right with rotation -->
<div class="trust-bar">
    <div class="container">
        <div class="trust-items">
            <div class="trust-item anim-slide-r delay-1"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>90-Day Warranty</div>
            <div class="trust-item anim-slide-r delay-2"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>Fast Turnaround</div>
            <div class="trust-item anim-slide-r delay-3"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>Live Tracking</div>
            <div class="trust-item anim-slide-r delay-4"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>Genuine Parts</div>
            <div class="trust-item anim-slide-r delay-5"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>Same-Day Fix</div>
        </div>
    </div>
</div>

<hr class="section-divider">

<!-- ═══════════════ SERVICES ═══════════════ -->
<!-- CONCEPT 2: Heading slides from Left with skew, CONCEPT 3: Cards 3D Flip Up -->
<section class="section services-section" id="services">
    <div class="container text-center">
        <div class="section-tag anim-zoom">Our Services</div>
        <h2 class="section-title section-title-white anim-slide-l">{{ $landing['services_title'] ?? 'Everything Your Device Needs' }}</h2>
        <p class="section-sub section-sub-white anim-slide-l delay-1">{{ $landing['services_subtitle'] ?? 'Professional repair services for all major smartphone and tablet brands' }}</p>
        <div class="services-grid">
            @if($services && count($services))
                @foreach($services as $idx => $svc)
                <div class="service-card anim-flip delay-{{ ($idx % 8) + 1 }}" data-tilt>
                    <div class="service-icon">
                        @if($svc->image)<img src="{{ asset('storage/'.$svc->image) }}" alt="{{ $svc->name }}">
                        @elseif($svc->thumbnail)<img src="{{ asset('storage/'.$svc->thumbnail) }}" alt="{{ $svc->name }}">
                        @else<svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z"/></svg>
                        @endif
                    </div>
                    <div class="service-name">{{ $svc->name }}</div>
                    @if($svc->description)<div class="service-desc">{{ Str::limit($svc->description, 90) }}</div>@endif
                    @if($svc->default_price > 0)<div class="service-price">Starting {{ $landing['currency'] ?? '₹' }}{{ number_format($svc->default_price, 0) }}</div>@endif
                </div>
                @endforeach
            @else
                @php
                $defaultServices = [
                    ['Screen Replacement','Cracked or broken display? Premium quality replacement for all major brands.','M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['Battery Replacement','Battery draining fast? Genuine replacement to restore full-day performance.','M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['Water Damage Repair','Dropped in water? Ultrasonic cleaning and micro-soldering to save your device.','M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707'],
                    ['Charging Port Fix','Phone not charging? Expert repair or replacement of faulty charging ports.','M13 10V3L4 14h7v7l9-11h-7z'],
                    ['Speaker & Mic Repair','Audio issues? We diagnose and fix all speaker and microphone problems.','M15.536 8.464a5 5 0 010 7.072M12 6a7 7 0 010 12M8.464 8.464a5 5 0 000 7.072'],
                    ['Software & Unlocking','Password reset, factory restore, or IMEI unlock — all software needs covered.','M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['Back Glass Repair','Shattered back glass replaced with OEM quality parts and perfect finish.','M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                    ['Camera Repair','Blurry photos or broken camera? Restore crystal-clear image quality.','M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z'],
                ];
                @endphp
                @foreach($defaultServices as $idx => [$name,$desc,$icon])
                <div class="service-card anim-flip delay-{{ ($idx % 8) + 1 }}" data-tilt>
                    <div class="service-icon"><svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}"/></svg></div>
                    <div class="service-name">{{ $name }}</div>
                    <div class="service-desc">{{ $desc }}</div>
                </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ═══════════════ WHY US ═══════════════ -->
<!-- CONCEPT 4: Heading zooms in, CONCEPT 6: Cards swing from alternating sides -->
<section class="section why-section" id="why">
    <div class="why-float-icons">
        <div class="why-float-icon" style="top:10%;left:5%;animation-delay:0s;"><svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(59,130,246,.2)" stroke-width="1.5"><rect x="4" y="4" width="16" height="16" rx="2"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3"/></svg></div>
        <div class="why-float-icon" style="top:30%;right:8%;animation-delay:-3s;"><svg width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="rgba(139,92,246,.15)" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg></div>
        <div class="why-float-icon" style="bottom:15%;left:10%;animation-delay:-5s;"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="rgba(6,182,212,.15)" stroke-width="1.5"><path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01M2.049 10.049A13.5 13.5 0 0112 4a13.5 13.5 0 019.951 6.049M5.09 13.09A9.5 9.5 0 0112 8a9.5 9.5 0 016.91 5.09"/></svg></div>
        <div class="why-float-icon" style="bottom:30%;right:5%;animation-delay:-7s;"><svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="rgba(245,158,11,.12)" stroke-width="1.5"><path d="M2 20h20M4 20l2-14 4 6 2-8 2 8 4-6 2 14"/></svg></div>
    </div>
    <div class="container text-center" style="position:relative;z-index:2;">
        <div class="section-tag anim-zoom" style="background:rgba(59,130,246,.12);border-color:rgba(59,130,246,.18);">Why Choose Us</div>
        <h2 class="section-title section-title-white anim-slide-r">{{ $landing['why_title'] ?? 'Your Device Is In Expert Hands' }}</h2>
        <p class="section-sub section-sub-white anim-slide-r delay-1">{{ $landing['why_subtitle'] ?? ($shopName . ' — trusted by hundreds of customers for quality repairs') }}</p>
        <div class="why-grid">
            <div class="why-card anim-swing-l delay-1"><div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div><div class="why-title-text">Genuine Parts</div><div class="why-desc">OEM and high-quality parts so your device performs like new.</div></div>
            <div class="why-card anim-swing-r delay-2"><div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="why-title-text">Quick Turnaround</div><div class="why-desc">Most repairs done in under an hour. We respect your time.</div></div>
            <div class="why-card anim-swing-l delay-3"><div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></div><div class="why-title-text">Live Repair Tracking</div><div class="why-desc">Track your repair status anytime with your unique Tracking ID.</div></div>
            <div class="why-card anim-swing-r delay-4"><div class="why-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><div class="why-title-text">Transparent Pricing</div><div class="why-desc">No surprises. Estimate given before any work starts.</div></div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ═══════════════ TRACK ═══════════════ -->
<!-- CONCEPT 5: Blur reveal + rise for left, CONCEPT 6: Swing in for right -->
<section class="section track-section" id="track">
    <div class="container">
        <div class="track-inner">
            <div class="anim-blur">
                <div class="section-tag">Repair Tracker</div>
                <h2 class="track-big-title">Know Exactly<br>Where Your Device Is</h2>
                <p style="font-size:15px;color:#94a3b8;line-height:1.8;margin-bottom:8px;">Every repair gets a unique Tracking ID printed on your receipt. Use it to instantly check the status — no login needed.</p>
                <div class="track-steps">
                    <div class="track-step"><div class="track-step-num">1</div><div class="track-step-text"><strong>Drop off your device</strong><span>Hand in your device and get a printed receipt.</span></div></div>
                    <div class="track-step"><div class="track-step-num">2</div><div class="track-step-text"><strong>Note the Tracking ID</strong><span>Your receipt shows a code like <code style="background:rgba(255,255,255,.06);padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700;color:#60a5fa;">TRK-XXXXXXXX</code>.</span></div></div>
                    <div class="track-step"><div class="track-step-num">3</div><div class="track-step-text"><strong>Track anytime</strong><span>Enter your ID to see live repair status updates.</span></div></div>
                </div>
            </div>
            <div class="anim-swing-r">
                <div class="track-widget">
                    <div class="track-widget-title">Track Your Repair</div>
                    <div class="track-widget-sub">Enter your Tracking ID from the receipt</div>
                    <input type="text" id="trackWidgetInput" class="track-widget-input" placeholder="e.g. TRK-C06C030E" autocomplete="off" spellcheck="false" maxlength="20" style="text-transform:uppercase;" onkeydown="if(event.key==='Enter')widgetTrack()">
                    <button class="track-widget-btn" onclick="widgetTrack()">Check Status &rarr;</button>
                    <div class="track-widget-hint">Free &bull; No login required &bull; Live status</div>
                </div>
            </div>
        </div>
    </div>
</section>

<hr class="section-divider">

<!-- ═══════════════ CONTACT ═══════════════ -->
<!-- CONCEPT 2: Heading slide from Left, CONCEPT 3: Items flip up, CONCEPT 6: Map swings in -->
<section class="section contact-section" id="contact">
    <div class="container text-center">
        <div class="section-tag anim-zoom">Get In Touch</div>
        <h2 class="section-title section-title-white anim-slide-l">{{ $landing['contact_title'] ?? 'Find Us & Contact Us' }}</h2>
        <p class="section-sub section-sub-white anim-slide-l delay-1">{{ $landing['contact_subtitle'] ?? 'We\'re here to help. Visit us, call us, or drop a message on WhatsApp.' }}</p>
        <div class="contact-grid">
            <div class="contact-items" style="text-align:left;">
                @if($shopAddress)
                <div class="contact-item anim-flip delay-1"><div class="contact-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div><div><div class="contact-label">Address</div><div class="contact-value" style="font-size:14px;line-height:1.6;">{{ $shopAddress }}</div></div></div>
                @endif
                @if($shopPhone)
                <div class="contact-item anim-flip delay-2"><div class="contact-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg></div><div><div class="contact-label">Phone</div><div class="contact-value"><a href="tel:{{ $shopPhone }}">{{ $shopPhone }}</a></div></div></div>
                @endif
                @if($shopEmail)
                <div class="contact-item anim-flip delay-3"><div class="contact-icon"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg></div><div><div class="contact-label">Email</div><div class="contact-value"><a href="mailto:{{ $shopEmail }}">{{ $shopEmail }}</a></div></div></div>
                @endif
                @if($shopWhatsapp)
                <div class="contact-item anim-flip delay-4"><div class="contact-icon" style="background:rgba(37,211,102,.08);color:#25d366;"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></div><div><div class="contact-label">WhatsApp</div><div class="contact-value"><a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" style="color:#25d366;">Chat on WhatsApp</a></div></div></div>
                @endif
            </div>
            <div class="map-frame anim-swing-r">
                @if(!empty($landing['map_embed']))
                <iframe src="{{ $landing['map_embed'] }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Shop Location"></iframe>
                @elseif($shopAddress)
                @php $mapZoom = !empty($landing['map_zoom']) ? (int) $landing['map_zoom'] : 15; @endphp
                <iframe src="https://maps.google.com/maps?q={{ urlencode($shopAddress) }}&output=embed&z={{ $mapZoom }}" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Shop Location"></iframe>
                @else
                <div class="map-placeholder"><svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg><p style="font-size:14px;">No address configured yet</p></div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ═══════════════ CTA BANNER ═══════════════ -->
<!-- CONCEPT 4: Zoom + Spin entrance -->
<div class="cta-banner">
    <div class="container">
        <h2 class="anim-zoom">{{ $landing['cta_title'] ?? 'Ready to Get Your Device Fixed?' }}</h2>
        <p class="anim-zoom delay-1">{{ $landing['cta_subtitle'] ?? 'Visit us today or reach out via WhatsApp. Fast, professional repair service.' }}</p>
        <div class="cta-btns anim-zoom delay-2">
            <a href="{{ route('track.landing') }}" class="btn btn-white">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                Track My Repair
            </a>
            @if($shopWhatsapp)
            <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" class="btn btn-outline-white">WhatsApp Us Now</a>
            @elseif($shopPhone)
            <a href="tel:{{ $shopPhone }}" class="btn btn-outline-white">Call {{ $shopPhone }}</a>
            @endif
        </div>
    </div>
</div>

<!-- ═══════════════ FOOTER ═══════════════ -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="anim-blur">
                <div class="footer-brand">
                    <div class="footer-brand-logo">
                        @if($shopIcon)<img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}">
                        @else<span style="font-size:11px;font-weight:800;color:#fff;text-align:center;line-height:1.2;">{{ strtoupper(substr($shopName,0,2)) }}</span>@endif
                    </div>
                    <div class="footer-brand-name">{{ $shopName }}</div>
                </div>
                <div class="footer-desc">{{ $shopSlogan }}. Professional mobile device repair services with genuine parts and transparent pricing.</div>
            </div>
            <div class="anim-slide-r delay-1">
                <div class="footer-col-title">Quick Links</div>
                <div class="footer-links"><a href="#services">Our Services</a><a href="{{ route('track.landing') }}">Track Repair</a><a href="#contact">Find Us</a><a href="/login">Admin Panel</a></div>
            </div>
            <div class="anim-slide-r delay-2">
                <div class="footer-col-title">Contact</div>
                <div class="footer-contact-items">
                    @if($shopPhone)<div class="footer-contact-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $shopPhone }}</div>@endif
                    @if($shopEmail)<div class="footer-contact-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $shopEmail }}</div>@endif
                    @if($shopAddress)<div class="footer-contact-item"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>{{ $shopAddress }}</div>@endif
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-copy">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
            <div class="footer-bottom-links"><a href="{{ route('track.landing') }}">Track Repair</a><a href="/login">Admin</a></div>
        </div>
    </div>
</footer>

@if($shopWhatsapp)
<div class="wa-float">
    <a href="https://wa.me/{{ preg_replace('/\D+/','',$shopWhatsapp) }}" target="_blank" title="Chat on WhatsApp">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
    </a>
</div>
@endif

<script>
(function(){
  // Page Loader
  window.addEventListener('load',function(){setTimeout(function(){document.getElementById('pageLoader').classList.add('hidden');},600);});

  // Scroll Progress
  var progressBar=document.getElementById('scrollProgress');
  function updateProgress(){var h=document.documentElement.scrollHeight-window.innerHeight;progressBar.style.width=h>0?((window.scrollY/h)*100)+'%':'0%';}

  // Navbar
  var navbar=document.getElementById('navbar');
  function updateNavbar(){navbar.classList.toggle('scrolled',window.scrollY>50);}

  // Hero 3D parallax on scroll — devices drift + crack
  var phone3d=document.getElementById('phone3d'),laptop3d=document.getElementById('laptop3d'),tablet3d=document.getElementById('tablet3d');
  var phoneCrack=document.getElementById('phoneCrack'),screenGlitch=document.getElementById('screenGlitch'),laptopCrack=document.getElementById('laptopCrack');
  var heroSection=document.getElementById('heroSection');
  function updateHeroParallax(){
    var rect=heroSection.getBoundingClientRect();
    var p=Math.max(0,Math.min(1,-rect.top/(heroSection.offsetHeight*0.5)));
    if(phone3d) phone3d.style.transform='translate(-50%,-50%) translateY('+(p*20)+'px) rotateZ('+(p*8)+'deg)';
    if(laptop3d) laptop3d.style.transform='translateY('+(p*-30)+'px) translateX('+(p*25)+'px) rotate('+(-8+p*12)+'deg)';
    if(tablet3d) tablet3d.style.transform='translateY('+(p*15)+'px) translateX('+(p*-20)+'px)';
    if(p>0.4){phoneCrack.classList.add('active');screenGlitch.classList.add('active');if(laptopCrack)laptopCrack.classList.add('active');}
    else{phoneCrack.classList.remove('active');screenGlitch.classList.remove('active');if(laptopCrack)laptopCrack.classList.remove('active');}
  }

  // Hero 3D mouse parallax
  var hero3dArea=document.getElementById('hero3dArea'),scene3d=document.getElementById('scene3d');
  if(hero3dArea){
    hero3dArea.addEventListener('mousemove',function(e){
      var r=hero3dArea.getBoundingClientRect(),x=(e.clientX-r.left)/r.width-.5,y=(e.clientY-r.top)/r.height-.5;
      if(scene3d)scene3d.style.transform='rotateX('+(-y*10)+'deg) rotateY('+(x*10)+'deg)';
    });
    hero3dArea.addEventListener('mouseleave',function(){if(scene3d)scene3d.style.transform='';});
  }

  // 3D Tilt on service cards
  function initTiltCards(){
    document.querySelectorAll('[data-tilt]').forEach(function(card){
      card.addEventListener('mousemove',function(e){
        var r=card.getBoundingClientRect(),x=(e.clientX-r.left)/r.width,y=(e.clientY-r.top)/r.height;
        card.style.transform='perspective(600px) rotateX('+((y-.5)*-12)+'deg) rotateY('+((x-.5)*12)+'deg) translateY(-8px)';
        card.style.setProperty('--mx',(x*100)+'%');
        card.style.setProperty('--my',(y*100)+'%');
      });
      card.addEventListener('mouseleave',function(){card.style.transform='';});
    });
  }

  // Master scroll handler
  var ticking=false;
  function onScroll(){if(!ticking){requestAnimationFrame(function(){updateProgress();updateNavbar();updateHeroParallax();ticking=false;});ticking=true;}}
  window.addEventListener('scroll',onScroll,{passive:true});
  onScroll();

  // 6-concept scroll reveal via IntersectionObserver
  function initScrollAnims(){
    var selectors='.anim-slide-r,.anim-slide-l,.anim-flip,.anim-zoom,.anim-blur,.anim-swing-l,.anim-swing-r';
    var els=document.querySelectorAll(selectors);
    var observer=new IntersectionObserver(function(entries){
      entries.forEach(function(entry){
        if(entry.isIntersecting){entry.target.classList.add('in-view');observer.unobserve(entry.target);}
      });
    },{threshold:0.08,rootMargin:'0px 0px -20px 0px'});
    els.forEach(function(el){observer.observe(el);});
  }

  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(function(a){
    a.addEventListener('click',function(e){var t=document.querySelector(this.getAttribute('href'));if(t){e.preventDefault();t.scrollIntoView({behavior:'smooth',block:'start'});}});
  });

  // Track widget
  window.widgetTrack=function(){var v=document.getElementById('trackWidgetInput').value.trim().toUpperCase();if(!v){document.getElementById('trackWidgetInput').focus();return;}window.location.href='/track/'+encodeURIComponent(v);};

  // Init
  document.addEventListener('DOMContentLoaded',function(){initScrollAnims();initTiltCards();});
})();
</script>
</body>
</html>
