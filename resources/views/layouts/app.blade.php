<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RepairBox') - Mobile Shop Management</title>
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
<body class="h-full" x-data="{ mobileMenuOpen: false }">

<div class="page-loader" id="pageLoader">
    <div class="loader-circle"></div>
</div>

@php
    $shopIcon = \App\Models\Setting::getValue('shop_icon');
    $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');

    $navItems = [
        ['name' => 'Sales', 'route' => '/pos', 'match' => 'pos*', 'icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z'],
        ['name' => 'Repairs', 'route' => '/repairs', 'match' => 'repairs*', 'icon' => 'M11.42 15.17l-4.655-5.55a.776.776 0 010-1.06v0a.776.776 0 011.13 0l3.72 3.72 7.08-7.08a.776.776 0 011.06 0v0a.776.776 0 010 1.06L11.42 15.17z'],
        ['name' => 'Recharge', 'route' => '/recharges', 'match' => 'recharges*', 'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
        ['name' => 'Expenses', 'route' => '/expenses', 'match' => 'expenses*', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ['name' => 'Invoices', 'route' => '/invoices', 'match' => 'invoices*', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ['name' => 'Returns', 'route' => '/returns', 'match' => 'returns*', 'icon' => 'M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z'],
    ];
@endphp

<!-- Top Navigation Bar -->
<nav class="topnav no-print">
    <div class="flex items-center justify-between px-4 h-14">
        <!-- Left: Logo + Shop Name -->
        <a href="/dashboard" class="flex items-center gap-2.5 flex-shrink-0">
            @if($shopIcon)
                <div class="w-8 h-8 rounded-lg overflow-hidden flex items-center justify-center bg-white border border-gray-200">
                    <img src="{{ asset('storage/' . $shopIcon) }}" alt="Logo" class="w-full h-full object-contain">
                </div>
            @else
                <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <span class="font-bold text-gray-800 text-sm hidden sm:block">{{ Str::limit($shopName, 14) }}</span>
        </a>

        <!-- Center: Module Links (desktop) -->
        <div class="hidden md:flex items-center gap-1">
            @foreach($navItems as $item)
                <a href="{{ $item['route'] }}" class="topnav-link {{ request()->is($item['match']) ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                    <span>{{ $item['name'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Right: Settings + User + Logout -->
        <div class="flex items-center gap-2">
            <a href="/settings" class="topnav-link {{ request()->is('settings*') ? 'active' : '' }}" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="hidden lg:inline">Settings</span>
            </a>

            <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-gray-200">
                <div class="w-7 h-7 bg-primary-600 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <span class="text-xs font-medium text-gray-700 hidden lg:block">{{ auth()->user()->name ?? 'User' }}</span>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 rounded-lg hover:bg-gray-100 transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </button>
            </form>

            <!-- Mobile hamburger -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-1.5 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile dropdown menu -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-1" @click.away="mobileMenuOpen = false" class="md:hidden border-t border-gray-200 bg-white px-3 pb-3">
        @foreach($navItems as $item)
            <a href="{{ $item['route'] }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium {{ request()->is($item['match']) ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                {{ $item['name'] }}
            </a>
        @endforeach
    </div>
</nav>

<!-- Main Content -->
<div class="flex flex-col" style="min-height: calc(100vh - 56px);">
    <!-- Page Header -->
    <header class="bg-white border-b border-gray-200 no-print">
        <div class="flex items-center justify-between px-4 sm:px-6 py-3">
            <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 @yield('content-class')">
        @yield('content')
    </main>
</div>

<script>
window.RepairBox = {
    ajax: async function(url, method, data) {
        method = method || 'GET';
        try {
            var config = { url: url, method: method, headers: { 'X-Requested-With': 'XMLHttpRequest' } };
            if (data && method !== 'GET') config.data = data;
            if (method === 'GET' && data) config.params = data;
            var response = await axios(config);
            var d = response.data;
            if (d && typeof d === 'object' && Array.isArray(d.data) && 'current_page' in d) {
                return { data: d.data, meta: { current_page: d.current_page, last_page: d.last_page, total: d.total }, success: true };
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

    window.addEventListener('load', () => {
        setTimeout(() => {
            if (pageLoader.classList.contains('active')) {
                pageLoader.classList.remove('active');
                pageLoader.classList.add('hide');
                setTimeout(() => pageLoader.classList.remove('hide'), 300);
            }
        }, 200);
    });
}
</script>
@stack('scripts')
</body>
</html>
