@extends('layouts.app')
@section('page-title', 'Settings')

@section('content')
<style>
    /* ── Settings Page Mobile Fixes ── */
    .settings-primary-tabs {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        scroll-snap-type: x proximity;
        padding-bottom: 2px;
    }
    .settings-primary-tabs::-webkit-scrollbar { display: none; }
    .settings-primary-tabs .secondary-tab { flex-shrink: 0; scroll-snap-align: start; }

    /* Print settings toolbar wrap on mobile */
    .print-toolbar-left { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .print-subtabs { display: inline-flex; gap: 4px; background: #f1f5f9; padding: 4px; border-radius: 12px; flex-wrap: wrap; }

    @media (max-width: 767px) {
        /* Settings tabs: force text visible & allow scroll */
        .settings-primary-tabs { margin-bottom: 0.85rem; }
        .settings-primary-tabs .secondary-tab { min-height: 2.2rem; padding: 0.5rem 0.8rem; font-size: 0.7rem; }

        /* Print toolbar stacks on mobile */
        .print-toolbar-left { gap: 8px; }
        .print-subtabs button { padding: 6px 10px !important; font-size: 0.65rem !important; }

        /* Backups table responsive */
        .backups-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .backups-table-wrap .data-table { min-width: 540px; }

        /* General settings / appearance card inner */
        .settings-appearance-inner .mt-3 { flex-direction: column !important; align-items: flex-start !important; }
    }

    @media (max-width: 480px) {
        /* Override global secondary-tab 50% stretch for settings tabs */
        .settings-primary-tabs .secondary-tab { flex: 0 0 auto !important; }
    }
</style>
<div x-data="settingsPage()" x-init="init()">
    <div class="page-header-inline">
        <div class="page-header-inline-copy">
            <h2 class="page-header-inline-title">Settings</h2>
            <p class="page-header-inline-description">System preferences, master data, notification templates, and maintenance tools.</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="secondary-tabs settings-primary-tabs">
        <button @click="tab='general'; updateUrl()" :class="tab==='general' ? 'secondary-tab is-active' : 'secondary-tab'">General</button>
        <button @click="tab='landing'; updateUrl()" :class="tab==='landing' ? 'secondary-tab is-active' : 'secondary-tab'">Landing Page</button>
        <button @click="tab='master-data'; updateUrl(); $dispatch('md-tab-activated')" :class="tab==='master-data' ? 'secondary-tab is-active' : 'secondary-tab'">Master Data</button>
        <button @click="tab='email-templates'; updateUrl()" :class="tab==='email-templates' ? 'secondary-tab is-active' : 'secondary-tab'">Email Templates</button>
        <button @click="tab='notifications'; updateUrl(); loadNotifications()" :class="tab==='notifications' ? 'secondary-tab is-active' : 'secondary-tab'">Notifications</button>
        <button @click="tab='print'; updateUrl(); initPrintTab()" :class="tab==='print' ? 'secondary-tab is-active' : 'secondary-tab'">Print Settings</button>
        <button @click="tab='backups'; updateUrl()" :class="tab==='backups' ? 'secondary-tab is-active' : 'secondary-tab'">Backups</button>
        <button @click="tab='import'; updateUrl()" :class="tab==='import' ? 'secondary-tab is-active' : 'secondary-tab'">Import</button>
        <button @click="tab='seo'; updateUrl()" :class="tab==='seo' ? 'secondary-tab is-active' : 'secondary-tab'">SEO</button>
    </div>

    {{-- Master Data --}}
    <div x-show="tab==='master-data'" x-data="masterDataPanel()" x-init="switchSection(mdSection)" @md-tab-activated.window="resyncSectionUrl()">
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
                /* Grid: single column, no min-height */
                .md-workspace { gap: 0.5rem; grid-template-columns: 1fr !important; min-height: auto !important; }
                .md-workspace .md-panel { border-radius: 1.1rem; }

                /* ── LEFT sidebar → horizontal scrolling chip nav ── */
                .md-workspace > .md-panel:first-child { position: static !important; height: auto !important; }
                .md-workspace > .md-panel:first-child > .card-header { display: none; }
                .md-workspace > .md-panel:first-child > .p-2 {
                    display: flex; flex-direction: row; overflow-x: auto;
                    gap: 4px; padding: 6px 8px; scrollbar-width: none;
                }
                .md-workspace > .md-panel:first-child > .p-2::-webkit-scrollbar { display: none; }
                /* Hide section group labels on mobile */
                .md-workspace > .md-panel:first-child .px-3 { display: none !important; }
                /* Menu items: vertical chip (icon above label) */
                .md-workspace .md-menu-item {
                    flex-direction: column !important; width: auto !important;
                    min-width: 62px; gap: 4px; padding: 8px 10px !important;
                    border-radius: 12px; flex-shrink: 0; text-align: center;
                    font-size: 0.68rem !important; line-height: 1.2;
                }
                .md-workspace .md-menu-icon { width: 28px; height: 28px; border-radius: 8px; margin: 0 auto; }

                /* ── Tables fit full screen width ── */
                .md-workspace .md-table-shell > div {
                    overflow-x: auto !important; overflow-y: auto !important;
                    -webkit-overflow-scrolling: touch;
                    max-height: 55vh !important;
                }
                .md-workspace .md-table-shell .data-table {
                    font-size: 0.72rem !important;
                }
                .md-workspace .md-table-shell .data-table th,
                .md-workspace .md-table-shell .data-table td {
                    white-space: nowrap;
                    font-size: 0.72rem !important; padding: 6px 5px !important;
                }
                /* Secondary columns hidden on mobile */
                .md-col-hide { display: none !important; }

                /* Containment */
                .md-workspace .md-search-input, .md-workspace .md-form-input { min-height: 2.4rem; border-radius: 0.82rem; }
                .md-workspace .card.md-panel { max-width: 100%; }
                .md-workspace { max-width: 100%; }
            }
        </style>

        <div class="md-workspace" style="display:grid; grid-template-columns: 260px 1fr; gap: 0.8rem; min-height: 70vh;">

            {{-- ===== LEFT: Menu Sidebar ===== --}}
            <div class="md-panel flex flex-col" style="height: fit-content; position: sticky; top: 1rem;">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">Master Data</h3>
                </div>
                <div class="p-2 space-y-0.5">
                    {{-- ── Repairs Module ── --}}
                    <div class="px-3 pt-2 pb-1"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Repairs Module</p></div>
                    <button @click="switchSection('parts')" :class="mdSection==='parts' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <span>Parts</span>
                    </button>
                    <button @click="switchSection('services')" :class="mdSection==='services' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <span>Services</span>
                    </button>

                    {{-- ── Sales Module ── --}}
                    <div class="px-3 pt-3 pb-1 border-t border-gray-100 mt-1"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Sales Module</p></div>
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
                    <button @click="switchSection('products')" :class="mdSection==='products' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span>Products</span>
                    </button>
                    <button @click="switchSection('inventory')" :class="mdSection==='inventory' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                        </div>
                        <span>Inventory</span>
                    </button>
                    <button @click="switchSection('recharge-providers')" :class="mdSection==='recharge-providers' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span>Recharge Providers</span>
                    </button>

                    {{-- ── People ── --}}
                    <div class="px-3 pt-3 pb-1 border-t border-gray-100 mt-1"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">People</p></div>
                    <button @click="switchSection('vendors')" :class="mdSection==='vendors' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#f97316,#c2410c);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span>Vendors</span>
                    </button>
                    <button @click="switchSection('customers')" :class="mdSection==='customers' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </div>
                        <span>Customers</span>
                    </button>

                    {{-- ── Team Access ── --}}
                    <div class="px-3 pt-3 pb-1 border-t border-gray-100 mt-1"><p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Team Access</p></div>
                    <button @click="switchSection('users')" :class="mdSection==='users' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span>Users</span>
                    </button>
                    <button @click="switchSection('roles')" :class="mdSection==='roles' ? 'md-menu-item is-active' : 'md-menu-item'" class="w-full text-left">
                        <div class="md-menu-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                            <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <span>Roles & Permissions</span>
                    </button>
                </div>
            </div>

            {{-- ===== RIGHT: Content Area ===== --}}
            <div class="flex flex-col gap-3">

                {{-- Search toolbar --}}
                <div class="md-toolbar flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input x-model="mdSearch" @input.debounce.400ms="loadMdData()" type="search"
                            class="form-input-custom md-search-input pl-10 pr-10 w-full text-sm" :placeholder="'Search ' + mdSectionLabel + '...'"
                            autocomplete="off" readonly onfocus="this.removeAttribute('readonly')">
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Specialization</th>
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
                                                    <template x-if="item.thumbnail || item.image">
                                                        <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-7 h-7 rounded object-cover">
                                                    </template>
                                                    <span x-text="item.name"></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm" x-text="item.phone || '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.specialization || '-'"></td>
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Reserved</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.product ? item.product.name : (item.name || '-')"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.current_stock ?? item.stock ?? 0"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.reserved_stock ?? 0"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold"
                                                    :class="(item.current_stock ?? item.stock ?? 0) > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                    x-text="(item.current_stock ?? item.stock ?? 0) > 0 ? 'In Stock' : 'Out of Stock'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500 md-col-hide" x-text="item.updated_at ? new Date(item.updated_at).toLocaleDateString() : '-'"></td>
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Models</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Image</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm text-gray-500" x-text="item.models && item.models.length ? item.models.length + ' model(s)' : '—'"></td>
                                            <td class="px-3 py-2">
                                                <template x-if="item.thumbnail || item.image">
                                                    <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-8 h-8 rounded object-cover">
                                                </template>
                                                <template x-if="!item.thumbnail && !item.image">
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
                                        <td colspan="5" class="text-center py-12">
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Description</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Subcategories</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm text-gray-500 md-col-hide" x-text="item.description || '-'"></td>
                                            <td class="px-3 py-2" @click.stop>
                                                <button @click="openCatSubModal(item)"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h8M4 18h8"/></svg>
                                                    <span x-text="(item.subcategories ? item.subcategories.length : 0) + ' subcategories'"></span>
                                                </button>
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
                                        <td colspan="5" class="text-center py-12">
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">SKU</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Cost Price</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Selling Price</th>
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
                                                    <template x-if="item.thumbnail || item.image">
                                                        <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-7 h-7 rounded object-cover">
                                                    </template>
                                                    <span x-text="item.name"></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.sku || '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.cost_price ? RepairBox.formatCurrency(item.cost_price) : '-'"></td>
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Img</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">SKU</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Category</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Subcategory</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">MRP</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Sale Price</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2">
                                                <template x-if="item.thumbnail || item.image">
                                                    <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-8 h-8 rounded-lg object-cover border border-gray-100">
                                                </template>
                                                <template x-if="!item.thumbnail && !item.image">
                                                    <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                    </div>
                                                </template>
                                            </td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.sku || '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.category ? item.category.name : '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.subcategory ? item.subcategory.name : '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.mrp ? RepairBox.formatCurrency(item.mrp) : '-'"></td>
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
                                        <td colspan="9" class="text-center py-12">
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Email</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Loyalty Pts</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm" x-text="item.mobile_number || '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.email || '-'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="item.loyalty_points || 0"></td>
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
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="border-t border-gray-100 hover:bg-gray-50/60 transition cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <template x-if="item.thumbnail || item.image">
                                                        <img :src="RepairBox.imageUrl(item.thumbnail || item.image)" class="w-7 h-7 rounded object-cover">
                                                    </template>
                                                    <span x-text="item.name"></span>
                                                </div>
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
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Description</th>
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
                                                        <img :src="RepairBox.imageUrl(item.thumbnail)" class="w-7 h-7 rounded object-cover">
                                                    </template>
                                                    <span x-text="item.name"></span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm" x-text="item.default_price ? RepairBox.formatCurrency(item.default_price) : '-'"></td>
                                            <td class="px-3 py-2 text-sm text-gray-500 md-col-hide" x-text="item.description ? item.description.substring(0,50) + (item.description.length > 50 ? '...' : '') : '-'"></td>
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

                        {{-- Users Table --}}
                        <template x-if="mdSection==='users'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Name</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Email</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Role</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm" x-text="item.name"></td>
                                            <td class="px-3 py-2 text-sm text-gray-600" x-text="item.email"></td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700" x-text="item.role ? item.role.name : '-'"></span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold"
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
                                            <p class="text-gray-400 font-medium">No users found</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </template>

                        {{-- Roles Table --}}
                        <template x-if="mdSection==='roles'">
                            <table class="data-table w-full">
                                <thead class="sticky top-0 z-10">
                                    <tr class="bg-gray-50">
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Role</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Description</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Users</th>
                                        <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase md-col-hide">Permissions</th>
                                        <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, idx) in mdItems" :key="item.id">
                                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="openMdEdit(item)">
                                            <td class="px-3 py-2 text-gray-400 text-sm" x-text="idx+1"></td>
                                            <td class="px-3 py-2 font-medium text-gray-800 text-sm">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                                         :class="item.name === 'Admin' ? 'bg-amber-100 text-amber-700' : 'bg-primary-100 text-primary-700'">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                                    </div>
                                                    <div>
                                                        <span x-text="item.name"></span>
                                                        <template x-if="item.name === 'Admin'">
                                                            <span class="ml-1.5 text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">System</span>
                                                        </template>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-500 md-col-hide" x-text="item.description || '-'"></td>
                                            <td class="px-3 py-2 text-sm" x-text="(item.users_count || 0) + ' users'"></td>
                                            <td class="px-3 py-2 text-sm md-col-hide" x-text="(item.permissions_count || 0) + ' permissions'"></td>
                                            <td class="px-3 py-2 text-center" @click.stop>
                                                <button @click="openMdEdit(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </button>
                                                <template x-if="item.name !== 'Admin'">
                                                    <button @click="deleteMdItem(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="mdItems.length === 0 && !mdLoading">
                                        <td colspan="6" class="text-center py-12">
                                            <p class="text-gray-400 font-medium">No roles found</p>
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

                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
                            </div>

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

                            {{-- Device Models --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Device Models <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="flex gap-2 mb-2">
                                    <input type="text" x-model="mdNewModel" @keydown.enter.prevent="addMdModel()" placeholder="e.g. Galaxy S24, iPhone 15..." class="form-input-custom flex-1 text-sm">
                                    <button type="button" @click="addMdModel()" class="btn-secondary text-sm px-3">Add</button>
                                </div>
                                <div class="flex flex-wrap gap-1.5" x-show="mdForm.models && mdForm.models.length > 0">
                                    <template x-for="(m, idx) in (mdForm.models || [])" :key="idx">
                                        <span class="inline-flex items-center gap-1 rounded-full border border-blue-200 bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-800">
                                            <span x-text="m"></span>
                                            <button type="button" @click="mdForm.models.splice(idx,1)" class="ml-0.5 text-blue-400 hover:text-red-500 text-sm leading-none font-bold">&times;</button>
                                        </span>
                                    </template>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Shown as suggestions when creating a repair for this brand.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
                            </div>
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

                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
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
                                    <select x-model="mdForm.category_id" class="form-select-custom" @change="mdLoadSubcategories()">
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
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                                <select x-model="mdForm.subcategory_id" class="form-select-custom">
                                    <option value="">Select</option>
                                    <template x-for="s in mdSubcategories" :key="s.id">
                                        <option :value="s.id" x-text="s.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div class="grid grid-cols-3 gap-3" :class="!mdEditing && 'sm:grid-cols-4'">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label>
                                    <input x-model="mdForm.purchase_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">MRP *</label>
                                    <input x-model="mdForm.mrp" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                                    <input x-model="mdForm.selling_price" type="number" step="0.01" class="form-input-custom" placeholder="0.00"></div>
                                <div x-show="!mdEditing"><label class="block text-sm font-medium text-gray-700 mb-1">Opening Stock</label>
                                    <input x-model="mdForm.opening_stock" type="number" step="1" min="0" class="form-input-custom" placeholder="0"></div>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="mdForm.description" class="form-input-custom" rows="2" placeholder="Description"></textarea></div>

                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
                            </div>
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
                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Provider Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
                            </div>
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

                            {{-- Image Upload --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Image <span class="text-gray-400 font-normal">(optional)</span></label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$nextTick(() => $refs.mdImageInput?.click())" @dragover.prevent @drop.prevent="mdHandleDrop($event)">
                                    <template x-if="mdImagePreview">
                                        <div class="relative inline-block">
                                            <img :src="mdImagePreview" class="max-h-20 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="mdImageFile=null; mdImagePreview=null; $refs.mdImageInput && ($refs.mdImageInput.value='')"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&#x2715;</button>
                                        </div>
                                    </template>
                                    <template x-if="!mdImagePreview">
                                        <div class="py-2">
                                            <svg class="w-6 h-6 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-[10px] text-gray-400">Click or drag & drop</p>
                                        </div>
                                    </template>
                                    <input x-ref="mdImageInput" type="file" accept="image/*" class="hidden" @change="mdHandlePick($event)" @click.stop>
                                </div>
                            </div>

                            <template x-if="mdEditing">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select x-model="mdForm.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                            </template>
                        </div>
                    </template>

                    {{-- Users Form --}}
                    <template x-if="mdSection==='users'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="Full name"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                                <input x-model="mdForm.email" type="email" class="form-input-custom" placeholder="Email address"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">
                                Password <span x-show="mdEditing" class="text-gray-400 font-normal">(leave blank to keep)</span><span x-show="!mdEditing" class="text-red-500">*</span>
                            </label>
                                <input x-model="mdForm.password" type="password" class="form-input-custom" placeholder="Password"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                                <input x-model="mdForm.password_confirmation" type="password" class="form-input-custom" placeholder="Confirm password"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                                <select x-model="mdForm.role_id" class="form-select-custom">
                                    <option value="">Select role</option>
                                    <template x-for="r in mdRolesList" :key="r.id">
                                        <option :value="r.id" x-text="r.name"></option>
                                    </template>
                                </select>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select x-model="mdForm.status" class="form-select-custom">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    {{-- Roles Form --}}
                    <template x-if="mdSection==='roles'">
                        <div class="space-y-4">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Role Name *</label>
                                <input x-model="mdForm.name" type="text" class="form-input-custom" placeholder="e.g. Manager, Technician"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea x-model="mdForm.description" class="form-input-custom" rows="2" placeholder="Brief description of this role"></textarea></div>
                            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 flex items-start gap-3">
                                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div>
                                    <p class="text-xs font-medium text-amber-800">Permissions can be assigned from the Roles page</p>
                                    <a href="/admin/roles" target="_blank" class="text-xs text-amber-700 underline hover:text-amber-900">Open Roles & Permissions →</a>
                                </div>
                            </div>
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

        {{-- Subcategory Management Modal --}}
        <div x-show="showCatSubModal" class="modal-overlay" x-cloak @click.self="showCatSubModal=false" style="z-index:55;">
            <div class="modal-container" style="max-width:560px;">
                <div class="modal-header">
                    <div>
                        <h3 class="text-lg font-semibold" x-text="'Subcategories — ' + (catSubItem ? catSubItem.name : '')"></h3>
                        <p class="text-xs text-gray-500 mt-0.5">Manage subcategories for this category</p>
                    </div>
                    <button @click="showCatSubModal=false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>
                <div class="modal-body">
                    {{-- Add new subcategory --}}
                    <div class="flex gap-2 mb-4">
                        <input x-model="catSubNewName" type="text" class="form-input-custom flex-1" placeholder="New subcategory name"
                            @keydown.enter.prevent="addCatSub()">
                        <button @click="addCatSub()" class="btn-primary px-4 text-sm" :disabled="catSubSaving || !catSubNewName.trim()">
                            <span x-show="catSubSaving" class="spinner mr-1"></span>
                            Add
                        </button>
                    </div>

                    {{-- Subcategories list --}}
                    <div x-show="catSubLoading" class="text-center py-8 text-gray-400 text-sm">Loading...</div>
                    <div x-show="!catSubLoading">
                        <div x-show="catSubItems.length === 0" class="text-center py-8 text-gray-400 text-sm">No subcategories yet. Add one above.</div>
                        <div class="divide-y divide-gray-100 rounded-xl border border-gray-100 overflow-hidden" x-show="catSubItems.length > 0">
                            <template x-for="sub in catSubItems" :key="sub.id">
                                <div class="flex items-center gap-3 px-3 py-2.5 bg-white hover:bg-gray-50/60 transition">
                                    {{-- View mode --}}
                                    <template x-if="catSubEditing !== sub.id">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <span class="flex-1 text-sm font-medium text-gray-800 truncate" x-text="sub.name"></span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold"
                                                :class="(sub.status||'active')==='active' ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-600'"
                                                x-text="sub.status || 'active'"></span>
                                            <button @click="startEditCatSub(sub)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition shrink-0" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="deleteCatSub(sub)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition shrink-0" title="Delete">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                    {{-- Edit mode --}}
                                    <template x-if="catSubEditing === sub.id">
                                        <div class="flex items-center gap-2 flex-1">
                                            <input x-model="catSubEditName" type="text" class="form-input-custom flex-1 text-sm py-1" @keydown.enter.prevent="saveCatSubEdit()" @keydown.escape.prevent="cancelCatSubEdit()">
                                            <select x-model="catSubEditStatus" class="form-select-custom text-sm py-1" style="min-width:90px;">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                            <button @click="saveCatSubEdit()" class="p-1.5 rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition shrink-0" :disabled="catSubSaving" title="Save">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                            <button @click="cancelCatSubEdit()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition shrink-0" title="Cancel">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="showCatSubModal=false" class="btn-secondary">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- General Settings --}}
    <div x-show="tab==='general'" class="space-y-5" x-cloak>

        {{-- ─── Shop Settings ─── --}}
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Shop Settings</h3></div>
            <div class="card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <template x-for="key in settingKeys" :key="key">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1" x-text="formatLabel(key)"></label>
                            <input :value="settings[key] || ''" @input="settings[key] = $event.target.value" type="text" class="form-input-custom">
                        </div>
                    </template>
                </div>

                {{-- Shop Logo & Favicon --}}
                <div class="pt-5 border-t">
                    <h4 class="text-md font-semibold mb-4">Shop Logo &amp; Favicon</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                        {{-- Shop Logo --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Shop Logo</label>
                            <p class="text-xs text-gray-500 mb-3">Shown in the top nav, invoices and receipts.</p>
                            <input type="file" @change="handleIconUpload" accept="image/*" class="form-input-custom">
                            <p class="text-xs text-gray-400 mt-1">Max 2MB &mdash; PNG, JPG, GIF, SVG</p>
                            <div class="mt-3 flex items-center gap-3">
                                <div x-show="previewIcon" class="w-20 h-20 border rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img :src="previewIcon" class="w-full h-full object-contain" alt="Logo preview">
                                </div>
                                <div x-show="!previewIcon && settings.shop_icon" class="w-20 h-20 border rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img :src="getIconUrl()" class="w-full h-full object-contain" alt="Shop Logo" x-on:error="$el.parentElement.style.display='none'">
                                </div>
                                <div x-show="!previewIcon && !settings.shop_icon" class="w-20 h-20 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Favicon --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Favicon</label>
                            <p class="text-xs text-gray-500 mb-3">Shown in browser tabs and bookmarks. Best at 32×32 or 64×64 px.</p>
                            <input type="file" @change="handleFaviconUpload" accept="image/png,image/x-icon,image/svg+xml,image/jpeg" class="form-input-custom">
                            <p class="text-xs text-gray-400 mt-1">Max 512KB &mdash; PNG, ICO, SVG</p>
                            <div class="mt-3 flex items-center gap-3">
                                <div x-show="previewFavicon" class="w-12 h-12 border rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img :src="previewFavicon" class="w-full h-full object-contain" alt="Favicon preview">
                                </div>
                                <div x-show="!previewFavicon && settings.shop_favicon" class="w-12 h-12 border rounded-lg bg-gray-50 flex items-center justify-center overflow-hidden">
                                    <img :src="getFaviconUrl()" class="w-full h-full object-contain" alt="Favicon" x-on:error="$el.parentElement.style.display='none'">
                                </div>
                                <div x-show="!previewFavicon && !settings.shop_favicon" class="w-12 h-12 border-2 border-dashed rounded-lg bg-gray-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" stroke-width="2"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h6M9 12h6M9 15h4"/></svg>
                                </div>
                                <div x-show="settings.shop_favicon || previewFavicon" class="text-xs text-gray-500">
                                    <p>Browser tab preview:</p>
                                    <div class="mt-1 flex items-center gap-1.5 px-2 py-1 bg-gray-100 rounded-lg w-fit">
                                        <img :src="previewFavicon || getFaviconUrl()" class="w-4 h-4 object-contain" alt="">
                                        <span class="text-[11px] text-gray-600 truncate max-w-[80px]" x-text="settings.shop_name || 'RepairBox'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div>
                    <button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Shop Settings</button>
                </div>

                {{-- ─── Shop Timings ─── --}}
                <div class="pt-5 border-t">
                    <h4 class="text-md font-semibold mb-1">Shop Timings</h4>
                    <p class="text-xs text-gray-500 mb-4">These appear on your website contact section and in Google search results.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Open Days</label>
                            <select x-model="settings.shop_open_days" class="form-select-custom">
                                <option value="">— Select —</option>
                                <option value="Monday – Sunday">Monday – Sunday (All Days)</option>
                                <option value="Monday – Saturday">Monday – Saturday</option>
                                <option value="Monday – Friday">Monday – Friday</option>
                                <option value="Tuesday – Sunday">Tuesday – Sunday</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Holiday / Closed Note</label>
                            <input x-model="settings.shop_holiday" type="text" class="form-input-custom" placeholder="e.g. Sunday Closed">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Opening Time</label>
                            <input x-model="settings.shop_open_time" type="time" class="form-input-custom">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Closing Time</label>
                            <input x-model="settings.shop_close_time" type="time" class="form-input-custom">
                        </div>
                    </div>
                    <div x-show="settings.shop_open_days && settings.shop_open_time && settings.shop_close_time" class="mt-3 inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 border border-blue-100 text-xs text-blue-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Preview: </span>
                        <strong x-text="settings.shop_open_days + ' · ' + settings.shop_open_time + ' – ' + settings.shop_close_time"></strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- ─── Appearance ─── --}}
        <div class="card">
            <div class="card-header"><h3 class="text-lg font-semibold">Appearance</h3></div>
            <div class="card-body">
                <div class="rounded-[28px] border border-white/60 bg-white/80 p-5 shadow-[0_20px_60px_-28px_rgba(15,23,42,0.35)] backdrop-blur">
                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                        <div>
                            <h4 class="text-xl font-semibold text-slate-900">Appearance</h4>
                            <p class="mt-1 max-w-2xl text-sm text-slate-500">Control the motion style for page transitions and hover effects.</p>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            Atelier Glass Theme
                        </div>
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
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">Current Settings</p>
                            <div class="mt-3 flex items-center justify-between rounded-2xl bg-white px-4 py-3 shadow-sm">
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Atelier Glass</div>
                                    <div class="text-xs text-slate-500">Bright editorial workspace with refined glass panels.</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-400">Motion</div>
                                    <div class="mt-1 text-sm font-semibold text-slate-700" x-text="formatMotionLabel(settings.ui_motion)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <button @click="saveSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Appearance</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         Landing Page Settings
    ══════════════════════════════════════════════════════════ --}}
    <div x-show="tab==='landing'" x-cloak>
        <div class="space-y-5" x-data="landingPageSettings()" x-init="loadLanding()">

            {{-- Hero Section --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-100">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Hero Section</h3>
                        <p class="text-xs text-gray-500">The main banner area visitors see first</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chip Text</label>
                        <input x-model="landing.hero_chip" type="text" class="form-input-custom" placeholder="e.g. Trusted by 10,000+ Customers">
                        <p class="text-xs text-gray-500 mt-1">Small badge text above the title</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input x-model="landing.hero_title" type="text" class="form-input-custom" placeholder="e.g. Expert Device Repair You Can Trust">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subtitle</label>
                        <textarea x-model="landing.hero_subtitle" class="form-input-custom" rows="2" placeholder="e.g. Fast, reliable repair for smartphones, tablets & laptops..."></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t">
                        <template x-for="n in [1,2,3]" :key="n">
                            <div class="p-3 rounded-xl border border-gray-200 bg-gray-50/60">
                                <p class="text-xs font-semibold text-gray-500 mb-2" x-text="'Stat ' + n"></p>
                                <div class="space-y-2">
                                    <input x-model="landing['stat'+n+'_value']" type="text" class="form-input-custom" placeholder="e.g. 5000+">
                                    <input x-model="landing['stat'+n+'_label']" type="text" class="form-input-custom" placeholder="e.g. Repairs Done">
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Services Section --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Services Section</h3>
                        <p class="text-xs text-gray-500">Title and subtitle for the services grid</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input x-model="landing.services_title" type="text" class="form-input-custom" placeholder="e.g. Our Repair Services">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Subtitle</label>
                        <input x-model="landing.services_subtitle" type="text" class="form-input-custom" placeholder="e.g. We handle all major brands and devices...">
                    </div>
                </div>
            </div>

            {{-- Why Choose Us --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-purple-100">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Why Choose Us</h3>
                        <p class="text-xs text-gray-500">Highlight your advantages</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input x-model="landing.why_title" type="text" class="form-input-custom" placeholder="e.g. Why Choose RepairBox?">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Subtitle</label>
                        <input x-model="landing.why_subtitle" type="text" class="form-input-custom" placeholder="e.g. Trusted by thousands for quality repairs...">
                    </div>
                </div>
            </div>

            {{-- Contact Section --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-100">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Contact Section</h3>
                        <p class="text-xs text-gray-500">Heading for the contact area</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input x-model="landing.contact_title" type="text" class="form-input-custom" placeholder="e.g. Get In Touch">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Subtitle</label>
                        <input x-model="landing.contact_subtitle" type="text" class="form-input-custom" placeholder="e.g. Visit our store or drop us a message...">
                    </div>
                </div>
            </div>

            {{-- Map Information --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Map Information</h3>
                        <p class="text-xs text-gray-500">Google Maps embed for contact section</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Map Embed URL</label>
                        <input x-model="landing.map_embed" type="url" class="form-input-custom" placeholder="e.g. https://www.google.com/maps/embed?pb=...">
                        <p class="text-xs text-gray-400 mt-1">Paste a Google Maps embed URL. If empty, the map will auto-generate from your shop address.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Map Zoom Level</label>
                        <input x-model="landing.map_zoom" type="number" min="1" max="20" class="form-input-custom" placeholder="15">
                        <p class="text-xs text-gray-400 mt-1">Zoom level 1-20 (used when auto-generating from shop address). Default: 15</p>
                    </div>
                </div>
            </div>

            {{-- CTA Banner --}}
            <div class="card">
                <div class="card-header flex items-center gap-3">
                    <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-rose-100">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sm">Call to Action Banner</h3>
                        <p class="text-xs text-gray-500">Bottom CTA section</p>
                    </div>
                </div>
                <div class="card-body space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Title</label>
                        <input x-model="landing.cta_title" type="text" class="form-input-custom" placeholder="e.g. Ready to Fix Your Device?">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CTA Subtitle</label>
                        <input x-model="landing.cta_subtitle" type="text" class="form-input-custom" placeholder="e.g. Walk in or book online — we'll handle the rest.">
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <div class="flex items-center gap-4">
                <button @click="saveLanding()" class="btn-primary" :disabled="savingLanding">
                    <span x-show="savingLanding" class="spinner mr-1"></span> Save Landing Page Settings
                </button>
                <a href="/" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Preview Landing Page &rarr;</a>
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
        <style>@media(max-width:1024px){ .notify-grid { grid-template-columns:1fr !important; } }</style>
        <div class="notify-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; align-items:start;">

            {{-- ═══ LEFT: Toggles + Config ═══ --}}
            <div class="space-y-5">
                {{-- Email Toggles --}}
                <div class="card">
                    <div class="card-header flex items-center gap-3">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-100">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-sm">Email Notifications</h3>
                    </div>
                    <div class="card-body space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_received === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                            <input type="checkbox" :checked="settings.notify_email_received === '1'" @change="settings.notify_email_received = $event.target.checked ? '1' : '0'" class="h-4 w-4 accent-indigo-600">
                            <div><p class="font-medium text-gray-800 text-sm">Order Received</p><p class="text-xs text-gray-500">Email when repair ticket created</p></div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_email_completed === '1' ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'">
                            <input type="checkbox" :checked="settings.notify_email_completed === '1'" @change="settings.notify_email_completed = $event.target.checked ? '1' : '0'" class="h-4 w-4 accent-indigo-600">
                            <div><p class="font-medium text-gray-800 text-sm">Repair Completed</p><p class="text-xs text-gray-500">Email when repair marked completed</p></div>
                        </label>

                        {{-- Email Templates quick-edit --}}
                        <div class="pt-2">
                            <h4 class="font-semibold text-gray-700 text-xs uppercase tracking-wider mb-2">Email Templates</h4>
                            <template x-for="et in emailTemplates.filter(t => ['repair_received','repair_completed'].includes(t.template_name))" :key="et.id">
                                <div class="flex items-center justify-between p-2.5 border border-gray-200 rounded-lg mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full" :class="et.template_name === 'repair_received' ? 'bg-blue-500' : 'bg-emerald-500'"></span>
                                        <span class="text-sm font-medium" x-text="et.template_name === 'repair_received' ? 'Order Received' : 'Repair Completed'"></span>
                                        <span class="badge text-[10px]" :class="et.status==='active' ? 'badge-success' : 'badge-danger'" x-text="et.status"></span>
                                    </div>
                                    <button @click="etEditing=et; etForm={subject:et.subject,body:et.body||'',status:et.status}; showEtModal=true" class="text-indigo-600 hover:text-indigo-800 text-xs font-semibold">Edit</button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- WhatsApp Toggles + Config --}}
                <div class="card">
                    <div class="card-header flex items-center gap-3">
                        <div class="w-8 h-8 flex items-center justify-center rounded-lg bg-green-100">
                            <svg class="w-4 h-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <h3 class="font-semibold text-sm">WhatsApp Notifications</h3>
                    </div>
                    <div class="card-body space-y-3">
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_received === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                            <input type="checkbox" :checked="settings.notify_whatsapp_received === '1'" @change="settings.notify_whatsapp_received = $event.target.checked ? '1' : '0'" class="h-4 w-4 accent-green-600">
                            <div><p class="font-medium text-gray-800 text-sm">Order Received</p><p class="text-xs text-gray-500">WhatsApp when repair ticket created</p></div>
                        </label>
                        <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition" :class="settings.notify_whatsapp_completed === '1' ? 'border-green-300 bg-green-50' : 'border-gray-200'">
                            <input type="checkbox" :checked="settings.notify_whatsapp_completed === '1'" @change="settings.notify_whatsapp_completed = $event.target.checked ? '1' : '0'" class="h-4 w-4 accent-green-600">
                            <div><p class="font-medium text-gray-800 text-sm">Repair Completed</p><p class="text-xs text-gray-500">WhatsApp when repair marked completed</p></div>
                        </label>

                        <div class="pt-2 space-y-3">
                            <h4 class="font-semibold text-gray-700 text-xs uppercase tracking-wider">API Configuration</h4>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Endpoint URL</label>
                                <input x-model="settings.whatsapp_api_url" type="url" class="form-input-custom" placeholder="https://api.ultramsg.com/instanceXXXX">
                                <p class="text-xs text-gray-400 mt-1">Base URL – <code class="bg-gray-100 px-1 rounded">/sendMessage</code> appended automatically</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">API Token</label>
                                <input x-model="settings.whatsapp_api_token" type="password" class="form-input-custom" placeholder="••••••••••">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">From Number <span class="text-gray-400 font-normal">(optional)</span></label>
                                <input x-model="settings.whatsapp_from_number" type="text" class="form-input-custom" placeholder="919876543210">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-3">
                            <button @click="saveNotificationSettings()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save Settings</button>
                            <button @click="showTestNotifyModal=true" class="btn-secondary text-sm">Test Message</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ═══ RIGHT: Template Editor ═══ --}}
            <div class="card" style="position:sticky; top:1rem;">
                <div class="card-header">
                    <h3 class="font-semibold text-sm">Message Templates</h3>
                </div>
                <div class="card-body space-y-4">
                    <select x-model="selectedNotifyTemplate" class="form-select-custom">
                        <option value="whatsapp_template_received">WhatsApp: Order Received</option>
                        <option value="whatsapp_template_completed">WhatsApp: Repair Completed</option>
                    </select>
                    <textarea x-model="settings[selectedNotifyTemplate]" class="form-input-custom font-mono text-sm" rows="14"
                              placeholder="Hello {customer_name}! Your device has been received..."></textarea>
                    <details class="text-sm">
                        <summary class="cursor-pointer text-indigo-600 font-medium select-none">Available template variables</summary>
                        <div class="mt-2 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 font-mono text-xs text-gray-600">
                                <span>{customer_name}</span><span>{ticket_number}</span><span>{tracking_id}</span>
                                <span>{tracking_url}</span><span>{device_brand}</span><span>{device_model}</span>
                                <span>{estimated_cost}</span><span>{service_charge}</span><span>{grand_total}</span>
                                <span>{expected_delivery_date}</span><span>{technician_name}</span><span>{status}</span>
                                <span>{shop_name}</span><span>{shop_phone}</span>
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
    {{-- /Notifications --}}

    {{-- ═══ Print Settings ═══ --}}
    <div x-show="tab==='print'" x-cloak>
        {{-- Toolbar --}}
        <div class="flex items-center justify-between flex-wrap gap-3 mb-3">
            <div class="print-toolbar-left">
                {{-- Sub-tabs --}}
                <div class="print-subtabs">
                    <button @click="printTab='sales-invoice'; loadPrintIframe()" :class="printTab==='sales-invoice' ? 'px-4 py-1.5 text-xs font-bold rounded-lg bg-white text-gray-900 shadow-sm' : 'px-4 py-1.5 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-700'">Sales Invoice</button>
                    <button @click="printTab='repair-receipt'; loadPrintIframe()" :class="printTab==='repair-receipt' ? 'px-4 py-1.5 text-xs font-bold rounded-lg bg-white text-gray-900 shadow-sm' : 'px-4 py-1.5 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-700'">Repair Receipt</button>
                    <button @click="printTab='repair-invoice'; loadPrintIframe()" :class="printTab==='repair-invoice' ? 'px-4 py-1.5 text-xs font-bold rounded-lg bg-white text-gray-900 shadow-sm' : 'px-4 py-1.5 text-xs font-medium rounded-lg text-gray-500 hover:text-gray-700'">Repair Invoice</button>
                </div>
                {{-- Language toggle --}}
                <div style="display:inline-flex; border-radius:8px; overflow:hidden; border:2px solid #1e293b;">
                    <button @click="printLang='en'; sendLangToIframe()" :class="printLang==='en' ? 'px-3 py-1 text-[11px] font-bold bg-gray-900 text-white' : 'px-3 py-1 text-[11px] font-bold bg-white text-gray-900 hover:bg-gray-50'" style="border:none; cursor:pointer;">EN</button>
                    <button @click="printLang='ta'; sendLangToIframe()" :class="printLang==='ta' ? 'px-3 py-1 text-[11px] font-bold bg-gray-900 text-white' : 'px-3 py-1 text-[11px] font-bold bg-white text-gray-900 hover:bg-gray-50'" style="border:none; cursor:pointer;">TA</button>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="printSample()" class="btn-secondary text-sm px-4">
                    <svg class="w-3.5 h-3.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Sample
                </button>
                <button @click="savePrintSettings()" class="btn-primary text-sm px-4" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1" style="width:14px;height:14px;"></span>
                    Save Settings
                </button>
            </div>
        </div>

        <p class="text-xs text-gray-400 mb-3">Click on any <span style="background:rgba(59,130,246,.06); border:1.5px dashed rgba(59,130,246,.45); border-radius:3px; padding:0 4px;">highlighted text</span> in the preview to edit it directly.</p>

        {{-- Iframe Container --}}
        <div style="background:#e8eaed; border-radius:14px; padding:0; overflow:hidden;">
            <iframe x-ref="printIframe"
                    style="width:100%; height:calc(100vh - 200px); min-height:600px; border:none; display:block;"
                    @load="onPrintIframeLoad()"></iframe>
        </div>
    </div>

    {{-- Backups --}}
    <div x-show="tab==='backups'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Backups</h3>
            <button @click="createBackup()" class="btn-primary text-sm" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Create Backup</button>
        </div>
        <div class="card-body p-0">
            <div class="backups-table-wrap">
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
                                <a :href="'/admin/backups/' + b.id + '/download'" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-800 transition-colors" title="Download Backup">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download
                                </a>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="backups.length===0"><td colspan="6" class="text-center text-gray-400 py-6">No backups</td></tr>
                </tbody>
            </table>
            </div>{{-- /.backups-table-wrap --}}
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
                            <option value="subcategories">Subcategories</option>
                            <option value="customers">Customers</option>
                            <option value="products">Products</option>
                            <option value="parts">Parts</option>
                            <option value="vendors">Vendors</option>
                            <option value="recharge_providers">Recharge Providers</option>
                            <option value="service_types">Services</option>
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
                    <p x-show="importType === 'brands'" class="text-xs text-blue-600 mt-1">
                        <span class="font-medium">Models column:</span> enter multiple models separated by <code class="bg-blue-100 px-1 rounded">;</code> e.g. <em>Galaxy S24;Galaxy A55;Galaxy M35</em>
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
                                        <tr class="border-t" :class="(r.errors || []).length > 0 ? 'bg-red-50/50' : ''">
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
                                                <template x-if="(r.errors || []).length === 0">
                                                    <span class="text-green-600 text-sm font-medium">OK</span>
                                                </template>
                                                <template x-if="(r.errors || []).length > 0">
                                                    <div class="text-red-600 text-xs space-y-0.5">
                                                        <template x-for="err in (r.errors || [])" :key="err">
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

    {{-- SEO Tab --}}
    <div x-show="tab==='seo'" x-cloak>
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="text-lg font-semibold">SEO Management</h3>
                <p class="text-sm text-gray-500 mt-1">Manage your website's search engine optimization, blog, FAQs, and landing pages.</p>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="/admin/blog" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-200 hover:border-primary-300 hover:bg-primary-50/50 transition-colors text-center group">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Blog Posts</div>
                            <div class="text-xs text-gray-500 mt-1">Create & manage SEO-optimized blog articles</div>
                        </div>
                    </a>
                    <a href="/admin/faqs" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition-colors text-center group">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">FAQs</div>
                            <div class="text-xs text-gray-500 mt-1">Manage FAQ categories & questions with schema markup</div>
                        </div>
                    </a>
                    <a href="/admin/seo-pages" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-200 hover:border-purple-300 hover:bg-purple-50/50 transition-colors text-center group">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">SEO Pages</div>
                            <div class="text-xs text-gray-500 mt-1">Dynamic landing pages for local SEO & services</div>
                        </div>
                    </a>
                    <a href="/admin/seo-settings" class="flex flex-col items-center gap-3 p-5 rounded-xl border border-gray-200 hover:border-amber-300 hover:bg-amber-50/50 transition-colors text-center group">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">SEO Settings</div>
                            <div class="text-xs text-gray-500 mt-1">Google Analytics, Schema.org, meta defaults & scripts</div>
                        </div>
                    </a>
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
        tab: 'general', saving: false, iconFile: null, previewIcon: '', faviconFile: null, previewFavicon: '',
        settings: {}, settingKeys: ['shop_name','shop_address','shop_phone','shop_email','shop_slogan','shop_whatsapp','currency_symbol','invoice_prefix','repair_prefix','low_stock_threshold'],
        selectedNotifyTemplate: 'whatsapp_template_received',
        // Print settings state
        printTab: 'sales-invoice', printLang: 'en', printSettings: {},
        // Import state
        importType: '', importFile: null, importValidating: false, importConfirming: false,
        importResults: null, importSummary: { total: 0, creates: 0, updates: 0, errors: 0 },
        importDone: false, importDoneMessage: '',
        importTemplates: [
            { type: 'brands', label: 'Brands', columns: ['name', 'models'] },
            { type: 'categories', label: 'Categories', columns: ['name', 'description'] },
            { type: 'subcategories', label: 'Subcategories', columns: ['category', 'name'] },
            { type: 'customers', label: 'Customers', columns: ['name', 'mobile_number', 'email', 'address', 'notes'] },
            { type: 'products', label: 'Products', columns: ['name', 'sku', 'barcode', 'category', 'subcategory', 'brand', 'purchase_price', 'mrp', 'selling_price', 'description', 'opening_stock', 'image_url'] },
            { type: 'parts', label: 'Parts', columns: ['name', 'sku', 'cost_price', 'selling_price'] },
            { type: 'vendors', label: 'Vendors', columns: ['name', 'phone', 'address', 'specialization'] },
            { type: 'recharge_providers', label: 'Recharge Providers', columns: ['name'] },
            { type: 'service_types', label: 'Services', columns: ['name', 'default_price', 'description'] },
        ],
        appearanceThemes: [
            {
                id: 'atelier',
                name: 'Atelier Glass',
                description: 'Bright editorial workspace with refined glass panels.',
                preview: 'background:linear-gradient(145deg,#0f172a 0%,#2563eb 42%,#8b5cf6 100%)'
            }
        ],
        notificationSettingKeys: ['notify_email_received','notify_email_completed','notify_whatsapp_received','notify_whatsapp_completed','whatsapp_api_url','whatsapp_api_token','whatsapp_from_number','whatsapp_template_received','whatsapp_template_completed'],
        emailTemplates: [], showEtModal: false, etEditing: null, etForm: {},
        backups: [],
        showTestNotifyModal: false, testTicket: '', testType: 'received', testChannel: 'email', testResult: null,
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.get('tab') === 'service-types') {
                window.location.href = '/admin/service-types';
                return;
            }
            if (p.get('tab') === 'recharge-providers') {
                this.tab = 'master-data';
            } else if (p.has('tab')) {
                this.tab = p.get('tab');
            }
            this.load();
            // Listen for postMessage from print iframe edits
            window.addEventListener('message', (event) => {
                if (event.data && event.data.type === 'setting-changed') {
                    this.printSettings[event.data.key] = event.data.value;
                }
            });
            // If landing on print tab, init it after load
            if (this.tab === 'print') {
                setTimeout(() => this.initPrintTab(), 600);
            }
        },
        updateUrl() {
            const params = new URLSearchParams(window.location.search);
            if (this.tab !== 'general') {
                params.set('tab', this.tab);
            } else {
                params.delete('tab');
            }
            if (this.tab !== 'master-data') {
                params.delete('section');
            }
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [s, et, b] = await Promise.all([
                RepairBox.ajax('/admin/settings'),
                RepairBox.ajax('/admin/email-templates'),
                RepairBox.ajax('/admin/backups')
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
                RepairBox.ajax('/admin/email-templates').then(r => { if(r.data) this.emailTemplates = r.data; });
            }
        },
        async saveNotificationSettings() {
            this.saving = true;
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('section', 'notifications');
                this.notificationSettingKeys.forEach(k => {
                    if (this.settings[k] !== undefined && this.settings[k] !== null)
                        formData.append('settings['+k+']', this.settings[k]);
                });
                const r = await fetch('/admin/settings', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: formData
                });
                if (!r.ok) {
                    const err = await r.json().catch(() => null);
                    throw new Error(err?.message || `HTTP ${r.status}`);
                }
                const data = await r.json();
                if (data.success !== false) RepairBox.toast('Notification settings saved', 'success');
                else RepairBox.toast(data.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: '+e.message, 'error'); }
            this.saving = false;
        },
        async sendTestNotification() {
            if (!this.testTicket.trim()) { RepairBox.toast('Enter a ticket number', 'warning'); return; }
            this.saving = true; this.testResult = null;
            const r = await RepairBox.ajax('/admin/notifications/test', 'POST', { ticket: this.testTicket, type: this.testType, channel: this.testChannel });
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
            return RepairBox.imageUrl(icon);
        },
        handleFaviconUpload(e) {
            const file = e.target.files[0];
            if (file) {
                this.faviconFile = file;
                const reader = new FileReader();
                reader.onload = (evt) => { this.previewFavicon = evt.target.result; };
                reader.readAsDataURL(file);
            }
        },
        getFaviconUrl() {
            const fav = this.settings.shop_favicon;
            if (!fav) return '';
            if (fav.startsWith('http') || fav.startsWith('data:')) return fav;
            return RepairBox.imageUrl(fav);
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
            const keys = { brands: 'name', categories: 'name', subcategories: 'name', customers: 'mobile_number', products: 'sku', parts: 'sku', vendors: 'name', recharge_providers: 'name', service_types: 'name' };
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
                const response = await fetch('/admin/import/validate', {
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
                const r = await RepairBox.ajax('/admin/import/confirm', 'POST');
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
            const generalKeys = ['shop_name','shop_address','shop_phone','shop_email','shop_slogan','shop_whatsapp','shop_open_days','shop_open_time','shop_close_time','shop_holiday','currency_symbol','invoice_prefix','repair_prefix','low_stock_threshold','ui_theme','ui_motion'];
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('section', 'general');
                generalKeys.forEach(key => {
                    if (this.settings[key] !== null && this.settings[key] !== undefined) {
                        formData.append(`settings[${key}]`, this.settings[key]);
                    }
                });
                if (this.iconFile) {
                    formData.append('shop_icon', this.iconFile);
                }
                if (this.faviconFile) {
                    formData.append('shop_favicon', this.faviconFile);
                }

                const response = await fetch('/admin/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (!response.ok) {
                    const err = await response.json().catch(() => null);
                    throw new Error(err?.message || `HTTP ${response.status}`);
                }

                const r = await response.json();
                this.saving = false;
                this.iconFile = null;
                this.faviconFile = null;
                if (r.success !== false) {
                    RepairBox.toast('General settings saved', 'success');
                    this.previewIcon = '';
                    this.iconFile = null;
                    this.previewFavicon = '';
                    this.faviconFile = null;
                } else {
                    RepairBox.toast(r.message || 'Error saving settings', 'error');
                }
            } catch (err) {
                this.saving = false;
                console.error('Save error:', err);
                RepairBox.toast('Error: ' + err.message, 'error');
            }
        },
        clearIconPreview() {
            this.previewIcon = '';
            this.iconFile = null;
            this.previewFavicon = '';
            this.faviconFile = null;
        },
        // ── Print Settings methods ──
        initPrintTab() {
            const printKeys = [
                'invoice_header_title_en','invoice_header_title_ta','invoice_footer_text','invoice_footer_text_ta',
                'invoice_sign_label_en','invoice_sign_label_ta','invoice_default_language','invoice_paper_size',
                'invoice_shop_name_ta','invoice_shop_slogan_ta','invoice_shop_address_ta',
                'receipt_header_title_en','receipt_header_title_ta','receipt_notes_en','receipt_notes_ta',
                'receipt_footer_text','receipt_footer_text_ta','receipt_sign_label_en','receipt_sign_label_ta',
                'receipt_shop_name_ta','receipt_shop_slogan_ta','receipt_shop_address_ta',
                'repair_invoice_header_title_en','repair_invoice_header_title_ta',
                'repair_invoice_footer_text','repair_invoice_footer_text_ta',
            ];
            printKeys.forEach(k => { this.printSettings[k] = this.settings[k] || ''; });
            this.printLang = this.settings.invoice_default_language || 'en';
            this.$nextTick(() => this.loadPrintIframe());
        },
        loadPrintIframe() {
            const iframe = this.$refs.printIframe;
            if (iframe) iframe.src = '/admin/print-preview/' + this.printTab + '?edit=1';
        },
        onPrintIframeLoad() {
            const iframe = this.$refs.printIframe;
            if (iframe && iframe.contentWindow) {
                try { iframe.contentWindow.postMessage({ type: 'init-edit-mode', lang: this.printLang }, '*'); } catch(e) {}
            }
        },
        sendLangToIframe() {
            const iframe = this.$refs.printIframe;
            if (iframe && iframe.contentWindow) {
                try { iframe.contentWindow.postMessage({ type: 'switch-lang', lang: this.printLang }, '*'); } catch(e) {}
            }
        },
        printSample() {
            const iframe = this.$refs.printIframe;
            if (iframe && iframe.contentWindow) { iframe.contentWindow.print(); }
        },
        async savePrintSettings() {
            this.saving = true;
            try {
                const fd = new FormData();
                fd.append('_method', 'PUT');
                fd.append('section', 'print');
                Object.keys(this.printSettings).forEach(k => {
                    if (this.printSettings[k] !== null && this.printSettings[k] !== undefined)
                        fd.append('settings['+k+']', this.printSettings[k]);
                });
                const r = await fetch('/admin/settings', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                    body: fd
                });
                if (!r.ok) { const err = await r.json().catch(() => null); throw new Error(err?.message || 'HTTP '+r.status); }
                const d = await r.json();
                if (d.success !== false) {
                    RepairBox.toast('Print settings saved!', 'success');
                    this.loadPrintIframe(); // reload to reflect changes
                } else RepairBox.toast(d.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: '+e.message, 'error'); }
            this.saving = false;
        },
        async saveEmailTemplate() {
            this.saving = true;
            const r = await RepairBox.ajax(`/admin/email-templates/${this.etEditing.id}`, 'PUT', this.etForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showEtModal = false; const et = await RepairBox.ajax('/admin/email-templates'); if(et.data) this.emailTemplates = et.data; }
        },
        async createBackup() {
            this.saving = true;
            const r = await RepairBox.ajax('/admin/backups', 'POST');
            this.saving = false; if(r.success !== false) { RepairBox.toast('Backup created', 'success'); const b = await RepairBox.ajax('/admin/backups'); if(b.data) this.backups = b.data; }
        }
    };
}

function landingPageSettings() {
    return {
        landing: {},
        savingLanding: false,
        async loadLanding() {
            try {
                const r = await RepairBox.ajax('/admin/settings');
                if (r.data && r.data.landing_page) {
                    try { this.landing = JSON.parse(r.data.landing_page); } catch(e) { this.landing = {}; }
                }
            } catch(e) { console.error('Failed to load landing settings', e); }
        },
        async saveLanding() {
            this.savingLanding = true;
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('section', 'landing');
                Object.keys(this.landing).forEach(k => {
                    formData.append('settings[' + k + ']', this.landing[k] || '');
                });
                const response = await fetch('/admin/settings', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });
                if (!response.ok) {
                    const err = await response.json().catch(() => null);
                    throw new Error(err?.message || 'HTTP ' + response.status);
                }
                const r = await response.json();
                if (r.success !== false) RepairBox.toast('Landing page settings saved', 'success');
                else RepairBox.toast(r.message || 'Error', 'error');
            } catch(e) { RepairBox.toast('Error: ' + e.message, 'error'); }
            this.savingLanding = false;
        }
    };
}

function masterDataPanel() {
    const sectionConfig = {
        vendors:    { label: 'Vendor Management', singular: 'Vendor',   url: '/admin/vendors',    deleteUrl: null },
        inventory:  { label: 'Inventory',         singular: 'Stock Adjustment', url: '/admin/inventory', deleteUrl: null },
        brands:     { label: 'Brands',            singular: 'Brand',    url: '/admin/brands',     deleteUrl: '/admin/brands' },
        categories: { label: 'Categories',        singular: 'Category', url: '/admin/categories', deleteUrl: '/admin/categories' },
        parts:      { label: 'Parts',             singular: 'Part',     url: '/admin/parts',      deleteUrl: '/admin/parts' },
        products:   { label: 'Products',          singular: 'Product',  url: '/admin/products',   deleteUrl: '/admin/products' },
        customers:  { label: 'Customers',         singular: 'Customer', url: '/admin/customers',  deleteUrl: '/admin/customers' },
        'recharge-providers': { label: 'Recharge Providers', singular: 'Provider', url: '/admin/recharge-providers', deleteUrl: '/admin/recharge-providers' },
        services:  { label: 'Services', singular: 'Service', url: '/admin/service-types', deleteUrl: '/admin/service-types' },
        users:     { label: 'Users',    singular: 'User',    url: '/admin/users',         deleteUrl: '/admin/users' },
        roles:     { label: 'Roles & Permissions', singular: 'Role', url: '/admin/roles', deleteUrl: '/admin/roles' },
    };

    return {
        mdSection: new URLSearchParams(window.location.search).get('section') || 'parts',
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
        mdRolesList: [],
        showCatSubModal: false,
        catSubItem: null,
        catSubItems: [],
        catSubLoading: false,
        catSubSaving: false,
        catSubNewName: '',
        catSubEditing: null,
        catSubEditName: '',
        catSubEditStatus: 'active',
        svcQuickFillTags: [
            'Xerox / Photocopy', 'Lamination', 'Screen Replacement', 'Battery Replacement',
            'Charging Port Repair', 'Software / Flashing', 'Data Recovery', 'Water Damage Repair',
            'Speaker Repair', 'Mic Repair', 'Camera Repair', 'Back Panel Replacement',
            'Keyboard Repair', 'Motherboard Repair', 'SIM Tray Replace', 'General Service',
        ],
        svcNewQuickFill: '',
        mdNewModel: '',
        mdImageFile: null, mdImagePreview: null,
        mdSubcategories: [],

        get mdSectionLabel() { return sectionConfig[this.mdSection]?.label || ''; },
        get mdSectionLabelSingular() { return sectionConfig[this.mdSection]?.singular || ''; },

        switchSection(section) {
            this.mdSection = section;
            this.mdSearch = '';
            this.mdEditing = null;
            this.mdForm = {};
            this.loadMdData();
            if (new URLSearchParams(window.location.search).get('tab') === 'master-data') {
                this.resyncSectionUrl();
            }
        },

        resyncSectionUrl() {
            const params = new URLSearchParams(window.location.search);
            params.set('section', this.mdSection);
            history.replaceState(null, '', window.location.pathname + '?' + params.toString());
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

        async openMdAdd() {
            this.mdEditing = null;
            this.mdForm = this.getDefaultForm();
            this.mdImageFile = null;
            this.mdImagePreview = null;

            if (this.mdSection === 'products') {
                this.loadDropdowns();
            }
            if (this.mdSection === 'inventory') {
                this.loadProducts();
            }
            if (this.mdSection === 'users') {
                await this.loadRolesList();
            }
            this.showMdModal = true;
        },

        async openMdEdit(item) {
            if (this.mdSection === 'inventory') return;
            this.mdEditing = item.id;
            this.mdForm = { ...item };
            this.mdImageFile = null;
            this.mdImagePreview = RepairBox.imageUrl(item.thumbnail || item.image);

            if (this.mdSection === 'brands') {
                this.mdForm.models = item.models ? [...item.models] : [];
                this.mdNewModel = '';
            }

            if (this.mdSection === 'services' && !this.mdForm.quick_fills) {
                this.mdForm.quick_fills = [];
            }

            if (this.mdSection === 'products') {
                this.loadDropdowns();
            }
            if (this.mdSection === 'users') {
                this.mdForm.password = '';
                this.mdForm.password_confirmation = '';
                await this.loadRolesList();
            }
            this.showMdModal = true;
        },

        addMdModel() {
            const m = this.mdNewModel.trim();
            if (!m) return;
            if (!this.mdForm.models) this.mdForm.models = [];
            if (!this.mdForm.models.includes(m)) this.mdForm.models.push(m);
            this.mdNewModel = '';
        },

        getDefaultForm() {
            switch(this.mdSection) {
                case 'vendors': return { name: '', phone: '', specialization: '', address: '' };
                case 'brands': return { name: '', logo_url: '', models: [] };
                case 'categories': return { name: '', description: '' };
                case 'parts': return { name: '', sku: '', cost_price: '', selling_price: '' };
                case 'products': return { name: '', sku: '', category_id: '', subcategory_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', description: '', opening_stock: '' };
                case 'customers': return { name: '', mobile_number: '', email: '', address: '' };
                case 'inventory': return { product_id: '', adjustment_type: 'addition', quantity: '', reason: '' };
                case 'recharge-providers': return { name: '' };
                case 'services': return { name: '', default_price: '', description: '', quick_fills: [] };
                case 'users': return { name: '', email: '', password: '', password_confirmation: '', role_id: '', status: 'active' };
                case 'roles': return { name: '', description: '' };
                default: return {};
            }
        },

        async loadDropdowns() {
            const [cats, brands] = await Promise.all([
                RepairBox.ajax('/admin/categories'),
                RepairBox.ajax('/admin/brands')
            ]);
            this.mdCategories = Array.isArray(cats) ? cats : (cats.data || []);
            this.mdBrands = Array.isArray(brands) ? brands : (brands.data || []);
            // Load subcategories for the currently selected category (for edit mode)
            if (this.mdForm.category_id) {
                await this.mdLoadSubcategories();
            }
        },

        async mdLoadSubcategories() {
            if (!this.mdForm.category_id) { this.mdSubcategories = []; return; }
            const r = await RepairBox.ajax(`/admin/subcategories/by-category/${this.mdForm.category_id}`);
            this.mdSubcategories = r.data || r || [];
        },

        async loadProducts() {
            const r = await RepairBox.ajax('/admin/products');
            this.mdProducts = Array.isArray(r) ? r : (r.data || []);
        },

        async loadRolesList() {
            const r = await RepairBox.ajax('/admin/roles');
            this.mdRolesList = Array.isArray(r) ? r : (r.data || []);
        },

        async openCatSubModal(item) {
            this.catSubItem = item;
            this.catSubNewName = '';
            this.catSubEditing = null;
            this.catSubEditName = '';
            this.catSubEditStatus = 'active';
            this.showCatSubModal = true;
            await this.loadCatSubItems();
        },

        async loadCatSubItems() {
            this.catSubLoading = true;
            const r = await RepairBox.ajax(`/admin/subcategories?category_id=${this.catSubItem.id}&per_page=200`);
            this.catSubItems = Array.isArray(r) ? r : (r.data || []);
            this.catSubLoading = false;
        },

        async addCatSub() {
            if (!this.catSubNewName.trim()) return;
            this.catSubSaving = true;
            const r = await RepairBox.ajax('/admin/subcategories', 'POST', { category_id: this.catSubItem.id, name: this.catSubNewName.trim() });
            this.catSubSaving = false;
            if (r.success !== false) {
                this.catSubNewName = '';
                await this.loadCatSubItems();
                // update count in parent list
                const parent = this.mdItems.find(i => i.id === this.catSubItem.id);
                if (parent) parent.subcategories = this.catSubItems;
                RepairBox.toast('Subcategory added', 'success');
            }
        },

        startEditCatSub(sub) {
            this.catSubEditing = sub.id;
            this.catSubEditName = sub.name;
            this.catSubEditStatus = sub.status || 'active';
        },

        cancelCatSubEdit() {
            this.catSubEditing = null;
            this.catSubEditName = '';
            this.catSubEditStatus = 'active';
        },

        async saveCatSubEdit() {
            if (!this.catSubEditName.trim()) return;
            this.catSubSaving = true;
            const r = await RepairBox.ajax(`/admin/subcategories/${this.catSubEditing}`, 'PUT', { category_id: this.catSubItem.id, name: this.catSubEditName.trim(), status: this.catSubEditStatus });
            this.catSubSaving = false;
            if (r.success !== false) {
                this.cancelCatSubEdit();
                await this.loadCatSubItems();
                const parent = this.mdItems.find(i => i.id === this.catSubItem.id);
                if (parent) parent.subcategories = this.catSubItems;
                RepairBox.toast('Subcategory updated', 'success');
            }
        },

        async deleteCatSub(sub) {
            if (!confirm(`Delete subcategory "${sub.name}"?`)) return;
            const r = await RepairBox.ajax(`/admin/subcategories/${sub.id}`, 'DELETE');
            if (r.success !== false) {
                await this.loadCatSubItems();
                const parent = this.mdItems.find(i => i.id === this.catSubItem.id);
                if (parent) parent.subcategories = this.catSubItems;
                RepairBox.toast('Subcategory deleted', 'success');
            }
        },

        mdHandlePick(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.mdImageFile = file;
            const reader = new FileReader();
            reader.onload = ev => this.mdImagePreview = ev.target.result;
            reader.readAsDataURL(file);
        },
        mdHandleDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file || !file.type.startsWith('image/')) return;
            this.mdImageFile = file;
            const reader = new FileReader();
            reader.onload = ev => this.mdImagePreview = ev.target.result;
            reader.readAsDataURL(file);
        },

        mdGetUploadUrl(id) {
            const urls = {
                'brands': `/admin/brands/${id}/upload-image`,
                'parts': `/admin/parts/${id}/upload-image`,
                'vendors': `/admin/vendors/${id}/upload-image`,
                'recharge-providers': `/admin/recharge-providers/${id}/upload-image`,
                'products': `/admin/products/${id}/upload-image`,
                'services': `/admin/service-types/${id}/upload-image`,
            };
            return urls[this.mdSection] || null;
        },

        async saveMdItem() {
            if (!this.mdForm.name && this.mdSection !== 'inventory') {
                return RepairBox.toast('Name is required', 'error');
            }
            if (this.mdSection === 'inventory' && (!this.mdForm.product_id || !this.mdForm.quantity)) {
                return RepairBox.toast('Product and quantity are required', 'error');
            }
            if (this.mdSection === 'users' && !this.mdEditing && !this.mdForm.password) {
                return RepairBox.toast('Password is required', 'error');
            }

            this.mdSaving = true;
            const cfg = sectionConfig[this.mdSection];

            if (this.mdSection === 'users') {
                const data = { ...this.mdForm };
                if (!data.password) { delete data.password; delete data.password_confirmation; }
                const url = this.mdEditing ? `${cfg.url}/${this.mdEditing}` : cfg.url;
                const method = this.mdEditing ? 'PUT' : 'POST';
                const r = await RepairBox.ajax(url, method, data);
                this.mdSaving = false;
                if (r.success !== false) {
                    RepairBox.toast(this.mdEditing ? 'User updated' : 'User created', 'success');
                    this.showMdModal = false;
                    this.mdEditing = null;
                    this.mdForm = {};
                    await this.loadMdData();
                }
                return;
            }

            if (this.mdSection === 'inventory') {
                const r = await RepairBox.ajax('/admin/inventory/adjust', 'POST', this.mdForm);
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
                const savedId = (r.data && r.data.id) || this.mdEditing;
                // Upload image if any
                if (this.mdImageFile && savedId) {
                    const uploadUrl = this.mdGetUploadUrl(savedId);
                    if (uploadUrl) {
                        const fd = new FormData();
                        fd.append('image', this.mdImageFile);
                        await RepairBox.upload(uploadUrl, fd);
                    }
                }
                RepairBox.toast(this.mdEditing ? `${cfg.singular} updated` : `${cfg.singular} added`, 'success');
                this.showMdModal = false;
                this.mdEditing = null;
                this.mdForm = {};
                this.mdImageFile = null;
                this.mdImagePreview = null;
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
