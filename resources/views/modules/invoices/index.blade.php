@extends('layouts.app')
@section('page-title', 'Invoices')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .invoices-workspace .workspace-toolbar,
    .invoices-workspace .workspace-filterbar,
    .invoices-workspace .workspace-table-card {
        border-radius: 1.2rem;
    }

    .invoices-workspace .invoice-status-pill {
        min-height: 2.25rem;
    }

    .invoices-workspace .workspace-table-scroll .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }
 </style>

<div x-data="invoicesPage()" x-init="load()" class="workspace-screen invoices-workspace">

    <x-ui.action-bar title="Invoice Ledger" description="Billing, payment collection, and invoice review stay in the same contained workspace.">
        <a href="/admin/pos" class="btn-primary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Invoice
        </a>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="flex flex-1 flex-col gap-3 min-w-0">
            <div class="flex flex-col lg:flex-row gap-3">

            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input x-model="filter.search" @input.debounce.400ms="page=1; load()" type="text"
                    class="form-input-custom pl-10 w-full"
                    placeholder="Search invoice # or customer name...">
                <button x-show="filter.search" @click="filter.search = ''; page=1; load()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 sm:w-auto w-full">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input x-model="filter.date_from" @change="page=1; load()" type="date"
                        class="form-input-custom pl-9 text-sm w-full" title="From date">
                </div>
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <input x-model="filter.date_to" @change="page=1; load()" type="date"
                        class="form-input-custom pl-9 text-sm w-full" title="To date">
                </div>
            </div>
            </div>

            <div class="mobile-scroll sm:overflow-visible">
            <div class="flex gap-1.5 items-center pb-1 sm:pb-0">
                <button @click="filter.payment_status = ''; page=1; load()"
                    class="invoice-status-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
                    :class="filter.payment_status === '' ? 'bg-gray-800 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    All
                </button>
                <button @click="filter.payment_status = 'paid'; page=1; load()"
                    class="invoice-status-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all inline-flex items-center gap-1"
                    :class="filter.payment_status === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    Paid
                </button>
                <button @click="filter.payment_status = 'partial'; page=1; load()"
                    class="invoice-status-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all inline-flex items-center gap-1"
                    :class="filter.payment_status === 'partial' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    Partial
                </button>
                <button @click="filter.payment_status = 'unpaid'; page=1; load()"
                    class="invoice-status-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all inline-flex items-center gap-1"
                    :class="filter.payment_status === 'unpaid' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                    Unpaid
                </button>
                <button @click="resetFilters()" title="Reset to current month"
                    class="invoice-status-pill px-3 py-1.5 rounded-full text-xs font-semibold bg-white border border-gray-200 text-gray-500 hover:bg-gray-50 hover:border-gray-300 transition-all inline-flex items-center gap-1"
                    x-show="filter.search || filter.payment_status || filter.date_from !== _monthStart() || filter.date_to !== _monthEnd()">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    This Month
                </button>
            </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-gray-100">
            <div class="flex items-center gap-3 bg-gray-50 rounded-lg px-3 py-2.5">
                <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Invoices</p>
                    <p class="text-base font-bold text-gray-800" x-text="meta ? meta.total : '—'"></p>
                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="filter.date_from && filter.date_to ? filter.date_from + ' – ' + filter.date_to : 'All time'"></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-emerald-50 rounded-lg px-3 py-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-emerald-600">Paid</p>
                    <p class="text-base font-bold text-emerald-700" x-text="pageStats.paid"></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-amber-50 rounded-lg px-3 py-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-amber-600">Partial</p>
                    <p class="text-base font-bold text-amber-700" x-text="pageStats.partial"></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-red-50 rounded-lg px-3 py-2.5">
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-red-500">Unpaid</p>
                    <p class="text-base font-bold text-red-600" x-text="pageStats.unpaid"></p>
                </div>
            </div>
        </div>
        </div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Invoice Register</h3>
                <p class="text-sm text-slate-500">Amounts, payment status, and actions remain attached to a fixed internal table.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50">
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="inv in items" :key="inv.id">
                            <tr>
                                <td>
                                    <span class="font-semibold text-primary-600" x-text="inv.invoice_number"></span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0 text-xs font-bold text-gray-500"
                                            x-text="(inv.customer ? inv.customer.name : 'W').charAt(0).toUpperCase()"></div>
                                        <span class="font-medium text-gray-700" x-text="inv.customer ? inv.customer.name : 'Walk-in'"></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold px-2"
                                        x-text="inv.items_count ?? (inv.items ? inv.items.length : 0)"></span>
                                </td>
                                <td class="font-semibold" x-text="'₹' + Number(inv.final_amount || inv.total_amount || 0).toFixed(2)"></td>
                                <td class="text-emerald-700 font-medium" x-text="'₹' + Number(inv.paid_amount || 0).toFixed(2)"></td>
                                <td>
                                    <span x-show="Number(inv.balance_due || 0) > 0"
                                        class="text-red-600 font-semibold"
                                        x-text="'₹' + Number(inv.balance_due || 0).toFixed(2)"></span>
                                    <span x-show="Number(inv.balance_due || 0) <= 0" class="text-gray-300">—</span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full border"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-200': inv.payment_status === 'paid',
                                            'bg-amber-50 text-amber-700 border-amber-200': inv.payment_status === 'partial',
                                            'bg-red-50 text-red-700 border-red-200': !inv.payment_status || inv.payment_status === 'unpaid'
                                        }">
                                        <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 6 6"><circle cx="3" cy="3" r="3"/></svg>
                                        <span x-text="(inv.payment_status || 'unpaid').charAt(0).toUpperCase() + (inv.payment_status || 'unpaid').slice(1)"></span>
                                    </span>
                                </td>
                                <td class="text-sm text-gray-600" x-text="new Date(inv.created_at).toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'})"></td>
                                <td class="whitespace-nowrap">
                                    <div class="flex items-center gap-1">
                                        <button @click="view(inv)"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button x-show="inv.payment_status !== 'paid'"
                                            @click="openPayModal(inv)"
                                            class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition-colors"
                                            title="Record Payment">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                            Pay
                                        </button>
                                        <a :href="'/admin/invoices/' + inv.id + '/print'" target="_blank"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-500 hover:text-green-600 hover:bg-green-50 transition-colors"
                                            title="Print">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty state --}}
                        <tr x-show="items.length === 0 && !loading">
                            <td colspan="9" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-600 font-medium">No invoices found</p>
                                        <p class="text-sm text-gray-400 mt-1">Try adjusting your search or filters</p>
                                    </div>
                                    <button x-show="filter.search || filter.payment_status || filter.date_from !== _monthStart() || filter.date_to !== _monthEnd()"
                                        @click="resetFilters()"
                                        class="text-xs text-primary-600 hover:underline">Reset to current month</button>
                                </div>
                            </td>
                        </tr>

                        {{-- Skeleton --}}
                        <template x-if="loading">
                            <template x-for="i in 8" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="flex items-center gap-2"><div class="skeleton w-7 h-7 rounded-full"></div><div class="skeleton h-3 w-28"></div></div></td>
                                    <td><div class="skeleton h-5 w-8 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                    <td><div class="skeleton h-3 w-14"></div></td>
                                    <td><div class="skeleton h-5 w-16 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-7 w-20 rounded-lg"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
        <x-slot:footer>
            <div x-show="meta && meta.last_page > 1"
                class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-500"
                    x-text="meta ? 'Showing ' + ((meta.current_page-1)*meta.per_page+1) + '–' + Math.min(meta.current_page*meta.per_page, meta.total) + ' of ' + meta.total + ' invoices' : ''"></p>
                <div class="flex items-center gap-1">
                    <button @click="goPage(meta.current_page - 1)" :disabled="!meta || meta.current_page === 1"
                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <template x-for="p in pageRange()" :key="'p'+p">
                        <button @click="p !== '…' && goPage(p)" :disabled="p === '…'"
                            class="w-8 h-8 rounded-lg border text-sm font-medium transition-colors"
                            :class="p === meta?.current_page
                                ? 'bg-primary-600 text-white border-primary-600'
                                : (p === '…' ? 'border-transparent text-gray-400 cursor-default' : 'border-gray-200 text-gray-600 hover:bg-white')"
                            x-text="p"></button>
                    </template>
                    <button @click="goPage(meta.current_page + 1)" :disabled="!meta || meta.current_page === meta.last_page"
                        class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </x-slot:footer>
    </x-ui.table-card>

    {{-- ══ VIEW MODAL ══ --}}
    <div x-show="showView" class="modal-overlay" x-cloak @keydown.escape.window="showView = false">
        <div class="modal-container admin-modal modal-lg">
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-semibold" x-text="'Invoice #' + (viewData?.invoice_number || '')"></h3>
                    <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                        :class="{
                            'bg-emerald-100 text-emerald-700': viewData?.payment_status === 'paid',
                            'bg-amber-100 text-amber-700': viewData?.payment_status === 'partial',
                            'bg-red-100 text-red-700': !viewData?.payment_status || viewData?.payment_status === 'unpaid'
                        }"
                        x-text="(viewData?.payment_status || 'unpaid').toUpperCase()"></span>
                </div>
                <button @click="showView = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 text-sm">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Customer</p>
                        <p class="font-semibold text-gray-800" x-text="viewData?.customer?.name || 'Walk-in'"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Date</p>
                        <p class="font-semibold text-gray-800" x-text="viewData ? new Date(viewData.created_at).toLocaleString('en-IN') : ''"></p>
                    </div>
                    <div class="bg-primary-50 rounded-lg p-3">
                        <p class="text-xs text-primary-500 mb-1">Invoice Total</p>
                        <p class="font-bold text-lg text-primary-700" x-text="'₹' + Number(viewData?.final_amount || viewData?.total_amount || 0).toFixed(2)"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-400 mb-1">Billed By</p>
                        <p class="font-semibold text-gray-800" x-text="viewData?.creator?.name || '—'"></p>
                    </div>
                </div>

                {{-- Balance banner --}}
                <div x-show="viewData && viewData.payment_status !== 'paid'"
                    class="rounded-lg p-3 flex items-center justify-between"
                    :class="viewData?.payment_status === 'partial' ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200'">
                    <div>
                        <p class="text-xs font-semibold"
                            :class="viewData?.payment_status === 'partial' ? 'text-amber-700' : 'text-red-700'"
                            x-text="viewData?.payment_status === 'partial' ? 'Partial Payment — Balance Remaining' : 'Payment Pending'"></p>
                        <p class="text-sm font-bold mt-0.5"
                            :class="viewData?.payment_status === 'partial' ? 'text-amber-800' : 'text-red-800'"
                            x-text="'Balance Due: ₹' + Number(viewData?.balance_due || viewData?.final_amount || viewData?.total_amount || 0).toFixed(2)"></p>
                    </div>
                    <button @click="showView = false; openPayModal(viewData)" class="btn-primary text-xs px-3 py-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Record Payment
                    </button>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Items</h4>
                    <div class="mobile-scroll">
                    <table class="data-table">
                        <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>MRP</th><th>Price</th><th>Total</th></tr></thead>
                        <tbody>
                            <template x-for="it in viewData?.items || []" :key="it.id">
                                <tr>
                                    <td x-text="it.item_name"></td>
                                    <td><span class="badge badge-secondary text-xs" x-text="it.item_type"></span></td>
                                    <td x-text="it.quantity"></td>
                                    <td>
                                        <template x-if="Number(it.mrp) > Number(it.price)">
                                            <span class="text-gray-500 text-xs" x-text="'₹' + Number(it.mrp).toFixed(2)"></span>
                                        </template>
                                        <template x-if="!(Number(it.mrp) > Number(it.price))">
                                            <span class="text-gray-400 text-xs">—</span>
                                        </template>
                                    </td>
                                    <td x-text="'₹' + Number(it.price).toFixed(2)"></td>
                                    <td class="font-medium" x-text="'₹' + Number(it.total).toFixed(2)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Payments Received</h4>
                    <div x-show="!(viewData?.payments?.length)" class="text-sm text-gray-400 italic">No payments recorded yet.</div>
                    <div x-show="viewData?.payments?.length" class="mobile-scroll">
                    <table class="data-table">
                        <thead><tr><th>Method</th><th>Amount</th><th>Reference</th><th>Date</th></tr></thead>
                        <tbody>
                            <template x-for="p in viewData?.payments || []" :key="p.id">
                                <tr>
                                    <td>
                                        <span class="badge text-xs"
                                            :class="{
                                                'bg-emerald-100 text-emerald-700': p.payment_method === 'cash',
                                                'bg-blue-100 text-blue-700': p.payment_method === 'upi',
                                                'bg-purple-100 text-purple-700': p.payment_method === 'card',
                                                'bg-gray-100 text-gray-700': p.payment_method === 'bank_transfer' || p.payment_method === 'cheque'
                                            }"
                                            x-text="p.payment_method === 'bank_transfer' ? 'Bank' : (p.payment_method || '-').toUpperCase()"></span>
                                    </td>
                                    <td class="font-medium" x-text="'₹' + Number(p.amount).toFixed(2)"></td>
                                    <td class="text-gray-500" x-text="p.transaction_reference || '—'"></td>
                                    <td class="text-xs text-gray-400" x-text="p.created_at ? new Date(p.created_at).toLocaleDateString('en-IN') : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <a :href="'/admin/invoices/' + viewData?.id + '/print'" target="_blank" class="btn-secondary text-xs flex items-center justify-center gap-1.5 w-full sm:w-auto">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </a>
                <button @click="showView = false" class="btn-secondary text-xs w-full sm:w-auto">Close</button>
            </div>
        </div>
    </div>

    {{-- ══ PAYMENT MODAL ══ --}}
    <div x-show="showPayModal" class="modal-overlay" x-cloak @keydown.escape.window="showPayModal = false">
        <div class="modal-container admin-modal max-w-lg" @click.stop>
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Record Payment</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Invoice <span class="font-semibold text-primary-600" x-text="'#' + (payTarget?.invoice_number ?? '')"></span>
                        <span x-show="payTarget?.payment_status === 'partial'" class="ml-1 text-amber-600">(partial — balance remaining)</span>
                    </p>
                </div>
                <button @click="showPayModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="modal-body space-y-4">
                <x-ui.form-section title="Payment Summary" description="Review the invoice total, amount already paid, and current balance before confirming." gridClass="grid grid-cols-1 sm:grid-cols-3 gap-3 text-center">
                    <div>
                        <p class="text-xs text-gray-500">Invoice Total</p>
                        <p class="text-base font-bold text-primary-700" x-text="'₹' + Number(payTarget?.final_amount || payTarget?.total_amount || 0).toFixed(2)"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Already Paid</p>
                        <p class="text-base font-bold text-emerald-700" x-text="'₹' + Number(payTarget?.paid_amount || 0).toFixed(2)"></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Balance Due</p>
                        <p class="text-base font-bold text-red-600" x-text="'₹' + payBalanceDue().toFixed(2)"></p>
                    </div>
                </x-ui.form-section>

                <template x-for="(pay, pidx) in payForm" :key="pidx">
                    <x-ui.form-section title="Payment Method" gridClass="space-y-3">
                        <div class="flex items-center justify-between">
                            <button x-show="payForm.length > 1" @click="payForm.splice(pidx, 1)"
                                class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <x-ui.select-field label="Method" x-model="pay.payment_method" class="text-sm w-full">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card / Swipe</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                            </x-ui.select-field>
                            <x-ui.input-field label="Amount (₹)" x-model.number="pay.amount" type="number" step="0.01" min="0" class="text-sm w-full" placeholder="0.00" />
                        </div>
                        <div x-show="pay.payment_method === 'upi' || pay.payment_method === 'bank_transfer' || pay.payment_method === 'cheque'">
                            <label class="text-xs text-gray-500 mb-1 block"
                                x-text="pay.payment_method === 'cheque' ? 'Cheque No.' : (pay.payment_method === 'upi' ? 'UPI / IMPS Ref No. *' : 'NEFT / RTGS Ref No.')"></label>
                            <input x-model="pay.transaction_reference" type="text"
                                class="form-input-custom text-sm w-full"
                                :placeholder="pay.payment_method === 'upi' ? 'Enter UPI transaction reference' : (pay.payment_method === 'cheque' ? 'Cheque number' : 'Transaction reference')"
                                :class="pay.payment_method === 'upi' && !pay.transaction_reference ? 'border-amber-400' : ''">
                            <p x-show="pay.payment_method === 'upi' && !pay.transaction_reference"
                                class="text-[10px] text-amber-600 mt-0.5">Required for UPI payments</p>
                        </div>
                    </x-ui.form-section>
                </template>

                <button @click="payForm.push({payment_method:'cash', amount:0, transaction_reference:''})"
                    class="text-xs text-primary-600 hover:underline flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Split Payment (add another method)
                </button>

                <div class="flex justify-between text-sm pt-1 border-t">
                    <span class="text-gray-500">Total Paying Now</span>
                    <span class="font-semibold"
                        :class="payTotalPaying() >= payBalanceDue() ? 'text-emerald-600' : 'text-amber-600'"
                        x-text="'₹' + payTotalPaying().toFixed(2)"></span>
                </div>
                <div x-show="payTotalPaying() > 0 && payTotalPaying() < payBalanceDue()"
                    class="text-xs text-amber-700 bg-amber-50 rounded px-3 py-2 border border-amber-200">
                    Partial payment — ₹<span x-text="(payBalanceDue() - payTotalPaying()).toFixed(2)"></span> will remain outstanding.
                </div>
                <div x-show="payTotalPaying() > payBalanceDue() && payBalanceDue() > 0"
                    class="flex justify-between text-sm text-emerald-700 bg-emerald-50 rounded px-3 py-2 border border-emerald-200">
                    <span>Change to return</span>
                    <span class="font-semibold" x-text="'₹' + (payTotalPaying() - payBalanceDue()).toFixed(2)"></span>
                </div>
            </div>

            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <button type="button" @click="showPayModal = false" class="btn-secondary text-xs w-full sm:w-auto">Cancel</button>
                <button type="button" @click="submitPayment()" class="btn-primary text-xs px-6 w-full sm:w-auto"
                    :disabled="paying || payTotalPaying() <= 0">
                    <span x-show="paying" class="spinner mr-2"></span>
                    <svg x-show="!paying" class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Confirm Payment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function _monthStart() {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth(), 1).toISOString().slice(0, 10);
}
function _monthEnd() {
    const d = new Date();
    return new Date(d.getFullYear(), d.getMonth() + 1, 0).toISOString().slice(0, 10);
}

function invoicesPage() {
    return {
        items: [],
        meta: null,
        page: 1,
        loading: true,
        filter: { search: '', date_from: _monthStart(), date_to: _monthEnd(), payment_status: '' },

        showView: false,
        viewData: null,
        showPayModal: false,
        payTarget: null,
        payForm: [{ payment_method: 'cash', amount: 0, transaction_reference: '' }],
        paying: false,

        get pageStats() {
            return {
                paid:    this.items.filter(i => i.payment_status === 'paid').length,
                partial: this.items.filter(i => i.payment_status === 'partial').length,
                unpaid:  this.items.filter(i => !i.payment_status || i.payment_status === 'unpaid').length,
            };
        },

        async load() {
            this.loading = true;
            const params = new URLSearchParams({ page: this.page, per_page: 20 });
            if (this.filter.search)          params.append('search', this.filter.search);
            if (this.filter.date_from)       params.append('date_from', this.filter.date_from);
            if (this.filter.date_to)         params.append('date_to', this.filter.date_to);
            if (this.filter.payment_status)  params.append('payment_status', this.filter.payment_status);

            const r = await RepairBox.ajax('/admin/invoices?' + params.toString());
            // RepairBox.ajax returns { data: <array>, meta: {current_page, last_page, total}, success }
            // for paginated responses
            if (r.data) {
                this.meta  = r.meta ? { ...r.meta, per_page: 20 } : null;
                this.items = (Array.isArray(r.data) ? r.data : (r.data.data || [])).map(inv => this.enrich(inv));
            }
            this.loading = false;
        },

        enrich(inv) {
            const paid  = (inv.payments || []).reduce((s, p) => s + Number(p.amount), 0);
            const total = Number(inv.final_amount || inv.total_amount || 0);
            inv.paid_amount  = inv.paid_amount  ?? paid;
            inv.balance_due  = inv.balance_due  ?? Math.max(0, total - paid);
            return inv;
        },

        resetFilters() {
            this.filter = { search: '', date_from: _monthStart(), date_to: _monthEnd(), payment_status: '' };
            this.page = 1;
            this.load();
        },

        goPage(p) {
            if (!this.meta || p < 1 || p > this.meta.last_page) return;
            this.page = p;
            this.load();
        },

        pageRange() {
            if (!this.meta) return [];
            const total = this.meta.last_page, cur = this.meta.current_page;
            if (total <= 7) return Array.from({ length: total }, (_, i) => i + 1);
            const pages = [1];
            if (cur > 3) pages.push('…');
            for (let p = Math.max(2, cur - 1); p <= Math.min(total - 1, cur + 1); p++) pages.push(p);
            if (cur < total - 2) pages.push('…');
            pages.push(total);
            return pages;
        },

        async view(inv) {
            const r = await RepairBox.ajax('/admin/invoices/' + inv.id);
            if (r.data) {
                this.viewData = this.enrich(r.data);
                this.showView = true;
            }
        },

        openPayModal(inv) {
            this.payTarget = inv;
            const balance = Number(inv.balance_due ?? (Number(inv.final_amount || inv.total_amount || 0) - Number(inv.paid_amount || 0)));
            this.payForm  = [{ payment_method: 'cash', amount: balance > 0 ? balance : 0, transaction_reference: '' }];
            this.showPayModal = true;
        },

        payTotalPaying() {
            return this.payForm.reduce((s, p) => s + (Number(p.amount) || 0), 0);
        },

        payBalanceDue() {
            if (!this.payTarget) return 0;
            return Number(this.payTarget.balance_due ?? (Number(this.payTarget.final_amount || this.payTarget.total_amount || 0) - Number(this.payTarget.paid_amount || 0)));
        },

        async submitPayment() {
            if (!this.payTarget) return;
            if (this.payTotalPaying() <= 0) { RepairBox.toast('Enter payment amount', 'error'); return; }
            for (const pay of this.payForm) {
                if (pay.payment_method === 'upi' && !String(pay.transaction_reference || '').trim()) {
                    RepairBox.toast('UPI reference number is required', 'error');
                    return;
                }
            }

            this.paying = true;
            const r = await RepairBox.ajax('/admin/invoices/' + this.payTarget.id + '/pay', 'POST', { payments: this.payForm });
            this.paying = false;

            if (r.success !== false && r.data) {
                RepairBox.toast('Payment recorded successfully', 'success');
                this.showPayModal = false;
                const updated = this.enrich(r.data);
                const idx = this.items.findIndex(i => i.id === updated.id);
                if (idx !== -1) this.items[idx] = updated;
                else await this.load();
            }
        },
    };
}
</script>
@endpush
