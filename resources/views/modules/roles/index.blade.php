@extends('layouts.app')
@section('page-title', 'Roles & Permissions')
@section('content-class', 'workspace-content')

@section('content')
<div x-data="rolesPage()" x-init="load()" class="workspace-screen">
    <x-ui.action-bar title="Roles & Permissions" description="Keep role management and permission mapping inside the same contained admin workspace.">
        <button @click="openCreate()" class="btn-primary w-full sm:w-auto">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Role
        </button>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> roles</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Role Matrix</h3>
                <p class="text-sm text-slate-500">Each role card stays in a fixed internal canvas instead of pushing the full page longer.</p>
            </div>
        </x-slot:header>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-4 sm:p-5">
        <template x-for="role in items" :key="role.id">
            <div class="card hover:shadow-md transition-shadow duration-200 cursor-pointer" @click="openEdit(role)">
                <div class="card-body">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                                 :class="role.name === 'Admin' ? 'bg-amber-100 text-amber-700' : 'bg-primary-100 text-primary-700'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900" x-text="role.name"></h3>
                                <p class="text-xs text-gray-500" x-text="role.description || 'No description'"></p>
                            </div>
                        </div>
                        <template x-if="role.name === 'Admin'">
                            <span class="badge badge-warning text-xs">System</span>
                        </template>
                    </div>
                    <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span x-text="(role.users_count || 0) + ' Users'"></span>
                        </div>
                        <div class="flex items-center gap-1.5 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="(role.permissions_count || 0) + ' Permissions'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <template x-if="loading">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 p-5 pt-0">
            <template x-for="i in 4" :key="'sk'+i">
                <div class="card"><div class="card-body"><div class="flex items-center gap-3"><div class="skeleton w-10 h-10 rounded-xl"></div><div><div class="skeleton h-4 w-24 mb-2"></div><div class="skeleton h-3 w-32"></div></div></div><div class="flex gap-4 mt-4 pt-3 border-t border-gray-100"><div class="skeleton h-3 w-20"></div><div class="skeleton h-3 w-24"></div></div></div></div>
            </template>
        </div>
    </template>
    </x-ui.table-card>

    <div x-show="showModal" class="modal-overlay" x-cloak @keydown.escape.window="showModal = false">
        <div class="modal-container modal-xl" @click.stop>
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-semibold" x-text="editing ? 'Edit Role' : 'Create Role'"></h3>
                    <p class="text-sm text-gray-500 mt-0.5">Configure role details and assign module permissions</p>
                </div>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body max-h-[70vh] overflow-y-auto">
                <x-ui.form-section title="Role Details" description="Define the role name and a short explanation for staff.">
                    <x-ui.input-field label="Role Name" x-model="form.name" placeholder="e.g. Manager" required />
                    <x-ui.input-field label="Description" x-model="form.description" placeholder="Brief description of this role" />
                </x-ui.form-section>

                <x-ui.form-section title="Module Permissions" description="Choose which actions this role is allowed to perform." gridClass="space-y-4" class="mt-5">
                    <div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between">
                        <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Module Permissions
                        </h4>
                        <div class="flex flex-wrap items-center gap-2">
                            <button @click="selectAllPermissions()" class="text-xs text-primary-600 hover:text-primary-800 font-medium px-2 py-1 rounded hover:bg-primary-50 transition">Select All</button>
                            <span class="text-gray-300">|</span>
                            <button @click="deselectAllPermissions()" class="text-xs text-gray-500 hover:text-gray-700 font-medium px-2 py-1 rounded hover:bg-gray-100 transition">Deselect All</button>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-lg px-4 py-2 mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <span class="text-sm text-gray-600">
                            <span class="font-semibold text-primary-600" x-text="form.permissions.length"></span> of
                            <span class="font-semibold" x-text="totalPermissions"></span> permissions selected
                        </span>
                        <div class="w-full sm:w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-primary-600 h-2 rounded-full transition-all duration-300"
                                 :style="'width: ' + (totalPermissions > 0 ? (form.permissions.length / totalPermissions * 100) : 0) + '%'"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">
                        <template x-for="group in permissionGroups" :key="group.module">
                            <div class="border border-gray-200 rounded-xl overflow-hidden hover:border-primary-300 transition-colors"
                                 :class="isModuleFullySelected(group) ? 'border-primary-300 bg-primary-50/30' : ''">
                                <!-- Module Header -->
                                <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200 cursor-pointer"
                                     @click="toggleModuleAll(group)">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full"
                                             :class="isModuleFullySelected(group) ? 'bg-primary-500' : (isModulePartiallySelected(group) ? 'bg-amber-400' : 'bg-gray-300')"></div>
                                        <span class="font-medium text-sm text-gray-800 capitalize" x-text="group.display_name"></span>
                                    </div>
                                    <span class="text-xs px-2 py-0.5 rounded-full"
                                          :class="isModuleFullySelected(group) ? 'bg-primary-100 text-primary-700' : 'bg-gray-200 text-gray-600'"
                                          x-text="getModuleSelectedCount(group) + '/' + group.permissions.length"></span>
                                </div>
                                <!-- Permission Checkboxes -->
                                <div class="px-4 py-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <template x-for="perm in group.permissions" :key="perm.id">
                                        <label class="flex items-center gap-2 cursor-pointer group/perm py-1 px-2 rounded-lg hover:bg-gray-50 transition-colors">
                                            <input type="checkbox"
                                                   :value="perm.id"
                                                   :checked="form.permissions.includes(perm.id)"
                                                   @change="togglePermission(perm.id)"
                                                   class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                            <span class="text-sm text-gray-700 group-hover/perm:text-gray-900 capitalize"
                                                  x-text="perm.name.split('.')[1]"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </x-ui.form-section>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <template x-if="editing && !isSystemRole">
                    <button @click="remove()" class="btn-danger w-full sm:mr-auto sm:w-auto" :disabled="saving">Delete Role</button>
                </template>
                <button @click="showModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving || !form.name">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    <span x-text="editing ? 'Update Role' : 'Create Role'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function rolesPage() {
    return {
        items: [],
        permissionGroups: [],
        totalPermissions: 0,
        showModal: false,
        editing: null,
        isSystemRole: false,
        saving: false,
        loading: true,
        form: { name: '', description: '', permissions: [] },

        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/roles');
            if (r.data) this.items = r.data;
            this.loading = false;
        },

        async loadPermissions() {
            const r = await RepairBox.ajax('/admin/permissions/grouped');
            if (r.data) {
                this.permissionGroups = r.data;
                this.totalPermissions = r.data.reduce((sum, g) => sum + g.permissions.length, 0);
            }
        },

        async openCreate() {
            await this.loadPermissions();
            this.editing = null;
            this.isSystemRole = false;
            this.form = { name: '', description: '', permissions: [] };
            this.showModal = true;
        },

        async openEdit(role) {
            await this.loadPermissions();
            const r = await RepairBox.ajax(`/roles/${role.id}`);
            if (r.data) {
                this.editing = role.id;
                this.isSystemRole = r.data.name === 'Admin';
                this.form = {
                    name: r.data.name,
                    description: r.data.description || '',
                    permissions: (r.data.permissions || []).map(p => p.id),
                };
            }
            this.showModal = true;
        },

        togglePermission(id) {
            const idx = this.form.permissions.indexOf(id);
            if (idx > -1) {
                this.form.permissions.splice(idx, 1);
            } else {
                this.form.permissions.push(id);
            }
        },

        toggleModuleAll(group) {
            const allIds = group.permissions.map(p => p.id);
            const allSelected = allIds.every(id => this.form.permissions.includes(id));
            if (allSelected) {
                this.form.permissions = this.form.permissions.filter(id => !allIds.includes(id));
            } else {
                allIds.forEach(id => {
                    if (!this.form.permissions.includes(id)) this.form.permissions.push(id);
                });
            }
        },

        isModuleFullySelected(group) {
            return group.permissions.every(p => this.form.permissions.includes(p.id));
        },

        isModulePartiallySelected(group) {
            return group.permissions.some(p => this.form.permissions.includes(p.id)) && !this.isModuleFullySelected(group);
        },

        getModuleSelectedCount(group) {
            return group.permissions.filter(p => this.form.permissions.includes(p.id)).length;
        },

        selectAllPermissions() {
            this.form.permissions = this.permissionGroups.flatMap(g => g.permissions.map(p => p.id));
        },

        deselectAllPermissions() {
            this.form.permissions = [];
        },

        async save() {
            this.saving = true;
            const url = this.editing ? `/roles/${this.editing}` : '/admin/roles';
            const method = this.editing ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast(this.editing ? 'Role updated' : 'Role created', 'success');
                this.showModal = false;
                this.load();
            }
        },

        async remove() {
            if (!await RepairBox.confirm('Delete this role? This cannot be undone.')) return;
            this.saving = true;
            const r = await RepairBox.ajax(`/roles/${this.editing}`, 'DELETE');
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Role deleted', 'success');
                this.showModal = false;
                this.load();
            }
        }
    };
}
</script>
@endpush
