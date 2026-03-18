@extends('layouts.app')
@section('page-title', 'POS Billing')

@php
    $canViewCostPrice = $canViewCostPrice ?? false;
@endphp

@section('content')
<div x-data="posBilling()" x-init="init()" class="h-full">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 h-full">

        {{-- LEFT: Product / Service Search --}}
        <div class="lg:col-span-2 flex flex-col">

            {{-- Search bar + type selector --}}
            <div class="mb-2 flex gap-2">
                <input x-model="searchQuery" @input.debounce.250ms="searchProducts()" type="text"
                    placeholder="Search by name, SKU, barcode..." class="form-input-custom flex-1" autofocus>
                <select x-model="itemType" @change="if(itemType==='product') searchProducts()" class="form-select-custom w-40">
                    <option value="product">Product</option>
                    <option value="service">Service</option>
                    <option value="manual">Manual Entry</option>
                </select>
            </div>

            {{-- Filter bar (products only) --}}
            <div x-show="itemType === 'product'" class="mb-2 flex flex-wrap gap-2 items-center">

                {{-- Category --}}
                <select @change="selectCategory($event.target.value ? filterCategories.find(c=>c.id===$event.target.value*1) : null)"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="">All Categories</option>
                    <template x-for="cat in filterCategories" :key="cat.id">
                        <option :value="cat.id" :selected="filterCategory === cat.id" x-text="cat.name"></option>
                    </template>
                </select>

                {{-- Subcategory (shown when a category with subcategories is selected) --}}
                <select x-show="filterSubcategories.length > 0"
                    @change="filterSubcategory = $event.target.value ? $event.target.value*1 : ''; onSubcategoryChange()"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="">All Subcategories</option>
                    <template x-for="sc in filterSubcategories" :key="sc.id">
                        <option :value="sc.id" :selected="filterSubcategory === sc.id" x-text="sc.name"></option>
                    </template>
                </select>

                {{-- Brand (dynamic based on selected category/subcategory) --}}
                <select x-show="filterBrands.length > 0"
                    @change="filterBrand = $event.target.value ? $event.target.value*1 : ''; searchProducts()"
                    class="text-sm border border-gray-200 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-primary-300">
                    <option value="">All Brands</option>
                    <template x-for="b in filterBrands" :key="b.id">
                        <option :value="b.id" :selected="filterBrand === b.id" x-text="b.name"></option>
                    </template>
                </select>

                {{-- Clear button --}}
                <button x-show="filterCategory !== null || filterSubcategory !== '' || filterBrand !== ''"
                    @click="clearFilters()"
                    class="text-xs text-red-500 hover:text-red-700 font-semibold px-2 py-1.5 rounded-lg border border-red-200 hover:bg-red-50 transition-colors">
                    &times; Clear
                </button>
            </div>

            {{-- Product grid --}}
            <div x-show="itemType === 'product'"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2.5 overflow-y-auto flex-1 max-h-[60vh] pb-1 pr-1 content-start auto-rows-max">
                <template x-for="p in searchResults" :key="p.id">
                    <button @click="addProduct(p)"
                        class="group relative bg-white rounded-lg text-left shadow-sm hover:shadow-lg border border-gray-100 hover:border-primary-300 transition-all duration-200 overflow-hidden flex flex-col cursor-pointer">
                        <div class="relative w-full overflow-hidden bg-gray-50" style="height:80px">
                            <img x-show="p.thumbnail" :src="'/storage/' + p.thumbnail"
                                class="absolute inset-0 w-full h-full object-contain p-1 group-hover:scale-110 transition-transform duration-300">
                            <div x-show="!p.thumbnail" class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-gray-50 to-gray-100">
                                <svg class="w-8 h-8 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span x-show="(p.inventory ? p.inventory.current_stock : 0) > 0"
                                class="absolute top-1 left-1 text-[8px] font-bold px-1.5 py-0.5 rounded leading-none bg-emerald-600 text-white"
                                x-text="'Stock: ' + (p.inventory ? p.inventory.current_stock : 0)"></span>
                            <span x-show="(p.inventory ? p.inventory.current_stock : 0) <= 0"
                                class="absolute top-1 left-1 text-[8px] font-bold px-1.5 py-0.5 rounded leading-none bg-red-600 text-white">Out</span>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-200 flex items-center justify-center">
                                <div class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-200">
                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="px-2 py-1.5 flex flex-col gap-0.5 border-t border-gray-50 w-full">
                            <p class="font-semibold text-[11px] text-gray-800 truncate leading-tight" x-text="p.name"></p>
                            <p class="text-[10px] text-gray-400 truncate leading-none" x-text="p.sku || 'No SKU'"></p>
                            <div class="flex items-center gap-1 mt-0.5 flex-wrap">
                                <span x-show="Number(p.mrp) > Number(p.selling_price)"
                                    class="text-gray-400 line-through text-[10px] leading-none"
                                    x-text="'MRP ₹' + Number(p.mrp).toLocaleString('en-IN')"></span>
                                <span class="text-primary-600 font-bold text-[13px] leading-none"
                                    x-text="'₹' + Number(p.selling_price).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                            </div>
                        </div>
                    </button>
                </template>
                <div x-show="searchResults.length === 0" class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <p class="text-sm font-medium">No products found</p>
                    <p class="text-xs text-gray-300">Try a different search term</p>
                </div>
            </div>

            {{-- Service grid --}}
            <div x-show="itemType === 'service'"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-2.5 overflow-y-auto flex-1 max-h-[60vh] pb-1 pr-1 content-start auto-rows-max">
                <template x-for="s in filteredServices" :key="s.id">
                    <button @click="openServiceModal(s)"
                        class="group relative bg-white rounded-lg text-left shadow-sm hover:shadow-lg border border-gray-100 hover:border-indigo-300 transition-all duration-200 overflow-hidden flex flex-col cursor-pointer">
                        <div class="relative w-full overflow-hidden" style="height:80px">
                            <img x-show="s.thumbnail" :src="'/storage/' + s.thumbnail"
                                class="absolute inset-0 w-full h-full object-contain p-1 group-hover:scale-110 transition-transform duration-300">
                            <div x-show="!s.thumbnail" class="absolute inset-0 flex items-center justify-center bg-gradient-to-b from-indigo-50 to-violet-100">
                                <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-200 flex items-center justify-center">
                                <div class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full shadow-md flex items-center justify-center opacity-0 group-hover:opacity-100 scale-50 group-hover:scale-100 transition-all duration-200">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </div>
                            </div>
                        </div>
                        <div class="px-2 py-1.5 flex flex-col gap-0.5 border-t border-gray-50">
                            <p class="font-semibold text-[11px] text-gray-800 truncate leading-tight" x-text="s.name"></p>
                            <p class="text-[10px] text-gray-400 truncate leading-none" x-text="s.description || ''"></p>
                            <span class="text-indigo-600 font-bold text-[13px] leading-none mt-0.5"
                                x-text="'₹' + Number(s.default_price || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                        </div>
                    </button>
                </template>
                <div x-show="filteredServices.length === 0" class="col-span-full flex flex-col items-center justify-center text-gray-400 py-20 gap-3">
                    <svg class="w-14 h-14 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <p class="text-sm font-medium">No services found</p>
                </div>
            </div>

            {{-- Manual item entry --}}
            <div x-show="itemType === 'manual'" class="card p-4">
                <h4 class="text-sm font-semibold text-gray-700 mb-3">Manual Item Entry</h4>
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <div class="md:col-span-2">
                        <label class="text-xs font-medium text-gray-600">Item Name *</label>
                        <input x-model="manualItem.item_name" type="text" class="form-input-custom mt-1 text-sm" placeholder="e.g. Tempered Glass">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">MRP</label>
                        <input x-model.number="manualItem.mrp" type="number" step="0.01" class="form-input-custom mt-1 text-sm" placeholder="0.00">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-600">Sell Price *</label>
                        <input x-model.number="manualItem.price" type="number" step="0.01" class="form-input-custom mt-1 text-sm" placeholder="0.00">
                    </div>
                    <div class="flex items-end">
                        <button @click="addManualItem()" class="btn-primary w-full text-sm">Add to Cart</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Cart & Customer --}}
        <div class="flex flex-col gap-3">

            {{-- Customer selector --}}
            <div class="card">
                <div class="card-body py-3">
                    <div class="flex gap-2 items-end">
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-600">Customer</label>
                            <input x-model="customerSearch" @input.debounce.300ms="findCustomers()" type="text"
                                class="form-input-custom mt-1 text-sm" placeholder="Search by name / phone...">
                        </div>
                        <button type="button" @click="showAddCustomer = true; newCustomer = {name:'',mobile_number:'',email:'',address:''}"
                            class="btn-primary text-sm px-3 py-2 whitespace-nowrap">+ New</button>
                    </div>
                    <div x-show="customerResults.length > 0" class="border rounded mt-1 max-h-32 overflow-y-auto bg-white shadow-sm z-20 relative">
                        <template x-for="c in customerResults" :key="c.id">
                            <button @click="selectCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-gray-50 text-sm border-b last:border-0"
                                x-text="c.name + ' - ' + c.mobile_number"></button>
                        </template>
                    </div>
                    <div x-show="customerSearch.length >= 2 && customerResults.length === 0"
                        class="text-xs text-gray-400 mt-1">No customers found - click <strong>+ New</strong> to add.</div>
                    <div x-show="selectedCustomer" class="mt-2 flex items-center gap-2">
                        <span class="badge badge-primary text-xs" x-text="selectedCustomer?.name"></span>
                        <button @click="selectedCustomer = null; form.customer_id = null; customerSearch = ''"
                            class="text-red-400 hover:text-red-600 text-xs">&times; Remove</button>
                    </div>
                </div>
            </div>

            {{-- Cart --}}
            <div class="card flex-1 flex flex-col">
                <div class="card-header py-2 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Cart (<span x-text="cart.length"></span> item<span x-show="cart.length !== 1">s</span>)
                    </h3>
                    <button x-show="cart.length > 0" @click="cart = []"
                        class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="flex-1 overflow-y-auto max-h-[38vh]">
                    <template x-for="(item, idx) in cart" :key="idx">
                        <div class="px-3 py-2 border-b last:border-0 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate leading-tight cursor-default"
                                        x-text="item.item_name"
                                        @if($canViewCostPrice) @click="item._showDisc = !item._showDisc" @endif></p>
                                    {{-- Service work description --}}
                                    <p x-show="item.item_type === 'service' && item.notes"
                                        class="text-[10px] text-indigo-500 truncate leading-tight mt-0.5"
                                        x-text="item.notes + (item.item_unit && item.item_unit !== 'pcs' ? ' · ' + item.item_unit : '')"></p>
                                    {{-- MRP reference --}}
                                    <div x-show="item.mrp && item.mrp > item.price" class="flex items-center gap-1 mt-0.5">
                                        <span class="text-[10px] text-gray-400">MRP:</span>
                                        <span class="text-[10px] text-gray-400 line-through" x-text="'₹' + Number(item.mrp).toFixed(2)"></span>
                                    </div>
                                    {{-- Max discount (privileged only, revealed by tapping item name) --}}
                                    @if($canViewCostPrice)
                                    <div x-show="item._showDisc && item.cost_price > 0" x-transition.opacity.duration.150ms class="mt-0.5">
                                        <span class="text-[10px] text-gray-500">↓ ₹<span x-text="Math.max(0, item.price - item.cost_price).toFixed(2)"></span></span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Qty controls --}}
                                <div class="flex items-center gap-1 shrink-0">
                                    <button @click="item.quantity > 1 ? item.quantity-- : null"
                                        class="w-5 h-5 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 text-xs">-</button>
                                    <span class="text-sm w-7 text-center font-medium" x-text="item.quantity"></span>
                                    <button @click="item.quantity++"
                                        class="w-5 h-5 rounded bg-gray-200 text-gray-700 flex items-center justify-center hover:bg-gray-300 text-xs">+</button>
                                </div>

                                {{-- Editable price + line total --}}
                                <div class="flex flex-col items-end shrink-0 gap-0.5">
                                    <div class="flex items-center gap-1">
                                        <span class="text-xs text-gray-400">₹</span>
                                        <input x-model.number="item.price" type="number" step="0.01" min="0"
                                            class="w-20 text-right text-sm border border-gray-300 rounded px-1.5 py-0.5 focus:border-primary-400 focus:outline-none"
                                            @change="if(item.price < 0) item.price = 0">
                                    </div>
                                    <span class="text-xs font-semibold text-gray-700"
                                        x-text="'= ₹' + (item.price * item.quantity).toFixed(2)"></span>
                                </div>

                                <button @click="cart.splice(idx, 1)" class="text-red-400 hover:text-red-600 shrink-0 ml-1 text-lg leading-none">&times;</button>
                            </div>
                        </div>
                    </template>
                    <div x-show="cart.length === 0" class="text-center text-gray-400 py-10 text-sm">
                        <svg class="w-10 h-10 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        Cart is empty - add products above
                    </div>
                </div>

                {{-- Summary --}}
                <div class="border-t px-4 py-3 space-y-1.5 text-sm bg-gray-50/50">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span x-text="'₹' + subtotal().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center text-gray-600">
                        <span>Discount (₹)</span>
                        <input x-model.number="form.discount" type="number" step="0.01" min="0"
                            class="w-24 text-right text-sm border border-gray-300 rounded px-2 py-1 focus:border-primary-400 focus:outline-none">
                    </div>
                    <div class="flex justify-between font-bold text-base pt-1.5 border-t border-gray-200">
                        <span>Total</span>
                        <span class="text-primary-600 text-lg" x-text="'₹' + grandTotal().toFixed(2)"></span>
                    </div>
                </div>

                {{-- Create Invoice button --}}
                <div class="border-t px-4 py-3">
                    <button @click="createInvoiceDraft()"
                        class="btn-primary w-full py-3 text-base font-semibold"
                        :disabled="saving || cart.length === 0 || grandTotal() <= 0">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Create Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- PAYMENT MODAL --}}
    <div x-show="showPaymentModal" x-cloak class="modal-overlay" @keydown.escape.window="skipPayment()">
        <div class="modal-container max-w-lg" @click.stop>
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Record Payment</h3>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Invoice <span class="font-semibold text-primary-600" x-text="'#' + (createdInvoice ? createdInvoice.invoice_number : '')"></span>
                        - collect payment now
                    </p>
                </div>
                <button @click="skipPayment()" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="modal-body space-y-4">
                <div class="bg-primary-50 border border-primary-100 rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-gray-500">Invoice Total</span>
                        <p class="text-xl font-bold text-primary-700" x-text="'₹' + Number(createdInvoice ? createdInvoice.final_amount : 0).toFixed(2)"></p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Balance Due</span>
                        <p class="text-xl font-bold text-red-600" x-text="'₹' + balanceDue().toFixed(2)"></p>
                    </div>
                </div>

                <template x-for="(pay, pidx) in payForm.payments" :key="pidx">
                    <div class="space-y-2 p-3 rounded-lg border border-gray-200 bg-gray-50">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold text-gray-600"
                                x-text="payForm.payments.length > 1 ? 'Payment ' + (pidx + 1) : 'Payment Method'"></span>
                            <button x-show="payForm.payments.length > 1" @click="payForm.payments.splice(pidx, 1)"
                                class="text-red-400 hover:text-red-600 text-xs">Remove</button>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Method</label>
                                <select x-model="pay.payment_method" class="form-select-custom text-sm w-full">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card / Swipe</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 mb-1 block">Amount (₹)</label>
                                <input x-model.number="pay.amount" type="number" step="0.01" min="0"
                                    class="form-input-custom text-sm w-full" placeholder="0.00">
                            </div>
                        </div>

                        {{-- Reference field: UPI / bank / cheque --}}
                        <div x-show="pay.payment_method === 'upi' || pay.payment_method === 'bank_transfer' || pay.payment_method === 'cheque'">
                            <label class="text-xs text-gray-500 mb-1 block"
                                x-text="pay.payment_method === 'cheque' ? 'Cheque No.' : (pay.payment_method === 'upi' ? 'UPI / IMPS Ref No. *' : 'NEFT / RTGS Ref No.')"></label>
                            <input x-model="pay.transaction_reference" type="text"
                                class="form-input-custom text-sm w-full"
                                :placeholder="pay.payment_method === 'upi' ? 'Enter UPI transaction reference' : (pay.payment_method === 'cheque' ? 'Enter cheque number' : 'Enter transaction reference')"
                                :class="pay.payment_method === 'upi' && !pay.transaction_reference ? 'border-amber-400' : ''">
                            <p x-show="pay.payment_method === 'upi' && !pay.transaction_reference"
                                class="text-[10px] text-amber-600 mt-0.5">Required for UPI payments</p>
                        </div>
                    </div>
                </template>

                <button @click="payForm.payments.push({payment_method:'cash', amount:0, transaction_reference:''})"
                    class="text-xs text-primary-600 hover:text-primary-800 hover:underline flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Split Payment (add another method)
                </button>

                <div class="flex items-center justify-between text-sm pt-1 border-t">
                    <span class="text-gray-500">Total Paying</span>
                    <span class="font-semibold" :class="totalPaying() >= Number(createdInvoice ? createdInvoice.final_amount : 0) ? 'text-green-600' : 'text-amber-600'"
                        x-text="'₹' + totalPaying().toFixed(2)"></span>
                </div>
                <div x-show="totalPaying() > 0 && totalPaying() < Number(createdInvoice ? createdInvoice.final_amount : 0)"
                    class="text-xs text-amber-700 bg-amber-50 rounded px-3 py-2 border border-amber-200">
                    Partial payment. Balance of ₹<span x-text="balanceDue().toFixed(2)"></span> will remain outstanding.
                </div>
                <div x-show="totalPaying() > Number(createdInvoice ? createdInvoice.final_amount : 0)"
                    class="flex justify-between text-sm text-green-700 bg-green-50 rounded px-3 py-2 border border-green-200">
                    <span>Change to return to customer</span>
                    <span class="font-semibold" x-text="'₹' + (totalPaying() - Number(createdInvoice ? createdInvoice.final_amount : 0)).toFixed(2)"></span>
                </div>
            </div>

            <div class="modal-footer gap-2">
                <button type="button" @click="skipPayment()" class="btn-secondary text-sm">Pay Later</button>
                <button type="button" @click="recordPayment()" class="btn-primary text-sm px-6"
                    :disabled="paying || totalPaying() <= 0">
                    <span x-show="paying" class="spinner mr-2"></span>
                    Pay &amp; Complete
                </button>
            </div>
        </div>
    </div>

    {{-- SERVICE ENTRY MODAL --}}
    <div x-show="svcModal.open" x-cloak class="modal-overlay" @keydown.escape.window="svcModal.open = false">
        <div class="modal-container max-w-lg" @click.stop>
            <div class="modal-header">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900" x-text="svcModal.service?.name"></h3>
                        <p class="text-xs text-gray-400" x-text="svcModal.service?.description || 'Enter service details below'"></p>
                    </div>
                </div>
                <button @click="svcModal.open = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-4">

                {{-- Smart quick-fill suggestions --}}
                <div x-show="svcModalSuggestions.length > 0">
                    <p class="text-xs font-medium text-gray-500 mb-1.5">Quick fill</p>
                    <div class="flex flex-wrap gap-1.5">
                        <template x-for="(tag, i) in svcModalSuggestions" :key="i">
                            <button type="button"
                                @click="svcModal.desc = tag[0]; if(tag[1]) svcModal.unit = tag[1]"
                                :class="svcModal.desc === tag[0] ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-400 hover:text-indigo-600'"
                                class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                                x-text="tag[0]"></button>
                        </template>
                    </div>
                </div>

                {{-- What was done --}}
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Work Description <span class="text-gray-400 font-normal">(what was done)</span></label>
                    <textarea x-model="svcModal.desc" rows="2" class="form-input-custom text-sm resize-none"
                        placeholder="e.g. A4 B&amp;W 20 pages, iPhone 13 OEM screen, Grade-A battery..."></textarea>
                </div>

                {{-- Qty / Unit / Price --}}
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
                        <input x-model.number="svcModal.qty" type="number" min="1" step="1"
                            class="form-input-custom text-sm text-center" placeholder="1">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                        <select x-model="svcModal.unit" class="form-select-custom text-sm">
                            <option value="pcs">pcs</option>
                            <option value="pages">pages</option>
                            <option value="sheets">sheets</option>
                            <option value="hours">hours</option>
                            <option value="minutes">minutes</option>
                            <option value="sets">sets</option>
                            <option value="items">items</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Price / unit (₹)</label>
                        <input x-model.number="svcModal.price" type="number" min="0" step="0.01"
                            class="form-input-custom text-sm text-right" placeholder="0.00">
                    </div>
                </div>

                {{-- Live total --}}
                <div class="bg-indigo-50 rounded-lg px-4 py-3 flex items-center justify-between">
                    <span class="text-sm text-indigo-700 font-medium">Total</span>
                    <span class="text-xl font-bold text-indigo-700"
                        x-text="'₹' + ((svcModal.qty || 0) * (svcModal.price || 0)).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="svcModal.open = false" class="btn-secondary">Cancel</button>
                <button type="button" @click="confirmAddService()" class="btn-primary">
                    <svg class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add to Cart
                </button>
            </div>
        </div>
    </div>

    {{-- SUCCESS MODAL --}}
    <div x-show="showSuccessModal" x-cloak class="modal-overlay">
        <div class="modal-container max-w-sm text-center" @click.stop>
            <div class="modal-body py-8 flex flex-col items-center gap-4">
                <div class="w-16 h-16 rounded-full flex items-center justify-center"
                    :class="createdInvoice && createdInvoice.payment_status === 'paid' ? 'bg-green-100' : 'bg-amber-100'">
                    <svg x-show="createdInvoice && createdInvoice.payment_status === 'paid'" class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="!createdInvoice || createdInvoice.payment_status !== 'paid'" class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800"
                        x-text="createdInvoice && createdInvoice.payment_status === 'paid' ? 'Payment Complete!' : 'Invoice Saved'"></h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Invoice <span class="font-semibold text-primary-600" x-text="'#' + (createdInvoice ? createdInvoice.invoice_number : '')"></span>
                    </p>
                    <p x-show="createdInvoice && createdInvoice.payment_status === 'unpaid'" class="text-xs text-amber-600 mt-1">
                        Outstanding: ₹<span x-text="Number(createdInvoice ? createdInvoice.final_amount : 0).toFixed(2)"></span>
                    </p>
                    <p x-show="createdInvoice && createdInvoice.payment_status === 'partial'" class="text-xs text-amber-600 mt-1">
                        Partial payment recorded - balance pending
                    </p>
                </div>
                <div class="flex gap-3 flex-wrap justify-center">
                    <a :href="'/invoices/' + (createdInvoice ? createdInvoice.id : '') + '/print'" target="_blank"
                        class="btn-secondary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Invoice
                    </a>
                    <button @click="newSale()" class="btn-primary text-sm px-4">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        New Sale
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD CUSTOMER MODAL --}}
    <div x-show="showAddCustomer" x-cloak class="modal-overlay">
        <div class="modal-container max-w-md" @click.stop>
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Add New Customer</h3>
                <button @click="showAddCustomer = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input x-model="newCustomer.name" type="text" class="form-input-custom" placeholder="Full name">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile *</label>
                    <input x-model="newCustomer.mobile_number" type="text" class="form-input-custom" placeholder="10-digit mobile number">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input x-model="newCustomer.email" type="email" class="form-input-custom" placeholder="Optional">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input x-model="newCustomer.address" type="text" class="form-input-custom" placeholder="Optional">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="showAddCustomer = false" class="btn-secondary">Cancel</button>
                <button type="button" @click.prevent="saveNewCustomer()" class="btn-primary">Save &amp; Select</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function posBilling() {
    return {
        searchQuery: '',
        searchResults: [],
        allServices: [],
        cart: [],
        itemType: 'product',
        saving: false,
        paying: false,

        // Filters
        filterCategories: [],
        filterSubcategories: [],
        filterBrands: [],
        filterCategory: null,
        filterSubcategory: '',
        filterBrand: '',

        customerSearch: '',
        customerResults: [],
        selectedCustomer: null,
        showAddCustomer: false,
        newCustomer: { name: '', mobile_number: '', email: '', address: '' },

        manualItem: { item_name: '', price: 0, mrp: 0 },

        svcModal: { open: false, service: null, desc: '', qty: 1, unit: 'pcs', price: 0 },

        form: { customer_id: null, discount: 0 },

        createdInvoice: null,
        showPaymentModal: false,
        showSuccessModal: false,
        payForm: { payments: [{ payment_method: 'cash', amount: 0, transaction_reference: '' }] },

        canViewCostPrice: {{ $canViewCostPrice ? 'true' : 'false' }},

        async init() {
            await Promise.all([this.searchProducts(), this.loadServices(), this.loadFilterData()]);
        },

        async loadFilterData() {
            const r = await RepairBox.ajax('/products-filter-data');
            const d = r.data || r;
            if (d.categories) this.filterCategories = d.categories;
            if (d.brands)     this.filterBrands     = d.brands;
        },

        async loadFilterBrands(categoryId, subcatId) {
            const params = new URLSearchParams();
            if (categoryId) params.set('category_id', categoryId);
            if (subcatId)   params.set('subcategory_id', subcatId);
            const r = await RepairBox.ajax('/products-filter-data?' + params.toString());
            const d = r.data || r;
            if (d.brands) this.filterBrands = d.brands;
        },

        async selectCategory(cat) {
            if (!cat) {
                this.filterCategory      = null;
                this.filterSubcategory   = '';
                this.filterSubcategories = [];
                this.filterBrand         = '';
                await this.loadFilterBrands();
            } else {
                this.filterCategory      = cat.id;
                this.filterSubcategory   = '';
                this.filterSubcategories = cat.subcategories || [];
                this.filterBrand         = '';
                await this.loadFilterBrands(cat.id);
            }
            this.searchProducts();
        },

        async onSubcategoryChange() {
            this.filterBrand = '';
            await this.loadFilterBrands(this.filterCategory, this.filterSubcategory || null);
            this.searchProducts();
        },

        clearFilters() {
            this.filterCategory      = null;
            this.filterSubcategory   = '';
            this.filterSubcategories = [];
            this.filterBrand         = '';
            this.loadFilterBrands();
            this.searchProducts();
        },

        async searchProducts() {
            const params = new URLSearchParams();
            params.set('q', this.searchQuery);
            if (this.filterCategory)    params.set('category_id',    this.filterCategory);
            if (this.filterSubcategory) params.set('subcategory_id', this.filterSubcategory);
            if (this.filterBrand)       params.set('brand_id',       this.filterBrand);
            const r = await RepairBox.ajax('/products-search?' + params.toString());
            if (r.data) this.searchResults = r.data;
        },

        async loadServices() {
            const r = await RepairBox.ajax('/service-types');
            if (Array.isArray(r.data)) this.allServices = r.data.filter(s => s.status === 'active');
        },

        get filteredServices() {
            if (!this.searchQuery) return this.allServices;
            const q = this.searchQuery.toLowerCase();
            return this.allServices.filter(s =>
                s.name.toLowerCase().includes(q) ||
                (s.description && s.description.toLowerCase().includes(q))
            );
        },

        addProduct(p) {
            const existing = this.cart.find(c => c.product_id === p.id && c.item_type === 'product');
            if (existing) { existing.quantity++; return; }
            this.cart.push({
                item_type: 'product',
                product_id: p.id,
                service_id: null,
                item_name: p.name,
                quantity: 1,
                price: Number(p.selling_price),
                mrp: Number(p.mrp || 0),
                cost_price: Number(p.purchase_price || 0),
                max_selling_price: Number(p.max_selling_price || 0),
                _showDisc: false,
            });
        },

        openServiceModal(s) {
            this.svcModal = { open: true, service: s, desc: '', qty: 1, unit: 'pcs', price: Number(s.default_price || 0) };
        },

        get svcModalSuggestions() {
            const n = (this.svcModal.service?.name || '').toLowerCase();
            if (n.includes('xerox') || n.includes('print') || n.includes('copy') || n.includes('photocopy'))
                return [['A4 B&W', 'pages'], ['A4 Color', 'pages'], ['A3 B&W', 'pages'], ['A3 Color', 'pages'], ['Legal B&W', 'pages']];
            if (n.includes('lamination') || n.includes('laminate'))
                return [['A4 Lamination', 'sheets'], ['A3 Lamination', 'sheets'], ['ID Card Size', 'pcs'], ['Passport Size', 'pcs']];
            if (n.includes('screen') || n.includes('display') || n.includes('lcd'))
                return [['Original Screen', null], ['OEM Screen', null], ['Grade A Screen', null], ['Grade B Screen', null], ['Copy Screen', null]];
            if (n.includes('battery'))
                return [['Original Battery', null], ['OEM Battery', null], ['Duplicate Battery', null]];
            if (n.includes('charging') || n.includes('port') || n.includes('usb'))
                return [['USB-C Port', null], ['Micro-USB Port', null], ['Lightning Port', null], ['Type-B Port', null]];
            if (n.includes('software') || n.includes('flash') || n.includes('update'))
                return [['Flash / Re-flash', null], ['Factory Reset', null], ['OS Update', null], ['IMEI Repair', null], ['Pattern Unlock', null]];
            if (n.includes('data') || n.includes('recovery'))
                return [['Full Recovery', null], ['Contacts Only', null], ['Photos Only', null], ['WhatsApp Backup', null]];
            if (n.includes('water') || n.includes('damage'))
                return [['Diagnosis Done', null], ['Board Level Repair', null], ['Cleaning + Dry', null]];
            return [];
        },

        confirmAddService() {
            const m = this.svcModal;
            if (!m.qty || m.qty <= 0) { RepairBox.toast('Quantity must be at least 1', 'error'); return; }
            if (!m.price || m.price < 0) { RepairBox.toast('Enter a valid price', 'error'); return; }
            this.cart.push({
                item_type: 'service',
                product_id: null,
                service_id: m.service.id,
                item_name: m.service.name,
                notes: m.desc.trim(),
                item_unit: m.unit,
                quantity: m.qty,
                price: m.price,
                mrp: m.price,
                cost_price: 0,
                max_selling_price: 0,
                _showDisc: false,
            });
            this.svcModal.open = false;
        },

        addManualItem() {
            if (!this.manualItem.item_name || !this.manualItem.price) {
                RepairBox.toast('Item name and price are required', 'error');
                return;
            }
            this.cart.push({
                item_type: 'manual',
                product_id: null,
                service_id: null,
                item_name: this.manualItem.item_name,
                quantity: 1,
                price: Number(this.manualItem.price),
                mrp: Number(this.manualItem.mrp || this.manualItem.price),
                cost_price: 0,
                max_selling_price: 0,
                _showDisc: false,
            });
            this.manualItem = { item_name: '', price: 0, mrp: 0 };
        },

        async findCustomers() {
            if (this.customerSearch.length < 2) { this.customerResults = []; return; }
            const r = await RepairBox.ajax('/customers-search?q=' + encodeURIComponent(this.customerSearch));
            if (r.data) this.customerResults = r.data;
        },

        selectCustomer(c) {
            this.selectedCustomer = c;
            this.form.customer_id = c.id;
            this.customerResults = [];
            this.customerSearch = '';
        },

        async saveNewCustomer() {
            if (!this.newCustomer.name || !this.newCustomer.mobile_number) {
                RepairBox.toast('Name and mobile are required', 'error');
                return;
            }
            const r = await RepairBox.ajax('/customers', 'POST', this.newCustomer);
            if (r.success !== false && r.data) {
                this.selectCustomer(r.data);
                this.showAddCustomer = false;
                RepairBox.toast('Customer added', 'success');
            }
        },

        subtotal() {
            return this.cart.reduce((s, i) => s + Number(i.price) * i.quantity, 0);
        },

        grandTotal() {
            return Math.max(0, this.subtotal() - (Number(this.form.discount) || 0));
        },

        totalPaying() {
            return this.payForm.payments.reduce((s, p) => s + (Number(p.amount) || 0), 0);
        },

        balanceDue() {
            const invoiceTotal = Number(this.createdInvoice ? this.createdInvoice.final_amount : 0);
            return Math.max(0, invoiceTotal - this.totalPaying());
        },

        async createInvoiceDraft() {
            if (this.cart.length === 0) { RepairBox.toast('Cart is empty', 'error'); return; }
            if (!this.form.customer_id) { RepairBox.toast('Please select a customer', 'error'); return; }

            this.saving = true;
            const payload = {
                customer_id: this.form.customer_id,
                discount: this.form.discount || 0,
                items: this.cart.map(item => ({
                    item_type: item.item_type,
                    product_id: item.product_id || null,
                    service_id: item.service_id || null,
                    item_name: item.notes ? item.item_name + ' — ' + item.notes : item.item_name,
                    quantity: item.quantity,
                    price: item.price,
                    mrp: item.mrp || item.price,
                })),
            };

            const r = await RepairBox.ajax('/invoices', 'POST', payload);
            this.saving = false;

            if (r.success !== false && r.data) {
                this.createdInvoice = r.data;
                this.payForm.payments = [{
                    payment_method: 'cash',
                    amount: Number(r.data.final_amount),
                    transaction_reference: '',
                }];
                this.showPaymentModal = true;
            }
        },

        async recordPayment() {
            if (!this.createdInvoice) return;
            if (this.totalPaying() <= 0) { RepairBox.toast('Enter payment amount', 'error'); return; }

            for (const pay of this.payForm.payments) {
                if (pay.payment_method === 'upi' && !String(pay.transaction_reference || '').trim()) {
                    RepairBox.toast('UPI reference number is required', 'error');
                    return;
                }
            }

            this.paying = true;
            const r = await RepairBox.ajax(
                '/invoices/' + this.createdInvoice.id + '/pay',
                'POST',
                { payments: this.payForm.payments }
            );
            this.paying = false;

            if (r.success !== false && r.data) {
                this.createdInvoice = r.data;
                this.showPaymentModal = false;
                this.showSuccessModal = true;
            }
        },

        skipPayment() {
            this.showPaymentModal = false;
            this.showSuccessModal = true;
        },

        newSale() {
            this.cart = [];
            this.form = { customer_id: null, discount: 0 };
            this.selectedCustomer = null;
            this.customerSearch = '';
            this.createdInvoice = null;
            this.showSuccessModal = false;
            this.showPaymentModal = false;
            this.payForm = { payments: [{ payment_method: 'cash', amount: 0, transaction_reference: '' }] };
            this.$nextTick(() => { const el = document.querySelector('[autofocus]'); if (el) el.focus(); });
        },
    };
}
</script>
@endpush
