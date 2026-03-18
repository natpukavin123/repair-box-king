@extends('layouts.app')
@section('page-title', 'Reports')

@section('content')
<div x-data="reportsPage()" x-init="init()">
    {{-- Report Type Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <button @click="type='sales'; updateUrl()" :class="type==='sales' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Sales Report</button>
        <button @click="type='profit'; updateUrl()" :class="type==='profit' ? 'btn-primary' : 'btn-secondary'" class="text-sm">Profit Report</button>
    </div>

    {{-- Date Range Filter --}}
    <div class="card mb-6">
        <div class="card-body">
            <div class="flex flex-col gap-4 sm:flex-row sm:flex-wrap sm:items-end">
                <div class="w-full sm:w-auto"><label class="block text-sm font-medium text-gray-700 mb-1">From</label><input x-model="from" type="date" class="form-input-custom w-full"></div>
                <div class="w-full sm:w-auto"><label class="block text-sm font-medium text-gray-700 mb-1">To</label><input x-model="to" type="date" class="form-input-custom w-full"></div>
                <div class="w-full sm:w-auto"><button @click="fetchReport()" class="btn-primary w-full sm:w-auto" :disabled="loading"><span x-show="loading" class="spinner mr-1"></span> Generate</button></div>
            </div>
        </div>
    </div>

    {{-- Sales Report --}}
    <div x-show="type==='sales'" class="card">
        <div class="card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold">Sales Report</h3>
            <button x-show="salesData" @click="printReport()" class="btn-secondary text-sm w-full sm:w-auto"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Print</button>
        </div>
        <div class="card-body" x-show="salesData">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 rounded-lg p-4 text-center"><p class="text-sm text-blue-600">Total Sales</p><p class="text-xl font-bold text-blue-800" x-text="RepairBox.formatCurrency(salesData?.total_sales || 0)"></p></div>
                <div class="bg-green-50 rounded-lg p-4 text-center"><p class="text-sm text-green-600">Invoices</p><p class="text-xl font-bold text-green-800" x-text="salesData?.total_invoices || 0"></p></div>
                <div class="bg-purple-50 rounded-lg p-4 text-center"><p class="text-sm text-purple-600">Items Sold</p><p class="text-xl font-bold text-purple-800" x-text="salesData?.total_items || 0"></p></div>
                <div class="bg-orange-50 rounded-lg p-4 text-center"><p class="text-sm text-orange-600">Avg Sale</p><p class="text-xl font-bold text-orange-800" x-text="RepairBox.formatCurrency(salesData?.average_sale || 0)"></p></div>
            </div>
            <div class="overflow-x-auto" x-show="salesData?.daily_sales?.length > 0">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Invoices</th><th>Amount</th></tr></thead>
                    <tbody>
                        <template x-for="d in (salesData?.daily_sales || [])" :key="d.date">
                            <tr><td x-text="d.date"></td><td x-text="d.count"></td><td x-text="RepairBox.formatCurrency(d.total)"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-body text-center text-gray-400 py-12" x-show="!salesData">Select a date range and click Generate</div>
    </div>

    {{-- Profit Report --}}
    <div x-show="type==='profit'" class="card">
        <div class="card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-semibold">Profit Report</h3>
            <button x-show="profitData" @click="printReport()" class="btn-secondary text-sm w-full sm:w-auto"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Print</button>
        </div>
        <div class="card-body" x-show="profitData">
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <div class="bg-green-50 rounded-lg p-4 text-center"><p class="text-sm text-green-600">Total Revenue</p><p class="text-xl font-bold text-green-800" x-text="RepairBox.formatCurrency(profitData?.total_revenue || 0)"></p></div>
                <div class="bg-red-50 rounded-lg p-4 text-center"><p class="text-sm text-red-600">Total Cost</p><p class="text-xl font-bold text-red-800" x-text="RepairBox.formatCurrency(profitData?.total_cost || 0)"></p></div>
                <div class="bg-blue-50 rounded-lg p-4 text-center"><p class="text-sm text-blue-600">Gross Profit</p><p class="text-xl font-bold text-blue-800" x-text="RepairBox.formatCurrency(profitData?.gross_profit || 0)"></p></div>
                <div class="bg-purple-50 rounded-lg p-4 text-center"><p class="text-sm text-purple-600">Net Profit</p><p class="text-xl font-bold text-purple-800" x-text="RepairBox.formatCurrency(profitData?.net_profit || 0)"></p></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-orange-50 rounded-lg p-4"><p class="text-sm text-orange-600">Total Expenses</p><p class="text-xl font-bold text-orange-800" x-text="RepairBox.formatCurrency(profitData?.total_expenses || 0)"></p></div>
                <div class="bg-teal-50 rounded-lg p-4"><p class="text-sm text-teal-600">Profit Margin</p><p class="text-xl font-bold text-teal-800" x-text="(profitData?.profit_margin || 0).toFixed(1) + '%'"></p></div>
            </div>
            <div class="overflow-x-auto" x-show="profitData?.category_breakdown?.length > 0">
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Category Breakdown</h4>
                <table class="data-table">
                    <thead><tr><th>Category</th><th>Revenue</th><th>Cost</th><th>Profit</th></tr></thead>
                    <tbody>
                        <template x-for="c in (profitData?.category_breakdown || [])" :key="c.category">
                            <tr>
                                <td x-text="c.category"></td>
                                <td x-text="RepairBox.formatCurrency(c.revenue)"></td>
                                <td x-text="RepairBox.formatCurrency(c.cost)"></td>
                                <td :class="c.profit >= 0 ? 'text-green-600' : 'text-red-600'" x-text="RepairBox.formatCurrency(c.profit)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-body text-center text-gray-400 py-12" x-show="!profitData">Select a date range and click Generate</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function reportsPage() {
    return {
        type: 'sales', from: '', to: '', loading: false, salesData: null, profitData: null,
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('type')) this.type = p.get('type');
            const today = new Date(); const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            this.from = p.get('from') || firstDay.toISOString().split('T')[0];
            this.to = p.get('to') || today.toISOString().split('T')[0];
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.type !== 'sales') params.set('type', this.type);
            if (this.from) params.set('from', this.from);
            if (this.to) params.set('to', this.to);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async fetchReport() {
            if(!this.from || !this.to) { RepairBox.toast('Select date range', 'error'); return; }
            this.loading = true;
            const r = await RepairBox.ajax(`/reports/${this.type}?from=${this.from}&to=${this.to}`);
            this.loading = false;
            if(r.data) { if(this.type === 'sales') this.salesData = r.data; else this.profitData = r.data; }
            this.updateUrl();
        },
        printReport() { window.print(); }
    };
}
</script>
@endpush
