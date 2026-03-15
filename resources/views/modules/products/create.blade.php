@extends('layouts.app')
@section('page-title', 'Add Product')

@section('content')
<div x-data="createProductPage()" class="max-w-3xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Product</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new product in inventory</p>
        </div>
        <a href="/products" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">SKU</label><input x-model="form.sku" type="text" class="form-input-custom"></div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select x-model="form.category_id" @change="loadSubcategories()" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
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
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase Price *</label><input x-model="form.purchase_price" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">MRP *</label><input x-model="form.mrp" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label><input x-model="form.selling_price" type="number" step="0.01" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label><input x-model="form.barcode" type="text" class="form-input-custom"></div>

                {{-- HSN Code — master-driven lookup --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        HSN Code
                        <span class="text-xs text-gray-400 font-normal ml-1">Search from master list — GST rate auto-maps</span>
                    </label>
                    <div x-show="selectedHsn" class="flex items-center gap-2 mb-2 p-2 bg-blue-50 border border-blue-200 rounded">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <div class="flex-1 min-w-0">
                            <span class="font-semibold text-blue-800 text-sm" x-text="selectedHsn?.code"></span>
                            <span class="text-blue-600 text-sm mx-1">—</span>
                            <span class="text-blue-700 text-sm" x-text="selectedHsn?.description"></span>
                        </div>
                        <span class="badge badge-success text-xs whitespace-nowrap" x-text="(selectedHsn?.tax_rate?.percentage ?? 0) + '% GST'"></span>
                        <button type="button" @click="clearHsn()" class="text-red-400 hover:text-red-600 ml-1">&times;</button>
                    </div>
                    <div x-show="!selectedHsn" class="relative">
                        <input x-model="hsnSearch"
                               @input.debounce.300ms="searchHsn()"
                               @focus="if(hsnSearch.length>=1) searchHsn()"
                               type="text" class="form-input-custom pr-8"
                               placeholder="Type HSN code or description to search...">
                        <svg x-show="hsnLoading" class="w-4 h-4 absolute right-2 top-3 animate-spin text-gray-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <div x-show="hsnResults.length > 0" class="absolute z-10 w-full bg-white border border-gray-200 rounded shadow-lg mt-0.5 max-h-48 overflow-y-auto">
                            <template x-for="h in hsnResults" :key="h.id">
                                <button type="button" @click="selectHsn(h)"
                                        class="w-full text-left px-3 py-2 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                                    <span class="font-semibold text-gray-800 text-sm" x-text="h.code"></span>
                                    <span class="text-gray-500 text-sm mx-1">—</span>
                                    <span class="text-gray-700 text-sm" x-text="h.description"></span>
                                    <span class="float-right text-xs text-green-600 font-medium" x-text="(h.tax_rate?.percentage ?? 0) + '% GST'"></span>
                                </button>
                            </template>
                        </div>
                        <p x-show="hsnSearched && hsnResults.length === 0 && hsnSearch.length > 0" class="text-xs text-gray-400 mt-1">No HSN codes found. Add them via <a href="/tax" class="text-primary-600 underline" target="_blank">Tax &amp; GST</a>.</p>
                    </div>
                </div>

                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/products" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Product
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createProductPage() {
    return {
        saving: false,
        subcategories: [],
        hsnSearch: '', hsnResults: [], hsnLoading: false, hsnSearched: false, selectedHsn: null,
        form: { name: '', sku: '', category_id: '', subcategory_id: '', brand_id: '', purchase_price: '', mrp: '', selling_price: '', barcode: '', description: '', hsn_code: '' },

        async loadSubcategories() {
            if (!this.form.category_id) { this.subcategories = []; return; }
            const r = await RepairBox.ajax(`/subcategories/by-category/${this.form.category_id}`);
            if (r.data) this.subcategories = r.data;
        },
        async searchHsn() {
            if (this.hsnSearch.length < 1) { this.hsnResults = []; return; }
            this.hsnLoading = true;
            const r = await RepairBox.ajax('/tax/hsn-search?type=hsn&q=' + encodeURIComponent(this.hsnSearch));
            this.hsnLoading = false;
            this.hsnSearched = true;
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
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/products', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Product created', 'success'); window.location.href = '/products'; }
        }
    };
}
</script>
@endpush
