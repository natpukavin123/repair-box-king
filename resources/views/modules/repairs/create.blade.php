@extends('layouts.app')
@section('page-title', 'Create Repair')

@section('content')
<div x-data="createRepairPage()" class="max-w-7xl mx-auto">

    {{-- ===== HEADER ===== --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Create New Repair</h2>
                <p class="text-sm text-gray-500">Fill in the details to create a repair ticket</p>
            </div>
        </div>
        <a href="/repairs" class="btn-secondary inline-flex items-center gap-1.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Repairs
        </a>
    </div>

    {{-- ===== MAIN GRID ===== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

        {{-- LEFT: Customer + Device --}}
        <div class="xl:col-span-2 space-y-5">

            {{-- ── CUSTOMER CARD ── --}}
            <div class="bg-white rounded-2xl shadow-sm border relative z-10">
                <div class="px-6 py-4 border-b flex items-center gap-3"
                     style="background:linear-gradient(135deg,#a29bbf,#bdbdbd);">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Customer</h3>
                        <p class="text-xs text-gray-500">Search or add a new customer</p>
                    </div>
                </div>
                <div class="p-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Customer <span class="text-red-500">*</span>
                    </label>

                    {{-- Selected chip --}}
                    <div x-show="form.customer_id" class="mb-3 flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium border border-indigo-200 bg-indigo-50 text-indigo-800 w-fit">
                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-text="selectedCust?.name + (selectedCust?.mobile_number ? ' · ' + selectedCust.mobile_number : '')"></span>
                        <button @click="form.customer_id = null; selectedCust = null; custSearch = ''; $nextTick(() => $refs.custInput.focus())"
                            class="ml-1 text-indigo-400 hover:text-red-500 transition text-lg leading-none">&times;</button>
                    </div>

                    <div class="flex gap-2" x-show="!form.customer_id">
                        {{-- Search box --}}
                        <div class="relative flex-1" @click.away="custOpen = false; custResults = []">
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 pl-3 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input
                                x-ref="custInput"
                                x-model="custSearch"
                                @focus="searchCustomers(1)"
                                @input.debounce.300ms="searchCustomers(1)"
                                type="text"
                                class="form-input-custom pl-9 text-sm w-full"
                                placeholder="Search by name or mobile…"
                                :disabled="!!form.customer_id"
                            >
                            {{-- Dropdown --}}
                            <div x-show="custOpen && Array.isArray(custResults) && custResults.length > 0"
                                 class="absolute z-50 w-full bg-white border border-gray-200 rounded-xl shadow-xl mt-1 overflow-hidden"
                                 style="max-height:260px;">
                                <div class="overflow-y-auto" style="max-height:260px;" @scroll="handleCustScroll($event)">
                                    <template x-for="c in (Array.isArray(custResults) ? custResults : [])" :key="c.id">
                                        <button @click="selectCustomer(c)"
                                            class="w-full text-left px-4 py-3 hover:bg-indigo-50 border-b last:border-0 transition flex items-center justify-between gap-3">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-800" x-text="c.name"></div>
                                                <div class="text-xs text-gray-400 mt-0.5" x-text="c.mobile_number || ''"></div>
                                            </div>
                                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    </template>
                                    <div x-show="custLoading" class="px-4 py-3 text-xs text-gray-400 text-center flex items-center justify-center gap-2">
                                        <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                        Loading…
                                    </div>
                                    <div x-show="!custLoading && custResults.length === 0 && custSearch.length > 0"
                                         class="px-4 py-3 text-xs text-gray-400 text-center">
                                        No customers found
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Add new customer button --}}
                        <button type="button"
                            @click="showAddCust = true; newCust = {name:'', mobile_number:'', email:'', address:''}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold border border-indigo-200 text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            New Customer
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── DEVICE CARD ── --}}
            <div class="bg-white rounded-2xl shadow-sm border">
                <div class="px-6 py-4 border-b flex items-center gap-3"
                     style="background:linear-gradient(135deg,#d1fae5,#dbeafe);">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Device Information</h3>
                        <p class="text-xs text-gray-500">Brand, model and serial number</p>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Brand --}}
                    <div x-data="{
                            brandOpen: false,
                            brandSearch: '',
                            get filteredBrands() {
                                const q = this.brandSearch.toLowerCase();
                                return this.brandList.filter(b => b.toLowerCase().includes(q));
                            }
                         }"
                         x-init="$watch('form.device_brand', v => brandSearch = v)"
                         @click.away="brandOpen = false"
                         class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                Device Brand
                            </span>
                        </label>
                        <input x-model="form.device_brand"
                            @focus="brandOpen = true; brandSearch = form.device_brand"
                            @input="brandOpen = true; brandSearch = form.device_brand"
                            type="text"
                            class="form-input-custom text-sm w-full"
                            placeholder="e.g. Samsung, Apple, Xiaomi"
                            autocomplete="off">
                        <div x-show="brandOpen && filteredBrands.length > 0" x-cloak
                             class="absolute z-20 w-full bg-white border rounded-xl shadow-lg mt-1 max-h-44 overflow-y-auto">
                            <template x-for="b in filteredBrands" :key="b">
                                <button type="button" @click="form.device_brand = b; brandOpen = false"
                                    class="w-full text-left px-4 py-2.5 hover:bg-emerald-50 text-sm border-b last:border-0 transition font-medium text-gray-700" x-text="b"></button>
                            </template>
                        </div>
                    </div>

                    {{-- Model --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                                Device Model
                            </span>
                        </label>
                        <input x-model="form.device_model" type="text" class="form-input-custom text-sm w-full" placeholder="e.g. Galaxy S24, iPhone 15">
                    </div>

                    {{-- IMEI --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2V9M9 21H5a2 2 0 01-2-2V9m0 0h18"/></svg>
                                IMEI / Serial No.
                            </span>
                        </label>
                        <input x-model="form.imei" type="text" class="form-input-custom text-sm w-full" placeholder="Device IMEI or serial number">
                    </div>

                    {{-- Estimated Cost --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Estimated Cost (₹)
                            </span>
                        </label>
                        <input x-model="form.estimated_cost" type="number" step="0.01" class="form-input-custom text-sm w-full" placeholder="0.00">
                    </div>

                    {{-- Problem Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Problem Description
                            </span>
                        </label>
                        <textarea x-model="form.problem_description"
                            class="form-input-custom text-sm w-full"
                            rows="3"
                            placeholder="Describe the issue in detail…"></textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT: Scheduling + Advance + Submit --}}
        <div class="space-y-5">

            {{-- ── SCHEDULING CARD ── --}}
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center gap-3"
                     style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                    <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Scheduling</h3>
                        <p class="text-xs text-gray-500">Technician & delivery date</p>
                    </div>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Expected Delivery Date</label>
                        <input x-model="form.expected_delivery_date" type="date" class="form-input-custom text-sm w-full">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Technician</label>
                        <select x-model="form.technician_id" class="form-select-custom text-sm w-full">
                            <option value="">— Unassigned —</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ── ADVANCE PAYMENT CARD ── --}}
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
                <div class="px-5 py-4 border-b flex items-center gap-3"
                     style="background:linear-gradient(135deg,#d1fae5,#a7f3d0);">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Advance Payment</h3>
                        <p class="text-xs text-gray-500">Optional — collected upfront</p>
                    </div>
                </div>
                <div class="p-5 space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Amount (₹)</label>
                        <input x-model="form.advance_amount" type="number" step="0.01" class="form-input-custom text-sm w-full" placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Payment Method</label>
                        <select x-model="form.advance_method" class="form-select-custom text-sm w-full">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 uppercase mb-1.5">Reference / Transaction ID</label>
                        <input x-model="form.advance_reference" type="text" class="form-input-custom text-sm w-full" placeholder="Optional">
                    </div>
                </div>
            </div>

            {{-- ── CREATE BUTTON ── --}}
            <div class="bg-white rounded-2xl shadow-sm border p-5">
                <p class="text-xs text-gray-500 mb-4 text-center">Review all details before submitting</p>
                <button @click="save()"
                    :disabled="saving"
                    class="w-full inline-flex items-center justify-center gap-2 py-3 rounded-xl text-sm font-bold transition-all duration-200"
                    :style="{
                        background: 'linear-gradient(135deg,#6366f1,#8b5cf6)',
                        boxShadow: '0 4px 14px rgba(99,102,241,0.35)',
                        color: '#ffffff',
                        opacity: saving ? '0.7' : '1',
                        cursor: saving ? 'not-allowed' : 'pointer'
                    }">
                    <template x-if="saving">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </template>
                    <template x-if="!saving">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </template>
                    <span x-text="saving ? 'Creating…' : 'Create Repair Ticket'"></span>
                </button>
                <a href="/repairs"
                   class="mt-3 block text-center text-sm text-gray-400 hover:text-gray-600 transition">
                    Cancel &amp; go back
                </a>
            </div>

        </div>{{-- end right --}}
    </div>{{-- end main grid --}}

    {{-- ===== ADD CUSTOMER MODAL ===== --}}
    <div x-show="showAddCust" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showAddCust = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Add New Customer
                </h3>
                <button @click="showAddCust = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input x-model="newCust.name" type="text" class="form-input-custom" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                    <input x-model="newCust.mobile_number" type="text" class="form-input-custom" placeholder="+91 9876543210">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input x-model="newCust.email" type="email" class="form-input-custom" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input x-model="newCust.address" type="text" class="form-input-custom" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showAddCust = false" class="btn-secondary">Cancel</button>
                <button @click="saveNewCust()" class="btn-primary inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save &amp; Select
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function createRepairPage() {
    return {
        saving: false,
        showAddCust: false,

        // Customer search state
        custSearch: '',
        custResults: [],
        custOpen: false,
        custHasMore: false,
        custPage: 1,
        custLoading: false,
        selectedCust: null,

        newCust: { name: '', mobile_number: '', email: '', address: '' },
        brandList: @json($brands),

        form: {
            customer_id: null,
            technician_id: '',
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

        // ── CUSTOMER SEARCH (paginated, lazy) ──
        async searchCustomers(page) {
            page = page || 1;
            if (page === 1) { this.custPage = 1; }
            this.custLoading = true;
            const url = '/customers-search?page=' + page + '&q=' + encodeURIComponent(this.custSearch || '');
            const r = await RepairBox.ajax(url);
            this.custLoading = false;
            const rows = Array.isArray(r.data) ? r.data : [];
            this.custResults = page === 1 ? rows : (Array.isArray(this.custResults) ? this.custResults : []).concat(rows);
            this.custHasMore = r.has_more || false;
            this.custPage = page;
            if (this.custResults.length > 0) this.custOpen = true;
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
            this.custResults = [];
            this.custOpen = false;
            this.custSearch = '';
        },

        async saveNewCust() {
            if (!this.newCust.name || !this.newCust.mobile_number) {
                RepairBox.toast('Name and mobile are required', 'error');
                return;
            }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCust);
            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.showAddCust = false;
                RepairBox.toast('Customer added & selected', 'success');
            }
        },

        async save() {
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }
            this.saving = true;
            const r = await RepairBox.ajax('/repairs', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Repair created: ' + r.data.ticket_number, 'success');
                window.location.href = '/repairs/' + r.data.id;
            }
        },
    };
}
</script>
@endpush
