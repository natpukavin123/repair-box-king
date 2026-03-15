@extends('layouts.app')
@section('page-title', 'GST & Tax Management')

@section('content')
<div x-data="taxPage()" x-init="init()">
    {{-- Tabs --}}
    <div class="flex flex-wrap gap-2 mb-6">
        <button @click="tab='rates'" :class="tab==='rates' ? 'btn-primary' : 'btn-secondary'" class="text-sm">GST Rates</button>
        <button @click="tab='hsn'" :class="tab==='hsn' ? 'btn-primary' : 'btn-secondary'" class="text-sm">HSN Codes (Goods)</button>
        <button @click="tab='sac'" :class="tab==='sac' ? 'btn-primary' : 'btn-secondary'" class="text-sm">SAC Codes (Services)</button>
        <button @click="tab='settings'" :class="tab==='settings' ? 'btn-primary' : 'btn-secondary'" class="text-sm">GST Settings</button>
    </div>

    {{-- ══ GST Rates ══ --}}
    <div x-show="tab==='rates'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">GST Tax Rates</h3>
            <button @click="rateForm={name:'',percentage:'',is_default:false}; rateEditing=null; showRateModal=true" class="btn-primary text-sm">Add Rate</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>GST %</th>
                        <th>CGST %</th>
                        <th>SGST %</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="rate in taxRates" :key="rate.id">
                        <tr>
                            <td class="font-medium" x-text="rate.name"></td>
                            <td x-text="parseFloat(rate.percentage).toFixed(2) + '%'"></td>
                            <td x-text="(parseFloat(rate.percentage)/2).toFixed(2) + '%'"></td>
                            <td x-text="(parseFloat(rate.percentage)/2).toFixed(2) + '%'"></td>
                            <td>
                                <span x-show="rate.is_default" class="badge badge-success">Default</span>
                                <span x-show="!rate.is_default" class="text-gray-400">-</span>
                            </td>
                            <td>
                                <span class="badge" :class="rate.is_active ? 'badge-success' : 'badge-danger'" x-text="rate.is_active ? 'Active' : 'Inactive'"></span>
                            </td>
                            <td class="flex gap-2">
                                <button @click="editRate(rate)" class="text-primary-600 hover:text-primary-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="deleteRate(rate.id)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="taxRates.length===0"><td colspan="7" class="text-center text-gray-400 py-6">No tax rates configured</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-body border-t bg-blue-50">
            <p class="text-xs text-blue-700">
                <strong>Note:</strong> For intra-state supply, GST is split equally into CGST + SGST. For inter-state supply, full IGST applies.
                The default rate is used when no specific rate is assigned to a product/service.
            </p>
        </div>
    </div>

    {{-- ══ HSN Codes (Goods) ══ --}}
    <div x-show="tab==='hsn'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">HSN Codes (Goods)</h3>
            <button @click="hsnForm={code:'',type:'hsn',description:'',tax_rate_id:''}; hsnEditing=null; showHsnModal=true" class="btn-primary text-sm">Add HSN Code</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>HSN Code</th>
                        <th>Description</th>
                        <th>GST Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="hsn in hsnCodes.filter(h => h.type === 'hsn')" :key="hsn.id">
                        <tr>
                            <td class="font-mono font-medium" x-text="hsn.code"></td>
                            <td x-text="hsn.description"></td>
                            <td><span class="badge badge-info" x-text="hsn.tax_rate ? hsn.tax_rate.name : '-'"></span></td>
                            <td><span class="badge" :class="hsn.is_active ? 'badge-success' : 'badge-danger'" x-text="hsn.is_active ? 'Active' : 'Inactive'"></span></td>
                            <td class="flex gap-2">
                                <button @click="editHsn(hsn)" class="text-primary-600 hover:text-primary-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="deleteHsn(hsn.id)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="hsnCodes.filter(h => h.type === 'hsn').length===0"><td colspan="5" class="text-center text-gray-400 py-6">No HSN codes added</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-body border-t bg-amber-50">
            <p class="text-xs text-amber-700">
                <strong>HSN</strong> (Harmonized System of Nomenclature) codes are used for classifying goods under GST.
                Assign these to your products and parts for proper tax filing.
            </p>
        </div>
    </div>

    {{-- ══ SAC Codes (Services) ══ --}}
    <div x-show="tab==='sac'" class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-lg font-semibold">SAC Codes (Services)</h3>
            <button @click="hsnForm={code:'',type:'sac',description:'',tax_rate_id:''}; hsnEditing=null; showHsnModal=true" class="btn-primary text-sm">Add SAC Code</button>
        </div>
        <div class="card-body p-0">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>SAC Code</th>
                        <th>Description</th>
                        <th>GST Rate</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="sac in hsnCodes.filter(h => h.type === 'sac')" :key="sac.id">
                        <tr>
                            <td class="font-mono font-medium" x-text="sac.code"></td>
                            <td x-text="sac.description"></td>
                            <td><span class="badge badge-info" x-text="sac.tax_rate ? sac.tax_rate.name : '-'"></span></td>
                            <td><span class="badge" :class="sac.is_active ? 'badge-success' : 'badge-danger'" x-text="sac.is_active ? 'Active' : 'Inactive'"></span></td>
                            <td class="flex gap-2">
                                <button @click="editHsn(sac)" class="text-primary-600 hover:text-primary-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button @click="deleteHsn(sac.id)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="hsnCodes.filter(h => h.type === 'sac').length===0"><td colspan="5" class="text-center text-gray-400 py-6">No SAC codes added</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-body border-t bg-green-50">
            <p class="text-xs text-green-700">
                <strong>SAC</strong> (Services Accounting Code) codes are used for classifying services under GST.
                Assign these to your service types for proper tax filing.
            </p>
        </div>
    </div>

    {{-- ══ GST Settings ══ --}}
    <div x-show="tab==='settings'" class="card">
        <div class="card-header"><h3 class="text-lg font-semibold">GST Configuration</h3></div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shop GSTIN</label>
                    <input x-model="gstSettings.shop_gstin" type="text" class="form-input-custom" placeholder="22AAAAA0000A1Z5" maxlength="15">
                    <p class="text-xs text-gray-500 mt-1">15-digit GST Identification Number</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Shop State</label>
                    <select x-model="gstSettings.shop_state" class="form-input-custom">
                        <option value="">Select State</option>
                        <template x-for="state in indianStates" :key="state.code">
                            <option :value="state.code" x-text="state.code + ' - ' + state.name"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Used to determine CGST/SGST vs IGST based on customer's state</p>
                </div>
            </div>
            <div class="mt-4 p-3 bg-blue-50 rounded text-xs text-blue-800">
                <strong>How GST works:</strong><br>
                <span class="font-semibold">Intra-State</span> (Customer in same state as shop) → CGST + SGST (each = GST% / 2)<br>
                <span class="font-semibold">Inter-State</span> (Customer in different state) → IGST (= full GST%)
            </div>
            <div class="mt-6">
                <button @click="saveGstSettings()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Save GST Settings
                </button>
            </div>
        </div>
    </div>

    {{-- ══ Rate Modal ══ --}}
    <div x-show="showRateModal" class="modal-overlay" x-cloak>
        <div class="modal-content max-w-md" @click.away="showRateModal=false">
            <div class="modal-header">
                <h3 x-text="rateEditing ? 'Edit Tax Rate' : 'Add Tax Rate'" class="text-lg font-semibold"></h3>
                <button @click="showRateModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input x-model="rateForm.name" type="text" class="form-input-custom" placeholder="GST 18%">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Percentage (%)</label>
                    <input x-model="rateForm.percentage" type="number" step="0.01" min="0" max="100" class="form-input-custom" placeholder="18">
                </div>
                <div class="flex items-center gap-2">
                    <input x-model="rateForm.is_default" type="checkbox" id="is_default" class="rounded">
                    <label for="is_default" class="text-sm text-gray-700">Set as default rate</label>
                </div>
                <template x-if="rateEditing">
                    <div class="flex items-center gap-2">
                        <input x-model="rateForm.is_active" type="checkbox" id="is_active" class="rounded">
                        <label for="is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="showRateModal=false" class="btn-secondary">Cancel</button>
                <button @click="saveRate()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Save
                </button>
            </div>
        </div>
    </div>

    {{-- ══ HSN/SAC Modal ══ --}}
    <div x-show="showHsnModal" class="modal-overlay" x-cloak>
        <div class="modal-content max-w-md" @click.away="showHsnModal=false">
            <div class="modal-header">
                <h3 x-text="hsnEditing ? ('Edit ' + (hsnForm.type==='hsn' ? 'HSN' : 'SAC') + ' Code') : ('Add ' + (hsnForm.type==='hsn' ? 'HSN' : 'SAC') + ' Code')" class="text-lg font-semibold"></h3>
                <button @click="showHsnModal=false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" x-text="hsnForm.type==='hsn' ? 'HSN Code' : 'SAC Code'"></label>
                    <input x-model="hsnForm.code" type="text" class="form-input-custom" :placeholder="hsnForm.type==='hsn' ? '8517' : '998314'" maxlength="10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input x-model="hsnForm.description" type="text" class="form-input-custom" placeholder="Description of goods/service">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">GST Rate</label>
                    <select x-model="hsnForm.tax_rate_id" class="form-input-custom">
                        <option value="">Select GST Rate</option>
                        <template x-for="rate in taxRates.filter(r => r.is_active)" :key="rate.id">
                            <option :value="rate.id" x-text="rate.name + ' (' + parseFloat(rate.percentage).toFixed(0) + '%)'"></option>
                        </template>
                    </select>
                </div>
                <template x-if="hsnEditing">
                    <div class="flex items-center gap-2">
                        <input x-model="hsnForm.is_active" type="checkbox" id="hsn_is_active" class="rounded">
                        <label for="hsn_is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="showHsnModal=false" class="btn-secondary">Cancel</button>
                <button @click="saveHsn()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span> Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function taxPage() {
    return {
        tab: 'rates',
        taxRates: [],
        hsnCodes: [],
        saving: false,

        // Rate modal
        showRateModal: false,
        rateEditing: null,
        rateForm: { name: '', percentage: '', is_default: false },

        // HSN/SAC modal
        showHsnModal: false,
        hsnEditing: null,
        hsnForm: { code: '', type: 'hsn', description: '', tax_rate_id: '' },

        // GST Settings
        gstSettings: { shop_gstin: '', shop_state: '' },

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

        async init() {
            await this.loadData();
        },

        async loadData() {
            try {
                const res = await fetch('/tax', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                this.taxRates = data.taxRates || [];
                this.hsnCodes = data.hsnCodes || [];
                this.gstSettings.shop_state = data.shopState || '';
                // Load GSTIN from general settings
                const settingsRes = await fetch('/settings', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const settings = await settingsRes.json();
                this.gstSettings.shop_gstin = settings.shop_gstin || '';
            } catch (e) {
                console.error('Failed to load tax data', e);
            }
        },

        // ─── Rate methods ───
        editRate(rate) {
            this.rateEditing = rate.id;
            this.rateForm = {
                name: rate.name,
                percentage: rate.percentage,
                is_default: rate.is_default,
                is_active: rate.is_active
            };
            this.showRateModal = true;
        },

        async saveRate() {
            this.saving = true;
            try {
                const url = this.rateEditing ? `/tax/rates/${this.rateEditing}` : '/tax/rates';
                const method = this.rateEditing ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.rateForm)
                });
                if (!res.ok) throw await res.json();
                this.showRateModal = false;
                await this.loadData();
                if (window.RepairBox) RepairBox.toast(this.rateEditing ? 'Rate updated' : 'Rate added', 'success');
            } catch (e) {
                const msg = e.message || Object.values(e.errors || {}).flat().join(', ') || 'Failed to save rate';
                if (window.RepairBox) RepairBox.toast(msg, 'error');
            }
            this.saving = false;
        },

        async deleteRate(id) {
            if (!confirm('Delete this tax rate? Items using it will lose their rate assignment.')) return;
            try {
                const res = await fetch(`/tax/rates/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (!res.ok) throw await res.json();
                await this.loadData();
                if (window.RepairBox) RepairBox.toast('Rate deleted', 'success');
            } catch (e) {
                if (window.RepairBox) RepairBox.toast('Failed to delete rate', 'error');
            }
        },

        // ─── HSN/SAC methods ───
        editHsn(hsn) {
            this.hsnEditing = hsn.id;
            this.hsnForm = {
                code: hsn.code,
                type: hsn.type,
                description: hsn.description,
                tax_rate_id: hsn.tax_rate_id,
                is_active: hsn.is_active
            };
            this.showHsnModal = true;
        },

        async saveHsn() {
            this.saving = true;
            try {
                const url = this.hsnEditing ? `/tax/hsn/${this.hsnEditing}` : '/tax/hsn';
                const method = this.hsnEditing ? 'PUT' : 'POST';
                const res = await fetch(url, {
                    method,
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.hsnForm)
                });
                if (!res.ok) throw await res.json();
                this.showHsnModal = false;
                await this.loadData();
                const label = this.hsnForm.type === 'hsn' ? 'HSN' : 'SAC';
                if (window.RepairBox) RepairBox.toast(`${label} code ${this.hsnEditing ? 'updated' : 'added'}`, 'success');
            } catch (e) {
                const msg = e.message || Object.values(e.errors || {}).flat().join(', ') || 'Failed to save';
                if (window.RepairBox) RepairBox.toast(msg, 'error');
            }
            this.saving = false;
        },

        async deleteHsn(id) {
            if (!confirm('Delete this code?')) return;
            try {
                const res = await fetch(`/tax/hsn/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                if (!res.ok) throw await res.json();
                await this.loadData();
                if (window.RepairBox) RepairBox.toast('Code deleted', 'success');
            } catch (e) {
                if (window.RepairBox) RepairBox.toast('Failed to delete', 'error');
            }
        },

        // ─── GST Settings ───
        async saveGstSettings() {
            this.saving = true;
            try {
                const res = await fetch('/tax/settings', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: JSON.stringify(this.gstSettings)
                });
                if (!res.ok) throw await res.json();
                if (window.RepairBox) RepairBox.toast('GST settings saved', 'success');
            } catch (e) {
                if (window.RepairBox) RepairBox.toast('Failed to save settings', 'error');
            }
            this.saving = false;
        }
    };
}
</script>
@endpush
