@extends('layouts.app')
@section('page-title', 'Users')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .users-workspace .workspace-toolbar,
    .users-workspace .workspace-filterbar,
    .users-workspace .workspace-table-card {
        border-radius: 1.2rem;
    }

    .users-workspace .workspace-table-scroll .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .users-workspace .workspace-table-scroll .data-table th,
    .users-workspace .workspace-table-scroll .data-table td {
        padding-top: 0.78rem;
        padding-bottom: 0.78rem;
    }
</style>

<div x-data="usersPage()" x-init="load()" class="workspace-screen users-workspace">
    <x-ui.action-bar title="Team Access" description="Manage user accounts, roles, and status from a contained single-screen table view.">
        <a href="/users/create" class="btn-primary inline-flex w-full items-center justify-center sm:w-auto"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add User</a>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> users</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">User Accounts</h3>
                <p class="text-sm text-slate-500">Roles and account status remain visible inside the same fixed workspace.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(u, i) in items" :key="u.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="u.name"></td>
                                <td x-text="u.email"></td>
                                <td><span class="badge badge-primary" x-text="u.role ? u.role.name : '-'"></span></td>
                                <td><span class="badge" :class="u.status === 'active' ? 'badge-success' : 'badge-danger'" x-text="u.status || 'active'"></span></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(u)" class="text-primary-600 hover:text-primary-800 mr-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(u)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No users found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-40"></div></td>
                                    <td><div class="skeleton h-3 w-24 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-20 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
    </x-ui.table-card>

    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container admin-modal">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit User</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email *</label><input x-model="form.email" type="email" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Password (leave blank to keep)</label><input x-model="form.password" type="password" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label><input x-model="form.password_confirmation" type="password" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select x-model="form.role_id" class="form-select-custom"><option value="">Select Role</option><template x-for="r in roles" :key="r.id"><option :value="r.id" x-text="r.name"></option></template></select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select x-model="form.status" class="form-select-custom"><option value="active">Active</option><option value="inactive">Inactive</option></select>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end"><button @click="showModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button><button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function usersPage() {
    return {
        items: [], roles: [], showModal: false, editing: null, saving: false, loading: true,
        form: { name: '', email: '', password: '', password_confirmation: '', role_id: '', status: 'active' },
        async load() { this.loading = true; const r = await RepairBox.ajax('/admin/users'); if(r.data) this.items = r.data.data || r.data; this.loading = false; },
        async edit(u) {
            const r = await RepairBox.ajax('/admin/roles'); if(r.data) this.roles = r.data;
            this.editing = u.id; this.form = { name: u.name, email: u.email, password: '', password_confirmation: '', role_id: u.role_id, status: u.status || 'active' }; this.showModal = true;
        },
        async save() {
            this.saving = true;
            const data = {...this.form}; if(!data.password) { delete data.password; delete data.password_confirmation; }
            const r = await RepairBox.ajax(`/admin/users/${this.editing}`, 'PUT', data);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(u) {
            if(!await RepairBox.confirm('Delete this user?')) return;
            const r = await RepairBox.ajax(`/admin/users/${u.id}`, 'DELETE'); if(r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
