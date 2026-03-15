@extends('layouts.invoice')
@section('title', 'Tax Invoice – ' . $repair->ticket_number)
@section('invoice-title', 'Tax Invoice')

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
    $shopState   = \App\Models\Setting::getValue('shop_state', '');

    // Determine IGST vs CGST/SGST based on customer state vs shop state
    $customerState = $repair->customer->billing_state ?? '';
    $isIgst = $shopState && $customerState && $shopState !== $customerState;

    $customerGstin   = $repair->customer->gstin   ?? '-';
    $customerAddress = $repair->customer->address ?? '-';

    $lineItems = collect();
    foreach ($repair->parts as $part) {
        $taxRate = (float) ($part->tax_rate ?? 0);
        $taxableValue = $part->cost_price * $part->quantity;
        $taxAmt = (float) ($part->tax_amount ?? 0);
        // If tax_amount not stored, calculate from rate
        if ($taxAmt == 0 && $taxRate > 0) {
            $taxAmt = round($taxableValue * $taxRate / 100, 2);
        }
        $lineItems->push([
            'name' => $part->part ? $part->part->name : ($part->product ? $part->product->name : 'Part'),
            'sub'  => $part->imei ?? null,
            'hsn'  => $part->hsn_code ?? ($part->part->hsn_code ?? ($part->product->hsn_code ?? '')),
            'qty'  => $part->quantity,
            'rate' => $part->cost_price,
            'taxable' => $taxableValue,
            'tax_rate' => $taxRate,
            'tax'  => $taxAmt,
            'total' => $taxableValue + $taxAmt,
        ]);
    }
    foreach ($repair->repairServices as $svc) {
        $taxRate = (float) ($svc->tax_rate ?? 0);
        $taxableValue = (float) $svc->customer_charge;
        $taxAmt = (float) ($svc->tax_amount ?? 0);
        if ($taxAmt == 0 && $taxRate > 0) {
            $taxAmt = round($taxableValue * $taxRate / 100, 2);
        }
        $lineItems->push([
            'name' => $svc->service_type_name, 'sub' => null,
            'hsn' => $svc->sac_code ?? '', 'qty' => 1,
            'rate' => $svc->customer_charge, 'taxable' => $taxableValue,
            'tax_rate' => $taxRate, 'tax' => $taxAmt,
            'total' => $taxableValue + $taxAmt,
        ]);
    }
    $serviceCharge = $repair->service_charge ?? 0;
    if ($serviceCharge > 0) {
        $lineItems->push(['name'=>'Service Charge','sub'=>null,'hsn'=>'','qty'=>1,
            'rate'=>$serviceCharge,'taxable'=>(float)$serviceCharge,
            'tax_rate'=>0,'tax'=>0,'total'=>(float)$serviceCharge]);
    }

    $taxableAmount = $lineItems->sum('taxable');
    $totalTax      = $lineItems->sum('tax');

    // Split tax into CGST/SGST or IGST
    if ($isIgst) {
        $igstAmount = $totalTax;
        $cgstAmount = 0;
        $sgstAmount = 0;
    } else {
        $igstAmount = 0;
        $cgstAmount = round($totalTax / 2, 2);
        $sgstAmount = $totalTax - $cgstAmount;
    }

    $grandTotal    = $taxableAmount + $totalTax;
    $totalQty      = $lineItems->sum('qty');
    $totalPaidIn   = $repair->payments->where('direction','IN')->sum('amount');
    $balanceDue    = max(0, $grandTotal - $totalPaidIn);

    $amountInWords = numWords($grandTotal);
    $showIgst      = $isIgst && $totalTax > 0;
    $showCgstSgst  = !$isIgst && $totalTax > 0;
    $hasTax        = $totalTax > 0;
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
                @if($customerGstin !== '-')
                <div class="irow"><span class="lb">GSTIN</span><span class="vl" style="font-family:monospace;font-size:11px;">{{ $customerGstin }}</span></div>
                @endif
                @if($repair->customer->billing_state)
                <div class="irow"><span class="lb">State</span><span class="vl">{{ $repair->customer->billing_state }}{{ $isIgst ? ' (Inter-State)' : ' (Intra-State)' }}</span></div>
                @endif
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
                    <th style="width:44px;">HSN/<br>SAC</th>
                    <th style="width:52px;">Qty</th>
                    <th style="width:78px;">Rate (₹)</th>
                    <th style="width:82px;">Taxable Value</th>
                    @if($showIgst)
                        <th colspan="2" style="width:100px;">IGST</th>
                    @elseif($showCgstSgst)
                        <th colspan="2">CGST</th>
                        <th colspan="2">SGST</th>
                    @endif
                    <th style="width:82px;">Total (₹)</th>
                </tr>
                @if($showIgst)
                <tr class="th2">
                    <th></th><th></th><th></th><th></th><th></th><th></th>
                    <th style="width:28px;">%</th><th style="width:72px;">Amount</th>
                    <th></th>
                </tr>
                @elseif($showCgstSgst)
                <tr class="th2">
                    <th></th><th></th><th></th><th></th><th></th><th></th>
                    <th>%</th><th>Amt</th><th>%</th><th>Amt</th><th></th>
                </tr>
                @endif
            </thead>
            <tbody>
                @foreach($lineItems as $idx => $item)
                <tr>
                    <td class="tc">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item['name'] }}</strong>
                        @if($item['sub'])<div class="item-sub">IMEI {{ $item['sub'] }}</div>@endif
                    </td>
                    <td class="tc">{{ $item['hsn'] }}</td>
                    <td class="tc">{{ number_format($item['qty']) }} NOS</td>
                    <td class="tr">{{ number_format($item['rate'], 2) }}</td>
                    <td class="tr">{{ number_format($item['taxable'], 2) }}</td>
                    @if($showIgst)
                        <td class="tc">{{ number_format($item['tax_rate'], 0) }}%</td>
                        <td class="tr">{{ number_format($item['tax'], 2) }}</td>
                    @elseif($showCgstSgst)
                        <td class="tc">{{ number_format($item['tax_rate']/2, 0) }}%</td>
                        <td class="tr">{{ number_format(round($item['tax']/2, 2), 2) }}</td>
                        <td class="tc">{{ number_format($item['tax_rate']/2, 0) }}%</td>
                        <td class="tr">{{ number_format($item['tax'] - round($item['tax']/2, 2), 2) }}</td>
                    @endif
                    <td class="tr"><strong>{{ number_format($item['total'], 2) }}</strong></td>
                </tr>
                @endforeach

                @for($e=0; $e < max(0, 7-$lineItems->count()); $e++)
                <tr style="height:26px;">
                    <td></td><td></td><td></td><td></td><td></td><td></td>
                    @if($showIgst)<td></td><td></td>
                    @elseif($showCgstSgst)<td></td><td></td><td></td><td></td>
                    @endif
                    <td></td>
                </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;letter-spacing:1px;">TOTAL</td>
                    <td class="tc">{{ number_format($totalQty) }}</td>
                    <td></td>
                    <td>{{ number_format($taxableAmount, 2) }}</td>
                    @if($showIgst)
                        <td></td><td>{{ number_format($igstAmount,2) }}</td>
                    @elseif($showCgstSgst)
                        <td></td><td>{{ number_format($cgstAmount,2) }}</td>
                        <td></td><td>{{ number_format($sgstAmount,2) }}</td>
                    @endif
                    <td>{{ number_format($lineItems->sum('total'),2) }}</td>
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
            <div class="s-hdr">Tax Summary</div>
            <table class="tax-tbl">
                <tr><td>Taxable Amount</td><td>{{ number_format($taxableAmount,2) }}</td></tr>
                @if($showIgst && $igstAmount>0)
                <tr><td>Add : IGST</td><td>{{ number_format($igstAmount,2) }}</td></tr>
                @endif
                @if($showCgstSgst && $cgstAmount>0)
                <tr><td>Add : CGST</td><td>{{ number_format($cgstAmount,2) }}</td></tr>
                @endif
                @if($showCgstSgst && $sgstAmount>0)
                <tr><td>Add : SGST</td><td>{{ number_format($sgstAmount,2) }}</td></tr>
                @endif
                <tr class="sep"><td>Total Tax</td><td>{{ number_format($totalTax,2) }}</td></tr>
                @if(!$hasTax)
                <tr class="note"><td colspan="2">* Tax rates not configured yet</td></tr>
                @endif
                <tr class="grand"><td>Total Amount After Tax</td><td>₹{{ number_format($grandTotal,2) }}</td></tr>
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
