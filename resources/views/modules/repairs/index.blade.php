@extends('layouts.app')
@section('page-title', 'Repairs')

@section('content')
<div x-data="repairsPage()" x-init="init()" class="h-full">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">

        {{-- LEFT: Repair Queue --}}
        <div class="lg:col-span-2 flex flex-col">

            {{-- Search + view toggle --}}
            <div class="mb-3 flex flex-col sm:flex-row gap-2 sm:items-center">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="searchQuery" @input.debounce.400ms="load()" type="text" class="form-input-custom pl-10 w-full" placeholder="Search ticket, customer, device, IMEI..." autofocus>
                    <button x-show="searchQuery" @click="searchQuery = ''; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="inline-flex rounded-lg border border-gray-200 bg-gray-50 p-1 w-full sm:w-auto">
                    <button @click="viewMode = 'table'; updateUrl()" class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all" :class="viewMode === 'table' ? 'bg-white text-primary-700 shadow-sm border-primary-200' : 'text-gray-600 hover:text-gray-800'" title="Table View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </button>
                    <button @click="viewMode = 'kanban'; updateUrl()" class="px-3 py-1.5 text-sm font-medium rounded-md border border-transparent transition-all" :class="viewMode === 'kanban' ? 'bg-white text-primary-700 shadow-sm border-primary-200' : 'text-gray-600 hover:text-gray-800'" title="Kanban Board">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="mb-3 flex flex-wrap gap-2 items-center">
                {{-- Status dropdown --}}
                <div class="relative" x-data="{ statusOpen: false }" @click.away="statusOpen = false">
                    <button type="button" @click="statusOpen = !statusOpen"
                        :class="selectedStatuses.length ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="flex items-center gap-1.5 text-sm h-9 pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        <span x-text="statusSummaryLabel()"></span>
                        <span x-show="selectedStatuses.length" class="ml-0.5 bg-primary-600 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1" x-text="selectedStatuses.length"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="statusOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl w-64 z-50 p-1.5">
                        <button type="button" @click="clearStatusSelection()" class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-sm transition-colors" :class="selectedStatuses.length === 0 ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50'">
                            <span class="font-medium">All Statuses</span>
                            <span class="text-xs font-bold" x-text="items.length"></span>
                        </button>
                        <template x-for="option in statusFilterOptions" :key="option.key">
                            <button type="button" @click="toggleStatusSelection(option.key)" class="flex w-full items-center justify-between rounded-xl px-3.5 py-2.5 text-left text-sm transition-colors" :class="isStatusSelected(option.key) ? option.activeCardClass : 'text-slate-700 hover:bg-slate-50'">
                                <span class="flex items-center gap-2 font-medium">
                                    <span class="inline-block h-2 w-2 rounded-full" :class="statusDotClass(option.key)"></span>
                                    <span x-text="option.label"></span>
                                </span>
                                <span class="text-xs font-bold" x-text="statusCount(option.key)"></span>
                            </button>
                        </template>
                        <div x-show="selectedStatuses.length" class="mt-1 border-t border-slate-100 pt-1">
                            <button type="button" @click="clearStatusSelection(); statusOpen = false" class="flex w-full items-center justify-center rounded-xl px-3.5 py-2 text-xs font-medium text-red-600 transition-colors hover:bg-red-50">Clear statuses</button>
                        </div>
                    </div>
                </div>

                {{-- Date From --}}
                <input x-model="dateFrom" @change="load()" type="date" class="text-sm h-9 pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="From date">

                {{-- Date To --}}
                <input x-model="dateTo" @change="load()" type="date" class="text-sm h-9 pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="To date">

                {{-- Payment filter --}}
                <select x-model="paymentFilter" @change="load()" class="text-sm h-9 pl-3 pr-8 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer">
                    <option value="">All Payments</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>

                {{-- Clear all filters --}}
                <button x-show="searchQuery || selectedStatuses.length || dateFrom || dateTo || paymentFilter"
                    @click="resetFilters()"
                    class="flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold px-3 h-9 rounded-lg border border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>

            {{-- Table view --}}
            <div x-show="viewMode === 'table'" class="card relative flex-1 flex flex-col" style="z-index:0;">
                <div class="card-header py-2 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Repair Queue (<span x-text="filteredItems.length"></span>)
                    </h3>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[50vh] lg:max-h-[60vh]">
                    <table class="data-table w-full">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Ticket</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Device</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                                <th class="px-3 py-2.5 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="r in paginatedItems" :key="r.id">
                                <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="window.location.href = '/repairs/' + r.id">
                                    <td class="px-3 py-2.5">
                                        <span class="font-semibold text-primary-600 text-sm" x-text="r.ticket_number"></span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="font-medium text-gray-800 text-sm" x-text="r.customer ? r.customer.name : '-'"></div>
                                        <div class="text-xs text-gray-400" x-text="r.customer ? r.customer.mobile_number : ''"></div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="text-sm font-medium text-gray-800" x-text="deviceLabel(r)"></div>
                                        <div class="text-xs text-gray-400" x-show="r.imei" x-text="'IMEI: ' + r.imei"></div>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold" :class="statusBadgeClass(r.status)" x-text="statusLabel(r.status)"></span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <div class="text-sm font-medium" x-text="'₹' + Number(r.grand_total || r.estimated_cost || 0).toFixed(2)"></div>
                                        <div class="text-xs" :class="r.balance_due > 0 ? 'text-red-500' : 'text-green-500'" x-text="amountMeta(r)"></div>
                                    </td>
                                    <td class="px-3 py-2.5 text-center" @click.stop>
                                        <div class="inline-flex items-center gap-1">
                                            <a :href="'/repairs/' + r.id" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="View">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a :href="'/repairs/' + r.id + '/print'" target="_blank" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 transition" title="Print" @click.stop>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="paginatedItems.length === 0 && !loading">
                                <td colspan="6" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <p class="text-gray-400 font-medium">No repairs found</p>
                                    <p class="text-gray-300 text-sm mt-1">Create a new repair from the right panel</p>
                                </td>
                            </tr>
                            <template x-if="loading">
                                <template x-for="i in 8" :key="'sk'+i">
                                    <tr>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-28"></div></td>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-24"></div></td>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2.5"><div class="skeleton h-3 w-14"></div></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div x-show="totalPages > 1" class="border-t px-4 py-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">
                        <span x-text="((currentPage - 1) * perPage) + 1"></span>-<span x-text="Math.min(currentPage * perPage, filteredItems.length)"></span> of <span x-text="filteredItems.length"></span>
                    </span>
                    <div class="flex items-center gap-1">
                        <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                        <template x-for="p in visiblePages" :key="'p'+p">
                            <button @click="if(p !== '...') currentPage = p" class="px-2.5 py-1 text-sm border rounded-lg" :class="p === currentPage ? 'bg-primary-600 text-white border-primary-600' : (p === '...' ? 'cursor-default border-transparent' : 'hover:bg-white')" x-text="p"></button>
                        </template>
                        <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-2.5 py-1 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
                    </div>
                </div>
            </div>

            {{-- Kanban view --}}
            <div x-show="viewMode === 'kanban'" x-cloak class="card overflow-hidden flex-1">
                <div class="card-header py-2 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">Repair Board</h3>
                </div>
                <div class="px-4 pb-4 pt-2">
                    <div class="overflow-x-auto overflow-y-hidden pb-2">
                        <div class="flex items-start gap-3 min-w-max">
                            <template x-for="[colKey, colMeta] of kanbanColumns" :key="colKey">
                                <div class="w-64 flex-shrink-0">
                                    <div class="rounded-t-xl px-3 py-2 flex items-center justify-between" :class="kanbanHeaderClass(colKey)">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-sm" x-text="colMeta.label"></span>
                                            <span class="text-xs bg-white/30 px-2 py-0.5 rounded-full font-medium" x-text="kanbanItems(colKey).length"></span>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 rounded-b-xl p-2 min-h-[200px] max-h-[50vh] lg:max-h-[calc(100vh-320px)] overflow-y-auto space-y-2">
                                        <template x-for="r in kanbanItems(colKey)" :key="r.id">
                                            <a :href="'/repairs/' + r.id" class="block bg-white rounded-lg border border-gray-200 p-3 shadow-sm hover:shadow-md hover:border-primary-300 transition-all cursor-pointer group">
                                                <div class="flex items-start justify-between mb-2">
                                                    <span class="text-xs font-bold text-primary-600" x-text="r.ticket_number"></span>
                                                </div>
                                                <div class="text-sm font-medium text-gray-800 mb-1" x-text="r.customer ? r.customer.name : 'Walk-in'"></div>
                                                <div class="text-xs text-gray-500 mb-2" x-text="(r.device_brand || '') + ' ' + (r.device_model || '')"></div>
                                                <div class="flex items-center justify-between">
                                                    <span class="text-xs font-semibold" :class="r.balance_due > 0 ? 'text-red-500' : 'text-green-600'" x-text="'₹' + Number(r.grand_total || r.estimated_cost || 0).toFixed(0)"></span>
                                                    <span class="text-[10px] text-gray-400" x-text="formatDate(r.created_at)"></span>
                                                </div>
                                            </a>
                                        </template>
                                        <div x-show="kanbanItems(colKey).length === 0" class="text-center py-8">
                                            <p class="text-xs text-gray-400">No repairs</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Create Repair (like POS cart) --}}
        <div class="relative flex flex-col gap-3 order-first lg:order-none" :style="custOpen ? 'z-index:95;' : 'z-index:0;'">

            {{-- Customer selector --}}
            <div class="card relative" :style="custOpen ? 'overflow:visible; z-index:110;' : 'overflow:visible; z-index:10;'">
                <div class="card-body py-3" style="overflow:visible">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Customer</label>
                            <p class="mt-1 text-xs text-gray-400" x-show="!selectedCust">Search and attach a customer to the repair ticket.</p>
                            <p class="mt-1 text-xs text-emerald-600" x-show="selectedCust">Customer selected for this repair.</p>
                        </div>
                        <button type="button" @click="openAddCustModal()"
                            class="btn-primary text-sm px-3 py-2 whitespace-nowrap w-auto">+ New</button>
                    </div>

                    <div x-show="!selectedCust" x-cloak class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-end">
                        <div class="flex-1 relative" @click.away="custOpen = false">
                            <input x-model="custSearch" @focus="searchCustomers(1)" @input.debounce.300ms="searchCustomers(1)" type="text"
                                class="form-input-custom text-sm" placeholder="Search by name / phone...">
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

                    <div x-show="custOpen && !custLoading && custResults.length === 0 && !selectedCust"
                        class="text-xs text-gray-400 mt-2">No customers found - click <strong>+ New</strong> to add.</div>

                    <div x-show="selectedCust" x-cloak class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50/70 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-emerald-900 truncate" x-text="selectedCust?.name"></div>
                                <div class="mt-1 text-xs text-emerald-700" x-text="selectedCust?.mobile_number || 'No mobile'"></div>
                                <div class="mt-1 text-xs text-emerald-600 break-all" x-show="selectedCust?.email" x-text="selectedCust?.email"></div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <button type="button" @click="clearCustomer(); $nextTick(() => searchCustomers(1))" class="inline-flex items-center rounded-lg border border-emerald-200 bg-white px-3 py-1.5 text-xs font-semibold text-emerald-700 transition hover:border-emerald-300 hover:bg-emerald-100">Change</button>
                                <button type="button" @click="clearCustomer()" class="inline-flex items-center rounded-lg px-2 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50 hover:text-red-600">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Repair Details Form --}}
            <div class="card relative flex-1 flex flex-col" style="z-index:0;">
                <div class="card-header py-2 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Repair Details
                    </h3>
                    <button x-show="hasFormData()" @click="resetForm()" class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[34vh] lg:max-h-[38vh]">
                    <div class="px-4 py-3 space-y-3">
                        {{-- Device info --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Device Brand *</label>
                                <input x-model="form.device_brand" list="brand-list" type="text" class="form-input-custom text-sm" placeholder="Samsung, Apple..." autocomplete="off">
                            </div>
                            <div>
                                <label class="text-xs font-medium text-gray-600 mb-1 block">Device Model *</label>
                                <input x-model="form.device_model" type="text" class="form-input-custom text-sm" placeholder="Galaxy S24, iPhone 15...">
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">IMEI / Serial No.</label>
                            <input x-model="form.imei" type="text" class="form-input-custom text-sm" placeholder="Optional IMEI or serial">
                        </div>

                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Problem Description *</label>
                            <textarea x-model="form.problem_description" class="form-input-custom text-sm resize-none" rows="3" placeholder="Describe the issue: display broken, charging issue, etc."></textarea>
                        </div>

                        {{-- Optional details (collapsible) --}}
                        <div class="border-t border-gray-100 pt-2">
                            <button type="button" @click="optionalOpen = !optionalOpen" class="flex w-full items-center justify-between text-left text-xs font-semibold text-gray-500 hover:text-gray-700">
                                <span>Optional Details</span>
                                <svg class="w-3.5 h-3.5 transition-transform" :class="optionalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="optionalOpen" x-cloak x-collapse class="mt-2 space-y-2">
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Delivery Date</label>
                                        <input x-model="form.expected_delivery_date" type="date" class="form-input-custom text-sm">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Estimated Cost</label>
                                        <input x-model="form.estimated_cost" type="number" step="0.01" class="form-input-custom text-sm" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Advance Amount</label>
                                        <input x-model="form.advance_amount" type="number" step="0.01" class="form-input-custom text-sm" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium text-gray-600 mb-1 block">Payment Method</label>
                                        <select x-model="form.advance_method" class="form-select-custom text-sm">
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="upi">UPI</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-gray-600 mb-1 block">Reference / Txn ID</label>
                                    <input x-model="form.advance_reference" type="text" class="form-input-custom text-sm" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="border-t px-4 py-3 space-y-1.5 text-sm bg-gray-50/50">
                    <div class="flex justify-between text-gray-600">
                        <span>Estimated Cost</span>
                        <span x-text="'₹' + Number(form.estimated_cost || 0).toFixed(2)"></span>
                    </div>
                    <div x-show="form.advance_amount > 0" class="flex justify-between text-gray-600">
                        <span>Advance</span>
                        <span class="text-emerald-600" x-text="'- ₹' + Number(form.advance_amount || 0).toFixed(2)"></span>
                    </div>
                    <div x-show="form.advance_amount > 0" class="flex justify-between font-bold text-base pt-1.5 border-t border-gray-200">
                        <span>Balance</span>
                        <span class="text-primary-600 text-lg" x-text="'₹' + Math.max(0, Number(form.estimated_cost || 0) - Number(form.advance_amount || 0)).toFixed(2)"></span>
                    </div>
                </div>

                {{-- Create button --}}
                <div class="border-t px-4 py-3">
                    <button @click="saveRepair()"
                        class="btn-primary w-full py-3 text-base font-semibold"
                        :disabled="saving || !canCreate()">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Create Repair Ticket
                    </button>
                </div>
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
                    <input x-model="newCust.name" type="text" class="form-input-custom" placeholder="Full name">
                    <p x-show="customerFormTried && !newCust.name.trim()" class="text-xs text-red-500 mt-1">Name is required</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile * <span class="text-xs text-gray-500">(10 digits)</span></label>
                    <input x-model="newCust.mobile_number" type="text" class="form-input-custom" placeholder="10-digit mobile number"
                        inputmode="numeric" pattern="[0-9]{10}" maxlength="10"
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
                <button type="button" @click.prevent="saveNewCust()" class="btn-primary" :disabled="customerSaving">
                    <span x-show="!customerSaving">Save &amp; Select</span>
                    <span x-show="customerSaving">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccess" x-cloak class="modal-overlay">
        <div class="modal-container max-w-sm text-center" @click.stop>
            <div class="modal-body py-8 flex flex-col items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Repair Created!</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Ticket <span class="font-semibold text-primary-600" x-text="'#' + (createdRepair ? createdRepair.ticket_number : '')"></span>
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap justify-center">
                    <a :href="'/repairs/' + (createdRepair ? createdRepair.id : '') + '/print'" target="_blank" class="btn-secondary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Ticket
                    </a>
                    <a :href="'/repairs/' + (createdRepair ? createdRepair.id : '')" class="btn-secondary text-sm px-4">View Details</a>
                    <button @click="newRepair()" class="btn-primary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Repair
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function repairsPage() {
    return {
        // Queue state
        items: [],
        loading: true,
        viewMode: 'table',
        currentPage: 1,
        perPage: 15,
        searchQuery: '',
        selectedStatuses: [],
        dateFrom: '',
        dateTo: '',
        paymentFilter: '',

        statusMeta: @json($statusMeta),

        // Create form state
        saving: false,
        optionalOpen: false,
        showSuccess: false,
        createdRepair: null,

        custSearch: '',
        custResults: [],
        custOpen: false,
        custHasMore: false,
        custPage: 1,
        custLoading: false,
        selectedCust: null,

        showAddCust: false,
        customerFormTried: false,
        customerSaving: false,
        customerSubmitError: '',
        newCust: { name: '', mobile_number: '', email: '', address: '' },

        brandList: @json($brands),

        form: {
            customer_id: null,
            device_brand: '',
            device_model: '',
            imei: '',
            problem_description: '',
            estimated_cost: '',
            expected_delivery_date: '',
            advance_amount: '',
            advance_method: 'cash',
            advance_reference: '',
        },

        // Computed
        get statusFilterOptions() {
            return Object.entries(this.statusMeta).map(([key, meta]) => ({
                key,
                label: meta.label,
                activeCardClass: this.statusCardActiveClass(key),
            }));
        },
        get filteredItems() {
            let items = this.items;
            if (this.selectedStatuses.length) {
                items = items.filter(i => this.selectedStatuses.includes(i.status));
            }
            return items;
        },
        get paginatedItems() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredItems.slice(start, start + this.perPage);
        },
        get totalPages() {
            return Math.max(1, Math.ceil(this.filteredItems.length / this.perPage));
        },
        get visiblePages() {
            const pages = [], total = this.totalPages, current = this.currentPage;
            if (total <= 7) { for (let i = 1; i <= total; i++) pages.push(i); }
            else {
                pages.push(1);
                if (current > 3) pages.push('...');
                for (let i = Math.max(2, current - 1); i <= Math.min(total - 1, current + 1); i++) pages.push(i);
                if (current < total - 2) pages.push('...');
                pages.push(total);
            }
            return pages;
        },
        get kanbanColumns() { return Object.entries(this.statusMeta); },

        // Init
        async init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.searchQuery = p.get('search');
            if (p.has('status')) this.selectedStatuses = p.get('status').split(',').filter(Boolean);
            if (p.has('from')) this.dateFrom = p.get('from');
            if (p.has('to')) this.dateTo = p.get('to');
            if (p.has('payment')) this.paymentFilter = p.get('payment');
            if (p.has('view')) this.viewMode = p.get('view');
            await this.load();
        },

        updateUrl() {
            const params = new URLSearchParams();
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.selectedStatuses.length) params.set('status', this.selectedStatuses.join(','));
            if (this.dateFrom) params.set('from', this.dateFrom);
            if (this.dateTo) params.set('to', this.dateTo);
            if (this.paymentFilter) params.set('payment', this.paymentFilter);
            if (this.viewMode !== 'table') params.set('view', this.viewMode);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },

        async load() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.dateFrom) params.set('date_from', this.dateFrom);
            if (this.dateTo) params.set('date_to', this.dateTo);
            if (this.paymentFilter) params.set('payment_status', this.paymentFilter);
            params.set('per_page', '500');
            const r = await RepairBox.ajax('/repairs?' + params.toString());
            if (r.data) this.items = Array.isArray(r.data) ? r.data : (r.data.data || r.data);
            this.currentPage = 1;
            this.loading = false;
            this.updateUrl();
        },

        // Status helpers
        toggleStatusSelection(status) {
            this.selectedStatuses = this.selectedStatuses.includes(status)
                ? this.selectedStatuses.filter(v => v !== status)
                : [...this.selectedStatuses, status];
            this.currentPage = 1;
            this.updateUrl();
        },
        clearStatusSelection() {
            this.selectedStatuses = [];
            this.currentPage = 1;
            this.updateUrl();
        },
        resetFilters() {
            this.searchQuery = '';
            this.selectedStatuses = [];
            this.dateFrom = '';
            this.dateTo = '';
            this.paymentFilter = '';
            this.currentPage = 1;
            this.load();
        },
        isStatusSelected(s) { return this.selectedStatuses.includes(s); },
        statusSummaryLabel() {
            if (!this.selectedStatuses.length) return 'All Statuses';
            if (this.selectedStatuses.length === 1) return this.statusLabel(this.selectedStatuses[0]);
            return this.selectedStatuses.length + ' statuses';
        },
        statusCount(s) { return this.items.filter(i => i.status === s).length; },
        statusLabel(s) { return this.statusMeta[s]?.label || s?.replace('_', ' ') || ''; },
        statusBadgeClass(s) {
            const m = { received: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700', payment: 'bg-purple-100 text-purple-700', closed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-700' };
            return m[s] || 'bg-gray-100 text-gray-700';
        },
        statusCardActiveClass(s) {
            const m = { received: 'border-blue-600 bg-blue-600 text-white', in_progress: 'border-amber-500 bg-amber-500 text-white', completed: 'border-emerald-600 bg-emerald-600 text-white', payment: 'border-purple-600 bg-purple-600 text-white', closed: 'border-green-700 bg-green-700 text-white', cancelled: 'border-red-600 bg-red-600 text-white' };
            return m[s] || 'border-slate-900 bg-slate-900 text-white';
        },
        statusDotClass(s) {
            const m = { received: 'bg-blue-500', in_progress: 'bg-amber-500', completed: 'bg-emerald-500', payment: 'bg-purple-500', closed: 'bg-green-600', cancelled: 'bg-red-500' };
            return m[s] || 'bg-slate-400';
        },
        kanbanHeaderClass(s) {
            const m = { received: 'bg-blue-500 text-white', in_progress: 'bg-amber-500 text-white', completed: 'bg-emerald-500 text-white', payment: 'bg-purple-500 text-white', closed: 'bg-green-700 text-white', cancelled: 'bg-red-500 text-white' };
            return m[s] || 'bg-gray-500 text-white';
        },
        kanbanItems(s) { return this.items.filter(i => i.status === s); },
        deviceLabel(r) { return [r.device_brand, r.device_model].filter(Boolean).join(' ') || 'Device not set'; },
        amountMeta(r) {
            if (r.status === 'cancelled' && Number(r.total_refunded || 0) > 0) return 'Refunded: ₹' + Number(r.total_refunded).toFixed(2);
            if (Number(r.balance_due || 0) > 0) return 'Due: ₹' + Number(r.balance_due).toFixed(2);
            return Number(r.grand_total || r.estimated_cost || 0) > 0 ? 'Paid' : 'Pending';
        },
        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); },

        // Customer helpers
        async searchCustomers(page) {
            page = page || 1;
            if (page === 1) this.custPage = 1;
            this.custLoading = true;
            const r = await RepairBox.ajax('/customers-search?page=' + page + '&q=' + encodeURIComponent(this.custSearch || ''));
            this.custLoading = false;
            const rows = Array.isArray(r.data) ? r.data : [];
            this.custResults = page === 1 ? rows : this.custResults.concat(rows);
            this.custHasMore = r.has_more || false;
            this.custPage = page;
            this.custOpen = true;
        },
        handleCustScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.custHasMore && !this.custLoading) {
                this.searchCustomers(this.custPage + 1);
            }
        },
        selectCustomer(c) {
            this.selectedCust = c;
            this.form.customer_id = c.id;
            this.custSearch = '';
            this.custResults = [];
            this.custOpen = false;
        },
        clearCustomer() {
            this.selectedCust = null;
            this.form.customer_id = null;
            this.custSearch = '';
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
        async saveNewCust() {
            this.customerFormTried = true;
            this.customerSubmitError = '';
            const validation = RepairBox.validateCustomerPayload(this.newCust);
            this.newCust = { ...this.newCust, ...validation.payload, email: validation.payload.email || '', address: validation.payload.address || '' };
            if (!validation.valid) return;
            this.customerSaving = true;
            const r = await RepairBox.ajax('/customers', 'POST', validation.payload);
            this.customerSaving = false;
            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.closeAddCustModal();
                this.newCust = RepairBox.emptyCustomer();
                RepairBox.toast('Customer added and selected', 'success');
                return;
            }
            this.customerSubmitError = r.message || 'Unable to save customer.';
        },

        // Create repair
        canCreate() {
            return this.form.customer_id && this.form.device_brand.trim() && this.form.device_model.trim() && this.form.problem_description.trim();
        },
        hasFormData() {
            return this.form.device_brand || this.form.device_model || this.form.imei || this.form.problem_description || this.form.estimated_cost || this.form.advance_amount;
        },
        resetForm() {
            this.form = {
                customer_id: this.form.customer_id,
                device_brand: '', device_model: '', imei: '', problem_description: '',
                estimated_cost: '', expected_delivery_date: '', advance_amount: '', advance_method: 'cash', advance_reference: '',
            };
        },
        async saveRepair() {
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }
            if (!this.form.device_brand.trim()) { RepairBox.toast('Device brand is required', 'error'); return; }
            if (!this.form.device_model.trim()) { RepairBox.toast('Device model is required', 'error'); return; }
            if (!this.form.problem_description.trim()) { RepairBox.toast('Problem description is required', 'error'); return; }

            this.saving = true;
            const r = await RepairBox.ajax('/repairs', 'POST', this.form);
            this.saving = false;

            if (r.success !== false) {
                this.createdRepair = r.data;
                this.showSuccess = true;
                RepairBox.toast('Repair created: ' + r.data.ticket_number, 'success');
                this.load();
            }
        },
        newRepair() {
            this.showSuccess = false;
            this.createdRepair = null;
            this.selectedCust = null;
            this.form = {
                customer_id: null, device_brand: '', device_model: '', imei: '', problem_description: '',
                estimated_cost: '', expected_delivery_date: '', advance_amount: '', advance_method: 'cash', advance_reference: '',
            };
            this.optionalOpen = false;
        },
    };
}
</script>

<datalist id="brand-list">
    @foreach($brands as $brand)
        <option value="{{ $brand }}"></option>
    @endforeach
</datalist>
@endpush
