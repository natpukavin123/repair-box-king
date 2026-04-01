<!DOCTYPE html>
<html lang="en" class="h-full" data-demo="{{ config('app.demo_mode', false) ? 'true' : 'false' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - RepairBox</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', system-ui, sans-serif; overflow: hidden; }

        /* ── Loader ── */
        .loader-screen {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: center; justify-content: center; flex-direction: column;
            background: #050a18;
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.8s;
        }
        .loader-screen.fade-out { opacity: 0; visibility: hidden; pointer-events: none; }

        .loader-rings { position: relative; width: 120px; height: 120px; margin-bottom: 40px; }
        .loader-ring {
            position: absolute; inset: 0;
            border: 2px solid transparent;
            border-radius: 50%;
        }
        .loader-ring:nth-child(1) { border-top-color: #3b82f6; animation: ringRotate 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; }
        .loader-ring:nth-child(2) { inset: 10px; border-right-color: #60a5fa; animation: ringRotate 1.6s cubic-bezier(0.5, 0, 0.5, 1) infinite reverse; }
        .loader-ring:nth-child(3) { inset: 20px; border-bottom-color: #93c5fd; animation: ringRotate 2s cubic-bezier(0.5, 0, 0.5, 1) infinite; }
        .loader-logo-center {
            position: absolute; inset: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            background: rgba(59, 130, 246, 0.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .loader-logo-center img { width: 70%; height: 70%; object-fit: contain; }
        .loader-logo-center svg { width: 55%; height: 55%; color: #60a5fa; }

        .loader-brand { font-size: 22px; font-weight: 800; color: #fff; letter-spacing: 3px; text-transform: uppercase; }
        .loader-tagline { font-size: 11px; color: #64748b; letter-spacing: 4px; text-transform: uppercase; margin-top: 8px; }
        .loader-bar-wrap { width: 160px; height: 3px; background: rgba(255,255,255,0.06); border-radius: 10px; margin-top: 32px; overflow: hidden; }
        .loader-bar { height: 100%; background: linear-gradient(90deg, #3b82f6, #8b5cf6); border-radius: 10px; animation: barProgress 2s ease-out forwards; }

        @keyframes ringRotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes barProgress { 0% { width: 0; } 100% { width: 100%; } }

        /* ── Login Page ── */
        .login-page {
            position: relative; height: 100vh; width: 100vw;
            display: flex; overflow: hidden; background: #050a18;
        }

        .login-bg { position: absolute; inset: 0; z-index: 0; }
        .login-bg::before {
            content: ''; position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 80%, rgba(59, 130, 246, 0.15), transparent),
                radial-gradient(ellipse 60% 40% at 80% 20%, rgba(139, 92, 246, 0.12), transparent),
                radial-gradient(ellipse 50% 50% at 50% 50%, rgba(6, 182, 212, 0.06), transparent);
        }

        .particle {
            position: absolute; width: 2px; height: 2px;
            background: rgba(59, 130, 246, 0.5); border-radius: 50%;
            animation: particleFloat linear infinite;
        }
        @keyframes particleFloat {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            10% { opacity: 1; } 90% { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        .water-ripple {
            position: absolute;
            border: 1px solid rgba(59, 130, 246, 0.1);
            border-radius: 50%;
            animation: rippleExpand 4s ease-out infinite;
        }
        @keyframes rippleExpand {
            0% { width: 0; height: 0; opacity: 0.6; }
            100% { width: 600px; height: 600px; opacity: 0; margin-left: -300px; margin-top: -300px; }
        }

        .grid-pattern {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black, transparent);
        }

        .login-left {
            flex: 1; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            position: relative; z-index: 1; padding: 40px;
        }

        .brand-display { text-align: center; animation: brandFadeIn 1s ease-out 0.3s both; }
        @keyframes brandFadeIn { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .brand-icon-wrap {
            width: 100px; height: 100px; margin: 0 auto 32px;
            background: rgba(255, 255, 255, 0.04); backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 28px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.15);
            animation: iconGlow 3s ease-in-out infinite alternate;
        }
        @keyframes iconGlow {
            0% { box-shadow: 0 20px 60px rgba(59, 130, 246, 0.15); }
            100% { box-shadow: 0 20px 80px rgba(59, 130, 246, 0.3), 0 0 40px rgba(59, 130, 246, 0.1); }
        }
        .brand-icon-wrap img { width: 80%; height: 80%; object-fit: contain; }
        .brand-icon-wrap svg { width: 60%; height: 60%; color: #60a5fa; }

        .brand-name-lg { font-size: 36px; font-weight: 900; color: #fff; letter-spacing: -0.5px; line-height: 1.1; }
        .brand-name-lg span { background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .brand-tagline { font-size: 14px; color: #64748b; margin-top: 12px; line-height: 1.6; max-width: 320px; }

        .brand-features { display: flex; flex-direction: column; gap: 16px; margin-top: 48px; animation: brandFadeIn 1s ease-out 0.6s both; }
        .brand-feature {
            display: flex; align-items: center; gap: 14px; padding: 14px 20px;
            background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 14px; backdrop-filter: blur(10px); transition: all 0.3s;
        }
        .brand-feature:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(59, 130, 246, 0.2); transform: translateX(4px); }
        .brand-feature-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(59, 130, 246, 0.1);
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .brand-feature-icon svg { width: 18px; height: 18px; color: #60a5fa; }
        .brand-feature-text { font-size: 13px; font-weight: 600; color: #94a3b8; }

        .login-right {
            width: 480px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            position: relative; z-index: 1; padding: 40px;
        }

        .login-card { width: 100%; max-width: 400px; animation: cardSlideIn 0.8s ease-out 0.4s both; }
        @keyframes cardSlideIn { from { opacity: 0; transform: translateY(20px) scale(0.98); } to { opacity: 1; transform: translateY(0) scale(1); } }

        .login-card-inner {
            background: rgba(255, 255, 255, 0.04); backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px;
            padding: 44px 36px; box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
        }

        .login-title { font-size: 24px; font-weight: 800; color: #fff; margin-bottom: 4px; }
        .login-subtitle { font-size: 14px; color: #64748b; margin-bottom: 32px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; }
        .form-group input {
            width: 100%; padding: 14px 16px;
            background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px; color: #fff; font-family: inherit; font-size: 15px; font-weight: 500;
            outline: none; transition: all 0.3s;
        }
        .form-group input::placeholder { color: rgba(255, 255, 255, 0.2); }
        .form-group input:focus {
            border-color: rgba(59, 130, 246, 0.5); background: rgba(59, 130, 246, 0.05);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-remember { display: flex; align-items: center; gap: 10px; margin-bottom: 28px; }
        .form-remember input[type="checkbox"] {
            width: 18px; height: 18px; border-radius: 6px;
            border: 1px solid rgba(255,255,255,0.15); background: rgba(255,255,255,0.04);
            cursor: pointer; accent-color: #3b82f6;
        }
        .form-remember label { font-size: 13px; color: #64748b; cursor: pointer; }

        .login-btn {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            border: none; border-radius: 14px;
            color: #fff; font-family: inherit; font-size: 15px; font-weight: 700; letter-spacing: 0.5px;
            cursor: pointer; transition: all 0.3s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 8px 30px rgba(37, 99, 235, 0.3);
        }
        .login-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(37, 99, 235, 0.4); }
        .login-btn:active { transform: translateY(0); }
        .login-btn:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }

        .login-btn .spinner {
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3); border-top-color: #fff;
            border-radius: 50%; animation: spin 0.6s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .login-error {
            background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px; padding: 12px 16px; margin-bottom: 20px;
            font-size: 13px; color: #fca5a5;
        }

        .demo-hint { text-align: center; margin-top: 20px; font-size: 12px; color: rgba(255,255,255,0.2); }

        .back-home { display: inline-flex; align-items: center; gap: 6px; margin-top: 24px; font-size: 13px; color: #64748b; text-decoration: none; transition: color 0.2s; }
        .back-home:hover { color: #94a3b8; }

        @media (max-width: 900px) {
            .login-left { display: none; }
            .login-right { width: 100%; }
            .login-page { justify-content: center; }
        }
    </style>
</head>
<body>
    @php
        $shopIcon = \App\Models\Setting::getValue('shop_icon');
        $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
    @endphp

    <div class="loader-screen" id="loaderScreen">
        <div class="loader-rings">
            <div class="loader-ring"></div>
            <div class="loader-ring"></div>
            <div class="loader-ring"></div>
            <div class="loader-logo-center">
                @if($shopIcon)
                    <img src="{{ image_url($shopIcon) }}" alt="Logo">
                @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @endif
            </div>
        </div>
        <div class="loader-brand">{{ Str::upper($shopName) }}</div>
        <div class="loader-tagline">Initializing System</div>
        <div class="loader-bar-wrap"><div class="loader-bar"></div></div>
    </div>

    <div class="login-page" x-data="loginForm()" x-init="init()">
        <div class="login-bg">
            <div class="grid-pattern"></div>
            @for($i = 0; $i < 30; $i++)
            <div class="particle" style="left:{{ rand(0,100) }}%;animation-duration:{{ rand(6,14) }}s;animation-delay:{{ rand(0,8) }}s;width:{{ rand(1,3) }}px;height:{{ rand(1,3) }}px;"></div>
            @endfor
            <div class="water-ripple" style="left:20%;top:70%;animation-delay:0s;"></div>
            <div class="water-ripple" style="left:75%;top:30%;animation-delay:1.5s;"></div>
            <div class="water-ripple" style="left:50%;top:50%;animation-delay:3s;"></div>
        </div>

        <div class="login-left">
            <div class="brand-display">
                <div class="brand-icon-wrap">
                    @if($shopIcon)
                        <img src="{{ image_url($shopIcon) }}" alt="Logo">
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @endif
                </div>
                <div class="brand-name-lg">{{ $shopName }}</div>
                <div class="brand-tagline">Complete repair shop management — track repairs, manage inventory, generate invoices, all in one place.</div>
                <div class="brand-features">
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                        <span class="brand-feature-text">Real-time repair tracking & status updates</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg></div>
                        <span class="brand-feature-text">Smart inventory & purchase management</span>
                    </div>
                    <div class="brand-feature">
                        <div class="brand-feature-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
                        <span class="brand-feature-text">Automated invoicing & financial reports</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-card">
                <div class="login-card-inner">
                    <div class="login-title">Welcome back</div>
                    <div class="login-subtitle">Sign in to your admin panel</div>
                    <div x-show="error" x-transition class="login-error" x-text="error"></div>
                    <form @submit.prevent="submit()" autocomplete="off">
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input id="email" x-model="email" type="email" required autofocus placeholder="admin@repairbox.com" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" x-model="password" type="password" required placeholder="••••••••" autocomplete="new-password">
                        </div>
                        <div class="form-remember">
                            <input type="checkbox" id="remember" x-model="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <button type="submit" class="login-btn" :disabled="loading">
                            <template x-if="loading"><div class="spinner"></div></template>
                            <span x-text="loading ? 'Signing in...' : 'Sign In'"></span>
                        </button>
                    </form>
                    <div class="demo-hint">Demo: admin@repairbox.com / password</div>
                </div>
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
            setTimeout(function() { document.getElementById('loaderScreen').classList.add('fade-out'); }, 2000);
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
                            method: 'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content, 'Accept':'application/json' },
                            body: JSON.stringify({ email:this.email, password:this.password, remember:this.remember })
                        });
                        var data = await res.json();
                        if(data.success) { window.location.href = data.redirect || '/admin/dashboard'; }
                        else { this.error = data.message || 'Invalid credentials'; this.loading = false; }
                    } catch(e) { this.error = 'Login failed. Please try again.'; this.loading = false; }
                }
            };
        }
    </script>
</body>
</html>
