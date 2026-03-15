<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100" data-demo="{{ config('app.demo_mode', false) ? 'true' : 'false' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - RepairBox</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        @keyframes floatUp { from { transform: translateY(0px); } to { transform: translateY(-10px); } }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }
        @keyframes slideInLeft { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes slideInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
        @keyframes glow { 0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), 0 15px 50px rgba(59, 130, 246, 0.15); } 50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.5), 0 15px 60px rgba(59, 130, 246, 0.25); } }
        @keyframes progress { 0% { width: 0; } 85% { width: 95%; } 100% { width: 100%; } }
        @keyframes dotAnimation { 0%, 20% { transform: scale(1) translateY(0); opacity: 1; } 50% { transform: scale(0.8) translateY(-15px); opacity: 0.5; } 100% { transform: scale(1) translateY(0); opacity: 1; } }
        @keyframes logoFill { 0% { background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%); box-shadow: 0 25px 60px rgba(59, 130, 246, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.3); } 50% { background: linear-gradient(135deg, rgba(255, 255, 255, 0.6) 0%, rgba(255, 255, 255, 0.4) 100%); box-shadow: 0 25px 60px rgba(59, 130, 246, 0.25), inset 0 1px 0 rgba(255, 255, 255, 0.6); } 100% { background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%); box-shadow: 0 25px 60px rgba(59, 130, 246, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.9); } }

        .app-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 50%, #bfdbfe 100%);
            animation: fadeOut 0.6s ease-in-out 2.4s forwards;
            overflow: hidden;
        }

        /* Tech decorative elements */
        .tech-dots {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .dot {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(59, 130, 246, 0.2);
            border-radius: 50%;
        }

        .dot-1 { top: 15%; left: 10%; animation: pulse 3s ease-in-out infinite; }
        .dot-2 { top: 25%; right: 15%; animation: pulse 3s ease-in-out 0.5s infinite; }
        .dot-3 { bottom: 20%; left: 20%; animation: pulse 3s ease-in-out 1s infinite; }
        .dot-4 { bottom: 25%; right: 10%; animation: pulse 3s ease-in-out 1.5s infinite; }
        .dot-5 { top: 50%; right: 5%; animation: pulse 3s ease-in-out 0.7s infinite; }

        /* Grid pattern background */
        .grid-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
        }

        .loader-content {
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
            position: relative;
            z-index: 10;
        }

        .loader-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 40px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 25px 60px rgba(59, 130, 246, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            animation: slideInLeft 0.7s cubic-bezier(0.34, 1.56, 0.64, 1), logoFill 2s ease-in-out 0.4s forwards;
        }

        .loader-logo img {
            width: 85%;
            height: 85%;
            object-fit: contain;
        }

        .loader-icon {
            width: 85%;
            height: 85%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3b82f6;
        }

        .loader-text {
            color: #1e40af;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 10px;
            animation: slideInRight 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) 0.1s both;
        }

        .loader-tagline {
            color: #3b82f6;
            font-size: 13px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 600;
            animation: fadeIn 0.8s ease-in-out 0.3s both;
        }

        .loader-progress {
            margin-top: 40px;
            height: 4px;
            width: 80px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 10px;
            margin-left: auto;
            margin-right: auto;
            overflow: hidden;
            animation: fadeIn 0.8s ease-in-out 0.4s both;
        }

        .loader-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
            border-radius: 10px;
            animation: progress 2.2s cubic-bezier(0.2, 0.8, 0.4, 1) forwards;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.6);
        }

        body.loading {
            overflow: hidden;
        }
    </style>
</head>
<body class="h-full loading">

{{-- App Loader --}}
<div class="app-loader" id="appLoader">
    {{-- Tech Background Elements --}}
    <div class="grid-overlay"></div>
    <div class="tech-dots">
        <div class="dot dot-1"></div>
        <div class="dot dot-2"></div>
        <div class="dot dot-3"></div>
        <div class="dot dot-4"></div>
        <div class="dot dot-5"></div>
    </div>

    <div class="loader-content">
        @php
            $shopIcon = \App\Models\Setting::getValue('shop_icon');
            $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
        @endphp

        <div class="loader-logo">
            @if($shopIcon)
                <img src="{{ asset('storage/' . $shopIcon) }}" alt="Shop Logo">
            @else
                <div class="loader-icon">
                    <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </div>

        <div class="loader-text">{{ Str::upper($shopName) }}</div>
        <div class="loader-tagline">Loading Shop Management</div>
        <div class="loader-progress">
            <div class="loader-progress-bar"></div>
        </div>
    </div>
</div>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            @if($shopIcon)
                <div class="mx-auto w-20 h-20 rounded-2xl flex items-center justify-center overflow-hidden bg-gradient-to-br from-blue-100 to-blue-50 border-2 border-blue-200 shadow-lg hover:shadow-xl transition-shadow">
                    <img src="{{ asset('storage/' . $shopIcon) }}" alt="Shop Logo" class="w-full h-full object-contain">
                </div>
            @else
                <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-500 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-xl transition-shadow">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <h2 class="mt-6 text-3xl font-bold text-gray-900">{{ $shopName }}</h2>
            <p class="mt-2 text-sm text-gray-600">Mobile Shop Management System</p>
        </div>

        <div class="card">
            <div class="card-body">

                <form x-data="loginForm()" x-init="init()" @submit.prevent="submit()" class="space-y-5" autocomplete="off">                    <div x-show="error" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm" x-text="error"></div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input id="email" x-model="email" type="email" required autofocus class="form-input-custom" placeholder="admin@repairbox.com" autocomplete="off">                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input id="password" x-model="password" type="password" required class="form-input-custom" placeholder="••••••••" autocomplete="new-password">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" x-model="remember" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            Remember me
                        </label>
                    </div>
                    <button type="submit" class="w-full btn-primary py-3 text-base" :disabled="loading">
                        <span x-show="loading" class="spinner mr-2"></span>Sign In
                    </button>
                </form>

                <div class="mt-4 text-center text-xs text-gray-400">
                    Demo: admin@repairbox.com / password
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const loader = document.getElementById('appLoader');
    if (loader) {
        setTimeout(() => {
            document.body.classList.remove('loading');
            loader.style.pointerEvents = 'none';
        }, 2400);
    }
});

function loginForm() {
    return {
        email: '', password: '', remember: false, loading: false, error: '',
        init() {
            // Check if demo mode is enabled
            const demoMode = document.documentElement.getAttribute('data-demo');
            if (demoMode === 'true') {
                this.email = 'admin@repairbox.com';
                this.password = 'password';
                this.remember = true;
            }
        },
        async submit() {
            this.error = ''; this.loading = true;
            try {
                const res = await fetch('/login', {
                    method: 'POST', headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'},
                    body: JSON.stringify({email:this.email,password:this.password,remember:this.remember})
                });
                const data = await res.json();
                if(data.success) { window.location.href = data.redirect || '/dashboard'; }
                else { this.error = data.message || 'Invalid credentials'; this.loading = false; }
            } catch(e) { this.error = 'Login failed. Please try again.'; this.loading = false; }
        }
    };
}
</script>
</body>
</html>
