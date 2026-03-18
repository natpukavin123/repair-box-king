@extends('layouts.app')
@section('page-title', 'Expenses')
@section('content-class', 'workspace-content')

@section('content')
<div x-data="expensesPage()" x-init="load()" class="workspace-screen">
    <x-ui.action-bar title="Expense Tracker" description="Filters, category tools, and the ledger stay in one working view.">
        <div class="workspace-toolbar-actions">
            <button @click="showCat = true; loadCategories()" class="btn-secondary w-full sm:w-auto">Categories</button>
            <a href="/expenses/create" class="btn-primary inline-flex w-full items-center justify-center sm:w-auto"><svg class="w-4 h-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg> Add Expense</a>
        </div>
    </x-ui.action-bar>

    <x-ui.filter-bar>
        <div class="workspace-filter-group">
            <select x-model="filter.category_id" @change="load()" class="form-select-custom text-sm min-w-0 sm:min-w-[140px]">
                <option value="">All Categories</option>
                <template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
            </select>
            <input x-model="filter.date_from" @change="load()" type="date" class="form-input-custom text-sm w-full sm:w-[140px]" placeholder="From">
            <input x-model="filter.date_to" @change="load()" type="date" class="form-input-custom text-sm w-full sm:w-[140px]" placeholder="To">
        </div>
        <div class="workspace-filter-meta">Showing <span x-text="items.length"></span> expenses</div>
    </x-ui.filter-bar>

    <x-ui.table-card>
        <x-slot:header>
            <div>
                <h3 class="text-base font-semibold text-slate-900">Expense Ledger</h3>
                <p class="text-sm text-slate-500">Date filters and category review stay attached to the table below.</p>
            </div>
        </x-slot:header>

        <table class="data-table">
                    <thead class="sticky top-0 z-10 bg-gray-50"><tr><th>#</th><th>Category</th><th>Description</th><th>Amount</th><th>Payment</th><th>Date</th><th>Actions</th></tr></thead>
                    <tbody>
                        <template x-for="(e, i) in items" :key="e.id">
                            <tr>
                                <td x-text="i+1"></td>
                                <td x-text="e.category ? e.category.name : '-'"></td>
                                <td class="max-w-xs truncate" x-text="e.description || '-'"></td>
                                <td class="font-semibold text-red-600" x-text="'₹' + Number(e.amount).toFixed(2)"></td>
                                <td>
                                    <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-1 rounded-full" :class="{'bg-green-100 text-green-700': e.payment_method === 'cash', 'bg-blue-100 text-blue-700': e.payment_method === 'upi', 'bg-purple-100 text-purple-700': e.payment_method === 'card', 'bg-gray-100 text-gray-700': e.payment_method === 'bank_transfer'}">
                                        <template x-if="e.payment_method === 'cash'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                        <template x-if="e.payment_method === 'upi'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                        <template x-if="e.payment_method === 'card'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                        <template x-if="e.payment_method === 'bank_transfer'"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                        <span x-text="e.payment_method === 'bank_transfer' ? 'Bank' : (e.payment_method || 'cash').toUpperCase()"></span>
                                    </span>
                                </td>
                                <td x-text="fmtDate(e.expense_date)"></td>
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
    </x-ui.table-card>

    <!-- Edit Expense Modal -->
    <div x-show="showModal" class="modal-overlay" x-cloak @click.self="showModal = false">
        <div class="modal-container">
            <div class="modal-header"><h3 class="text-lg font-semibold">Edit Expense</h3><button @click="showModal = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select x-model="form.category_id" class="form-select-custom"><option value="">Select</option><template x-for="c in categories" :key="c.id"><option :value="c.id" x-text="c.name"></option></template></select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label><input x-model="form.amount" type="number" step="0.01" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label><input x-model="form.expense_date" type="date" class="form-input-custom"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select x-model="form.payment_method" class="form-select-custom"><option value="cash">Cash</option><option value="card">Card</option><option value="upi">UPI</option><option value="bank_transfer">Bank Transfer</option></select>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea x-model="form.description" class="form-input-custom" rows="2"></textarea></div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end"><button @click="showModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button><button @click="save()" class="btn-primary w-full sm:w-auto" :disabled="saving"><span x-show="saving" class="spinner mr-1"></span>Update</button></div>
        </div>
    </div>

    <!-- Categories Modal -->
    <div x-show="showCat" class="modal-overlay" x-cloak @click.self="showCat = false">
        <div class="modal-container" style="max-width:500px">
            <div class="modal-header"><h3 class="text-lg font-semibold">Expense Categories</h3><button @click="showCat = false" class="text-gray-400 hover:text-gray-600">&times;</button></div>
            <div class="modal-body">
                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    <template x-for="c in catList" :key="c.id">
                        <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded group">
                            <template x-if="editingCat !== c.id">
                                <div class="flex flex-col gap-2 w-full sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <span class="font-medium" x-text="c.name"></span>
                                        <span class="text-xs text-gray-400 ml-2" x-text="(c.expenses_count || 0) + ' expenses'"></span>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button @click="editingCat = c.id; editCatName = c.name" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="deleteCat(c)" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="editingCat === c.id">
                                <div class="flex flex-col gap-2 w-full sm:flex-row sm:items-center">
                                    <input x-model="editCatName" type="text" class="form-input-custom flex-1 text-sm" @keydown.enter="updateCat(c)" @keydown.escape="editingCat = null">
                                    <button @click="updateCat(c)" class="btn-primary text-xs px-2 py-1 w-full sm:w-auto">Save</button>
                                    <button @click="editingCat = null" class="btn-secondary text-xs px-2 py-1 w-full sm:w-auto">Cancel</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="catList.length === 0" class="text-center text-gray-400 py-4">No categories yet</div>
                </div>
                <div class="flex flex-col gap-2 pt-2 border-t sm:flex-row">
                    <input x-model="catForm.name" type="text" class="form-input-custom flex-1" placeholder="New category name" @keydown.enter="saveCat()">
                    <button @click="saveCat()" class="btn-primary w-full sm:w-auto">Add</button>
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
        items: [], categories: [], catList: [], showModal: false, showCat: false,
        editing: null, saving: false, loading: true,
        editingCat: null, editCatName: '',
        form: { category_id: '', amount: '', expense_date: '', payment_method: 'cash', description: '' },
        catForm: { name: '' },
        filter: { category_id: '', date_from: '', date_to: '' },

        fmtDate(d) {
            if (!d) return '-';
            const dt = new Date(d);
            if (isNaN(dt)) return d;
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        async load() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.filter.category_id) params.set('category_id', this.filter.category_id);
            if (this.filter.date_from) params.set('date_from', this.filter.date_from);
            if (this.filter.date_to) params.set('date_to', this.filter.date_to);
            const qs = params.toString() ? '?' + params.toString() : '';
            const [r, c] = await Promise.all([
                RepairBox.ajax('/expenses' + qs),
                RepairBox.ajax('/expenses/categories')
            ]);
            if (r.data) this.items = r.data;
            if (Array.isArray(c)) this.categories = c;
            else if (c.data) this.categories = c.data;
            this.loading = false;
        },

        async loadCategories() {
            const c = await RepairBox.ajax('/expenses/categories');
            if (Array.isArray(c)) this.catList = c;
            else if (c.data) this.catList = c.data;
        },

        edit(e) {
            this.editing = e.id;
            this.form = {
                category_id: e.category_id,
                amount: e.amount,
                expense_date: e.expense_date ? e.expense_date.substring(0, 10) : '',
                payment_method: e.payment_method || 'cash',
                description: e.description || ''
            };
            this.showModal = true;
        },

        async save() {
            this.saving = true;
            const r = await RepairBox.ajax(`/expenses/${this.editing}`, 'PUT', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },

        async remove(e) {
            if (!await RepairBox.confirm('Delete this expense?')) return;
            const r = await RepairBox.ajax(`/expenses/${e.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        },

        async saveCat() {
            if (!this.catForm.name.trim()) return;
            const r = await RepairBox.ajax('/expenses/categories', 'POST', this.catForm);
            if (r.success !== false) {
                RepairBox.toast('Category added', 'success');
                this.catForm = { name: '' };
                this.loadCategories();
                this.load();
            }
        },

        async updateCat(c) {
            if (!this.editCatName.trim()) return;
            const r = await RepairBox.ajax(`/expenses/categories/${c.id}`, 'PUT', { name: this.editCatName });
            if (r.success !== false) {
                RepairBox.toast('Category updated', 'success');
                this.editingCat = null;
                this.loadCategories();
                this.load();
            }
        },

        async deleteCat(c) {
            if (!await RepairBox.confirm(`Delete category "${c.name}"?`)) return;
            const r = await RepairBox.ajax(`/expenses/categories/${c.id}`, 'DELETE');
            if (r.success !== false) {
                RepairBox.toast('Category deleted', 'success');
                this.loadCategories();
                this.load();
            } else {
                RepairBox.toast(r.message || 'Cannot delete category', 'error');
            }
        }
    };
}
</script>
@endpush
