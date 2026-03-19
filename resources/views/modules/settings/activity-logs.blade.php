@extends('layouts.app')
@section('page-title', 'Activity Logs')

@section('content')
<div x-data="activityLogsPage()" x-init="load()">
    <div class="page-header-inline">
        <div class="page-header-inline-copy">
            <h2 class="page-header-inline-title">Activity Logs</h2>
            <p class="page-header-inline-description">Review system activity with module-level filtering in a tighter audit view.</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="secondary-tabs items-end mb-6">
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
            <select x-model="filterModule" @change="page=1;load()" class="form-select-custom">
                <option value="">All Modules</option>
                <template x-for="m in modules" :key="m"><option :value="m" x-text="m"></option></template>
            </select>
        </div>
        <div><button @click="filterModule='';page=1;load()" class="btn-secondary text-sm">Clear Filters</button></div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Date</th><th>User</th><th>Module</th><th>Action</th><th>Description</th><th>IP</th></tr></thead>
                    <tbody>
                        <template x-for="log in items" :key="log.id">
                            <tr>
                                <td class="whitespace-nowrap text-sm" x-text="new Date(log.created_at).toLocaleString()"></td>
                                <td class="font-medium" x-text="log.user ? log.user.name : '-'"></td>
                                <td><span class="badge badge-primary" x-text="log.module"></span></td>
                                <td>
                                    <span class="badge" :class="{'badge-success': log.action==='create', 'badge-warning': log.action==='update', 'badge-danger': log.action==='delete', 'badge-info': !['create','update','delete'].includes(log.action)}" x-text="log.action"></span>
                                </td>
                                <td class="text-sm text-gray-600 max-w-xs truncate" x-text="log.description || '-'"></td>
                                <td class="text-sm text-gray-500" x-text="log.ip_address || '-'"></td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0"><td colspan="6" class="text-center text-gray-400 py-8">No activity logs</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div x-show="lastPage > 1" class="flex items-center justify-center gap-2 mt-4">
        <button @click="page--; load()" :disabled="page <= 1" class="btn-secondary text-sm">&laquo; Prev</button>
        <span class="text-sm text-gray-600" x-text="'Page ' + page + ' of ' + lastPage"></span>
        <button @click="page++; load()" :disabled="page >= lastPage" class="btn-secondary text-sm">Next &raquo;</button>
    </div>
</div>
@endsection

@push('scripts')
<script>
function activityLogsPage() {
    return {
        items: [], page: 1, lastPage: 1, filterModule: '',
        modules: ['invoices','repairs','customers','products','inventory','purchases','expenses','recharges','services','users','settings','po'],
        async load() {
            let url = `/activity-logs?page=${this.page}`;
            if(this.filterModule) url += `&module=${encodeURIComponent(this.filterModule)}`;
            const r = await RepairBox.ajax(url);
            if(r.data) { this.items = r.data.data || r.data; this.lastPage = r.data.last_page || 1; }
        }
    };
}
</script>
@endpush
