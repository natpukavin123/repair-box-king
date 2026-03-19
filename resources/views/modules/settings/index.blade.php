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
        <button @click="tab='service-types'; updateUrl()" :class="tab==='service-types' ? 'secondary-tab is-active' : 'secondary-tab'">Service Types</button>
        <button @click="tab='recharge-providers'; updateUrl()" :class="tab==='recharge-providers' ? 'secondary-tab is-active' : 'secondary-tab'">Recharge Providers</button>
        <button @click="tab='email-templates'; updateUrl()" :class="tab==='email-templates' ? 'secondary-tab is-active' : 'secondary-tab'">Email Templates</button>
        <button @click="tab='notifications'; updateUrl(); loadNotifications()" :class="tab==='notifications' ? 'secondary-tab is-active' : 'secondary-tab'">Notifications</button>
        <button @click="tab='print-settings'; updateUrl()" :class="tab==='print-settings' ? 'secondary-tab is-active' : 'secondary-tab'">Print Settings</button>
        <button @click="tab='backups'; updateUrl()" :class="tab==='backups' ? 'secondary-tab is-active' : 'secondary-tab'">Backups</button>
    </div>

    {{-- Master Data --}}
    <div x-show="tab==='master-data'">
        <style>
            .md-card {
                display: flex; align-items: center; gap: 14px; padding: 16px 20px;
                background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
                text-decoration: none; transition: all 0.2s ease;
            }
            .md-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px -4px rgba(0,0,0,0.1); border-color: #d1d5db; }
            .md-icon {
                width: 42px; height: 42px; border-radius: 12px;
                display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            }
            .md-label { font-size: 14px; font-weight: 600; color: #1f2937; }
            .md-desc { font-size: 11px; color: #6b7280; margin-top: 2px; }
        </style>

        {{-- Data Management --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Data Management</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <a href="/customers" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#22c55e,#16a34a);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div><div class="md-label">Customers</div><div class="md-desc">Manage customer details</div></div>
                </a>
                <a href="/suppliers" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div><div class="md-label">Suppliers</div><div class="md-desc">Manage suppliers</div></div>
                </a>
                <a href="/products" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div><div class="md-label">Products</div><div class="md-desc">Add & edit products</div></div>
                </a>
                <a href="/parts" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#f97316,#ea580c);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div><div class="md-label">Parts</div><div class="md-desc">Repair spare parts</div></div>
                </a>
                <a href="/categories" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#14b8a6,#0d9488);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                    <div><div class="md-label">Categories</div><div class="md-desc">Product categories</div></div>
                </a>
                <a href="/brands" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#ec4899,#db2777);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    </div>
                    <div><div class="md-label">Brands</div><div class="md-desc">Product brands</div></div>
                </a>
                <a href="/inventory" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                    <div><div class="md-label">Inventory</div><div class="md-desc">Stock levels</div></div>
                </a>
                <a href="/purchases" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#6366f1,#4f46e5);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                    <div><div class="md-label">Purchases</div><div class="md-desc">Purchase orders</div></div>
                </a>
                <a href="/services" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div><div class="md-label">Services</div><div class="md-desc">Service records</div></div>
                </a>
            </div>
        </div>

        {{-- Finance & Reports --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Finance & Reports</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <a href="/ledger" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div><div class="md-label">Ledger</div><div class="md-desc">Financial transactions</div></div>
                </a>
                <a href="/reports" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#0ea5e9,#0284c7);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div><div class="md-label">Reports</div><div class="md-desc">Business analytics</div></div>
                </a>
            </div>
        </div>

        {{-- System --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">System</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                <a href="/users" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#64748b,#475569);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div><div class="md-label">Users</div><div class="md-desc">User accounts</div></div>
                </a>
                <a href="/roles" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <div><div class="md-label">Roles & Permissions</div><div class="md-desc">Access control</div></div>
                </a>
                <a href="/vendors" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#f97316,#c2410c);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div><div class="md-label">Vendors</div><div class="md-desc">Repair vendors</div></div>
                </a>
                <a href="/menus" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#334155,#1e293b);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </div>
                    <div><div class="md-label">Menus</div><div class="md-desc">Navigation manager</div></div>
                </a>
                <a href="/activity-logs" class="md-card">
                    <div class="md-icon" style="background:linear-gradient(135deg,#78716c,#57534e);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div><div class="md-label">Activity Logs</div><div class="md-desc">System activity</div></div>
                </a>
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

    {{-- Service Types --}}
    <div x-show="tab==='service-types'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Service Types</h3>
            <button @click="stForm={name:'',default_price:'',description:''}; stEditing=null; stImageFile=null; stImagePreview=null; stThumbFile=null; stThumbPreview=null; showStModal=true" class="btn-primary text-sm">Add Type</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Image</th><th>Name</th><th>Default Price</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <template x-for="st in serviceTypes" :key="st.id">
                        <tr>
                            <td>
                                <template x-if="st.thumbnail">
                                    <img :src="'/storage/' + st.thumbnail" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm">
                                </template>
                                <template x-if="!st.thumbnail">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </template>
                            </td>
                            <td class="font-medium" x-text="st.name"></td>
                            <td x-text="st.default_price ? RepairBox.formatCurrency(st.default_price) : '-'"></td>
                            <td><span class="badge" :class="st.status==='active' ? 'badge-success' : 'badge-danger'" x-text="st.status || 'active'"></span></td>
                            <td><button @click="openEditSt(st)" class="text-primary-600 hover:text-primary-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button></td>
                        </tr>
                    </template>
                    <tr x-show="serviceTypes.length===0"><td colspan="5" class="text-center text-gray-400 py-6">No service types</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recharge Providers --}}
    <div x-show="tab==='recharge-providers'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">Recharge Providers</h3>
            <button @click="rpForm={name:'',provider_type:'',commission_percentage:''}; showRpModal=true" class="btn-primary text-sm">Add Provider</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead><tr><th>Name</th><th>Type</th><th>Commission %</th></tr></thead>
                <tbody>
                    <template x-for="rp in rechargeProviders" :key="rp.id"><tr><td class="font-medium" x-text="rp.name"></td><td x-text="rp.provider_type"></td><td x-text="rp.commission_percentage + '%'"></td></tr></template>
                    <tr x-show="rechargeProviders.length===0"><td colspan="3" class="text-center text-gray-400 py-6">No providers</td></tr>
                </tbody>
            </table>
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
                <thead><tr><th>Type</th><th>File</th><th>Size</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    <template x-for="b in backups" :key="b.id">
                        <tr>
                            <td x-text="b.backup_type"></td>
                            <td class="text-sm" x-text="b.file_path"></td>
                            <td x-text="b.file_size ? (b.file_size / 1024).toFixed(1)+' KB' : '-'"></td>
                            <td><span class="badge badge-success" x-text="b.status"></span></td>
                            <td x-text="new Date(b.created_at).toLocaleString()"></td>
                        </tr>
                    </template>
                    <tr x-show="backups.length===0"><td colspan="5" class="text-center text-gray-400 py-6">No backups</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Service Type Modal --}}
    <div x-show="showStModal" class="modal-overlay" x-cloak @click.self="showStModal=false">
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="stEditing ? 'Edit Service Type' : 'Add Service Type'"></h3>
                <button @click="showStModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="stForm.name" type="text" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Default Price</label>
                    <input x-model="stForm.default_price" type="number" step="0.01" class="form-input-custom">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea x-model="stForm.description" class="form-input-custom" rows="2"></textarea>
                </div>

                {{-- Image Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Images</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Main Image</p>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.stImageInput.click()" @dragover.prevent @drop.prevent="stHandleFileDrop('image', $event)">
                                <template x-if="stImagePreview">
                                    <div class="relative">
                                        <img :src="stImagePreview" class="max-h-28 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="stImageFile=null; stImagePreview=null; $refs.stImageInput.value=''"
                                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                    </div>
                                </template>
                                <template x-if="!stImagePreview">
                                    <div class="py-3">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs text-gray-400">Click to upload</p>
                                    </div>
                                </template>
                                <input x-ref="stImageInput" type="file" accept="image/*" class="hidden" @change="stHandleFilePick('image', $event)">
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5 font-medium">Thumbnail <span class="text-gray-400">(auto if not set)</span></p>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                 @click="$refs.stThumbInput.click()" @dragover.prevent @drop.prevent="stHandleFileDrop('thumb', $event)">
                                <template x-if="stThumbPreview">
                                    <div class="relative">
                                        <img :src="stThumbPreview" class="max-h-28 mx-auto rounded-lg object-contain">
                                        <button type="button" @click.stop="stThumbFile=null; stThumbPreview=null; $refs.stThumbInput.value=''"
                                                class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                    </div>
                                </template>
                                <template x-if="!stThumbPreview">
                                    <div class="py-3">
                                        <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-xs text-gray-400">Click to upload</p>
                                    </div>
                                </template>
                                <input x-ref="stThumbInput" type="file" accept="image/*" class="hidden" @change="stHandleFilePick('thumb', $event)">
                            </div>
                        </div>
                    </div>
                </div>
                <template x-if="stEditing">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="stForm.status" class="form-select-custom">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="showStModal=false" class="btn-secondary">Cancel</button>
                <button @click="saveServiceType()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Save
                </button>
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

    {{-- Recharge Provider Modal --}}
    <div x-show="showRpModal" class="modal-overlay" x-cloak @click.self="showRpModal=false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Recharge Provider</h3><button @click="showRpModal=false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="rpForm.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select x-model="rpForm.provider_type" class="form-select-custom"><option value="">Select</option><option value="mobile">Mobile</option><option value="dth">DTH</option><option value="data_card">Data Card</option><option value="electricity">Electricity</option></select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Commission % *</label><input x-model="rpForm.commission_percentage" type="number" step="0.01" min="0" max="100" class="form-input-custom" placeholder="e.g. 3.5"></div>
            </div>
            <div class="modal-footer"><button @click="showRpModal=false" class="btn-secondary">Cancel</button><button @click="saveRechargeProvider()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Save</button></div>
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
        serviceTypes: [], showStModal: false, stEditing: null, stForm: {},
        stImageFile: null, stImagePreview: null, stThumbFile: null, stThumbPreview: null,
        rechargeProviders: [], showRpModal: false, rpForm: {},
        emailTemplates: [], showEtModal: false, etEditing: null, etForm: {},
        backups: [],
        showTestNotifyModal: false, testTicket: '', testType: 'received', testChannel: 'email', testResult: null,
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('tab')) this.tab = p.get('tab');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.tab !== 'general') params.set('tab', this.tab);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [s, st, rp, et, b] = await Promise.all([
                RepairBox.ajax('/settings'), RepairBox.ajax('/service-types'),
                RepairBox.ajax('/recharge-providers'), RepairBox.ajax('/email-templates'),
                RepairBox.ajax('/backups')
            ]);
            if (s.data) this.settings = { ui_theme: 'atelier', ui_motion: 'enhanced', ...s.data };
            else this.settings = { ui_theme: 'atelier', ui_motion: 'enhanced' };
            this.applyAppearancePreview();
            if(st.data) this.serviceTypes = st.data;
            if(rp.data) this.rechargeProviders = rp.data; if(et.data) this.emailTemplates = et.data;
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
        async saveServiceType() {
            this.saving = true;
            const r = await RepairBox.ajax(this.stEditing ? `/service-types/${this.stEditing}` : '/service-types', this.stEditing ? 'PUT' : 'POST', this.stForm);
            if (r.success !== false && r.data) {
                const id = r.data.id || this.stEditing;
                if (this.stImageFile || this.stThumbFile) {
                    const fd = new FormData();
                    if (this.stImageFile) fd.append('image', this.stImageFile);
                    if (this.stThumbFile) fd.append('thumbnail', this.stThumbFile);
                    await RepairBox.upload(`/service-types/${id}/upload-image`, fd);
                }
                RepairBox.toast('Saved', 'success'); this.showStModal = false;
                const st = await RepairBox.ajax('/service-types'); if(st.data) this.serviceTypes = st.data;
            }
            this.saving = false;
        },
        openEditSt(st) {
            this.stEditing = st.id;
            this.stForm = { name: st.name, default_price: st.default_price || '', description: st.description || '', status: st.status || 'active' };
            this.stImageFile = null; this.stThumbFile = null;
            this.stImagePreview = st.image ? '/storage/' + st.image : null;
            this.stThumbPreview = st.thumbnail ? '/storage/' + st.thumbnail : null;
            this.showStModal = true;
        },
        stHandleFilePick(type, e) {
            const file = e.target.files[0]; if (!file) return;
            if (type === 'image') { this.stImageFile = file; const r = new FileReader(); r.onload = ev => this.stImagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.stThumbFile = file; const r = new FileReader(); r.onload = ev => this.stThumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        stHandleFileDrop(type, e) {
            const file = e.dataTransfer.files[0]; if (!file || !file.type.startsWith('image/')) return;
            if (type === 'image') { this.stImageFile = file; const r = new FileReader(); r.onload = ev => this.stImagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.stThumbFile = file; const r = new FileReader(); r.onload = ev => this.stThumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        async saveRechargeProvider() {
            this.saving = true;
            const r = await RepairBox.ajax('/recharge-providers', 'POST', this.rpForm);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Saved', 'success'); this.showRpModal = false; const rp = await RepairBox.ajax('/recharge-providers'); if(rp.data) this.rechargeProviders = rp.data; }
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
</script>
@endpush
