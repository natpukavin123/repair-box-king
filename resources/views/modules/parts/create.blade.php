@extends('layouts.app')
@section('page-title', 'Add Part')

@section('content')
<div x-data="createPartPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Part</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new repair part</p>
        </div>
        <a href="/parts" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="form.name" type="text" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input x-model="form.sku" type="text" class="form-input-custom">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                        <input x-model="form.cost_price" type="number" step="0.01" class="form-input-custom">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                        <input x-model="form.selling_price" type="number" step="0.01" class="form-input-custom">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/parts" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Part
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createPartPage() {
    return {
        saving: false,
        form: { name: '', sku: '', cost_price: '', selling_price: '' },

        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/parts', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Part created', 'success'); window.location.href = '/parts'; }
        }
    };
}
</script>
@endpush
