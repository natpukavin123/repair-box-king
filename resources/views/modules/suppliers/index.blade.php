@extends('layouts.app')
@section('page-title', 'Suppliers')
@section('content-class', 'workspace-content')

@section('content')
<div x-data="suppliersPage()" x-init="load()" class="workspace-screen">
    <x-ui.action-bar title="Supplier Records" description="Core supplier details stay in one contained workspace with the table scrolling inside the page.">
        <a href="/suppliers/create" class="btn-primary inline-flex w-full items-center justify-center sm:w-auto"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Supplier</a>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> suppliers</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Supplier List</h3>
                <p class="text-sm text-slate-500">View and update supplier records without the whole page growing longer.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>Company</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(item, i) in items" :key="item.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="item.name"></td>
                                <td x-text="item.company_name || '-'"></td>
                                <td x-text="item.phone || '-'"></td>
                                <td x-text="item.email || '-'"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(item)" class="text-primary-600 hover:text-primary-800 mr-2"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(item)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="6" class="text-center text-gray-400 py-8">No suppliers found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-36"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
    </x-ui.table-card>

    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Supplier</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Company</label><input x-model="form.company_name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input x-model="form.phone" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="form.email" type="email" class="form-input-custom"></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea x-model="form.address" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <button @click="showModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function suppliersPage() {
    return {
        items: [], showModal: false, editing: null, saving: false, loading: true,
        form: { name: '', company_name: '', email: '', phone: '', address: '' },
        async load() { this.loading = true; const r = await RepairBox.ajax('/suppliers'); if(r.data) this.items = r.data; this.loading = false; },
        edit(item) { this.editing = item.id; this.form = { name: item.name, company_name: item.company_name || '', email: item.email || '', phone: item.phone || '', address: item.address || '' }; this.showModal = true; },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/suppliers/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(item) {
            if (!await RepairBox.confirm('Delete this supplier?')) return;
            const r = await RepairBox.ajax(`/suppliers/${item.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
