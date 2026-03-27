@extends('layouts.app')
@section('page-title', 'Credit Notes')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="creditNotesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Manage credit notes and process refunds</p>
    </div>

    <!-- Filters -->
    <div class="card mb-4 !flex-none">
        <div class="card-body py-3">
            <div class="flex flex-wrap items-center gap-3">
                <input x-model="filters.search" @input.debounce.400ms="load()" type="text" class="form-input-custom w-56" placeholder="Search CN# or customer...">
                <select x-model="filters.status" @change="load()" class="form-select-custom w-40">
                    <option value="">All Statuses</option>
                    <option value="draft">Draft</option>
                    <option value="approved">Approved</option>
                    <option value="partially_refunded">Partially Refunded</option>
                    <option value="fully_refunded">Fully Refunded</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <select x-model="filters.source_type" @change="load()" class="form-select-custom w-36">
                    <option value="">All Sources</option>
                    <option value="invoice">Invoice</option>
                    <option value="repair">Repair</option>
                </select>
                <input x-model="filters.date_from" @change="load()" type="date" class="form-input-custom w-36">
                <input x-model="filters.date_to" @change="load()" type="date" class="form-input-custom w-36">
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">CN Number</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Source</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Refunded</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="cn in items" :key="cn.id">
                        <tr class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer" @click="window.location.href = '/admin/credit-notes/' + cn.id">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-primary-600" x-text="cn.credit_note_number"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="badge text-xs" :class="cn.source_type === 'invoice' ? 'badge-info' : 'badge-warning'" x-text="cn.source_type === 'invoice' ? 'Invoice' : 'Repair'"></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700" x-text="cn.customer?.name || '—'"></td>
                            <td class="px-4 py-3 text-right font-semibold text-sm" x-text="'₹' + Number(cn.total_amount).toLocaleString('en-IN', {minimumFractionDigits:2})"></td>
                            <td class="px-4 py-3 text-right text-sm" :class="cn.refunded_amount > 0 ? 'text-emerald-600 font-medium' : 'text-gray-400'" x-text="'₹' + Number(cn.refunded_amount).toLocaleString('en-IN', {minimumFractionDigits:2})"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge text-xs"
                                    :class="{
                                        'badge-secondary': cn.status === 'draft',
                                        'badge-info': cn.status === 'approved',
                                        'badge-warning': cn.status === 'partially_refunded',
                                        'badge-success': cn.status === 'fully_refunded',
                                        'badge-danger': cn.status === 'cancelled',
                                    }" x-text="cn.status.replace('_', ' ')"></span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500" x-text="new Date(cn.created_at).toLocaleDateString('en-IN')"></td>
                            <td class="px-4 py-3 text-center">
                                <a :href="'/admin/credit-notes/' + cn.id" class="text-primary-600 hover:text-primary-800 text-sm font-medium" @click.stop>View</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <template x-if="pagination.lastPage > 1">
        <div class="flex justify-center gap-2 mt-4">
            <template x-for="p in pagination.lastPage" :key="p">
                <button @click="goToPage(p)" class="px-3 py-1 rounded text-sm"
                    :class="p === pagination.currentPage ? 'bg-primary-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border'"
                    x-text="p"></button>
            </template>
        </div>
    </template>

    <!-- Loading -->
    <template x-if="loading">
        <div class="space-y-2 mt-4">
            <template x-for="i in 5" :key="'sk'+i"><div class="skeleton h-12 rounded"></div></template>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script>
function creditNotesPage() {
    return {
        items: [],
        loading: true,
        filters: { search: '', status: '', source_type: '', date_from: '', date_to: '' },
        pagination: { currentPage: 1, lastPage: 1 },

        async load() {
            this.loading = true;
            const params = new URLSearchParams({
                page: this.pagination.currentPage,
                ...Object.fromEntries(Object.entries(this.filters).filter(([,v]) => v))
            });
            const r = await RepairBox.ajax('/admin/credit-notes?' + params);
            if (r.data) {
                this.items = r.data.data || r.data;
                this.pagination.currentPage = r.data.current_page || 1;
                this.pagination.lastPage = r.data.last_page || 1;
            }
            this.loading = false;
        },

        goToPage(p) {
            this.pagination.currentPage = p;
            this.load();
        }
    };
}
</script>
@endpush
