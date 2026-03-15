@extends('layouts.app')
@section('page-title', 'Invoices')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="invoicesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-end mb-4">
        <a href="/pos" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> New Invoice</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>Invoice #</th><th>Customer</th><th>Items</th><th>Discount</th><th>Total</th><th>Payment</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="inv in items" :key="inv.id">
                            <tr>
                                <td class="font-medium text-primary-600" x-text="inv.invoice_number"></td>
                                <td x-text="inv.customer ? inv.customer.name : 'Walk-in'"></td>
                                <td x-text="inv.items_count ?? (inv.items ? inv.items.length : 0)"></td>
                                <td x-text="'₹' + Number(inv.discount || 0).toFixed(2)"></td>
                                <td class="font-semibold" x-text="'₹' + Number(inv.total_amount).toFixed(2)"></td>
                                <td><span class="badge badge-success" x-text="inv.payment_status || 'paid'"></span></td>
                                <td x-text="new Date(inv.created_at).toLocaleDateString()"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="view(inv)" class="text-primary-600 hover:text-primary-800 mr-1" title="View"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <a :href="'/invoices/' + inv.id + '/print'" target="_blank" class="text-green-600 hover:text-green-800" title="Print"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg></a>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="8" class="text-center text-gray-400 py-8">No invoices found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div x-show="showView" class="modal-overlay" x-cloak>
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold" x-text="'Invoice: ' + (viewData?.invoice_number || '')"></h3><button @click="showView = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4 text-sm">
                    <div><span class="text-gray-500">Customer:</span><br><span class="font-medium" x-text="viewData?.customer?.name || 'Walk-in'"></span></div>
                    <div><span class="text-gray-500">Date:</span><br><span class="font-medium" x-text="viewData ? new Date(viewData.created_at).toLocaleString() : ''"></span></div>
                    <div><span class="text-gray-500">Total:</span><br><span class="font-bold text-lg text-primary-600" x-text="'₹' + Number(viewData?.total_amount || 0).toFixed(2)"></span></div>
                    <div><span class="text-gray-500">Billing By:</span><br><span class="font-medium" x-text="viewData?.creator?.name || '-'"></span></div>
                </div>
                <h4 class="font-medium text-gray-700 mb-2">Items</h4>
                <table class="data-table mb-4">
                    <thead><tr><th>Item</th><th>Type</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <template x-for="it in viewData?.items || []" :key="it.id">
                            <tr>
                                <td x-text="it.item_name"></td>
                                <td><span class="badge badge-secondary" x-text="it.item_type"></span></td>
                                <td x-text="it.quantity"></td>
                                <td x-text="'₹' + Number(it.price).toFixed(2)"></td>
                                <td x-text="'₹' + Number(it.total).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <h4 class="font-medium text-gray-700 mb-2">Payments</h4>
                <table class="data-table">
                    <thead><tr><th>Method</th><th>Amount</th><th>Reference</th></tr></thead>
                    <tbody>
                        <template x-for="p in viewData?.payments || []" :key="p.id">
                            <tr>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full" :class="{'bg-green-100 text-green-700': p.payment_method === 'cash', 'bg-blue-100 text-blue-700': p.payment_method === 'upi', 'bg-purple-100 text-purple-700': p.payment_method === 'card', 'bg-gray-100 text-gray-700': p.payment_method === 'bank_transfer'}">
                                        <template x-if="p.payment_method === 'cash'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                        <template x-if="p.payment_method === 'upi'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                        <template x-if="p.payment_method === 'card'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                        <template x-if="p.payment_method === 'bank_transfer'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                        <span x-text="p.payment_method === 'bank_transfer' ? 'Bank' : (p.payment_method || '-').toUpperCase()"></span>
                                    </span>
                                </td>
                                <td x-text="'₹' + Number(p.amount).toFixed(2)"></td>
                                <td x-text="p.transaction_reference || '-'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function invoicesPage() {
    return {
        items: [], showView: false, viewData: null, loading: true,
        async load() { this.loading = true; const r = await RepairBox.ajax('/invoices'); if(r.data) this.items = r.data; this.loading = false; },
        async view(inv) { const r = await RepairBox.ajax(`/invoices/${inv.id}`); if(r.data) { this.viewData = r.data; this.showView = true; } }
    };
}
</script>
@endpush
