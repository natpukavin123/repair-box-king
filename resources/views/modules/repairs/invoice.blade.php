@extends('layouts.invoice')
@section('title', 'Invoice – ' . $repair->ticket_number)
@section('invoice-title', 'Repair Invoice')

@php
    if (!function_exists('numWords')) {
        function numWords(float $n): string {
            $o=['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN','ELEVEN',
                'TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
            $t=['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
            $c=function(int $x)use($o,$t,&$c):string{
                if($x<20)  return $o[$x];
                if($x<100) return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
                if($x<1000)return $o[(int)($x/100)].' HUNDRED'.($x%100?' '.$c($x%100):'');
                if($x<100000)   return $c((int)($x/1000)).' THOUSAND'.($x%1000?' '.$c($x%1000):'');
                if($x<10000000) return $c((int)($x/100000)).' LAKH'.($x%100000?' '.$c($x%100000):'');
                return $c((int)($x/10000000)).' CRORE'.($x%10000000?' '.$c($x%10000000):'');
            };
            return $c((int)$n).' RUPEES ONLY';
        }
    }

    $shopName    = \App\Models\Setting::getValue('shop_name', 'RepairBox');
    $shopUpiId   = \App\Models\Setting::getValue('upi_id', '');

    $customerAddress = $repair->customer->address ?? '-';

    $lineItems = collect();
    foreach ($repair->parts as $part) {
        $lineItems->push([
            'name' => $part->part ? $part->part->name : ($part->product ? $part->product->name : 'Part'),
            'sub'  => $part->imei ?? null,
            'qty'  => $part->quantity,
            'rate' => $part->cost_price,
            'total' => $part->cost_price * $part->quantity,
        ]);
    }
    foreach ($repair->repairServices as $svc) {
        $lineItems->push([
            'name' => $svc->service_type_name, 'sub' => null,
            'qty' => 1,
            'rate' => $svc->customer_charge,
            'total' => (float) $svc->customer_charge,
        ]);
    }
    $serviceCharge = $repair->service_charge ?? 0;
    if ($serviceCharge > 0) {
        $lineItems->push(['name'=>'Service Charge','sub'=>null,'qty'=>1,
            'rate'=>$serviceCharge,'total'=>(float)$serviceCharge]);
    }

    $grandTotal    = $lineItems->sum('total');
    $totalQty      = $lineItems->sum('qty');
    $totalPaidIn   = $repair->payments->where('direction','IN')->sum('amount');
    $balanceDue    = max(0, $grandTotal - $totalPaidIn);

    $amountInWords = numWords($grandTotal);
@endphp

@section('invoice-content')
    {{-- ══ INFO GRID ══ --}}
    <div class="info-grid">
        <div class="info-col">
            <div class="col-hdr">Customer Detail</div>
            <div class="col-body">
                @if($repair->customer)
                <div class="irow"><span class="lb">Name</span><span class="vl">{{ $repair->customer->name }}</span></div>
                <div class="irow"><span class="lb">Address</span><span class="vl">{{ $customerAddress }}</span></div>
                <div class="irow"><span class="lb">Phone</span><span class="vl">{{ $repair->customer->mobile_number ?? '-' }}</span></div>
                @else
                <div class="irow"><span class="lb">Name</span><span class="vl">Walk-in Customer</span></div>
                @endif
            </div>
        </div>

        <div class="info-col" style="max-width:215px;">
            <div class="col-hdr">Invoice Detail</div>
            <div class="col-body">
                <div class="irow"><span class="lb">Invoice No.</span><span class="vl inv-num">{{ $repair->ticket_number }}</span></div>
                <div style="height:6px;"></div>
                <div class="irow"><span class="lb">Invoice Date</span><span class="vl">{{ $repair->created_at->format('d M Y') }}</span></div>
                <div class="irow"><span class="lb">Device</span><span class="vl">{{ $repair->device_brand }} {{ $repair->device_model }}</span></div>
                @if($repair->imei)
                <div class="irow"><span class="lb">IMEI</span><span class="vl">{{ $repair->imei }}</span></div>
                @endif
            </div>
        </div>
    </div>

    {{-- ══ ITEMS TABLE ══ --}}
    <div class="tbl-wrap">
        <table class="items">
            <thead>
                <tr>
                    <th style="width:26px;">Sr.</th>
                    <th class="tl">Name of Product / Service</th>
                    <th style="width:52px;">Qty</th>
                    <th style="width:78px;">Rate (₹)</th>
                    <th style="width:82px;">Total (₹)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lineItems as $idx => $item)
                <tr>
                    <td class="tc">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item['name'] }}</strong>
                        @if($item['sub'])<div class="item-sub">IMEI {{ $item['sub'] }}</div>@endif
                    </td>
                    <td class="tc">{{ number_format($item['qty']) }} NOS</td>
                    <td class="tr">{{ number_format($item['rate'], 2) }}</td>
                    <td class="tr"><strong>{{ number_format($item['total'], 2) }}</strong></td>
                </tr>
                @endforeach

                @for($e=0; $e < max(0, 7-$lineItems->count()); $e++)
                <tr style="height:26px;">
                    <td></td><td></td><td></td><td></td><td></td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" style="text-align:right;letter-spacing:1px;">TOTAL</td>
                    <td class="tc">{{ number_format($totalQty) }}</td>
                    <td></td>
                    <td>{{ number_format($grandTotal,2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ══ BOTTOM SECTION ══ --}}
    <div class="bottom">
        <div class="b-left">
            <div class="s-hdr">Amount in Words</div>
            <div class="s-body">
                <div class="words-box">{{ $amountInWords }}</div>
            </div>

            @if($repair->payments->count())
            <div class="s-hdr mt4">Payments Received</div>
            <div class="s-body">
                @foreach($repair->payments->where('direction', 'IN') as $pay)
                <div class="pay-item">
                    <span>{{ ucfirst($pay->payment_type) }} · {{ ucfirst($pay->payment_method) }}</span>
                    <span class="p-in">+₹{{ number_format($pay->amount,2) }}</span>
                </div>
                @endforeach
            </div>
            @endif

            @if($shopUpiId)
            <div class="s-body mt4">
                <div class="qr-area">
                    <div class="qr-box">QR<br>CODE</div>
                    <div class="qr-meta">
                        <div class="qr-upi">{{ $shopUpiId }}</div>
                        <div class="qr-scan">Scan to pay via any UPI app</div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="b-right">
            <div class="s-hdr">Summary</div>
            <table class="summary-tbl">
                <tr class="grand"><td>Total Amount</td><td>₹{{ number_format($grandTotal,2) }}</td></tr>
                <tr class="note"><td colspan="2">&nbsp;(E &amp; O.E.)</td></tr>

                @if($totalPaidIn > 0)
                <tr class="sep green"><td>Total Paid</td><td>₹{{ number_format($totalPaidIn,2) }}</td></tr>
                @if($balanceDue > 0)
                <tr class="bal"><td>Balance Due</td><td>₹{{ number_format($balanceDue,2) }}</td></tr>
                @else
                <tr class="full"><td colspan="2">✓ &nbsp;PAID IN FULL</td></tr>
                @endif
                @endif
            </table>

            <div class="sign-box">
                <div class="sign-cert">Certified that the particulars given above are true and correct.</div>
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
