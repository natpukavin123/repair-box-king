@extends('layouts.app')
@section('page-title', 'Repairs')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="repairsPage()" x-init="init()" class="page-list">

    <!-- ===== HEADER BAR ===== -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Repair Orders</h2>
            <p class="text-sm text-gray-500 mt-0.5">Manage device repair workflow from received to closed</p>
        </div>
        <div class="flex items-center gap-2">
            <!-- View Toggle -->
            <div class="inline-flex bg-white border rounded-lg shadow-sm">
                <button @click="viewMode = 'table'; updateUrl()" class="px-3 py-2 text-sm font-medium rounded-l-lg transition-colors" :class="viewMode === 'table' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50'" title="Table View">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </button>
                <button @click="viewMode = 'kanban'; updateUrl()" class="px-3 py-2 text-sm font-medium rounded-r-lg transition-colors" :class="viewMode === 'kanban' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-50'" title="Kanban Board">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/></svg>
                </button>
            </div>
            <a href="/repairs/create" class="btn-primary inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Repair
            </a>
        </div>
    </div>

    <!-- ===== FILTERS & SEARCH ===== -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5">
        <div class="flex flex-col lg:flex-row gap-3">
            <!-- Search -->
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input x-model="searchQuery" @input.debounce.400ms="load()" type="text" class="form-input-custom pl-10 w-full" placeholder="Search ticket, customer, device, IMEI...">
                <button x-show="searchQuery" @click="searchQuery = ''; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <!-- Status Filter Pills -->
            <div class="flex flex-wrap gap-1.5">
                <button @click="statusFilter = ''; currentPage = 1; updateUrl()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="statusFilter === '' ? 'bg-gray-800 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                    All <span class="ml-1 opacity-70" x-text="'(' + items.length + ')'"></span>
                </button>
                <template x-for="[key, meta] of Object.entries(statusMeta)" :key="key">
                    <button @click="statusFilter = key; currentPage = 1; updateUrl()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="statusFilter === key ? statusPillActive(key) : statusPillInactive(key)" x-text="meta.label + ' (' + statusCount(key) + ')'"></button>
                </template>
                <button @click="statusFilter = 'void'; currentPage = 1; updateUrl()" class="px-3 py-1.5 rounded-full text-xs font-semibold transition-all" :class="statusFilter === 'void' ? 'bg-red-800 text-white shadow-md' : 'bg-red-50 text-red-700 hover:bg-red-100'">
                    Void <span class="ml-1 opacity-70" x-text="'(' + voidCount + ')'"></span>
                </button>
            </div>
        </div>
        <!-- Advanced Filters (collapsible) -->
        <div x-show="showAdvancedFilters" x-collapse class="mt-3 pt-3 border-t grid grid-cols-2 md:grid-cols-4 gap-3">
            <div>
                <label class="text-xs font-medium text-gray-500 mb-1 block">Technician</label>
                <select x-model="techFilter" @change="load()" class="form-select-custom text-sm w-full">
                    <option value="">All Technicians</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
            </div>
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
        <div class="flex items-center justify-between mt-2">
            <button @click="showAdvancedFilters = !showAdvancedFilters" class="text-xs text-primary-600 hover:text-primary-800 font-medium inline-flex items-center gap-1">
                <svg class="w-3 h-3 transition-transform" :class="showAdvancedFilters && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                <span x-text="showAdvancedFilters ? 'Hide Filters' : 'More Filters'"></span>
            </button>
            <button x-show="searchQuery || statusFilter || techFilter || dateFrom || dateTo || paymentFilter" @click="searchQuery = ''; statusFilter = ''; techFilter = ''; dateFrom = ''; dateTo = ''; paymentFilter = ''; currentPage = 1; load()" class="text-xs text-red-600 hover:text-red-800 font-medium inline-flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                Clear All
            </button>
        </div>
    </div>

    <!-- ===== TABLE VIEW ===== -->
    <div x-show="viewMode === 'table'" class="bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col min-h-0 flex-1">
        <div class="table-scroll">
            <table class="data-table w-full">
                <thead class="sticky top-0 z-10">
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Ticket</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Device</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Progress</th>
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
                                <span class="font-semibold text-primary-600" x-text="r.ticket_number"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-800 text-sm" x-text="r.customer ? r.customer.name : '-'"></div>
                                <div class="text-xs text-gray-400" x-text="r.customer ? r.customer.mobile_number : ''"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm" x-text="(r.device_brand || '') + ' ' + (r.device_model || '')"></div>
                                <div class="text-xs text-gray-400" x-show="r.imei" x-text="'IMEI: ' + r.imei"></div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center" x-show="r.status !== 'cancelled'">
                                    <template x-for="(step, idx) in progressSteps" :key="step.key">
                                        <div class="flex items-center">
                                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold transition-all border-2"
                                                :class="stepReached(r.status, step.key)
                                                    ? (r.status === step.key ? statusDotCurrent(step.key) : 'bg-green-500 border-green-500 text-white')
                                                    : 'bg-white border-gray-200 text-gray-300'"
                                                x-text="idx + 1">
                                            </div>
                                            <div x-show="idx < progressSteps.length - 1" class="w-4 h-0.5" :class="stepReached(r.status, step.key) && stepReached(r.status, progressSteps[idx+1]?.key) ? 'bg-green-500' : 'bg-gray-200'"></div>
                                        </div>
                                    </template>
                                </div>
                                <span x-show="r.status === 'cancelled' && r.record_type !== 'void'" class="inline-flex items-center gap-1 text-red-500 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Cancelled
                                </span>
                                <span x-show="r.record_type === 'void'" class="inline-flex items-center gap-1 text-gray-500 text-xs font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                    Void
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold" :class="r.record_type === 'void' ? 'bg-gray-800 text-white' : statusBadgeClass(r.status)" x-text="r.record_type === 'void' ? 'Void' : statusLabel(r.status)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium" x-text="'₹' + Number(r.grand_total || r.estimated_cost || 0).toFixed(2)"></div>
                                <template x-if="r.status === 'cancelled'">
                                    <div class="text-xs text-red-500" x-show="r.total_refunded > 0" x-text="'Refunded: ₹' + Number(r.total_refunded).toFixed(2)"></div>
                                </template>
                                <template x-if="r.status !== 'cancelled'">
                                    <div class="text-xs" :class="r.balance_due > 0 ? 'text-red-500' : 'text-green-500'" x-text="r.balance_due > 0 ? 'Due: ₹' + Number(r.balance_due).toFixed(2) : (r.grand_total > 0 ? 'Paid' : '')"></div>
                                </template>
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
                        <td colspan="8" class="text-center py-12">
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
                                <td><div class="flex gap-1"><div class="skeleton h-4 w-4 rounded-full"></div><div class="skeleton h-4 w-4 rounded-full"></div><div class="skeleton h-4 w-4 rounded-full"></div><div class="skeleton h-4 w-4 rounded-full"></div><div class="skeleton h-4 w-4 rounded-full"></div></div></td>
                                <td><div class="skeleton h-3 w-24 rounded-full"></div></td>
                                <td><div class="skeleton h-3 w-24"></div></td>
                                <td><div class="skeleton h-3 w-20"></div></td>
                                <td><div class="skeleton h-3 w-16"></div></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div x-show="totalPages > 1" class="flex items-center justify-between px-4 py-3 border-t bg-gray-50/50">
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
    </div>

    <!-- ===== KANBAN BOARD VIEW ===== -->
    <div x-show="viewMode === 'kanban'" class="overflow-x-auto pb-4">
        <div class="flex gap-4 min-w-max">
            <template x-for="[colKey, colMeta] of kanbanColumns" :key="colKey">
                <div class="w-72 flex-shrink-0">
                    <div class="rounded-t-xl px-4 py-3 flex items-center justify-between" :class="kanbanHeaderClass(colKey)">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-sm" x-text="colMeta.label"></span>
                            <span class="text-xs bg-white/30 px-2 py-0.5 rounded-full font-medium" x-text="kanbanItems(colKey).length"></span>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-b-xl p-2 min-h-[200px] max-h-[calc(100vh-280px)] overflow-y-auto space-y-2">
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
        statusFilter: '',
        techFilter: '',
        dateFrom: '',
        dateTo: '',
        paymentFilter: '',
        showAdvancedFilters: false,

        statusMeta: @json($statusMeta),

        progressSteps: [
            { key: 'received', label: 'Received' },
            { key: 'in_progress', label: 'In Progress' },
            { key: 'completed', label: 'Completed' },
            { key: 'payment', label: 'Payment' },
            { key: 'closed', label: 'Closed' },
        ],

        // Computed
        get filteredItems() {
            let items = this.items;
            if (this.statusFilter === 'void') {
                items = items.filter(i => i.record_type === 'void');
            } else if (this.statusFilter) {
                items = items.filter(i => i.status === this.statusFilter);
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
            if (p.has('status')) this.statusFilter = p.get('status');
            if (p.has('technician')) this.techFilter = p.get('technician');
            if (p.has('from')) this.dateFrom = p.get('from');
            if (p.has('to')) this.dateTo = p.get('to');
            if (p.has('payment')) this.paymentFilter = p.get('payment');
            if (p.has('view')) this.viewMode = p.get('view');
            if (this.techFilter || this.dateFrom || this.dateTo || this.paymentFilter) this.showAdvancedFilters = true;
            await this.load();
        },

        updateUrl() {
            const params = new URLSearchParams();
            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.statusFilter) params.set('status', this.statusFilter);
            if (this.techFilter) params.set('technician', this.techFilter);
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
            if (this.techFilter) params.set('technician_id', this.techFilter);
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
        statusCount(status) { return this.items.filter(i => i.status === status).length; },
        get voidCount() { return this.items.filter(i => i.record_type === 'void').length; },
        statusLabel(status) { return this.statusMeta[status]?.label || status?.replace('_', ' ') || ''; },
        statusBadgeClass(status) {
            const map = { received: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700', payment: 'bg-purple-100 text-purple-700', closed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-700' };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
        statusPillActive(status) {
            const map = { received: 'bg-blue-600 text-white shadow-md', in_progress: 'bg-amber-500 text-white shadow-md', completed: 'bg-emerald-600 text-white shadow-md', payment: 'bg-purple-600 text-white shadow-md', closed: 'bg-green-700 text-white shadow-md', cancelled: 'bg-red-600 text-white shadow-md' };
            return map[status] || 'bg-gray-800 text-white shadow-md';
        },
        statusPillInactive(status) {
            const map = { received: 'bg-blue-50 text-blue-600 hover:bg-blue-100', in_progress: 'bg-amber-50 text-amber-600 hover:bg-amber-100', completed: 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100', payment: 'bg-purple-50 text-purple-600 hover:bg-purple-100', closed: 'bg-green-50 text-green-700 hover:bg-green-100', cancelled: 'bg-red-50 text-red-600 hover:bg-red-100' };
            return map[status] || 'bg-gray-100 text-gray-600 hover:bg-gray-200';
        },
        statusDotCurrent(status) {
            const map = { received: 'bg-blue-500 border-blue-500 text-white ring-2 ring-blue-200', in_progress: 'bg-amber-500 border-amber-500 text-white ring-2 ring-amber-200', completed: 'bg-emerald-500 border-emerald-500 text-white ring-2 ring-emerald-200', payment: 'bg-purple-500 border-purple-500 text-white ring-2 ring-purple-200', closed: 'bg-green-600 border-green-600 text-white ring-2 ring-green-200' };
            return map[status] || 'bg-primary-600 border-primary-600 text-white ring-2 ring-primary-200';
        },
        stepReached(current, step) {
            const order = ['received', 'in_progress', 'completed', 'payment', 'closed'];
            return order.indexOf(current) >= order.indexOf(step);
        },
        kanbanHeaderClass(status) {
            const map = { received: 'bg-blue-500 text-white', in_progress: 'bg-amber-500 text-white', completed: 'bg-emerald-500 text-white', payment: 'bg-purple-500 text-white', closed: 'bg-green-700 text-white', cancelled: 'bg-red-500 text-white' };
            return map[status] || 'bg-gray-500 text-white';
        },
        kanbanItems(status) { return this.items.filter(i => i.status === status && i.record_type !== 'void'); },

        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); },
    };
}
</script>
@endpush
