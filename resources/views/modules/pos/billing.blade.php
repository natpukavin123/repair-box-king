@extends('layouts.app')
@section('page-title', 'POS Billing')
@section('content-class', 'workspace-content')

@php
    $canViewCostPrice = $canViewCostPrice ?? false;
@endphp

@section('content')
<style>
    @keyframes search-pulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.45); }
        50%       { box-shadow: 0 0 0 6px rgba(99, 102, 241, 0); }
    }
    .search-glow {
        animation: search-pulse 1.4s ease-in-out infinite;
    }
    @keyframes search-idle-ring {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.12), inset 0 1px 0 rgba(255,255,255,0.6); }
        50%       { box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.06), inset 0 1px 0 rgba(255,255,255,0.6); }
    }
    .search-idle-ring {
        animation: search-idle-ring 3s ease-in-out infinite;
    }

    .sales-workspace {
        --sales-panel-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(246, 249, 255, 0.88));
        --sales-panel-border: rgba(148, 163, 184, 0.18);
        --sales-shadow: 0 22px 48px -34px rgba(15, 23, 42, 0.34);
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sales-workspace .sales-toolbar,
    .sales-workspace .sales-filterbar,
    .sales-workspace .sales-panel,
    .sales-workspace .sales-item-card {
        border: 1px solid var(--sales-panel-border);
        background: var(--sales-panel-bg);
        box-shadow: var(--sales-shadow);
    }

    .sales-workspace .sales-toolbar,
    .sales-workspace .sales-filterbar {
        border-radius: 1.2rem;
        backdrop-filter: blur(16px);
    }

    .sales-workspace .sales-toolbar {
        padding: 0.55rem;
    }

    .sales-workspace .sales-filterbar {
        padding: 0.45rem;
    }

    .sales-workspace .sales-segmented {
        padding: 0.22rem;
        border-radius: 0.95rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        background: rgba(241, 245, 249, 0.9);
    }

    .sales-workspace .sales-segmented > button {
        min-height: 2.5rem;
        border-radius: 0.78rem;
    }

    .sales-workspace .sales-field,
    .sales-workspace .sales-select {
        min-height: 2.7rem;
        border-radius: 0.95rem;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.72), 0 12px 28px -24px rgba(15, 23, 42, 0.28);
    }

    .sales-workspace .sales-panel {
        border-radius: 1.35rem;
        overflow: hidden;
    }

    .sales-workspace .sales-panel .card-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(241, 245, 255, 0.48));
    }

    .sales-workspace .sales-panel .card-body {
        padding: 1rem;
    }

    .sales-workspace .sales-item-card {
        border-radius: 1rem;
        height: 100%;
    }

    .sales-workspace .sales-item-grid {
        grid-auto-rows: 148px;
    }

    .sales-workspace .sales-item-card:hover {
        transform: translateY(-1px);
    }

    .sales-workspace .sales-cart-row {
        padding: 0.7rem 0.85rem;
    }

    .sales-workspace .sales-summary,
    .sales-workspace .sales-actionbar {
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
    }

    @media (max-width: 1023px) {
        .sales-workspace .sales-toolbar,
        .sales-workspace .sales-filterbar,
        .sales-workspace .sales-panel .card-header,
        .sales-workspace .sales-panel .card-body,
        .sales-workspace .sales-summary,
        .sales-workspace .sales-actionbar {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }
    }

    .pos-main-grid {
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
</style>

<div x-data="posBilling()" x-init="init()" class="workspace-screen sales-workspace w-full">
    <div class="pos-main-grid grid w-full lg:flex-1 lg:min-h-0 grid-cols-1 gap-2 lg:grid-cols-3 lg:grid-rows-1">

        {{-- LEFT: Product / Service Search --}}
        <div class="flex lg:min-h-0 flex-col lg:overflow-hidden lg:col-span-2">

            {{-- Search bar + type selector --}}
            <div class="sales-toolbar mb-3 flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="flex items-center flex-1 gap-0" style="position:relative;">
                    <span class="pointer-events-none text-gray-400" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); z-index:2; line-height:0;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input x-model="searchQuery" @input.debounce.250ms="searchProducts()" type="text"
                        placeholder="Search by name, SKU, barcode..."
                        :class="searchQuery ? 'ring-2 ring-primary-400 border-primary-400 search-glow' : 'search-idle-ring'"
                        class="form-input-custom sales-field w-full transition-all duration-300"
                        style="padding-left:2.25rem; padding-right:2.5rem;" autofocus>
                    <button x-show="searchQuery" x-cloak type="button"
                        @click="searchQuery = ''; searchProducts()"
                        class="flex items-center justify-center rounded-full bg-gray-200 hover:bg-red-100 text-gray-500 hover:text-red-500 transition-colors"
                        style="position:absolute; right:0.625rem; top:50%; transform:translateY(-50%); z-index:10; width:1.5rem; height:1.5rem; flex-shrink:0;"
                        title="Clear search">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="sales-segmented inline-flex w-full overflow-x-auto sm:w-auto">
                    <button type="button"
                        @click="switchTab('product'); searchProducts()"
                        :class="itemType === 'product' ? 'bg-white text-primary-700 shadow-sm border-primary-200' : 'text-gray-600 hover:text-gray-800'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all">
                        Product
                    </button>
                    <button type="button"
                        @click="switchTab('service')"
                        :class="itemType === 'service' ? 'bg-white text-primary-700 shadow-sm border-primary-200' : 'text-gray-600 hover:text-gray-800'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all">
                        Service
                    </button>
                    <button type="button"
                        @click="switchTab('manual')"
                        :class="itemType === 'manual' ? 'bg-white text-primary-700 shadow-sm border-primary-200' : 'text-gray-600 hover:text-gray-800'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all">
                        Manual Entry
                    </button>
                    <button type="button"
                        @click="switchTab('recharge'); loadCustomerRecharges()"
                        :class="itemType === 'recharge' ? 'bg-white text-teal-700 shadow-sm border-teal-200' : 'text-gray-600 hover:text-gray-800'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all">
                        Recharge
                    </button>
                    <button type="button"
                        @click="switchTab('repair'); loadCustomerRepairs()"
                        :class="itemType === 'repair' ? 'bg-white text-orange-700 shadow-sm border-orange-200' : 'text-gray-600 hover:text-gray-800'"
                        class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all">
                        Repair
                    </button>
                </div>
                <a href="/admin/invoices" title="Invoices" class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 transition-all border border-blue-200 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </a>
            </div>

            {{-- Filter bar (products only) --}}
            <div x-show="itemType === 'product'" class="sales-filterbar mb-3 flex flex-wrap items-center gap-2 relative z-30">

                {{-- Category multi-select --}}
                <div class="relative" @click.away="catOpen = false">
                    <button type="button" @click="catOpen = !catOpen"
                        :class="selCategories.length ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center gap-1.5 text-sm min-h-[2.5rem] pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        <span x-text="selCategories.length === 0 ? 'Category' : (selCategories.length === 1 ? filterCategories.find(c=>c.id===selCategories[0])?.name || 'Category' : selCategories.length + ' Categories')"></span>
                        <span x-show="selCategories.length" class="ml-0.5 bg-primary-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1" x-text="selCategories.length"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="catOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl w-64 z-50">
                        <div class="p-2 border-b border-gray-100">
                            <input x-model="catSearch" type="text" placeholder="Search categories..."
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-300">
                        </div>
                        <div class="max-h-52 overflow-y-auto p-1">
                            <template x-for="cat in filterCategories.filter(c => !catSearch || c.name.toLowerCase().includes(catSearch.toLowerCase()))" :key="cat.id">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                                    <input type="checkbox" :checked="selCategories.includes(cat.id)"
                                        @change="toggleCategory(cat.id)"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span x-text="cat.name" class="text-gray-700 truncate"></span>
                                </label>
                            </template>
                            <div x-show="filterCategories.filter(c => !catSearch || c.name.toLowerCase().includes(catSearch.toLowerCase())).length === 0"
                                class="px-3 py-3 text-sm text-gray-400 text-center">No categories found</div>
                        </div>
                    </div>
                </div>

                {{-- Subcategory multi-select --}}
                <div class="relative" x-show="allFilterSubcategories.length > 0" @click.away="subOpen = false">
                    <button type="button" @click="subOpen = !subOpen"
                        :class="selSubcategories.length ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center gap-1.5 text-sm min-h-[2.5rem] pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <span x-text="selSubcategories.length === 0 ? 'Subcategory' : (selSubcategories.length === 1 ? allFilterSubcategories.find(s=>s.id===selSubcategories[0])?.name || 'Subcategory' : selSubcategories.length + ' Subcategories')"></span>
                        <span x-show="selSubcategories.length" class="ml-0.5 bg-primary-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1" x-text="selSubcategories.length"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="subOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl w-64 z-50">
                        <div class="p-2 border-b border-gray-100">
                            <input x-model="subSearch" type="text" placeholder="Search subcategories..."
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-300">
                        </div>
                        <div class="max-h-52 overflow-y-auto p-1">
                            <template x-for="sc in allFilterSubcategories.filter(s => !subSearch || s.name.toLowerCase().includes(subSearch.toLowerCase()))" :key="sc.id">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                                    <input type="checkbox" :checked="selSubcategories.includes(sc.id)"
                                        @change="toggleSubcategory(sc.id)"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span x-text="sc.name" class="text-gray-700 truncate"></span>
                                </label>
                            </template>
                            <div x-show="allFilterSubcategories.filter(s => !subSearch || s.name.toLowerCase().includes(subSearch.toLowerCase())).length === 0"
                                class="px-3 py-3 text-sm text-gray-400 text-center">No subcategories found</div>
                        </div>
                    </div>
                </div>

                {{-- Brand multi-select --}}
                <div class="relative" x-show="filterBrands.length > 0" @click.away="brandOpen = false">
                    <button type="button" @click="brandOpen = !brandOpen"
                        :class="selBrands.length ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center gap-1.5 text-sm min-h-[2.5rem] pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5a1.99 1.99 0 011.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span x-text="selBrands.length === 0 ? 'Brand' : (selBrands.length === 1 ? filterBrands.find(b=>b.id===selBrands[0])?.name || 'Brand' : selBrands.length + ' Brands')"></span>
                        <span x-show="selBrands.length" class="ml-0.5 bg-primary-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1" x-text="selBrands.length"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="brandOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl w-64 z-50">
                        <div class="p-2 border-b border-gray-100">
                            <input x-model="brandSearch" type="text" placeholder="Search brands..."
                                class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-300">
                        </div>
                        <div class="max-h-52 overflow-y-auto p-1">
                            <template x-for="b in filterBrands.filter(b => !brandSearch || b.name.toLowerCase().includes(brandSearch.toLowerCase()))" :key="b.id">
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer text-sm">
                                    <input type="checkbox" :checked="selBrands.includes(b.id)"
                                        @change="toggleBrand(b.id)"
                                        class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <span x-text="b.name" class="text-gray-700 truncate"></span>
                                </label>
                            </template>
                            <div x-show="filterBrands.filter(b => !brandSearch || b.name.toLowerCase().includes(brandSearch.toLowerCase())).length === 0"
                                class="px-3 py-3 text-sm text-gray-400 text-center">No brands found</div>
                        </div>
                    </div>
                </div>

                {{-- Clear all filters --}}
                <button x-show="selCategories.length || selSubcategories.length || selBrands.length"
                    @click="clearFilters()"
                    class="flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold px-3 min-h-[2.5rem] rounded-lg border border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>

            {{-- Product grid --}}
            <div x-show="itemType === 'product'"
                class="sales-item-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2.5 overflow-y-auto flex-1 min-h-0 pb-1 pr-1 content-start">
                <template x-for="p in searchResults" :key="p.id">
                    <button @click="addProduct(p)"
                        :class="searchQuery ? 'ring-2 ring-primary-400 border-primary-300 shadow-md' : ''"
                        class="sales-item-card group relative rounded-lg text-left hover:shadow-lg hover:border-primary-300 transition-all duration-200 overflow-hidden flex flex-col cursor-pointer">
                        <div class="relative w-full overflow-hidden bg-gray-50" style="height:80px">
                            <img x-show="p.thumbnail" :src="RepairBox.imageUrl(p.thumbnail)"
                                class="absolute inset-0 w-full h-full object-contain p-1 group-hover:scale-110 transition-transform duration-300">
                            <div x-show="!p.thumbnail" class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-gray-50 to-gray-100">
                                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span x-show="(p.inventory ? p.inventory.current_stock : 0) > 0"
                                class="absolute top-1 left-1 text-[8px] font-bold px-1.5 py-0.5 rounded leading-none bg-emerald-600 text-white"
                                x-text="'Stock: ' + (p.inventory ? p.inventory.current_stock : 0)"></span>
                            <span x-show="(p.inventory ? p.inventory.current_stock : 0) <= 0"
                                class="absolute top-1 left-1 text-[8px] font-bold px-1.5 py-0.5 rounded leading-none bg-red-600 text-white">Out</span>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-200 flex items-center justify-center">
                                <div class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-200">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="px-2 py-1.5 flex flex-col gap-0.5 border-t border-gray-50 w-full" style="flex:1; overflow:hidden;">
                            <p class="font-semibold text-[11px] text-gray-800 truncate leading-tight" x-text="p.name"></p>
                            <p class="text-[10px] text-gray-400 truncate leading-none" x-text="p.sku || 'No SKU'"></p>
                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                <span x-show="Number(p.mrp) > Number(p.selling_price)"
                                    class="text-gray-400 line-through text-[10px] leading-none"
                                    x-text="'MRP ₹' + Number(p.mrp).toLocaleString('en-IN')"></span>
                                <span class="text-primary-600 font-bold text-[13px] leading-none"
                                    x-text="'₹' + Number(p.selling_price).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                            </div>
                        </div>
                    </button>
                </template>
                <div x-show="searchResults.length === 0" class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="text-sm font-medium">No products found</p>
                    <p class="text-xs text-gray-300">Try a different search term</p>
                </div>
            </div>

            {{-- Service grid --}}
            <div x-show="itemType === 'service'"
                class="sales-item-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2.5 overflow-y-auto flex-1 min-h-0 pb-1 pr-1 content-start">
                <template x-for="s in filteredServices" :key="s.id">
                    <button @click="openServiceModal(s)"
                        :class="searchQuery ? 'ring-2 ring-indigo-300 border-indigo-300 shadow-md' : ''"
                        class="sales-item-card group relative rounded-lg text-left hover:shadow-lg hover:border-indigo-300 transition-all duration-200 overflow-hidden flex flex-col cursor-pointer">
                        <div class="relative w-full overflow-hidden bg-indigo-50" style="height:80px">
                            <img x-show="s.thumbnail" :src="RepairBox.imageUrl(s.thumbnail)"
                                class="absolute inset-0 w-full h-full object-contain p-1 group-hover:scale-110 transition-transform duration-300">
                            <div x-show="!s.thumbnail" class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-indigo-50 to-violet-100">
                                <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-200 flex items-center justify-center">
                                <div class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-200">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="px-2 py-1.5 flex flex-col gap-0.5 border-t border-gray-50 w-full" style="flex:1; overflow:hidden;">
                            <p class="font-semibold text-[11px] text-gray-800 truncate leading-tight" x-text="s.name"></p>
                            <p class="text-[10px] text-gray-400 truncate leading-none" x-text="s.description || 'Service'"></p>
                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                <span class="text-indigo-600 font-bold text-[13px] leading-none"
                                    x-text="'₹' + Number(s.default_price || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                            </div>
                        </div>
                    </button>
                </template>
                <div x-show="filteredServices.length === 0" class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <p class="text-sm font-medium">No services found</p>
                </div>
            </div>

            {{-- Manual item entry --}}
            <div x-show="itemType === 'manual'" class="card sales-panel p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Manual Item Entry</h4>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs font-medium text-gray-600">Item Name *</label>
                        <input x-model="manualItem.item_name" type="text" class="form-input-custom sales-field mt-1 text-sm" placeholder="e.g. Tempered Glass">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600">MRP</label>
                            <input x-model.number="manualItem.mrp" type="number" step="0.01" class="form-input-custom sales-field mt-1 text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Sell Price *</label>
                            <input x-model.number="manualItem.price" type="number" step="0.01" class="form-input-custom sales-field mt-1 text-sm" placeholder="0.00">
                        </div>
                    </div>
                    <button @click="addManualItem()" class="btn-primary w-full text-sm">Add to Cart</button>
                </div>
            </div>

            {{-- Recharge list (linked items) --}}
            <div x-show="itemType === 'recharge'" class="flex flex-col flex-1 min-h-0">
                {{-- Mobile number search --}}
                <div class="mb-2 relative">
                    <span class="pointer-events-none text-gray-400" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); z-index:2;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </span>
                    <input x-model="rechargeSearch" @input.debounce.300ms="loadCustomerRecharges()" type="text"
                        class="form-input-custom sales-field text-sm w-full" style="padding-left:2.25rem;"
                        placeholder="Filter by mobile number..." inputmode="numeric">
                </div>
                <div x-show="!selectedCustomer && !rechargeSearch" class="card sales-panel p-6 flex flex-col items-center justify-center text-gray-400 py-12 gap-3">
                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <p class="text-sm font-medium">Select a customer or search by mobile</p>
                    <p class="text-xs text-gray-300">Type a phone number above to find recharges</p>
                </div>
                <div x-show="selectedCustomer || rechargeSearch" class="overflow-y-auto flex-1 min-h-0 space-y-2 pb-1 pr-1">
                    <template x-for="r in linkedRecharges" :key="r.id">
                        <button @click="addLinkedRecharge(r)"
                            :class="cart.find(c => c.item_type === 'recharge' && c.linked_id === r.id) ? 'border-teal-400 bg-teal-50 opacity-60 pointer-events-none' : 'hover:border-teal-300 hover:shadow-md'"
                            class="w-full text-left rounded-lg border border-gray-200 p-3 transition-all cursor-pointer">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="r.label"></p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5" x-text="r.description"></p>
                                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="r.date"></p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-teal-600 font-bold text-sm" x-text="'₹' + Number(r.amount).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                                    <span x-show="cart.find(c => c.item_type === 'recharge' && c.linked_id === r.id)" class="block text-[10px] text-teal-500 font-medium mt-0.5">Added</span>
                                </div>
                            </div>
                        </button>
                    </template>
                    <div x-show="linkedRecharges.length === 0 && !linkedLoading" class="flex flex-col items-center justify-center text-gray-400 py-16 gap-3">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm font-medium">No recharges found</p>
                        <p class="text-xs text-gray-300">This customer has no completed recharges</p>
                    </div>
                    <div x-show="linkedLoading" class="flex items-center justify-center py-16 text-gray-400">
                        <svg class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        Loading…
                    </div>
                </div>
            </div>

            {{-- Repair list (linked items) --}}
            <div x-show="itemType === 'repair'" class="flex flex-col flex-1 min-h-0">
                {{-- Repair search --}}
                <div class="mb-2 relative">
                    <span class="pointer-events-none text-gray-400" style="position:absolute; left:0.75rem; top:50%; transform:translateY(-50%); z-index:2;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input x-model="repairSearch" @input.debounce.300ms="loadCustomerRepairs()" type="text"
                        class="form-input-custom sales-field text-sm w-full" style="padding-left:2.25rem;"
                        placeholder="Search by ticket, device, IMEI...">
                </div>
                <div x-show="!selectedCustomer && !repairSearch" class="card sales-panel p-6 flex flex-col items-center justify-center text-gray-400 py-12 gap-3">
                    <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-sm font-medium">Select a customer or search</p>
                    <p class="text-xs text-gray-300">Search by ticket number, device or IMEI</p>
                </div>
                <div x-show="selectedCustomer || repairSearch" class="overflow-y-auto flex-1 min-h-0 space-y-2 pb-1 pr-1">
                    <template x-for="r in linkedRepairs" :key="r.id">
                        <button @click="addLinkedRepair(r)"
                            :class="cart.find(c => c.item_type === 'repair' && c.linked_id === r.id) ? 'border-orange-400 bg-orange-50 opacity-60 pointer-events-none' : 'hover:border-orange-300 hover:shadow-md'"
                            class="w-full text-left rounded-lg border border-gray-200 p-3 transition-all cursor-pointer">
                            <div class="flex items-center justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="r.label"></p>
                                    <p class="text-xs text-gray-500 truncate mt-0.5" x-text="r.description"></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[10px] text-gray-400" x-text="r.date"></span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded-full font-medium"
                                            :class="r.status === 'closed' ? 'bg-green-100 text-green-700' : (r.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700')"
                                            x-text="r.status"></span>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-orange-600 font-bold text-sm" x-text="'₹' + Number(r.amount).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                                    <span x-show="cart.find(c => c.item_type === 'repair' && c.linked_id === r.id)" class="block text-[10px] text-orange-500 font-medium mt-0.5">Added</span>
                                </div>
                            </div>
                        </button>
                    </template>
                    <div x-show="linkedRepairs.length === 0 && !linkedLoading" class="flex flex-col items-center justify-center text-gray-400 py-16 gap-3">
                        <svg class="w-12 h-12 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <p class="text-sm font-medium">No repairs found</p>
                        <p class="text-xs text-gray-300">This customer has no completed repairs</p>
                    </div>
                    <div x-show="linkedLoading" class="flex items-center justify-center py-16 text-gray-400">
                        <svg class="animate-spin w-6 h-6 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        Loading…
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart & Customer --}}
        <div class="relative flex flex-col min-h-0 gap-3 order-first lg:order-none overflow-hidden" :style="custOpen ? 'z-index:95;' : 'z-index:10;'">

            {{-- Customer selector --}}
            <div class="card sales-panel relative shrink-0" :style="custOpen ? 'overflow:visible; z-index:110;' : 'overflow:visible; z-index:10;'">
                <div class="card-body py-3" style="overflow:visible">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Customer</label>
                            <p class="mt-1 text-xs text-gray-400" x-show="!selectedCustomer">Search and attach the customer before billing.</p>
                            <p class="mt-1 text-xs text-emerald-600" x-show="selectedCustomer">Customer selected for this invoice.</p>
                        </div>
                        <button type="button" @click="openAddCustomerModal()"
                            class="btn-primary text-sm px-3 py-2 whitespace-nowrap w-auto">+ New</button>
                    </div>

                    <div x-show="!selectedCustomer" x-cloak class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                        <div class="flex-1 relative" @click.away="custOpen = false">
                            <input x-model="customerSearch" @focus="findCustomers(1)" @input.debounce.300ms="findCustomers(1)" type="text"
                                class="form-input-custom sales-field text-sm" placeholder="Search by name / phone...">
                            <div x-show="custOpen && customerResults.length > 0" x-cloak class="absolute left-0 right-0 mt-1 overflow-hidden rounded-lg border bg-white shadow-lg" style="z-index:160;">
                                <div class="max-h-48 overflow-y-auto" @scroll="handleCustScroll($event)">
                                    <template x-for="c in customerResults" :key="c.id">
                                        <button @click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b last:border-0">
                                            <div class="font-medium text-gray-800" x-text="c.name"></div>
                                            <div class="text-xs text-gray-400" x-text="c.mobile_number || 'No mobile number'"></div>
                                        </button>
                                    </template>
                                    <div x-show="custLoading" class="px-3 py-2 text-xs text-gray-400 text-center flex items-center justify-center gap-2"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Loading…</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="custOpen && !custLoading && customerResults.length === 0 && !selectedCustomer"
                        class="text-xs text-gray-400 mt-2">No customers found - click <strong>+ New</strong> to add.</div>

                    <div x-show="selectedCustomer" x-cloak class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-emerald-900 truncate" x-text="selectedCustomer?.name"></div>
                                <div class="mt-1 text-xs text-emerald-700" x-text="selectedCustomer?.mobile_number || 'No mobile number'"></div>
                                <div class="mt-1 text-xs text-emerald-600 break-all" x-show="selectedCustomer?.email" x-text="selectedCustomer?.email"></div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="clearCustomer(); $nextTick(() => findCustomers(1))" class="inline-flex items-center rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Change</button>
                                <button type="button" @click="clearCustomer()" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="card sales-panel relative flex-1 flex flex-col min-h-0" style="z-index:0;">
                <div class="card-header py-2 flex items-center justify-between shrink-0">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Cart (<span x-text="cart.length"></span> item<span x-show="cart.length !== 1">s</span>)
                    </h3>
                    <button x-show="cart.length > 0" @click="cart = []"
                        class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="sales-cart-row border-b last:border-0 transition-colors"
                            :class="item.is_linked ? 'bg-amber-50/60 hover:bg-amber-50' : 'hover:bg-gray-50/50'">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-sm font-medium truncate leading-tight cursor-default"
                                            :class="item.is_linked ? 'text-gray-700' : 'text-gray-900'"
                                            x-text="item.item_name"
                                            @if($canViewCostPrice) @click="!item.is_linked && (item._showDisc = !item._showDisc)" @endif></p>
                                        <span x-show="item.is_linked"
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wide shrink-0"
                                            :class="item.item_type === 'recharge' ? 'bg-teal-100 text-teal-700' : 'bg-orange-100 text-orange-700'"
                                            x-text="item.item_type === 'recharge' ? 'Linked' : 'Linked'"></span>
                                    </div>
                                    {{-- Service work description --}}
                                    <p x-show="item.item_type === 'service' && item.notes"
                                        class="text-[10px] text-indigo-500 truncate leading-tight mt-0.5"
                                        x-text="item.notes + (item.item_unit && item.item_unit !== 'pcs' ? ' · ' + item.item_unit : '')"></p>
                                    {{-- Linked item note --}}
                                    <p x-show="item.is_linked"
                                        class="text-[10px] text-amber-600 mt-0.5">Not included in total</p>
                                    {{-- MRP reference --}}
                                    <div x-show="!item.is_linked && item.mrp && item.mrp > item.price" class="flex items-center gap-1 mt-0.5">
                                        <span class="text-[10px] text-gray-400">MRP:</span>
                                        <span class="text-[10px] text-gray-400" x-text="'₹' + Number(item.mrp).toFixed(2)"></span>
                                    </div>
                                    {{-- Max discount (privileged only, revealed by tapping item name) --}}
                                    @if($canViewCostPrice)
                                    <div x-show="item._showDisc && item.cost_price > 0" x-transition.opacity.duration.150ms class="mt-0.5">
                                        <span class="text-[10px] text-gray-500">↓ ₹<span x-text="Math.max(0, item.price - item.cost_price).toFixed(2)"></span></span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Qty controls (not for linked items) --}}
                                <div x-show="!item.is_linked" class="flex items-center gap-1 shrink-0">
                                    <button @click="item.quantity > 1 ? item.quantity-- : null"
                                        class="w-5 h-5 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 text-xs">-</button>
                                    <span class="text-sm w-7 text-center font-medium" x-text="item.quantity"></span>
                                    <button @click="item.quantity++"
                                        class="w-5 h-5 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 text-xs">+</button>
                                </div>

                                {{-- Editable price + line total (read-only for linked) --}}
                                <div class="flex flex-col items-end shrink-0 gap-0.5">
                                    <template x-if="!item.is_linked">
                                        <div class="flex items-center gap-1">
                                            <span class="text-xs text-gray-400">₹</span>
                                            <input x-model.number="item.price" type="number" step="0.01" min="0"
                                                class="w-20 text-right text-sm border border-gray-300 rounded px-1.5 py-0.5 focus:border-primary-400 focus:outline-none"
                                                @change="if(item.price < 0) item.price = 0">
                                        </div>
                                    </template>
                                    <span class="text-xs font-semibold"
                                        :class="item.is_linked ? 'text-amber-600' : 'text-gray-700'"
                                        x-text="'₹' + (item.price * item.quantity).toFixed(2)"></span>
                                </div>

                                <button @click="cart.splice(idx, 1)" class="text-red-400 hover:text-red-600 shrink-0 ml-1 text-lg leading-none">&times;</button>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center text-gray-400 py-10 text-sm">
                        <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        Cart is empty - add products above
                    </div>
                </div>

                {{-- Summary --}}
                <div class="sales-summary border-t px-4 py-3 space-y-1.5 text-sm bg-gray-50/50 shrink-0">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="'₹' + subtotal().toFixed(2)"></span>
                    </div>
                    <div x-show="linkedTotal() > 0" class="flex justify-between text-amber-600 text-xs">
                        <span>Linked (Recharge/Repair)</span>
                        <span x-text="'₹' + linkedTotal().toFixed(2) + ' (ref only)'"></span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Discount (₹)</span>
                        <input x-model.number="form.discount" type="number" step="0.01" min="0"
                            class="w-24 text-right text-sm border border-gray-300 rounded px-2 py-1 focus:border-primary-400 focus:outline-none">
                    </div>
                    <div class="flex justify-between font-bold text-base pt-1.5 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-primary-600 text-lg" x-text="'₹' + grandTotal().toFixed(2)"></span>
                    </div>
                </div>

                {{-- Create Invoice button --}}
                <div class="sales-actionbar border-t px-4 py-3 shrink-0">
                    <button @click="createInvoiceDraft()"
                        class="btn-primary w-full py-3 text-base font-semibold"
                        :disabled="saving || cart.length === 0 || (grandTotal() <= 0 && linkedTotal() <= 0)">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div x-show="showPaymentModal" x-cloak class="modal-overlay" @keydown.escape.window="skipPayment()">
        <div class="modal-container max-w-lg" @click.stop>
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Record Payment</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Invoice <span class="font-semibold text-primary-600" x-text="'#' + (createdInvoice ? createdInvoice.invoice_number : '')"></span>
                        - collect payment now
                    </p>
                </div>
                <button @click="skipPayment()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="modal-body space-y-4">
                <div class="bg-primary-50 border border-primary-100 rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500">Invoice Total</span>
                        <p class="text-xl font-bold text-primary-700" x-text="'₹' + Number(createdInvoice ? createdInvoice.final_amount : 0).toFixed(2)"></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Balance Due</span>
                        <p class="text-xl font-bold text-red-600" x-text="'₹' + balanceDue().toFixed(2)"></p>
                    </div>
                </div>

                <template x-for="(pay, pidx) in payForm.payments" :key="pidx">
                    <div class="space-y-2 p-3 rounded-lg border border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-600"
                                x-text="payForm.payments.length > 1 ? 'Payment ' + (pidx + 1) : 'Payment Method'"></span>
                            <button x-show="payForm.payments.length > 1" @click="payForm.payments.splice(pidx, 1)"
                                class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Method</label>
                                <select x-model="pay.payment_method" class="form-select-custom text-sm w-full">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card / Swipe</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Amount (₹)</label>
                                <input x-model.number="pay.amount" type="number" step="0.01" min="0"
                                    class="form-input-custom text-sm w-full" placeholder="0.00">
                            </div>
                        </div>

                        {{-- Reference field: UPI / bank / cheque --}}
                        <div x-show="pay.payment_method === 'upi' || pay.payment_method === 'bank_transfer' || pay.payment_method === 'cheque'">
                            <label class="text-xs text-gray-500 mb-1 block"
                                x-text="pay.payment_method === 'cheque' ? 'Cheque No.' : (pay.payment_method === 'upi' ? 'UPI / IMPS Ref No. *' : 'NEFT / RTGS Ref No.')"></label>
                            <input x-model="pay.transaction_reference" type="text"
                                class="form-input-custom text-sm w-full"
                                :placeholder="pay.payment_method === 'upi' ? 'Enter UPI transaction reference' : (pay.payment_method === 'cheque' ? 'Enter cheque number' : 'Enter transaction reference')"
                                :class="pay.payment_method === 'upi' && !pay.transaction_reference ? 'border-amber-400' : ''">
                            <p x-show="pay.payment_method === 'upi' && !pay.transaction_reference"
                                class="text-[10px] text-amber-600 mt-0.5">Required for UPI payments</p>
                        </div>
                    </div>
                </template>

                <button @click="payForm.payments.push({payment_method:'cash', amount:0, transaction_reference:''})"
                    class="text-xs text-primary-600 hover:text-primary-800 hover:underline flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Split Payment (add another method)
                </button>

                <div class="flex items-center justify-between text-sm pt-1 border-t">
                    <span class="text-gray-500">Total Paying</span>
                    <span class="font-semibold" :class="totalPaying() >= Number(createdInvoice ? createdInvoice.final_amount : 0) ? 'text-green-600' : 'text-amber-600'"
                        x-text="'₹' + totalPaying().toFixed(2)"></span>
                </div>
                <div x-show="totalPaying() > 0 && totalPaying() < Number(createdInvoice ? createdInvoice.final_amount : 0)"
                    class="text-xs text-amber-700 bg-amber-50 rounded px-3 py-2 border border-amber-200">
                    Partial payment. Balance of ₹<span x-text="balanceDue().toFixed(2)"></span> will remain outstanding.
                </div>
                <div x-show="totalPaying() > Number(createdInvoice ? createdInvoice.final_amount : 0)"
                    class="flex justify-between text-sm text-green-700 bg-green-50 rounded px-3 py-2 border border-green-200">
                    <span>Change to return to customer</span>
                    <span class="font-semibold" x-text="'₹' + (totalPaying() - Number(createdInvoice ? createdInvoice.final_amount : 0)).toFixed(2)"></span>
                </div>
            </div>

            <div class="modal-footer gap-2">
                <button type="button" @click="skipPayment()" class="btn-secondary text-sm">Pay Later</button>
                <button type="button" @click="recordPayment()" class="btn-primary text-sm px-6"
                    :disabled="paying || totalPaying() <= 0">
                    <span x-show="paying" class="spinner mr-2"></span>
                    Pay &amp; Complete
                </button>
            </div>
        </div>
    </div>

    {{-- SERVICE ENTRY MODAL --}}
    <div x-show="svcModal.open" x-cloak class="modal-overlay" @keydown.escape.window="svcModal.open = false">
        <div class="modal-container max-w-lg" @click.stop>
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900" x-text="svcModal.service?.name"></h3>
                        <p class="text-xs text-gray-400" x-text="svcModal.service?.description || 'Enter service details below'"></p>
                    </div>
                </div>
                <button @click="svcModal.open = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-4">

                {{-- Smart quick-fill suggestions --}}
                <div x-show="svcModalSuggestions.length > 0">
                    <p class="text-xs font-medium text-gray-500 mb-1.5">Quick fill</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="(tag, i) in svcModalSuggestions" :key="i">
                            <button type="button"
                                @click="svcModal.desc = tag[0]; if(tag[1]) svcModal.unit = tag[1]"
                                :class="svcModal.desc === tag[0] ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                                x-text="tag[0]"></button>
                        </template>
                    </div>
                </div>

                {{-- What was done --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Work Description <span class="text-gray-400 font-normal">(what was done)</span></label>
                    <textarea x-model="svcModal.desc" rows="2" class="form-input-custom text-sm resize-none"
                        placeholder="e.g. A4 B&amp;W 20 pages, iPhone 13 OEM screen, Grade-A battery..."></textarea>
                </div>

                {{-- Qty / Unit / Price --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                        <input x-model.number="svcModal.qty" type="number" min="1" step="1"
                            class="form-input-custom text-sm text-center" placeholder="1">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                        <select x-model="svcModal.unit" class="form-select-custom text-sm">
                            <option value="pcs">pcs</option>
                            <option value="pages">pages</option>
                            <option value="sheets">sheets</option>
                            <option value="hours">hours</option>
                            <option value="minutes">minutes</option>
                            <option value="sets">sets</option>
                            <option value="items">items</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price / unit (₹)</label>
                        <input x-model.number="svcModal.price" type="number" min="0" step="0.01"
                            class="form-input-custom text-sm text-right" placeholder="0.00">
                    </div>
                </div>

                {{-- Live total --}}
                <div class="bg-indigo-50 rounded-lg px-4 py-3 flex items-center justify-between">
                    <span class="text-sm text-indigo-700 font-medium">Total</span>
                    <span class="text-xl font-bold text-indigo-700"
                        x-text="'₹' + ((svcModal.qty || 0) * (svcModal.price || 0)).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="svcModal.open = false" class="btn-secondary">Cancel</button>
                <button type="button" @click="confirmAddService()" class="btn-primary">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add to Cart
                </button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccessModal" x-cloak class="modal-overlay">
        <div class="modal-container max-w-sm text-center" @click.stop>
            <div class="modal-body py-8 flex flex-col items-center gap-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                    :class="createdInvoice && createdInvoice.payment_status === 'paid' ? 'bg-green-100' : 'bg-amber-100'">
                    <svg x-show="createdInvoice && createdInvoice.payment_status === 'paid'" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="!createdInvoice || createdInvoice.payment_status !== 'paid'" class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800"
                        x-text="createdInvoice && createdInvoice.payment_status === 'paid' ? 'Payment Complete!' : 'Invoice Saved'"></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Invoice <span class="font-semibold text-primary-600" x-text="'#' + (createdInvoice ? createdInvoice.invoice_number : '')"></span>
                    </p>
                    <p x-show="createdInvoice && createdInvoice.payment_status === 'unpaid'" class="text-xs text-amber-600 mt-1">
                        Outstanding: ₹<span x-text="Number(createdInvoice ? createdInvoice.final_amount : 0).toFixed(2)"></span>
                    </p>
                    <p x-show="createdInvoice && createdInvoice.payment_status === 'partial'" class="text-xs text-amber-600 mt-1">
                        Partial payment recorded - balance pending
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap justify-center">
                    <a :href="'/admin/invoices/' + (createdInvoice ? createdInvoice.id : '') + '/print'" target="_blank"
                        class="btn-secondary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Invoice
                    </a>
                    <button @click="newSale()" class="btn-primary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD CUSTOMER MODAL --}}
    <div x-show="showAddCustomer" x-cloak class="modal-overlay">
        <div class="modal-container max-w-md" @click.stop>
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Add New Customer</h3>
                <button @click="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-3">
                <div x-show="customerSubmitError" x-text="customerSubmitError" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="newCustomer.name" type="text" class="form-input-custom" placeholder="Full name" required>
                    <p x-show="customerFormTried && !newCustomer.name.trim()" class="text-xs text-red-500 mt-1">Name is required</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile * <span class="text-xs text-gray-500">(10 digits)</span></label>
                    <input x-model="newCustomer.mobile_number" type="text" class="form-input-custom" placeholder="10-digit mobile number"
                        inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required
                        @input="newCustomer.mobile_number = RepairBox.normalizeCustomerMobile(newCustomer.mobile_number)"
                        @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()">
                    <p x-show="customerFormTried && !newCustomer.mobile_number.trim()" class="text-xs text-red-500 mt-1">Mobile number is required</p>
                    <p x-show="(customerFormTried || newCustomer.mobile_number) && newCustomer.mobile_number.trim() && !/^\d{10}$/.test(newCustomer.mobile_number.trim())" class="text-xs text-red-500 mt-1">Mobile must be exactly 10 digits</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input x-model="newCustomer.email" type="email" class="form-input-custom" placeholder="Optional">
                    <p x-show="(customerFormTried || newCustomer.email) && newCustomer.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCustomer.email.trim())" class="text-xs text-red-500 mt-1">Please enter a valid email</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input x-model="newCustomer.address" type="text" class="form-input-custom" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="closeAddCustomerModal()" class="btn-secondary">Cancel</button>
                <button type="button" @click.prevent="saveNewCustomer()" class="btn-primary" :disabled="customerSaving">
                    <span x-show="!customerSaving">Save &amp; Select</span>
                    <span x-show="customerSaving">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posBilling() {
    return {
        searchQuery: '',
        searchResults: [],
        allServices: [],
        cart: [],
        itemType: new URLSearchParams(window.location.search).get('type') || 'product',
        saving: false,
        paying: false,

        // Filters
        filterCategories: [],
        filterBrands: [],
        selCategories: [],
        selSubcategories: [],
        selBrands: [],
        catOpen: false, subOpen: false, brandOpen: false,
        catSearch: '', subSearch: '', brandSearch: '',

        customerSearch: '',
        customerResults: [],
        custOpen: false,
        custHasMore: false,
        custPage: 1,
        custLoading: false,
        selectedCustomer: null,
        showAddCustomer: false,
        customerFormTried: false,
        customerSaving: false,
        customerSubmitError: '',
        newCustomer: { name: '', mobile_number: '', email: '', address: '' },

        manualItem: { item_name: '', price: 0, mrp: 0 },

        svcModal: { open: false, service: null, desc: '', qty: 1, unit: 'pcs', price: 0 },

        // Linked items (recharge & repair)
        linkedRecharges: [],
        linkedRepairs: [],
        linkedLoading: false,
        rechargeSearch: '',
        repairSearch: '',

        form: { customer_id: null, discount: 0 },

        createdInvoice: null,
        showPaymentModal: false,
        showSuccessModal: false,
        payForm: { payments: [{ payment_method: 'cash', amount: 0, transaction_reference: '' }] },

        canViewCostPrice: {{ $canViewCostPrice ? 'true' : 'false' }},

        async init() {
            const validTypes = ['product', 'service', 'manual', 'recharge', 'repair'];
            if (!validTypes.includes(this.itemType)) this.itemType = 'product';
            window.addEventListener('popstate', () => {
                const type = new URLSearchParams(window.location.search).get('type') || 'product';
                this.itemType = validTypes.includes(type) ? type : 'product';
            });
            await Promise.all([this.searchProducts(), this.loadServices(), this.loadFilterData()]);
        },

        switchTab(type) {
            this.itemType = type;
            const url = new URL(window.location);
            url.searchParams.set('type', type);
            history.pushState(null, '', url);
        },

        get allFilterSubcategories() {
            if (this.selCategories.length === 0) return [];
            const subs = [];
            this.filterCategories.forEach(cat => {
                if (this.selCategories.includes(cat.id) && cat.subcategories) {
                    subs.push(...cat.subcategories);
                }
            });
            return subs;
        },

        async loadFilterData() {
            const r = await RepairBox.ajax('/admin/products-filter-data');
            const d = r.data || r;
            if (d.categories) this.filterCategories = d.categories;
            if (d.brands)     this.filterBrands     = d.brands;
        },

        async reloadBrands() {
            const params = new URLSearchParams();
            if (this.selCategories.length)    params.set('category_id',    this.selCategories.join(','));
            if (this.selSubcategories.length) params.set('subcategory_id', this.selSubcategories.join(','));
            const r = await RepairBox.ajax('/admin/products-filter-data?' + params.toString());
            const d = r.data || r;
            if (d.brands) this.filterBrands = d.brands;
            // Remove any selected brands no longer in the list
            const validIds = this.filterBrands.map(b => b.id);
            this.selBrands = this.selBrands.filter(id => validIds.includes(id));
        },

        async toggleCategory(id) {
            const idx = this.selCategories.indexOf(id);
            if (idx === -1) this.selCategories.push(id);
            else this.selCategories.splice(idx, 1);
            // Remove subcategory selections that no longer belong to selected categories
            const validSubIds = this.allFilterSubcategories.map(s => s.id);
            this.selSubcategories = this.selSubcategories.filter(sid => validSubIds.includes(sid));
            await this.reloadBrands();
            this.searchProducts();
        },

        async toggleSubcategory(id) {
            const idx = this.selSubcategories.indexOf(id);
            if (idx === -1) this.selSubcategories.push(id);
            else this.selSubcategories.splice(idx, 1);
            await this.reloadBrands();
            this.searchProducts();
        },

        toggleBrand(id) {
            const idx = this.selBrands.indexOf(id);
            if (idx === -1) this.selBrands.push(id);
            else this.selBrands.splice(idx, 1);
            this.searchProducts();
        },

        clearFilters() {
            this.selCategories = [];
            this.selSubcategories = [];
            this.selBrands = [];
            this.catSearch = '';
            this.subSearch = '';
            this.brandSearch = '';
            this.reloadBrands();
            this.searchProducts();
        },

        async searchProducts() {
            const params = new URLSearchParams();
            params.set('q', this.searchQuery);
            if (this.selCategories.length)    params.set('category_id',    this.selCategories.join(','));
            if (this.selSubcategories.length) params.set('subcategory_id', this.selSubcategories.join(','));
            if (this.selBrands.length)        params.set('brand_id',       this.selBrands.join(','));
            const r = await RepairBox.ajax('/admin/products-search?' + params.toString());
            if (r.data) this.searchResults = r.data;
        },

        async loadServices() {
            const r = await RepairBox.ajax('/admin/service-types');
            if (Array.isArray(r.data)) this.allServices = r.data.filter(s => s.status === 'active');
        },

        get filteredServices() {
            if (!this.searchQuery) return this.allServices;
            const q = this.searchQuery.toLowerCase();
            return this.allServices.filter(s =>
                s.name.toLowerCase().includes(q) ||
                (s.description && s.description.toLowerCase().includes(q))
            );
        },

        addProduct(p) {
            const existing = this.cart.find(c => c.product_id === p.id && c.item_type === 'product');
            if (existing) { existing.quantity++; return; }
            this.cart.push({
                item_type: 'product',
                product_id: p.id,
                service_id: null,
                item_name: p.name,
                quantity: 1,
                price: Number(p.selling_price),
                mrp: Number(p.mrp || 0),
                cost_price: Number(p.purchase_price || 0),
                max_selling_price: Number(p.max_selling_price || 0),
                _showDisc: false,
            });
        },

        openServiceModal(s) {
            this.svcModal = { open: true, service: s, desc: '', qty: 1, unit: 'pcs', price: Number(s.default_price || 0) };
        },

        get svcModalSuggestions() {
            const s = this.svcModal.service;
            if (s && Array.isArray(s.quick_fills) && s.quick_fills.length > 0) {
                return s.quick_fills.map(qf => [qf, null]);
            }
            return [];
        },

        confirmAddService() {
            const m = this.svcModal;
            if (!m.qty || m.qty <= 0) { RepairBox.toast('Quantity must be at least 1', 'error'); return; }
            if (!m.price || m.price < 0) { RepairBox.toast('Enter a valid price', 'error'); return; }
            this.cart.push({
                item_type: 'service',
                product_id: null,
                service_id: m.service.id,
                item_name: m.service.name,
                notes: m.desc.trim(),
                item_unit: m.unit,
                quantity: m.qty,
                price: m.price,
                mrp: m.price,
                cost_price: 0,
                max_selling_price: 0,
                _showDisc: false,
            });
            this.svcModal.open = false;
        },

        addManualItem() {
            if (!this.manualItem.item_name || !this.manualItem.price) {
                RepairBox.toast('Item name and price are required', 'error');
                return;
            }
            this.cart.push({
                item_type: 'manual',
                product_id: null,
                service_id: null,
                item_name: this.manualItem.item_name,
                quantity: 1,
                price: Number(this.manualItem.price),
                mrp: Number(this.manualItem.mrp || this.manualItem.price),
                cost_price: 0,
                max_selling_price: 0,
                _showDisc: false,
            });
            this.manualItem = { item_name: '', price: 0, mrp: 0 };
        },

        // ── Linked items (Recharge / Repair) ──
        async loadCustomerRecharges() {
            if (!this.selectedCustomer && !this.rechargeSearch) { this.linkedRecharges = []; return; }
            this.linkedLoading = true;
            let url = '/admin/customer-recharges?';
            if (this.selectedCustomer) url += 'customer_id=' + this.selectedCustomer.id + '&';
            if (this.rechargeSearch) url += 'mobile=' + encodeURIComponent(this.rechargeSearch);
            const r = await RepairBox.ajax(url);
            this.linkedRecharges = Array.isArray(r.data) ? r.data : [];
            this.linkedLoading = false;
        },

        async loadCustomerRepairs() {
            if (!this.selectedCustomer && !this.repairSearch) { this.linkedRepairs = []; return; }
            this.linkedLoading = true;
            let url = '/admin/customer-repairs?';
            if (this.selectedCustomer) url += 'customer_id=' + this.selectedCustomer.id + '&';
            if (this.repairSearch) url += 'search=' + encodeURIComponent(this.repairSearch);
            const r = await RepairBox.ajax(url);
            this.linkedRepairs = Array.isArray(r.data) ? r.data : [];
            this.linkedLoading = false;
        },

        addLinkedRecharge(r) {
            if (this.cart.find(c => c.item_type === 'recharge' && c.linked_id === r.id)) return;
            this.cart.push({
                item_type: 'recharge',
                product_id: null,
                service_id: null,
                item_name: r.label,
                quantity: 1,
                price: r.amount,
                mrp: r.amount,
                cost_price: 0,
                max_selling_price: 0,
                is_linked: true,
                linked_id: r.id,
                _showDisc: false,
            });
        },

        addLinkedRepair(r) {
            if (this.cart.find(c => c.item_type === 'repair' && c.linked_id === r.id)) return;
            this.cart.push({
                item_type: 'repair',
                product_id: null,
                service_id: null,
                item_name: r.label,
                quantity: 1,
                price: r.amount,
                mrp: r.amount,
                cost_price: 0,
                max_selling_price: 0,
                is_linked: true,
                linked_id: r.id,
                _showDisc: false,
            });
        },

        async findCustomers(page) {
            page = page || 1;
            if (page === 1) this.custPage = 1;
            this.custLoading = true;
            const r = await RepairBox.ajax('/admin/customers-search?page=' + page + '&q=' + encodeURIComponent(this.customerSearch || ''));
            this.custLoading = false;
            const rows = Array.isArray(r.data) ? r.data : [];
            this.customerResults = page === 1 ? rows : this.customerResults.concat(rows);
            this.custHasMore = r.has_more || false;
            this.custPage = page;
            if (this.customerResults.length > 0 || this.customerSearch) this.custOpen = true;
        },

        handleCustScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.custHasMore && !this.custLoading) {
                this.findCustomers(this.custPage + 1);
            }
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.form.customer_id = c.id;
            this.customerResults = [];
            this.custOpen = false;
            this.customerSearch = '';
            // Refresh linked data for the new customer
            if (this.itemType === 'recharge') this.loadCustomerRecharges();
            else if (this.itemType === 'repair') this.loadCustomerRepairs();
        },
        clearCustomer() {
            this.selectedCustomer = null;
            this.form.customer_id = null;
            this.customerSearch = '';
            this.customerResults = [];
            this.custOpen = false;
            this.linkedRecharges = [];
            this.linkedRepairs = [];
            this.rechargeSearch = '';
            this.repairSearch = '';
        },
        openAddCustomerModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.newCustomer = RepairBox.emptyCustomer();
            this.showAddCustomer = true;
        },
        closeAddCustomerModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.showAddCustomer = false;
        },

        async saveNewCustomer() {
            this.customerFormTried = true;
            this.customerSubmitError = '';

            const validation = RepairBox.validateCustomerPayload(this.newCustomer);
            this.newCustomer = {
                ...this.newCustomer,
                ...validation.payload,
                email: validation.payload.email || '',
                address: validation.payload.address || '',
            };

            if (!validation.valid) {
                return;
            }

            this.customerSaving = true;
            const r = await RepairBox.ajax('/admin/customers', 'POST', validation.payload);
            this.customerSaving = false;

            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.closeAddCustomerModal();
                this.newCustomer = RepairBox.emptyCustomer();
                RepairBox.toast('Customer added', 'success');
                return;
            }

            this.customerSubmitError = r.message || 'Unable to save customer. Please check the details and try again.';
        },

        subtotal() {
            return this.cart.filter(i => !i.is_linked).reduce((s, i) => s + Number(i.price) * i.quantity, 0);
        },

        linkedTotal() {
            return this.cart.filter(i => i.is_linked).reduce((s, i) => s + Number(i.price) * i.quantity, 0);
        },

        grandTotal() {
            return Math.max(0, this.subtotal() + this.linkedTotal() - (Number(this.form.discount) || 0));
        },

        totalPaying() {
            return this.payForm.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0);
        },

        balanceDue() {
            const invoiceTotal = Number(this.createdInvoice ? this.createdInvoice.final_amount : 0);
            return Math.max(0, invoiceTotal - this.totalPaying());
        },

        async createInvoiceDraft() {
            if (this.cart.length === 0) { RepairBox.toast('Cart is empty', 'error'); return; }
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }

            this.saving = true;
            const payload = {
                customer_id: this.form.customer_id,
                discount: this.form.discount || 0,
                items: this.cart.map(item => ({
                    item_type: item.item_type,
                    product_id: item.product_id || null,
                    service_id: item.service_id || null,
                    item_name: item.notes ? item.item_name + ' — ' + item.notes : item.item_name,
                    quantity: item.quantity,
                    price: item.price,
                    mrp: item.mrp || item.price,
                    is_linked: item.is_linked || false,
                    linked_id: item.linked_id || null,
                })),
            };

            const r = await RepairBox.ajax('/admin/invoices', 'POST', payload);
            this.saving = false;

            if (r.success !== false && r.data) {
                this.createdInvoice = r.data;
                this.payForm.payments = [{
                    payment_method: 'cash',
                    amount: Number(r.data.final_amount),
                    transaction_reference: '',
                }];
                this.showPaymentModal = true;
            }
        },

        async recordPayment() {
            if (!this.createdInvoice) return;
            if (this.totalPaying() <= 0) { RepairBox.toast('Enter payment amount', 'error'); return; }

            for (const pay of this.payForm.payments) {
                if (pay.payment_method === 'upi' && !String(pay.transaction_reference || '').trim()) {
                    RepairBox.toast('UPI reference number is required', 'error');
                    return;
                }
            }

            this.paying = true;
            const r = await RepairBox.ajax(
                '/admin/invoices/' + this.createdInvoice.id + '/pay',
                'POST',
                { payments: this.payForm.payments }
            );
            this.paying = false;

            if (r.success !== false && r.data) {
                this.createdInvoice = r.data;
                this.showPaymentModal = false;
                this.showSuccessModal = true;
            }
        },

        skipPayment() {
            this.showPaymentModal = false;
            this.showSuccessModal = true;
        },

        newSale() {
            this.cart = [];
            this.form = { customer_id: null, discount: 0 };
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.linkedRecharges = [];
            this.linkedRepairs = [];
            this.rechargeSearch = '';
            this.repairSearch = '';
            this.createdInvoice = null;
            this.showSuccessModal = false;
            this.showPaymentModal = false;
            this.payForm = { payments: [{ payment_method: 'cash', amount: 0, transaction_reference: '' }] };
            this.$nextTick(() => { const el = document.querySelector('[autofocus]'); if (el) el.focus(); });
        },
    };
}
</script>
@endpush
