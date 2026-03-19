@extends('layouts.app')
@section('page-title', 'Products')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .products-workspace .workspace-toolbar,
    .products-workspace .workspace-filterbar,
    .products-workspace .workspace-table-card {
        border-radius: 1.2rem;
    }

    .products-workspace .workspace-table-scroll .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .products-workspace .workspace-table-scroll .data-table th,
    .products-workspace .workspace-table-scroll .data-table td {
        padding-top: 0.78rem;
        padding-bottom: 0.78rem;
    }
</style>

<div x-data="productsPage()" x-init="init()" class="workspace-screen products-workspace">
    <x-ui.action-bar title="Product Inventory" description="Keep search, stock review, and product updates on a single screen.">
        <a href="/products/create" class="btn-primary inline-flex w-full items-center justify-center sm:w-auto"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Product</a>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-group">
            <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search products by name, SKU, or brand" class="form-input-custom workspace-search-input">
        </div>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> products</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Inventory Table</h3>
                <p class="text-sm text-slate-500">Product list stays inside the page with its own scroll area.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>Img</th><th>#</th><th>Name</th><th>SKU</th><th>Category</th><th>Brand</th><th>MRP</th><th>Sale Price</th><th>Stock</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td>
                                    <template x-if="item.thumbnail">
                                        <img :src="'/storage/' + item.thumbnail" class="w-10 h-10 rounded-lg object-cover border border-gray-200 shadow-sm">
                                    </template>
                                    <template x-if="!item.thumbnail">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </template>
                                </td>
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
                        <tr x-show="items.length === 0 && !loading"><td colspan="10" class="text-center text-gray-400 py-8">No products found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-10 w-10 rounded-lg"></div></td>
                                    <td><div class="skeleton h-3 w-8"></div></td>
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
    </x-ui.table-card>

    <!-- Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container admin-modal modal-lg">
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
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Max Selling Price</label><input x-model="form.max_selling_price" type="number" step="0.01" class="form-input-custom" placeholder="Optional"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label><input x-model="form.barcode" type="text" class="form-input-custom"></div>

                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>

                    {{-- Image Upload --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Main Image --}}
                            <div>
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Main Image</p>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$refs.editImageInput.click()" @dragover.prevent @drop.prevent="handleFileDrop('image', $event)">
                                    <template x-if="imagePreview">
                                        <div class="relative">
                                            <img :src="imagePreview" class="max-h-32 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="imageFile=null; imagePreview=currentImageUrl; $refs.editImageInput.value=''"
                                                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                        </div>
                                    </template>
                                    <template x-if="!imagePreview">
                                        <div class="py-3">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-xs text-gray-400">Click to upload</p>
                                        </div>
                                    </template>
                                    <input x-ref="editImageInput" type="file" accept="image/*" class="hidden" @change="handleFilePick('image', $event)">
                                </div>
                            </div>
                            {{-- Thumbnail --}}
                            <div>
                                <p class="text-xs text-gray-500 mb-1.5 font-medium">Thumbnail <span class="text-gray-400">(auto if not set)</span></p>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-3 text-center cursor-pointer hover:border-primary-400 hover:bg-primary-50 transition-all"
                                     @click="$refs.editThumbInput.click()" @dragover.prevent @drop.prevent="handleFileDrop('thumb', $event)">
                                    <template x-if="thumbPreview">
                                        <div class="relative">
                                            <img :src="thumbPreview" class="max-h-32 mx-auto rounded-lg object-contain">
                                            <button type="button" @click.stop="thumbFile=null; thumbPreview=currentThumbUrl; $refs.editThumbInput.value=''"
                                                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">✕</button>
                                        </div>
                                    </template>
                                    <template x-if="!thumbPreview">
                                        <div class="py-3">
                                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="text-xs text-gray-400">Click to upload</p>
                                        </div>
                                    </template>
                                    <input x-ref="editThumbInput" type="file" accept="image/*" class="hidden" @change="handleFilePick('thumb', $event)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <button @click="showModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button>
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
        imageFile: null, imagePreview: null, thumbFile: null, thumbPreview: null,
        currentImageUrl: null, currentThumbUrl: null,
        form: { name: '', sku: '', category_id: '', subcategory_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', max_selling_price: '', barcode: '', description: '' },

        handleFilePick(type, e) {
            const file = e.target.files[0]; if (!file) return;
            if (type === 'image') { this.imageFile = file; const r = new FileReader(); r.onload = ev => this.imagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.thumbFile = file; const r = new FileReader(); r.onload = ev => this.thumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
        handleFileDrop(type, e) {
            const file = e.dataTransfer.files[0]; if (!file || !file.type.startsWith('image/')) return;
            if (type === 'image') { this.imageFile = file; const r = new FileReader(); r.onload = ev => this.imagePreview = ev.target.result; r.readAsDataURL(file); }
            else { this.thumbFile = file; const r = new FileReader(); r.onload = ev => this.thumbPreview = ev.target.result; r.readAsDataURL(file); }
        },
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
        edit(item) {
            this.editing = item.id;
            this.imageFile = null; this.thumbFile = null;
            this.currentImageUrl = item.thumbnail ? '/storage/' + item.thumbnail : null;
            this.currentThumbUrl = item.thumbnail ? '/storage/' + item.thumbnail : null;
            this.imagePreview = item.image ? '/storage/' + item.image : null;
            this.thumbPreview = item.thumbnail ? '/storage/' + item.thumbnail : null;
            this.form = {
                name: item.name, sku: item.sku || '',
                category_id: item.category_id || '', subcategory_id: item.subcategory_id || '',
                brand_id: item.brand_id || '', purchase_price: item.purchase_price,
                mrp: item.mrp, selling_price: item.selling_price,
                max_selling_price: item.max_selling_price || '',
                barcode: item.barcode || '', description: item.description || '',
            };
            if (item.category_id) this.loadSubcategories();
            this.showModal = true;
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/products/${this.editing}`, 'PUT', this.form);
            if (r.success !== false) {
                if (this.imageFile || this.thumbFile) {
                    const fd = new FormData();
                    if (this.imageFile) fd.append('image', this.imageFile);
                    if (this.thumbFile) fd.append('thumbnail', this.thumbFile);
                    await RepairBox.upload(`/products/${this.editing}/upload-image`, fd);
                }
                RepairBox.toast('Updated', 'success'); this.showModal = false; this.load();
            }
            this.saving = false;
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
