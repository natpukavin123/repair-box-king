@extends('layouts.app')
@section('page-title', 'Inventory')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="inventoryPage()" x-init="init()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search products..." class="form-input-custom max-w-xs">
        <button @click="openAdjust()" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Stock Adjust</button>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Product</th><th>Stock</th><th>Reserved</th><th>Status</th><th>Last Updated</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.product ? item.product.name : '-'"></td>
                                <td><span class="font-semibold" x-text="item.current_stock"></span></td>
                                <td x-text="item.reserved_stock || 0"></td>
                                <td><span class="badge" :class="item.current_stock <= 5 ? 'badge-danger' : 'badge-success'" x-text="item.current_stock <= 5 ? 'Low' : 'OK'"></span></td>
                                <td x-text="new Date(item.updated_at).toLocaleDateString()"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="openAdjust(item)" class="text-primary-600 hover:text-primary-800 mr-2" title="Adjust Stock">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                    <button @click="viewHistory(item)" class="text-green-600 hover:text-green-800" title="View History">
                                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="7" class="text-center text-gray-400 py-8">No inventory records</td></tr>
                        <template x-if="loading">
                            <tr><td colspan="7" class="p-0"><template x-for="i in 10" :key="'sk'+i"><div class="skeleton-row"><div class="skeleton h-3 w-16"></div><div class="skeleton h-3 w-28"></div><div class="skeleton h-3 w-16"></div><div class="skeleton h-3 w-16"></div><div class="skeleton h-3 w-16 rounded-full"></div><div class="skeleton h-3 w-20"></div><div class="skeleton h-3 w-16"></div></div></template></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Adjust Modal -->
    <div x-show="showAdjust" class="modal-overlay" x-cloak>
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Stock Adjustment</h3><button @click="showAdjust = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                        <select x-model="adjustForm.product_id" class="form-select-custom" :disabled="adjustForm._locked">
                            <option value="">Select Product</option>
                            <template x-for="p in products" :key="p.id"><option :value="p.id" x-text="p.name"></option></template>
                        </select>
                        <div x-show="adjustForm._locked" class="text-xs text-gray-500 mt-1">Product pre-selected from inventory row</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select x-model="adjustForm.adjustment_type" class="form-select-custom">
                            <option value="addition">Addition (+)</option>
                            <option value="subtraction">Subtraction (-)</option>
                        </select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label><input x-model="adjustForm.quantity" type="number" min="1" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason</label><textarea x-model="adjustForm.reason" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showAdjust = false" class="btn-secondary">Cancel</button>
                <button @click="adjust()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span> Adjust</button>
            </div>
        </div>
    </div>

    <!-- History Modal -->
    <div x-show="showHistory" class="modal-overlay" x-cloak>
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Stock History: <span class="text-primary-600" x-text="historyProduct"></span></h3>
                <button @click="showHistory = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>Date</th><th>Type</th><th>Qty</th><th>Reason</th><th>By</th></tr></thead>
                        <tbody>
                            <template x-for="h in historyItems" :key="h.id">
                                <tr>
                                    <td class="text-sm" x-text="new Date(h.created_at).toLocaleString()"></td>
                                    <td>
                                        <span class="badge"
                                            :class="h.adjustment_type === 'addition' ? 'badge-success' : (h.adjustment_type === 'subtraction' ? 'badge-danger' : 'badge-warning')"
                                            x-text="h.adjustment_type"></span>
                                    </td>
                                    <td class="font-semibold"
                                        :class="h.adjustment_type === 'addition' ? 'text-green-600' : 'text-red-600'"
                                        x-text="(h.adjustment_type === 'addition' ? '+' : '-') + h.quantity"></td>
                                    <td class="text-sm text-gray-600" x-text="h.reason || '-'"></td>
                                    <td class="text-sm" x-text="h.creator ? h.creator.name : '-'"></td>
                                </tr>
                            </template>
                            <tr x-show="historyItems.length === 0"><td colspan="5" class="text-center text-gray-400 py-6">No adjustments found</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function inventoryPage() {
    return {
        items: [], products: [], showAdjust: false, saving: false, search: '', loading: true,
        adjustForm: { product_id: '', adjustment_type: 'addition', quantity: 1, reason: '', _locked: false },
        showHistory: false, historyProduct: '', historyItems: [],

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
            let url = '/inventory';
            if (this.search) url += '?search=' + encodeURIComponent(this.search);
            const [r, p] = await Promise.all([RepairBox.ajax(url), RepairBox.ajax('/products')]);
            if(r.data) this.items = r.data; if(p.data) this.products = p.data;
            this.updateUrl();
            this.loading = false;
        },

        openAdjust(item) {
            if (item && item.product) {
                this.adjustForm = { product_id: item.product_id, adjustment_type: 'addition', quantity: 1, reason: '', _locked: true };
            } else {
                this.adjustForm = { product_id: '', adjustment_type: 'addition', quantity: 1, reason: '', _locked: false };
            }
            this.showAdjust = true;
        },

        async adjust() {
            this.saving = true;
            const payload = { product_id: this.adjustForm.product_id, adjustment_type: this.adjustForm.adjustment_type, quantity: this.adjustForm.quantity, reason: this.adjustForm.reason };
            const r = await RepairBox.ajax('/inventory/adjust', 'POST', payload);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Stock adjusted', 'success'); this.showAdjust = false; this.load(); }
        },

        async viewHistory(item) {
            this.historyProduct = item.product ? item.product.name : 'Unknown';
            this.historyItems = [];
            this.showHistory = true;
            const r = await RepairBox.ajax('/inventory/adjustments?product_id=' + item.product_id);
            if (r.data) this.historyItems = r.data;
        }
    };
}
</script>
@endpush
