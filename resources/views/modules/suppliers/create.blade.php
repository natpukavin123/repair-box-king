@extends('layouts.app')
@section('page-title', 'Add Supplier')

@section('content')
<div x-data="createSupplierPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Supplier</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new supplier record</p>
        </div>
        <a href="/suppliers" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Company</label><input x-model="form.company_name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input x-model="form.phone" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="form.email" type="email" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">GST Number</label><input x-model="form.gst_number" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea x-model="form.address" class="form-input-custom" rows="2"></textarea></div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/suppliers" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Supplier
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createSupplierPage() {
    return {
        saving: false,
        form: { name: '', company_name: '', email: '', phone: '', address: '', gst_number: '' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/suppliers', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Supplier created', 'success'); window.location.href = '/suppliers'; }
        }
    };
}
</script>
@endpush
