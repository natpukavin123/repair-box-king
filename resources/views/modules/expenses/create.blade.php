@extends('layouts.app')
@section('page-title', 'Add Expense')

@section('content')
<div x-data="createExpensePage()" class="max-w-2xl mx-auto">
    <div class="flex flex-col gap-3 mb-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Expense</h2>
            <p class="text-sm text-gray-500 mt-0.5">Record a new expense</p>
        </div>
        <a href="/expenses" class="btn-secondary inline-flex w-full items-center justify-center gap-1.5 sm:w-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body space-y-5">
            <x-ui.form-section title="Expense Details" description="Keep the amount, category, and payment method aligned with the shared form structure.">
                <x-ui.select-field label="Category" x-model="form.category_id" required>
                    <option value="">Select</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </x-ui.select-field>
                <x-ui.input-field label="Amount" x-model="form.amount" type="number" step="0.01" required />
                <x-ui.input-field label="Date" x-model="form.expense_date" type="date" required />
                <x-ui.select-field label="Payment Method" x-model="form.payment_method">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </x-ui.select-field>
            </x-ui.form-section>

            <x-ui.form-section title="Description" description="Optional note for staff or reporting." gridClass="grid grid-cols-1 gap-4">
                <x-ui.textarea-field label="Description" x-model="form.description" rows="3" />
            </x-ui.form-section>
        </div>
        <div class="card-footer flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="/expenses" class="btn-secondary w-full text-center sm:w-auto">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex w-full items-center justify-center gap-2 sm:w-auto" :disabled="saving">
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
        form: { category_id: '', amount: '', expense_date: new Date().toISOString().split('T')[0], payment_method: 'cash', description: '' },
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
