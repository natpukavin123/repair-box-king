@extends('layouts.app')
@section('page-title', 'New Service')

@section('content')
<div x-data="createServicePage()" class="max-w-3xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">New Service</h2>
            <p class="text-sm text-gray-500 mt-0.5">Record a new service entry</p>
        </div>
        <a href="/services" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <x-ui.form-section title="Service Booking" description="Primary service fields follow the same reusable pattern as the rest of the app.">
                <x-ui.select-field label="Service Type" x-model="form.service_type_id" required>
                    <option value="">Select</option>
                    @foreach($serviceTypes as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </x-ui.select-field>
                <div class="workspace-field md:col-span-2">
                    <label class="form-label">Customer <span class="text-red-500">*</span></label>
                    <div class="flex flex-col gap-2 sm:flex-row">
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
                        <button type="button" @click="openAddCustModal()" class="btn-primary text-sm px-3 whitespace-nowrap w-full sm:w-auto">+ New</button>
                    </div>
                    <div x-show="custOpen && !custLoading && custResults.length === 0" class="text-xs text-gray-400 mt-1">No customers found.</div>
                    <div x-show="selCust" class="mt-1"><span class="badge badge-primary" x-text="selCust?.name"></span> <button @click="selCust = null; form.customer_id = null" class="text-red-400 text-xs">&times;</button></div>
                </div>
                <x-ui.input-field label="Customer Charge" x-model="form.customer_charge" type="number" step="0.01" required />
                <x-ui.input-field label="Vendor Cost" x-model="form.vendor_cost" type="number" step="0.01" />
                <x-ui.select-field label="Payment Method" x-model="form.payment_method">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="upi">UPI</option>
                </x-ui.select-field>
                <x-ui.select-field label="Status" x-model="form.status">
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                </x-ui.select-field>
            </x-ui.form-section>

            <x-ui.form-section title="Description" description="Optional service note for follow-up or billing reference." gridClass="grid grid-cols-1 gap-4">
                <x-ui.textarea-field label="Description" x-model="form.description" rows="3" />
            </x-ui.form-section>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/services" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Service
            </button>
        </div>
    </div>

    <!-- Add Customer Modal -->
    <div x-show="showAddCust" class="modal-overlay" x-cloak @click.self="closeAddCustModal()">
        <div class="modal-container max-w-md">
            <div class="modal-header"><h3 class="text-lg font-semibold">Add Customer</h3><button @click="closeAddCustModal()" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <x-ui.form-section title="Customer Details" description="Create and select a customer without leaving the service form.">
                    <div class="workspace-field" x-show="customerSubmitError">
                        <div x-text="customerSubmitError" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                    </div>
                    <div class="workspace-field">
                        <x-ui.input-field label="Name" x-model="newCust.name" required />
                        <p x-show="customerFormTried && !newCust.name.trim()" class="workspace-field-hint text-red-500">Name is required</p>
                    </div>
                    <div class="workspace-field">
                        <label class="form-label">Mobile <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(10 digits)</span></label>
                        <input x-model="newCust.mobile_number" type="text" class="form-input-custom" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" required @input="newCust.mobile_number = RepairBox.normalizeCustomerMobile(newCust.mobile_number)" @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()">
                        <p x-show="customerFormTried && !newCust.mobile_number.trim()" class="workspace-field-hint text-red-500">Mobile number is required</p>
                        <p x-show="(customerFormTried || newCust.mobile_number) && newCust.mobile_number.trim() && !/^\d{10}$/.test(newCust.mobile_number.trim())" class="workspace-field-hint text-red-500">Mobile must be exactly 10 digits</p>
                    </div>
                    <div class="workspace-field">
                        <x-ui.input-field label="Email" x-model="newCust.email" type="email" />
                        <p x-show="(customerFormTried || newCust.email) && newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCust.email.trim())" class="workspace-field-hint text-red-500">Please enter a valid email</p>
                    </div>
                    <x-ui.input-field label="Address" x-model="newCust.address" />
                </x-ui.form-section>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end"><button @click="closeAddCustModal()" class="btn-secondary w-full sm:w-auto">Cancel</button><button @click="saveNewCust()" class="btn-primary w-full sm:w-auto" :disabled="customerSaving"><span x-text="customerSaving ? 'Saving...' : 'Save & Select'"></span></button></div>
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
        customerFormTried: false, customerSaving: false, customerSubmitError: '',
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

            if (r.success !== false && r.data) { this.selCust = r.data; this.form.customer_id = r.data.id; this.custResults = []; this.custSearch = ''; this.closeAddCustModal(); this.newCust = RepairBox.emptyCustomer(); RepairBox.toast('Customer added', 'success'); return; }
            this.customerSubmitError = r.message || 'Unable to save customer. Please check the details and try again.';
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
