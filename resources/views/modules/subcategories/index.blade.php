@extends('layouts.app')
@section('page-title', 'Subcategories')

@section('content')
<div x-data="subcategoriesPage()" x-init="init()">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search subcategories..." class="form-input-custom max-w-xs">
            <select x-model="filterCat" @change="load()" class="form-select-custom w-48">
                <option value="">All Categories</option>
                <template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
            </select>
        </div>
        <a href="/subcategories/create" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Subcategory</a>
    </div>

    <!-- Grouped by Category -->
    <div class="space-y-4">
        <template x-for="group in groupedItems" :key="group.category_id">
            <div class="card overflow-hidden">
                <!-- Category Header -->
                <div class="bg-gradient-to-r from-primary-50 to-white px-4 py-3 border-b flex items-center justify-between cursor-pointer" @click="toggleGroup(group.category_id)">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-800" x-text="group.category_name"></h3>
                        <span class="badge badge-primary text-xs" x-text="group.items.length + ' subcategories'"></span>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="openGroups.includes(group.category_id) ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
                <!-- Subcategories List -->
                <div x-show="openGroups.includes(group.category_id)" x-transition.duration.200ms>
                    <div class="divide-y divide-gray-100">
                        <template x-for="(item, i) in group.items" :key="item.id">
                            <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs font-medium" x-text="i + 1"></span>
                                    <span class="font-medium text-gray-700" x-text="item.name"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="edit(item)" class="p-1.5 rounded-lg text-primary-600 hover:bg-primary-50 transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click="remove(item)" class="p-1.5 rounded-lg text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        <div x-show="groupedItems.length === 0" class="card">
            <div class="card-body text-center text-gray-400 py-8">No subcategories found</div>
        </div>
    </div>

    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Edit Subcategory</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select x-model="form.category_id" class="form-select-custom">
                            <option value="">Select Category</option>
                            <template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input x-model="form.name" type="text" class="form-input-custom">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function subcategoriesPage() {
    return {
        items: [], categories: [], showModal: false, editing: null, saving: false,
        search: '', filterCat: '', openGroups: [],
        form: { name: '', category_id: '' },

        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.search = p.get('search');
            if (p.has('category')) this.filterCat = p.get('category');
            this.load();
        },

        updateUrl() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.filterCat) params.set('category', this.filterCat);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },

        get groupedItems() {
            const groups = {};
            this.items.forEach(item => {
                const catId = item.category_id || 0;
                const catName = item.category ? item.category.name : 'Uncategorized';
                if (!groups[catId]) groups[catId] = { category_id: catId, category_name: catName, items: [] };
                groups[catId].items.push(item);
            });
            return Object.values(groups).sort((a, b) => a.category_name.localeCompare(b.category_name));
        },

        toggleGroup(catId) {
            const idx = this.openGroups.indexOf(catId);
            if (idx >= 0) this.openGroups.splice(idx, 1);
            else this.openGroups.push(catId);
        },

        async load() {
            let url = '/subcategories?per_page=200';
            if (this.search) url += '&search=' + encodeURIComponent(this.search);
            if (this.filterCat) url += '&category_id=' + this.filterCat;
            const [r, c] = await Promise.all([RepairBox.ajax(url), RepairBox.ajax('/categories')]);
            if (r.data) this.items = r.data;
            if (c.data) this.categories = c.data;
            // Auto-open all groups on first load
            if (this.openGroups.length === 0) this.openGroups = this.groupedItems.map(g => g.category_id);
            this.updateUrl();
        },
        edit(item) { this.editing = item.id; this.form = { name: item.name, category_id: item.category_id }; this.showModal = true; },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/subcategories/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this subcategory?')) return;
            const r = await RepairBox.ajax(`/subcategories/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
