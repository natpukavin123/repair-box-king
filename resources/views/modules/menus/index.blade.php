@extends('layouts.app')
@section('page-title', 'Menu Management')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="menusPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm text-gray-500">Manage sidebar navigation menus</p>
        <button @click="openCreate()" class="btn-primary">
            <svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Menu Item
        </button>
    </div>

    <!-- Menu Groups by Section -->
    <div class="space-y-4">
        <template x-for="(menus, section) in groupedMenus" :key="section">
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wider" x-text="section || 'General'"></h3>
                    <span class="badge badge-info text-xs" x-text="menus.length + ' items'"></span>
                </div>
                <div class="card-body p-0">
                    <div class="divide-y divide-gray-100">
                        <template x-for="menu in menus" :key="menu.id">
                            <div class="flex items-center justify-between px-6 py-3 hover:bg-gray-50 transition-colors group">
                                <div class="flex items-center gap-3">
                                    <!-- Icon Preview -->
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center"
                                         :class="menu.is_active ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-400'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="menu.icon || 'M4 6h16M4 12h16M4 18h16'"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-800" x-text="menu.name"></span>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-gray-400" x-text="menu.route || 'No route'"></span>
                                            <template x-if="menu.module">
                                                <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-600" x-text="menu.module"></span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <!-- Active Toggle -->
                                    <button @click.stop="toggleActive(menu)" class="text-xs px-2 py-1 rounded-lg transition"
                                            :class="menu.is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                                            x-text="menu.is_active ? 'Active' : 'Inactive'"></button>
                                    <button @click.stop="openEdit(menu)" class="text-primary-600 hover:text-primary-800 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button @click.stop="remove(menu)" class="text-red-600 hover:text-red-800 p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>

                                <!-- Children -->
                                <template x-if="menu.children && menu.children.length > 0">
                                    <div class="ml-12 mt-1 space-y-1 border-l-2 border-gray-100 pl-3">
                                        <template x-for="child in menu.children" :key="child.id">
                                            <div class="flex items-center justify-between py-1.5 text-sm text-gray-600">
                                                <span x-text="child.name"></span>
                                                <div class="flex items-center gap-1">
                                                    <button @click.stop="openEdit(child)" class="text-gray-400 hover:text-primary-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                                    <button @click.stop="remove(child)" class="text-gray-400 hover:text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading -->
    <template x-if="loading">
        <div class="space-y-4">
            <template x-for="i in 3" :key="'sk'+i">
                <div class="card"><div class="card-header"><div class="skeleton h-4 w-24"></div></div><div class="card-body"><template x-for="j in 3" :key="'skr'+j"><div class="skeleton-row"><div class="skeleton h-8 w-8 rounded-lg"></div><div class="skeleton h-4 w-32"></div></div></template></div></div>
            </template>
        </div>
    </template>

    <!-- Create/Edit Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak @keydown.escape.window="showModal = false">
        <div class="modal-container" @click.stop>
            <div class="modal-header">
                <h3 class="text-lg font-semibold" x-text="editing ? 'Edit Menu Item' : 'Add Menu Item'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Name *</label>
                            <input x-model="form.name" type="text" class="form-input-custom" placeholder="Menu label">
                        </div>
                        <div>
                            <label class="form-label">Route</label>
                            <input x-model="form.route" type="text" class="form-input-custom" placeholder="/example">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Section</label>
                            <select x-model="form.section" class="form-select-custom">
                                <option value="">No Section</option>
                                <option value="Sales">Sales</option>
                                <option value="Repair">Repair</option>
                                <option value="Products">Products</option>
                                <option value="Services">Services</option>
                                <option value="Finance">Finance</option>
                                <option value="System">System</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Module (Permission Link)</label>
                            <input x-model="form.module" type="text" class="form-input-custom" placeholder="e.g. dashboard, repairs">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label">Sort Order</label>
                            <input x-model="form.sort_order" type="number" class="form-input-custom" min="0">
                        </div>
                        <div>
                            <label class="form-label">Parent Menu</label>
                            <select x-model="form.parent_id" class="form-select-custom">
                                <option value="">None (Top Level)</option>
                                <template x-for="m in allMenus" :key="m.id">
                                    <option :value="m.id" x-text="m.name" :disabled="editing && m.id == editing"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Icon (SVG Path)</label>
                        <div class="flex gap-2">
                            <input x-model="form.icon" type="text" class="form-input-custom flex-1" placeholder="M4 6h16M4 12h16M4 18h16">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="form.icon || 'M4 6h16M4 12h16M4 18h16'"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <input x-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" id="is_active">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="save()" class="btn-primary" :disabled="saving || !form.name">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    <span x-text="editing ? 'Update' : 'Create'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function menusPage() {
    return {
        items: [],
        allMenus: [],
        groupedMenus: {},
        showModal: false,
        editing: null,
        saving: false,
        loading: true,
        form: { name: '', route: '', icon: '', module: '', section: '', parent_id: '', sort_order: 0, is_active: true },

        async load() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/menus');
            if (r.data) {
                this.items = r.data;
                this.allMenus = r.data;
                // Group by section
                this.groupedMenus = {};
                r.data.forEach(menu => {
                    const sec = menu.section || 'General';
                    if (!this.groupedMenus[sec]) this.groupedMenus[sec] = [];
                    this.groupedMenus[sec].push(menu);
                });
            }
            this.loading = false;
        },

        openCreate() {
            this.editing = null;
            this.form = { name: '', route: '', icon: '', module: '', section: '', parent_id: '', sort_order: 0, is_active: true };
            this.showModal = true;
        },

        openEdit(menu) {
            this.editing = menu.id;
            this.form = {
                name: menu.name,
                route: menu.route || '',
                icon: menu.icon || '',
                module: menu.module || '',
                section: menu.section || '',
                parent_id: menu.parent_id || '',
                sort_order: menu.sort_order || 0,
                is_active: menu.is_active !== false,
            };
            this.showModal = true;
        },

        async save() {
            this.saving = true;
            const url = this.editing ? `/menus/${this.editing}` : '/admin/menus';
            const method = this.editing ? 'PUT' : 'POST';
            const data = {...this.form};
            if (!data.parent_id) data.parent_id = null;
            const r = await RepairBox.ajax(url, method, data);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast(this.editing ? 'Menu updated' : 'Menu created', 'success');
                this.showModal = false;
                this.load();
            }
        },

        async toggleActive(menu) {
            const r = await RepairBox.ajax(`/admin/menus/${menu.id}`, 'PUT', { ...menu, is_active: !menu.is_active });
            if (r.success !== false) {
                RepairBox.toast(menu.is_active ? 'Menu hidden' : 'Menu visible', 'success');
                this.load();
            }
        },

        async remove(menu) {
            if (!await RepairBox.confirm('Delete this menu item?')) return;
            const r = await RepairBox.ajax(`/admin/menus/${menu.id}`, 'DELETE');
            if (r.success !== false) {
                RepairBox.toast('Menu deleted', 'success');
                this.load();
            }
        }
    };
}
</script>
@endpush
