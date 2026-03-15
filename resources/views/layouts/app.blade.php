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
<body class="h-full" x-data="{ sidebarOpen: window.innerWidth >= 1024 }" x-init="window.addEventListener('resize', () => { sidebarOpen = window.innerWidth >= 1024 })">

<div class="page-loader" id="pageLoader">
    <div class="loader-circle"></div>
</div>

<div class="flex h-screen overflow-hidden">
    <!-- Sidebar -->
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 z-50 w-64 bg-sidebar flex flex-col lg:relative lg:translate-x-0"
         @click.away="if(window.innerWidth < 1024) sidebarOpen = false">

        <!-- Logo -->
        @php
            $shopIcon = \App\Models\Setting::getValue('shop_icon');
            $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
        @endphp
        <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-700">
            @if($shopIcon)
                <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center justify-center bg-white">
                    <img src="{{ asset('storage/' . $shopIcon) }}" alt="Shop Icon" class="w-full h-full object-contain">
                </div>
            @else
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            <div>
                <h1 class="text-white font-bold text-lg">{{ Str::limit($shopName, 12) }}</h1>
                <p class="text-gray-400 text-xs">Shop Management</p>
            </div>
        </div>

        <!-- Navigation -->
        @php
            $menusBySection = \App\Models\Menu::getMenusForUser(auth()->user());
        @endphp
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
            @foreach($menusBySection as $section => $menus)
                @if($section && $section !== '0')
                    <div class="pt-3 pb-1 px-4"><p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $section }}</p></div>
                @endif
                @foreach($menus as $menu)
                    @php
                        $menuRoute = $menu['route'] ?? '';
                        $menuName = $menu['name'] ?? '';
                        $menuIcon = $menu['icon'] ?? 'M4 6h16M4 12h16M4 18h16';
                        $isActive = $menuRoute && request()->is(ltrim($menuRoute, '/') . '*');
                    @endphp
                    <a href="{{ $menuRoute ?: '#' }}" class="sidebar-link {{ $isActive ? 'active' : '' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menuIcon }}"/></svg>
                        <span>{{ $menuName }}</span>
                    </a>
                    @if(!empty($menu['children']))
                        @foreach($menu['children'] as $child)
                            @php
                                $childRoute = $child['route'] ?? '';
                                $childName = $child['name'] ?? '';
                                $childIcon = $child['icon'] ?? 'M9 5l7 7-7 7';
                                $childActive = $childRoute && request()->is(ltrim($childRoute, '/') . '*');
                            @endphp
                            <a href="{{ $childRoute ?: '#' }}" class="sidebar-link pl-12 {{ $childActive ? 'active' : '' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $childIcon }}"/></svg>
                                <span>{{ $childName }}</span>
                            </a>
                        @endforeach
                    @endif
                @endforeach
            @endforeach
        </nav>

        <!-- User info -->
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth()->user()->role->name ?? 'Staff' }}</p>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-white" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top bar -->
        <header class="bg-white shadow-sm border-b border-gray-200 no-print">
            <div class="flex items-center justify-between px-4 sm:px-6 py-3">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 lg:hidden">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 @yield('content-class')">
            @yield('content')
        </main>
    </div>
</div>

<!-- Overlay for mobile -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

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
            // Auto-unwrap Laravel paginated responses
            if (d && typeof d === 'object' && Array.isArray(d.data) && 'current_page' in d) {
                return { data: d.data, meta: { current_page: d.current_page, last_page: d.last_page, total: d.total }, success: true };
            }
            // Auto-unwrap {success, data, message} envelope responses
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
    formatCurrency: function(amount) { return new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(amount); }
};

// Page Loader
const pageLoader = document.getElementById('pageLoader');
if (pageLoader) {
    // Show loader on page navigation
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a');
        if (link && link.href && !link.href.includes('#') && link.target !== '_blank' && !link.classList.contains('no-loader')) {
            if (link.href.includes(window.location.origin) || link.href.startsWith('/')) {
                pageLoader.classList.add('active');
            }
        }
    });

    // Hide loader on page load
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
