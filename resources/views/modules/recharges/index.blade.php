@extends('layouts.app')
@section('page-title', 'Recharges')

@section('content')
<div x-data="rechargesPage()" x-init="init()">

    {{-- Top Bar: Customer Search + New Recharge --}}
    <div class="card mb-4" style="overflow:visible">
        <div class="card-body" style="overflow:visible">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                {{-- Customer Search --}}
                <div class="flex-1 relative">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Select Customer</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1" @click.away="custOpen = false">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input x-model="custSearch" @focus="findCust(1)" @input.debounce.300ms="findCust(1)" type="text" class="form-input-custom pl-10" placeholder="Search by name or mobile...">
                        </div>
                        <button type="button" @click="showAddCust = true; newCust = {name:'', mobile_number:'', email:'', address:''}" class="btn-secondary text-sm px-3 whitespace-nowrap">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg> New Customer
                        </button>
                    </div>
                    {{-- Search Results Dropdown --}}
                    <div x-show="custOpen && custResults.length > 0" x-cloak class="absolute left-0 right-0 mt-1 border rounded-lg bg-white shadow-lg overflow-hidden" style="z-index:50">
                        <div class="max-h-48 overflow-y-auto" @scroll="handleCustScroll($event)">
                            <template x-for="c in custResults" :key="c.id">
                                <button @click="selectCustomer(c)" class="w-full text-left px-4 py-2.5 hover:bg-primary-50 text-sm border-b last:border-b-0 flex items-center gap-3 transition-colors">
                                    <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold" x-text="c.name.charAt(0).toUpperCase()"></div>
                                    <div><div class="font-medium text-gray-800" x-text="c.name"></div><div class="text-xs text-gray-400" x-text="c.mobile_number"></div></div>
                                </button>
                            </template>
                            <div x-show="custLoading" class="px-4 py-2.5 text-xs text-gray-400 text-center flex items-center justify-center gap-2"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Loading…</div>
                        </div>
                    </div>
                    <div x-show="custOpen && !custLoading && custResults.length === 0" class="text-xs text-gray-400 mt-1">No customers found.</div>
                </div>
                {{-- Selected Customer Badge --}}
                <div x-show="selCust" class="flex items-center gap-2 bg-primary-50 border border-primary-200 rounded-lg px-3 py-2">
                    <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs font-bold" x-text="selCust?.name?.charAt(0).toUpperCase()"></div>
                    <div>
                        <div class="text-sm font-semibold text-gray-800" x-text="selCust?.name"></div>
                        <div class="text-xs text-gray-500" x-text="selCust?.mobile_number"></div>
                    </div>
                    <button @click="clearCustomer()" class="ml-2 text-gray-400 hover:text-red-500 transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Two Column Layout when customer selected --}}
    <div x-show="!viewItem">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

        {{-- Left: New Recharge Form (only when customer selected) --}}
        <div x-show="selCust" x-cloak class="lg:col-span-1">
            <div class="card sticky top-4">
                <div class="card-header bg-primary-50 border-b border-primary-100">
                    <h3 class="text-base font-semibold text-primary-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Recharge
                    </h3>
                </div>
                <div class="card-body">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Provider *</label>
                            <select x-model="form.provider_id" class="form-select-custom text-sm">
                                <option value="">Select Provider</option>
                                <template x-for="p in providers" :key="p.id">
                                    <option :value="p.id" x-text="p.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Mobile Number *</label>
                            <input x-model="form.mobile_number" type="text" class="form-input-custom text-sm" placeholder="Enter mobile number">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Amount *</label>
                            <input x-model="form.recharge_amount" type="number" step="0.01" class="form-input-custom text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method</label>
                            <div class="flex gap-2">
                                <template x-for="m in ['cash','upi','card']" :key="m">
                                    <button type="button" @click="form.payment_method = m" class="flex-1 py-2 px-3 text-xs font-medium rounded-lg border-2 transition-all flex items-center justify-center gap-1.5" :class="form.payment_method === m ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                        <template x-if="m === 'cash'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                        <template x-if="m === 'upi'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                        <template x-if="m === 'card'"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                        <span x-text="m.toUpperCase()"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div x-show="form.payment_method === 'upi' || form.payment_method === 'card'" x-cloak>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Reference No.</label>
                            <input x-model="form.transaction_id" type="text" class="form-input-custom text-sm" placeholder="UTR / Transaction ID">
                        </div>
                        <button @click="save()" class="btn-primary w-full mt-2" :disabled="saving">
                            <span x-show="saving" class="spinner mr-1"></span>
                            <svg x-show="!saving" class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Submit Recharge
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Recharge History --}}
        <div :class="selCust ? 'lg:col-span-2' : 'lg:col-span-3'">
            {{-- Stats Cards (when customer selected) --}}
            <div x-show="selCust" x-cloak class="grid grid-cols-2 gap-3 mb-4">
                <div class="card">
                    <div class="card-body py-3 px-4 text-center">
                        <div class="text-2xl font-bold text-primary-600" x-text="custStats.totalRecharges"></div>
                        <div class="text-xs text-gray-500">Total Recharges</div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body py-3 px-4 text-center">
                        <div class="text-2xl font-bold text-blue-600" x-text="'₹' + Number(custStats.totalAmount).toFixed(0)"></div>
                        <div class="text-xs text-gray-500">Total Amount</div>
                    </div>
                </div>
            </div>

            {{-- History Table --}}
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-700" x-text="selCust ? selCust.name + '\'s Recharge History' : 'All Recharges'"></h3>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <input x-model="dateFrom" @change="loadHistory(1)" type="date" class="form-input-custom text-sm w-36" title="From date">
                            <span class="text-gray-400 text-xs">to</span>
                            <input x-model="dateTo" @change="loadHistory(1)" type="date" class="form-input-custom text-sm w-36" title="To date">
                            <button x-show="dateFrom || dateTo" @click="dateFrom = ''; dateTo = ''; loadHistory(1)" class="text-xs text-red-500 hover:text-red-700 whitespace-nowrap" title="Clear dates">&times; Clear</button>
                        </div>
                        <div class="relative">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input x-model="tableSearch" @input.debounce.400ms="loadHistory(1)" type="text" class="form-input-custom text-sm pl-10 w-56" placeholder="Search by number or name...">
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th x-show="!selCust">Customer</th>
                                    <th>Provider</th>
                                    <th>Number</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Ref No.</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(r, i) in items" :key="r.id">
                                    <tr class="hover:bg-primary-50 transition-colors cursor-pointer" @click="viewDetail(r)">
                                        <td class="text-gray-400 text-xs" x-text="(pagination.from || 0) + i"></td>
                                        <td x-show="!selCust">
                                            <div class="flex items-center gap-2">
                                                <button @click.stop="selectCustomer(r.customer)" x-show="r.customer" class="flex items-center gap-2 hover:text-primary-600 transition-colors group" title="Filter by this customer">
                                                    <div class="w-6 h-6 bg-gray-100 text-gray-500 group-hover:bg-primary-100 group-hover:text-primary-600 rounded-full flex items-center justify-center text-xs font-bold" x-text="r.customer ? r.customer.name.charAt(0).toUpperCase() : '?'"></div>
                                                    <span class="text-sm font-medium" x-text="r.customer ? r.customer.name : '-'"></span>
                                                </button>
                                                <span x-show="!r.customer" class="text-gray-400 text-sm">Walk-in</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="inline-flex items-center gap-2 text-sm" x-show="r.provider">
                                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold text-white" :style="'background:' + providerColor(r.provider?.name)" x-text="r.provider?.name?.charAt(0).toUpperCase()"></span>
                                                <span x-text="r.provider.name"></span>
                                            </span>
                                            <span x-show="!r.provider" class="text-gray-400">-</span>
                                        </td>
                                        <td class="font-mono text-sm" x-text="r.mobile_number"></td>
                                        <td class="font-semibold" x-text="'₹' + Number(r.recharge_amount).toFixed(2)"></td>
                                        <td>
                                            <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-1 rounded-full" :class="{
                                                'bg-green-100 text-green-700': r.payment_method === 'cash',
                                                'bg-blue-100 text-blue-700': r.payment_method === 'upi',
                                                'bg-purple-100 text-purple-700': r.payment_method === 'card',
                                                'bg-gray-100 text-gray-700': r.payment_method === 'bank_transfer'
                                            }">
                                                <template x-if="r.payment_method === 'cash'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                                <template x-if="r.payment_method === 'upi'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                                <template x-if="r.payment_method === 'card'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                                <template x-if="r.payment_method === 'bank_transfer'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                                <span x-text="r.payment_method === 'bank_transfer' ? 'Bank' : r.payment_method ? r.payment_method.toUpperCase() : '-'"></span>
                                            </span>
                                        </td>
                                        <td class="text-xs text-gray-400 font-mono" x-text="r.transaction_id || '—'"></td>
                                        <td>
                                            <span class="badge" :class="r.status === 'success' ? 'badge-success' : r.status === 'failed' ? 'badge-danger' : 'badge-warning'" x-text="r.status"></span>
                                        </td>
                                        <td class="text-xs text-gray-500" x-text="formatDate(r.created_at)"></td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td :colspan="selCust ? 8 : 9" class="text-center py-10">
                                        <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <div class="text-gray-400 text-sm" x-text="selCust ? 'No recharges found for this customer' : 'No recharges found'"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div x-show="pagination.lastPage > 1" class="flex items-center justify-between px-4 py-3 border-t">
                        <div class="text-xs text-gray-500">
                            Showing <span x-text="pagination.from"></span>-<span x-text="pagination.to"></span> of <span x-text="pagination.total"></span>
                        </div>
                        <div class="flex gap-1">
                            <button @click="loadHistory(pagination.currentPage - 1)" :disabled="pagination.currentPage <= 1" class="px-3 py-1 text-xs rounded border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                            <template x-for="pg in paginationPages()" :key="pg">
                                <button @click="if(pg !== '...') loadHistory(pg)" class="px-3 py-1 text-xs rounded border" :class="pg === pagination.currentPage ? 'bg-primary-500 text-white border-primary-500' : pg === '...' ? 'border-transparent cursor-default' : 'hover:bg-gray-50'" x-text="pg"></button>
                            </template>
                            <button @click="loadHistory(pagination.currentPage + 1)" :disabled="pagination.currentPage >= pagination.lastPage" class="px-3 py-1 text-xs rounded border hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>{{-- /!viewItem --}}

    {{-- Recharge Detail View --}}
    <div x-show="viewItem" x-cloak>
        <div class="card">
            <div class="card-header flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="closeDetail()" class="flex items-center gap-1.5 text-gray-500 hover:text-gray-800 transition-colors text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        Back to Recharges
                    </button>
                    <span class="text-gray-300">|</span>
                    <h3 class="text-base font-semibold text-gray-700">Recharge Detail</h3>
                </div>
                <span class="badge" :class="viewItem?.status === 'success' ? 'badge-success' : viewItem?.status === 'failed' ? 'badge-danger' : 'badge-warning'" x-text="viewItem?.status || ''"></span>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Customer</div>
                        <div class="font-semibold text-gray-800" x-text="viewItem?.customer?.name || 'Walk-in'"></div>
                        <div class="text-xs text-gray-400 mt-0.5" x-text="viewItem?.customer?.mobile_number || ''"></div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Mobile Number</div>
                        <div class="font-mono font-bold text-gray-800 text-lg" x-text="viewItem?.mobile_number || '—'"></div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Provider</div>
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white flex-shrink-0" :style="'background:' + providerColor(viewItem?.provider?.name)" x-text="viewItem?.provider?.name?.charAt(0).toUpperCase() || '?'"></span>
                            <span class="font-semibold text-gray-800" x-text="viewItem?.provider?.name || '—'"></span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Amount</div>
                        <div class="font-bold text-2xl text-primary-600" x-text="'₹' + Number(viewItem?.recharge_amount || 0).toFixed(2)"></div>
                    </div>
                    <div x-show="viewItem?.plan_name">
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Plan</div>
                        <div class="font-medium text-gray-700" x-text="viewItem?.plan_name || '—'"></div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Payment Method</div>
                        <div class="inline-flex items-center gap-1.5 text-sm font-semibold px-3 py-1 rounded-full" :class="{
                            'bg-green-100 text-green-700': viewItem?.payment_method === 'cash',
                            'bg-blue-100 text-blue-700': viewItem?.payment_method === 'upi',
                            'bg-purple-100 text-purple-700': viewItem?.payment_method === 'card',
                        }" x-text="viewItem?.payment_method ? viewItem.payment_method.toUpperCase() : '—'"></div>
                    </div>
                    <div x-show="viewItem?.transaction_id">
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Reference No.</div>
                        <div class="font-mono text-sm font-semibold text-gray-700" x-text="viewItem?.transaction_id || '—'"></div>
                    </div>
                    <div>
                        <div class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-1">Date &amp; Time</div>
                        <div class="text-sm text-gray-700" x-text="viewItem?.created_at ? formatDate(viewItem.created_at) : '—'"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Customer Modal --}}
    <div x-show="showAddCust" class="modal-overlay" x-cloak @click.self="showAddCust = false">
        <div class="modal-container max-w-md">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Customer</h3><button @click="showAddCust = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="newCust.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label><input x-model="newCust.mobile_number" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="newCust.email" type="email" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input x-model="newCust.address" type="text" class="form-input-custom"></div>
                </div>
            </div>
            <div class="modal-footer"><button type="button" @click="showAddCust = false" class="btn-secondary">Cancel</button><button type="button" @click.prevent="saveNewCust()" class="btn-primary">Save & Select</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rechargesPage() {
    return {
        items: [], providers: [], saving: false,
        custSearch: '', custResults: [], custOpen: false, custHasMore: false, custPage: 1, custLoading: false, selCust: null,
        showAddCust: false, newCust: {name: '', mobile_number: '', email: '', address: ''},
        tableSearch: '', dateFrom: '', dateTo: '',
        viewItem: null,
        form: { customer_id: null, provider_id: '', mobile_number: '', recharge_amount: '', transaction_id: '', payment_method: 'cash' },
        custStats: { totalRecharges: 0, totalAmount: 0 },
        pagination: { currentPage: 1, lastPage: 1, from: 0, to: 0, total: 0 },

        async init() {
            const p = new URLSearchParams(window.location.search);
            const now = new Date();
            const y = now.getFullYear(), mo = String(now.getMonth() + 1).padStart(2, '0');
            this.dateFrom = p.get('date_from') || (y + '-' + mo + '-01');
            this.dateTo   = p.get('date_to')   || now.toISOString().split('T')[0];
            if (p.has('search')) this.tableSearch = p.get('search');
            const r = await RepairBox.ajax('/recharge-providers');
            if (r.data) this.providers = r.data;
            if (p.has('customer_id')) await this.loadCustomerById(p.get('customer_id'));
            await this.loadHistory(1);
            if (p.has('view')) this.loadDetailById(p.get('view'));
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
            // persist in URL
            const params = new URLSearchParams(window.location.search);
            params.set('customer_id', c.id);
            history.replaceState(null, '', window.location.pathname + '?' + params.toString());
            this.loadHistory(1);
        },

        clearCustomer() {
            this.selCust = null;
            this.form.customer_id = null;
            this.form.mobile_number = '';
            this.custStats = { totalRecharges: 0, totalAmount: 0 };
            const params = new URLSearchParams(window.location.search);
            params.delete('customer_id');
            history.replaceState(null, '', window.location.pathname + (params.toString() ? '?' + params.toString() : ''));
            this.loadHistory(1);
        },

        updateUrl() {
            const params = new URLSearchParams(window.location.search);
            params.delete('search'); params.delete('date_from'); params.delete('date_to');
            if (this.tableSearch) params.set('search', this.tableSearch);
            if (this.dateFrom) params.set('date_from', this.dateFrom);
            if (this.dateTo) params.set('date_to', this.dateTo);
            if (this.selCust) params.set('customer_id', this.selCust.id); else params.delete('customer_id');
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },

        async loadHistory(page) {
            if (page < 1) return;
            let url = '/recharges?page=' + page + '&per_page=15';
            if (this.selCust) url += '&customer_id=' + this.selCust.id;
            if (this.tableSearch) url += '&search=' + encodeURIComponent(this.tableSearch);
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
                if (this.selCust) {
                    let statsUrl = '/recharges?customer_id=' + this.selCust.id + '&per_page=9999';
                    if (this.dateFrom) statsUrl += '&date_from=' + this.dateFrom;
                    if (this.dateTo) statsUrl += '&date_to=' + this.dateTo;
                    const all = await RepairBox.ajax(statsUrl);
                    if (all.data) {
                        this.custStats.totalRecharges = all.data.length;
                        this.custStats.totalAmount = all.data.reduce((s, r) => s + parseFloat(r.recharge_amount || 0), 0);
                    }
                }
                this.updateUrl();
            }
        },

        paginationPages() {
            const pages = [];
            const cur = this.pagination.currentPage;
            const last = this.pagination.lastPage;
            if (last <= 7) { for (let i = 1; i <= last; i++) pages.push(i); return pages; }
            pages.push(1);
            if (cur > 3) pages.push('...');
            for (let i = Math.max(2, cur - 1); i <= Math.min(last - 1, cur + 1); i++) pages.push(i);
            if (cur < last - 2) pages.push('...');
            pages.push(last);
            return pages;
        },

        formatDate(d) {
            const dt = new Date(d);
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }) + ' ' + dt.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit' });
        },

        async saveNewCust() {
            if (!this.newCust.name || !this.newCust.mobile_number) { RepairBox.toast('Name and mobile are required', 'error'); return; }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCust);
            if (r.success !== false && r.data) {
                this.showAddCust = false;
                this.selectCustomer(r.data);
                RepairBox.toast('Customer added', 'success');
            }
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
                this.form = { customer_id: this.selCust?.id, provider_id: '', mobile_number: this.selCust?.mobile_number || '', recharge_amount: '', transaction_id: '', payment_method: 'cash' };
                this.loadHistory(1);
            }
        },

        viewDetail(r) {
            this.viewItem = r;
            const params = new URLSearchParams(window.location.search);
            params.set('view', r.id);
            history.pushState(null, '', window.location.pathname + '?' + params.toString());
        },

        closeDetail() {
            this.viewItem = null;
            const params = new URLSearchParams(window.location.search);
            params.delete('view');
            const qs = params.toString();
            history.pushState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },

        async loadDetailById(id) {
            const r = await RepairBox.ajax('/recharges/' + id);
            if (r.data) this.viewItem = r.data;
            else if (r.id) this.viewItem = r;
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
