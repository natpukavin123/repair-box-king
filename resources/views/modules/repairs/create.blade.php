@extends('layouts.app')
@section('page-title', 'Create Repair')

@section('content')
<div x-data="createRepairPage()" class="max-w-4xl mx-auto">

    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Create New Repair</h2>
            <p class="mt-1 text-sm text-slate-500">Fill only the needed details first. Optional details can be added in the last step.</p>
        </div>
        <a href="/repairs" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 text-sm sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Repairs
        </a>
    </div>

    <div class="mb-5 rounded-2xl border border-slate-200 bg-white p-3 sm:p-4">
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
            <template x-for="step in steps" :key="step.id">
                <button type="button"
                    @click="goToStep(step.id)"
                    class="rounded-xl border px-4 py-3 text-left transition"
                    :class="currentStep === step.id ? 'border-primary-600 bg-primary-600 text-white shadow-sm' : (isStepComplete(step.id) ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-slate-200 bg-slate-50 text-slate-700')">
                    <div class="text-xs font-semibold" x-text="'Step ' + step.id"></div>
                    <div class="mt-1 text-sm font-semibold" x-text="step.title"></div>
                </button>
            </template>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white shadow-[0_24px_70px_-48px_rgba(15,23,42,0.35)]">
        <div class="border-b border-slate-200 bg-white px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900" x-text="currentStepMeta.title"></h3>
            <p class="mt-1 text-sm text-slate-500" x-text="currentStepMeta.description"></p>
        </div>

        <div class="p-6 space-y-6">
                <section x-show="currentStep === 1" x-cloak class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Choose the customer</h4>
                                <p class="text-sm text-slate-500">This is required so the ticket can be tracked and notified correctly.</p>
                            </div>
                        </div>

                        <div x-show="form.customer_id" class="mb-4 inline-flex max-w-full items-center gap-2 rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                            <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="truncate" x-text="selectedCust?.name + (selectedCust?.mobile_number ? ' · ' + selectedCust.mobile_number : '')"></span>
                            <button type="button" @click="clearSelectedCustomer()" class="text-slate-400 hover:text-red-500 text-lg leading-none">&times;</button>
                        </div>

                        <div x-show="!form.customer_id" class="flex flex-col gap-3 sm:flex-row">
                            <div class="relative flex-1" @click.away="custOpen = false; custResults = []">
                                <div class="absolute left-0 top-1/2 -translate-y-1/2 pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input x-ref="custInput"
                                    x-model="custSearch"
                                    @focus="searchCustomers(1)"
                                    @input.debounce.300ms="searchCustomers(1)"
                                    type="text"
                                    class="form-input-custom pl-10 text-base w-full min-h-[3.5rem]"
                                    placeholder="Search customer by name or mobile number">
                                <div x-show="custOpen && custResults.length > 0" x-cloak class="absolute z-50 mt-1 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                                    <div class="max-h-64 overflow-y-auto" @scroll="handleCustScroll($event)">
                                        <template x-for="c in custResults" :key="c.id">
                                            <button type="button" @click="selectCustomer(c)" class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 text-left hover:bg-slate-50 transition">
                                                <div>
                                                    <div class="text-base font-semibold text-slate-800" x-text="c.name"></div>
                                                    <div class="text-sm text-slate-500" x-text="c.mobile_number || ''"></div>
                                                </div>
                                                <svg class="w-4 h-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </template>
                                        <div x-show="custLoading" class="px-4 py-3 text-center text-xs text-slate-400">Loading…</div>
                                        <div x-show="!custLoading && custResults.length === 0 && custSearch.length > 0" class="px-4 py-3 text-center text-xs text-slate-400">No customers found</div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" @click="openAddCustModal()" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 min-h-[3.5rem] px-5 sm:w-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                New Customer
                            </button>
                        </div>

                        <p class="mt-3 text-xs text-slate-500">Search the customer or add a new one if not found.</p>
                    </div>
                </section>

                <section x-show="currentStep === 2" x-cloak class="space-y-6">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]">
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-600 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-slate-900">Identify the device</h4>
                                    <p class="text-sm text-slate-500">Enter the basic device details.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Device Brand <span class="text-red-500">*</span></label>
                                    <input x-model="form.device_brand" list="repair-create-brand-list" type="text" class="form-input-custom w-full text-sm" placeholder="Samsung, Apple, Xiaomi" autocomplete="off">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Device Model <span class="text-red-500">*</span></label>
                                    <input x-model="form.device_model" type="text" class="form-input-custom w-full text-sm" placeholder="Galaxy S24, iPhone 15, Redmi Note 13">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">IMEI / Serial No.</label>
                                    <input x-model="form.imei" type="text" class="form-input-custom w-full text-sm" placeholder="Optional serial or IMEI reference">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500 text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-base font-semibold text-slate-900">Describe the issue</h4>
                                    <p class="text-sm text-slate-500">Write the complaint in simple words.</p>
                                </div>
                            </div>

                            <label class="block text-sm font-semibold text-slate-700 mb-2">Problem Description <span class="text-red-500">*</span></label>
                            <textarea x-model="form.problem_description" class="form-input-custom w-full text-sm" rows="10" placeholder="Describe the issue clearly: no power, display broken, charging issue, speaker not working, water damage, etc."></textarea>
                        </div>
                    </div>
                </section>

                <section x-show="currentStep === 3" x-cloak class="space-y-6">
                    <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                        <button type="button" @click="optionalOpen = !optionalOpen" class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left hover:bg-slate-50 transition">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Optional Details</div>
                                <div class="mt-1 text-xs text-slate-500">Estimate, delivery date, and advance payment.</div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="optionalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="optionalOpen" x-cloak class="border-t border-slate-200 p-5">
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Expected Delivery Date</label>
                                    <input x-model="form.expected_delivery_date" type="date" class="form-input-custom w-full text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Estimated Cost</label>
                                    <input x-model="form.estimated_cost" type="number" step="0.01" class="form-input-custom w-full text-sm" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Advance Amount</label>
                                    <input x-model="form.advance_amount" type="number" step="0.01" class="form-input-custom w-full text-sm" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Payment Method</label>
                                    <select x-model="form.advance_method" class="form-select-custom w-full text-sm">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Reference / Transaction ID</label>
                                    <input x-model="form.advance_reference" type="text" class="form-input-custom w-full text-sm" placeholder="Optional">
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm text-slate-600">
                    Required: customer, device brand, device model, and problem description.
                </div>
        </div>

            <div class="border-t border-slate-200 bg-white px-6 py-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button type="button" @click="previousStep()" class="btn-secondary w-full sm:w-auto" :disabled="currentStep === 1">Previous</button>
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:items-center">
                <a href="/repairs" class="btn-secondary w-full sm:w-auto text-center">Cancel</a>
                <button type="button" x-show="currentStep < steps.length" @click="nextStep()" class="btn-primary w-full sm:w-auto">Continue</button>
                <button type="button" x-show="currentStep === steps.length" @click="save()" :disabled="saving" class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2">
                    <span x-show="saving" class="spinner"></span>
                    <span x-text="saving ? 'Creating...' : 'Create Repair Ticket'"></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="showAddCust" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="closeAddCustModal()">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-slate-900">Add New Customer</h3>
                <button @click="closeAddCustModal()" class="text-slate-400 hover:text-slate-600 text-xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-3">
                <div x-show="customerSubmitError" x-text="customerSubmitError" class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input x-model="newCust.name" type="text" class="form-input-custom" placeholder="John Doe">
                    <p x-show="customerFormTried && !newCust.name.trim()" class="text-xs text-red-500 mt-1">Name is required</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mobile Number <span class="text-red-500">*</span></label>
                    <input x-model="newCust.mobile_number" type="text" class="form-input-custom" placeholder="9876543210" inputmode="numeric" pattern="[0-9]{10}" maxlength="10"
                        @input="newCust.mobile_number = RepairBox.normalizeCustomerMobile(newCust.mobile_number)"
                        @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()">
                    <p x-show="customerFormTried && !newCust.mobile_number.trim()" class="text-xs text-red-500 mt-1">Mobile number is required</p>
                    <p x-show="(customerFormTried || newCust.mobile_number) && newCust.mobile_number.trim() && !/^\d{10}$/.test(newCust.mobile_number.trim())" class="text-xs text-red-500 mt-1">Mobile must be exactly 10 digits</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input x-model="newCust.email" type="email" class="form-input-custom" placeholder="Optional">
                    <p x-show="(customerFormTried || newCust.email) && newCust.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(newCust.email.trim())" class="text-xs text-red-500 mt-1">Please enter a valid email</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                    <input x-model="newCust.address" type="text" class="form-input-custom" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <button @click="closeAddCustModal()" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="saveNewCust()" class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2" :disabled="customerSaving">
                    <span x-show="customerSaving" class="spinner"></span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="customerSaving ? 'Saving...' : 'Save & Select'"></span>
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
        customerFormTried: false,
        customerSaving: false,
        customerSubmitError: '',
        currentStep: 1,
        optionalOpen: false,

        steps: [
            { id: 1, title: 'Customer', description: 'Select the customer for this repair ticket.' },
            { id: 2, title: 'Device & Issue', description: 'Enter the device details and the customer complaint.' },
            { id: 3, title: 'Optional Details', description: 'Add estimate, delivery date, or advance if needed.' },
        ],

        get currentStepMeta() {
            return this.steps.find((step) => step.id === this.currentStep) || this.steps[0];
        },

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

        isStepComplete(step) {
            if (step === 1) return !!this.form.customer_id;
            if (step === 2) return !!this.form.device_brand.trim() && !!this.form.device_model.trim() && !!this.form.problem_description.trim();
            return this.isStepComplete(1) && this.isStepComplete(2);
        },

        goToStep(step) {
            if (step <= this.currentStep || (step === 2 && this.isStepComplete(1)) || (step === 3 && this.isStepComplete(1) && this.isStepComplete(2))) {
                this.currentStep = step;
            }
        },

        previousStep() {
            if (this.currentStep > 1) this.currentStep -= 1;
        },

        nextStep() {
            if (this.currentStep === 1 && !this.form.customer_id) {
                RepairBox.toast('Please select a customer first', 'error');
                return;
            }
            if (this.currentStep === 2) {
                if (!this.form.device_brand.trim()) { RepairBox.toast('Device brand is required', 'error'); return; }
                if (!this.form.device_model.trim()) { RepairBox.toast('Device model is required', 'error'); return; }
                if (!this.form.problem_description.trim()) { RepairBox.toast('Problem description is required', 'error'); return; }
            }
            if (this.currentStep < this.steps.length) {
                this.currentStep += 1;
                if (this.currentStep === 3) {
                    this.optionalOpen = false;
                }
            }
        },

        clearSelectedCustomer() {
            this.form.customer_id = null;
            this.selectedCust = null;
            this.custSearch = '';
            this.$nextTick(() => this.$refs.custInput?.focus());
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

        handleCustScroll(event) {
            const element = event.target;
            if (element.scrollTop + element.clientHeight >= element.scrollHeight - 10 && this.custHasMore && !this.custLoading) {
                this.searchCustomers(this.custPage + 1);
            }
        },

        selectCustomer(customer) {
            this.selectedCust = customer;
            this.form.customer_id = customer.id;
            this.custSearch = '';
            this.custResults = [];
            this.custOpen = false;
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

            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.closeAddCustModal();
                this.newCust = RepairBox.emptyCustomer();
                RepairBox.toast('Customer added and selected', 'success');
                return;
            }

            this.customerSubmitError = r.message || 'Unable to save customer. Please check the details and try again.';
        },

        async save() {
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); this.currentStep = 1; return; }
            if (!this.form.device_brand.trim()) { RepairBox.toast('Device brand is required', 'error'); this.currentStep = 2; return; }
            if (!this.form.device_model.trim()) { RepairBox.toast('Device model is required', 'error'); this.currentStep = 2; return; }
            if (!this.form.problem_description.trim()) { RepairBox.toast('Problem description is required', 'error'); this.currentStep = 2; return; }

            this.saving = true;
            const r = await RepairBox.ajax('/repairs', 'POST', this.form);
            this.saving = false;

            if (r.success !== false) {
                RepairBox.toast('Repair created: ' + r.data.ticket_number, 'success');
                window.location.href = '/repairs/' + r.data.id;
            }
        }
    };
}
</script>

<datalist id="repair-create-brand-list">
    @foreach($brands as $brand)
        <option value="{{ $brand }}"></option>
    @endforeach
</datalist>
@endpush
