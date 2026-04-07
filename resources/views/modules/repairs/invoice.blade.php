@php
    $printKeys = [
        'headerTitleEn'=>'repair_invoice_header_title_en', 'headerTitleTa'=>'repair_invoice_header_title_ta',
        'shopNameTa'=>'receipt_shop_name_ta', 'shopSloganTa'=>'receipt_shop_slogan_ta',
        'shopAddressTa'=>'receipt_shop_address_ta',
        'signLabelEn'=>'receipt_sign_label_en', 'signLabelTa'=>'receipt_sign_label_ta',
    ];
    $printDefaults = [
        'headerTitleEn'=>'Repair Invoice', 'headerTitleTa'=>'பழுதுபார்ப்பு விலைப்பட்டியல்',
    ];
    require_once resource_path('views/partials/print-a4-vars.php');

    $shopUpiId   = \App\Models\Setting::getValue('upi_id', '');

    // Layout variables
    $pageTitle      = 'Repair Invoice – ' . $repair->ticket_number;
    $backUrl        = url('/admin/repairs');
    $printBtnLabel  = 'Print Invoice';
    $docNumber      = $repair->ticket_number;
    $docDate        = $repair->created_at->format('d M Y');

    // Line items
    $lineItems = collect();
    foreach ($repair->parts as $part) {
        $mrp = $part->product
            ? (float) ($part->product->mrp ?? $part->cost_price)
            : ($part->part ? (float) ($part->part->selling_price ?? $part->cost_price) : (float) $part->cost_price);
        $lineItems->push([
            'name'   => $part->part ? $part->part->name : ($part->product ? $part->product->name : 'Part'),
            'serial' => $part->imei ?? null,
            'qty'    => (int) $part->quantity,
            'mrp'    => $mrp,
            'rate'   => (float) $part->cost_price,
            'total'  => (float) $part->cost_price * (int) $part->quantity,
        ]);
    }
    foreach ($repair->repairServices as $svc) {
        $lineItems->push([
            'name'   => $svc->service_type_name,
            'serial' => null,
            'qty'    => 1,
            'mrp'    => (float) $svc->customer_charge,
            'rate'   => (float) $svc->customer_charge,
            'total'  => (float) $svc->customer_charge,
        ]);
    }
    if (($repair->service_charge ?? 0) > 0) {
        $lineItems->push([
            'name'   => 'Service Charge',
            'serial' => null,
            'qty'    => 1,
            'mrp'    => (float) $repair->service_charge,
            'rate'   => (float) $repair->service_charge,
            'total'  => (float) $repair->service_charge,
        ]);
    }

    $grandTotal  = $lineItems->sum('total');
    $totalQty    = $lineItems->sum('qty');
    $totalPaidIn = $repair->payments->where('direction','IN')->sum('amount');
    $balanceDue  = max(0, $grandTotal - $totalPaidIn);
    $amtWords    = numWords((float) $grandTotal);
    $amtWordsTa  = numWordsTa((float) $grandTotal);
    $emptyRows   = max(0, 6 - $lineItems->count());
@endphp

@extends('layouts.print-a4')

@section('printContent')
            <!-- Info -->
            <div class="inv-info">
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Customer Detail" data-ta="வாடிக்கையாளர் விவரம்">{{ $defaultLang === 'ta' ? 'வாடிக்கையாளர் விவரம்' : 'Customer Detail' }}</div>
                    <div class="inf-val" data-en="{{ e($repair->customer?->name ?? 'Walk-in Customer') }}" data-ta="{{ e($repair->customer?->name ?? 'நடை வாடிக்கையாளர்') }}">{{ $repair->customer?->name ?? ($defaultLang === 'ta' ? 'நடை வாடிக்கையாளர்' : 'Walk-in Customer') }}</div>
                    @if($repair->customer)
                    <div class="inf-sub">
                        @if($repair->customer->mobile_number)&#128222; {{ $repair->customer->mobile_number }}@endif
                        @if($repair->customer->address)<br>{{ $repair->customer->address }}@endif
                    </div>
                    @endif
                </div>
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Invoice Detail" data-ta="விலைப்பட்டியல் விவரம்">{{ $defaultLang === 'ta' ? 'விலைப்பட்டியல் விவரம்' : 'Invoice Detail' }}</div>
                    <div class="inf-sub">
                        <span data-en="Date" data-ta="தேதி">{{ $defaultLang === 'ta' ? 'தேதி' : 'Date' }}</span>: {{ $repair->created_at->format('d M Y, g:i A') }}<br>
                        <span data-en="Device" data-ta="சாதனம்">{{ $defaultLang === 'ta' ? 'சாதனம்' : 'Device' }}</span>: {{ $repair->device_brand }} {{ $repair->device_model }}<br>
                        @if($repair->imei)IMEI: {{ $repair->imei }}<br>@endif
                        <span data-en="Ticket" data-ta="டிக்கெட்">{{ $defaultLang === 'ta' ? 'டிக்கெட்' : 'Ticket' }}</span>: {{ $repair->ticket_number }}
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="inv-tbl-wrap">
                <table class="inv-tbl">
                    <thead><tr>
                        <th style="width:18px;">#</th>
                        <th class="tl" data-en="Product / Service" data-ta="பொருள் / சேவை">{{ $defaultLang === 'ta' ? 'பொருள் / சேவை' : 'Product / Service' }}</th>
                        <th style="width:24px;" data-en="Qty" data-ta="எண்.">{{ $defaultLang === 'ta' ? 'எண்.' : 'Qty' }}</th>
                        <th style="width:46px;" class="tr" data-en="MRP" data-ta="அதிகபட்ச விலை">{{ $defaultLang === 'ta' ? 'அதிகபட்ச விலை' : 'MRP' }}</th>
                        <th style="width:46px;" class="tr" data-en="Price" data-ta="விலை">{{ $defaultLang === 'ta' ? 'விலை' : 'Price' }}</th>
                        <th style="width:58px;" class="tr" data-en="Amount" data-ta="தொகை">{{ $defaultLang === 'ta' ? 'தொகை' : 'Amount' }}</th>
                    </tr></thead>
                    <tbody>
                        @foreach($lineItems as $idx => $item)
                        <tr>
                            <td class="tc">{{ $idx+1 }}</td>
                            <td>{{ $item['name'] }}@if($item['serial'])<div class="serial-sub">IMEI: {{ $item['serial'] }}</div>@endif</td>
                            <td class="tc">{{ $item['qty'] }}</td>
                            <td class="tr" style="color:#000;font-weight:500;">@if($item['mrp'] > $item['rate']){{ number_format($item['mrp'],2) }}@else&mdash;@endif</td>
                            <td class="tr" style="font-weight:600;">{{ number_format($item['rate'],2) }}</td>
                            <td class="tr" style="font-weight:600;">{{ number_format($item['total'],2) }}</td>
                        </tr>
                        @endforeach
                        @for($e=0;$e<$emptyRows;$e++)
                        <tr class="erow"><td></td><td></td><td></td><td></td><td></td><td></td></tr>
                        @endfor
                    </tbody>
                    <tfoot><tr>
                        <td colspan="2" class="tr" style="letter-spacing:1px;font-size:7px;" data-en="TOTAL" data-ta="மொத்தம்">{{ $defaultLang === 'ta' ? 'மொத்தம்' : 'TOTAL' }}</td>
                        <td class="tc">{{ number_format($totalQty) }}</td>
                        <td></td>
                        <td></td>
                        <td class="tr">{{ number_format($grandTotal,2) }}</td>
                    </tr></tfoot>
                </table>
            </div>

            <!-- Bottom -->
            <div class="inv-bottom">
                <div class="inv-bl">
                    <div>
                        <div class="sec-lbl" data-en="Amount in Words" data-ta="தொகை வார்த்தைகளில்">{{ $defaultLang === 'ta' ? 'தொகை வார்த்தைகளில்' : 'Amount in Words' }}</div>
                        <div class="words-box" id="amtWordsBox" data-en="{{ e($amtWords) }}" data-ta="{{ e($amtWordsTa) }}">{{ $defaultLang === 'ta' ? $amtWordsTa : $amtWords }}</div>
                    </div>
                    @if($repair->payments->where('direction','IN')->count())
                    <div>
                        <div class="sec-lbl" data-en="Payments Received" data-ta="பெறப்பட்ட பணம்">{{ $defaultLang === 'ta' ? 'பெறப்பட்ட பணம்' : 'Payments Received' }}</div>
                        @foreach($repair->payments->where('direction','IN') as $p)
                        <div class="pay-row">
                            <span>
                                <span data-en="{{ ucfirst($p->payment_type ?? '') }} &middot; {{ ucfirst(str_replace('_',' ',$p->payment_method)) }}" data-ta="{{ ucfirst($p->payment_type ?? '') }} &middot; {{ $methodsTa[$p->payment_method] ?? ucfirst(str_replace('_',' ',$p->payment_method)) }}">{{ ucfirst($p->payment_type ?? '') }} &middot; {{ $defaultLang === 'ta' ? ($methodsTa[$p->payment_method] ?? ucfirst(str_replace('_',' ',$p->payment_method))) : ucfirst(str_replace('_',' ',$p->payment_method)) }}</span>
                                @if($p->transaction_reference)<span style="color:#9ca3af;font-size:6.5px;">({{ $p->transaction_reference }})</span>@endif
                            </span>
                            <span class="p-green">+&#8377;{{ number_format($p->amount,2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                    @if($shopUpiId)
                    <div>
                        <div class="sec-lbl" data-en="Pay via UPI" data-ta="UPI வழியாக செலுத்தவும்">{{ $defaultLang === 'ta' ? 'UPI வழியாக செலுத்தவும்' : 'Pay via UPI' }}</div>
                        <div class="qr-area">
                            <div class="qr-box">QR<br>CODE</div>
                            <div class="qr-meta">
                                <div class="qr-upi">{{ $shopUpiId }}</div>
                                <div class="qr-scan" data-en="Scan to pay via any UPI app" data-ta="எந்த UPI ஆப்பிலும் ஸ்கேன் செய்து செலுத்தவும்">{{ $defaultLang === 'ta' ? 'எந்த UPI ஆப்பிலும் ஸ்கேன் செய்து செலுத்தவும்' : 'Scan to pay via any UPI app' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="inv-br">
                    <table class="sum-tbl">
                        <tr class="row-grand">
                            <td data-en="Grand Total" data-ta="மொத்த தொகை">{{ $defaultLang === 'ta' ? 'மொத்த தொகை' : 'Grand Total' }}</td>
                            <td>&#8377;{{ number_format($grandTotal,2) }}</td>
                        </tr>
                        @if($totalPaidIn > 0)
                        <tr class="row-paid">
                            <td data-en="Total Paid" data-ta="செலுத்தியது">{{ $defaultLang === 'ta' ? 'செலுத்தியது' : 'Total Paid' }}</td>
                            <td>&#8377;{{ number_format($totalPaidIn,2) }}</td>
                        </tr>
                        @if($balanceDue > 0)
                        <tr class="row-bal">
                            <td data-en="Balance Due" data-ta="நிலுவை">{{ $defaultLang === 'ta' ? 'நிலுவை' : 'Balance Due' }}</td>
                            <td>&#8377;{{ number_format($balanceDue,2) }}</td>
                        </tr>
                        @else
                        <tr class="row-full"><td colspan="2">&#10003; <span data-en="PAID IN FULL" data-ta="முழுமையாக செலுத்தப்பட்டது">{{ $defaultLang === 'ta' ? 'முழுமையாக செலுத்தப்பட்டது' : 'PAID IN FULL' }}</span></td></tr>
                        @endif
                        @endif
                    </table>
                    <div class="sign-area">
                        <div class="sign-blank"></div>
                        <div class="sign-line"></div>
                        <div class="sign-for" data-en="For {{ e($shopName) }}" data-ta="{{ e($shopNameTa) }} சார்பாக">{{ $defaultLang === 'ta' ? $shopNameTa . ' சார்பாக' : 'For ' . $shopName }}</div>
                        <div class="sign-auth" data-en="{{ e($signLabelEn) }}" data-ta="{{ e($signLabelTa) }}" data-setting-en="receipt_sign_label_en" data-setting-ta="receipt_sign_label_ta">{{ $defaultLang === 'ta' ? $signLabelTa : $signLabelEn }}</div>
                    </div>
                </div>
            </div>
@endsection
