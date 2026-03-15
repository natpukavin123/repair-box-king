@extends('layouts.app')
@section('page-title', 'Returns & Refunds')

@section('content')
<div x-data="returnsPage()" x-init="init()">
    <!-- Tabs -->
    <div class="flex gap-2 mb-4 border-b pb-2">
        <button @click="tab = 'customer'; updateUrl()" class="px-4 py-2 text-sm font-medium rounded-t" :class="tab === 'customer' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:text-gray-800'">Customer Returns</button>
        <button @click="tab = 'supplier'; updateUrl()" class="px-4 py-2 text-sm font-medium rounded-t" :class="tab === 'supplier' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:text-gray-800'">Supplier Returns</button>
        <button @click="tab = 'refunds'; loadRefunds(); updateUrl()" class="px-4 py-2 text-sm font-medium rounded-t" :class="tab === 'refunds' ? 'bg-primary-600 text-white' : 'text-gray-600 hover:text-gray-800'">Refunds</button>
    </div>

    <!-- Customer Returns -->
    <div x-show="tab === 'customer'">
        <div class="flex justify-end mb-3">
            <button @click="showCustReturn = true; custReturnForm = {invoice_id:'',product_id:'',quantity:1,reason:'',refund_amount:''}" class="btn-primary text-sm">+ Customer Return</button>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>#</th><th>Invoice</th><th>Product</th><th>Qty</th><th>Reason</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                        <tbody>
                            <template x-for="(r, i) in customerReturns" :key="r.id">
                                <tr>
                                    <td x-text="i+1"></td>
                                    <td x-text="r.invoice ? r.invoice.invoice_number : r.invoice_id"></td>
                                    <td x-text="r.product ? r.product.name : '-'"></td>
                                    <td x-text="r.quantity"></td>
                                    <td class="max-w-xs truncate" x-text="r.reason || '-'"></td>
                                    <td><span class="badge" :class="r.status === 'approved' ? 'badge-success' : r.status === 'rejected' ? 'badge-danger' : 'badge-warning'" x-text="r.status"></span></td>
                                    <td x-text="new Date(r.created_at).toLocaleDateString()"></td>
                                    <td>
                                        <select x-show="r.status === 'pending'" @change="updateStatus('customer', r.id, $event.target.value)" class="form-select-custom text-xs w-24">
                                            <option value="pending" selected>Pending</option>
                                            <option value="approved">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="customerReturns.length === 0"><td colspan="8" class="text-center text-gray-400 py-8">No customer returns</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Returns -->
    <div x-show="tab === 'supplier'">
        <div class="flex justify-end mb-3">
            <button @click="showSuppReturn = true; suppReturnForm = {purchase_id:'',product_id:'',quantity:1,reason:''}" class="btn-primary text-sm">+ Supplier Return</button>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>#</th><th>Purchase</th><th>Product</th><th>Qty</th><th>Reason</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <template x-for="(r, i) in supplierReturns" :key="r.id">
                                <tr>
                                    <td x-text="i+1"></td>
                                    <td x-text="r.purchase_id"></td>
                                    <td x-text="r.product ? r.product.name : '-'"></td>
                                    <td x-text="r.quantity"></td>
                                    <td x-text="r.reason || '-'"></td>
                                    <td><span class="badge" :class="r.status === 'approved' ? 'badge-success' : r.status === 'rejected' ? 'badge-danger' : 'badge-warning'" x-text="r.status"></span></td>
                                    <td x-text="new Date(r.created_at).toLocaleDateString()"></td>
                                </tr>
                            </template>
                            <tr x-show="supplierReturns.length === 0"><td colspan="7" class="text-center text-gray-400 py-8">No supplier returns</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Refunds -->
    <div x-show="tab === 'refunds'">
        <div class="card">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr><th>#</th><th>Type</th><th>Ref ID</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <template x-for="(r, i) in refunds" :key="r.id">
                                <tr>
                                    <td x-text="i+1"></td>
                                    <td x-text="r.refundable_type || '-'"></td>
                                    <td x-text="r.refundable_id"></td>
                                    <td class="text-red-600 font-medium" x-text="'₹' + Number(r.amount).toFixed(2)"></td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full" :class="{'bg-green-100 text-green-700': r.payment_method === 'cash', 'bg-blue-100 text-blue-700': r.payment_method === 'upi', 'bg-purple-100 text-purple-700': r.payment_method === 'card', 'bg-gray-100 text-gray-700': r.payment_method === 'bank_transfer'}">
                                            <template x-if="r.payment_method === 'cash'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                            <template x-if="r.payment_method === 'upi'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                            <template x-if="r.payment_method === 'card'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                            <template x-if="r.payment_method === 'bank_transfer'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                            <span x-text="r.payment_method === 'bank_transfer' ? 'Bank' : (r.payment_method || '-').toUpperCase()"></span>
                                        </span>
                                    </td>
                                    <td><span class="badge badge-success" x-text="r.status || 'completed'"></span></td>
                                    <td x-text="new Date(r.created_at).toLocaleDateString()"></td>
                                </tr>
                            </template>
                            <tr x-show="refunds.length === 0"><td colspan="7" class="text-center text-gray-400 py-8">No refunds</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Return Modal -->
    <div x-show="showCustReturn" class="modal-overlay" x-cloak @click.self="showCustReturn = false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Customer Return</h3><button @click="showCustReturn = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Invoice ID *</label><input x-model="custReturnForm.invoice_id" type="number" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Product ID</label><input x-model="custReturnForm.product_id" type="number" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label><input x-model="custReturnForm.quantity" type="number" min="1" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Refund Amount</label><input x-model="custReturnForm.refund_amount" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason</label><textarea x-model="custReturnForm.reason" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button @click="showCustReturn = false" class="btn-secondary">Cancel</button><button @click="saveCustReturn()" class="btn-primary">Submit</button></div>
        </div>
    </div>

    <!-- Supplier Return Modal -->
    <div x-show="showSuppReturn" class="modal-overlay" x-cloak @click.self="showSuppReturn = false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Supplier Return</h3><button @click="showSuppReturn = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Purchase ID *</label><input x-model="suppReturnForm.purchase_id" type="number" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Product ID *</label><input x-model="suppReturnForm.product_id" type="number" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label><input x-model="suppReturnForm.quantity" type="number" min="1" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Reason</label><textarea x-model="suppReturnForm.reason" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button @click="showSuppReturn = false" class="btn-secondary">Cancel</button><button @click="saveSuppReturn()" class="btn-primary">Submit</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function returnsPage() {
    return {
        tab: 'customer', customerReturns: [], supplierReturns: [], refunds: [],
        showCustReturn: false, showSuppReturn: false,
        custReturnForm: {}, suppReturnForm: {},
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('tab')) this.tab = p.get('tab');
            this.load();
            if (this.tab === 'refunds') this.loadRefunds();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.tab !== 'customer') params.set('tab', this.tab);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            const [cr, sr] = await Promise.all([
                RepairBox.ajax('/returns?type=customer'),
                RepairBox.ajax('/returns?type=supplier')
            ]);
            if(cr.data) this.customerReturns = cr.data;
            if(sr.data) this.supplierReturns = sr.data;
        },
        async loadRefunds() { const r = await RepairBox.ajax('/returns/refunds'); if(r.data) this.refunds = r.data; },
        async saveCustReturn() {
            const r = await RepairBox.ajax('/returns/customer', 'POST', this.custReturnForm);
            if(r.success !== false) { RepairBox.toast('Return submitted', 'success'); this.showCustReturn = false; this.load(); }
        },
        async saveSuppReturn() {
            const r = await RepairBox.ajax('/returns/supplier', 'POST', this.suppReturnForm);
            if(r.success !== false) { RepairBox.toast('Return submitted', 'success'); this.showSuppReturn = false; this.load(); }
        },
        async updateStatus(type, id, status) {
            const r = await RepairBox.ajax(`/returns/${type}/${id}/status`, 'PUT', {status});
            if(r.success !== false) { RepairBox.toast('Status updated', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
