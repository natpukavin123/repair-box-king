@extends('layouts.app')
@section('page-title', 'Purchases')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="purchasesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-end mb-4">
        <a href="/purchases/create" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> New Purchase</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Invoice #</th><th>Supplier</th><th>Date</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.invoice_number || '-'"></td>
                                <td x-text="item.supplier ? item.supplier.name : '-'"></td>
                                <td x-text="item.purchase_date ? new Date(item.purchase_date).toLocaleDateString('en-IN') : '-'"></td>
                                <td x-text="'\u20b9' + Number(item.total_amount).toFixed(2)"></td>
                                <td><span class="badge" :class="{'badge-success': item.status === 'received', 'badge-warning': item.status === 'pending', 'badge-danger': item.status === 'cancelled'}" x-text="item.status || 'received'"></span></td>
                                <td class="whitespace-nowrap">
                                    <button @click="view(item)" class="text-primary-600 hover:text-primary-800 mr-2"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="7" class="text-center text-gray-400 py-8">No purchases found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20 rounded-full"></div></td>
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
    <div x-show="showView" class="modal-overlay" x-cloak @click.self="showView = false">
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Purchase Details</h3><button @click="showView = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body" x-show="viewData">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4 text-sm">
                    <div><span class="text-gray-500">Supplier:</span> <span class="font-medium" x-text="viewData?.supplier?.name"></span></div>
                    <div><span class="text-gray-500">Invoice:</span> <span class="font-medium" x-text="viewData?.invoice_number || '-'"></span></div>
                    <div><span class="text-gray-500">Date:</span> <span class="font-medium" x-text="viewData?.purchase_date ? new Date(viewData.purchase_date).toLocaleDateString('en-IN') : '-'"></span></div>
                    <div><span class="text-gray-500">Total:</span> <span class="font-medium" x-text="'₹' + Number(viewData?.total_amount || 0).toFixed(2)"></span></div>
                </div>
                <table class="data-table">
                    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
                    <tbody>
                        <template x-for="it in viewData?.items || []" :key="it.id">
                            <tr>
                                <td x-text="it.product ? it.product.name : '-'"></td>
                                <td x-text="it.quantity"></td>
                                <td x-text="'₹' + Number(it.purchase_price).toFixed(2)"></td>
                                <td x-text="'₹' + (it.quantity * it.purchase_price).toFixed(2)"></td>
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
function purchasesPage() {
    return {
        items: [], showView: false, viewData: null, loading: true,
        async load() { this.loading = true; const r = await RepairBox.ajax('/purchases'); if(r.data) this.items = r.data; this.loading = false; },
        async view(item) { const r = await RepairBox.ajax(`/purchases/${item.id}`); if(r.data) { this.viewData = r.data; this.showView = true; } },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this purchase?')) return;
            const r = await RepairBox.ajax(`/purchases/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
