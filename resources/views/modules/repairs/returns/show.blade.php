@extends('layouts.app')
@section('page-title', 'Return ' . $return->return_number)

@section('content')
<div>

    <!-- Breadcrumb -->
    <div class="mb-5">
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="/repairs" class="text-primary-600 hover:text-primary-800">Repairs</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="/repairs/{{ $repair->id }}" class="text-primary-600 hover:text-primary-800">{{ $repair->ticket_number }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-500">Return {{ $return->return_number }}</span>
        </div>
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800">{{ $return->return_number }}</h2>
                @php
                    $statusClass = match($return->status) {
                        'draft' => 'bg-gray-100 text-gray-700',
                        'confirmed' => 'bg-amber-100 text-amber-700',
                        'refunded' => 'bg-green-100 text-green-700',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ ucfirst($return->status) }}</span>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <a href="/repairs/{{ $repair->id }}/returns/{{ $return->id }}/invoice" target="_blank" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print Return
                </a>
                @php
                    $returnedParts = [];
                    $returnedServices = [];
                    foreach ($repair->repairReturns as $ret) {
                        foreach ($ret->items as $item) {
                            if ($item->item_type === 'part' && $item->repair_part_id) {
                                $returnedParts[$item->repair_part_id] = ($returnedParts[$item->repair_part_id] ?? 0) + $item->quantity;
                            }
                            if ($item->item_type === 'service' && $item->repair_service_id) {
                                $returnedServices[$item->repair_service_id] = true;
                            }
                        }
                    }
                    $hasReturnableParts = $repair->parts->contains(fn($rp) => $rp->quantity - ($returnedParts[$rp->id] ?? 0) > 0);
                    $hasReturnableServices = $repair->repairServices->contains(fn($svc) => !isset($returnedServices[$svc->id]));
                    $hasReturnableItems = $hasReturnableParts || $hasReturnableServices;
                @endphp
                @if($hasReturnableItems)
                <a href="/repairs/{{ $repair->id }}/returns/create" class="btn-secondary text-sm inline-flex items-center gap-1.5 !border-orange-300 !text-orange-700 hover:!bg-orange-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Return More Items
                </a>
                @endif
                <a href="/repairs/{{ $repair->id }}" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Back to Repair
                </a>
            </div>
        </div>
    </div>

    <!-- ===== CREDIT NOTE LINK ===== -->
    @if($return->creditNote)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-blue-800 text-base">Credit Note {{ $return->creditNote->credit_note_number }}</h4>
                    <p class="text-sm text-blue-600">
                        Amount: <strong>₹{{ number_format($return->creditNote->total_amount, 2) }}</strong>
                        &bull; Status:
                        @php
                            $cnStatusClass = match($return->creditNote->status) {
                                'draft' => 'text-gray-600',
                                'approved' => 'text-blue-600',
                                'partially_refunded' => 'text-amber-600',
                                'fully_refunded' => 'text-green-600',
                                'cancelled' => 'text-red-600',
                                default => 'text-gray-600',
                            };
                        @endphp
                        <strong class="{{ $cnStatusClass }}">{{ ucwords(str_replace('_', ' ', $return->creditNote->status)) }}</strong>
                    </p>
                </div>
            </div>
            <a href="/credit-notes/{{ $return->creditNote->id }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                View Credit Note
            </a>
        </div>
    </div>
    @endif

    <!-- ===== INFO CARDS ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
        <!-- Return Info -->
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Return Details</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Return #</span><span class="font-medium">{{ $return->return_number }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Repair #</span><a href="/repairs/{{ $repair->id }}" class="font-medium text-primary-600">{{ $repair->ticket_number }}</a></div>
                <div class="flex justify-between"><span class="text-gray-500">Customer</span><span class="font-medium">{{ $return->customer?->name ?? 'Walk-in' }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Device</span><span class="font-medium">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Created</span><span class="font-medium">{{ $return->created_at->format('d M Y, h:i A') }}</span></div>
                @if($return->creator)
                <div class="flex justify-between"><span class="text-gray-500">Created By</span><span class="font-medium">{{ $return->creator->name }}</span></div>
                @endif
            </div>
        </div>

        <!-- Reason -->
        <div class="bg-white rounded-xl border shadow-sm p-5">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Return Reason</h3>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $return->reason }}</p>
        </div>

        <!-- Financial Summary -->
        <div class="bg-white rounded-xl border shadow-sm p-5 border-l-4 border-l-red-500">
            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">Total Return</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Return Amount</span><span class="text-xl font-bold text-red-600">₹{{ number_format($return->total_return_amount, 2) }}</span></div>
                @if($return->creditNote)
                    @if($return->creditNote->status === 'fully_refunded')
                    <div class="flex justify-between"><span class="text-gray-500">Resolution</span><span class="font-bold text-green-600">Fully Resolved</span></div>
                    @elseif($return->creditNote->refunded_amount > 0)
                    <div class="flex justify-between"><span class="text-gray-500">Resolved</span><span class="font-semibold text-amber-600">₹{{ number_format($return->creditNote->refunded_amount, 2) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Remaining</span><span class="font-semibold text-red-600">₹{{ number_format($return->creditNote->remainingRefundable(), 2) }}</span></div>
                    @else
                    <div class="mt-2 text-xs text-amber-600 font-semibold">⚠ Pending resolution via Credit Note</div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- ===== RETURNED ITEMS ===== -->
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Returned Items ({{ $return->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Unit Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Return Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($return->items as $i => $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->item_type === 'part' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ ucfirst($item->item_type) }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ $item->item_name }}</td>
                        <td class="px-5 py-3 text-center text-sm">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold text-red-600">₹{{ number_format($item->return_amount, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $item->reason ?: '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-5 py-3 text-sm" colspan="5">Total Return Amount</td>
                        <td class="px-5 py-3 text-right text-sm text-red-600">₹{{ number_format($return->total_return_amount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
