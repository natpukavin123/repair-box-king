@extends('layouts.app')
@section('page-title', 'Customers')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="customersPage()" x-init="init()" class="page-list">
    <div class="flex items-center justify-between mb-4">
        <input x-model="search" @input.debounce.300ms="load()" type="text" placeholder="Search customers..." class="form-input-custom max-w-md">
        <a href="/customers/create" class="btn-primary ml-3"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Customer</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
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
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak>
        <div class="modal-container modal-lg">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Customer</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="form.name" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label><input x-model="form.mobile_number" type="text" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="form.email" type="email" class="form-input-custom"></div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">GSTIN</label>
                        <input x-model="form.gstin" type="text" class="form-input-custom" placeholder="22AAAAA0000A1Z5" maxlength="15">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Billing State
                            <span class="text-xs text-gray-400 font-normal ml-1">Determines IGST vs CGST+SGST</span>
                        </label>
                        <select x-model="form.billing_state" class="form-select-custom">
                            <option value="">-- Select State --</option>
                            <template x-for="s in indianStates" :key="s.code">
                                <option :value="s.code" :selected="s.code === form.billing_state" x-text="s.code + ' - ' + s.name"></option>
                            </template>
                        </select>
                    </div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea x-model="form.address" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button @click="showModal = false" class="btn-secondary">Cancel</button><button @click="save()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-show="showDetail" class="modal-overlay" x-cloak>
        <div class="modal-container modal-xl">
            <div class="modal-header"><h3 class="text-lg font-semibold" x-text="detail?.name"></h3><button @click="showDetail = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm mb-4">
                    <div><span class="text-gray-500">Mobile:</span><br><span class="font-medium" x-text="detail?.mobile_number"></span></div>
                    <div><span class="text-gray-500">Email:</span><br><span class="font-medium" x-text="detail?.email || '-'"></span></div>
                    <div><span class="text-gray-500">Points:</span><br><span class="font-medium" x-text="detail?.loyalty_points || 0"></span></div>
                    <div><span class="text-gray-500">Total Spent:</span><br><span class="font-bold text-primary-600" x-text="'₹' + Number(detail?.total_spent || 0).toFixed(2)"></span></div>
                    <div><span class="text-gray-500">GSTIN:</span><br><span class="font-mono text-xs font-medium" x-text="detail?.gstin || '-'"></span></div>
                    <div><span class="text-gray-500">Billing State:</span><br><span class="font-medium" x-text="detail?.billing_state || '-'"></span></div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div><h4 class="font-medium text-gray-700 mb-2">Invoices</h4>
                        <table class="data-table"><thead><tr><th>Invoice #</th><th>Amount</th><th>Date</th></tr></thead><tbody>
                            <template x-for="inv in detail?.invoices || []" :key="inv.id"><tr><td x-text="inv.invoice_number"></td><td x-text="'₹' + Number(inv.total_amount).toFixed(2)"></td><td x-text="new Date(inv.created_at).toLocaleDateString()"></td></tr></template>
                        </tbody></table>
                    </div>
                    <div><h4 class="font-medium text-gray-700 mb-2">Repairs</h4>
                        <table class="data-table"><thead><tr><th>Ticket</th><th>Status</th><th>Date</th></tr></thead><tbody>
                            <template x-for="rep in detail?.repairs || []" :key="rep.id"><tr><td x-text="rep.ticket_number"></td><td x-text="rep.status.replace('_',' ')"></td><td x-text="new Date(rep.created_at).toLocaleDateString()"></td></tr></template>
                        </tbody></table>
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
        form: { name: '', mobile_number: '', email: '', address: '', gstin: '', billing_state: '{{ \App\Models\Setting::getValue('shop_state', '') }}' },
        indianStates: [
            { code: '01', name: 'Jammu & Kashmir' }, { code: '02', name: 'Himachal Pradesh' },
            { code: '03', name: 'Punjab' }, { code: '04', name: 'Chandigarh' },
            { code: '05', name: 'Uttarakhand' }, { code: '06', name: 'Haryana' },
            { code: '07', name: 'Delhi' }, { code: '08', name: 'Rajasthan' },
            { code: '09', name: 'Uttar Pradesh' }, { code: '10', name: 'Bihar' },
            { code: '11', name: 'Sikkim' }, { code: '12', name: 'Arunachal Pradesh' },
            { code: '13', name: 'Nagaland' }, { code: '14', name: 'Manipur' },
            { code: '15', name: 'Mizoram' }, { code: '16', name: 'Tripura' },
            { code: '17', name: 'Meghalaya' }, { code: '18', name: 'Assam' },
            { code: '19', name: 'West Bengal' }, { code: '20', name: 'Jharkhand' },
            { code: '21', name: 'Odisha' }, { code: '22', name: 'Chhattisgarh' },
            { code: '23', name: 'Madhya Pradesh' }, { code: '24', name: 'Gujarat' },
            { code: '26', name: 'Dadra & Nagar Haveli and Daman & Diu' },
            { code: '27', name: 'Maharashtra' }, { code: '28', name: 'Andhra Pradesh (Old)' },
            { code: '29', name: 'Karnataka' }, { code: '30', name: 'Goa' },
            { code: '31', name: 'Lakshadweep' }, { code: '32', name: 'Kerala' },
            { code: '33', name: 'Tamil Nadu' }, { code: '34', name: 'Puducherry' },
            { code: '35', name: 'Andaman & Nicobar Islands' },
            { code: '36', name: 'Telangana' }, { code: '37', name: 'Andhra Pradesh (New)' },
            { code: '38', name: 'Ladakh' },
        ],
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
            const url = this.search ? `/customers?search=${encodeURIComponent(this.search)}` : '/customers';
            const r = await RepairBox.ajax(url); if(r.data) this.items = r.data;
            this.updateUrl();
            this.loading = false;
        },
        edit(c) { this.editing = c.id; this.form = { name: c.name, mobile_number: c.mobile_number, email: c.email || '', address: c.address || '', gstin: c.gstin || '', billing_state: c.billing_state || '{{ \App\Models\Setting::getValue('shop_state', '') }}' }; this.showModal = true; },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/customers/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async view(c) { const r = await RepairBox.ajax(`/customers/${c.id}`); if(r.data) { this.detail = r.data; this.showDetail = true; } },
        async remove(c) {
            if (!await RepairBox.confirm('Delete this customer?')) return;
            const r = await RepairBox.ajax(`/customers/${c.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        }
    };
}
</script>
@endpush
