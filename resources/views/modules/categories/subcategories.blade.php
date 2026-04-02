@extends('layouts.app')
@section('page-title', $category->name . ' - Subcategories')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="categorySubcategoriesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <a href="/categories" class="text-gray-500 hover:text-gray-700 transition-colors" title="Back to Categories">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-xl font-bold text-gray-800">{{ $category->name }} <span class="text-gray-400 text-sm font-normal">Subcategories</span></h2>
            <div class="relative max-w-xs ml-4">
                <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search..." class="form-input-custom pl-9">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
        </div>
        <button @click="showModal = true" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Subcategory</button>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th class="w-10">#</th><th>Name</th><th class="w-32">Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr class="group hover:bg-gray-50 transition-colors">
                                <td x-text="(page - 1) * perPage + i + 1"></td>
                                <td class="font-medium text-gray-800" x-text="item.name"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(item)" class="text-primary-600 hover:text-primary-800 mr-2 p-1 rounded hover:bg-primary-50 transition-colors" title="Edit"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 transition-colors" title="Delete"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="3" class="text-center text-gray-500 py-8">No subcategories found in this category.</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 5" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-48"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div x-show="total > perPage" class="border-t border-gray-100 flex items-center justify-between px-4 py-3 bg-gray-50 rounded-b-xl">
            <span class="text-sm text-gray-500" x-text="`Showing ${items.length ? (page-1)*perPage+1 : 0} to ${Math.min(page*perPage, total)} of ${total}`"></span>
            <div class="flex gap-1" x-show="lastPage > 1">
                <button @click="page = 1; load()" :disabled="page===1" class="btn-pagination group">&laquo;</button>
                <button @click="page--; load()" :disabled="page===1" class="btn-pagination group">&lsaquo;</button>
                <button @click="page++; load()" :disabled="page===lastPage" class="btn-pagination group">&rsaquo;</button>
                <button @click="page = lastPage; load()" :disabled="page===lastPage" class="btn-pagination group">&raquo;</button>
            </div>
        </div>
    </div>

    <!-- Edit/Add Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md">
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="editing ? 'Edit Subcategory' : 'Add Subcategory'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">&times;</button>
            </div>
            <div class="modal-body">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input x-model="form.name" type="text" class="form-input-custom" @keydown.enter="save()" placeholder="Enter name...">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span>
                    <span x-text="editing ? 'Update' : 'Save'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function categorySubcategoriesPage() {
    return {
        categoryId: {{ $category->id }},
        items: [], total: 0, page: 1, perPage: 15, lastPage: 1,
        search: '', loading: true, saving: false,
        showModal: false, editing: null,
        form: { name: '', category_id: {{ $category->id }} },

        async load() {
            this.loading = true;
            let url = `/categories/${this.categoryId}/subcategories?page=${this.page}`;
            if (this.search) url += '&search=' + encodeURIComponent(this.search);
            const r = await RepairBox.ajax(url);
            if (r.data) {
                this.items = r.data; this.total = r.total; this.lastPage = r.last_page;
            }
            this.loading = false;
        },

        edit(item) {
            this.editing = item.id;
            this.form = { name: item.name, category_id: this.categoryId };
            this.showModal = true;
        },

        resetForm() {
            this.editing = null;
            this.form = { name: '', category_id: this.categoryId };
        },

        async save() {
            if (!this.form.name.trim()) return RepairBox.toast('Name is required', 'error');
            this.saving = true;
            let url = this.editing ? `/subcategories/${this.editing}` : '/admin/subcategories';
            let method = this.editing ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);
            this.saving = false;

            if (r.success !== false) {
                RepairBox.toast(this.editing ? 'Updated successfully' : 'Created successfully', 'success');
                this.showModal = false;
                this.resetForm();
                if (!this.editing) this.page = 1; // goto first page if new
                this.load();
            }
        },

        async remove(item) {
            if (!await RepairBox.confirm('Delete this subcategory?')) return;
            const r = await RepairBox.ajax(`/admin/subcategories/${item.id}`, 'DELETE');
            if (r.success !== false) {
                RepairBox.toast('Deleted successfully', 'success');
                if (this.items.length === 1 && this.page > 1) this.page--;
                this.load();
            }
        }
    };
}
</script>
@endpush
