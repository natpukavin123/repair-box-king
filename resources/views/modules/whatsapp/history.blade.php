@extends('layouts.app')
@section('title', 'WA History')
@section('page-title', 'WA History')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .wa-workspace { gap: 0.7rem; }
    .wa-card {
        border-radius: 1.35rem;
        border: 1px solid rgba(148,163,184,0.16);
        background: linear-gradient(180deg,rgba(255,255,255,0.9),rgba(250,252,255,0.82));
        box-shadow: 0 26px 60px -42px rgba(15,23,42,0.38);
        overflow: hidden;
    }
    .wa-card .card-header {
        padding: 0.9rem 1.1rem;
        border-bottom: 1px solid rgba(148,163,184,0.12);
        background: linear-gradient(180deg,rgba(255,255,255,0.72),rgba(241,245,255,0.48));
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;
    }
    .wa-nav-tab {
        display: flex; gap: 0.25rem; padding: 0.25rem;
        background: rgba(148,163,184,0.1); border-radius: 0.9rem;
        border: 1px solid rgba(148,163,184,0.14); flex-shrink: 0;
        overflow-x: auto; -webkit-overflow-scrolling: touch;
    }
    .wa-nav-tab a {
        flex: 1; text-align: center; padding: 0.5rem 0.7rem;
        border-radius: 0.7rem; font-size: 0.78rem; font-weight: 500;
        color: #64748b; text-decoration: none; transition: all 0.18s;
        white-space: nowrap; min-width: 0;
    }
    .wa-nav-tab a.active { background: white; color: #16a34a; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

    /* Mobile history cards */
    .hist-mobile-card {
        padding: 0.75rem 0.9rem;
        border-bottom: 1px solid rgba(226,232,240,0.7);
    }
    .hist-mobile-card:last-child { border-bottom: none; }

    @media (max-width: 767px) {
        .wa-nav-tab a { font-size: 0.74rem; padding: 0.45rem 0.5rem; }
    }
</style>

<div class="flex flex-col wa-workspace" style="min-height:0;" x-data="waHistory()" x-init="init()">

    {{-- Tabs --}}
    <div class="wa-nav-tab">
        <a href="/admin/whatsapp">Dashboard</a>
        <a href="/admin/whatsapp/groups">Groups</a>
        <a href="/admin/whatsapp/schedules">Schedules</a>
        <a href="/admin/whatsapp/history" class="active">History</a>
    </div>

    {{-- Toolbar --}}
    <div class="wa-card">
        <div class="card-header flex-wrap">
            <span class="text-sm font-semibold text-slate-700">
                Message History
                <span class="ml-1 sm:ml-2 text-xs font-normal text-slate-400" x-text="total + ' records'"></span>
            </span>
            <div class="flex flex-wrap gap-2 items-center w-full sm:w-auto">
                {{-- Search --}}
                <input x-model="filters.search" @input.debounce.400ms="load()"
                    type="text" class="form-input text-sm py-1.5 flex-1 sm:flex-none sm:w-36" placeholder="Search...">

                {{-- Status --}}
                <select x-model="filters.status" @change="load()" class="form-input text-sm py-1.5 w-24 sm:w-28">
                    <option value="">All</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                </select>

                {{-- Date --}}
                <input x-model="filters.date" @change="load()" type="date" class="form-input text-sm py-1.5 flex-1 sm:flex-none">

                {{-- Clear history --}}
                <button @click="clearOld()" class="btn btn-sm text-red-500 border-red-200 hover:bg-red-50 text-xs">
                    Clear Old
                </button>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="wa-card lg:flex-1 flex flex-col lg:min-h-0" style="min-height:200px;">
        <div class="flex-1 overflow-auto">
            {{-- Desktop table --}}
            <table class="data-table w-full hidden md:table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Schedule</th>
                        <th>Group</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th class="text-right">Del</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="logs.length === 0">
                        <tr><td colspan="7" class="text-center text-slate-400 py-8">No records found.</td></tr>
                    </template>
                    <template x-for="(log, i) in logs" :key="log.id">
                        <tr>
                            <td class="text-xs text-slate-400" x-text="(currentPage-1)*50 + i + 1"></td>
                            <td class="text-xs text-slate-600" x-text="log.schedule_name || '—'"></td>
                            <td>
                                <div class="text-sm font-medium text-slate-700" x-text="log.group_name"></div>
                                <div class="text-xs text-slate-400 font-mono" x-text="log.group_wa_id"></div>
                            </td>
                            <td>
                                <div class="text-sm text-slate-700 max-w-xs truncate" x-text="log.message" :title="log.message"></div>
                                <template x-if="log.error">
                                    <div class="text-xs text-red-500 mt-0.5" x-text="log.error"></div>
                                </template>
                            </td>
                            <td>
                                <span :class="log.status==='sent' ? 'badge-success' : 'badge-danger'" class="badge text-xs" x-text="log.status"></span>
                            </td>
                            <td class="text-xs text-slate-500" x-text="new Date(log.sent_at).toLocaleString()"></td>
                            <td class="text-right">
                                <button @click="deleteLog(log)" class="icon-action icon-action-danger">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            {{-- Mobile card list --}}
            <div class="md:hidden">
                <template x-if="logs.length === 0">
                    <div class="text-center text-slate-400 py-8 text-sm">No records found.</div>
                </template>
                <template x-for="(log, i) in logs" :key="log.id">
                    <div class="hist-mobile-card">
                        <div class="flex items-start justify-between gap-2 mb-1.5">
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-slate-700 truncate" x-text="log.group_name"></div>
                                <div class="text-xs text-slate-400" x-text="log.schedule_name || 'Manual'"></div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <span :class="log.status==='sent' ? 'badge-success' : 'badge-danger'" class="badge text-xs" x-text="log.status"></span>
                                <button @click="deleteLog(log)" class="icon-action icon-action-danger">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                        <div class="text-sm text-slate-600 mb-1.5 line-clamp-2" x-text="log.message"></div>
                        <template x-if="log.error">
                            <div class="text-xs text-red-500 mb-1" x-text="log.error"></div>
                        </template>
                        <div class="text-xs text-slate-400" x-text="new Date(log.sent_at).toLocaleString()"></div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-500">Page <span x-text="currentPage"></span> of <span x-text="lastPage"></span></span>
            <div class="flex gap-1">
                <button @click="prevPage()" :disabled="currentPage<=1" class="btn btn-sm">←</button>
                <button @click="nextPage()" :disabled="currentPage>=lastPage" class="btn btn-sm">→</button>
            </div>
        </div>
    </div>
</div>

<script>
function waHistory() {
    return {
        logs: [],
        total: 0,
        currentPage: 1,
        lastPage: 1,
        filters: { search: '', status: '', date: '' },

        async init() { await this.load(); },

        async load() {
            this.currentPage = 1;
            await this.fetch();
        },

        async fetch() {
            const params = { ...this.filters, page: this.currentPage };
            const r = await RepairBox.ajax('/admin/whatsapp/history', 'GET', params);
            if (r.data) {
                this.logs        = r.data.data    || r.data;
                this.total       = r.data.total   || (r.data.data ? r.data.total : 0);
                this.currentPage = r.data.current_page || 1;
                this.lastPage    = r.data.last_page    || 1;
            }
        },

        async prevPage() {
            if (this.currentPage > 1) { this.currentPage--; await this.fetch(); }
        },

        async nextPage() {
            if (this.currentPage < this.lastPage) { this.currentPage++; await this.fetch(); }
        },

        async deleteLog(log) {
            if (!await RepairBox.confirm('Delete this log entry?')) return;
            const r = await RepairBox.ajax(`/admin/whatsapp/history/${log.id}`, 'DELETE');
            if (r.success) { RepairBox.toast('Deleted', 'success'); await this.fetch(); }
        },

        async clearOld() {
            const days = prompt('Delete logs older than how many days?', '30');
            if (!days || isNaN(days)) return;
            const r = await RepairBox.ajax('/admin/whatsapp/history/clear', 'POST', { days });
            if (r.success) {
                RepairBox.toast(`Deleted ${r.data ? r.data.deleted : ''} records`, 'success');
                await this.load();
            }
        }
    };
}
</script>
@endsection
