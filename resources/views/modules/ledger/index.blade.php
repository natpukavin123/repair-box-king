@extends('layouts.app')
@section('page-title', 'Ledger')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="ledgerPage()" x-init="load()" class="page-list">
    <div class="page-header-inline">
        <div class="page-header-inline-copy">
            <h2 class="page-header-inline-title">Ledger</h2>
            <p class="page-header-inline-description">See cash movement, totals, and transaction history in one cleaner accounting view.</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Total In</p><p class="text-2xl font-bold text-green-600" x-text="'₹' + Number(summary.total_in || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></p></div>
        <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Total Out</p><p class="text-2xl font-bold text-red-600" x-text="'₹' + Number(summary.total_out || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></p></div>
        <div class="stat-card"><p class="text-xs text-gray-500 uppercase">Balance</p><p class="text-2xl font-bold" :class="Number(summary.balance || 0) >= 0 ? 'text-green-600' : 'text-red-600'" x-text="'₹' + Number(summary.balance || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></p></div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Type</th><th>Description</th><th>In</th><th>Out</th><th>Date</th></tr></thead>
                    <tbody>
                        <template x-for="(t, i) in items" :key="t.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td><span class="badge" :class="t.type === 'in' ? 'badge-success' : 'badge-danger'" x-text="t.type.toUpperCase()"></span></td>
                                <td x-text="t.description || '-'"></td>
                                <td class="text-green-600" x-text="t.type === 'in' ? '₹' + Number(t.amount).toFixed(2) : '-'"></td>
                                <td class="text-red-600" x-text="t.type === 'out' ? '₹' + Number(t.amount).toFixed(2) : '-'"></td>
                                <td x-text="new Date(t.created_at).toLocaleDateString()"></td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No transactions</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-40"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                </tr>
                            </template>
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
function ledgerPage() {
    return {
        items: [], summary: {}, loading: true,
        async load() {
            this.loading = true;
            const [r, s] = await Promise.all([RepairBox.ajax('/admin/ledger'), RepairBox.ajax('/admin/ledger/summary')]);
            if(r.data) this.items = r.data; if(s.data) this.summary = s.data;
            this.loading = false;
        }
    };
}
</script>
@endpush
