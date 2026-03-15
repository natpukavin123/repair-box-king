@extends('layouts.invoice')
@section('title', 'Repair Ticket – ' . $repair->ticket_number)
@section('invoice-title', 'Repair Receipt')

@php
    $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
@endphp

@section('invoice-content')
    {{-- ══ INFO GRID ══ --}}
    <div class="info-grid">
        <div class="info-col">
            <div class="col-hdr">Customer Detail</div>
            <div class="col-body">
                @if($repair->customer)
                <div class="irow"><span class="lb">Name</span><span class="vl">{{ $repair->customer->name }}</span></div>
                <div class="irow"><span class="lb">Address</span><span class="vl">{{ $repair->customer->address ?? '-' }}</span></div>
                <div class="irow"><span class="lb">Phone</span><span class="vl">{{ $repair->customer->mobile_number ?? '-' }}</span></div>
                @else
                <div class="irow"><span class="lb">Name</span><span class="vl">Walk-in Customer</span></div>
                @endif
            </div>
        </div>

        <div class="info-col" style="max-width:215px;">
            <div class="col-hdr">Repair Detail</div>
            <div class="col-body">
                <div class="irow"><span class="lb">Ticket No.</span><span class="vl inv-num">{{ $repair->ticket_number }}</span></div>
                <div style="height:6px;"></div>
                <div class="irow"><span class="lb">Date</span><span class="vl">{{ $repair->created_at->format('d M Y, h:i A') }}</span></div>
                <div class="irow"><span class="lb">Device</span><span class="vl">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
                @if($repair->imei)
                <div class="irow"><span class="lb">IMEI</span><span class="vl">{{ $repair->imei }}</span></div>
                @endif
                <div class="irow"><span class="lb">Status</span><span class="vl">{{ ucfirst(str_replace('_',' ',$repair->status)) }}</span></div>
                @if($repair->expected_delivery_date)
                <div class="irow"><span class="lb">Est. Delivery</span><span class="vl">{{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d M Y') }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ PROBLEM DESCRIPTION ══ --}}
    @if($repair->problem_description)
    <div style="border-bottom: 2px solid #111; padding: 10px 14px; font-size: 10.5px;">
        <strong style="font-size: 8.5px; letter-spacing: 1px; text-transform: uppercase; color: #666;">Problem Description:</strong>
        <div style="margin-top: 4px; white-space: pre-line; color: #333; line-height: 1.6;">{{ $repair->problem_description }}</div>
    </div>
    @endif

    {{-- ══ ESTIMATED COST ══ --}}
    <div style="border-bottom: 2px solid #111; padding: 16px 14px; text-align: center;">
        <div style="font-size: 9px; color: #666; text-transform: uppercase; letter-spacing: 1.5px;">Estimated Repair Cost</div>
        <div style="font-family: 'Playfair Display', serif; font-size: 30px; font-weight: 900; color: #111; margin-top: 6px;">₹{{ number_format($repair->estimated_cost, 2) }}</div>
    </div>

    {{-- ══ BOTTOM SECTION ══ --}}
    <div class="bottom">
        {{-- LEFT: Tracking + advance payments --}}
        <div class="b-left">
            <div class="s-hdr">Tracking</div>
            <div class="s-body" style="text-align: center; padding: 14px 13px;">
                <div style="font-size: 10px; color: #666;">Track your repair status online using this ID:</div>
                <div style="font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 900; letter-spacing: 3px; margin-top: 6px; color: #111;">{{ $repair->tracking_id }}</div>
            </div>

            @if($repair->payments->where('direction','IN')->count())
            <div class="s-hdr mt4">Advance Payment</div>
            <div class="s-body">
                @foreach($repair->payments->where('direction', 'IN')->where('payment_type', 'advance') as $pay)
                <div class="pay-item">
                    <span>{{ ucfirst($pay->payment_method) }}</span>
                    <span class="p-in">+₹{{ number_format($pay->amount,2) }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- RIGHT: Notes + Signature --}}
        <div class="b-right">
            <div class="s-hdr">Important Notes</div>
            <div class="s-body" style="font-size: 9.5px; color: #555; line-height: 1.8;">
                <p>• Please keep this receipt for tracking your repair.</p>
                <p>• Estimated cost may vary upon diagnosis.</p>
                <p>• Unclaimed devices after 30 days are not our responsibility.</p>
                <p>• Data backup is the customer's responsibility.</p>
            </div>

            <div class="sign-box">
                <div class="sign-for">For {{ $shopName }}</div>
                <div class="sign-line"></div>
                <div class="sign-auth">Authorised Signatory</div>
            </div>
        </div>
    </div>
@endsection

@section('footer-extra')
    <strong>Tracking ID:</strong> {{ $repair->tracking_id ?? '—' }}
    &nbsp;|&nbsp;
@endsection