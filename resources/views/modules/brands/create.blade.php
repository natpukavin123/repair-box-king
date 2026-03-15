@extends('layouts.app')
@section('page-title', 'Add Brand')

@section('content')
<div x-data="createBrandPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Brand</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new product brand</p>
        </div>
        <a href="/brands" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Logo URL</label><input x-model="form.logo_url" type="text" class="form-input-custom" placeholder="https://..."></div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/brands" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Brand
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createBrandPage() {
    return {
        saving: false,
        form: { name: '', logo_url: '' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/brands', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Brand created', 'success'); window.location.href = '/brands'; }
        }
    };
}
</script>
@endpush
