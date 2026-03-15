@extends('layouts.app')
@section('page-title', 'Add Category')

@section('content')
<div x-data="createCategoryPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Category</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new product category</p>
        </div>
        <a href="/categories" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="3"></textarea></div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/categories" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Category
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createCategoryPage() {
    return {
        saving: false,
        form: { name: '', description: '' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/categories', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Category created', 'success'); window.location.href = '/categories'; }
        }
    };
}
</script>
@endpush
