@extends('layouts.app')
@section('page-title', 'Purchase Orders')

@section('content')
<div x-data="poPage()" x-init="init()" class="h-full">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">

        {{-- ===== LEFT: New PO Request Form ===== --}}
        <div class="lg:col-span-1 flex flex-col gap-3">

            {{-- Customer Search (POS-style) --}}
            <div class="card" style="overflow:visible">
                <div class="card-body py-3" style="overflow:visible">
                    <label class="text-xs font-medium text-gray-500 mb-1 block">Customer</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1" @click.away="custOpen = false">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input x-model="custSearch" @focus="findCustomers(1)" @input.debounce.300ms="findCustomers(1)" type="text"
                                class="form-input-custom pl-10 text-sm" placeholder="Search by name or mobile...">
                            {{-- Search Results Dropdown --}}
                            <div x-show="custOpen && custResults.length > 0" x-cloak
                                class="absolute left-0 right-0 mt-1 border rounded-lg bg-white shadow-lg overflow-hidden" style="z-index:50">
                                <div class="max-h-48 overflow-y-auto" @scroll="handleCustScroll($event)">
                                    <template x-for="c in custResults" :key="c.id">
                                        <button @click="selectCustomer(c)"
                                            class="w-full text-left px-4 py-2.5 hover:bg-primary-50 text-sm border-b last:border-b-0 flex items-center gap-3 transition-colors">
                                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-xs font-bold"
                                                x-text="c.name.charAt(0).toUpperCase()"></div>
                                            <div>
                                                <div class="font-medium text-gray-800" x-text="c.name"></div>
                                                <div class="text-xs text-gray-400" x-text="c.mobile_number"></div>
                                            </div>
                                        </button>
                                    </template>
                                    <div x-show="custLoading" class="px-4 py-2.5 text-xs text-gray-400 text-center flex items-center justify-center gap-2">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                        Loading…
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="showNewCust = !showNewCust"
                            class="btn-secondary text-sm px-3 whitespace-nowrap">
                            <svg class="w-4 h-4 inline mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            New
                        </button>
                    </div>
                    <div x-show="custOpen && !custLoading && custResults.length === 0"
                        class="text-xs text-gray-400 mt-1">No customers found — click <strong>New</strong> to add manually.</div>

                    {{-- Selected Customer Badge --}}
                    <div x-show="selectedCustomer" x-cloak class="mt-2 flex items-center gap-2 bg-primary-50 border border-primary-200 rounded-lg px-3 py-2">
                        <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs font-bold"
                            x-text="selectedCustomer?.name?.charAt(0).toUpperCase()"></div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-800 truncate" x-text="selectedCustomer?.name"></div>
                            <div class="text-xs text-gray-500" x-text="selectedCustomer?.mobile_number"></div>
                        </div>
                        <button @click="clearCustomer()" class="text-gray-400 hover:text-red-500 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- New Customer Inline Form --}}
                    <div x-show="showNewCust" x-cloak x-transition class="mt-3 p-3 bg-gray-50 rounded-lg border space-y-2">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Name *</label>
                            <input x-model="form.customer_name" type="text" class="form-input-custom text-sm mt-0.5" placeholder="Customer name">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Phone</label>
                            <input x-model="form.customer_phone" type="text" class="form-input-custom text-sm mt-0.5" placeholder="Mobile number">
                        </div>
                    </div>
                </div>
            </div>

            {{-- PO Request Details --}}
            <div class="card flex-1 flex flex-col">
                <div class="card-header bg-primary-50 border-b border-primary-100 py-2">
                    <h3 class="text-base font-semibold text-primary-800 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        New PO Request
                    </h3>
                </div>
                <div class="card-body flex-1 flex flex-col">
                    <div class="space-y-3 flex-1">
                        <div>
                            <label class="text-xs font-medium text-gray-600">Requested Products / Parts *</label>
                            <textarea x-model="form.requested_items" rows="5" class="form-input-custom text-sm mt-0.5"
                                placeholder="e.g. iPhone 14 display&#10;Samsung A14 battery × 2&#10;Type-C fast charger"></textarea>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Required By</label>
                            <input type="date" x-model="form.required_by" class="form-input-custom text-sm mt-0.5">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600">Notes</label>
                            <textarea x-model="form.notes" rows="2" class="form-input-custom text-sm mt-0.5"
                                placeholder="Budget, color, any extra detail..."></textarea>
                        </div>
                    </div>
                    <button @click="save()" class="btn-primary w-full py-3 text-base font-semibold mt-4" :disabled="saving">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save PO Request
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: PO Request List ===== --}}
        <div class="lg:col-span-2 flex flex-col gap-3">

            {{-- Status summary pills --}}
            <div class="flex flex-wrap gap-2">
                <button @click="setStatus('')"
                    class="px-3 py-1.5 rounded-full text-xs font-semibold border-2 transition-all"
                    :class="!filters.status ? 'border-gray-700 bg-gray-800 text-white' : 'border-gray-200 text-gray-500 hover:border-gray-300 bg-white'">
                    All <span class="ml-1 opacity-70" x-text="allCounts.all"></span>
                </button>
                <template x-for="s in statusList" :key="s.key">
                    <button @click="setStatus(filters.status === s.key ? '' : s.key)"
                        class="px-3 py-1.5 rounded-full text-xs font-semibold border-2 transition-all"
                        :class="filters.status === s.key ? s.activeCls : 'border-gray-200 text-gray-500 hover:border-gray-300 bg-white'">
                        <span x-text="s.label"></span>
                        <span class="ml-1 opacity-70" x-text="allCounts[s.key]"></span>
                    </button>
                </template>
            </div>

            {{-- Search + Date range --}}
            <div class="flex flex-wrap items-end gap-2">
                <div class="relative flex-1" style="min-width:180px">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="filters.search" @input.debounce.400ms="page=1; load()" type="text"
                        class="form-input-custom pl-10 text-sm" placeholder="Search requests...">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 mb-0.5 block">From Date</label>
                    <input type="date" x-model="filters.date_from" @change="page=1; load()"
                        class="form-input-custom text-sm" style="width:140px">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-500 mb-0.5 block">To Date</label>
                    <input type="date" x-model="filters.date_to" @change="page=1; load()"
                        class="form-input-custom text-sm" style="width:140px">
                </div>
                <button x-show="filters.date_from || filters.date_to || filters.search" @click="clearFilters()"
                    class="btn-secondary text-sm px-3 h-[38px]" title="Clear filters">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- List --}}
            <div class="card flex-1 flex flex-col relative">
                {{-- Overlay loader (shown on subsequent loads, not first) --}}
                <div x-show="loading && !firstLoad" x-cloak x-transition.opacity
                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-xs text-gray-400 font-medium">Loading...</span>
                    </div>
                </div>

                <div class="card-header py-2 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        PO Requests (<span x-text="total"></span>)
                    </h3>
                    <button @click="load()" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                </div>

                <div class="flex-1 overflow-y-auto" style="max-height:calc(100vh - 260px)">
                    {{-- Items --}}
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="px-4 py-3 border-b last:border-0 hover:bg-gray-50/50 transition-colors cursor-pointer"
                            :class="viewing?.id === item.id ? 'bg-primary-50/50 border-l-2 border-l-primary-500' : ''"
                            @click="viewDetail(item)">
                            <div class="flex items-start gap-3">
                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0"
                                    :class="item.status === 'completed' ? 'bg-emerald-100 text-emerald-700' : item.status === 'cancelled' ? 'bg-red-100 text-red-500' : 'bg-primary-100 text-primary-700'"
                                    x-text="(item.customer_name || item.customer?.name || '?').charAt(0).toUpperCase()"></div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="font-medium text-sm text-gray-900 truncate" x-text="item.customer_name || item.customer?.name || 'Walk-in'"></span>
                                        <span class="badge text-[10px]" :class="statusBadge(item.status)" x-text="statusLabel(item.status)"></span>
                                    </div>
                                    <p class="text-xs text-gray-500 truncate" x-text="item.requested_items"></p>
                                    <div class="flex items-center gap-3 mt-1">
                                        <span class="text-[10px] text-gray-400" x-text="fmtDate(item.created_at)"></span>
                                        <template x-if="item.required_by">
                                            <span class="text-[10px] inline-flex items-center gap-0.5"
                                                :class="isOverdue(item.required_by) && item.status !== 'completed' && item.status !== 'cancelled' ? 'text-red-500 font-semibold' : 'text-gray-400'">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span x-text="'Due: ' + fmtDate(item.required_by)"></span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Right arrow --}}
                                <svg class="w-4 h-4 text-gray-300 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </template>

                    {{-- First-load skeletons (only when no items yet) --}}
                    <template x-if="loading && firstLoad">
                        <div>
                            <template x-for="i in 6" :key="'sk'+i">
                                <div class="px-4 py-3 border-b flex items-start gap-3">
                                    <div class="skeleton w-9 h-9 rounded-full flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <div class="skeleton h-3.5 w-32 mb-2"></div>
                                        <div class="skeleton h-3 w-48 mb-1.5"></div>
                                        <div class="skeleton h-2.5 w-20"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Empty --}}
                    <div x-show="!loading && items.length === 0" class="text-center py-16">
                        <svg class="w-12 h-12 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm text-gray-400">No PO requests found</p>
                        <p class="text-xs text-gray-300 mt-1">Create one using the form on the left</p>
                    </div>
                </div>

                {{-- Pagination --}}
                <div x-show="lastPage > 1" class="border-t px-4 py-2 flex items-center justify-between bg-gray-50/50 text-xs">
                    <button @click="page--; load()" :disabled="page <= 1"
                        class="text-primary-600 hover:text-primary-800 disabled:text-gray-300 font-medium">&laquo; Prev</button>
                    <span class="text-gray-500" x-text="'Page ' + page + ' of ' + lastPage"></span>
                    <button @click="page++; load()" :disabled="page >= lastPage"
                        class="text-primary-600 hover:text-primary-800 disabled:text-gray-300 font-medium">Next &raquo;</button>
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
                    <div class="grid grid-cols-2 gap-4">
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

        statusList: [
            { key: 'open',      label: 'Open',      activeCls: 'border-blue-500 bg-blue-50 text-blue-700' },
            { key: 'ordered',   label: 'Ordered',   activeCls: 'border-amber-500 bg-amber-50 text-amber-700' },
            { key: 'received',  label: 'Received',  activeCls: 'border-purple-500 bg-purple-50 text-purple-700' },
            { key: 'completed', label: 'Completed', activeCls: 'border-emerald-500 bg-emerald-50 text-emerald-700' },
            { key: 'cancelled', label: 'Cancelled', activeCls: 'border-red-500 bg-red-50 text-red-700' },
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
            this.filters.date_from = '';
            this.filters.date_to = '';
            this.page = 1;
            this.load();
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
            // Reset form
            this.form = { customer_id: '', customer_name: '', customer_phone: '', requested_items: '', required_by: '', notes: '' };
            this.selectedCustomer = null;
            this.custSearch = '';
            this.showNewCust = false;
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
