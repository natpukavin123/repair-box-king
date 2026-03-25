@extends('layouts.app')
@section('page-title', 'Settings')

@section('content')
<div x-data="settingsPage()" x-init="init()">
    <div class="page-header-inline">
        <div class="page-header-inline-copy">
            <h2 class="page-header-inline-title">Settings</h2>
            <p class="page-header-inline-description">System preferences, master data, notification templates, and maintenance tools.</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="secondary-tabs">
        <button @click="tab='general'; updateUrl()" :class="tab==='general' ? 'secondary-tab is-active' : 'secondary-tab'">General</button>
        <button @click="tab='master-data'; updateUrl()" :class="tab==='master-data' ? 'secondary-tab is-active' : 'secondary-tab'">Master Data</button>
        <button @click="tab='email-templates'; updateUrl()" :class="tab==='email-templates' ? 'secondary-tab is-active' : 'secondary-tab'">Email Templates</button>
        <button @click="tab='notifications'; updateUrl(); loadNotifications()" :class="tab==='notifications' ? 'secondary-tab is-active' : 'secondary-tab'">Notifications</button>
        <button @click="tab='print-settings'; updateUrl()" :class="tab==='print-settings' ? 'secondary-tab is-active' : 'secondary-tab'">Print Settings</button>
        <button @click="tab='backups'; updateUrl()" :class="tab==='backups' ? 'secondary-tab is-active' : 'secondary-tab'">Backups</button>
        <button @click="tab='import'; updateUrl()" :class="tab==='import' ? 'secondary-tab is-active' : 'secondary-tab'">Import</button>
    </div>

    {{-- Master Data --}}
    <div x-show="tab==='master-data'" x-data="masterDataPanel()" x-init="switchSection(mdSection)">
        <style>
            .md-workspace { gap: 0.7rem; }
            .md-workspace .md-panel {
                border-radius: 1.35rem;
                border: 1px solid rgba(148, 163, 184, 0.16);
                background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(250,252,255,0.82));
                box-shadow: 0 26px 60px -42px rgba(15,23,42,0.38);
            }
            .md-workspace .md-panel .card-header {
                padding: 0.9rem 1rem;
                background: linear-gradient(180deg, rgba(255,255,255,0.72), rgba(241,245,255,0.48));
            }
            .md-workspace .md-menu-item {
                display: flex; align-items: center; gap: 12px; padding: 10px 14px;
                border-radius: 0.85rem; cursor: pointer; transition: all 0.2s ease;
                border: 1px solid transparent; font-size: 0.88rem; font-weight: 500; color: #475569;
            }
            .md-workspace .md-menu-item:hover { background: rgba(37,99,235,0.04); color: #1e293b; }
            .md-workspace .md-menu-item.is-active {
                background: linear-gradient(135deg, rgba(37,99,235,0.08), rgba(99,102,241,0.06));
                border-color: rgba(37,99,235,0.15); color: #2563eb; font-weight: 600;
            }
            .md-workspace .md-menu-icon {
                width: 34px; height: 34px; border-radius: 9px;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .md-workspace .md-table-shell { padding: 0.35rem 0.4rem 0.15rem; }
            .md-workspace .md-table-shell .data-table thead {
                background: linear-gradient(180deg, rgba(248,250,252,0.98), rgba(238,242,255,0.9));
            }
            .md-workspace .md-table-shell .data-table th {
                padding: 0.75rem 0.9rem; font-size: 0.65rem; letter-spacing: 0.14em;
            }
            .md-workspace .md-table-shell .data-table td {
                padding: 0.8rem 0.9rem; font-size: 0.88rem;
            }
            .md-workspace .md-table-shell .data-table tbody tr {
                border-top-color: rgba(226,232,240,0.92);
            }
            .md-workspace .md-table-shell .data-table tbody tr:hover {
                background: rgba(37,99,235,0.04);
            }
            .md-workspace .md-search-input,
            .md-workspace .md-form-input {
                min-height: 2.7rem; border-radius: 0.95rem;
                border-color: rgba(148,163,184,0.22);
                background: rgba(255,255,255,0.94);
                box-shadow: inset 0 1px 0 rgba(255,255,255,0.7), 0 12px 28px -24px rgba(15,23,42,0.28);
            }
            .md-workspace .md-toolbar {
                border: 1px solid rgba(148,163,184,0.18); border-radius: 1.2rem;
                background: linear-gradient(135deg, rgba(255,255,255,0.96), rgba(244,247,255,0.88));
                box-shadow: 0 18px 42px -34px rgba(15,23,42,0.34);
                backdrop-filter: blur(16px); padding: 0.55rem;
            }
            .md-workspace .md-form-scroll > div { padding: 0.95rem 1rem; }
            @media (max-width: 1023px) {
                .md-workspace { gap: 0.6rem; }
                .md-workspace .md-panel .card-header,
                .md-workspace .md-table-shell .data-table th,
                .md-workspace .md-table-shell .data-table td { padding-left: 0.75rem; padding-right: 0.75rem; }
            }
            @media (max-width: 767px) {
                .md-workspace { gap: 0.5rem; }
                .md-workspace .md-panel { border-radius: 1.1rem; }
                .md-workspace .md-search-input, .md-workspace .md-form-input { min-height: 2.5rem; border-radius: 0.82rem; }
            }
        </style>

        <div class="md-workspace" style="display:grid; grid-template-columns: 260px 1fr; gap: 0.8rem; min-height: 70vh;">

            {{-- ===== LEFT: Menu Sidebar ===== --}}
            <div class="md-panel flex flex-col" style="height: fit-content; position: sticky; top: 1rem;">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">Master Data</h3>
                </div>
                <div class="p-2 space-y-0.5">
                    <button @click="switchSection('vendors')" :class="mdSection==='vendors' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#f97316,#c2410c);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span>Vendor Management</span>
                    </button>
                    <button @click="switchSection('inventory')" :class="mdSection==='inventory' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span>Inventory</span>
                    </button>
                    <button @click="switchSection('brands')" :class="mdSection==='brands' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#ec4899,#db2777);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        </div>
                        <span>Brands</span>
                    </button>
                    <button @click="switchSection('categories')" :class="mdSection==='categories' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#14b8a6,#0d9488);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <span>Categories</span>
                    </button>
                    <button @click="switchSection('parts')" :class="mdSection==='parts' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span>Parts</span>
                    </button>
                    <button @click="switchSection('products')" :class="mdSection==='products' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span>Products</span>
                    </button>
                    <button @click="switchSection('customers')" :class="mdSection==='customers' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Customers</span>
                    </button>
                    <button @click="switchSection('recharge-providers')" :class="mdSection==='recharge-providers' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span>Recharge Providers</span>
                    </button>
                    <button @click="switchSection('services')" :class="mdSection==='services' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span>Services</span>
                    </button>
                </div>
            </div>

            {{-- ===== RIGHT: Content Area ===== --}}
            <div class="flex flex-col gap-3">

                {{-- Search toolbar --}}
                <div class="md-toolbar flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="mdSearch" @input.debounce.400ms="loadMdData()" type="text"
                            class="form-input-custom md-search-input pl-10 pr-10 w-full text-sm" :placeholder="'Search ' + mdSectionLabel + '...'">
                        <button x-show="mdSearch" @click="mdSearch = ''; loadMdData()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <button @click="openMdAdd()" class="btn-primary px-4 py-2.5 text-sm font-semibold rounded-xl whitespace-nowrap">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add <span x-text="mdSectionLabelSingular"></span>
                    </button>
                </div>

                {{-- Data Table --}}
                <div class="card md-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                    {{-- Overlay loader --}}
                    <div x-show="mdLoading" x-cloak x-transition.opacity
                        class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span class="text-xs text-gray-400 font-medium">Loading...</span>
                        </div>
                    </div>

                    <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                        <h3 class="font-semibold text-gray-800 text-sm">
                            <span x-text="mdSectionLabel"></span> (<span x-text="mdItems.length"></span>)
                        </h3>
                        <button @click="loadMdData()" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                    </div>

                    <div class="md-table-shell min-h-0 flex-1 overflow-hidden">
                        <div class="h-full overflow-y-auto overscroll-contain" style="max-height: 60vh;">

                        {{-- Vendors Table --}}
                        <template x-if="mdSection==='vendors'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Phone</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Specialization</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.phone || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.specialization || '-'"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="(item.status||'active')==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                    x-text="item.status||'active'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="6" class="text-center py-12">
                                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            <p class="text-gray-400 font-medium">No vendors found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Inventory Table --}}
                        <template x-if="mdSection==='inventory'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Product</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Stock</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Reserved</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.product ? item.product.name : (item.name || '-')"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.current_stock ?? item.stock ?? 0"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.reserved_stock ?? 0"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="(item.current_stock ?? item.stock ?? 0) > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                    x-text="(item.current_stock ?? item.stock ?? 0) > 0 ? 'In Stock' : 'Out of Stock'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500" x-text="item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '-'"></td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="6" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No inventory data found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Brands Table --}}
                        <template x-if="mdSection==='brands'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Logo</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2">
                                                <template x-if="item.logo_url">
                                                    <img :src="item.logo_url" class="w-8 h-8 rounded object-contain">
                                                </template>
                                                <template x-if="!item.logo_url">
                                                    <span class="text-gray-300 text-sm">-</span>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="4" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No brands found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Categories Table --}}
                        <template x-if="mdSection==='categories'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Description</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm text-gray-500" x-text="item.description || '-'"></td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="4" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No categories found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Parts Table --}}
                        <template x-if="mdSection==='parts'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">SKU</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Cost Price</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Selling Price</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.sku || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.cost_price ? RepairBox.formatCurrency(item.cost_price) : '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.selling_price ? RepairBox.formatCurrency(item.selling_price) : '-'"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="(item.status||'active')==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                    x-text="item.status||'active'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="7" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No parts found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Products Table --}}
                        <template x-if="mdSection==='products'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">SKU</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Category</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">MRP</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Sale Price</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.sku || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.category ? item.category.name : '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.mrp ? RepairBox.formatCurrency(item.mrp) : '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.selling_price ? RepairBox.formatCurrency(item.selling_price) : '-'"></td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="7" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No products found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Customers Table --}}
                        <template x-if="mdSection==='customers'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Mobile</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Email</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Loyalty Pts</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.mobile_number || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.email || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.loyalty_points || 0"></td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="6" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No customers found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Recharge Providers Table --}}
                        <template x-if="mdSection==='recharge-providers'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Type</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Commission %</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="border-t border-gray-100 hover:bg-gray-50/60 transition cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.provider_type"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.commission_percentage + '%'"></td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="5" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No recharge providers found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Services Table --}}
                        <template x-if="mdSection==='services'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Default Price</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Description</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <template x-if="item.thumbnail">
                                                        <img :src="'/storage/' + item.thumbnail" class="w-7 h-7 rounded object-cover">
                                                    </template>
                                                    <span x-text="item.name"></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm" x-text="item.default_price ? RepairBox.formatCurrency(item.default_price) : '-'"></td>
                                            <td class="px-3 py-2 text-sm text-gray-500" x-text="item.description ? item.description.substring(0,50) + (item.description.length > 50 ? '...' : '') : '-'"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="(item.status||'active')==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                    x-text="item.status||'active'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="6" class="text-center py-12">
                                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                            <p class="text-gray-400 font-medium">No services found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add/Edit Modal --}}
        <div x-show="showMdModal" class="modal-overlay" x-cloak @click.self="showMdModal=false" style="z-index:50;">
            <div class="modal-container">
                <div class="modal-header">
                    <h3 class="text-lg font-semibold" x-text="mdEditing ? 'Edit ' + mdSectionLabelSingular : 'Add ' + mdSectionLabelSingular"></h3>
                    <button @click="showMdModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>
                <div class="modal-body space-y-4">
                    {{-- Vendor Form --}}
                    <template x-if="mdSection==='vendors'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Vendor name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input x-model="mdForm.phone" type="text" class="form-input-custom" placeholder="Phone number"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label>
                                <input x-model="mdForm.specialization" type="text" class="form-input-custom" placeholder="e.g. Mobile repair"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea x-model="mdForm.address" class="form-input-custom" rows="2" placeholder="Address"></textarea></div>
                            <template x-if="mdEditing">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select x-model="mdForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                            </template>
                        </div>
                    </template>

                    {{-- Brand Form --}}
                    <template x-if="mdSection==='brands'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Brand name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
                                <input x-model="mdForm.logo_url" type="text" class="form-input-custom" placeholder="https://..."></div>
                        </div>
                    </template>

                    {{-- Category Form --}}
                    <template x-if="mdSection==='categories'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Category name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="mdForm.description" class="form-input-custom" rows="2" placeholder="Description"></textarea></div>
                        </div>
                    </template>

                    {{-- Parts Form --}}
                    <template x-if="mdSection==='parts'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Part name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                <input x-model="mdForm.sku" type="text" class="form-input-custom" placeholder="SKU code"></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                                    <input x-model="mdForm.cost_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                                    <input x-model="mdForm.selling_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                            </div>
                            <template x-if="mdEditing">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select x-model="mdForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                            </template>
                        </div>
                    </template>

                    {{-- Products Form --}}
                    <template x-if="mdSection==='products'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Product name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                <input x-model="mdForm.sku" type="text" class="form-input-custom" placeholder="SKU code"></div>
                            <div class="grid grid-cols-2 gap-3">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select x-model="mdForm.category_id" class="form-select-custom">
                                        <option value="">Select</option>
                                        <template x-for="cat in mdCategories" :key="cat.id">
                                            <option :value="cat.id" x-text="cat.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                                    <select x-model="mdForm.brand_id" class="form-select-custom">
                                        <option value="">Select</option>
                                        <template x-for="b in mdBrands" :key="b.id">
                                            <option :value="b.id" x-text="b.name"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label>
                                    <input x-model="mdForm.purchase_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">MRP *</label>
                                    <input x-model="mdForm.mrp" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                                    <input x-model="mdForm.selling_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="mdForm.description" class="form-input-custom" rows="2" placeholder="Description"></textarea></div>
                        </div>
                    </template>

                    {{-- Customers Form --}}
                    <template x-if="mdSection==='customers'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Customer name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label>
                                <input x-model="mdForm.mobile_number" type="text" class="form-input-custom" placeholder="10-digit mobile number"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input x-model="mdForm.email" type="email" class="form-input-custom" placeholder="Email address"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <textarea x-model="mdForm.address" class="form-input-custom" rows="2" placeholder="Address"></textarea></div>
                        </div>
                    </template>

                    {{-- Inventory Form --}}
                    <template x-if="mdSection==='inventory'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select x-model="mdForm.product_id" class="form-select-custom">
                                    <option value="">Select product</option>
                                    <template x-for="p in mdProducts" :key="p.id">
                                        <option :value="p.id" x-text="p.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type *</label>
                                <select x-model="mdForm.adjustment_type" class="form-select-custom">
                                    <option value="addition">Addition</option>
                                    <option value="subtraction">Subtraction</option>
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input x-model="mdForm.quantity" type="number" min="1" class="form-input-custom" placeholder="Qty"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                                <textarea x-model="mdForm.reason" class="form-input-custom" rows="2" placeholder="Reason for adjustment"></textarea></div>
                        </div>
                    </template>

                    {{-- Recharge Provider Form --}}
                    <template x-if="mdSection==='recharge-providers'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Provider name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select x-model="mdForm.provider_type" class="form-select-custom">
                                    <option value="">Select</option>
                                    <option value="mobile">Mobile</option>
                                    <option value="dth">DTH</option>
                                    <option value="data_card">Data Card</option>
                                    <option value="electricity">Electricity</option>
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Commission % *</label>
                                <input x-model="mdForm.commission_percentage" type="number" step="0.01" min="0" max="100" class="form-input-custom" placeholder="e.g. 3.5"></div>
                        </div>
                    </template>

                    {{-- Services Form --}}
                    <template x-if="mdSection==='services'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Service name"></div>

                            {{-- Quick fill suggestions for name (only when creating) --}}
                            <div x-show="!mdEditing" x-cloak>
                                <p class="text-xs font-medium text-gray-500 mb-1.5">Quick fill</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="tag in svcQuickFillTags" :key="tag">
                                        <button type="button" @click="mdForm.name = tag"
                                            :class="mdForm.name === tag ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                            class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                                            x-text="tag"></button>
                                    </template>
                                </div>
                            </div>

                            {{-- Quick fill tags for POS --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quick Fill Options <span class="text-gray-400 font-normal">(shown in POS)</span></label>
                                <div class="flex flex-wrap gap-1.5 mb-2">
                                    <template x-for="(qf, qi) in (mdForm.quick_fills || [])" :key="qi">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            <span x-text="qf"></span>
                                            <button type="button" @click="mdForm.quick_fills.splice(qi, 1)" class="text-indigo-400 hover:text-red-500 leading-none">&times;</button>
                                        </span>
                                    </template>
                                </div>
                                <div class="flex gap-2">
                                    <input x-model="svcNewQuickFill" type="text" class="form-input-custom text-sm flex-1" placeholder="Type quick fill option & press Enter"
                                        @keydown.enter.prevent="if(svcNewQuickFill.trim()) { if(!mdForm.quick_fills) mdForm.quick_fills=[]; mdForm.quick_fills.push(svcNewQuickFill.trim()); svcNewQuickFill=''; }">
                                    <button type="button" class="btn-secondary text-xs px-3"
                                        @click="if(svcNewQuickFill.trim()) { if(!mdForm.quick_fills) mdForm.quick_fills=[]; mdForm.quick_fills.push(svcNewQuickFill.trim()); svcNewQuickFill=''; }">Add</button>
                                </div>
                            </div>

                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Default Price</label>
                                <input x-model="mdForm.default_price" type="number" step="0.01" min="0" class="form-input-custom" placeholder="0.00"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="mdForm.description" class="form-input-custom" rows="2" placeholder="Service description"></textarea></div>
                            <template x-if="mdEditing">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select x-model="mdForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="modal-footer">
                    <button @click="showMdModal=false" class="btn-secondary">Cancel</button>
                    <button @click="saveMdItem()" class="btn-primary" :disabled="mdSaving">
                        <span x-show="mdSaving" class="spinner mr-1"></span>
                        <span x-text="mdEditing ? 'Update' : 'Save'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- General Settings --}}
    <div x-show="tab==='general'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">General Settings</h3></div>
        <div class="card-body space-y-8">
            <div class="rounded-[28px] border border-white/60 bg-white/80 p-5 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)] backdrop-blur">
                <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-slate-500">Appearance</p>
                        <h4 class="mt-2 text-xl font-semibold text-slate-900">Theme Studio</h4>
                        <p class="mt-1 max-w-2xl text-sm text-slate-500">Control the overall app mood, top-bar polish, and motion style. Changes preview immediately and are saved for all pages.</p>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-medium text-slate-600">
                        <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                        Live preview enabled
                    </div>
                </div>

                <div class="mt-5 grid gap-4 xl:grid-cols-3">
                    <template x-for="theme in appearanceThemes" :key="theme.id">
                        <button
                            type="button"
                            @click="settings.ui_theme = theme.id; applyAppearancePreview()"
                            class="group rounded-[24px] border p-4 text-left transition duration-300"
                            :class="settings.ui_theme === theme.id ? 'border-slate-900 bg-slate-950 text-white shadow-[0_24px_70px_-30px_rgba(15,23,42,0.8)]' : 'border-slate-200 bg-white text-slate-900 hover:-translate-y-1 hover:border-slate-300 hover:shadow-[0_18px_50px_-30px_rgba(15,23,42,0.35)]'"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm font-semibold" x-text="theme.name"></div>
                                    <div class="mt-1 text-xs" :class="settings.ui_theme === theme.id ? 'text-white/70' : 'text-slate-500'" x-text="theme.description"></div>
                                </div>
                                <div class="rounded-full px-2.5 py-1 text-[11px] font-semibold uppercase tracking-[0.22em]"
                                     :class="settings.ui_theme === theme.id ? 'bg-white/10 text-white' : 'bg-slate-100 text-slate-500'">Theme</div>
                            </div>

                            <div class="mt-5 rounded-[20px] p-4" :style="theme.preview">
                                <div class="flex items-center justify-between rounded-2xl border border-white/20 bg-white/10 px-3 py-3 backdrop-blur-sm">
                                    <div>
                                        <div class="text-[11px] uppercase tracking-[0.24em] text-white/70">Preview</div>
                                        <div class="mt-1 text-sm font-semibold text-white">Dashboard Shell</div>
                                    </div>
                                    <div class="flex gap-2">
                                        <span class="h-3 w-3 rounded-full bg-white/80"></span>
                                        <span class="h-3 w-3 rounded-full bg-white/45"></span>
                                        <span class="h-3 w-3 rounded-full bg-white/25"></span>
                                    </div>
                                </div>
                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <span class="h-16 rounded-2xl border border-white/10 bg-white/15"></span>
                                    <span class="h-16 rounded-2xl border border-white/10 bg-black/10"></span>
                                    <span class="h-16 rounded-2xl border border-white/10 bg-white/10"></span>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <div class="mt-5 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="form-label">Motion Style</label>
                        <select x-model="settings.ui_motion" @change="applyAppearancePreview()" class="form-select-custom">
                            <option value="enhanced">Enhanced</option>
                            <option value="reduced">Reduced</option>
                            <option value="none">Off</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-500">Enhanced adds page transitions and hover movement. Reduced keeps the app calmer. Off removes decorative motion.</p>
                    </div>
                    <div class="rounded-[22px] border border-slate-200 bg-slate-50/80 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Current Selection</p>
                        <div class="mt-3 flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-sm">
                            <div>
                                <div class="text-sm font-semibold text-slate-900" x-text="selectedTheme.name"></div>
                                <div class="text-xs text-slate-500" x-text="selectedTheme.description"></div>
                            </div>
                            <div class="text-right">
                                <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Motion</div>
                                <div class="mt-1 text-sm font-semibold text-slate-700" x-text="formatMotionLabel(settings.ui_motion)"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="key in settingKeys" :key="key">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1" x-text="formatLabel(key)"></label>
                        <input :value="settings[key] || ''" @input="settings[key] = $event.target.value" type="text" class="form-input-custom">
                    </div>
                </template>
            </div>

            {{-- Shop Icon Upload --}}
            <div class="mt-6 pt-6 border-t">
                <h4 class="text-md font-semibold mb-4">Shop Logo</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Icon</label>
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <input type="file" @change="handleIconUpload" accept="image/*" class="form-input-custom">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB. PNG, JPG, GIF, SVG</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center">
                        <div x-show="previewIcon" class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img :src="previewIcon" class="w-full h-full object-contain" alt="Preview">
                        </div>
                        <div x-show="!previewIcon && settings.shop_icon" class="w-24 h-24 border rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                            <img :src="getIconUrl()" class="w-full h-full object-contain" alt="Shop Icon" x-on:error="$el.parentElement.style.display='none'" @load="console.log('Icon loaded:', getIconUrl())">
                        </div>
                        <div x-show="!previewIcon && !settings.shop_icon" class="w-24 h-24 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button>
            </div>
        </div>
    </div>

    {{-- Email Templates --}}
    <div x-show="tab==='email-templates'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">Email Templates</h3></div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Subject</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="et in emailTemplates" :key="et.id">
                        <tr>
                            <td class="font-medium" x-text="et.template_name"></td>
                            <td x-text="et.subject"></td>
                            <td><span class="badge" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span></td>
                            <td><button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="emailTemplates.length===0"><td colspan="4" class="text-center text-gray-400 py-6">No templates</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         Notifications
    ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='notifications'" x-cloak>

        {{-- ─── Email Notifications ─────────────────────── --}}
        <div class="card mb-6">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-indigo-100">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold">Email Notifications</h3>
            </div>
            <div class="card-body space-y-5">
                <p class="text-sm text-gray-500">Automatically send emails to customers when their repair status changes. Configure SMTP settings in <code class="bg-gray-100 px-1 rounded">.env</code> or under your hosting mail settings.</p>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_received === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_email_received === '1'" @change="settings.notify_email_received = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-indigo-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Order Received</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send email when a repair ticket is created.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_completed === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_email_completed === '1'" @change="settings.notify_email_completed = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-indigo-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Repair Completed</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send email when a repair is marked as completed.</p>
                        </div>
                    </label>
                </div>

                {{-- Variable Reference --}}
                <details class="text-sm">
                    <summary class="cursor-pointer text-indigo-600 font-medium select-none">Available template variables</summary>
                    <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-x-4 gap-y-1 font-mono text-xs text-gray-600">
                            <span>{customer_name}</span><span>{ticket_number}</span><span>{tracking_id}</span>
                            <span>{tracking_url}</span><span>{device_brand}</span><span>{device_model}</span>
                            <span>{estimated_cost}</span><span>{service_charge}</span><span>{grand_total}</span>
                            <span>{expected_delivery_date}</span><span>{technician_name}</span><span>{status}</span>
                            <span>{shop_name}</span><span>{shop_phone}</span>
                        </div>
                    </div>
                </details>

                {{-- Email Templates quick-edit --}}
                <div>
                    <h4 class="font-semibold text-gray-700 mb-3">Email Templates</h4>
                    <div class="space-y-4">
                        <template x-for="et in emailTemplates.filter(t => ['repair_received','repair_completed'].includes(t.template_name))" :key="et.id">
                            <div class="border border-gray-200 rounded-xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" :class="et.template_name === 'repair_received' ? 'bg-blue-500' : 'bg-emerald-500'"></span>
                                        <span class="font-medium text-sm" x-text="et.template_name === 'repair_received' ? '📥 Order Received' : '✅ Repair Completed'"></span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="badge" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span>
                                        <button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Edit</button>
                                    </div>
                                </div>
                                <div class="px-4 py-3">
                                    <p class="text-xs text-gray-500 mb-1 uppercase tracking-wide font-semibold">Subject</p>
                                    <p class="text-sm text-gray-700 font-mono" x-text="et.subject || '(no subject)'"></p>
                                </div>
                            </div>
                        </template>
                        <p x-show="emailTemplates.filter(t => ['repair_received','repair_completed'].includes(t.template_name)).length === 0"
                           class="text-sm text-gray-400">No repair email templates found. Run migrations to seed default templates.</p>
                    </div>
                </div>

                <div><button @click="saveNotificationSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Email Settings</button></div>
            </div>
        </div>

        {{-- ─── WhatsApp Notifications ─────────────────────── --}}
        <div class="card">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-100">
                    <svg class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="text-lg font-semibold">WhatsApp Notifications</h3>
            </div>
            <div class="card-body space-y-5">
                <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Works with any HTTP WhatsApp gateway like <strong>Ultramsg</strong>, <strong>2chat</strong>, or <strong>WA-Gateway</strong>. Enter the API URL, token, and your sender number below.</span>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_received === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_whatsapp_received === '1'" @change="settings.notify_whatsapp_received = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-green-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Order Received</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send WhatsApp when a repair ticket is created.</p>
                        </div>
                    </label>
                    <label class="flex items-start gap-3 p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_completed === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                        <input type="checkbox" :checked="settings.notify_whatsapp_completed === '1'" @change="settings.notify_whatsapp_completed = $event.target.checked ? '1' : '0'" class="mt-0.5 h-4 w-4 accent-green-600">
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Repair Completed</p>
                            <p class="text-xs text-gray-500 mt-0.5">Send WhatsApp when a repair is marked as completed.</p>
                        </div>
                    </label>
                </div>

                {{-- API Config --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Endpoint URL</label>
                        <input x-model="settings.whatsapp_api_url" type="url" class="form-input-custom" placeholder="https://api.ultramsg.com/instanceXXXX">
                        <p class="text-xs text-gray-400 mt-1">Base URL – the system appends <code class="bg-gray-100 px-1 rounded">/sendMessage</code> automatically.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">API Token / Secret</label>
                        <input x-model="settings.whatsapp_api_token" type="password" class="form-input-custom" placeholder="••••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From Number / Instance ID <span class="text-gray-400 font-normal">(optional)</span></label>
                        <input x-model="settings.whatsapp_from_number" type="text" class="form-input-custom" placeholder="919876543210">
                        <p class="text-xs text-gray-400 mt-1">Some providers need the sender number or instance ID.</p>
                    </div>
                </div>

                {{-- WhatsApp Templates --}}
                <div class="space-y-4">
                    <h4 class="font-semibold text-gray-700">Message Templates</h4>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span class="font-medium text-sm">📥 Order Received Message</span>
                        </div>
                        <div class="p-4">
                            <textarea x-model="settings.whatsapp_template_received" class="form-input-custom font-mono text-sm" rows="7"
                                      placeholder="Hello {customer_name}! Your device has been received..."></textarea>
                            <p class="text-xs text-gray-400 mt-1">Use the same <code class="bg-gray-100 px-1 rounded">{variable}</code> placeholders as email templates.</p>
                        </div>
                    </div>

                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 border-b border-gray-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="font-medium text-sm">✅ Repair Completed Message</span>
                        </div>
                        <div class="p-4">
                            <textarea x-model="settings.whatsapp_template_completed" class="form-input-custom font-mono text-sm" rows="7"
                                      placeholder="Hello {customer_name}! Your device is ready for pickup..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button @click="saveNotificationSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save WhatsApp Settings</button>
                    <button @click="showTestNotifyModal=true" class="btn-secondary text-sm">🧪 Send Test Message</button>
                </div>
            </div>
        </div>
    </div>
    {{-- /Notifications --}}

    {{-- ══════════════════════════════════════════════════════════
         Print Settings (all print types in one place)
    ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='print-settings'" class="space-y-6">

        {{-- ─── SALES INVOICE ─── --}}
        <div class="card">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-100">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-lg font-semibold">Sales Invoice</h3>
            </div>
            <div class="card-body space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Default Print Language</label>
                        <select x-model="settings.invoice_default_language" class="form-select-custom w-full">
                            <option value="en">English</option>
                            <option value="ta">Tamil (தமிழ்)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pre-selected when print dialog appears. Can be changed at print time.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paper Size</label>
                        <select x-model="settings.invoice_paper_size" class="form-select-custom w-full">
                            <option value="A4_landscape">A4 Landscape (half page)</option>
                            <option value="A5">A5 Portrait</option>
                        </select>
                    </div>
                </div>

                {{-- Header Titles --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Header Titles</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Title (English)</label>
                            <input type="text" x-model="settings.invoice_header_title_en" class="form-input-custom" placeholder="Sales Invoice">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Title (Tamil)</label>
                            <input type="text" x-model="settings.invoice_header_title_ta" class="form-input-custom" placeholder="விற்பனை இரசீது">
                        </div>
                    </div>
                </div>

                {{-- Shop Info (Tamil variants) --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Tamil Shop Info</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name (Tamil)</label>
                            <input type="text" x-model="settings.invoice_shop_name_ta" class="form-input-custom" placeholder="Leave blank to use English name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Slogan (Tamil)</label>
                            <input type="text" x-model="settings.invoice_shop_slogan_ta" class="form-input-custom" placeholder="உங்கள் நம்பகமான மொபைல் பார்ட்னர்">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Address (Tamil)</label>
                            <input type="text" x-model="settings.invoice_shop_address_ta" class="form-input-custom" placeholder="Leave blank to use English address">
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Footer Text</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Footer (English)</label>
                            <textarea x-model="settings.invoice_footer_text" class="form-input-custom" rows="2" placeholder="Subject to jurisdiction..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Footer (Tamil)</label>
                            <textarea x-model="settings.invoice_footer_text_ta" class="form-input-custom" rows="2" placeholder="நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Signature --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Signature Labels</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signature Label (English)</label>
                            <input type="text" x-model="settings.invoice_sign_label_en" class="form-input-custom" placeholder="Authorised Signatory">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signature Label (Tamil)</label>
                            <input type="text" x-model="settings.invoice_sign_label_ta" class="form-input-custom" placeholder="அங்கீகரிக்கப்பட்ட கையொப்பம்">
                        </div>
                    </div>
                </div>

                <div><button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button></div>
            </div>
        </div>

        {{-- ─── REPAIR RECEIPT ─── --}}
        <div class="card">
            <div class="card-header flex items-center gap-3">
                <div class="w-9 h-9 flex items-center justify-center rounded-lg bg-emerald-100">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <h3 class="text-lg font-semibold">Repair Receipt</h3>
            </div>
            <div class="card-body space-y-5">

                {{-- Header Titles --}}
                <div>
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Header Titles</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Title (English)</label>
                            <input type="text" x-model="settings.receipt_header_title_en" class="form-input-custom" placeholder="Repair Receipt">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Title (Tamil)</label>
                            <input type="text" x-model="settings.receipt_header_title_ta" class="form-input-custom" placeholder="பழுதுபார்ப்பு ரசீது">
                        </div>
                    </div>
                </div>

                {{-- Shop Info (Tamil variants) --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Tamil Shop Info</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name (Tamil)</label>
                            <input type="text" x-model="settings.receipt_shop_name_ta" class="form-input-custom" placeholder="Leave blank to use English name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Slogan (Tamil)</label>
                            <input type="text" x-model="settings.receipt_shop_slogan_ta" class="form-input-custom" placeholder="உங்கள் நம்பகமான மொபைல் பார்ட்னர்">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Address (Tamil)</label>
                            <input type="text" x-model="settings.receipt_shop_address_ta" class="form-input-custom" placeholder="Leave blank to use English address">
                        </div>
                    </div>
                </div>

                {{-- Important Notes --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Important Notes <span class="text-xs text-gray-400 font-normal">(printed on receipt, one per line)</span></h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (English)</label>
                            <textarea x-model="settings.receipt_notes_en" class="form-input-custom" rows="4" placeholder="Keep this receipt to claim your device.&#10;Estimated cost may change upon diagnosis.&#10;Data backup is customer's responsibility.&#10;Unclaimed devices after 30 days — not our liability."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Tamil)</label>
                            <textarea x-model="settings.receipt_notes_ta" class="form-input-custom" rows="4" placeholder="உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.&#10;மதிப்பீட்டுச் செலவு ஆய்வுக்குப் பிறகு மாறலாம்."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Footer Text</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Footer (English)</label>
                            <textarea x-model="settings.receipt_footer_text" class="form-input-custom" rows="2" placeholder="Keep this receipt to claim your device..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Footer (Tamil)</label>
                            <textarea x-model="settings.receipt_footer_text_ta" class="form-input-custom" rows="2" placeholder="உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- Signature --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Signature Labels</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signature Label (English)</label>
                            <input type="text" x-model="settings.receipt_sign_label_en" class="form-input-custom" placeholder="Authorised Signatory">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Signature Label (Tamil)</label>
                            <input type="text" x-model="settings.receipt_sign_label_ta" class="form-input-custom" placeholder="அங்கீகரிக்கப்பட்ட கையொப்பம்">
                        </div>
                    </div>
                </div>

                {{-- Repair Invoice --}}
                <div class="border-t pt-5">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wider mb-3">Repair Invoice Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Repair Invoice Title (English)</label>
                            <input type="text" x-model="settings.repair_invoice_header_title_en" class="form-input-custom" placeholder="Repair Invoice">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Repair Invoice Title (Tamil)</label>
                            <input type="text" x-model="settings.repair_invoice_header_title_ta" class="form-input-custom" placeholder="பழுதுபார்ப்பு இரசீது">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Repair Invoice Footer (English)</label>
                            <textarea x-model="settings.repair_invoice_footer_text" class="form-input-custom" rows="2" placeholder="Subject to jurisdiction..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Repair Invoice Footer (Tamil)</label>
                            <textarea x-model="settings.repair_invoice_footer_text_ta" class="form-input-custom" rows="2" placeholder="நீதிமன்ற அதிகார வரம்புக்கு..."></textarea>
                        </div>
                    </div>
                </div>

                <div><button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button></div>
            </div>
        </div>
    </div>

    {{-- Backups --}}
    <div x-show="tab==='backups'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Backups</h3>
            <button @click="createBackup()" class="btn-primary text-sm" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Create Backup</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Type</th><th>File</th><th>Size</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="b in backups" :key="b.id">
                        <tr>
                            <td x-text="b.backup_type"></td>
                            <td class="text-sm" x-text="b.file_path"></td>
                            <td x-text="b.file_size ? (b.file_size > 1048576 ? (b.file_size / 1048576).toFixed(1)+' MB' : (b.file_size / 1024).toFixed(1)+' KB') : '-'"></td>
                            <td><span class="badge badge-success" x-text="b.status"></span></td>
                            <td x-text="new Date(b.created_at).toLocaleString()"></td>
                            <td>
                                <a :href="'/backups/' + b.id + '/download'" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors" title="Download Backup">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="backups.length===0"><td colspan="6" class="text-center text-gray-400 py-6">No backups</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Import Tab --}}
    <div x-show="tab==='import'" x-cloak>
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Import Master Data</h3>
                <p class="text-sm text-gray-500 mt-1">Upload a CSV file to create or update records in bulk. The file will be validated first before importing.</p>
            </div>
            <div class="card-body space-y-6">

                {{-- Step 1: Select type and upload --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Import Type</label>
                        <select x-model="importType" class="form-select-custom" @change="resetImport()">
                            <option value="">-- Select type --</option>
                            <option value="brands">Brands</option>
                            <option value="categories">Categories</option>
                            <option value="customers">Customers</option>
                            <option value="products">Products</option>
                            <option value="parts">Parts</option>
                            <option value="vendors">Vendors</option>
                            <option value="recharge_providers">Recharge Providers</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                        <input type="file" accept=".csv" @change="importFile = $event.target.files[0]"
                            class="form-input-custom text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                            x-ref="importFileInput">
                    </div>
                </div>

                {{-- Expected columns hint --}}
                <div x-show="importType" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Expected CSV Columns</h4>
                    <p class="text-xs text-blue-700" x-text="getExpectedColumns()"></p>
                    <p class="text-xs text-blue-600 mt-1">
                        <span class="font-medium">Matching key:</span>
                        <span x-text="getUniqueKeyLabel()"></span> — existing records with the same value will be <strong>updated</strong>, new ones will be <strong>created</strong>.
                    </p>
                </div>

                {{-- Validate button --}}
                <div class="flex gap-3">
                    <button @click="validateImport()" class="btn-primary px-5 py-2.5 text-sm font-semibold rounded-xl"
                        :disabled="!importType || !importFile || importValidating">
                        <span x-show="importValidating" class="spinner mr-1"></span>
                        Validate File
                    </button>
                    <button x-show="importType || importFile" @click="resetImport(); $refs.importFileInput.value=''" class="btn-secondary px-4 py-2.5 text-sm rounded-xl">
                        Reset
                    </button>
                </div>

                {{-- Validation Results --}}
                <div x-show="importResults" x-cloak>
                    {{-- Summary --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        <div class="bg-gray-50 rounded-xl p-4 text-center border">
                            <div class="text-2xl font-bold text-gray-800" x-text="importSummary.total"></div>
                            <div class="text-xs text-gray-500 mt-1">Total Rows</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-4 text-center border border-green-200">
                            <div class="text-2xl font-bold text-green-700" x-text="importSummary.creates"></div>
                            <div class="text-xs text-green-600 mt-1">New Records</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4 text-center border border-blue-200">
                            <div class="text-2xl font-bold text-blue-700" x-text="importSummary.updates"></div>
                            <div class="text-xs text-blue-600 mt-1">Updates</div>
                        </div>
                        <div class="rounded-xl p-4 text-center border" :class="importSummary.errors > 0 ? 'bg-red-50 border-red-200' : 'bg-gray-50'">
                            <div class="text-2xl font-bold" :class="importSummary.errors > 0 ? 'text-red-700' : 'text-gray-400'" x-text="importSummary.errors"></div>
                            <div class="text-xs mt-1" :class="importSummary.errors > 0 ? 'text-red-600' : 'text-gray-500'">Errors</div>
                        </div>
                    </div>

                    {{-- Row details table --}}
                    <div class="border rounded-xl overflow-hidden">
                        <div class="max-h-[400px] overflow-y-auto">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Row</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Action</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Data</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="r in importResults" :key="r.row">
                                        <tr class="border-t" :class="r.errors.length > 0 ? 'bg-red-50/50' : ''">
                                            <td class="px-3 py-2 text-sm font-mono" x-text="r.row"></td>
                                            <td class="px-3 py-2">
                                                <span class="badge text-xs" :class="r.action==='create' ? 'badge-success' : 'badge-info'" x-text="r.action"></span>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-600">
                                                <template x-for="(val, key) in r.data" :key="key">
                                                    <span class="inline-block mr-2">
                                                        <span class="font-medium text-gray-700" x-text="key + ': '"></span>
                                                        <span x-text="val || '-'"></span>
                                                    </span>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2">
                                                <template x-if="r.errors.length === 0">
                                                    <span class="text-green-600 text-sm font-medium">OK</span>
                                                </template>
                                                <template x-if="r.errors.length > 0">
                                                    <div class="text-red-600 text-xs space-y-0.5">
                                                        <template x-for="err in r.errors" :key="err">
                                                            <div x-text="err"></div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Confirm button --}}
                    <div class="flex items-center gap-4 mt-4">
                        <button @click="confirmImport()" class="btn-primary px-6 py-2.5 text-sm font-semibold rounded-xl"
                            :disabled="importSummary.errors > 0 || importConfirming">
                            <span x-show="importConfirming" class="spinner mr-1"></span>
                            Confirm & Import
                        </button>
                        <p x-show="importSummary.errors > 0" class="text-sm text-red-600">Fix all errors before importing.</p>
                    </div>
                </div>

                {{-- Import success --}}
                <div x-show="importDone" x-cloak class="bg-green-50 border border-green-200 rounded-xl p-5">
                    <div class="flex items-center gap-3">
                        <svg class="w-8 h-8 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <h4 class="font-semibold text-green-800" x-text="importDoneMessage"></h4>
                            <p class="text-sm text-green-700 mt-1">You can now verify the imported data in the Master Data tab.</p>
                        </div>
                    </div>
                    <button @click="resetImport(); $refs.importFileInput.value=''" class="btn-secondary mt-3 text-sm">Import Another File</button>
                </div>
            </div>
        </div>

        {{-- Download sample templates --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="text-lg font-semibold">Sample Templates</h3>
                <p class="text-sm text-gray-500 mt-1">Download a sample CSV template for each data type.</p>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <template x-for="t in importTemplates" :key="t.type">
                        <button @click="downloadTemplate(t.type)" class="flex items-center gap-2 p-3 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50/50 transition-colors text-left">
                            <svg class="w-5 h-5 text-primary-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div>
                                <div class="text-sm font-medium text-gray-800" x-text="t.label"></div>
                                <div class="text-xs text-gray-500">CSV Template</div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Notification Modal --}}
    <div x-show="showTestNotifyModal" class="modal-overlay" x-cloak @click.self="showTestNotifyModal=false">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">🧪 Send Test Notification</h3>
                <button @click="showTestNotifyModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <p class="text-sm text-gray-600">Enter a repair ticket number to fire a test notification right now (bypasses the enabled/disabled toggles).</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ticket Number</label>
                    <input x-model="testTicket" type="text" class="form-input-custom" placeholder="e.g. REP-0001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notification Type</label>
                    <select x-model="testType" class="form-select-custom">
                        <option value="received">📥 Order Received</option>
                        <option value="completed">✅ Repair Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Channel</label>
                    <select x-model="testChannel" class="form-select-custom">
                        <option value="email">📧 Email only</option>
                        <option value="whatsapp">💬 WhatsApp only</option>
                        <option value="both">📧+💬 Both</option>
                    </select>
                </div>
                <div x-show="testResult" class="p-3 rounded-lg text-sm" :class="testResult?.success ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'" x-text="testResult?.message"></div>
            </div>
            <div class="modal-footer">
                <button @click="showTestNotifyModal=false" class="btn-secondary">Close</button>
                <button @click="sendTestNotification()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Send Test
                </button>
            </div>
        </div>
    </div>

    {{-- Email Template Modal --}}
    <div x-show="showEtModal" class="modal-overlay" x-cloak @click.self="showEtModal=false">
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Email Template</h3><button @click="showEtModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Subject</label><input x-model="etForm.subject" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Body</label><textarea x-model="etForm.body" class="form-input-custom" rows="8"></textarea></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="etForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                </div>
            </div>
            <div class="modal-footer"><button @click="showEtModal=false" class="btn-secondary">Cancel</button><button @click="saveEmailTemplate()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function settingsPage() {
    return {
        tab: 'general', saving: false, iconFile: null, previewIcon: '',
        settings: {}, settingKeys: ['shop_name','shop_address','shop_phone','shop_email','shop_slogan','currency_symbol','invoice_prefix','repair_prefix','low_stock_threshold'],
        // Import state
        importType: '', importFile: null, importValidating: false, importConfirming: false,
        importResults: null, importSummary: { total: 0, creates: 0, updates: 0, errors: 0 },
        importDone: false, importDoneMessage: '',
        importTemplates: [
            { type: 'brands', label: 'Brands', columns: ['name'] },
            { type: 'categories', label: 'Categories', columns: ['name', 'description'] },
            { type: 'customers', label: 'Customers', columns: ['name', 'mobile_number', 'email', 'address', 'notes'] },
            { type: 'products', label: 'Products', columns: ['name', 'sku', 'barcode', 'category', 'brand', 'purchase_price', 'mrp', 'selling_price', 'description'] },
            { type: 'parts', label: 'Parts', columns: ['name', 'sku', 'cost_price', 'selling_price'] },
            { type: 'vendors', label: 'Vendors', columns: ['name', 'phone', 'address', 'specialization'] },
            { type: 'recharge_providers', label: 'Recharge Providers', columns: ['name', 'provider_type', 'commission_percentage'] },
        ],
        appearanceThemes: [
            {
                id: 'atelier',
                name: 'Atelier Glass',
                description: 'Bright editorial workspace with refined glass panels.',
                preview: 'background:linear-gradient(145deg,#0f172a 0%,#2563eb 42%,#8b5cf6 100%)'
            },
            {
                id: 'graphite',
                name: 'Graphite Luxe',
                description: 'Smoky neutrals, brass accents, and executive contrast.',
                preview: 'background:linear-gradient(145deg,#111827 0%,#334155 48%,#f59e0b 100%)'
            },
            {
                id: 'solstice',
                name: 'Solstice Warm',
                description: 'Warm daylight palette with copper and sandstone tones.',
                preview: 'background:linear-gradient(145deg,#7c2d12 0%,#ea580c 38%,#facc15 100%)'
            }
        ],
        notificationSettingKeys: ['notify_email_received','notify_email_completed','notify_whatsapp_received','notify_whatsapp_completed','whatsapp_api_url','whatsapp_api_token','whatsapp_from_number','whatsapp_template_received','whatsapp_template_completed'],
        emailTemplates: [], showEtModal: false, etEditing: null, etForm: {},
        backups: [],
        showTestNotifyModal: false, testTicket: '', testType: 'received', testChannel: 'email', testResult: null,
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.get('tab') === 'service-types') {
                window.location.href = '/service-types';
                return;
            }
            if (p.get('tab') === 'recharge-providers') {
                this.tab = 'master-data';
            } else if (p.has('tab')) {
                this.tab = p.get('tab');
            }
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.tab !== 'general') params.set('tab', this.tab);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [s, et, b] = await Promise.all([
                RepairBox.ajax('/settings'),
                RepairBox.ajax('/email-templates'),
                RepairBox.ajax('/backups')
            ]);
            if (s.data) this.settings = { ui_theme: 'atelier', ui_motion: 'enhanced', ...s.data };
            else this.settings = { ui_theme: 'atelier', ui_motion: 'enhanced' };
            this.applyAppearancePreview();
            if(et.data) this.emailTemplates = et.data;
            if(b.data) this.backups = b.data;
        },
        loadNotifications() {
            // already loaded in load() — just ensure email templates are present
            if (this.emailTemplates.length === 0) {
                RepairBox.ajax('/email-templates').then(r => { if(r.data) this.emailTemplates = r.data; });
            }
        },
        async saveNotificationSettings() {
            this.saving = true;
            try {
                const payload = {};
                this.notificationSettingKeys.forEach(k => { if (this.settings[k] !== undefined) payload['settings['+k+']'] = this.settings[k]; });
                const formData = new FormData();
                formData.append('_method', 'PUT');
                this.notificationSettingKeys.forEach(k => {
                    if (this.settings[k] !== undefined && this.settings[k] !== null)
                        formData.append('settings['+k+']', this.settings[k]);
                });
                const r = await fetch('/settings', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                const data = await r.json();
                if (data.success !== false) RepairBox.toast('Notification settings saved', 'success');
                else RepairBox.toast(data.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: '+e.message, 'error'); }
            this.saving = false;
        },
        async sendTestNotification() {
            if (!this.testTicket.trim()) { RepairBox.toast('Enter a ticket number', 'warning'); return; }
            this.saving = true; this.testResult = null;
            const r = await RepairBox.ajax('/notifications/test', 'POST', { ticket: this.testTicket, type: this.testType, channel: this.testChannel });
            this.saving = false;
            this.testResult = { success: r.success !== false, message: r.message || (r.success !== false ? 'Sent successfully!' : 'Failed to send.') };
        },
        formatLabel(key) { return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); },
        formatMotionLabel(value) {
            return ({ enhanced: 'Enhanced', reduced: 'Reduced', none: 'Off' })[value] || 'Enhanced';
        },
        get selectedTheme() {
            return this.appearanceThemes.find(theme => theme.id === this.settings.ui_theme) || this.appearanceThemes[0];
        },
        applyAppearancePreview() {
            const root = document.documentElement;
            const body = document.body;
            const theme = this.settings.ui_theme || 'atelier';
            const motion = this.settings.ui_motion || 'enhanced';

            root.dataset.theme = theme;
            root.dataset.motion = motion;

            if (body) {
                body.dataset.theme = theme;
                body.dataset.motion = motion;
            }
        },
        getIconUrl() {
            const icon = this.settings.shop_icon;
            if (!icon) return '';
            if (icon.startsWith('http') || icon.startsWith('data:')) return icon;
            // Construct the correct storage URL
            return '/storage/' + (icon.startsWith('/') ? icon.substring(1) : icon);
        },
        handleIconUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.iconFile = file;
                const reader = new FileReader();
                reader.onload = (evt) => {
                    this.previewIcon = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        },
        getExpectedColumns() {
            const t = this.importTemplates.find(t => t.type === this.importType);
            return t ? t.columns.join(', ') : '';
        },
        getUniqueKeyLabel() {
            const keys = { brands: 'name', categories: 'name', customers: 'mobile_number', products: 'sku', parts: 'sku', vendors: 'name', recharge_providers: 'name' };
            return keys[this.importType] || '';
        },
        resetImport() {
            this.importFile = null;
            this.importResults = null;
            this.importSummary = { total: 0, creates: 0, updates: 0, errors: 0 };
            this.importDone = false;
            this.importDoneMessage = '';
        },
        async validateImport() {
            if (!this.importType || !this.importFile) return;
            this.importValidating = true;
            this.importResults = null;
            this.importDone = false;

            const formData = new FormData();
            formData.append('type', this.importType);
            formData.append('file', this.importFile);

            try {
                const response = await fetch('/import/validate', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                const r = await response.json();
                this.importValidating = false;

                if (r.success === false) {
                    RepairBox.toast(r.message || 'Validation failed', 'error');
                    return;
                }

                this.importResults = r.results;
                this.importSummary = { total: r.total, creates: r.creates, updates: r.updates, errors: r.errors };

                if (r.errors === 0) {
                    RepairBox.toast('Validation passed! Review and click Confirm to import.', 'success');
                } else {
                    RepairBox.toast(`${r.errors} row(s) have errors. Fix your CSV and re-upload.`, 'error');
                }
            } catch (err) {
                this.importValidating = false;
                RepairBox.toast('Error validating file: ' + err.message, 'error');
            }
        },
        async confirmImport() {
            this.importConfirming = true;
            try {
                const r = await RepairBox.ajax('/import/confirm', 'POST');
                this.importConfirming = false;
                const msg = r.message || (r.data && r.data.message);

                if (r.success !== false) {
                    this.importResults = null;
                    this.importDone = true;
                    this.importDoneMessage = msg || 'Import completed successfully.';
                    RepairBox.toast(msg || 'Import completed successfully.', 'success');
                } else {
                    RepairBox.toast(msg || 'Import failed', 'error');
                }
            } catch (err) {
                this.importConfirming = false;
                RepairBox.toast('Import failed: ' + err.message, 'error');
            }
        },
        downloadTemplate(type) {
            const t = this.importTemplates.find(t => t.type === type);
            if (!t) return;
            const csv = t.columns.join(',') + '\n';
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${type}_template.csv`;
            a.click();
            URL.revokeObjectURL(url);
        },
        async saveSettings() {
            this.saving = true;
            try {
                const formData = new FormData();
                // Laravel method spoofing: POST + _method=PUT so PHP parses multipart/form-data
                formData.append('_method', 'PUT');
                // Append each setting individually with array notation
                Object.keys(this.settings).forEach(key => {
                    if (key !== 'shop_icon' && this.settings[key] !== null) {
                        formData.append(`settings[${key}]`, this.settings[key]);
                    }
                });
                if (this.iconFile) {
                    formData.append('shop_icon', this.iconFile);
                }

                const response = await fetch('/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                const r = await response.json();
                this.saving = false;
                this.iconFile = null;
                if (r.success !== false) {
                    RepairBox.toast('Settings saved', 'success');
                    this.previewIcon = '';
                    this.iconFile = null;
                    setTimeout(() => this.load(), 500);
                } else {
                    RepairBox.toast(r.message || 'Error saving settings', 'error');
                }
            } catch (err) {
                this.saving = false;
                console.error('Save error:', err);
                RepairBox.toast('Error saving settings: ' + err.message, 'error');
            }
        },
        clearIconPreview() {
            this.previewIcon = '';
            this.iconFile = null;
        },
        async saveEmailTemplate() {
            this.saving = true;
            const r = await RepairBox.ajax(`/email-templates/${this.etEditing.id}`, 'PUT', this.etForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showEtModal = false; const et = await RepairBox.ajax('/email-templates'); if(et.data) this.emailTemplates = et.data; }
        },
        async createBackup() {
            this.saving = true;
            const r = await RepairBox.ajax('/backups', 'POST');
            this.saving = false; if(r.success !== false) { RepairBox.toast('Backup created', 'success'); const b = await RepairBox.ajax('/backups'); if(b.data) this.backups = b.data; }
        }
    };
}
function masterDataPanel() {
    const sectionConfig = {
        vendors:    { label: 'Vendor Management', singular: 'Vendor',   url: '/vendors',    deleteUrl: null },
        inventory:  { label: 'Inventory',         singular: 'Stock Adjustment', url: '/inventory', deleteUrl: null },
        brands:     { label: 'Brands',            singular: 'Brand',    url: '/brands',     deleteUrl: '/brands' },
        categories: { label: 'Categories',        singular: 'Category', url: '/categories', deleteUrl: '/categories' },
        parts:      { label: 'Parts',             singular: 'Part',     url: '/parts',      deleteUrl: '/parts' },
        products:   { label: 'Products',          singular: 'Product',  url: '/products',   deleteUrl: '/products' },
        customers:  { label: 'Customers',         singular: 'Customer', url: '/customers',  deleteUrl: '/customers' },
        'recharge-providers': { label: 'Recharge Providers', singular: 'Provider', url: '/recharge-providers', deleteUrl: '/recharge-providers' },
        services:  { label: 'Services', singular: 'Service', url: '/service-types', deleteUrl: '/service-types' },
    };

    return {
        mdSection: 'vendors',
        mdItems: [],
        mdLoading: false,
        mdSaving: false,
        mdSearch: '',
        mdEditing: null,
        mdForm: {},
        showMdModal: false,
        mdCategories: [],
        mdBrands: [],
        mdProducts: [],
        svcQuickFillTags: [
            'Xerox / Photocopy', 'Lamination', 'Screen Replacement', 'Battery Replacement',
            'Charging Port Repair', 'Software / Flashing', 'Data Recovery', 'Water Damage Repair',
            'Speaker Repair', 'Mic Repair', 'Camera Repair', 'Back Panel Replacement',
            'Keyboard Repair', 'Motherboard Repair', 'SIM Tray Replace', 'General Service',
        ],
        svcNewQuickFill: '',

        get mdSectionLabel() { return sectionConfig[this.mdSection]?.label || ''; },
        get mdSectionLabelSingular() { return sectionConfig[this.mdSection]?.singular || ''; },

        switchSection(section) {
            this.mdSection = section;
            this.mdSearch = '';
            this.mdEditing = null;
            this.mdForm = {};
            this.loadMdData();
        },

        async loadMdData() {
            this.mdLoading = true;
            const cfg = sectionConfig[this.mdSection];
            const params = {};
            if (this.mdSearch) params.search = this.mdSearch;

            const r = await RepairBox.ajax(cfg.url, 'GET', params);
            if (Array.isArray(r)) {
                this.mdItems = r;
            } else if (r.data) {
                this.mdItems = Array.isArray(r.data) ? r.data : [];
            } else {
                this.mdItems = [];
            }
            this.mdLoading = false;
        },

        openMdAdd() {
            this.mdEditing = null;
            this.mdForm = this.getDefaultForm();

            if (this.mdSection === 'products') {
                this.loadDropdowns();
            }
            if (this.mdSection === 'inventory') {
                this.loadProducts();
            }
            this.showMdModal = true;
        },

        openMdEdit(item) {
            if (this.mdSection === 'inventory') return;
            this.mdEditing = item.id;
            this.mdForm = { ...item };

            if (this.mdSection === 'services' && !this.mdForm.quick_fills) {
                this.mdForm.quick_fills = [];
            }

            if (this.mdSection === 'products') {
                this.loadDropdowns();
            }
            this.showMdModal = true;
        },

        getDefaultForm() {
            switch(this.mdSection) {
                case 'vendors': return { name: '', phone: '', specialization: '', address: '' };
                case 'brands': return { name: '', logo_url: '' };
                case 'categories': return { name: '', description: '' };
                case 'parts': return { name: '', sku: '', cost_price: '', selling_price: '' };
                case 'products': return { name: '', sku: '', category_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', description: '' };
                case 'customers': return { name: '', mobile_number: '', email: '', address: '' };
                case 'inventory': return { product_id: '', adjustment_type: 'addition', quantity: '', reason: '' };
                case 'recharge-providers': return { name: '', provider_type: '', commission_percentage: '' };
                case 'services': return { name: '', default_price: '', description: '', quick_fills: [] };
                default: return {};
            }
        },

        async loadDropdowns() {
            const [cats, brands] = await Promise.all([
                RepairBox.ajax('/categories'),
                RepairBox.ajax('/brands')
            ]);
            this.mdCategories = Array.isArray(cats) ? cats : (cats.data || []);
            this.mdBrands = Array.isArray(brands) ? brands : (brands.data || []);
        },

        async loadProducts() {
            const r = await RepairBox.ajax('/products');
            this.mdProducts = Array.isArray(r) ? r : (r.data || []);
        },

        async saveMdItem() {
            if (!this.mdForm.name && this.mdSection !== 'inventory') {
                return RepairBox.toast('Name is required', 'error');
            }
            if (this.mdSection === 'inventory' && (!this.mdForm.product_id || !this.mdForm.quantity)) {
                return RepairBox.toast('Product and quantity are required', 'error');
            }

            this.mdSaving = true;
            const cfg = sectionConfig[this.mdSection];

            if (this.mdSection === 'inventory') {
                const r = await RepairBox.ajax('/inventory/adjust', 'POST', this.mdForm);
                this.mdSaving = false;
                if (r.success !== false) {
                    RepairBox.toast('Stock adjusted', 'success');
                    this.showMdModal = false;
                    await this.loadMdData();
                }
                return;
            }

            const url = this.mdEditing ? `${cfg.url}/${this.mdEditing}` : cfg.url;
            const method = this.mdEditing ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.mdForm);
            this.mdSaving = false;

            if (r.success !== false) {
                RepairBox.toast(this.mdEditing ? `${cfg.singular} updated` : `${cfg.singular} added`, 'success');
                this.showMdModal = false;
                this.mdEditing = null;
                this.mdForm = {};
                await this.loadMdData();
            }
        },

        async deleteMdItem(item) {
            const cfg = sectionConfig[this.mdSection];
            if (!cfg.deleteUrl) return;
            if (!confirm(`Delete this ${cfg.singular.toLowerCase()}?`)) return;

            const r = await RepairBox.ajax(`${cfg.deleteUrl}/${item.id}`, 'DELETE');
            if (r.success !== false) {
                RepairBox.toast(`${cfg.singular} deleted`, 'success');
                await this.loadMdData();
            }
        }
    };
}
</script>
@endpush
