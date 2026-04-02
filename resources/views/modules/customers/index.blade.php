@extends('layouts.app')
@section('page-title', 'Customers')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .customers-workspace .workspace-toolbar,
    .customers-workspace .workspace-filterbar,
    .customers-workspace .workspace-table-card {
        border-radius: 1.2rem;
    }

    .customers-workspace .workspace-table-scroll .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .customers-workspace .workspace-table-scroll .data-table th,
    .customers-workspace .workspace-table-scroll .data-table td {
        padding-top: 0.78rem;
        padding-bottom: 0.78rem;
    }
</style>

<div x-data="customersPage()" x-init="init()" class="workspace-screen customers-workspace">
    <x-ui.action-bar title="Customer Desk" description="Search, open, and update customers without leaving the page.">
        <a href="/admin/customers/create" class="btn-primary inline-flex w-full items-center justify-center sm:w-auto"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Customer</a>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-group">
            <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search customers by name or mobile" class="form-input-custom workspace-search-input">
        </div>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> customers</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Customer Records</h3>
                <p class="text-sm text-slate-500">All customer entries stay inside one working screen.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Name</th><th>Mobile</th><th>Email</th><th>Loyalty Pts</th><th>Total Spent</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(c, i) in items" :key="c.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td class="font-medium" x-text="c.name"></td>
                                <td x-text="c.mobile_number"></td>
                                <td x-text="c.email || '-'"></td>
                                <td><span class="badge badge-info" x-text="c.loyalty_points || 0"></span></td>
                                <td x-text="'₹' + Number(c.total_spent || 0).toFixed(2)"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="view(c)" class="text-primary-600 hover:text-primary-800 mr-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                                    <button @click="edit(c)" class="text-primary-600 hover:text-primary-800 mr-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(c)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="7" class="text-center text-gray-400 py-8">No customers found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-32"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-36"></div></td>
                                    <td><div class="skeleton h-3 w-16 rounded-full"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
    </x-ui.table-card>

    <!-- Form Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container admin-modal modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Customer</h3><button @click="closeEditModal()" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div x-show="submitError" x-text="submitError" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 mb-4"></div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"><p x-show="formTried && !form.name.trim()" class="text-xs text-red-500 mt-1">Name is required</p></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile * <span class="text-xs text-gray-500">(10 digits)</span></label><input x-model="form.mobile_number" type="text" class="form-input-custom" inputmode="numeric" pattern="[0-9]{10}" maxlength="10" @input="form.mobile_number = RepairBox.normalizeCustomerMobile(form.mobile_number)" @keydown="if(!/[0-9]/.test($event.key) && !['Backspace','Delete','Tab','ArrowLeft','ArrowRight'].includes($event.key)) $event.preventDefault()"><p x-show="formTried && !form.mobile_number.trim()" class="text-xs text-red-500 mt-1">Mobile number is required</p><p x-show="(formTried || form.mobile_number) && form.mobile_number.trim() && !/^\d{10}$/.test(form.mobile_number.trim())" class="text-xs text-red-500 mt-1">Mobile must be exactly 10 digits</p></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="form.email" type="email" class="form-input-custom"><p x-show="(formTried || form.email) && form.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())" class="text-xs text-red-500 mt-1">Please enter a valid email</p></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea x-model="form.address" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end"><button @click="closeEditModal()" class="btn-secondary w-full sm:w-auto">Cancel</button><button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetail" class="modal-overlay" x-cloak>
        <div class="modal-container admin-modal modal-xl">
            <div class="modal-header"><h3 class="text-lg font-semibold" x-text="detail?.name"></h3><button @click="showDetail = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 text-sm mb-4">
                    <div><span class="text-gray-500">Mobile:</span><br><span class="font-medium" x-text="detail?.mobile_number"></span></div>
                    <div><span class="text-gray-500">Email:</span><br><span class="font-medium" x-text="detail?.email || '-'"></span></div>
                    <div><span class="text-gray-500">Points:</span><br><span class="font-medium" x-text="detail?.loyalty_points || 0"></span></div>
                    <div><span class="text-gray-500">Total Spent:</span><br><span class="font-bold text-primary-600" x-text="'₹' + Number(detail?.total_spent || 0).toFixed(2)"></span></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div><h4 class="font-medium text-gray-700 mb-2">Invoices</h4>
                        <div class="table-scroll">
                            <table class="data-table"><thead><tr><th>Invoice #</th><th>Amount</th><th>Date</th></tr></thead><tbody>
                                <template x-for="inv in detail?.invoices || []" :key="inv.id"><tr><td x-text="inv.invoice_number"></td><td x-text="'₹' + Number(inv.total_amount).toFixed(2)"></td><td x-text="new Date(inv.created_at).toLocaleDateString()"></td></tr></template>
                            </tbody></table>
                        </div>
                    </div>
                    <div><h4 class="font-medium text-gray-700 mb-2">Repairs</h4>
                        <div class="table-scroll">
                            <table class="data-table"><thead><tr><th>Ticket</th><th>Status</th><th>Date</th></tr></thead><tbody>
                                <template x-for="rep in detail?.repairs || []" :key="rep.id"><tr><td x-text="rep.ticket_number"></td><td x-text="rep.status.replace('_',' ')"></td><td x-text="new Date(rep.created_at).toLocaleDateString()"></td></tr></template>
                            </tbody></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function customersPage() {
    return {
        items: [], showModal: false, showDetail: false, editing: null, saving: false, search: '', detail: null, loading: true,
        formTried: false, submitError: '',
        form: { name: '', mobile_number: '', email: '', address: '' },
        init() {
            const p = new URLSearchParams(window.location.search);
            if (p.has('search')) this.search = p.get('search');
            this.load();
        },
        updateUrl() {
            const params = new URLSearchParams();
            if (this.search) params.set('search', this.search);
            const qs = params.toString();
            history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
        },
        async load() {
            this.loading = true;
            const url = this.search ? `/customers?search=${encodeURIComponent(this.search)}` : '/admin/customers';
            const r = await RepairBox.ajax(url); if(r.data) this.items = r.data;
            this.updateUrl();
            this.loading = false;
        },
        edit(c) { this.editing = c.id; this.formTried = false; this.submitError = ''; this.form = { name: c.name, mobile_number: c.mobile_number, email: c.email || '', address: c.address || '' }; this.showModal = true; },
        closeEditModal() { this.formTried = false; this.submitError = ''; this.showModal = false; },
        async save() {
            this.formTried = true;
            this.submitError = '';

            const validation = RepairBox.validateCustomerPayload(this.form);
            this.form = {
                ...this.form,
                ...validation.payload,
                email: validation.payload.email || '',
                address: validation.payload.address || '',
            };

            if (!validation.valid) {
                return;
            }

            this.saving = true;
            const r = await RepairBox.ajax(`/admin/customers/${this.editing}`, 'PUT', validation.payload);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.closeEditModal(); this.load(); return; }
            this.submitError = r.message || 'Unable to update customer. Please check the details and try again.';
        },
        async view(c) { const r = await RepairBox.ajax(`/admin/customers/${c.id}`); if(r.data) { this.detail = r.data; this.showDetail = true; } },
        async remove(c) {
            if (!await RepairBox.confirm('Delete this customer?')) return;
            const r = await RepairBox.ajax(`/admin/customers/${c.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
