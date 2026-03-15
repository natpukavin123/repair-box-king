@extends('layouts.print')
@section('title', 'Credit Note - ' . $return->return_number)
@section('doc-type', 'Credit Note / Return')

@section('extra-styles')
    .title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 1px; color: #c00; }
    .section-title { font-weight: bold; font-size: 11px; text-transform: uppercase; color: #555; margin-top: 10px; margin-bottom: 4px; border-bottom: 1px solid #ddd; padding-bottom: 2px; }
    .refund-info { background: #f9f9f9; border: 1px solid #ddd; padding: 6px; margin-top: 8px; font-size: 11px; }
    .refund-info div { display: flex; justify-content: space-between; padding: 1px 0; }
    .refund-info .refund-total { font-weight: bold; color: #090; font-size: 13px; border-top: 1px solid #ccc; padding-top: 4px; margin-top: 4px; }
    .repair-summary { background: #f0f4ff; border: 1px solid #c0ccff; padding: 6px; margin-top: 10px; font-size: 11px; }
    .repair-summary .rs-title { font-weight: bold; font-size: 11px; text-align: center; text-transform: uppercase; color: #444; margin-bottom: 4px; }
    .repair-summary div { display: flex; justify-content: space-between; padding: 1px 0; }
    .repair-summary .rs-total { font-weight: bold; font-size: 12px; border-top: 1px solid #aac; padding-top: 3px; margin-top: 3px; }
    .return-status { text-align: center; margin-top: 6px; font-size: 11px; font-weight: bold; padding: 4px; border: 1px dashed; }
    .return-more-btn { display: block; margin: 12px auto 0; text-align: center; background: #f97316; color: #fff; font-size: 12px; font-weight: bold; padding: 8px 14px; border-radius: 6px; text-decoration: none; }
    @media print { .return-more-btn { display: none; } }
@endsection

@section('print-content')
    <div class="info">
        <div><span class="label">Return #:</span><span class="value">{{ $return->return_number }}</span></div>
        <div><span class="label">Against Repair:</span><span class="value">{{ $repair->ticket_number }}</span></div>
        <div><span class="label">Date:</span><span class="value">{{ $return->created_at->format('d/m/Y') }}</span></div>
        @if($return->customer)
        <div><span class="label">Customer:</span><span class="value">{{ $return->customer->name }}</span></div>
        <div><span class="label">Phone:</span><span class="value">{{ $return->customer->mobile_number }}</span></div>
        @endif
        <div><span class="label">Device:</span><span class="value">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
    </div>

    <div class="section-title">Reason</div>
    <p style="font-size:11px; margin-bottom:8px; white-space:pre-line;">{{ $return->reason }}</p>

    @php
        $partItems = $return->items->where('item_type', 'part');
        $serviceItems = $return->items->where('item_type', 'service');
    @endphp

    @if($partItems->count())
    <div class="section-title">Returned Parts</div>
    <table>
        <thead><tr><th>Part</th><th>Qty</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($partItems as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>₹{{ number_format($item->return_amount, 2) }}</td>
            </tr>
            @if($item->reason)
            <tr><td colspan="3" style="font-size:10px; color:#666; padding:0 0 3px;">↳ {{ $item->reason }}</td></tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    @if($serviceItems->count())
    <div class="section-title">Returned Services</div>
    <table>
        <thead><tr><th>Service</th><th>Amount</th></tr></thead>
        <tbody>
            @foreach($serviceItems as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>₹{{ number_format($item->return_amount, 2) }}</td>
            </tr>
            @if($item->reason)
            <tr><td colspan="2" style="font-size:10px; color:#666; padding:0 0 3px;">↳ {{ $item->reason }}</td></tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="totals">
        <div class="grand"><span>Total Return:</span><span style="color:#c00;">₹{{ number_format($return->total_return_amount, 2) }}</span></div>
    </div>

    @if($return->status === 'refunded')
    <div class="refund-info">
        <div style="font-weight:bold; margin-bottom:4px; text-align:center; text-transform:uppercase; font-size:11px; color:#555;">Refund Details</div>
        <div><span>Method:</span><span>{{ ucfirst(str_replace('_', ' ', $return->refund_method)) }}</span></div>
        @if($return->refund_reference)
        <div><span>Reference:</span><span>{{ $return->refund_reference }}</span></div>
        @endif
        @if($return->refund_notes)
        <div><span>Notes:</span><span>{{ $return->refund_notes }}</span></div>
        @endif
        <div><span>Refunded On:</span><span>{{ $return->refunded_at->format('d/m/Y h:i A') }}</span></div>
        <div class="refund-total"><span>Refunded Amount:</span><span>₹{{ number_format($return->refund_amount, 2) }}</span></div>
    </div>
    @else
    <div style="text-align:center; margin-top:8px; font-size:11px; color:#c00; font-weight:bold;">
        ⚠ REFUND PENDING
    </div>
    @endif

    @php
        $repairPartsTotal    = $repair->parts->sum(fn($rp) => $rp->cost_price * $rp->quantity);
        $repairServicesTotal = $repair->repairServices->sum('customer_charge');
        $repairGrandTotal    = $repairPartsTotal + $repairServicesTotal + ($repair->service_charge ?? 0);
        $netAfterReturns     = $repairGrandTotal - $totalAlreadyReturned;
    @endphp

    <div class="repair-summary">
        <div class="rs-title">Original Repair: {{ $repair->ticket_number }}</div>
        <div><span>Parts:</span><span>₹{{ number_format($repairPartsTotal, 2) }}</span></div>
        <div><span>Services:</span><span>₹{{ number_format($repairServicesTotal, 2) }}</span></div>
        @if(($repair->service_charge ?? 0) > 0)
        <div><span>Service Charge:</span><span>₹{{ number_format($repair->service_charge, 2) }}</span></div>
        @endif
        <div class="rs-total"><span>Repair Total:</span><span>₹{{ number_format($repairGrandTotal, 2) }}</span></div>
        <div style="color:#c00;"><span>Total Returned:</span><span>₹{{ number_format($totalAlreadyReturned, 2) }}</span></div>
        <div style="font-weight:bold;"><span>Net (after returns):</span><span>₹{{ number_format($netAfterReturns, 2) }}</span></div>
    </div>

    @if($hasReturnableItems)
    <div class="return-status" style="color:#d97706; border-color:#d97706; background:#fffbeb;">
        PARTIAL RETURN — More items eligible
    </div>
    @else
    <div class="return-status" style="color:#059669; border-color:#059669; background:#ecfdf5;">
        FULLY RETURNED — All items returned
    </div>
    @endif

    @if($hasReturnableItems)
    <a href="/repairs/{{ $repair->id }}/returns/create" class="return-more-btn">↩ Return More Items</a>
    @endif
@endsection
