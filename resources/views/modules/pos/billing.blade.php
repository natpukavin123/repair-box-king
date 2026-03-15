@extends('layouts.app')
@section('page-title', 'POS Billing')

@section('content')
<div x-data="posBilling()" x-init="init()" class="h-full">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">
        <!-- Left: Product Search & List -->
        <div class="lg:col-span-2 flex flex-col">
            <!-- Search -->
            <div class="mb-3 flex gap-2">
                <input x-model="searchQuery" @input.debounce.300ms="searchProducts()" type="text" placeholder="Search products by name, SKU..." class="form-input-custom flex-1" autofocus>
                <select x-model="itemType" class="form-select-custom w-40">
                    <option value="product">Product</option>
                    <option value="service">Service</option>
                    <option value="manual">Manual</option>
                </select>
            </div>

            <!-- Product Grid -->
            <div x-show="itemType === 'product'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 overflow-y-auto flex-1 max-h-[60vh]">
                <template x-for="p in searchResults" :key="p.id">
                    <button @click="addProduct(p)" class="card p-3 text-left hover:shadow-lg hover:border-primary-300 border-2 border-transparent transition-all">
                        <p class="font-medium text-sm text-gray-900 truncate" x-text="p.name"></p>
                        <p class="text-xs text-gray-500" x-text="p.sku || ''"></p>
                        <div class="flex justify-between items-center mt-2">
                            <span class="text-primary-600 font-bold text-sm" x-text="'₹' + Number(p.selling_price).toFixed(2)"></span>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="(p.inventory ? p.inventory.current_stock : 0) > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" x-text="'Stock: ' + (p.inventory ? p.inventory.current_stock : 0)"></span>
                        </div>
                        <div x-show="p.tax_rate" class="mt-1">
                            <span class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded font-medium" x-text="'GST ' + parseFloat(p.tax_rate?.percentage || 0).toFixed(0) + '%'"></span>
                        </div>
                    </button>
                </template>
                <div x-show="searchResults.length === 0" class="col-span-full text-center text-gray-400 py-12">
                    <p>No products found. Try searching or add manually.</p>
                </div>
            </div>

            <!-- Service Grid -->
            <div x-show="itemType === 'service'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 overflow-y-auto flex-1 max-h-[60vh]">
                <template x-for="s in filteredServices" :key="s.id">
                    <button @click="addService(s)" class="card p-3 text-left hover:shadow-lg hover:border-primary-300 border-2 border-transparent transition-all">
                        <p class="font-medium text-sm text-gray-900 truncate" x-text="s.name"></p>
                        <p class="text-xs text-gray-500 truncate" x-text="s.description || ''"></p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-primary-600 font-bold text-sm" x-text="'₹' + Number(s.default_price || 0).toFixed(2)"></span>
                            <span x-show="s.tax_rate" class="text-xs bg-orange-100 text-orange-600 px-1.5 py-0.5 rounded font-medium" x-text="'GST ' + parseFloat(s.tax_rate?.percentage || 0).toFixed(0) + '%'"></span>
                        </div>
                    </button>
                </template>
                <div x-show="filteredServices.length === 0" class="col-span-full text-center text-gray-400 py-12">
                    <p>No services found.</p>
                </div>
            </div>

            <!-- Manual Item Entry -->
            <div x-show="itemType === 'manual'" class="card p-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="md:col-span-2"><label class="text-sm font-medium text-gray-700">Item Name</label><input x-model="manualItem.item_name" type="text" class="form-input-custom mt-1"></div>
                    <div><label class="text-sm font-medium text-gray-700">Price</label><input x-model="manualItem.price" type="number" step="0.01" class="form-input-custom mt-1"></div>
                    <div class="flex items-end"><button @click="addManualItem()" class="btn-primary w-full">Add</button></div>
                </div>
            </div>
        </div>

        <!-- Right: Cart & Payment -->
        <div class="flex flex-col">
            <!-- Customer -->
            <div class="card mb-3">
                <div class="card-body py-3">
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-600">Customer *</label>
                            <input x-model="customerSearch" @input.debounce.300ms="findCustomers()" type="text" class="form-input-custom mt-1 text-sm" placeholder="Search by name/phone">
                        </div>
                        <button type="button" @click="showAddCustomer = true; newCustomer = {name:'', mobile_number:'', email:'', address:''}" class="btn-primary text-sm px-3 py-2 whitespace-nowrap">+ New</button>
                    </div>
                    <div x-show="customerResults.length > 0" class="border rounded mt-1 max-h-32 overflow-y-auto bg-white shadow-sm">
                        <template x-for="c in customerResults" :key="c.id">
                            <button @click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b" x-text="c.name + ' - ' + c.mobile_number"></button>
                        </template>
                    </div>
                    <div x-show="customerSearch.length >= 2 && customerResults.length === 0" class="text-xs text-gray-400 mt-1">No customers found. Click <strong>+ New</strong> to add one.</div>
                    <div x-show="selectedCustomer" class="mt-2 flex items-center gap-2">
                        <span class="badge badge-primary" x-text="selectedCustomer?.name"></span>
                        <button @click="selectedCustomer = null; form.customer_id = null" class="text-red-400 hover:text-red-600 text-xs">&times; Remove</button>
                    </div>
                </div>
            </div>

            <!-- Add Customer Modal -->
            <div x-show="showAddCustomer" class="modal-overlay" x-cloak>
                <div class="modal-container max-w-md">
                    <div class="modal-header">
                        <h3 class="text-lg font-semibold">Add Customer</h3>
                        <button @click="showAddCustomer = false" class="text-gray-400 hover:text-gray-600">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="space-y-3">
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input x-model="newCustomer.name" type="text" class="form-input-custom"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label><input x-model="newCustomer.mobile_number" type="text" class="form-input-custom"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input x-model="newCustomer.email" type="email" class="form-input-custom"></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input x-model="newCustomer.address" type="text" class="form-input-custom"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="showAddCustomer = false" class="btn-secondary">Cancel</button>
                        <button type="button" @click.prevent="saveNewCustomer()" class="btn-primary">Save & Select</button>
                    </div>
                </div>
            </div>

            <!-- Cart -->
            <div class="card flex-1 flex flex-col">
                <div class="card-header py-2"><h3 class="font-semibold text-gray-800 text-sm">Cart (<span x-text="cart.length"></span> items)</h3></div>
                <div class="flex-1 overflow-y-auto max-h-[35vh]">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="px-4 py-2 border-b flex items-center gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.item_name"></p>
                                <p class="text-xs text-gray-500" x-text="'₹' + Number(item.price).toFixed(2) + ' × ' + item.quantity"></p>
                                <p class="text-xs text-orange-500" x-show="item.tax_rate_percent > 0" x-text="'GST ' + item.tax_rate_percent + '% = ₹' + (item.price * item.quantity * item.tax_rate_percent / 100).toFixed(2)"></p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="item.quantity > 1 ? item.quantity-- : null" class="w-6 h-6 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300">−</button>
                                <span class="text-sm w-8 text-center" x-text="item.quantity"></span>
                                <button @click="item.quantity++" class="w-6 h-6 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300">+</button>
                            </div>
                            <span class="text-sm font-semibold w-20 text-right" x-text="'₹' + (item.price * item.quantity).toFixed(2)"></span>
                            <button @click="cart.splice(idx, 1)" class="text-red-400 hover:text-red-600">&times;</button>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center text-gray-400 py-8 text-sm">Cart is empty</div>
                </div>

                <div class="border-t px-4 py-3 space-y-1 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span x-text="'₹' + subtotal().toFixed(2)"></span></div>
                    <div x-show="taxTotal() > 0" class="flex justify-between text-orange-600 font-medium">
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                            GST (est.)
                        </span>
                        <span x-text="'₹' + taxTotal().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Discount</span>
                        <div class="flex items-center gap-1"><span>₹</span><input x-model.number="form.discount" type="number" step="0.01" min="0" class="w-20 text-right text-sm border border-gray-300 rounded px-2 py-1"></div>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-1 border-t"><span>Total</span><span class="text-primary-600" x-text="'₹' + grandTotal().toFixed(2)"></span></div>
                </div>

                <!-- Payment -->
                <div class="border-t px-4 py-3">
                    <template x-for="(pay, pidx) in form.payments" :key="pidx">
                        <div class="flex gap-2 mb-2 items-center">
                            <select x-model="pay.payment_method" class="form-select-custom text-sm flex-1">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="upi">UPI</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                            <input x-model.number="pay.amount" type="number" step="0.01" class="form-input-custom text-sm w-24" placeholder="Amount">
                            <button x-show="form.payments.length > 1" @click="form.payments.splice(pidx, 1)" class="text-red-400 hover:text-red-600">&times;</button>
                        </div>
                    </template>
                    <button @click="form.payments.push({payment_method: 'cash', amount: 0, transaction_reference: ''})" class="text-xs text-primary-600 hover:underline">+ Split Payment</button>
                </div>

                <!-- Submit -->
                <div class="border-t px-4 py-3">
                    <button @click="submitInvoice()" class="btn-primary w-full py-3 text-lg" :disabled="saving || cart.length === 0">
                        <span x-show="saving" class="spinner mr-2"></span>
                        Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posBilling() {
    return {
        searchQuery: '', searchResults: [], allServices: [], cart: [], itemType: 'product', saving: false,
        customerSearch: '', customerResults: [], selectedCustomer: null,
        showAddCustomer: false, newCustomer: {name: '', mobile_number: '', email: '', address: ''},
        manualItem: { item_name: '', price: '' },
        form: { customer_id: null, discount: 0, items: [], payments: [{payment_method: 'cash', amount: 0, transaction_reference: ''}] },

        async init() { await this.searchProducts(); await this.loadServices(); },

        async searchProducts() {
            const r = await RepairBox.ajax('/products-search?q=' + encodeURIComponent(this.searchQuery));
            if(r.data) this.searchResults = r.data;
        },

        async loadServices() {
            const r = await RepairBox.ajax('/service-types');
            if(Array.isArray(r.data)) this.allServices = r.data.filter(s => s.status === 'active');
        },

        get filteredServices() {
            if (!this.searchQuery) return this.allServices;
            const q = this.searchQuery.toLowerCase();
            return this.allServices.filter(s => s.name.toLowerCase().includes(q) || (s.description && s.description.toLowerCase().includes(q)));
        },

        addProduct(p) {
            const existing = this.cart.find(c => c.product_id === p.id && c.item_type === 'product');
            if (existing) { existing.quantity++; return; }
            this.cart.push({ item_type: 'product', product_id: p.id, item_name: p.name, quantity: 1, price: Number(p.selling_price), tax_rate_percent: p.tax_rate ? parseFloat(p.tax_rate.percentage) : 0, hsn_code: p.hsn_code || '' });
        },

        addService(s) {
            const existing = this.cart.find(c => c.service_id === s.id && c.item_type === 'service');
            if (existing) { existing.quantity++; return; }
            this.cart.push({ item_type: 'service', product_id: null, service_id: s.id, item_name: s.name, quantity: 1, price: Number(s.default_price || 0), tax_rate_percent: s.tax_rate ? parseFloat(s.tax_rate.percentage) : 0, sac_code: s.sac_code || '' });
        },

        addManualItem() {
            if (!this.manualItem.item_name || !this.manualItem.price) return;
            this.cart.push({ item_type: 'manual', product_id: null, item_name: this.manualItem.item_name, quantity: 1, price: Number(this.manualItem.price) });
            this.manualItem = { item_name: '', price: '' };
        },

        async findCustomers() {
            if (this.customerSearch.length < 2) { this.customerResults = []; return; }
            const r = await RepairBox.ajax('/customers-search?q=' + encodeURIComponent(this.customerSearch));
            if(r.data) this.customerResults = r.data;
        },

        selectCustomer(c) {
            this.selectedCustomer = c; this.form.customer_id = c.id; this.customerResults = []; this.customerSearch = '';
        },

        async saveNewCustomer() {
            if (!this.newCustomer.name || !this.newCustomer.mobile_number) { RepairBox.toast('Name and mobile are required', 'error'); return; }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCustomer);
            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.showAddCustomer = false;
                RepairBox.toast('Customer added', 'success');
            }
        },

        subtotal() { return this.cart.reduce((s, i) => s + i.price * i.quantity, 0); },
        taxTotal() { return this.cart.reduce((s, i) => s + (i.price * i.quantity * (i.tax_rate_percent || 0) / 100), 0); },
        grandTotal() { return Math.max(0, this.subtotal() + this.taxTotal() - (Number(this.form.discount) || 0)); },

        async submitInvoice() {
            if (this.cart.length === 0) return;
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }
            this.form.items = this.cart;
            this.form.customer_billing_state = this.selectedCustomer?.billing_state || '';
            if (this.form.payments.length === 1) this.form.payments[0].amount = this.grandTotal();
            this.saving = true;
            const r = await RepairBox.ajax('/invoices', 'POST', this.form);
            this.saving = false;
            if (r.success !== false && r.data) {
                RepairBox.toast('Invoice created: ' + r.data.invoice_number, 'success');
                window.open('/invoices/' + r.data.id + '/print', '_blank');
                this.cart = [];
                this.form = { customer_id: null, discount: 0, items: [], payments: [{payment_method: 'cash', amount: 0, transaction_reference: ''}] };
                this.selectedCustomer = null;
            }
        }
    };
}
</script>
@endpush
