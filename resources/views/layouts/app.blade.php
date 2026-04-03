@php
    $allowedThemes = ['atelier'];
    $allowedMotion = ['enhanced', 'reduced', 'none'];
    $isWorkspaceLayout = trim($__env->yieldContent('content-class')) === 'workspace-content';

    $uiTheme = \App\Models\Setting::getValue('ui_theme', 'atelier');
    $uiMotion = \App\Models\Setting::getValue('ui_motion', 'enhanced');

    if (! in_array($uiTheme, $allowedThemes, true)) {
        $uiTheme = 'atelier';
    }

    if (! in_array($uiMotion, $allowedMotion, true)) {
        $uiMotion = 'enhanced';
    }

    $themeNames = [
        'atelier' => 'Atelier Glass',
    ];
@endphp
<!DOCTYPE html>
<html lang="en" class="h-full {{ $isWorkspaceLayout ? 'lg:overflow-hidden' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RepairBox') - Mobile Shop Management</title>
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#2563eb">
    @php $shopFavicon = \App\Models\Setting::getValue('shop_favicon', ''); @endphp
    @if($shopFavicon)
        <link rel="icon" href="{{ app(\App\Services\ImageService::class)->url($shopFavicon) }}" type="image/png">
    @else
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
    @endif
    <script>window.__pwaPrompt=null;window.addEventListener('beforeinstallprompt',function(e){e.preventDefault();window.__pwaPrompt=e;});</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        .page-loader {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(4px);
        }

        .page-loader.active {
            display: flex;
            animation: fadeIn 0.2s ease-in-out;
        }

        .page-loader.hide {
            animation: fadeOut 0.3s ease-in-out forwards;
        }

        .loader-circle {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 255, 255, 0.2);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
    </style>
</head>
<body class="h-full app-shell {{ $isWorkspaceLayout ? 'workspace-layout' : '' }}" data-theme="{{ $uiTheme }}" data-motion="{{ $uiMotion }}" x-data="{ mobileMenuOpen: false }">

<div class="app-backdrop" aria-hidden="true">
    <span class="app-orb app-orb-one"></span>
    <span class="app-orb app-orb-two"></span>
    <span class="app-orb app-orb-three"></span>
</div>

<div class="app-frame {{ $isWorkspaceLayout ? 'workspace-layout-frame' : '' }}">

<div class="page-loader" id="pageLoader">
    <div class="loader-circle"></div>
</div>

@php
    $shopIcon = \App\Models\Setting::getValue('shop_icon');
    $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');

    $navItems = [
        ['name' => 'Sales', 'route' => '/admin/pos', 'match' => 'admin/pos*', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z'],
        ['name' => 'Repairs', 'route' => '/admin/repairs', 'match' => 'admin/repairs*', 'icon' => 'M11.42 15.17l-4.655-5.55a.776.776 0 010-1.06v0a.776.776 0 011.13 0l3.72 3.72 7.08-7.08a.776.776 0 011.06 0v0a.776.776 0 010 1.06L11.42 15.17z'],
        ['name' => 'Recharge', 'route' => '/admin/recharges', 'match' => 'admin/recharges*', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
        ['name' => 'Expenses', 'route' => '/admin/expenses', 'match' => 'admin/expenses*', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ['name' => 'PO', 'route' => '/admin/po', 'match' => ['admin/po', 'admin/po/*'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ];
@endphp

<!-- Top Navigation Bar -->
<nav class="topnav no-print">
    <div class="topnav-inner">
        <!-- Left: Logo + Shop Name -->
        <a href="/admin/dashboard" class="brand-link">
            @if($shopIcon)
                <div class="brand-logo">
                    <img src="{{ image_url($shopIcon) }}" alt="Logo" class="w-full h-full object-contain">
                </div>
            @else
                <div class="brand-logo brand-logo-fallback">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <div class="hidden sm:block">
                <div class="brand-name">{{ Str::limit($shopName, 20) }}</div>
                <div class="brand-subtitle">Business control center</div>
            </div>
        </a>

        <!-- Center: Module Links (desktop) -->
        <div class="topnav-links-shell hidden md:flex">
            @foreach($navItems as $item)
                <a href="{{ $item['route'] }}" class="topnav-link {{ request()->is($item['match']) ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Right: Settings + User + Logout -->
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="/admin/settings" class="topnav-link topnav-link-compact {{ request()->is('admin/settings*') ? 'active' : '' }}" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="hidden lg:inline">Settings</span>
            </a>

            <div class="user-chip hidden sm:flex">
                <div class="user-chip-avatar">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden lg:block">
                    <div class="user-chip-label">Signed in</div>
                    <div class="user-chip-name">{{ auth()->user()->name ?? 'User' }}</div>
                </div>
            </div>

            <!-- PWA Install Button -->
            <button id="pwa-install-btn" title="Install App" class="icon-action" style="color: #60a5fa;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span class="hidden lg:inline text-xs font-medium">Install</span>
            </button>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="icon-action icon-action-danger" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>

            <!-- Mobile hamburger - only shown on md+ if needed or hidden completely -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="icon-action hidden" :class="mobileMenuOpen ? 'is-active' : ''">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile dropdown menu -->
    {{-- Mobile dropdown kept for md+ if ever needed, hidden via md:hidden --}}
</nav>

<!-- Bottom Navigation Bar (mobile only) -->
<nav class="bottom-nav md:hidden no-print" aria-label="Mobile navigation">
    @foreach($navItems as $item)
        <a href="{{ $item['route'] }}" class="bottom-nav-item {{ request()->is($item['match']) ? 'active' : '' }}">
            <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
            <span class="bottom-nav-label">{{ $item['name'] }}</span>
        </a>
    @endforeach
    <a href="/admin/settings" class="bottom-nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
        <svg class="bottom-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <span class="bottom-nav-label">Settings</span>
    </a>
</nav>

<!-- Main Content -->
<div class="app-main-shell {{ $isWorkspaceLayout ? 'workspace-layout-main' : '' }}">
    <!-- Page Content -->
    <main class="page-main @yield('content-class')">
        <div class="content-shell">
            @yield('content')
        </div>
    </main>
</div>

</div>

<script>
window.RepairBox = {
    imageBaseUrl: @json(app(\App\Services\ImageService::class)->baseUrl()),
    imageUrl: function(path) {
        if (!path) return '';
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        return this.imageBaseUrl + '/' + path.replace(/^\/+/, '');
    },
    ajax: async function(url, method, data) {
        method = method || 'GET';
        try {
            var config = { url: url, method: method, headers: { 'X-Requested-With': 'XMLHttpRequest' } };
            if (data && method !== 'GET') config.data = data;
            if (method === 'GET' && data) config.params = data;
            var response = await axios(config);
            var d = response.data;
            if (d && typeof d === 'object' && Array.isArray(d.data) && 'current_page' in d) {
                var result = { data: d.data, meta: { current_page: d.current_page, last_page: d.last_page, total: d.total }, success: true };
                if (d.counts) result.counts = d.counts;
                return result;
            }
            if (d && typeof d === 'object' && 'success' in d && 'data' in d) {
                var result = { data: d.data, success: d.success, message: d.message || null };
                if ('has_more' in d) result.has_more = d.has_more;
                if ('page' in d) result.page = d.page;
                return result;
            }
            return { data: d, success: true };
        } catch (error) {
            var msg = 'Something went wrong';
            if (error.response && error.response.data) {
                if (error.response.data.errors) {
                    var errs = error.response.data.errors;
                    msg = Object.keys(errs).map(function(k) { return errs[k].join(', '); }).join('; ');
                } else if (error.response.data.message) {
                    msg = error.response.data.message;
                }
            }
            RepairBox.toast(msg, 'error');
            return { data: null, success: false };
        }
    },
    toast: function(message, type) {
        type = type || 'success';
        var container = document.getElementById('toast-container');
        if (!container) { container = document.createElement('div'); container.id = 'toast-container'; container.className = 'toast-container'; document.body.appendChild(container); }
        var colors = { success: 'bg-emerald-500 text-white', error: 'bg-red-500 text-white', warning: 'bg-amber-500 text-white', info: 'bg-blue-500 text-white' };
        var toast = document.createElement('div');
        toast.className = 'toast ' + (colors[type] || colors.info);
        toast.innerHTML = '<span class="flex-1 text-sm font-medium">' + message + '</span><button onclick="this.parentElement.remove()" class="ml-2 hover:opacity-75">&times;</button>';
        container.appendChild(toast);
        setTimeout(function() { if (toast.parentElement) toast.remove(); }, 5000);
    },
    confirm: function(message) { return Promise.resolve(window.confirm(message)); },
    formatCurrency: function(amount) { return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amount); },
    emptyCustomer: function() {
        return { name: '', mobile_number: '', email: '', address: '' };
    },
    normalizeCustomerMobile: function(value) {
        return String(value || '').replace(/\D+/g, '').slice(0, 10);
    },
    normalizeCustomerPayload: function(payload) {
        return {
            name: String(payload?.name || '').trim(),
            mobile_number: RepairBox.normalizeCustomerMobile(payload?.mobile_number),
            email: String(payload?.email || '').trim(),
            address: String(payload?.address || '').trim(),
        };
    },
    validateCustomerPayload: function(payload) {
        var normalized = RepairBox.normalizeCustomerPayload(payload);
        var errors = {};

        if (!normalized.name) {
            errors.name = 'Name is required';
        }

        if (!normalized.mobile_number) {
            errors.mobile_number = 'Mobile number is required';
        } else if (!/^\d{10}$/.test(normalized.mobile_number)) {
            errors.mobile_number = 'Mobile must be exactly 10 digits';
        }

        if (normalized.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalized.email)) {
            errors.email = 'Please enter a valid email';
        }

        return {
            valid: Object.keys(errors).length === 0,
            errors: errors,
            payload: {
                name: normalized.name,
                mobile_number: normalized.mobile_number,
                email: normalized.email || null,
                address: normalized.address || null,
            },
            firstError: Object.values(errors)[0] || '',
        };
    },
    upload: async function(url, formData) {
        try {
            const response = await axios.post(url, formData, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'multipart/form-data' }
            });
            const d = response.data;
            if (d && 'success' in d) return d;
            return { data: d, success: true };
        } catch(error) {
            const msg = error.response?.data?.message || 'Upload failed';
            RepairBox.toast(msg, 'error');
            return { data: null, success: false };
        }
    }
};

// Page Loader
const pageLoader = document.getElementById('pageLoader');
if (pageLoader) {
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.href.includes('#') && link.target !== '_blank' && !link.classList.contains('no-loader')) {
            if (link.href.includes(window.location.origin) || link.href.startsWith('/')) {
                pageLoader.classList.add('active');
            }
        }
    });

    window.addEventListener('pageshow', function(event) {
        pageLoader.classList.remove('active');
        pageLoader.classList.remove('hide');
    });
}

// Prevent browser from navigating to files dropped outside a drop zone
document.addEventListener('dragover', function(e) { e.preventDefault(); });
document.addEventListener('drop', function(e) { e.preventDefault(); });

// PWA Install
(function() {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw.js').catch(function() {});
    }

    var installBtn = document.getElementById('pwa-install-btn');
    if (!installBtn) return;

    // Hide button only when already running as an installed PWA
    if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
        installBtn.classList.add('hidden');
        return;
    }

    // Capture native install prompt whenever it fires
    window.addEventListener('beforeinstallprompt', function(e) {
        e.preventDefault();
        window.__pwaPrompt = e;
    });

    installBtn.addEventListener('click', async function() {
        if (window.__pwaPrompt) {
            // Native browser install dialog
            window.__pwaPrompt.prompt();
            var result = await window.__pwaPrompt.userChoice;
            if (result.outcome === 'accepted') {
                window.__pwaPrompt = null;
                installBtn.classList.add('hidden');
            }
        } else {
            // Fallback instructions when native prompt isn't available
            RepairBox.toast('To install: open browser menu → "Install app" or "Add to Home Screen"', 'info');
        }
    });

    window.addEventListener('appinstalled', function() {
        window.__pwaPrompt = null;
        installBtn.classList.add('hidden');
        RepairBox.toast('App installed successfully!', 'success');
    });
})();
</script>
@stack('scripts')
</body>
</html>
