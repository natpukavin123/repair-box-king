@extends('layouts.app')
@section('page-title', 'Expenses')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="expensesPage()" x-init="load()" class="page-list">
    <div class="flex items-center justify-end gap-2 mb-4">
        <button @click="showCat = true; catForm = {name: ''}" class="btn-secondary">Categories</button>
        <a href="/expenses/create" class="btn-primary"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Expense</a>
    </div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Category</th><th>Description</th><th>Amount</th><th>Payment</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(e, i) in items" :key="e.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td x-text="e.expense_category ? e.expense_category.name : '-'"></td>
                                <td class="max-w-xs truncate" x-text="e.description || '-'"></td>
                                <td class="font-semibold text-red-600" x-text="'₹' + Number(e.amount).toFixed(2)"></td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full" :class="{'bg-green-100 text-green-700': e.payment_method === 'cash', 'bg-blue-100 text-blue-700': e.payment_method === 'upi', 'bg-purple-100 text-purple-700': e.payment_method === 'card', 'bg-gray-100 text-gray-700': e.payment_method === 'bank_transfer'}">
                                        <template x-if="e.payment_method === 'cash'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                        <template x-if="e.payment_method === 'upi'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                        <template x-if="e.payment_method === 'card'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                        <template x-if="e.payment_method === 'bank_transfer'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                        <span x-text="e.payment_method === 'bank_transfer' ? 'Bank' : (e.payment_method || '-').toUpperCase()"></span>
                                    </span>
                                </td>
                                <td x-text="e.expense_date"></td>
                                <td class="whitespace-nowrap">
                                    <button @click="edit(e)" class="text-primary-600 hover:text-primary-800 mr-1"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></button>
                                    <button @click="remove(e)" class="text-red-600 hover:text-red-800"><svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0 && !loading"><td colspan="7" class="text-center text-gray-400 py-8">No expenses found</td></tr>
                        <template x-if="loading">
                            <template x-for="i in 10" :key="'sk'+i">
                                <tr>
                                    <td><div class="skeleton h-3 w-8"></div></td>
                                    <td><div class="skeleton h-3 w-28"></div></td>
                                    <td><div class="skeleton h-3 w-40"></div></td>
                                    <td><div class="skeleton h-3 w-20"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-24"></div></td>
                                    <td><div class="skeleton h-3 w-16"></div></td>
                                </tr>
                            </template>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak @click.self="showModal = false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Expense</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select x-model="form.expense_category_id" class="form-select-custom"><option value="">Select</option><template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template></select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label><input x-model="form.amount" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label><input x-model="form.expense_date" type="date" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select x-model="form.payment_method" class="form-select-custom"><option value="cash">Cash</option><option value="card">Card</option><option value="upi">UPI</option><option value="bank_transfer">Bank Transfer</option></select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer"><button @click="showModal = false" class="btn-secondary">Cancel</button><button @click="save()" class="btn-primary" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>

    <!-- Categories Modal -->
    <div x-show="showCat" class="modal-overlay" x-cloak @click.self="showCat = false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Expense Categories</h3><button @click="showCat = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-2 mb-4">
                    <template x-for="c in categories" :key="c.id"><div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded"><span x-text="c.name"></span></div></template>
                </div>
                <div class="flex gap-2">
                    <input x-model="catForm.name" type="text" class="form-input-custom flex-1" placeholder="New category name">
                    <button @click="saveCat()" class="btn-primary">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function expensesPage() {
    return {
        items: [], categories: [], showModal: false, showCat: false, editing: null, saving: false, loading: true,
        form: { expense_category_id: '', amount: '', expense_date: '', payment_method: 'cash', description: '' },
        catForm: { name: '' },
        async load() {
            this.loading = true;
            const [r, c] = await Promise.all([RepairBox.ajax('/expenses'), RepairBox.ajax('/expenses/categories')]);
            if(r.data) this.items = r.data; if(c.data) this.categories = c.data;
            this.loading = false;
        },
        edit(e) { this.editing = e.id; this.form = { expense_category_id: e.expense_category_id, amount: e.amount, expense_date: e.expense_date, payment_method: e.payment_method || 'cash', description: e.description || '' }; this.showModal = true; },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/expenses/${this.editing}`, 'PUT', this.form);
            this.saving = false; if(r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },
        async remove(e) { if(!await RepairBox.confirm('Delete?')) return; const r = await RepairBox.ajax(`/expenses/${e.id}`, 'DELETE'); if(r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); } },
        async saveCat() {
            const r = await RepairBox.ajax('/expenses/categories', 'POST', this.catForm);
            if(r.success !== false) { RepairBox.toast('Category added', 'success'); this.catForm = {name:''}; this.load(); }
        }
    };
}
</script>
@endpush
