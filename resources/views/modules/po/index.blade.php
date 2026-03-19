@extends('layouts.app')
@section('page-title', 'Purchase Orders')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .po-workspace {
        gap: 0.7rem;
    }

    .po-workspace .po-toolbar,
    .po-workspace .po-filterbar {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(244, 247, 255, 0.88));
        box-shadow: 0 18px 42px -34px rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(16px);
    }

    .po-workspace .po-toolbar {
        padding: 0.55rem;
    }

    .po-workspace .po-filterbar {
        padding: 0.45rem;
        gap: 0.45rem;
    }

    .po-workspace .po-search-input,
    .po-workspace .po-form-input,
    .po-workspace .po-filter-control {
        min-height: 2.7rem;
        border-radius: 0.95rem;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 12px 28px -24px rgba(15, 23, 42, 0.28);
    }

    .po-workspace .po-search-input {
        padding-top: 0.72rem;
        padding-bottom: 0.72rem;
    }

    .po-workspace .po-filter-control {
        height: 2.5rem;
        min-height: 2.5rem;
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }

    .po-workspace .po-panel {
        border-radius: 1.35rem;
        border-color: rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 252, 255, 0.82));
        box-shadow: 0 26px 60px -42px rgba(15, 23, 42, 0.38);
    }

    .po-workspace .po-panel .card-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(241, 245, 255, 0.48));
    }

    .po-workspace .po-panel .card-body {
        padding: 1rem;
    }

    .po-workspace .po-table-shell {
        padding: 0.35rem 0.4rem 0.15rem;
    }

    .po-workspace .po-table-shell .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .po-workspace .po-table-shell .data-table th {
        padding: 0.75rem 0.9rem;
        font-size: 0.65rem;
        letter-spacing: 0.14em;
    }

    .po-workspace .po-table-shell .data-table td {
        padding: 0.8rem 0.9rem;
        font-size: 0.88rem;
    }

    .po-workspace .po-table-shell .data-table tbody tr {
        border-top-color: rgba(226, 232, 240, 0.92);
    }

    .po-workspace .po-table-shell .data-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
    }

    .po-workspace .po-form-scroll > div {
        padding: 0.95rem 1rem;
    }

    .po-workspace .po-status-menu {
        border-radius: 1rem;
        padding: 0.35rem;
    }

    @media (max-width: 1023px) {
        .po-workspace {
            gap: 0.6rem;
        }

        .po-workspace .po-toolbar,
        .po-workspace .po-filterbar {
            padding: 0.45rem;
        }

        .po-workspace .po-panel .card-header,
        .po-workspace .po-panel .card-body,
        .po-workspace .po-form-scroll > div {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .po-workspace .po-table-shell .data-table th,
        .po-workspace .po-table-shell .data-table td {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 767px) {
        .po-workspace {
            gap: 0.5rem;
        }

        .po-workspace .po-toolbar,
        .po-workspace .po-filterbar {
            padding: 0.35rem;
            border-radius: 1rem;
        }

        .po-workspace .po-search-input,
        .po-workspace .po-form-input,
        .po-workspace .po-filter-control {
            min-height: 2.5rem;
            border-radius: 0.82rem;
        }

        .po-workspace .po-filter-control {
            min-height: 2.3rem;
            height: 2.3rem;
        }

        .po-workspace .po-panel {
            border-radius: 1.1rem;
        }

        .po-workspace .po-panel .card-header,
        .po-workspace .po-panel .card-body,
        .po-workspace .po-form-scroll > div {
            padding-left: 0.72rem;
            padding-right: 0.72rem;
        }

        .po-workspace .po-table-shell .data-table th,
        .po-workspace .po-table-shell .data-table td {
            padding-left: 0.68rem;
            padding-right: 0.68rem;
        }
    }

    @media (min-width: 1024px) {
        .po-workspace .po-table-shell .data-table th {
            padding: 0.65rem 0.8rem;
        }

        .po-workspace .po-table-shell .data-table td {
            padding: 0.68rem 0.8rem;
        }
    }
</style>

<div x-data="poPage()" x-init="init()" class="workspace-screen po-workspace w-full lg:overflow-hidden">
    <div class="grid w-full lg:flex-1 lg:min-h-0 grid-cols-1 gap-2 lg:overflow-hidden lg:grid-cols-3 lg:grid-rows-1">

        {{-- ===== LEFT: PO Request List (table) ===== --}}
        <div class="flex lg:min-h-0 flex-col lg:overflow-hidden lg:col-span-2">

            {{-- Search toolbar --}}
            <div class="po-toolbar mb-1 flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="filters.search" @input.debounce.400ms="page=1; load()" type="text"
                        class="form-input-custom po-search-input pl-10 pr-10 w-full text-sm" placeholder="Search PO requests, customer...">
                    <button x-show="filters.search" @click="filters.search = ''; page=1; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="po-filterbar mb-1 flex shrink-0 flex-wrap items-center gap-1.5">
                {{-- Status dropdown --}}
                <div class="relative" x-data="{ statusOpen: false }" @click.away="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                        :class="filters.status ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="po-filter-control flex items-center gap-1.5 text-sm pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        <span x-text="filters.status ? statusLabel(filters.status) : 'All Statuses'"></span>
                        <span x-show="filters.status" class="ml-0.5 bg-primary-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">1</span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-cloak x-transition.origin.top.left
                        class="po-status-menu absolute top-full left-0 mt-1 w-64 z-50 border border-gray-200 bg-white shadow-xl">
                        <button type="button" @click="setStatus(''); statusOpen = false" class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-sm transition-colors" :class="!filters.status ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50'">
                            <span class="font-medium">All Statuses</span>
                            <span class="text-xs font-bold" x-text="allCounts.all"></span>
                        </button>
                        <template x-for="s in statusList" :key="s.key">
                            <button type="button" @click="setStatus(filters.status === s.key ? '' : s.key); statusOpen = false" class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-sm transition-colors" :class="filters.status === s.key ? s.activeCls : 'text-slate-700 hover:bg-slate-50'">
                                <span class="flex items-center gap-2 font-medium">
                                    <span class="inline-block h-2 w-2 rounded-full" :class="s.dotCls"></span>
                                    <span x-text="s.label"></span>
                                </span>
                                <span class="text-xs font-bold" x-text="allCounts[s.key]"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Date From --}}
                <input x-model="filters.date_from" @change="page=1; load()" type="date"
                    class="po-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="From date">

                {{-- Date To --}}
                <input x-model="filters.date_to" @change="page=1; load()" type="date"
                    class="po-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="To date">

                {{-- Clear all filters --}}
                <button x-show="filters.search || filters.status || filters.date_from || filters.date_to"
                    @click="clearFilters()"
                    class="po-filter-control flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold px-3 rounded-lg border border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>

            {{-- Table --}}
            <div class="card po-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                {{-- Overlay loader --}}
                <div x-show="loading && !firstLoad" x-cloak x-transition.opacity
                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-xs text-gray-400 font-medium">Loading...</span>
                    </div>
                </div>

                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        PO Requests (<span x-text="total"></span>)
                    </h3>
                    <button @click="load()" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                </div>

                <div class="po-table-shell min-h-0 flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto overscroll-contain">
                    <table class="data-table w-full">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">ID</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Customer</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Requested Items</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Due Date</th>
                                <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="item in items" :key="item.id">
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="viewDetail(item)">
                                    <td class="px-3 py-2">
                                        <span class="font-semibold text-primary-600 text-sm" x-text="'#' + item.id"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-gray-800 text-sm leading-tight" x-text="item.customer_name || item.customer?.name || 'Walk-in'"></div>
                                        <div class="text-[11px] leading-tight text-gray-400" x-text="item.customer_phone || item.customer?.mobile_number || ''"></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-gray-700 truncate max-w-[220px]" x-text="item.requested_items"></p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="statusBadge(item.status)" x-text="statusLabel(item.status)"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm leading-tight" x-text="fmtDate(item.created_at)"></div>
                                        <template x-if="item.required_by">
                                            <div class="text-[11px] leading-tight mt-0.5"
                                                :class="isOverdue(item.required_by) && item.status !== 'completed' && item.status !== 'cancelled' ? 'text-red-500 font-semibold' : 'text-gray-400'"
                                                x-text="'Due: ' + fmtDate(item.required_by)"></div>
                                        </template>
                                    </td>
                                    <td class="px-3 py-2 text-center" @click.stop>
                                        <div class="inline-flex items-center gap-1">
                                            <button @click="viewDetail(item)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0 && !loading">
                                <td colspan="6" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <p class="text-gray-400 font-medium">No PO requests found</p>
                                    <p class="text-gray-300 text-sm mt-1">Create one using the form on the right</p>
                                </td>
                            </tr>
                            <template x-if="loading && firstLoad">
                                <template x-for="i in 8" :key="'sk'+i">
                                    <tr>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-12"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-28"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-36"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-10"></div></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div x-show="lastPage > 1" class="flex shrink-0 items-center justify-between border-t px-4 py-2 text-sm">
                    <span class="text-gray-500">
                        Page <span x-text="page"></span> of <span x-text="lastPage"></span>
                    </span>
                    <div class="flex items-center gap-1">
                        <button @click="page--; load()" :disabled="page <= 1"
                            class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                        <button @click="page++; load()" :disabled="page >= lastPage"
                            class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: New PO Request Form ===== --}}
        <div class="relative order-first flex lg:min-h-0 flex-col gap-1.5 lg:overflow-hidden lg:order-none" :style="custOpen ? 'z-index:95;' : 'z-index:0;'">

            {{-- Customer selector --}}
            <div class="card po-panel relative shrink-0" :style="custOpen ? 'overflow:visible; z-index:110;' : 'overflow:visible; z-index:10;'">
                <div class="card-body py-2.5" style="overflow:visible">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Customer</label>
                            <p class="mt-0.5 text-[11px] leading-tight text-gray-400" x-show="!selectedCustomer">Search and attach a customer to the PO request.</p>
                            <p class="mt-0.5 text-[11px] leading-tight text-emerald-600" x-show="selectedCustomer">Customer selected for this PO.</p>
                        </div>
                        <button type="button" @click="openNewCustModal()"
                            class="btn-primary text-sm px-3 py-1.5 whitespace-nowrap w-auto">+ New</button>
                    </div>

                    <div x-show="!selectedCustomer" x-cloak class="mt-2 flex flex-col gap-1.5 sm:flex-row sm:items-end">
                        <div class="flex-1 relative" @click.away="custOpen = false">
                            <input x-model="custSearch" @focus="findCustomers(1)" @input.debounce.300ms="findCustomers(1)" type="text"
                                class="form-input-custom po-search-input min-h-[2.4rem] py-2 text-sm" placeholder="Search by name / phone...">
                            <div x-show="custOpen && custResults.length > 0" x-cloak class="absolute left-0 right-0 mt-1 overflow-hidden rounded-lg border bg-white shadow-lg" style="z-index:160;">
                                <div class="max-h-48 overflow-y-auto" @scroll="handleCustScroll($event)">
                                    <template x-for="c in custResults" :key="c.id">
                                        <button @click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b last:border-0">
                                            <div class="font-medium text-gray-800" x-text="c.name"></div>
                                            <div class="text-xs text-gray-400" x-text="c.mobile_number || 'No mobile'"></div>
                                        </button>
                                    </template>
                                    <div x-show="custLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading…</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="custOpen && !custLoading && custResults.length === 0 && !selectedCustomer"
                        class="mt-1.5 text-[11px] leading-tight text-gray-400">No customers found - click <strong>+ New</strong> to add.</div>

                    <div x-show="selectedCustomer" x-cloak class="mt-2 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-emerald-900 truncate" x-text="selectedCustomer?.name"></div>
                                <div class="mt-0.5 text-[11px] leading-tight text-emerald-700" x-text="selectedCustomer?.mobile_number || 'No mobile'"></div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="clearCustomer(); $nextTick(() => findCustomers(1))" class="inline-flex items-center rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Change</button>
                                <button type="button" @click="clearCustomer()" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PO Request Details Form --}}
            <div class="card po-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        New PO Request
                    </h3>
                    <button x-show="form.requested_items || form.notes || form.required_by" @click="resetForm()" class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="po-form-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain">
                    <div class="px-4 py-2 space-y-2">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Requested Products / Parts *</label>
                            <textarea x-model="form.requested_items" rows="5" class="form-input-custom po-form-input text-sm"
                                placeholder="e.g. iPhone 14 display&#10;Samsung A14 battery × 2&#10;Type-C fast charger"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Required By</label>
                            <input type="date" x-model="form.required_by" class="form-input-custom po-form-input text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Notes</label>
                            <textarea x-model="form.notes" rows="2" class="form-input-custom po-form-input text-sm"
                                placeholder="Budget, color, any extra detail..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t px-4 py-3">
                    <button @click="save()" class="btn-primary w-full py-3 text-base font-semibold" :disabled="saving">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save PO Request
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== VIEW DETAIL MODAL ==================== --}}
    <div x-show="viewing" class="modal-overlay" x-cloak @click.self="viewing = null" x-transition>
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    PO Request #<span x-text="viewing?.id"></span>
                </h3>
                <button @click="viewing = null" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Customer info --}}
                <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold bg-primary-100 text-primary-700"
                        x-text="(viewing?.customer_name || viewing?.customer?.name || '?').charAt(0).toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900" x-text="viewing?.customer_name || viewing?.customer?.name || '-'"></p>
                        <p class="text-sm text-gray-500" x-text="viewing?.customer_phone || viewing?.customer?.mobile_number || '-'"></p>
                    </div>
                    <span class="badge text-xs" :class="statusBadge(viewing?.status)" x-text="statusLabel(viewing?.status)"></span>
                </div>

                {{-- Details --}}
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Requested Items</label>
                        <div class="text-sm text-gray-800 whitespace-pre-wrap bg-white border rounded-lg px-4 py-3" x-text="viewing?.requested_items"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-0.5">Required By</label>
                            <p class="text-sm text-gray-800" x-text="viewing?.required_by ? fmtDate(viewing.required_by) : 'Not set'"></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-0.5">Created</label>
                            <p class="text-sm text-gray-800" x-text="fmtDate(viewing?.created_at) + (viewing?.creator ? ' by ' + viewing.creator.name : '')"></p>
                        </div>
                    </div>
                    <div x-show="viewing?.notes">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Notes</label>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap" x-text="viewing?.notes"></p>
                    </div>

                    {{-- Status changer --}}
                    <div class="border-t pt-4">
                        <label class="block text-xs font-medium text-gray-500 mb-2">Update Status</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="s in ['open','ordered','received','completed','cancelled']" :key="s">
                                <button @click="changeStatus(viewing, s)"
                                    class="px-3.5 py-1.5 rounded-full text-xs font-semibold border-2 transition-all"
                                    :class="viewing?.status === s ? statusBtnActive(s) : 'border-gray-200 text-gray-500 hover:border-gray-300 bg-white'"
                                    x-text="statusLabel(s)"></button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="viewing = null" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    {{-- ADD CUSTOMER MODAL --}}
    <div x-show="showNewCust" x-cloak class="modal-overlay">
        <div class="modal-container max-w-md" @click.stop>
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Add New Customer</h3>
                <button @click="closeNewCustModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-3">
                <div x-show="customerSubmitError" x-text="customerSubmitError" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="newCust.name" type="text" class="form-input-custom" placeholder="Full name" required>
                    <p x-show="customerFormTried && !newCust.name.trim()" class="text-xs text-red-500 mt-1">Name is required</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile * <span class="text-xs text-gray-500">(10 digits)</span></label>
                    <input x-model="newCust.mobile_number" type="text" class="form-input-custom" placeholder="10-digit mobile number"
                        inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required
                        @input="newCust.mobile_number = RepairBox.normalizeCustomerMobile(newCust.mobile_number)"
                        @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()">
                    <p x-show="customerFormTried && !newCust.mobile_number.trim()" class="text-xs text-red-500 mt-1">Mobile number is required</p>
                    <p x-show="(customerFormTried || newCust.mobile_number) && newCust.mobile_number.trim() && !/^\d{10}$/.test(newCust.mobile_number.trim())" class="text-xs text-red-500 mt-1">Mobile must be exactly 10 digits</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input x-model="newCust.email" type="email" class="form-input-custom" placeholder="Optional">
                    <p x-show="(customerFormTried || newCust.email) && newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCust.email.trim())" class="text-xs text-red-500 mt-1">Please enter a valid email</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input x-model="newCust.address" type="text" class="form-input-custom" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="closeNewCustModal()" class="btn-secondary">Cancel</button>
                <button type="button" @click="saveNewCust()" class="btn-primary" :disabled="customerSaving"><span x-text="customerSaving ? 'Saving...' : 'Save & Select'"></span></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function poPage() {
    return {
        items: [],
        loading: false,
        firstLoad: true,
        saving: false,
        viewing: null,
        page: 1,
        lastPage: 1,
        total: 0,
        allCounts: { all: 0, open: 0, ordered: 0, received: 0, completed: 0, cancelled: 0 },
        filters: { search: '', status: '', date_from: '', date_to: '' },
        form: { customer_id: '', customer_name: '', customer_phone: '', requested_items: '', required_by: '', notes: '' },

        // Customer search
        custSearch: '',
        custResults: [],
        custOpen: false,
        custHasMore: false,
        custPage: 1,
        custLoading: false,
        selectedCustomer: null,
        showNewCust: false,
        newCust: { name: '', mobile_number: '', email: '', address: '' },
        customerFormTried: false,
        customerSaving: false,
        customerSubmitError: '',

        statusList: [
            { key: 'open',      label: 'Open',      activeCls: 'bg-blue-50 text-blue-700',    dotCls: 'bg-blue-500' },
            { key: 'ordered',   label: 'Ordered',   activeCls: 'bg-amber-50 text-amber-700',  dotCls: 'bg-amber-500' },
            { key: 'received',  label: 'Received',  activeCls: 'bg-purple-50 text-purple-700', dotCls: 'bg-purple-500' },
            { key: 'completed', label: 'Completed', activeCls: 'bg-emerald-50 text-emerald-700', dotCls: 'bg-emerald-500' },
            { key: 'cancelled', label: 'Cancelled', activeCls: 'bg-red-50 text-red-700',      dotCls: 'bg-red-500' },
        ],

        async init() {
            this.readUrl();
            await this.load();
        },

        // === URL Sync ===
        readUrl() {
            const p = new URLSearchParams(window.location.search);
            this.filters.status = p.get('status') || '';
            this.filters.search = p.get('search') || '';
            this.filters.date_from = p.get('date_from') || '';
            this.filters.date_to = p.get('date_to') || '';
            this.page = parseInt(p.get('page')) || 1;
        },

        pushUrl() {
            const p = new URLSearchParams();
            if (this.filters.status) p.set('status', this.filters.status);
            if (this.filters.search) p.set('search', this.filters.search);
            if (this.filters.date_from) p.set('date_from', this.filters.date_from);
            if (this.filters.date_to) p.set('date_to', this.filters.date_to);
            if (this.page > 1) p.set('page', this.page);
            const qs = p.toString();
            const url = window.location.pathname + (qs ? '?' + qs : '');
            window.history.replaceState(null, '', url);
        },

        setStatus(status) {
            this.filters.status = status;
            this.page = 1;
            this.load();
        },

        clearFilters() {
            this.filters.search = '';
            this.filters.status = '';
            this.filters.date_from = '';
            this.filters.date_to = '';
            this.page = 1;
            this.load();
        },

        resetForm() {
            this.form = { customer_id: '', customer_name: '', customer_phone: '', requested_items: '', required_by: '', notes: '' };
            this.selectedCustomer = null;
            this.custSearch = '';
        },

        // === Customer Search (paginated, show on focus) ===
        async findCustomers(page) {
            page = page || 1;
            if (page === 1) this.custPage = 1;
            this.custLoading = true;
            const r = await RepairBox.ajax('/customers-search?page=' + page + '&q=' + encodeURIComponent(this.custSearch || ''));
            this.custLoading = false;
            const rows = Array.isArray(r.data) ? r.data : [];
            this.custResults = page === 1 ? rows : this.custResults.concat(rows);
            this.custHasMore = r.has_more || false;
            this.custPage = page;
            if (this.custResults.length > 0 || this.custSearch) this.custOpen = true;
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
            this.form.customer_name = c.name;
            this.form.customer_phone = c.mobile_number;
            this.custSearch = '';
            this.custResults = [];
            this.custOpen = false;
            this.showNewCust = false;
        },

        clearCustomer() {
            this.selectedCustomer = null;
            this.form.customer_id = '';
            this.form.customer_name = '';
            this.form.customer_phone = '';
            this.custSearch = '';
        },

        openNewCustModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.newCust = RepairBox.emptyCustomer();
            this.showNewCust = true;
        },

        closeNewCustModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.showNewCust = false;
        },

        async saveNewCust() {
            this.customerFormTried = true;
            this.customerSubmitError = '';

            const validation = RepairBox.validateCustomerPayload(this.newCust);
            this.newCust = {
                ...this.newCust,
                ...validation.payload,
                email: validation.payload.email || '',
                address: validation.payload.address || '',
            };

            if (!validation.valid) {
                return;
            }

            this.customerSaving = true;
            const r = await RepairBox.ajax('/customers', 'POST', validation.payload);
            this.customerSaving = false;

            if (r.success !== false && r.data) {
                this.form.customer_id = r.data.id;
                this.form.customer_name = r.data.name;
                this.form.customer_phone = r.data.mobile_number || '';
                this.selectedCustomer = r.data;
                this.custResults = [];
                this.custSearch = '';
                this.custOpen = false;
                this.closeNewCustModal();
                this.newCust = RepairBox.emptyCustomer();
                RepairBox.toast('Customer added', 'success');
                return;
            }

            this.customerSubmitError = r.message || 'Unable to save customer. Please check the details and try again.';
        },

        // === Helpers ===
        statusLabel(s) {
            return { open: 'Open', ordered: 'Ordered', received: 'Received', completed: 'Completed', cancelled: 'Cancelled' }[s] || s;
        },
        statusBadge(s) {
            return { open: 'badge-info', ordered: 'badge-warning', received: 'badge-primary', completed: 'badge-success', cancelled: 'badge-danger' }[s] || 'badge-info';
        },
        statusBtnActive(s) {
            return {
                open:      'border-blue-500 bg-blue-50 text-blue-700',
                ordered:   'border-amber-500 bg-amber-50 text-amber-700',
                received:  'border-purple-500 bg-purple-50 text-purple-700',
                completed: 'border-emerald-500 bg-emerald-50 text-emerald-700',
                cancelled: 'border-red-500 bg-red-50 text-red-700',
            }[s] || '';
        },
        fmtDate(d) {
            if (!d) return '-';
            const dt = new Date(d);
            if (isNaN(dt)) return d;
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        isOverdue(d) {
            if (!d) return false;
            const today = new Date(); today.setHours(0,0,0,0);
            return new Date(d) < today;
        },

        // === Data ===
        async load() {
            this.loading = true;
            this.pushUrl();
            const params = { page: this.page, with_counts: 1 };
            if (this.filters.search) params.search = this.filters.search;
            if (this.filters.status) params.status = this.filters.status;
            if (this.filters.date_from) params.date_from = this.filters.date_from;
            if (this.filters.date_to) params.date_to = this.filters.date_to;

            const r = await RepairBox.ajax('/po', 'GET', params);
            if (r.data) this.items = r.data;
            if (r.meta) { this.lastPage = r.meta.last_page; this.total = r.meta.total; }
            if (r.counts) {
                this.allCounts = r.counts;
            }
            this.loading = false;
            this.firstLoad = false;
        },

        async save() {
            if (!this.form.requested_items.trim()) {
                return RepairBox.toast('Please enter the requested items', 'error');
            }
            if (!this.form.customer_name.trim() && !this.form.customer_id) {
                return RepairBox.toast('Please select or enter a customer', 'error');
            }
            this.saving = true;
            const r = await RepairBox.ajax('/po', 'POST', { ...this.form });
            this.saving = false;
            if (r.success === false) return;

            RepairBox.toast(r.message || 'PO request saved', 'success');
            this.resetForm();
            this.closeNewCustModal();
            this.page = 1;
            await this.load();
        },

        viewDetail(item) {
            this.viewing = item;
        },

        async changeStatus(item, status) {
            if (!status || status === item.status) return;
            const r = await RepairBox.ajax(`/po/${item.id}/status`, 'PUT', { status });
            if (r.success === false) return;
            item.status = status;
            RepairBox.toast(r.message || 'Status updated', 'success');
            await this.load();
        }
    };
}
</script>
@endpush
