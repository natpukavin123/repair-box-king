@extends('layouts.app')
@section('page-title', 'Add Expense')

@section('content')
<div x-data="createExpensePage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Expense</h2>
            <p class="text-sm text-gray-500 mt-0.5">Record a new expense</p>
        </div>
        <a href="/expenses" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select x-model="form.expense_category_id" class="form-select-custom">
                        <option value="">Select</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input x-model="form.amount" type="number" step="0.01" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input x-model="form.expense_date" type="date" class="form-input-custom">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select x-model="form.payment_method" class="form-select-custom">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea x-model="form.description" class="form-input-custom" rows="2"></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer flex justify-end gap-3">
            <a href="/expenses" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Expense
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createExpensePage() {
    return {
        saving: false,
        form: { expense_category_id: '', amount: '', expense_date: new Date().toISOString().split('T')[0], payment_method: 'cash', description: '' },
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/expenses', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Expense recorded', 'success'); window.location.href = '/expenses'; }
        }
    };
}
</script>
@endpush
