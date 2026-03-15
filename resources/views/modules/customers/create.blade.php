@extends('layouts.app')
@section('page-title', 'Add Customer')

@section('content')
<div x-data="createCustomerPage()" class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Add Customer</h2>
            <p class="text-sm text-gray-500 mt-0.5">Create a new customer record</p>
        </div>
        <a href="/customers" class="btn-secondary inline-flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">
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
        <div class="card-footer flex justify-end gap-3">
            <a href="/customers" class="btn-secondary">Cancel</a>
            <button @click="save()" class="btn-primary inline-flex items-center gap-2" :disabled="saving">
                <span x-show="saving" class="spinner"></span>
                Create Customer
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createCustomerPage() {
    return {
        saving: false,
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
        async save() {
            this.saving = true;
            const r = await RepairBox.ajax('/customers', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Customer created', 'success'); window.location.href = '/customers'; }
        }
    };
}
</script>
@endpush
