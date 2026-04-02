<!DOCTYPE html>
<html lang="en" class="h-full" data-demo="{{ config('app.demo_mode', false) ? 'true' : 'false' }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="robots" content="noindex,nofollow">
  <meta name="theme-color" content="#030712">
  <title>Admin Login &mdash; {{ \App\Models\Setting::getValue('shop_name', 'RepairBox') }}</title>
  <link rel="manifest" href="/manifest.json">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;font-family:'Inter',system-ui,sans-serif;}
    body{background:#030712;color:#e2e8f0;overflow:hidden;}
    /* LOADER */
    .ldr{position:fixed;inset:0;z-index:9999;background:#030712;display:flex;align-items:center;justify-content:center;flex-direction:column;transition:opacity .7s,visibility .7s;}
    .ldr.out{opacity:0;visibility:hidden;pointer-events:none;}
    .ldr-ring{width:64px;height:64px;position:relative;flex-shrink:0;}
    .ldr-ring::before{content:'';position:absolute;inset:0;border-radius:50%;background:conic-gradient(from 0deg,#3b82f6,#8b5cf6,#06b6d4,#3b82f6);animation:ldrSpin 1.1s linear infinite;}
    .ldr-ring::after{content:'';position:absolute;inset:4px;border-radius:50%;background:#030712;}
    .ldr-icon{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:1;}
    .ldr-icon img,.ldr-icon svg{width:28px;height:28px;object-fit:contain;color:#60a5fa;}
    .ldr-name{margin-top:18px;font-size:14px;font-weight:800;color:#fff;letter-spacing:2px;}
    .ldr-sub{font-size:10px;color:#475569;letter-spacing:4px;text-transform:uppercase;margin-top:4px;}
    .ldr-bar{width:90px;height:2px;background:rgba(255,255,255,.05);border-radius:10px;margin-top:16px;overflow:hidden;}
    .ldr-bar-fill{height:100%;background:linear-gradient(90deg,#3b82f6,#8b5cf6);border-radius:10px;animation:ldrFill 2s ease-out forwards;}
    @keyframes ldrSpin{to{transform:rotate(360deg);}}
    @keyframes ldrFill{0%{width:0}100%{width:100%}}
    /* LAYOUT */
    .page{display:grid;grid-template-columns:1fr 1fr;height:100vh;position:relative;overflow:hidden;}
    /* BACKGROUND */
    .bg{position:absolute;inset:0;overflow:hidden;}
    .bg-g1{position:absolute;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.18),transparent 65%);top:-180px;left:-120px;}
    .bg-g2{position:absolute;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(124,58,237,.14),transparent 65%);bottom:-120px;right:-80px;}
    .bg-g3{position:absolute;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(6,182,212,.07),transparent 65%);top:40%;right:40%;}
    .bg-grid{position:absolute;inset:0;background-image:linear-gradient(rgba(59,130,246,.02) 1px,transparent 1px),linear-gradient(90deg,rgba(59,130,246,.02) 1px,transparent 1px);background-size:60px 60px;}
    .particle{position:absolute;border-radius:50%;animation:pFloat linear infinite;}
    @keyframes pFloat{0%{transform:translateY(100vh);opacity:0;}10%{opacity:.7;}90%{opacity:.3;}100%{transform:translateY(-5vh);opacity:0;}}
    /* LEFT PANEL */
    .panel-left{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;padding:48px;border-right:1px solid rgba(255,255,255,.05);}
    .brand-wrap{position:relative;max-width:400px;}
    /* Glow rings behind brand */
    .brand-ring{position:absolute;border-radius:50%;border:1px solid rgba(59,130,246,.08);animation:brPulse 4s ease-in-out infinite;top:50%;left:50%;transform:translate(-50%,-50%);}
    .br1{width:180px;height:180px;}
    .br2{width:260px;height:260px;border-color:rgba(139,92,246,.05);animation-delay:.8s;}
    .br3{width:340px;height:340px;border-color:rgba(6,182,212,.04);animation-delay:1.6s;}
    @keyframes brPulse{0%,100%{opacity:.4;scale:1;}50%{opacity:1;scale:1.03;}}
    .brand-glow{position:absolute;width:140px;height:140px;border-radius:50%;background:radial-gradient(circle,rgba(37,99,235,.22),rgba(124,58,237,.1) 50%,transparent 70%);top:50%;left:50%;transform:translate(-50%,-50%);animation:bgBreath 4s ease-in-out infinite;}
    @keyframes bgBreath{0%,100%{opacity:.7;scale:1;}50%{opacity:1;scale:1.1;}}
    .brand-icon{position:relative;z-index:1;width:72px;height:72px;border-radius:20px;border:1px solid rgba(255,255,255,.1);background:rgba(255,255,255,.04);display:flex;align-items:center;justify-content:center;overflow:hidden;margin:0 auto 20px;box-shadow:0 20px 50px rgba(0,0,0,.4);}
    .brand-icon img{width:100%;height:100%;object-fit:cover;}
    .brand-icon svg{color:#60a5fa;width:34px;height:34px;}
    .brand-icon-txt{font-size:20px;font-weight:900;color:#fff;}
    .brand-name{font-size:26px;font-weight:900;color:#fff;text-align:center;margin-bottom:10px;letter-spacing:-.5px;}
    .brand-desc{font-size:13px;color:#64748b;text-align:center;line-height:1.7;margin-bottom:28px;max-width:320px;}
    .brand-features{display:flex;flex-direction:column;gap:10px;}
    .bf{display:flex;align-items:center;gap:12px;padding:11px 14px;background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.06);border-radius:10px;}
    .bf-ic{width:32px;height:32px;border-radius:8px;background:rgba(37,99,235,.08);border:1px solid rgba(37,99,235,.1);display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#60a5fa;}
    .bf-txt{font-size:12px;color:#94a3b8;font-weight:500;}
    /* RIGHT PANEL */
    .panel-right{position:relative;z-index:1;display:flex;align-items:center;justify-content:center;padding:40px;}
    .login-card{background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.08);border-radius:24px;padding:40px;width:100%;max-width:390px;backdrop-filter:blur(30px);box-shadow:0 40px 100px rgba(0,0,0,.5);}
    .lc-title{font-size:24px;font-weight:900;color:#fff;margin-bottom:6px;letter-spacing:-.3px;}
    .lc-sub{font-size:14px;color:#64748b;margin-bottom:28px;}
    .lc-error{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);color:#fca5a5;padding:11px 14px;border-radius:10px;font-size:13px;margin-bottom:18px;}
    .fg{margin-bottom:16px;}
    .fg label{display:block;font-size:12px;font-weight:600;color:#94a3b8;margin-bottom:7px;letter-spacing:.3px;text-transform:uppercase;}
    .fg input{width:100%;padding:12px 14px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#fff;font-family:inherit;font-size:14px;outline:none;transition:all .3s;}
    .fg input::placeholder{color:#475569;}
    .fg input:focus{border-color:rgba(59,130,246,.5);background:rgba(59,130,246,.05);box-shadow:0 0 0 4px rgba(59,130,246,.1);}
    .fg-remember{display:flex;align-items:center;gap:8px;margin-bottom:20px;}
    .fg-remember input[type=checkbox]{width:15px;height:15px;accent-color:#3b82f6;cursor:pointer;}
    .fg-remember label{font-size:13px;color:#64748b;cursor:pointer;}
    .lc-btn{width:100%;padding:13px;background:linear-gradient(135deg,#2563eb,#7c3aed);border:none;border-radius:12px;color:#fff;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 8px 28px rgba(37,99,235,.3);}
    .lc-btn:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 14px 36px rgba(37,99,235,.4);}
    .lc-btn:disabled{opacity:.7;cursor:not-allowed;}
    .spin{width:18px;height:18px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;animation:spin .8s linear infinite;}
    @keyframes spin{to{transform:rotate(360deg);}}
    .demo-hint{text-align:center;font-size:11px;color:#374151;margin-top:18px;padding-top:16px;border-top:1px solid rgba(255,255,255,.05);}
    .back-home{display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#475569;margin-top:20px;transition:color .2s;}
    .back-home:hover{color:#64748b;}
    /* RESPONSIVE */
    @media(max-width:900px){
      .page{grid-template-columns:1fr;}
      .panel-left{display:none;}
      .panel-right{padding:24px;}
      body{overflow:auto;}
    }
  </style>
</head>
<body>
@php
  $shopIcon = \App\Models\Setting::getValue('shop_icon');
  $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
@endphp

{{-- PAGE LOADER --}}
<div class="ldr" id="loginLoader">
  <div class="ldr-ring">
    <div class="ldr-icon">
      @if($shopIcon)<img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">@else<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>@endif
    </div>
  </div>
  <div class="ldr-name">{{ Str::upper($shopName) }}</div>
  <div class="ldr-sub">Admin Panel</div>
  <div class="ldr-bar"><div class="ldr-bar-fill"></div></div>
</div>

<div class="page" x-data="loginForm()" x-init="init()">
  {{-- BACKGROUND --}}
  <div class="bg">
    <div class="bg-g1"></div><div class="bg-g2"></div><div class="bg-g3"></div>
    <div class="bg-grid"></div>
    @for($i=0;$i<16;$i++)
    <div class="particle" style="left:{{ rand(2,98) }}%;width:{{ rand(1,3) }}px;height:{{ rand(1,3) }}px;animation-duration:{{ rand(9,18) }}s;animation-delay:-{{ rand(0,10) }}s;background:rgba({{ $i%2==0 ? '59,130,246' : '139,92,246' }},.4);"></div>
    @endfor
  </div>

  {{-- LEFT PANEL --}}
  <div class="panel-left">
    <div class="brand-wrap">
      <div class="brand-ring br3"></div>
      <div class="brand-ring br2"></div>
      <div class="brand-ring br1"></div>
      <div class="brand-glow"></div>
      <div class="brand-icon">
        @if($shopIcon)<img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">@else<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>@endif
      </div>
      <div class="brand-name" style="position:relative;z-index:1;">{{ $shopName }}</div>
      <div class="brand-desc" style="position:relative;z-index:1;">Complete repair shop management — track repairs, manage inventory, generate invoices, all in one place.</div>
      <div class="brand-features" style="position:relative;z-index:1;">
        <div class="bf">
          <div class="bf-ic"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
          <span class="bf-txt">Real-time repair tracking & status updates</span>
        </div>
        <div class="bf">
          <div class="bf-ic"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
          <span class="bf-txt">Smart inventory & purchase management</span>
        </div>
        <div class="bf">
          <div class="bf-ic"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
          <span class="bf-txt">Automated invoicing & financial reports</span>
        </div>
        <div class="bf">
          <div class="bf-ic"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
          <span class="bf-txt">Reminders, notifications & expense tracking</span>
        </div>
      </div>
    </div>
  </div>

  {{-- RIGHT PANEL - LOGIN FORM --}}
  <div class="panel-right">
    <div>
      <div class="login-card">
        <div class="lc-title">Welcome back</div>
        <div class="lc-sub">Sign in to your admin panel</div>
        <div x-show="error" x-transition class="lc-error" x-text="error"></div>
        <form @submit.prevent="submit()" autocomplete="off">
          <div class="fg">
            <label for="email">Email Address</label>
            <input id="email" x-model="email" type="email" required autofocus placeholder="admin@example.com" autocomplete="off">
          </div>
          <div class="fg">
            <label for="password">Password</label>
            <input id="password" x-model="password" type="password" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="new-password">
          </div>
          <div class="fg-remember">
            <input type="checkbox" id="remember" x-model="remember">
            <label for="remember">Remember me</label>
          </div>
          <button type="submit" class="lc-btn" :disabled="loading">
            <template x-if="loading"><div class="spin"></div></template>
            <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
          </button>
        </form>
        @if(config('app.demo_mode'))
        <div class="demo-hint">Demo: admin@repairbox.com / password</div>
        @endif      </div>
      <a href="{{ route('home') }}" class="back-home">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to website
      </a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
  window.addEventListener('load', function() {
    setTimeout(function() {
      var l = document.getElementById('loginLoader');
      if (l) l.classList.add('out');
    }, 1800);
  });
  function loginForm() {
    return {
      email: '', password: '', remember: false, loading: false, error: '',
      init() {
        var d = document.documentElement.getAttribute('data-demo');
        if (d === 'true') { this.email = 'admin@repairbox.com'; this.password = 'password'; this.remember = true; }
      },
      async submit() {
        this.error = ''; this.loading = true;
        try {
          var res = await fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            body: JSON.stringify({ email: this.email, password: this.password, remember: this.remember })
          });
          var data = await res.json();
          if (data.success) { window.location.href = data.redirect || '/admin/dashboard'; }
          else { this.error = data.message || 'Invalid credentials. Please try again.'; this.loading = false; }
        } catch(e) { this.error = 'Login failed. Please check your connection.'; this.loading = false; }
      }
    };
  }
</script>
</body>
</html>
