@extends('layouts.app')
@section('page-title', 'Add Customer')

@section('content')
<div x-data="createCustomerPage()" class="max-w-2xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Customer</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new customer record</p>
        </div>
        <a href="/customers" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <div x-show="submitError" x-text="submitError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"></div>
            <x-ui.form-section title="Customer Details" description="Capture the basic contact information used across billing and repairs.">
                <div class="workspace-field">
                    <x-ui.input-field label="Name" x-model="form.name" required />
                    <p x-show="formTried && !form.name.trim()" class="workspace-field-hint text-red-500">Name is required</p>
                </div>
                <div class="workspace-field">
                    <label class="form-label">Mobile <span class="text-red-500">*</span> <span class="text-xs text-gray-500">(10 digits)</span></label>
                    <input x-model="form.mobile_number" type="text" class="form-input-custom" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" @input="form.mobile_number = RepairBox.normalizeCustomerMobile(form.mobile_number)" @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()">
                    <p x-show="formTried && !form.mobile_number.trim()" class="workspace-field-hint text-red-500">Mobile number is required</p>
                    <p x-show="(formTried || form.mobile_number) && form.mobile_number.trim() && !/^\d{10}$/.test(form.mobile_number.trim())" class="workspace-field-hint text-red-500">Mobile must be exactly 10 digits</p>
                </div>
                <div class="workspace-field">
                    <x-ui.input-field label="Email" x-model="form.email" type="email" />
                    <p x-show="(formTried || form.email) && form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())" class="workspace-field-hint text-red-500">Please enter a valid email</p>
                </div>
                <x-ui.textarea-field label="Address" x-model="form.address" rows="2" />
            </x-ui.form-section>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/customers" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Customer
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createCustomerPage() {
    return {
        saving: false,
        formTried: false,
        submitError: '',
        form: { name: '', mobile_number: '', email: '', address: '' },
        async save() {
            this.formTried = true;
            this.submitError = '';

            const validation = RepairBox.validateCustomerPayload(this.form);
            this.form = {
                ...this.form,
                ...validation.payload,
                email: validation.payload.email || '',
                address: validation.payload.address || '',
            };

            if (!validation.valid) {
                return;
            }

            this.saving = true;
            const r = await RepairBox.ajax('/customers', 'POST', validation.payload);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Customer created', 'success'); window.location.href = '/customers'; return; }
            this.submitError = r.message || 'Unable to save customer. Please check the details and try again.';
        }
    };
}
</script>
@endpush
