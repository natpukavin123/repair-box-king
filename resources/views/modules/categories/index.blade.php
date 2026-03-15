@extends('layouts.app')
@section('page-title', 'Categories')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="categoriesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <div></div>
        <a href="/categories/create" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Category</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th class="w-10">#</th><th>Name</th><th>Description</th><th>Subcategories</th><th class="w-32">Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr class="group">
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.name"></td>
                                <td class="text-gray-500 text-sm" x-text="item.description || '-'"></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <template x-if="(item.subcategories || []).length > 0">
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                                <span x-text="item.subcategories.length + ' subcategories'"></span>
                                            </span>
                                        </template>
                                        <a :href="'/categories/' + item.id + '/subcategories'" class="inline-flex items-center justify-center w-6 h-6 rounded-md text-primary-600 hover:text-white hover:bg-primary-600 bg-primary-50 transition-colors" title="Manage Subcategories">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </a>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(item)" class="text-primary-600 hover:text-primary-800 mr-2" title="Edit"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800" title="Delete"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="5" class="text-center text-gray-400 py-8">No categories found</td></tr>
                        <template x-if="loading">
                            <tr><td colspan="5" class="p-0"><template x-for="i in 10" :key="'sk'+i"><div class="skeleton-row"><div class="skeleton h-3 w-16"></div><div class="skeleton h-3 w-28"></div><div class="skeleton h-3 w-32"></div><div class="skeleton h-3 w-24"></div><div class="skeleton h-3 w-16"></div></div></template></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Edit Category</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input x-model="form.name" type="text" class="form-input-custom" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="form.description" class="form-input-custom" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span>
                    Update
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Subcategory Modal -->
    <div x-show="showSubModal" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-sm">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Edit Subcategory</h3>
                <button @click="showSubModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="subForm.name" type="text" class="form-input-custom" @keydown.enter="updateSub()">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showSubModal = false" class="btn-secondary">Cancel</button>
                <button @click="updateSub()" class="btn-primary" :disabled="savingSub">
                    <span x-show="savingSub" class="spinner mr-1"></span>
                    Update
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function categoriesPage() {
    return {
        items: [], showModal: false, editing: null, saving: false, loading: true,
        form: { name: '', description: '' },
        // Subcategory inline add
        addingSubFor: null, newSubName: '', savingSub: false,
        // Subcategory edit modal
        showSubModal: false, editingSubId: null, subForm: { name: '' },
        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/categories'); if(r.data) this.items = r.data;
            this.loading = false;
        },
        edit(item) {
            this.editing = item.id; this.form = { name: item.name, description: item.description || '' }; this.showModal = true;
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/categories/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Category updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this category?')) return;
            const r = await RepairBox.ajax(`/categories/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Category deleted', 'success'); this.load(); }
        },
        // Subcategory methods
        startAddSub(item) {
            this.addingSubFor = item.id; this.newSubName = '';
            this.$nextTick(() => { if (this.$refs.subInput) this.$refs.subInput.focus(); });
        },
        async saveSub(item) {
            if (!this.newSubName.trim()) return;
            this.savingSub = true;
            const r = await RepairBox.ajax('/subcategories', 'POST', { category_id: item.id, name: this.newSubName.trim() });
            this.savingSub = false;
            if (r.success !== false) { RepairBox.toast('Subcategory added', 'success'); this.addingSubFor = null; this.newSubName = ''; this.load(); }
        },
        editSub(item, sub) {
            this.editingSubId = sub.id; this.subForm = { name: sub.name, category_id: item.id }; this.showSubModal = true;
        },
        async updateSub() {
            this.savingSub = true;
            const r = await RepairBox.ajax(`/subcategories/${this.editingSubId}`, 'PUT', this.subForm);
            this.savingSub = false;
            if (r.success !== false) { RepairBox.toast('Subcategory updated', 'success'); this.showSubModal = false; this.load(); }
        },
        async removeSub(sub) {
            if (!await RepairBox.confirm('Delete this subcategory?')) return;
            const r = await RepairBox.ajax(`/subcategories/${sub.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Subcategory deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
