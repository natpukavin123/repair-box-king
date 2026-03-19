@extends('layouts.app')
@section('page-title', 'Recharges')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .rch-workspace {
        gap: 0.7rem;
    }

    .rch-workspace .rch-toolbar,
    .rch-workspace .rch-filterbar {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(244, 247, 255, 0.88));
        box-shadow: 0 18px 42px -34px rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(16px);
    }

    .rch-workspace .rch-toolbar {
        padding: 0.55rem;
    }

    .rch-workspace .rch-filterbar {
        padding: 0.45rem;
        gap: 0.45rem;
    }

    .rch-workspace .rch-search-input,
    .rch-workspace .rch-form-input,
    .rch-workspace .rch-filter-control {
        min-height: 2.7rem;
        border-radius: 0.95rem;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 12px 28px -24px rgba(15, 23, 42, 0.28);
    }

    .rch-workspace .rch-search-input {
        padding-top: 0.72rem;
        padding-bottom: 0.72rem;
    }

    .rch-workspace .rch-filter-control {
        height: 2.5rem;
        min-height: 2.5rem;
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }

    .rch-workspace .rch-panel {
        border-radius: 1.35rem;
        border-color: rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 252, 255, 0.82));
        box-shadow: 0 26px 60px -42px rgba(15, 23, 42, 0.38);
    }

    .rch-workspace .rch-panel .card-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(241, 245, 255, 0.48));
    }

    .rch-workspace .rch-panel .card-body {
        padding: 1rem;
    }

    .rch-workspace .rch-table-shell {
        padding: 0.35rem 0.4rem 0.15rem;
    }

    .rch-workspace .rch-table-shell .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .rch-workspace .rch-table-shell .data-table th {
        padding: 0.75rem 0.9rem;
        font-size: 0.65rem;
        letter-spacing: 0.14em;
    }

    .rch-workspace .rch-table-shell .data-table td {
        padding: 0.8rem 0.9rem;
        font-size: 0.88rem;
    }

    .rch-workspace .rch-table-shell .data-table tbody tr {
        border-top-color: rgba(226, 232, 240, 0.92);
    }

    .rch-workspace .rch-table-shell .data-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
    }

    .rch-workspace .rch-form-scroll > div {
        padding: 0.95rem 1rem;
    }

    @media (max-width: 1023px) {
        .rch-workspace {
            gap: 0.6rem;
        }

        .rch-workspace .rch-toolbar,
        .rch-workspace .rch-filterbar {
            padding: 0.45rem;
        }

        .rch-workspace .rch-panel .card-header,
        .rch-workspace .rch-panel .card-body,
        .rch-workspace .rch-form-scroll > div {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .rch-workspace .rch-table-shell .data-table th,
        .rch-workspace .rch-table-shell .data-table td {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 767px) {
        .rch-workspace {
            gap: 0.5rem;
        }

        .rch-workspace .rch-toolbar,
        .rch-workspace .rch-filterbar {
            padding: 0.35rem;
            border-radius: 1rem;
        }

        .rch-workspace .rch-search-input,
        .rch-workspace .rch-form-input,
        .rch-workspace .rch-filter-control {
            min-height: 2.5rem;
            border-radius: 0.82rem;
        }

        .rch-workspace .rch-filter-control {
            min-height: 2.3rem;
            height: 2.3rem;
        }

        .rch-workspace .rch-panel {
            border-radius: 1.1rem;
        }

        .rch-workspace .rch-panel .card-header,
        .rch-workspace .rch-panel .card-body,
        .rch-workspace .rch-form-scroll > div {
            padding-left: 0.72rem;
            padding-right: 0.72rem;
        }

        .rch-workspace .rch-table-shell .data-table th,
        .rch-workspace .rch-table-shell .data-table td {
            padding-left: 0.68rem;
            padding-right: 0.68rem;
        }
    }

    @media (min-width: 1024px) {
        .rch-workspace .rch-table-shell .data-table th {
            padding: 0.65rem 0.8rem;
        }

        .rch-workspace .rch-table-shell .data-table td {
            padding: 0.68rem 0.8rem;
        }
    }
</style>

<div x-data="rechargesPage()" x-init="init()" class="workspace-screen rch-workspace w-full">
    <div class="grid w-full lg:flex-1 lg:min-h-0 grid-cols-1 gap-2 lg:grid-cols-3 lg:grid-rows-1">

        {{-- ===== LEFT: Recharge List (table) ===== --}}
        <div class="flex lg:min-h-0 flex-col lg:overflow-hidden lg:col-span-2">

            {{-- Search toolbar --}}
            <div class="rch-toolbar mb-1 flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="tableSearch" @input.debounce.400ms="loadHistory(1)" type="text"
                        class="form-input-custom rch-search-input pl-10 pr-10 w-full text-sm" placeholder="Search by number, customer name...">
                    <button x-show="tableSearch" @click="tableSearch = ''; loadHistory(1)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="rch-filterbar mb-1 flex shrink-0 flex-wrap items-center gap-1.5 relative z-20">
                {{-- Status dropdown --}}
                <div class="relative" x-data="{ statusOpen: false }" @click.away="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                        :class="filterStatus ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="rch-filter-control flex items-center gap-1.5 text-sm pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        <span x-text="filterStatus ? filterStatus.charAt(0).toUpperCase() + filterStatus.slice(1) : 'All Statuses'"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 w-48 z-50 border border-gray-200 bg-white shadow-xl rounded-xl p-1">
                        <template x-for="s in ['', 'success', 'pending', 'failed']" :key="'st-'+s">
                            <button type="button" @click="filterStatus = s; loadHistory(1); statusOpen = false"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors"
                                :class="filterStatus === s ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50'">
                                <span class="font-medium" x-text="s ? s.charAt(0).toUpperCase() + s.slice(1) : 'All Statuses'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Date From --}}
                <input x-model="dateFrom" @change="loadHistory(1)" type="date"
                    class="rch-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="From date">

                {{-- Date To --}}
                <input x-model="dateTo" @change="loadHistory(1)" type="date"
                    class="rch-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="To date">

                {{-- Clear all filters --}}
                <button x-show="tableSearch || filterStatus || dateFrom || dateTo"
                    @click="tableSearch = ''; filterStatus = ''; dateFrom = ''; dateTo = ''; loadHistory(1)"
                    class="rch-filter-control flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold px-3 rounded-lg border border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>

            {{-- Table --}}
            <div class="card rch-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                {{-- Overlay loader --}}
                <div x-show="histLoading && !firstLoad" x-cloak x-transition.opacity
                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-xs text-gray-400 font-medium">Loading...</span>
                    </div>
                </div>

                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Recharges (<span x-text="pagination.total"></span>)
                    </h3>
                    <button @click="loadHistory(pagination.currentPage)" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                </div>

                <div class="rch-table-shell min-h-0 flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto overscroll-contain">
                    <table class="data-table w-full">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">ID</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Customer</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Provider</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Number</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Amount</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Payment</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(r, i) in items" :key="r.id">
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="viewDetail(r)">
                                    <td class="px-3 py-2">
                                        <span class="font-semibold text-primary-600 text-sm" x-text="'#' + r.id"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="font-medium text-gray-800 text-sm leading-tight" x-text="r.customer ? r.customer.name : 'Walk-in'"></div>
                                        <div class="text-[11px] leading-tight text-gray-400" x-text="r.customer ? r.customer.mobile_number : ''"></div>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1.5 text-sm" x-show="r.provider">
                                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-white" :style="'background:' + providerColor(r.provider?.name)" x-text="r.provider?.name?.charAt(0).toUpperCase()"></span>
                                            <span x-text="r.provider.name"></span>
                                        </span>
                                        <span x-show="!r.provider" class="text-gray-400">-</span>
                                    </td>
                                    <td class="px-3 py-2 font-mono text-sm" x-text="r.mobile_number"></td>
                                    <td class="px-3 py-2 font-semibold" x-text="'₹' + Number(r.recharge_amount).toFixed(2)"></td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full" :class="{
                                            'bg-green-100 text-green-700': r.payment_method === 'cash',
                                            'bg-blue-100 text-blue-700': r.payment_method === 'upi',
                                            'bg-purple-100 text-purple-700': r.payment_method === 'card',
                                            'bg-gray-100 text-gray-700': r.payment_method === 'bank_transfer'
                                        }" x-text="r.payment_method === 'bank_transfer' ? 'Bank' : r.payment_method ? r.payment_method.toUpperCase() : '-'"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="r.status === 'success' ? 'badge-success' : r.status === 'failed' ? 'badge-danger' : 'badge-warning'" x-text="r.status"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm leading-tight" x-text="formatDate(r.created_at)"></div>
                                    </td>
                                    <td class="px-3 py-2 text-center" @click.stop>
                                        <div class="inline-flex items-center gap-1">
                                            <button @click="viewDetail(r)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0 && !histLoading">
                                <td colspan="9" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-gray-400 font-medium">No recharges found</p>
                                    <p class="text-gray-300 text-sm mt-1">Create one using the form on the right</p>
                                </td>
                            </tr>
                            <template x-if="histLoading && firstLoad">
                                <template x-for="i in 8" :key="'sk'+i">
                                    <tr>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-10"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-28"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-24"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-16"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-14"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-16 rounded-full"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-8"></div></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>

                {{-- Pagination --}}
                <div x-show="pagination.lastPage > 1" class="flex shrink-0 items-center justify-between border-t px-4 py-2 text-sm">
                    <span class="text-gray-500">
                        Page <span x-text="pagination.currentPage"></span> of <span x-text="pagination.lastPage"></span>
                    </span>
                    <div class="flex items-center gap-1">
                        <button @click="loadHistory(pagination.currentPage - 1)" :disabled="pagination.currentPage <= 1"
                            class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                        <button @click="loadHistory(pagination.currentPage + 1)" :disabled="pagination.currentPage >= pagination.lastPage"
                            class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: New Recharge Form ===== --}}
        <div class="relative order-first flex lg:min-h-0 flex-col gap-1.5 lg:order-none" :style="custOpen ? 'z-index:95;' : 'z-index:10;'">

            {{-- Customer selector --}}
            <div class="card rch-panel relative shrink-0" :style="custOpen ? 'overflow:visible; z-index:110;' : 'overflow:visible; z-index:10;'">
                <div class="card-body py-2.5" style="overflow:visible">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Customer</label>
                            <p class="mt-0.5 text-[11px] leading-tight text-gray-400" x-show="!selCust">Search and select a customer for recharge.</p>
                            <p class="mt-0.5 text-[11px] leading-tight text-emerald-600" x-show="selCust">Customer selected for this recharge.</p>
                        </div>
                        <button type="button" @click="openAddCustModal()"
                            class="btn-primary text-sm px-3 py-1.5 whitespace-nowrap w-auto">+ New</button>
                    </div>

                    <div x-show="!selCust" x-cloak class="mt-2 flex flex-col gap-1.5 sm:flex-row sm:items-end">
                        <div class="flex-1 relative" @click.away="custOpen = false">
                            <input x-model="custSearch" @focus="findCust(1)" @input.debounce.300ms="findCust(1)" type="text"
                                class="form-input-custom rch-search-input min-h-[2.4rem] py-2 text-sm" placeholder="Search by name / phone...">
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

                    <div x-show="custOpen && !custLoading && custResults.length === 0 && !selCust"
                        class="mt-1.5 text-[11px] leading-tight text-gray-400">No customers found - click <strong>+ New</strong> to add.</div>

                    <div x-show="selCust" x-cloak class="mt-2 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-emerald-900 truncate" x-text="selCust?.name"></div>
                                <div class="mt-0.5 text-[11px] leading-tight text-emerald-700" x-text="selCust?.mobile_number || 'No mobile'"></div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="clearCustomer(); $nextTick(() => findCust(1))" class="inline-flex items-center rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Change</button>
                                <button type="button" @click="clearCustomer()" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recharge Form --}}
            <div class="card rch-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Recharge
                    </h3>
                    <button x-show="form.provider_id || form.recharge_amount || form.transaction_id" @click="resetForm()" class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="rch-form-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain">
                    <div class="px-4 py-2 space-y-2">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Provider *</label>
                            <select x-model="form.provider_id" class="form-select-custom rch-form-input text-sm">
                                <option value="">Select Provider</option>
                                <template x-for="p in providers" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Mobile Number *</label>
                            <input x-model="form.mobile_number" type="text" class="form-input-custom rch-form-input text-sm" placeholder="Enter mobile number">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Amount *</label>
                            <input x-model="form.recharge_amount" type="number" step="0.01" class="form-input-custom rch-form-input text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Payment Method</label>
                            <div class="flex gap-2">
                                <template x-for="m in ['cash','upi','card']" :key="m">
                                    <button type="button" @click="form.payment_method = m" class="flex-1 py-2 px-2 text-xs font-medium rounded-lg border-2 transition-all flex items-center justify-center gap-1" :class="form.payment_method === m ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                        <span x-text="m.toUpperCase()"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="form.payment_method === 'upi' || form.payment_method === 'card'" x-cloak>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Reference No.</label>
                            <input x-model="form.transaction_id" type="text" class="form-input-custom rch-form-input text-sm" placeholder="UTR / Transaction ID">
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t px-4 py-3">
                    <button @click="save()" class="btn-primary w-full py-3 text-base font-semibold" :disabled="saving">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Submit Recharge
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== VIEW DETAIL MODAL ==================== --}}
    <div x-show="viewItem" class="modal-overlay" x-cloak @click.self="viewItem = null" x-transition>
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Recharge #<span x-text="viewItem?.id"></span>
                </h3>
                <button @click="viewItem = null" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="modal-body">
                {{-- Customer info --}}
                <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold bg-primary-100 text-primary-700"
                        x-text="(viewItem?.customer?.name || '?').charAt(0).toUpperCase()"></div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900" x-text="viewItem?.customer?.name || 'Walk-in'"></p>
                        <p class="text-sm text-gray-500" x-text="viewItem?.customer?.mobile_number || '-'"></p>
                    </div>
                    <span class="badge text-xs" :class="viewItem?.status === 'success' ? 'badge-success' : viewItem?.status === 'failed' ? 'badge-danger' : 'badge-warning'" x-text="viewItem?.status || ''"></span>
                </div>

                {{-- Details --}}
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Provider</label>
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0" :style="'background:' + providerColor(viewItem?.provider?.name)" x-text="viewItem?.provider?.name?.charAt(0).toUpperCase() || '?'"></span>
                            <span class="text-sm font-medium text-gray-800" x-text="viewItem?.provider?.name || '-'"></span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Mobile Number</label>
                        <p class="text-sm font-mono font-semibold text-gray-800" x-text="viewItem?.mobile_number || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Amount</label>
                        <p class="text-lg font-bold text-primary-600" x-text="'₹' + Number(viewItem?.recharge_amount || 0).toFixed(2)"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Payment Method</label>
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full" :class="{
                            'bg-green-100 text-green-700': viewItem?.payment_method === 'cash',
                            'bg-blue-100 text-blue-700': viewItem?.payment_method === 'upi',
                            'bg-purple-100 text-purple-700': viewItem?.payment_method === 'card',
                        }" x-text="viewItem?.payment_method ? viewItem.payment_method.toUpperCase() : '-'"></span>
                    </div>
                    <div x-show="viewItem?.transaction_id">
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Reference No.</label>
                        <p class="text-sm font-mono text-gray-700" x-text="viewItem?.transaction_id || '-'"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-0.5">Date</label>
                        <p class="text-sm text-gray-800" x-text="viewItem?.created_at ? formatDate(viewItem.created_at) : '-'"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="viewItem = null" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    {{-- ADD CUSTOMER MODAL --}}
    <div x-show="showAddCust" x-cloak class="modal-overlay">
        <div class="modal-container max-w-md" @click.stop>
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Add New Customer</h3>
                <button @click="closeAddCustModal()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
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
                <button type="button" @click="closeAddCustModal()" class="btn-secondary">Cancel</button>
                <button type="button" @click="saveNewCust()" class="btn-primary" :disabled="customerSaving"><span x-text="customerSaving ? 'Saving...' : 'Save & Select'"></span></button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rechargesPage() {
    return {
        items: [], providers: [], saving: false, firstLoad: true, histLoading: false,
        custSearch: '', custResults: [], custOpen: false, custHasMore: false, custPage: 1, custLoading: false, selCust: null,
        showAddCust: false, newCust: {name: '', mobile_number: '', email: '', address: ''},
        customerFormTried: false, customerSaving: false, customerSubmitError: '',
        tableSearch: '', dateFrom: '', dateTo: '', filterStatus: '',
        viewItem: null,
        form: { customer_id: null, provider_id: '', mobile_number: '', recharge_amount: '', transaction_id: '', payment_method: 'cash' },
        pagination: { currentPage: 1, lastPage: 1, from: 0, to: 0, total: 0 },

        async init() {
            const p = new URLSearchParams(window.location.search);
            const now = new Date();
            const y = now.getFullYear(), mo = String(now.getMonth() + 1).padStart(2, '0');
            this.dateFrom = p.get('date_from') || (y + '-' + mo + '-01');
            this.dateTo   = p.get('date_to')   || now.toISOString().split('T')[0];
            if (p.has('search')) this.tableSearch = p.get('search');
            if (p.has('status')) this.filterStatus = p.get('status');
            const r = await RepairBox.ajax('/recharge-providers');
            if (r.data) this.providers = r.data;
            if (p.has('customer_id')) await this.loadCustomerById(p.get('customer_id'));
            await this.loadHistory(1);
        },

        async findCust(page) {
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
                this.findCust(this.custPage + 1);
            }
        },

        selectCustomer(c) {
            this.selCust = c;
            this.form.customer_id = c.id;
            this.form.mobile_number = c.mobile_number || '';
            this.custResults = [];
            this.custOpen = false;
            this.custSearch = '';
            this.updateUrl();
        },

        clearCustomer() {
            this.selCust = null;
            this.form.customer_id = null;
            this.form.mobile_number = '';
            this.custSearch = '';
            this.updateUrl();
        },

        openAddCustModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.newCust = RepairBox.emptyCustomer();
            this.showAddCust = true;
        },

        closeAddCustModal() {
            this.customerFormTried = false;
            this.customerSaving = false;
            this.customerSubmitError = '';
            this.showAddCust = false;
        },

        resetForm() {
            this.form = { customer_id: this.selCust?.id || null, provider_id: '', mobile_number: this.selCust?.mobile_number || '', recharge_amount: '', transaction_id: '', payment_method: 'cash' };
        },

        updateUrl() {
            const params = new URLSearchParams();
            if (this.tableSearch) params.set('search', this.tableSearch);
            if (this.filterStatus) params.set('status', this.filterStatus);
            if (this.dateFrom) params.set('date_from', this.dateFrom);
            if (this.dateTo) params.set('date_to', this.dateTo);
            if (this.selCust) params.set('customer_id', this.selCust.id);
            if (this.pagination.currentPage > 1) params.set('page', this.pagination.currentPage);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },

        async loadHistory(page) {
            if (page < 1) return;
            this.histLoading = true;
            this.updateUrl();
            let url = '/recharges?page=' + page + '&per_page=15';
            if (this.selCust) url += '&customer_id=' + this.selCust.id;
            if (this.tableSearch) url += '&search=' + encodeURIComponent(this.tableSearch);
            if (this.filterStatus) url += '&status=' + this.filterStatus;
            if (this.dateFrom) url += '&date_from=' + this.dateFrom;
            if (this.dateTo) url += '&date_to=' + this.dateTo;
            const r = await RepairBox.ajax(url);
            if (r.data) {
                this.items = r.data;
                this.pagination = {
                    currentPage: r.current_page || 1,
                    lastPage: r.last_page || 1,
                    from: r.from || 0,
                    to: r.to || 0,
                    total: r.total || 0
                };
            }
            this.histLoading = false;
            this.firstLoad = false;
        },

        formatDate(d) {
            if (!d) return '-';
            const dt = new Date(d);
            if (isNaN(dt)) return d;
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
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

            if (!validation.valid) return;

            this.customerSaving = true;
            const r = await RepairBox.ajax('/customers', 'POST', validation.payload);
            this.customerSaving = false;

            if (r.success !== false && r.data) {
                this.closeAddCustModal();
                this.newCust = RepairBox.emptyCustomer();
                this.selectCustomer(r.data);
                RepairBox.toast('Customer added', 'success');
                return;
            }

            this.customerSubmitError = r.message || 'Unable to save customer. Please check the details and try again.';
        },

        async save() {
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }
            if (!this.form.provider_id) { RepairBox.toast('Please select a provider', 'error'); return; }
            if (!this.form.mobile_number) { RepairBox.toast('Mobile number is required', 'error'); return; }
            if (!this.form.recharge_amount || parseFloat(this.form.recharge_amount) <= 0) { RepairBox.toast('Enter a valid amount', 'error'); return; }
            this.saving = true;
            const r = await RepairBox.ajax('/recharges', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Recharge recorded successfully', 'success');
                this.resetForm();
                await this.loadHistory(1);
            }
        },

        viewDetail(r) {
            this.viewItem = r;
        },

        async loadCustomerById(id) {
            const r = await RepairBox.ajax('/customers/' + id);
            const c = r.data || r;
            if (c && c.id) {
                this.selCust = c;
                this.form.customer_id = c.id;
                this.form.mobile_number = c.mobile_number || '';
            }
        },

        providerColor(name) {
            if (!name) return '#94a3b8';
            const colors = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#06b6d4','#3b82f6'];
            let hash = 0;
            for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
            return colors[Math.abs(hash) % colors.length];
        }
    };
}
</script>
@endpush
