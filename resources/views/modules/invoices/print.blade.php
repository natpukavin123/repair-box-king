@extends('layouts.invoice')
@section('title', 'Sales Invoice – ' . $invoice->invoice_number)
@section('invoice-title', 'Sales Invoice')

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

    $shopName  = \App\Models\Setting::getValue('shop_name', 'RepairBox');
    $shopUpiId = \App\Models\Setting::getValue('upi_id', '');

    // Use stored tax data from the invoice
    $isIgst = (bool) $invoice->is_igst;

    $customerGstin   = $invoice->customer->gstin   ?? '-';
    $customerAddress = $invoice->customer->address ?? '-';

    $lineItems = collect();
    foreach ($invoice->items as $item) {
        $lineItems->push([
            'name'    => $item->item_name,
            'sub'     => $item->serial_number ?? null,
            'hsn'     => $item->hsn_code ?? '',
            'qty'     => $item->quantity,
            'rate'    => $item->price,
            'taxable' => $item->price * $item->quantity,
            'tax_rate'=> (float) $item->tax_rate,
            'igst'    => (float) $item->igst_amount,
            'cgst'    => (float) $item->cgst_amount,
            'sgst'    => (float) $item->sgst_amount,
            'total'   => $item->price * $item->quantity + (float) $item->tax_amount,
        ]);
    }

    $taxableAmount = $lineItems->sum('taxable');
    $igstAmount    = (float) $invoice->igst_amount;
    $cgstAmount    = (float) $invoice->cgst_amount;
    $sgstAmount    = (float) $invoice->sgst_amount;
    $totalTax      = (float) $invoice->tax_amount;
    $subTotal      = $taxableAmount + $totalTax;
    $discount      = $invoice->discount ?? 0;
    $grandTotal    = $subTotal - $discount;
    $totalQty      = $lineItems->sum('qty');
    $totalPaidIn   = $invoice->payments->sum('amount');
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
                @if($invoice->customer)
                <div class="irow"><span class="lb">Name</span><span class="vl">{{ $invoice->customer->name }}</span></div>
                <div class="irow"><span class="lb">Address</span><span class="vl">{{ $customerAddress }}</span></div>
                <div class="irow"><span class="lb">Phone</span><span class="vl">{{ $invoice->customer->mobile_number ?? '-' }}</span></div>
                @if($customerGstin !== '-')
                <div class="irow"><span class="lb">GSTIN</span><span class="vl">{{ $customerGstin }}</span></div>
                @endif
                @else
                <div class="irow"><span class="lb">Name</span><span class="vl">Walk-in Customer</span></div>
                @endif
            </div>
        </div>

        <div class="info-col" style="max-width:215px;">
            <div class="col-hdr">Invoice Detail</div>
            <div class="col-body">
                <div class="irow"><span class="lb">Invoice No.</span><span class="vl inv-num">{{ $invoice->invoice_number }}</span></div>
                <div style="height:6px;"></div>
                <div class="irow"><span class="lb">Invoice Date</span><span class="vl">{{ $invoice->created_at->format('d M Y') }}</span></div>
                <div class="irow"><span class="lb">Due Date</span><span class="vl">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</span></div>
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
            </thead>
            <tbody>
                @foreach($lineItems as $idx => $item)
                <tr>
                    <td class="tc">{{ $idx + 1 }}</td>
                    <td>
                        <strong>{{ $item['name'] }}</strong>
                        @if($item['sub'])<div class="item-sub">S/N {{ $item['sub'] }}</div>@endif
                    </td>
                    <td class="tc">{{ $item['hsn'] }}</td>
                    <td class="tc">{{ number_format($item['qty']) }} NOS</td>
                    <td class="tr">{{ number_format($item['rate'], 2) }}</td>
                    <td class="tr">{{ number_format($item['taxable'], 2) }}</td>
                    @if($showIgst)
                        <td class="tc">{{ number_format($item['tax_rate'], 0) }}%</td>
                        <td class="tr">{{ number_format($item['igst'], 2) }}</td>
                    @elseif($showCgstSgst)
                        <td class="tc">{{ number_format($item['tax_rate']/2, 0) }}%</td>
                        <td class="tr">{{ number_format($item['cgst'], 2) }}</td>
                        <td class="tc">{{ number_format($item['tax_rate']/2, 0) }}%</td>
                        <td class="tr">{{ number_format($item['sgst'], 2) }}</td>
                    @endif
                    <td class="tr"><strong>{{ number_format($item['total'], 2) }}</strong></td>
                </tr>
                @endforeach

                @for($e=0; $e < max(0, 5-$lineItems->count()); $e++)
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

            @if($invoice->payments->count())
            <div class="s-hdr mt4">Payments Received</div>
            <div class="s-body">
                @foreach($invoice->payments as $pay)
                <div class="pay-item">
                    <span>{{ ucfirst($pay->payment_method) }}</span>
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
                @if($discount > 0)
                <tr><td>Sub Total</td><td>{{ number_format($subTotal,2) }}</td></tr>
                <tr style="color:#c0392b;"><td>Discount</td><td>-{{ number_format($discount,2) }}</td></tr>
                @endif
                <tr class="grand"><td>Grand Total</td><td>₹{{ number_format($grandTotal,2) }}</td></tr>
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
    <strong>Invoice:</strong> {{ $invoice->invoice_number }}
    &nbsp;|&nbsp;
@endsection
