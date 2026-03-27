@extends('layouts.app')
@section('page-title', 'Create Credit Note')
@section('content-class', 'flex flex-col')

@section('content')
<div x-data="createCreditNote()" class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Create Credit Note</h2>
            <p class="text-sm text-gray-500 mt-0.5">
                Against
                @if($sourceType === 'invoice')
                    Invoice <strong>{{ $source->invoice_number }}</strong>
                    @if($source->customer) — {{ $source->customer->name }} @endif
                @else
                    Repair <strong>{{ $source->ticket_number }}</strong>
                    @if($source->customer) — {{ $source->customer->name }} @endif
                @endif
            </p>
        </div>
        <a href="{{ $sourceType === 'invoice' ? '/admin/invoices' : '/admin/repairs/' . $source->id }}" class="btn-secondary">← Back</a>
    </div>

    <!-- Select Items -->
    <div class="card">
        <div class="card-header"><h3 class="font-semibold text-gray-700">Select Items to Return</h3></div>
        <div class="card-body p-0">
            <table class="w-full">
                <thead>
                    <tr class="border-b bg-gray-50">
                        <th class="px-4 py-2 text-center w-12"></th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Item</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Type</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Orig Qty</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Already Credited</th>
                        <th class="px-4 py-2 text-center text-xs font-semibold text-gray-600">Return Qty</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Unit Price</th>
                        <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600">Return Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sourceType === 'invoice')
                        @foreach($source->items as $item)
                            @php
                                $credited = $creditedQuantities[$item->id] ?? 0;
                                $maxQty = $item->quantity - $credited;
                            @endphp
                            @if($maxQty > 0)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" x-model="selectedItems" value="{{ $item->id }}"
                                        @change="toggleItem({{ $item->id }}, '{{ addslashes($item->item_name) }}', 'product', {{ $item->price }}, {{ $maxQty }}, {{ $item->id }})"
                                        class="rounded border-gray-300 text-primary-600">
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $item->item_name }}</td>
                                <td class="px-4 py-3"><span class="badge badge-info text-xs">{{ ucfirst($item->item_type) }}</span></td>
                                <td class="px-4 py-3 text-center text-sm">{{ $item->quantity }}</td>
                                <td class="px-4 py-3 text-center text-sm text-amber-600">{{ $credited }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" x-model.number="itemQuantities[{{ $item->id }}]"
                                        min="1" max="{{ $maxQty }}" class="form-input-custom w-16 text-center text-sm"
                                        @input="recalcItem({{ $item->id }}, {{ $item->price }})">
                                </td>
                                <td class="px-4 py-3 text-right text-sm">₹{{ number_format($item->price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold" x-text="'₹' + (itemTotals[{{ $item->id }}] || 0).toFixed(2)"></td>
                            </tr>
                            @endif
                        @endforeach
                    @else
                        {{-- Repair Parts --}}
                        @foreach($source->parts as $rp)
                            @php
                                $key = 'part_' . $rp->id;
                                $credited = $creditedQuantities[$key] ?? 0;
                                $maxQty = $rp->quantity - $credited;
                            @endphp
                            @if($maxQty > 0)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" x-model="selectedItems" value="part_{{ $rp->id }}"
                                        @change="toggleItem('part_{{ $rp->id }}', '{{ addslashes($rp->part?->name ?? 'Part') }}', 'part', {{ $rp->cost_price }}, {{ $maxQty }}, {{ $rp->id }})"
                                        class="rounded border-gray-300 text-primary-600">
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $rp->part?->name ?? 'Part' }}</td>
                                <td class="px-4 py-3"><span class="badge badge-warning text-xs">Part</span></td>
                                <td class="px-4 py-3 text-center text-sm">{{ $rp->quantity }}</td>
                                <td class="px-4 py-3 text-center text-sm text-amber-600">{{ $credited }}</td>
                                <td class="px-4 py-3 text-center">
                                    <input type="number" x-model.number="itemQuantities['part_{{ $rp->id }}']"
                                        min="1" max="{{ $maxQty }}" class="form-input-custom w-16 text-center text-sm"
                                        @input="recalcItem('part_{{ $rp->id }}', {{ $rp->cost_price }})">
                                </td>
                                <td class="px-4 py-3 text-right text-sm">₹{{ number_format($rp->cost_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold" x-text="'₹' + (itemTotals['part_{{ $rp->id }}'] || 0).toFixed(2)"></td>
                            </tr>
                            @endif
                        @endforeach
                        {{-- Repair Services --}}
                        @foreach($source->repairServices as $svc)
                            @php
                                $key = 'service_' . $svc->id;
                                $credited = $creditedQuantities[$key] ?? 0;
                            @endphp
                            @if(!$credited)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox" x-model="selectedItems" value="service_{{ $svc->id }}"
                                        @change="toggleItem('service_{{ $svc->id }}', '{{ addslashes($svc->service_type_name ?? 'Service') }}', 'service', {{ $svc->customer_charge }}, 1, {{ $svc->id }})"
                                        class="rounded border-gray-300 text-primary-600">
                                </td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $svc->service_type_name ?? 'Service' }}</td>
                                <td class="px-4 py-3"><span class="badge badge-success text-xs">Service</span></td>
                                <td class="px-4 py-3 text-center text-sm">1</td>
                                <td class="px-4 py-3 text-center text-sm text-amber-600">{{ $credited ? 1 : 0 }}</td>
                                <td class="px-4 py-3 text-center text-sm">1</td>
                                <td class="px-4 py-3 text-right text-sm">₹{{ number_format($svc->customer_charge, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold" x-text="'₹' + (itemTotals['service_{{ $svc->id }}'] || 0).toFixed(2)"></td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reason & Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card">
            <div class="card-body space-y-4">
                <div>
                    <label class="form-label">Reason for Return *</label>
                    <textarea x-model="form.reason" rows="3" class="form-input-custom" placeholder="Describe the reason for this credit note..."></textarea>
                </div>
                <div>
                    <label class="form-label">Additional Notes</label>
                    <textarea x-model="form.notes" rows="2" class="form-input-custom" placeholder="Optional internal notes"></textarea>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="card border-2 border-primary-200">
            <div class="card-header bg-primary-50"><h3 class="font-semibold text-primary-800 text-sm">Credit Note Summary</h3></div>
            <div class="card-body space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Items selected</span>
                    <span class="font-semibold" x-text="selectedItems.length"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Total credit amount</span>
                    <span class="font-bold text-lg text-primary-600" x-text="'₹' + grandTotal.toFixed(2)"></span>
                </div>
                <hr>
                <button @click="submit()" class="btn-primary w-full" :disabled="saving || selectedItems.length === 0 || !form.reason">
                    <span x-show="saving" class="spinner mr-1" style="width:16px;height:16px;border-width:2px"></span>
                    Create Credit Note (Draft)
                </button>
                <p class="text-xs text-gray-400 text-center">Credit note will be created as draft. An admin must approve it before refunds can be processed.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createCreditNote() {
    return {
        selectedItems: [],
        itemQuantities: {},
        itemTotals: {},
        itemData: {},
        form: { reason: '', notes: '' },
        saving: false,

        get grandTotal() {
            return Object.values(this.itemTotals).reduce((s, v) => s + (v || 0), 0);
        },

        toggleItem(id, name, type, price, maxQty, originalId) {
            if (this.selectedItems.includes(String(id))) {
                if (!this.itemQuantities[id]) this.itemQuantities[id] = 1;
                this.itemTotals[id] = (this.itemQuantities[id] || 1) * price;
                this.itemData[id] = { item_name: name, item_type: type, unit_price: price, original_item_id: originalId };
            } else {
                delete this.itemTotals[id];
                delete this.itemData[id];
            }
        },

        recalcItem(id, price) {
            this.itemTotals[id] = (this.itemQuantities[id] || 1) * price;
        },

        async submit() {
            const items = this.selectedItems.map(id => ({
                item_type: this.itemData[id]?.item_type || 'product',
                item_name: this.itemData[id]?.item_name || '',
                quantity: this.itemQuantities[id] || 1,
                unit_price: this.itemData[id]?.unit_price || 0,
                total: this.itemTotals[id] || 0,
                original_item_id: this.itemData[id]?.original_item_id || null,
            }));

            this.saving = true;
            const r = await RepairBox.ajax('/admin/credit-notes', 'POST', {
                source_type: '{{ $sourceType }}',
                source_id: {{ $source->id }},
                reason: this.form.reason,
                notes: this.form.notes,
                items: items,
            });
            this.saving = false;

            if (r.success !== false && r.data?.redirect) {
                RepairBox.toast(r.message || 'Credit note created', 'success');
                window.location.href = r.data.redirect;
            }
        }
    };
}
</script>
@endpush
