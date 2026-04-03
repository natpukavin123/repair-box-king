@extends('layouts.app')
@section('page-title', 'Create Repair')

@section('content')
<div x-data="createRepairPage()" class="max-w-4xl mx-auto">

    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">Create New Repair</h2>
            <p class="mt-1 text-sm text-slate-500">Fill only the needed details first. Optional details can be added in the last step.</p>
        </div>
        <a href="/admin/repairs" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 text-sm sm:w-auto">
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
                                <div x-data="brandDropdown(brandList, (v) => { form.device_brand = v; form.device_model = ''; modelOpen = false; })" x-effect="syncValue(form.device_brand)" @click.outside="open = false" class="relative">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Device Brand <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="query" @focus="open = true" @input="open = true; selected = query; updateValue(query)" @keydown.arrow-down.prevent="highlightNext()" @keydown.arrow-up.prevent="highlightPrev()" @keydown.enter.prevent="selectHighlighted()" @keydown.escape="open = false" class="form-input-custom w-full text-sm" placeholder="Type to search brands..." autocomplete="off">
                                    <div x-show="open && filtered.length > 0" x-cloak class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                                        <template x-for="(brand, idx) in filtered" :key="brand">
                                            <div @click="pick(brand)" :class="idx === highlighted ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50'" class="cursor-pointer px-3 py-2 text-sm" x-text="brand"></div>
                                        </template>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Device Model <span class="text-red-500">*</span></label>
                                    <div class="relative" @click.away="modelOpen = false">
                                        <input x-model="form.device_model" type="text"
                                            @focus="modelOpen = currentModels().length > 0"
                                            @input="modelOpen = true"
                                            @keydown.escape="modelOpen = false"
                                            @keydown.enter.prevent="if(filteredModels().length===1){ pickModel(filteredModels()[0]) }"
                                            class="form-input-custom w-full text-sm" placeholder="Galaxy S24, iPhone 15, Redmi Note 13" autocomplete="off">
                                        <div x-show="modelOpen && filteredModels().length > 0" x-cloak
                                            class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                                            <template x-for="m in filteredModels()" :key="m">
                                                <div @mousedown.prevent="pickModel(m)" class="cursor-pointer px-3 py-2 text-sm text-slate-700 hover:bg-slate-50" x-text="m"></div>
                                            </template>
                                        </div>
                                    </div>
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

                            {{-- Added issue chips --}}
                            <div class="flex flex-wrap gap-2 mb-3" x-show="problemIssues.length > 0">
                                <template x-for="(issue, idx) in problemIssues" :key="idx">
                                    <span class="inline-flex items-center gap-1 rounded-full border border-amber-300 bg-amber-50 px-3 py-1.5 text-sm font-medium text-amber-800">
                                        <span x-text="issue"></span>
                                        <button type="button" @click="removeProblemIssue(idx)" class="ml-1 text-amber-400 hover:text-red-500 text-lg leading-none font-bold">&times;</button>
                                    </span>
                                </template>
                            </div>

                            {{-- Input + autocomplete dropdown --}}
                            <div class="relative">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <input
                                            type="text"
                                            x-model="problemQuery"
                                            @input="problemShowSugg = true"
                                            @keydown.enter.prevent="addProblemIssue()"
                                            @keydown.escape="problemQuery = ''"
                                            @blur="setTimeout(() => { problemShowSugg = false }, 150)"
                                            class="form-input-custom w-full text-sm"
                                            placeholder="Type issue, press Enter to add — e.g. screen cracked">
                                        <div x-show="problemQuery.trim().length > 0 && getSuggestions().length > 0" x-cloak
                                            class="absolute z-50 left-0 right-0 mt-1 rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden">
                                            <template x-for="s in getSuggestions()" :key="s">
                                                <button type="button" @mousedown.prevent="pickSuggestion(s)"
                                                    class="flex w-full items-center gap-2 border-b border-slate-100 px-4 py-2.5 text-left text-sm text-slate-700 hover:bg-amber-50 hover:text-amber-800 transition last:border-0">
                                                    <svg class="w-3.5 h-3.5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span x-text="s"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <button type="button" @click="addProblemIssue()"
                                        class="shrink-0 inline-flex items-center gap-1 rounded-xl bg-amber-500 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        Add
                                    </button>
                                </div>
                            </div>

                            {{-- Quick-pick suggestion pills --}}
                            <div class="mt-3">
                                <p class="text-xs text-slate-400 mb-2">Common issues — tap to add quickly:</p>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="s in getQuickSuggestions()" :key="s">
                                        <button type="button" @mousedown.prevent="pickSuggestion(s)"
                                            class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs text-slate-600 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-700 transition"
                                            x-text="s"></button>
                                    </template>
                                </div>
                            </div>
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
                <a href="/admin/repairs" class="btn-secondary w-full sm:w-auto text-center">Cancel</a>
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
        brandModelMap: @json($brandModelMap),
        brandList: @json($brands),
        modelOpen: false,

        // Problem description — chip / tag state
        problemIssues: [],
        problemQuery: '',
        problemShowSugg: false,
        allSuggestions: [
            'Screen cracked','Display not working','Touch not responding','Display flickering',
            'Black screen','LCD damaged','Display lines / spots','Ghost touch',
            'Battery draining fast','Battery not charging','Battery swollen','Phone not turning on',
            'Charging port not working','Charging very slow','USB port damaged',
            'Speaker not working','Microphone not working','Earpiece not working','No sound',
            'Sound distorted','Headphone jack not working',
            'Camera not working','Camera blurry','Front camera not working','Flash not working',
            'Camera app crashing','Video recording issue',
            'No network signal','SIM not detecting','WiFi not connecting','Bluetooth not working',
            'Mobile data not working','Call keeps dropping',
            'Water damage','Phone overheating','Phone hanging / freezing','Phone restarting itself',
            'Fingerprint not working','Face unlock issue','Power button not working',
            'Volume button not working','Home button not working','Vibration not working',
            'Software issue / stuck','App crashing','Phone running slow','Storage full',
        ],

        getSuggestions() {
            if (!this.problemQuery.trim()) return [];
            const q = this.problemQuery.toLowerCase();
            return this.allSuggestions
                .filter(s => s.toLowerCase().includes(q) && !this.problemIssues.includes(s))
                .slice(0, 8);
        },

        getQuickSuggestions() {
            return this.allSuggestions.filter(s => !this.problemIssues.includes(s)).slice(0, 14);
        },

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
            if (step === 2) return !!this.form.device_brand.trim() && !!this.form.device_model.trim() && (this.problemIssues.length > 0 || !!this.form.problem_description.trim());
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
                if (this.problemIssues.length === 0 && !this.form.problem_description.trim()) { RepairBox.toast('Problem description is required', 'error'); return; }
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
            const r = await RepairBox.ajax('/admin/customers-search?page=' + page + '&q=' + encodeURIComponent(this.custSearch || ''));
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

        addProblemIssue() {
            const v = this.problemQuery.trim();
            if (!v) return;
            if (!this.problemIssues.includes(v)) {
                this.problemIssues.push(v);
                this.form.problem_description = this.problemIssues.join(', ');
            }
            this.problemQuery = '';
            this.problemShowSugg = false;
        },

        pickSuggestion(s) {
            if (!this.problemIssues.includes(s)) {
                this.problemIssues.push(s);
                this.form.problem_description = this.problemIssues.join(', ');
            }
            this.problemQuery = '';
            this.problemShowSugg = false;
        },

        removeProblemIssue(idx) {
            this.problemIssues.splice(idx, 1);
            this.form.problem_description = this.problemIssues.join(', ');
        },

        currentModels() {
            const bm = this.brandModelMap.find(b => b.name === this.form.device_brand);
            return (bm && bm.models) ? bm.models : [];
        },
        filteredModels() {
            const q = (this.form.device_model || '').toLowerCase();
            return this.currentModels().filter(m => !q || m.toLowerCase().includes(q));
        },
        pickModel(m) {
            this.form.device_model = m;
            this.modelOpen = false;
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
            const r = await RepairBox.ajax('/admin/customers', 'POST', validation.payload);
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
            // auto-add any typed-but-not-yet-added query
            if (this.problemQuery.trim()) this.addProblemIssue();
            if (this.problemIssues.length === 0 && !this.form.problem_description.trim()) { RepairBox.toast('Problem description is required', 'error'); this.currentStep = 2; return; }

            this.saving = true;
            const r = await RepairBox.ajax('/admin/repairs', 'POST', this.form);
            this.saving = false;

            if (r.success !== false) {
                RepairBox.toast('Repair created: ' + r.data.ticket_number, 'success');
                window.location.href = '/admin/repairs/' + r.data.id;
            }
        }
    };
}
</script>

<script>
function brandDropdown(brands, onChange) {
    return {
        open: false,
        query: '',
        selected: '',
        highlighted: -1,
        brands: brands,
        filtered: [],
        init() {
            this.filtered = this.brands ? this.brands.slice() : [];
            this.$watch('query', (val) => {
                const q = val.trim().toLowerCase();
                this.filtered = q ? this.brands.filter(b => b.toLowerCase().includes(q)) : this.brands.slice();
            });
        },
        syncValue(val) {
            if ((val || '') !== this.selected) {
                this.query = val || '';
                this.selected = val || '';
            }
        },
        pick(brand) {
            this.query = brand;
            this.selected = brand;
            this.open = false;
            this.highlighted = -1;
            onChange(brand);
        },
        updateValue(val) { onChange(val); },
        highlightNext() {
            if (this.filtered.length === 0) return;
            this.highlighted = (this.highlighted + 1) % this.filtered.length;
            this.scrollToHighlighted();
        },
        highlightPrev() {
            if (this.filtered.length === 0) return;
            this.highlighted = this.highlighted <= 0 ? this.filtered.length - 1 : this.highlighted - 1;
            this.scrollToHighlighted();
        },
        selectHighlighted() {
            if (this.highlighted >= 0 && this.highlighted < this.filtered.length) {
                this.pick(this.filtered[this.highlighted]);
            }
        },
        scrollToHighlighted() {
            this.$nextTick(() => {
                const container = this.$el.querySelector('.overflow-y-auto');
                const item = container?.children[this.highlighted];
                if (item) item.scrollIntoView({ block: 'nearest' });
            });
        }
    };
}
</script>
@endpush
