@extends('layouts.app')
@section('page-title', 'Vendors')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="vendorsPage()" x-init="init()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <input x-model="search" @input.debounce.400ms="load()" type="text" class="form-input-custom max-w-sm" placeholder="Search vendors...">
        <a href="/vendors/create" class="btn-primary ml-3"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Vendor</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>Phone</th><th>Specialization</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(v, i) in items" :key="v.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="v.name"></td>
                                <td x-text="v.phone || '-'"></td>
                                <td x-text="v.specialization || '-'"></td>
                                <td><span class="badge" :class="v.status === 'active' ? 'badge-success' : 'badge-danger'" x-text="v.status || 'active'"></span></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(v)" class="text-primary-600 hover:text-primary-800 mr-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No vendors found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-36"></div></td>
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

    {{-- Pagination --}}
    <div x-show="lastPage > 1" class="flex items-center justify-center gap-2 mt-4">
        <button @click="page--; load()" :disabled="page <= 1" class="btn-secondary text-sm">&laquo; Prev</button>
        <span class="text-sm text-gray-600" x-text="'Page ' + page + ' of ' + lastPage"></span>
        <button @click="page++; load()" :disabled="page >= lastPage" class="btn-secondary text-sm">Next &raquo;</button>
    </div>

    {{-- Modal --}}
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Vendor</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input x-model="form.phone" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Specialization</label><input x-model="form.specialization" type="text" class="form-input-custom"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea x-model="form.address" class="form-input-custom" rows="2"></textarea></div>
                <template x-if="editing">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="form.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </template>
            </div>
            <div class="modal-footer"><button @click="showModal = false" class="btn-secondary">Cancel</button><button @click="save()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function vendorsPage() {
    return {
        items: [], showModal: false, editing: null, saving: false, search: '', page: 1, lastPage: 1, loading: true,
        form: { name: '', phone: '', address: '', specialization: '' },
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.search = p.get('search');
            if (p.has('page')) this.page = parseInt(p.get('page')) || 1;
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            if (this.page > 1) params.set('page', this.page);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            this.loading = true;
            const r = await RepairBox.ajax(`/vendors?page=${this.page}${this.search ? '&search='+encodeURIComponent(this.search) : ''}`);
            if(r.data) { this.items = r.data.data || r.data; this.lastPage = r.data.last_page || 1; }
            this.updateUrl();
            this.loading = false;
        },
        edit(v) { this.editing = v.id; this.form = { name: v.name, phone: v.phone || '', address: v.address || '', specialization: v.specialization || '', status: v.status || 'active' }; this.showModal = true; },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/vendors/${this.editing}`, 'PUT', this.form);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        }
    };
}
</script>
@endpush
