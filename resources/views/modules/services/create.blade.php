@extends('layouts.app')
@section('page-title', 'New Service')

@section('content')
<div x-data="createServicePage()" class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">New Service</h2>
            <p class="text-sm text-gray-500 mt-0.5">Record a new service entry</p>
        </div>
        <a href="/services" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type *</label>
                    <select x-model="form.service_type_id" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($serviceTypes as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer *</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1" @click.away="custOpen = false">
                            <input x-model="custSearch" @focus="findCust(1)" @input.debounce.300ms="findCust(1)" type="text" class="form-input-custom" placeholder="Search customer...">
                            <div x-show="custOpen && custResults.length > 0" x-cloak class="absolute left-0 right-0 mt-1 border rounded-lg bg-white shadow-lg overflow-hidden" style="z-index:50">
                                <div class="max-h-48 overflow-y-auto" @scroll="handleCustScroll($event)">
                                    <template x-for="c in custResults" :key="c.id">
                                        <button @click="selectCust(c)" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b" x-text="c.name + ' - ' + (c.mobile_number || '')"></button>
                                    </template>
                                    <div x-show="custLoading" class="px-3 py-2 text-xs text-gray-400 text-center flex items-center justify-center gap-2"><svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>Loading…</div>
                                </div>
                            </div>
                        </div>
                        <button type="button" @click="showAddCust = true; newCust = {name:'', mobile_number:'', email:'', address:''}" class="btn-primary text-sm px-3 whitespace-nowrap">+ New</button>
                    </div>
                    <div x-show="custOpen && !custLoading && custResults.length === 0" class="text-xs text-gray-400 mt-1">No customers found.</div>
                    <div x-show="selCust" class="mt-1"><span class="badge badge-primary" x-text="selCust?.name"></span> <button @click="selCust = null; form.customer_id = null" class="text-red-400 text-xs">&times;</button></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Customer Charge *</label><input x-model="form.customer_charge" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Vendor Cost</label><input x-model="form.vendor_cost" type="number" step="0.01" class="form-input-custom"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select x-model="form.payment_method" class="form-select-custom">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select x-model="form.status" class="form-select-custom">
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/services" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Service
            </button>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div x-show="showAddCust" class="modal-overlay" x-cloak @click.self="showAddCust = false">
        <div class="modal-container max-w-md">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Customer</h3><button @click="showAddCust = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-3">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="newCust.name" type="text" class="form-input-custom" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile * <span class="text-xs text-gray-500">(10 digits)</span></label><input x-model="newCust.mobile_number" type="text" class="form-input-custom" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()"><p x-show="newCust.mobile_number && !/^\d{10}$/.test(newCust.mobile_number)" class="text-xs text-red-500 mt-1">Mobile must be exactly 10 digits</p></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="newCust.email" type="email" class="form-input-custom"><p x-show="newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCust.email)" class="text-xs text-red-500 mt-1">Please enter a valid email</p></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input x-model="newCust.address" type="text" class="form-input-custom"></div>
                </div>
            </div>
            <div class="modal-footer"><button @click="showAddCust = false" class="btn-secondary">Cancel</button><button @click="saveNewCust()" class="btn-primary" :disabled="!newCust.name.trim() || !/^\d{10}$/.test(newCust.mobile_number) || (newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCust.email))">Save & Select</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createServicePage() {
    return {
        saving: false,
        custSearch: '', custResults: [], custOpen: false, custHasMore: false, custPage: 1, custLoading: false, selCust: null,
        showAddCust: false, newCust: {name: '', mobile_number: '', email: '', address: ''},
        form: { service_type_id: '', customer_id: null, description: '', customer_charge: '', vendor_cost: '', payment_method: 'cash', status: 'completed' },
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
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.custHasMore && !this.custLoading) { this.findCust(this.custPage + 1); }
        },
        selectCust(c) { this.selCust = c; this.form.customer_id = c.id; this.custResults = []; this.custOpen = false; this.custSearch = ''; },
        async saveNewCust() {
            if (!this.newCust.name.trim()) {
                RepairBox.toast('Name is required', 'error');
                return;
            }
            const mobile = this.newCust.mobile_number.trim();
            if (!mobile) {
                RepairBox.toast('Mobile number is required', 'error');
                return;
            }
            if (!/^\d{10}$/.test(mobile)) {
                RepairBox.toast('Mobile must be exactly 10 digits', 'error');
                return;
            }
            if (this.newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.newCust.email)) {
                RepairBox.toast('Please enter a valid email address', 'error');
                return;
            }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCust);
            if (r.success !== false && r.data) { this.selCust = r.data; this.form.customer_id = r.data.id; this.custResults = []; this.custSearch = ''; this.showAddCust = false; RepairBox.toast('Customer added', 'success'); }
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/services', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Service created', 'success'); window.location.href = '/services'; }
        }
    };
}
</script>
@endpush
