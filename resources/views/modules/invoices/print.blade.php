@php
    $printKeys = [
        'shopNameTa'=>'invoice_shop_name_ta', 'shopSloganTa'=>'invoice_shop_slogan_ta',
        'shopAddressTa'=>'invoice_shop_address_ta',
        'signLabelEn'=>'invoice_sign_label_en', 'signLabelTa'=>'invoice_sign_label_ta',
    ];
    $printDefaults = [
        'headerTitleEn'=>'Sales Invoice', 'headerTitleTa'=>'விற்பனை இரசீது',
    ];
    require_once resource_path('views/partials/print-a4-vars.php');

    // Layout variables
    $pageTitle      = 'Invoice ' . $invoice->invoice_number;
    $backUrl        = url('/admin/invoices');
    $printBtnLabel  = 'Print Invoice';
    $docNumber      = $invoice->invoice_number;
    $docDate        = $invoice->created_at->format('d M Y');

    $lineItems  = $invoice->items->map(fn($i)=>[
        'name'=>$i->item_name,'serial'=>$i->serial_number??null,
        'qty'=>(int)$i->quantity,
        'mrp'=>(float)($i->mrp ?? $i->price),
        'rate'=>(float)$i->price,
        'total'=>(float)$i->price*(int)$i->quantity,
        'is_linked'=>(bool)$i->is_linked,
        'item_type'=>$i->item_type,
    ]);
    $regularItems = $lineItems->filter(fn($i) => !$i['is_linked']);
    $linkedItems  = $lineItems->filter(fn($i) => $i['is_linked']);
    $subTotal   = $lineItems->sum('total');
    $discount   = (float)($invoice->discount ?? 0);
    $grandTotal = $subTotal - $discount;
    $totalQty   = $regularItems->sum('qty');
    $paidAmount = $invoice->payments->sum('amount');
    $balanceDue = max(0, $grandTotal - $paidAmount);
    $payStatus  = $invoice->payment_status ?? 'unpaid';
    $amtWordsEn = numWords((float)$grandTotal);
    $amtWordsTa = numWordsTamil((float)$grandTotal);
    $emptyRows  = max(0, 6 - $lineItems->count());
@endphp

@extends('layouts.print-a4')

@section('printContent')
            <!-- Info -->
            <div class="inv-info">
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Bill To" data-ta="வாடிக்கையாளர்">{{ $defaultLang === 'ta' ? 'வாடிக்கையாளர்' : 'Bill To' }}</div>
                    <div class="inf-val" data-en="{{ e($invoice->customer?->name ?? 'Walk-in Customer') }}" data-ta="{{ e($invoice->customer?->name ?? 'நடை வாடிக்கையாளர்') }}">{{ $invoice->customer?->name ?? ($defaultLang === 'ta' ? 'நடை வாடிக்கையாளர்' : 'Walk-in Customer') }}</div>
                    @if($invoice->customer?->mobile_number || $invoice->customer?->address)
                    <div class="inf-sub">
                        @if($invoice->customer->mobile_number)&#128222; {{ $invoice->customer->mobile_number }}@endif
                        @if($invoice->customer->address)<br>{{ $invoice->customer->address }}@endif
                    </div>
                    @endif
                </div>
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Invoice Details" data-ta="இரசீது விவரங்கள்">{{ $defaultLang === 'ta' ? 'இரசீது விவரங்கள்' : 'Invoice Details' }}</div>
                    <div class="inf-sub">
                        <span data-en="Date" data-ta="தேதி">{{ $defaultLang === 'ta' ? 'தேதி' : 'Date' }}</span>: {{ $invoice->created_at->format('d M Y, g:i A') }}<br>
                        @if($invoice->due_date)<span data-en="Due" data-ta="நிலுவை தேதி">{{ $defaultLang === 'ta' ? 'நிலுவை தேதி' : 'Due' }}</span>: {{ $invoice->due_date->format('d M Y') }}<br>@endif
                        <span data-en="Staff" data-ta="ஊழியர்">{{ $defaultLang === 'ta' ? 'ஊழியர்' : 'Staff' }}</span>: {{ $invoice->creator?->name ?? '—' }}
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
                            <td>{{ $item['name'] }}@if($item['serial'])<div class="serial-sub"><span data-en="S/N" data-ta="வ.எண்">{{ $defaultLang === 'ta' ? 'வ.எண்' : 'S/N' }}</span>: {{ $item['serial'] }}</div>@endif
                            </td>
                            <td class="tc">{{ $item['qty'] }}</td>
                            <td class="tr" style="color:#000;font-weight:500;">@if(!$item['is_linked'] && $item['mrp'] > $item['rate']){{ number_format($item['mrp'],2) }}@else&mdash;@endif</td>
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
                        <td class="tr">{{ number_format($subTotal,2) }}</td>
                    </tr></tfoot>
                </table>
            </div>

            <!-- Bottom -->
            <div class="inv-bottom">
                <div class="inv-bl">
                    <div>
                        <div class="sec-lbl" data-en="Amount in Words" data-ta="தொகை சொற்களில்">{{ $defaultLang === 'ta' ? 'தொகை சொற்களில்' : 'Amount in Words' }}</div>
                        <div class="words-box" data-en="{{ $amtWordsEn }}" data-ta="{{ $amtWordsTa }}">{{ $defaultLang === 'ta' ? $amtWordsTa : $amtWordsEn }}</div>
                    </div>
                    @if($invoice->payments->count())
                    <div>
                        <div class="sec-lbl" data-en="Payments Received" data-ta="பெறப்பட்ட பணம்">{{ $defaultLang === 'ta' ? 'பெறப்பட்ட பணம்' : 'Payments Received' }}</div>
                        @foreach($invoice->payments as $p)
                        <div class="pay-row">
                            <span>
                                <span data-en="{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}" data-ta="{{ $methodsTa[$p->payment_method] ?? ucfirst(str_replace('_',' ',$p->payment_method)) }}">{{ $defaultLang === 'ta' ? ($methodsTa[$p->payment_method] ?? ucfirst(str_replace('_',' ',$p->payment_method))) : ucfirst(str_replace('_',' ',$p->payment_method)) }}</span>
                                @if($p->transaction_reference)<span style="color:#9ca3af;font-size:6.5px;">({{ $p->transaction_reference }})</span>@endif
                            </span>
                            <span class="p-green">+&#8377;{{ number_format($p->amount,2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="inv-br">
                    <table class="sum-tbl">
                        @if($discount > 0)
                        <tr><td data-en="Sub Total" data-ta="கூட்டுத்தொகை">{{ $defaultLang === 'ta' ? 'கூட்டுத்தொகை' : 'Sub Total' }}</td><td>{{ number_format($subTotal,2) }}</td></tr>
                        <tr class="row-disc"><td data-en="Discount" data-ta="தள்ளுபடி">{{ $defaultLang === 'ta' ? 'தள்ளுபடி' : 'Discount' }}</td><td>-{{ number_format($discount,2) }}</td></tr>
                        @endif
                        <tr class="row-grand"><td data-en="Grand Total" data-ta="மொத்த தொகை">{{ $defaultLang === 'ta' ? 'மொத்த தொகை' : 'Grand Total' }}</td><td>&#8377;{{ number_format($grandTotal,2) }}</td></tr>
                        @if($paidAmount > 0)
                        <tr class="row-paid"><td data-en="Paid" data-ta="செலுத்தியது">{{ $defaultLang === 'ta' ? 'செலுத்தியது' : 'Paid' }}</td><td>&#8377;{{ number_format($paidAmount,2) }}</td></tr>
                        @if($balanceDue > 0)
                        <tr class="row-bal"><td data-en="Balance Due" data-ta="நிலுவை">{{ $defaultLang === 'ta' ? 'நிலுவை' : 'Balance Due' }}</td><td>&#8377;{{ number_format($balanceDue,2) }}</td></tr>
                        @else
                        <tr class="row-full"><td colspan="2" data-en="✓ PAID IN FULL" data-ta="✓ முழுமையாக செலுத்தப்பட்டது">{{ $defaultLang === 'ta' ? '✓ முழுமையாக செலுத்தப்பட்டது' : '✓ PAID IN FULL' }}</td></tr>
                        @endif
                        @endif
                    </table>
                    <div class="sign-area">
                        <div class="sign-blank"></div>
                        <div class="sign-line"></div>
                        <div class="sign-for" data-en="For {{ e($shopName) }}" data-ta="{{ e($shopNameTa) }} சார்பாக">{{ $defaultLang === 'ta' ? $shopNameTa . ' சார்பாக' : 'For ' . $shopName }}</div>
                        <div class="sign-auth" data-en="{{ e($signLabelEn) }}" data-ta="{{ e($signLabelTa) }}" data-setting-en="invoice_sign_label_en" data-setting-ta="invoice_sign_label_ta">{{ $defaultLang === 'ta' ? $signLabelTa : $signLabelEn }}</div>
                    </div>
                </div>
            </div>
@endsection
