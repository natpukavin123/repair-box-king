@extends('layouts.app')
@section('page-title', 'Credit Note ' . $creditNote->credit_note_number)

@section('content')
<div x-data="creditNoteShow()" class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-gray-800">{{ $creditNote->credit_note_number }}</h2>
                @php
                $statusClass = match($creditNote->status) {
                'draft' => 'badge-secondary',
                'approved' => 'badge-info',
                'partially_refunded' => 'badge-warning',
                'fully_refunded' => 'badge-success',
                'cancelled' => 'badge-danger',
                default => 'badge-secondary',
                };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $creditNote->status)) }}</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">
                Source:
                @if($creditNote->source_type === 'invoice')
                <a href="/invoices" class="text-primary-600 hover:underline">Invoice #{{
                    $creditNote->sourceInvoice?->invoice_number }}</a>
                @else
                <a href="/repairs/{{ $creditNote->source_id }}" class="text-primary-600 hover:underline">Repair #{{
                    $creditNote->sourceRepair?->ticket_number }}</a>
                @endif
                &bull; Created {{ $creditNote->created_at->format('d M Y, h:i A') }}
                @if($creditNote->creator) by {{ $creditNote->creator->name }} @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/credit-notes" class="btn-secondary">← Back</a>
            @if($creditNote->status === 'draft')
            <button @click="approve()" class="btn-primary" :disabled="processing">Approve</button>
            <button @click="cancel()" class="btn-danger" :disabled="processing">Cancel</button>
            @endif
            @if($creditNote->status === 'approved')
            <button @click="cancel()" class="btn-danger" :disabled="processing">Cancel</button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer & Reason -->
            <div class="card">
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Customer</p>
                            <p class="font-medium text-gray-800">{{ $creditNote->customer?->name ?? 'Walk-in' }}</p>
                            @if($creditNote->customer?->phone)
                            <p class="text-sm text-gray-500">{{ $creditNote->customer->phone }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Reason</p>
                            <p class="text-sm text-gray-700">{{ $creditNote->reason }}</p>
                        </div>
                    </div>
                    @if($creditNote->notes)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Notes</p>
                        <p class="text-sm text-gray-600">{{ $creditNote->notes }}</p>
                    </div>
                    @endif
                    @if($creditNote->approver)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500">Approved by <strong>{{ $creditNote->approver->name }}</strong>
                            on {{ $creditNote->approved_at->format('d M Y, h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Items -->
            <div class="card">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-700">Return Items</h3>
                </div>
                <div class="card-body p-0">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Item</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Type</th>
                                <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Qty</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Unit Price</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($creditNote->items as $item)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $item->item_name }}</td>
                                <td class="px-4 py-3"><span class="badge badge-info text-xs">{{
                                        ucfirst($item->item_type) }}</span></td>
                                <td class="px-4 py-3 text-center text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-right text-sm">₹{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold">₹{{ number_format($item->total,
                                    2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-4 py-3 text-right font-semibold text-gray-700">Credit Total
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">₹{{
                                    number_format($creditNote->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Refund / Resolution History -->
            <div class="card">
                <div class="card-header">
                    <h3 class="font-semibold text-gray-700">Resolution History</h3>
                </div>
                <div class="card-body">
                    @if($creditNote->refunds->count() > 0)
                    <div class="space-y-3">
                        @foreach($creditNote->refunds as $refund)
                        @php
                        $rType = $refund->resolution_type ?? 'refund';
                        $bgClass = match($rType) {
                        'new_repair' => 'bg-blue-50 border-blue-100',
                        'new_invoice' => 'bg-purple-50 border-purple-100',
                        default => 'bg-emerald-50 border-emerald-100',
                        };
                        $textClass = match($rType) {
                        'new_repair' => 'text-blue-800',
                        'new_invoice' => 'text-purple-800',
                        default => 'text-emerald-800',
                        };
                        $label = match($rType) {
                        'new_repair' => 'Applied to Repair',
                        'new_invoice' => 'Applied to Invoice',
                        default => 'Cash Refund',
                        };
                        @endphp
                        <div class="flex items-center justify-between p-3 {{ $bgClass }} rounded-xl border">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-semibold {{ $textClass }}">₹{{ number_format($refund->amount, 2) }}
                                    </p>
                                    <span
                                        class="text-xs px-2 py-0.5 rounded-full bg-white/60 font-medium {{ $textClass }}">{{
                                        $label }}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    via {{ ucfirst(str_replace('_', ' ', $refund->method)) }}
                                    @if($refund->reference_number) &bull; Ref: {{ $refund->reference_number }} @endif
                                    &bull; {{ $refund->processed_at?->format('d M Y, h:i A') }}
                                    @if($refund->processor) &bull; by {{ $refund->processor->name }} @endif
                                </p>
                                @if($refund->notes)
                                <p class="text-xs text-gray-500 mt-1">{{ $refund->notes }}</p>
                                @endif
                                @if($refund->reference_id && $refund->reference_type === 'repairs')
                                <a href="/repairs/{{ $refund->reference_id }}"
                                    class="text-xs text-blue-600 hover:underline mt-1 inline-block">→ View Repair</a>
                                @elseif($refund->reference_id && $refund->reference_type === 'invoices')
                                <a href="/invoices" class="text-xs text-purple-600 hover:underline mt-1 inline-block">→
                                    View Invoice</a>
                                @endif
                            </div>
                            <svg class="w-6 h-6 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 text-center py-6">No resolutions yet</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Summary & Actions -->
        <div class="space-y-6">
            <!-- Amount Summary -->
            <div class="card">
                <div class="card-body !py-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Credit Amount</span>
                        <span class="font-bold text-lg">₹{{ number_format($creditNote->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Used / Refunded</span>
                        <span class="font-semibold text-emerald-600">₹{{ number_format($creditNote->refunded_amount, 2)
                            }}</span>
                    </div>
                    <hr>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-700">Balance</span>
                        <span class="font-bold text-lg text-amber-600">₹{{
                            number_format($creditNote->remainingRefundable(), 2) }}</span>
                    </div>
                    @if($creditNote->total_amount > 0)
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full transition-all"
                            style="width: {{ min(100, ($creditNote->refunded_amount / $creditNote->total_amount) * 100) }}%">
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Resolution Actions -->
            @if(in_array($creditNote->status, ['approved', 'partially_refunded']))
            <div class="card border-2 border-primary-200">
                <div class="card-header bg-primary-50 p-0">
                    <!-- Tabs -->
                    <div class="secondary-tabs m-0 rounded-none border-0 border-b border-primary-100 bg-transparent p-2 shadow-none">
                        <button @click="activeTab = 'refund'"
                            class="secondary-tab flex-1 text-xs transition-colors"
                            :class="activeTab === 'refund' ? 'secondary-tab is-active' : 'secondary-tab'">
                            💰 Refund
                        </button>
                        <button @click="activeTab = 'repair'"
                            class="secondary-tab flex-1 text-xs transition-colors"
                            :class="activeTab === 'repair' ? 'secondary-tab is-active' : 'secondary-tab'">
                            🔧 New Repair
                        </button>
                        <button @click="activeTab = 'invoice'"
                            class="secondary-tab flex-1 text-xs transition-colors"
                            :class="activeTab === 'invoice' ? 'secondary-tab is-active' : 'secondary-tab'">
                            🧾 New Invoice
                        </button>
                    </div>
                </div>
                <div class="card-body space-y-3">
                    <!-- Tab 1: Cash Refund -->
                    <div x-show="activeTab === 'refund'" x-transition>
                        <div class="space-y-3">
                            <div>
                                <label class="form-label">Amount *</label>
                                <input x-model="refundForm.amount" type="number" step="0.01" min="0.01"
                                    max="{{ $creditNote->remainingRefundable() }}" class="form-input-custom"
                                    placeholder="0.00">
                                <p class="text-xs text-gray-400 mt-1">Max: ₹{{
                                    number_format($creditNote->remainingRefundable(), 2) }}</p>
                            </div>
                            <div>
                                <label class="form-label">Method *</label>
                                <select x-model="refundForm.method" class="form-select-custom">
                                    <option value="">Select</option>
                                    <option value="cash">Cash</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="card">Card</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Reference No.</label>
                                <input x-model="refundForm.reference_number" type="text" class="form-input-custom"
                                    placeholder="Transaction ref">
                            </div>
                            <div>
                                <label class="form-label">Notes</label>
                                <textarea x-model="refundForm.notes" rows="2" class="form-input-custom"
                                    placeholder="Optional"></textarea>
                            </div>
                            <button @click="processRefund()" class="btn-primary w-full"
                                :disabled="processing || !refundForm.amount || !refundForm.method">
                                <span x-show="processing" class="spinner mr-1"
                                    style="width:16px;height:16px;border-width:2px"></span>
                                Process Refund
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Apply to Repair -->
                    <div x-show="activeTab === 'repair'" x-transition>
                        <div class="space-y-3">
                            <p class="text-xs text-gray-500">Apply this credit as advance payment on a repair order.</p>
                            <div>
                                <label class="form-label">Search Repair *</label>
                                <input x-model="repairSearch" @input.debounce.400ms="searchRepairs()" type="text"
                                    class="form-input-custom" placeholder="Search by ticket number...">
                                <template x-if="repairResults.length > 0">
                                    <div
                                        class="mt-2 border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-40 overflow-y-auto">
                                        <template x-for="r in repairResults" :key="r.id">
                                            <div @click="selectRepair(r)"
                                                class="px-3 py-2 hover:bg-primary-50 cursor-pointer text-sm flex justify-between items-center"
                                                :class="repairForm.repair_id == r.id ? 'bg-primary-50' : ''">
                                                <div>
                                                    <span class="font-medium" x-text="r.ticket_number"></span>
                                                    <span class="text-gray-500 ml-2"
                                                        x-text="r.customer?.name || 'Walk-in'"></span>
                                                </div>
                                                <span class="text-xs text-gray-400" x-text="r.status"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <label class="form-label">Amount to Apply *</label>
                                <input x-model="repairForm.amount" type="number" step="0.01" min="0.01"
                                    max="{{ $creditNote->remainingRefundable() }}" class="form-input-custom"
                                    placeholder="0.00">
                                <p class="text-xs text-gray-400 mt-1">Max: ₹{{
                                    number_format($creditNote->remainingRefundable(), 2) }}</p>
                            </div>
                            <button @click="applyToRepair()"
                                class="btn-primary w-full bg-blue-600 hover:bg-blue-700 border-blue-600"
                                :disabled="processing || !repairForm.repair_id || !repairForm.amount">
                                <span x-show="processing" class="spinner mr-1"
                                    style="width:16px;height:16px;border-width:2px"></span>
                                Apply to Repair
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Apply to Invoice -->
                    <div x-show="activeTab === 'invoice'" x-transition>
                        <div class="space-y-3">
                            <p class="text-xs text-gray-500">Apply this credit as payment on an existing invoice.</p>
                            <div>
                                <label class="form-label">Search Invoice *</label>
                                <input x-model="invoiceSearch" @input.debounce.400ms="searchInvoices()" type="text"
                                    class="form-input-custom" placeholder="Search by invoice number...">
                                <template x-if="invoiceResults.length > 0">
                                    <div
                                        class="mt-2 border border-gray-200 rounded-lg divide-y divide-gray-100 max-h-40 overflow-y-auto">
                                        <template x-for="inv in invoiceResults" :key="inv.id">
                                            <div @click="selectInvoice(inv)"
                                                class="px-3 py-2 hover:bg-purple-50 cursor-pointer text-sm flex justify-between items-center"
                                                :class="invoiceForm.invoice_id == inv.id ? 'bg-purple-50' : ''">
                                                <div>
                                                    <span class="font-medium" x-text="inv.invoice_number"></span>
                                                    <span class="text-gray-500 ml-2"
                                                        x-text="inv.customer?.name || 'Walk-in'"></span>
                                                </div>
                                                <span class="text-xs"
                                                    x-text="'₹' + Number(inv.final_amount).toFixed(2)"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <label class="form-label">Amount to Apply *</label>
                                <input x-model="invoiceForm.amount" type="number" step="0.01" min="0.01"
                                    max="{{ $creditNote->remainingRefundable() }}" class="form-input-custom"
                                    placeholder="0.00">
                                <p class="text-xs text-gray-400 mt-1">Max: ₹{{
                                    number_format($creditNote->remainingRefundable(), 2) }}</p>
                            </div>
                            <button @click="applyToInvoice()"
                                class="btn-primary w-full bg-purple-600 hover:bg-purple-700 border-purple-600"
                                :disabled="processing || !invoiceForm.invoice_id || !invoiceForm.amount">
                                <span x-show="processing" class="spinner mr-1"
                                    style="width:16px;height:16px;border-width:2px"></span>
                                Apply to Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function creditNoteShow() {
        return {
            processing: false,
            activeTab: 'refund',
            refundForm: { amount: '', method: '', reference_number: '', notes: '' },
            repairSearch: '', repairResults: [], repairForm: { repair_id: '', amount: '' },
            invoiceSearch: '', invoiceResults: [], invoiceForm: { invoice_id: '', amount: '' },

            async approve() {
                if (!await RepairBox.confirm('Approve this credit note? This will allow resolutions to be processed.')) return;
                this.processing = true;
                const r = await RepairBox.ajax('/credit-notes/{{ $creditNote->id }}/approve', 'POST');
                this.processing = false;
                if (r.success !== false) { RepairBox.toast('Credit note approved', 'success'); location.reload(); }
            },

            async cancel() {
                if (!await RepairBox.confirm('Cancel this credit note? This action cannot be undone.')) return;
                this.processing = true;
                const r = await RepairBox.ajax('/credit-notes/{{ $creditNote->id }}/cancel', 'POST');
                this.processing = false;
                if (r.success !== false) { RepairBox.toast('Credit note cancelled', 'success'); location.reload(); }
            },

            async processRefund() {
                if (!await RepairBox.confirm(`Process refund of ₹${this.refundForm.amount}?`)) return;
                this.processing = true;
                const r = await RepairBox.ajax('/credit-notes/{{ $creditNote->id }}/refund', 'POST', this.refundForm);
                this.processing = false;
                if (r.success !== false) { RepairBox.toast(r.message || 'Refund processed', 'success'); location.reload(); }
            },

            async searchRepairs() {
                if (this.repairSearch.length < 2) { this.repairResults = []; return; }
                const r = await RepairBox.ajax('/repairs?search=' + encodeURIComponent(this.repairSearch) + '&per_page=10');
                this.repairResults = r.data?.data || r.data || [];
            },

            selectRepair(repair) {
                this.repairForm.repair_id = repair.id;
                this.repairSearch = repair.ticket_number;
                this.repairResults = [];
            },

            async applyToRepair() {
                if (!await RepairBox.confirm(`Apply ₹${this.repairForm.amount} to this repair?`)) return;
                this.processing = true;
                const r = await RepairBox.ajax('/credit-notes/{{ $creditNote->id }}/apply-repair', 'POST', this.repairForm);
                this.processing = false;
                if (r.success !== false) {
                    RepairBox.toast(r.message || 'Applied to repair', 'success');
                    if (r.data?.redirect) window.location.href = r.data.redirect;
                    else location.reload();
                }
            },

            async searchInvoices() {
                if (this.invoiceSearch.length < 2) { this.invoiceResults = []; return; }
                const r = await RepairBox.ajax('/invoices?search=' + encodeURIComponent(this.invoiceSearch) + '&per_page=10');
                this.invoiceResults = r.data?.data || r.data || [];
            },

            selectInvoice(inv) {
                this.invoiceForm.invoice_id = inv.id;
                this.invoiceSearch = inv.invoice_number;
                this.invoiceResults = [];
            },

            async applyToInvoice() {
                if (!await RepairBox.confirm(`Apply ₹${this.invoiceForm.amount} to this invoice?`)) return;
                this.processing = true;
                const r = await RepairBox.ajax('/credit-notes/{{ $creditNote->id }}/apply-invoice', 'POST', this.invoiceForm);
                this.processing = false;
                if (r.success !== false) { RepairBox.toast(r.message || 'Applied to invoice', 'success'); location.reload(); }
            }
        };
    }
</script>
@endpush
