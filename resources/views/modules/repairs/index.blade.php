@extends('layouts.app')
@section('page-title', 'Repairs')
@section('content-class', 'workspace-content')

@section('content')
<div x-data="repairsPage()" x-init="init()" class="workspace-screen">

    <x-ui.action-bar title="Repair Workspace" description="Run the entire repair queue from one contained screen with internal table and board scrolling.">
        <div class="flex flex-wrap items-center justify-end gap-3 w-full sm:w-auto">
            <div class="inline-flex bg-white border rounded-lg shadow-sm w-full sm:w-auto">
                <button @click="viewMode = 'table'; updateUrl()" class="px-3 py-2 text-sm font-medium rounded-l-lg transition-colors" :class="viewMode === 'table' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50'" title="Table View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>
                <button @click="viewMode = 'kanban'; updateUrl()" class="px-3 py-2 text-sm font-medium rounded-r-lg transition-colors" :class="viewMode === 'kanban' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50'" title="Kanban Board">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                </button>
            </div>
            <a href="/repairs/create" class="btn-primary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Repair
            </a>
        </div>
    </x-ui.action-bar>

    <x-ui.filter-bar class="flex-col items-stretch gap-3" style="position: relative; z-index: 50;">
        <div class="w-full space-y-4">
            <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
                <div class="relative min-w-0 flex-1 xl:max-w-none">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="searchQuery" @input.debounce.400ms="load()" type="text" class="form-input-custom pl-10 w-full" placeholder="Search ticket, customer, device, IMEI...">
                    <button x-show="searchQuery" @click="searchQuery = ''; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center xl:flex-none xl:justify-end">
                    <button type="button" @click="showAdvancedFilters = !showAdvancedFilters" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 shadow-sm transition hover:border-slate-300 hover:text-slate-900 hover:shadow-md">
                        <svg class="h-4 w-4 transition-transform" :class="showAdvancedFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        <span x-text="showAdvancedFilters ? 'Hide filters' : 'More filters'"></span>
                    </button>

                    <button x-show="searchQuery || selectedStatuses.length || dateFrom || dateTo || paymentFilter" type="button" @click="resetFilters()" class="inline-flex h-11 items-center justify-center rounded-xl px-3 text-sm font-medium text-red-600 transition hover:bg-red-50 hover:text-red-700">
                        Reset filters
                    </button>
                </div>
            </div>

            <div class="relative mt-1 w-fit" x-data="{ statusOpen: false }" @click.away="statusOpen = false">
                <button type="button" @click="statusOpen = !statusOpen" class="form-input-custom inline-flex h-11 w-[18rem] items-center justify-between gap-3 px-4 text-sm sm:w-[20rem]">
                    <span class="truncate" x-text="statusSummaryLabel()"></span>
                    <div class="flex items-center gap-2">
                        <span x-show="selectedStatuses.length" class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-primary-600 px-1.5 text-[10px] font-bold text-white" x-text="selectedStatuses.length"></span>
                        <svg class="h-4 w-4 text-slate-400 transition-transform" :class="statusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </button>
                <div x-show="statusOpen" x-transition.opacity.duration.150ms class="absolute left-0 top-full mt-1.5 w-full min-w-[16rem] rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl" style="z-index: 9999;" x-cloak>
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
                        <button type="button" @click="clearStatusSelection(); statusOpen = false" class="flex w-full items-center justify-center rounded-xl px-3.5 py-2 text-xs font-medium text-red-600 transition-colors hover:bg-red-50">
                            Clear statuses
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showAdvancedFilters" x-collapse class="grid w-full self-stretch grid-cols-1 gap-3 border-t border-slate-200 pt-3 sm:grid-cols-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">From Date</label>
                <input x-model="dateFrom" @change="load()" type="date" class="form-input-custom text-sm w-full">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">To Date</label>
                <input x-model="dateTo" @change="load()" type="date" class="form-input-custom text-sm w-full">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Payment</label>
                <select x-model="paymentFilter" @change="load()" class="form-select-custom text-sm w-full">
                    <option value="">All</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
        </div>
    </x-ui.filter-bar>

    <x-ui.table-card x-show="viewMode === 'table'" x-cloak>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Repair Queue</h3>
                <p class="text-sm text-slate-500">A shorter list with just the core repair details.</p>
            </div>
        </x-slot:header>

        <table class="data-table w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="r in paginatedItems" :key="r.id">
                        <tr class="hover:bg-gray-50/50 transition-colors cursor-pointer" @click="window.location.href = '/repairs/' + r.id">
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    <span class="font-semibold text-primary-600" x-text="r.ticket_number"></span>
                                    <span class="text-xs text-gray-400" x-text="'#' + r.id"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 text-sm" x-text="r.customer ? r.customer.name : '-'"></div>
                                <div class="text-xs text-gray-400" x-text="r.customer ? r.customer.mobile_number : ''"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-800" x-text="deviceLabel(r)"></div>
                                <div class="text-xs text-gray-400" x-show="r.imei" x-text="'IMEI: ' + r.imei"></div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold" :class="statusBadgeClass(r.status)" x-text="statusLabel(r.status)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium" x-text="'₹' + Number(r.grand_total || r.estimated_cost || 0).toFixed(2)"></div>
                                <div class="text-xs" :class="r.balance_due > 0 ? 'text-red-500' : 'text-green-500'" x-text="amountMeta(r)"></div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500" x-text="formatDate(r.created_at)"></td>
                            <td class="px-4 py-3 text-center" @click.stop>
                                <div class="inline-flex items-center gap-1">
                                    <!-- Cost/Profit Breakdown -->
                                    <a :href="'/repairs/' + r.id + '/cost-breakdown'" class="p-1.5 rounded-lg text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Cost & Profit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </a>
                                    <a :href="'/repairs/' + r.id" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="View Details">
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
                        <td colspan="7" class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <p class="text-gray-400 font-medium">No repairs found</p>
                            <p class="text-gray-300 text-sm mt-1">Try adjusting your filters or create a new repair</p>
                        </td>
                    </tr>
                    <!-- Skeleton Loader -->
                    <template x-if="loading">
                        <template x-for="i in 10" :key="'sk'+i">
                            <tr>
                                <td><div class="skeleton h-3 w-24"></div></td>
                                <td><div class="skeleton h-3 w-32"></div></td>
                                <td><div class="skeleton h-3 w-28"></div></td>
                                <td><div class="skeleton h-3 w-24 rounded-full"></div></td>
                                <td><div class="skeleton h-3 w-24"></div></td>
                                <td><div class="skeleton h-3 w-20"></div></td>
                                <td><div class="skeleton h-3 w-16"></div></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        <x-slot:footer>
        <div x-show="totalPages > 1" class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-500">
                Showing <span class="font-medium" x-text="((currentPage - 1) * perPage) + 1"></span> to <span class="font-medium" x-text="Math.min(currentPage * perPage, filteredItems.length)"></span> of <span class="font-medium" x-text="filteredItems.length"></span>
            </div>
            <div class="flex items-center gap-1">
                <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Prev</button>
                <template x-for="p in visiblePages" :key="'p'+p">
                    <button @click="if(p !== '...') currentPage = p" class="px-3 py-1.5 text-sm border rounded-lg" :class="p === currentPage ? 'bg-primary-600 text-white border-primary-600' : (p === '...' ? 'cursor-default border-transparent' : 'hover:bg-white')" x-text="p"></button>
                </template>
                <button @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-white disabled:opacity-40 disabled:cursor-not-allowed">Next</button>
            </div>
        </div>
        </x-slot:footer>
    </x-ui.table-card>

    <div x-show="viewMode === 'kanban'" x-cloak class="card overflow-hidden">
        <div class="card-header">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Repair Board</h3>
                <p class="text-sm text-slate-500">Drag your focus across stages without leaving the same workspace.</p>
            </div>
        </div>

        <div class="px-4 pb-4 pt-0 sm:px-5 sm:pb-5">
            <div class="overflow-x-auto overflow-y-hidden pb-2">
                <div class="flex items-start gap-4 min-w-max">
                    <template x-for="[colKey, colMeta] of kanbanColumns" :key="colKey">
                        <div class="w-72 flex-shrink-0">
                            <div class="rounded-t-xl px-4 py-3 flex items-center justify-between" :class="kanbanHeaderClass(colKey)">
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
@endsection

@push('scripts')
<script>
function repairsPage() {
    return {
        items: [],
        loading: true,
        saving: false,

        viewMode: 'table',
        currentPage: 1,
        perPage: 15,

        searchQuery: '',
        selectedStatuses: [],
        dateFrom: '',
        dateTo: '',
        paymentFilter: '',
        showAdvancedFilters: false,

        statusMeta: @json($statusMeta),

        // Computed
        get statusFilterOptions() {
            return Object.entries(this.statusMeta).map(([key, meta]) => ({
                key,
                label: meta.label,
                activeCardClass: this.statusCardActiveClass(key),
                inactiveCardClass: this.statusCardInactiveClass(key),
                activeEyebrowClass: 'text-white/70',
                countActiveClass: this.statusCountActiveClass(key),
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

        async init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.searchQuery = p.get('search');
            if (p.has('status')) this.selectedStatuses = p.get('status').split(',').filter(Boolean);
            if (p.has('from')) this.dateFrom = p.get('from');
            if (p.has('to')) this.dateTo = p.get('to');
            if (p.has('payment')) this.paymentFilter = p.get('payment');
            if (p.has('view')) this.viewMode = p.get('view');
            if (this.dateFrom || this.dateTo || this.paymentFilter) this.showAdvancedFilters = true;
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

        toggleStatusSelection(status) {
            if (this.selectedStatuses.includes(status)) {
                this.selectedStatuses = this.selectedStatuses.filter(value => value !== status);
            } else {
                this.selectedStatuses = [...this.selectedStatuses, status];
            }

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
        isStatusSelected(status) {
            return this.selectedStatuses.includes(status);
        },
        statusSummaryLabel() {
            if (this.selectedStatuses.length === 0) {
                return 'All Statuses (' + this.items.length + ')';
            }

            if (this.selectedStatuses.length === 1) {
                return this.statusLabel(this.selectedStatuses[0]);
            }

            return this.selectedStatuses.length + ' statuses selected';
        },

        // Status helpers
        statusCount(status) {
            return this.items.filter(i => i.status === status).length;
        },
        statusLabel(status) { return this.statusMeta[status]?.label || status?.replace('_', ' ') || ''; },
        statusBadgeClass(status) {
            const map = { received: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700', payment: 'bg-purple-100 text-purple-700', closed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-700' };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
        statusCardActiveClass(status) {
            const map = {
                received: 'border-blue-600 bg-blue-600 text-white',
                in_progress: 'border-amber-500 bg-amber-500 text-white',
                completed: 'border-emerald-600 bg-emerald-600 text-white',
                payment: 'border-purple-600 bg-purple-600 text-white',
                closed: 'border-green-700 bg-green-700 text-white',
                cancelled: 'border-red-600 bg-red-600 text-white',
            };
            return map[status] || 'border-slate-900 bg-slate-900 text-white';
        },
        statusCardInactiveClass(status) {
            const map = {
                received: 'border-blue-100 bg-blue-50/70 text-blue-700 hover:border-blue-200',
                in_progress: 'border-amber-100 bg-amber-50/70 text-amber-700 hover:border-amber-200',
                completed: 'border-emerald-100 bg-emerald-50/70 text-emerald-700 hover:border-emerald-200',
                payment: 'border-purple-100 bg-purple-50/70 text-purple-700 hover:border-purple-200',
                closed: 'border-green-100 bg-green-50/70 text-green-700 hover:border-green-200',
                cancelled: 'border-red-100 bg-red-50/70 text-red-700 hover:border-red-200',
            };
            return map[status] || 'border-slate-200 bg-white text-slate-700 hover:border-slate-300';
        },
        statusDotClass(status) {
            const map = { received: 'bg-blue-500', in_progress: 'bg-amber-500', completed: 'bg-emerald-500', payment: 'bg-purple-500', closed: 'bg-green-600', cancelled: 'bg-red-500' };
            return map[status] || 'bg-slate-400';
        },
        statusCountActiveClass(status) {
            const map = {
                received: 'bg-blue-700 text-white',
                in_progress: 'bg-amber-600 text-white',
                completed: 'bg-emerald-700 text-white',
                payment: 'bg-purple-700 text-white',
                closed: 'bg-green-800 text-white',
                cancelled: 'bg-red-700 text-white',
            };
            return map[status] || 'bg-slate-800 text-white';
        },
        kanbanHeaderClass(status) {
            const map = { received: 'bg-blue-500 text-white', in_progress: 'bg-amber-500 text-white', completed: 'bg-emerald-500 text-white', payment: 'bg-purple-500 text-white', closed: 'bg-green-700 text-white', cancelled: 'bg-red-500 text-white' };
            return map[status] || 'bg-gray-500 text-white';
        },
        kanbanItems(status) { return this.items.filter(i => i.status === status); },

        deviceLabel(repair) {
            return [repair.device_brand, repair.device_model].filter(Boolean).join(' ') || 'Device not set';
        },
        amountMeta(repair) {
            if (repair.status === 'cancelled' && Number(repair.total_refunded || 0) > 0) {
                return 'Refunded: ₹' + Number(repair.total_refunded).toFixed(2);
            }
            if (Number(repair.balance_due || 0) > 0) {
                return 'Due: ₹' + Number(repair.balance_due).toFixed(2);
            }
            return Number(repair.grand_total || repair.estimated_cost || 0) > 0 ? 'Paid' : 'Pending';
        },

        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); },
    };
}
</script>
@endpush
