@extends('layouts.app')
@section('page-title', 'Products')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="productsPage()" x-init="init()" class="page-list">
    <!-- Search -->
    <div class="flex items-center justify-between mb-4">
        <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search products..." class="form-input-custom max-w-md">
        <a href="/products/create" class="btn-primary ml-3"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Product</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>SKU</th><th>Category</th><th>Brand</th><th>MRP</th><th>Sale Price</th><th>Stock</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.name"></td>
                                <td class="text-sm text-gray-500" x-text="item.sku || '-'"></td>
                                <td x-text="item.category ? item.category.name : '-'"></td>
                                <td x-text="item.brand ? item.brand.name : '-'"></td>
                                <td x-text="'\u20b9' + Number(item.mrp || 0).toFixed(2)"></td>
                                <td x-text="'\u20b9' + Number(item.selling_price || 0).toFixed(2)"></td>
                                <td>
                                    <span class="badge" :class="(item.inventory ? item.inventory.current_stock : 0) <= 5 ? 'badge-danger' : 'badge-success'" x-text="item.inventory ? item.inventory.current_stock : 0"></span>
                                </td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(item)" class="text-primary-600 hover:text-primary-800 mr-2"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="9" class="text-center text-gray-400 py-8">No products found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-36"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-28"></div></td>
                                    <td><div class="skeleton h-3 w-28"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-16 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Product</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input x-model="form.sku" type="text" class="form-input-custom"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select x-model="form.category_id" @change="loadSubcategories()" class="form-select-custom">
                            <option value="">Select</option>
                            <template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subcategory</label>
                        <select x-model="form.subcategory_id" class="form-select-custom">
                            <option value="">Select</option>
                            <template x-for="s in subcategories" :key="s.id"><option :value="s.id" x-text="s.name"></option></template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                        <select x-model="form.brand_id" class="form-select-custom">
                            <option value="">Select</option>
                            <template x-for="b in brands" :key="b.id"><option :value="b.id" x-text="b.name"></option></template>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label><input x-model="form.purchase_price" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">MRP *</label><input x-model="form.mrp" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label><input x-model="form.selling_price" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label><input x-model="form.barcode" type="text" class="form-input-custom"></div>

                    {{-- HSN Code --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            HSN Code
                            <span class="text-xs text-gray-400 font-normal ml-1">Search from master — GST rate auto-maps</span>
                        </label>
                        <div x-show="selectedHsn" class="flex items-center gap-2 mb-2 p-2 bg-blue-50 border border-blue-200 rounded">
                            <div class="flex-1 min-w-0">
                                <span class="font-semibold text-blue-800 text-sm" x-text="selectedHsn?.code"></span>
                                <span class="text-blue-600 text-sm mx-1">—</span>
                                <span class="text-blue-700 text-sm" x-text="selectedHsn?.description"></span>
                            </div>
                            <span class="badge badge-success text-xs whitespace-nowrap" x-text="(selectedHsn?.tax_rate?.percentage ?? 0) + '% GST'"></span>
                            <button type="button" @click="clearHsn()" class="text-red-400 hover:text-red-600">&times;</button>
                        </div>
                        <div x-show="!selectedHsn" class="relative">
                            <input x-model="hsnSearch" @input.debounce.300ms="searchHsn()"
                                   type="text" class="form-input-custom" placeholder="Type HSN code or description...">
                            <div x-show="hsnResults.length > 0" class="absolute z-10 w-full bg-white border border-gray-200 rounded shadow-lg mt-0.5 max-h-40 overflow-y-auto">
                                <template x-for="h in hsnResults" :key="h.id">
                                    <button type="button" @click="selectHsn(h)"
                                            class="w-full text-left px-3 py-2 hover:bg-gray-50 border-b border-gray-100 last:border-0 text-sm">
                                        <span class="font-semibold text-gray-800" x-text="h.code"></span>
                                        <span class="text-gray-500 mx-1">—</span>
                                        <span class="text-gray-700" x-text="h.description"></span>
                                        <span class="float-right text-xs text-green-600 font-medium" x-text="(h.tax_rate?.percentage ?? 0) + '% GST'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>
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
function productsPage() {
    return {
        items: [], categories: [], subcategories: [], brands: [],
        showModal: false, editing: null, saving: false, search: '', loading: true,
        hsnSearch: '', hsnResults: [], selectedHsn: null,
        form: { name: '', sku: '', category_id: '', subcategory_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', barcode: '', description: '', hsn_code: '' },

        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.search = p.get('search');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            this.loading = true;
            const [pr, cats, brs] = await Promise.all([
                RepairBox.ajax('/products?search=' + encodeURIComponent(this.search)),
                RepairBox.ajax('/categories'),
                RepairBox.ajax('/brands'),
            ]);
            if (pr.data) this.items = pr.data;
            if (cats.data) this.categories = cats.data;
            if (brs.data) this.brands = brs.data;
            this.updateUrl();
            this.loading = false;
        },
        async loadSubcategories() {
            if (!this.form.category_id) { this.subcategories = []; return; }
            const r = await RepairBox.ajax(`/subcategories/by-category/${this.form.category_id}`);
            if (r.data) this.subcategories = r.data;
        },
        async searchHsn() {
            if (this.hsnSearch.length < 1) { this.hsnResults = []; return; }
            const r = await RepairBox.ajax('/tax/hsn-search?type=hsn&q=' + encodeURIComponent(this.hsnSearch));
            this.hsnResults = r.data ?? [];
        },
        selectHsn(h) {
            this.selectedHsn = h;
            this.form.hsn_code = h.code;
            this.hsnResults = [];
            this.hsnSearch = '';
        },
        clearHsn() {
            this.selectedHsn = null;
            this.form.hsn_code = '';
            this.hsnSearch = '';
            this.hsnResults = [];
        },
        edit(item) {
            this.editing = item.id;
            this.form = {
                name: item.name, sku: item.sku || '',
                category_id: item.category_id || '', subcategory_id: item.subcategory_id || '',
                brand_id: item.brand_id || '', purchase_price: item.purchase_price,
                mrp: item.mrp, selling_price: item.selling_price,
                barcode: item.barcode || '', description: item.description || '',
                hsn_code: item.hsn_code || '',
            };
            if (item.category_id) this.loadSubcategories();
            if (item.hsn_code) {
                this.selectedHsn = { code: item.hsn_code, description: item.hsn_code, tax_rate: null };
                RepairBox.ajax('/tax/hsn-search?type=hsn&q=' + encodeURIComponent(item.hsn_code)).then(r => {
                    const match = (r.data ?? []).find(h => h.code === item.hsn_code);
                    if (match) this.selectedHsn = match;
                });
            } else {
                this.selectedHsn = null;
                this.hsnSearch = '';
                this.hsnResults = [];
            }
            this.showModal = true;
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/products/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this product?')) return;
            const r = await RepairBox.ajax(`/products/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
