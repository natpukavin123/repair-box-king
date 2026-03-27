@extends('layouts.app')
@section('page-title', 'Add Vendor')

@section('content')
<div x-data="createVendorPage()" class="max-w-2xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Vendor</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new vendor record</p>
        </div>
        <a href="/vendors" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <x-ui.form-section title="Vendor Details" description="Capture the supplier-side contact and specialization details in the shared form layout.">
                <x-ui.input-field label="Name" x-model="form.name" required />
                <x-ui.input-field label="Phone" x-model="form.phone" />
                <x-ui.input-field label="Specialization" x-model="form.specialization" />
                <x-ui.textarea-field label="Address" x-model="form.address" rows="2" />
            </x-ui.form-section>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/vendors" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Vendor
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createVendorPage() {
    return {
        saving: false,
        form: { name: '', phone: '', address: '', specialization: '' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/admin/vendors', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Vendor created', 'success'); window.location.href = '/admin/vendors'; }
        }
    };
}
</script>
@endpush
