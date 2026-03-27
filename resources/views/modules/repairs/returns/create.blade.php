@extends('layouts.app')
@section('page-title', 'Return - ' . $repair->ticket_number)

@section('content')
<div x-data="returnCreate()" x-init="init()">

    <!-- Breadcrumb -->
    <div class="mb-5">
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="/admin/repairs" class="text-primary-600 hover:text-primary-800">Repairs</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="/admin/repairs/{{ $repair->id }}" class="text-primary-600 hover:text-primary-800">{{ $repair->ticket_number }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-500">Create Return</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Create Return Order</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $repair->ticket_number }} &mdash;
                    {{ $repair->customer?->name ?? 'Walk-in' }} &mdash;
                    {{ $repair->device_brand }} {{ $repair->device_model }}
                </p>
            </div>
            <a href="/admin/repairs/{{ $repair->id }}" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back
            </a>
        </div>
    </div>

    <!-- ===== PREVIOUS RETURNS ===== -->
    @if($repair->repairReturns->count() > 0)
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-5">
        <h3 class="text-sm font-bold text-orange-700 uppercase tracking-wide mb-2 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Previous Returns ({{ $repair->repairReturns->count() }})
        </h3>
        <div class="space-y-1.5">
            @foreach($repair->repairReturns as $prevReturn)
            <a href="/admin/repairs/{{ $repair->id }}/returns/{{ $prevReturn->id }}" class="flex items-center justify-between bg-white rounded-lg px-3 py-2 text-sm hover:bg-orange-100/50 transition border border-orange-100">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-orange-600">{{ $prevReturn->return_number }}</span>
                    @php
                        $retStatusClass = match($prevReturn->status) {
                            'refunded' => 'bg-green-100 text-green-700',
                            'confirmed' => 'bg-blue-100 text-blue-700',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded {{ $retStatusClass }}">{{ $prevReturn->status }}</span>
                </div>
                <span class="font-medium text-gray-700">₹{{ number_format($prevReturn->total_return_amount, 2) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @if(!$hasReturnableItems)
    <!-- All items fully returned -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-5 text-center">
        <svg class="w-12 h-12 text-green-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <h3 class="text-lg font-bold text-green-700 mb-1">All Items Fully Returned</h3>
        <p class="text-sm text-green-600">All parts and services for this repair have already been returned. No further returns can be created.</p>
        <a href="/admin/repairs/{{ $repair->id }}" class="inline-flex items-center gap-1.5 mt-4 btn-secondary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Repair
        </a>
    </div>
    @else

    <!-- ===== SELECT PARTS TO RETURN ===== -->
    @if($repair->parts->count() > 0)
    <div class="bg-white rounded-xl border shadow-sm mb-5">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Parts Used in Repair
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-10">
                            <input type="checkbox" @click="toggleAllParts($event.target.checked)" class="rounded border-gray-300">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Part</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Bought Qty</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Already Returned</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Return Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Return Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($repair->parts as $rp)
                    @php
                        $alreadyReturned = $returnedParts[$rp->id] ?? 0;
                        $maxQty = $rp->quantity - $alreadyReturned;
                    @endphp
                    @if($maxQty > 0)
                    <tr class="hover:bg-gray-50/50" :class="isPartSelected({{ $rp->id }}) ? 'bg-blue-50/50' : ''">
                        <td class="px-5 py-3">
                            <input type="checkbox" :checked="isPartSelected({{ $rp->id }})" @change="togglePart({{ $rp->id }}, $event.target.checked)" class="rounded border-gray-300">
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-800">{{ $rp->part?->name ?? 'Part' }}</div>
                            @if($rp->part?->sku)<div class="text-xs text-gray-400">SKU: {{ $rp->part->sku }}</div>@endif
                        </td>
                        <td class="px-5 py-3 text-center text-sm">{{ $rp->quantity }}</td>
                        <td class="px-5 py-3 text-center text-sm {{ $alreadyReturned > 0 ? 'text-orange-500 font-medium' : 'text-gray-400' }}">{{ $alreadyReturned }}</td>
                        <td class="px-5 py-3 text-center">
                            <input type="number" min="1" max="{{ $maxQty }}" :disabled="!isPartSelected({{ $rp->id }})"
                                x-model.number="getPartItem({{ $rp->id }}).quantity"
                                @input="recalcPartAmount({{ $rp->id }})"
                                class="w-16 text-center text-sm border rounded-lg px-2 py-1 disabled:opacity-40">
                        </td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($rp->cost_price, 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            <input type="number" step="0.01" min="0" :disabled="!isPartSelected({{ $rp->id }})"
                                x-model.number="getPartItem({{ $rp->id }}).return_amount"
                                class="w-24 text-right text-sm border rounded-lg px-2 py-1 disabled:opacity-40">
                        </td>
                        <td class="px-5 py-3">
                            <input type="text" maxlength="500" :disabled="!isPartSelected({{ $rp->id }})"
                                x-model="getPartItem({{ $rp->id }}).reason"
                                placeholder="Optional reason"
                                class="w-full text-sm border rounded-lg px-2 py-1 disabled:opacity-40">
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ===== SELECT SERVICES TO RETURN ===== -->
    @if($repair->repairServices->count() > 0)
    <div class="bg-white rounded-xl border shadow-sm mb-5">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Services in Repair
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-10">
                            <input type="checkbox" @click="toggleAllServices($event.target.checked)" class="rounded border-gray-300">
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Customer Charge</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Return Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($repair->repairServices as $svc)
                    @if(!isset($returnedServices[$svc->id]))
                    <tr class="hover:bg-gray-50/50" :class="isServiceSelected({{ $svc->id }}) ? 'bg-purple-50/50' : ''">
                        <td class="px-5 py-3">
                            <input type="checkbox" :checked="isServiceSelected({{ $svc->id }})" @change="toggleService({{ $svc->id }}, $event.target.checked)" class="rounded border-gray-300">
                        </td>
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-800">{{ $svc->service_type_name }}</div>
                            @if($svc->reference_no)<div class="text-xs text-gray-400">Ref: {{ $svc->reference_no }}</div>@endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ $svc->vendor?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $svcStatusClass = match($svc->status) {
                                    'completed' => 'bg-green-100 text-green-700',
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $svcStatusClass }}">{{ ucfirst(str_replace('_', ' ', $svc->status)) }}</span>
                        </td>
                        <td class="px-5 py-3 text-right text-sm font-medium">₹{{ number_format($svc->customer_charge, 2) }}</td>
                        <td class="px-5 py-3 text-right">
                            <input type="number" step="0.01" min="0" :disabled="!isServiceSelected({{ $svc->id }})"
                                x-model.number="getServiceItem({{ $svc->id }}).return_amount"
                                class="w-24 text-right text-sm border rounded-lg px-2 py-1 disabled:opacity-40">
                        </td>
                        <td class="px-5 py-3">
                            <input type="text" maxlength="500" :disabled="!isServiceSelected({{ $svc->id }})"
                                x-model="getServiceItem({{ $svc->id }}).reason"
                                placeholder="Optional reason"
                                class="w-full text-sm border rounded-lg px-2 py-1 disabled:opacity-40">
                        </td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- ===== RETURN REASON & SUMMARY ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <!-- Reason -->
        <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Return Reason</h3>
            <textarea x-model="returnReason" rows="3" placeholder="Describe the reason for this return..." class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide mb-3">Return Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Parts Return</span>
                    <span class="font-medium" x-text="'₹' + partsReturnTotal().toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Services Return</span>
                    <span class="font-medium" x-text="'₹' + servicesReturnTotal().toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-sm font-bold border-t-2 pt-2">
                    <span class="text-gray-800">Total Return</span>
                    <span class="text-red-600" x-text="'₹' + totalReturn().toFixed(2)"></span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Selected Items</span>
                    <span x-text="selectedCount() + ' items'"></span>
                </div>
            </div>
            <button @click="submitReturn()"
                :disabled="saving || selectedCount() === 0 || !returnReason.trim()"
                class="mt-4 w-full btn-primary text-sm py-2.5 disabled:opacity-40">
                <span x-show="!saving">Confirm Return</span>
                <span x-show="saving">Processing...</span>
            </button>
        </div>
    </div>

    @endif {{-- hasReturnableItems --}}

</div>
@endsection

@push('scripts')
<script>
function returnCreate() {
    return {
        saving: false,
        returnReason: '',
        partItems: {},
        serviceItems: {},

        init() {
            // Initialize part items from server data
            @foreach($repair->parts as $rp)
            @php $maxQty = $rp->quantity - ($returnedParts[$rp->id] ?? 0); @endphp
            @if($maxQty > 0)
            this.partItems[{{ $rp->id }}] = {
                selected: false,
                type: 'part',
                id: {{ $rp->id }},
                quantity: {{ $maxQty }},
                maxQty: {{ $maxQty }},
                unitPrice: {{ $rp->cost_price }},
                return_amount: {{ $rp->cost_price * $maxQty }},
                reason: '',
            };
            @endif
            @endforeach

            // Initialize service items
            @foreach($repair->repairServices as $svc)
            @if(!isset($returnedServices[$svc->id]))
            this.serviceItems[{{ $svc->id }}] = {
                selected: false,
                type: 'service',
                id: {{ $svc->id }},
                quantity: 1,
                unitPrice: {{ $svc->customer_charge }},
                return_amount: {{ $svc->customer_charge }},
                reason: '',
            };
            @endif
            @endforeach
        },

        isPartSelected(id) { return this.partItems[id]?.selected ?? false; },
        isServiceSelected(id) { return this.serviceItems[id]?.selected ?? false; },
        getPartItem(id) { return this.partItems[id] || {}; },
        getServiceItem(id) { return this.serviceItems[id] || {}; },

        togglePart(id, checked) {
            if (this.partItems[id]) this.partItems[id].selected = checked;
        },
        toggleService(id, checked) {
            if (this.serviceItems[id]) this.serviceItems[id].selected = checked;
        },
        toggleAllParts(checked) {
            Object.values(this.partItems).forEach(p => p.selected = checked);
        },
        toggleAllServices(checked) {
            Object.values(this.serviceItems).forEach(s => s.selected = checked);
        },
        recalcPartAmount(id) {
            const p = this.partItems[id];
            if (p) {
                p.quantity = Math.min(Math.max(1, p.quantity), p.maxQty);
                p.return_amount = +(p.unitPrice * p.quantity).toFixed(2);
            }
        },

        partsReturnTotal() {
            return Object.values(this.partItems).filter(p => p.selected).reduce((s, p) => s + (Number(p.return_amount) || 0), 0);
        },
        servicesReturnTotal() {
            return Object.values(this.serviceItems).filter(s => s.selected).reduce((t, s) => t + (Number(s.return_amount) || 0), 0);
        },
        totalReturn() { return this.partsReturnTotal() + this.servicesReturnTotal(); },
        selectedCount() {
            return Object.values(this.partItems).filter(p => p.selected).length + Object.values(this.serviceItems).filter(s => s.selected).length;
        },

        async submitReturn() {
            if (this.selectedCount() === 0) return RepairBox.toast('Select at least one item to return', 'error');
            if (!this.returnReason.trim()) return RepairBox.toast('Please provide a return reason', 'error');

            this.saving = true;
            const items = [];
            Object.values(this.partItems).filter(p => p.selected).forEach(p => {
                items.push({ type: 'part', id: p.id, quantity: p.quantity, return_amount: Number(p.return_amount), reason: p.reason });
            });
            Object.values(this.serviceItems).filter(s => s.selected).forEach(s => {
                items.push({ type: 'service', id: s.id, quantity: 1, return_amount: Number(s.return_amount), reason: s.reason });
            });

            const r = await RepairBox.ajax('/admin/repairs/{{ $repair->id }}/returns', 'POST', { reason: this.returnReason, items });
            this.saving = false;
            if (r.data?.redirect) {
                window.location.href = r.data.redirect;
            }
        },
    };
}
</script>
@endpush
