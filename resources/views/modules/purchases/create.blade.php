@extends('layouts.app')
@section('page-title', 'New Purchase')

@section('content')
<div x-data="createPurchasePage()" class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">New Purchase</h2>
            <p class="text-sm text-gray-500 mt-0.5">Record a new purchase from supplier</p>
        </div>
        <a href="/purchases" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                    <select x-model="form.supplier_id" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number</label>
                    <input x-model="form.invoice_number" type="text" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input x-model="form.purchase_date" type="date" class="form-input-custom">
                </div>
            </div>

            <h4 class="font-medium text-gray-700 mb-2">Items</h4>
            <template x-for="(it, idx) in form.items" :key="idx">
                <div class="grid grid-cols-12 gap-2 mb-2 items-end">
                    <div class="col-span-5">
                        <select x-model="it.product_id" class="form-select-custom text-sm">
                            <option value="">Product</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2"><input x-model="it.quantity" type="number" min="1" class="form-input-custom text-sm" placeholder="Qty"></div>
                    <div class="col-span-3"><input x-model="it.purchase_price" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Price"></div>
                    <div class="col-span-2 flex gap-1">
                        <span class="text-sm font-medium text-gray-600 py-2" x-text="'₹' + (Number(it.quantity||0) * Number(it.purchase_price||0)).toFixed(2)"></span>
                        <button @click="form.items.splice(idx, 1)" class="text-red-500 hover:text-red-700 ml-1" x-show="form.items.length > 1">&times;</button>
                    </div>
                </div>
            </template>
            <button @click="form.items.push({product_id: '', quantity: 1, purchase_price: ''})" class="text-primary-600 text-sm hover:underline">+ Add Item</button>

            <div class="mt-4 text-right font-semibold text-lg">
                Total: <span x-text="'₹' + form.items.reduce((s, i) => s + Number(i.quantity||0) * Number(i.purchase_price||0), 0).toFixed(2)"></span>
            </div>
            <div class="mt-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea x-model="form.notes" class="form-input-custom" rows="2"></textarea>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/purchases" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Save Purchase
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createPurchasePage() {
    return {
        saving: false,
        form: {
            supplier_id: '', invoice_number: '',
            purchase_date: new Date().toISOString().split('T')[0],
            notes: '',
            items: [{product_id: '', quantity: 1, purchase_price: ''}]
        },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/purchases', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Purchase recorded', 'success');
                window.location.href = '/purchases';
            }
        }
    };
}
</script>
@endpush
