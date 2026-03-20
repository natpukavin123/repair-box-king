@php
    if (!function_exists('numWords')) {
        function numWords(float $n): string {
            $o=['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN',
                'ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
            $t=['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
            $c=function(int $x)use($o,$t,&$c):string{
                if($x<20)return $o[$x];if($x<100)return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
                if($x<1000)return $o[(int)($x/100)].' HUNDRED'.($x%100?' '.$c($x%100):'');
                if($x<100000)return $c((int)($x/1000)).' THOUSAND'.($x%1000?' '.$c($x%1000):'');
                if($x<10000000)return $c((int)($x/100000)).' LAKH'.($x%100000?' '.$c($x%100000):'');
                return $c((int)($x/10000000)).' CRORE'.($x%10000000?' '.$c($x%10000000):'');
            };
            return $c((int)$n).' RUPEES ONLY';
        }
    }
    if (!function_exists('numWordsTamil')) {
        function numWordsTamil(float $n): string {
            $o=['','ஒன்று','இரண்டு','மூன்று','நான்கு','ஐந்து','ஆறு','ஏழு','எட்டு','ஒன்பது','பத்து',
                'பதினொன்று','பன்னிரண்டு','பதிமூன்று','பதினான்கு','பதினைந்து','பதினாறு','பதினேழு','பதினெட்டு','பத்தொன்பது'];
            $t=['','','இருபது','முப்பது','நாற்பது','ஐம்பது','அறுபது','எழுபது','எண்பது','தொண்ணூறு'];
            $c=function(int $x)use($o,$t,&$c):string{
                if($x<20)return $o[$x];
                if($x<100)return $t[(int)($x/10)].($x%10?' '.$o[$x%10]:'');
                if($x<1000)return $o[(int)($x/100)].' நூறு'.($x%100?' '.$c($x%100):'');
                if($x<100000)return $c((int)($x/1000)).' ஆயிரம்'.($x%1000?' '.$c($x%1000):'');
                if($x<10000000)return $c((int)($x/100000)).' லட்சம்'.($x%100000?' '.$c($x%100000):'');
                return $c((int)($x/10000000)).' கோடி'.($x%10000000?' '.$c($x%10000000):'');
            };
            return $c((int)$n).' ரூபாய் மட்டும்';
        }
    }

    // Shop (General)
    $shopName    = \App\Models\Setting::getValue('shop_name',    'RepairBox');
    $shopAddress = \App\Models\Setting::getValue('shop_address', 'Your shop address');
    $shopPhone   = \App\Models\Setting::getValue('shop_phone',   '');
    $shopEmail   = \App\Models\Setting::getValue('shop_email',   '');
    $shopSlogan  = \App\Models\Setting::getValue('shop_slogan',  'Your Trusted Mobile Partner');
    $shopIcon    = \App\Models\Setting::getValue('shop_icon',    '');

    // Invoice print settings (dynamic)
    $headerTitleEn = \App\Models\Setting::getValue('invoice_header_title_en', 'Sales Invoice');
    $headerTitleTa = \App\Models\Setting::getValue('invoice_header_title_ta', 'விற்பனை இரசீது');
    $shopNameTa    = \App\Models\Setting::getValue('invoice_shop_name_ta', '') ?: $shopName;
    $shopSloganTa  = \App\Models\Setting::getValue('invoice_shop_slogan_ta', '') ?: $shopSlogan;
    $shopAddressTa = \App\Models\Setting::getValue('invoice_shop_address_ta', '') ?: $shopAddress;
    $signLabelEn   = \App\Models\Setting::getValue('invoice_sign_label_en', 'Authorised Signatory');
    $signLabelTa   = \App\Models\Setting::getValue('invoice_sign_label_ta', 'அங்கீகரிக்கப்பட்ட கையொப்பம்');
    $footerTextEn  = \App\Models\Setting::getValue('invoice_footer_text',
        'Subject to jurisdiction. Our responsibility ceases as soon as goods leave our premises. Goods once sold will not be taken back.');
    $footerTextTa  = \App\Models\Setting::getValue('invoice_footer_text_ta',
        'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது. பொருட்கள் எங்கள் வளாகத்தை விட்டு வெளியேறியவுடன் எங்கள் பொறுப்பு முடிவடைகிறது. விற்கப்பட்ட பொருட்கள் திரும்ப ஏற்றுக்கொள்ளப்படாது.');
    $defaultLang   = \App\Models\Setting::getValue('invoice_default_language', 'en');

    $lineItems  = $invoice->items->map(fn($i)=>[
        'name'=>$i->item_name,'serial'=>$i->serial_number??null,
        'qty'=>(int)$i->quantity,
        'mrp'=>(float)($i->mrp ?? $i->price),
        'rate'=>(float)$i->price,
        'total'=>(float)$i->price*(int)$i->quantity,
    ]);
    $subTotal   = $lineItems->sum('total');
    $discount   = (float)($invoice->discount ?? 0);
    $grandTotal = $subTotal - $discount;
    $totalQty   = $lineItems->sum('qty');
    $paidAmount = $invoice->payments->sum('amount');
    $balanceDue = max(0, $grandTotal - $paidAmount);
    $payStatus  = $invoice->payment_status ?? 'unpaid';
    $amtWordsEn = numWords((float)$grandTotal);
    $amtWordsTa = numWordsTamil((float)$grandTotal);
    $emptyRows  = max(0, 6 - $lineItems->count());
    $methodsTa  = ['cash'=>'பணம்','card'=>'அட்டை','upi'=>'UPI','bank_transfer'=>'வங்கி மாற்றம்','cheque'=>'காசோலை'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=Noto+Sans+Tamil:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#000;background:#ccc;}
body.lang-ta .inv,body.lang-ta .inv *:not(.inv-logo-txt):not(.inv-num){font-family:'Noto Sans Tamil','DM Sans',sans-serif;}
body.lang-ta .inv-shop-name{font-size:18px;}

/* ═══════ TOOLBAR (screen) ═══════ */
.toolbar{width:297mm;margin:0 auto;padding:14px 4px 10px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.toolbar-left{display:flex;align-items:center;gap:14px;}
.back-btn{display:inline-flex;align-items:center;gap:5px;color:#000;font-size:12px;font-weight:600;
    text-decoration:none;padding:6px 10px;background:#fff;border-radius:6px;border:1px solid #000;}
.back-btn:hover{background:#eee;}
.t-title{font-size:14px;font-weight:700;color:#000;}
.t-sub{font-size:11px;color:#000;margin-top:1px;}
.toolbar-actions{display:flex;align-items:center;gap:10px;}
.lang-picker{display:flex;gap:0;border-radius:8px;overflow:hidden;border:2px solid #000;}
.lang-btn{padding:8px 18px;font-size:12px;font-weight:600;border:none;cursor:pointer;
    background:#fff;color:#000;transition:all .15s;font-family:inherit;}
.lang-btn:hover{background:#eee;}
.lang-btn.active{background:#000;color:#fff;}
.print-btn{display:inline-flex;align-items:center;gap:8px;background:#000;color:#fff;
    font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:600;
    padding:9px 22px 9px 18px;border:none;border-radius:7px;cursor:pointer;}
.print-btn:hover{background:#333;}

/* ═══════ A4 LANDSCAPE SHELL ═══════ */
.a4-shell{width:297mm;height:210mm;margin:0 auto 20mm;background:#fff;display:flex;flex-direction:row;
    box-shadow:0 2px 12px rgba(0,0,0,.3);}
.inv-half{width:148.5mm;height:210mm;flex-shrink:0;overflow:hidden;padding:4mm 5mm;background:#fff;}

/* CUT LINE */
.cut-zone{width:0;flex-shrink:0;border-left:1.5px dashed #999;position:relative;}
.cut-label{position:absolute;top:50%;left:-1px;transform:translate(-50%,-50%) rotate(-90deg);
    background:#ddd;border:1px dashed #999;border-radius:2px;padding:1px 12px;font-size:8px;
    font-weight:800;letter-spacing:2px;color:#666;text-transform:uppercase;white-space:nowrap;display:flex;align-items:center;gap:6px;}
/* BLANK HALF */
.blank-half{flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;background:#f5f5f5;}
.blank-circle{width:44px;height:44px;border:2px dashed #bbb;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:20px;}
.blank-title{font-size:11px;font-weight:800;color:#aaa;letter-spacing:2px;text-transform:uppercase;}
.blank-sub{font-size:9px;color:#aaa;text-align:center;line-height:1.65;}

/* ══════════════════ INVOICE CARD ══════════════════ */
.inv{width:100%;height:100%;display:flex;flex-direction:column;border:2px solid #000;overflow:hidden;}

.inv-hdr{background:#fff;padding:12px 14px 10px;display:flex;align-items:center;gap:12px;flex-shrink:0;border-bottom:2px solid #000;}
.inv-logo{width:44px;height:44px;border:2px solid #000;border-radius:50%;overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:#fff;}
.inv-logo img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
.inv-logo-txt{font-size:7px;font-weight:700;color:#000;text-align:center;line-height:1.4;}
.inv-shop{flex:1;min-width:0;}
.inv-shop-name{font-family:'Playfair Display',Georgia,serif;font-size:20px;font-weight:900;color:#000;line-height:1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.inv-shop-slogan{font-size:8px;color:#000;letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;}
.inv-shop-contact{font-size:9px;color:#000;margin-top:4px;line-height:1.7;}
.inv-badge{text-align:right;flex-shrink:0;}
.inv-type{display:inline-block;border:1.5px solid #000;color:#000;font-size:8px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:3px 8px;margin-bottom:3px;}
.inv-num{font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:900;color:#000;line-height:1;}
.inv-date{font-size:9px;color:#000;margin-top:2px;}
.inv-rule{height:0;flex-shrink:0;}

.inv-info{display:flex;border-bottom:2px solid #000;flex-shrink:0;}
.inf-cell{flex:1;padding:8px 10px;}
.inf-cell+.inf-cell{border-left:1.5px solid #000;}
.inf-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:3px;}
.inf-val{font-size:12px;font-weight:700;color:#000;line-height:1.3;}
.inf-sub{font-size:9px;color:#000;margin-top:2px;line-height:1.55;}

.inv-tbl-wrap{border-bottom:2px solid #000;flex-shrink:0;}
table.inv-tbl{width:100%;border-collapse:collapse;}
table.inv-tbl thead th{background:#fff;color:#000;font-size:8px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:5px 6px;text-align:center;border:1.5px solid #000;}
table.inv-tbl thead th.tl{text-align:left;}
table.inv-tbl tbody td{padding:5px 6px;font-size:11px;border:1px solid #000;vertical-align:middle;}
table.inv-tbl tbody tr.erow{height:18px;}
table.inv-tbl tfoot td{background:#fff;color:#000;font-size:10px;font-weight:700;padding:5px 6px;text-align:right;border:1.5px solid #000;}
.tc{text-align:center;}.tr{text-align:right;}.tl{text-align:left;}
.serial-sub{font-size:8px;color:#000;font-style:italic;margin-top:1px;}

.inv-bottom{display:flex;flex:1;border-bottom:2px solid #000;min-height:0;overflow:hidden;}
.inv-bl{flex:1;border-right:1.5px solid #000;padding:7px 10px;display:flex;flex-direction:column;gap:5px;overflow:hidden;}
.inv-br{width:155px;flex-shrink:0;display:flex;flex-direction:column;}
.sec-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:2px;}
.words-box{border:1.5px solid #000;padding:5px 8px;font-size:9px;font-weight:600;color:#000;line-height:1.55;background:#fff;}
.pay-row{display:flex;justify-content:space-between;font-size:10px;padding:2px 0;border-bottom:1px solid #000;color:#000;}
.pay-row:last-child{border-bottom:none;}
.p-green{color:#000;font-weight:700;}

table.sum-tbl{width:100%;border-collapse:collapse;}
table.sum-tbl td{padding:5px 9px;font-size:10px;border-bottom:1px solid #000;color:#000;}
table.sum-tbl td:last-child{text-align:right;font-weight:600;}
table.sum-tbl .row-disc td{color:#000;font-weight:700;}
table.sum-tbl .row-grand td{background:#fff;color:#000;font-family:'Playfair Display',Georgia,serif;font-size:12px;font-weight:900;padding:6px 9px;border:1.5px solid #000;}
table.sum-tbl .row-paid td{color:#000;font-weight:700;border-bottom:1px solid #000;padding-top:4px;}
table.sum-tbl .row-bal td{color:#000;font-weight:900;font-size:11px;border-bottom:none;text-decoration:underline;}
table.sum-tbl .row-full td{color:#000;font-weight:900;text-align:center;border-bottom:none;}

.sign-area{padding:5px 9px 7px;text-align:center;border-top:1.5px solid #000;margin-top:auto;}
.sign-blank{height:20px;}
.sign-line{border-top:1.5px solid #000;margin:0 12px 2px;}
.sign-for{font-family:'Playfair Display',Georgia,serif;font-size:10px;font-weight:700;color:#000;}
.sign-auth{font-size:8px;color:#000;letter-spacing:1px;text-transform:uppercase;margin-top:1px;}

.inv-foot{background:#fff;border-top:2px solid #000;padding:4px 10px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.inv-tc{font-size:8px;color:#000;flex:1;margin-right:8px;line-height:1.5;}
.inv-gen{font-size:8px;color:#000;white-space:nowrap;}

@page{size:A4 landscape;margin:0;}
@media print{
    html,body{margin:0;padding:0;background:#fff;width:297mm;height:210mm;}
    .toolbar,.cut-zone{display:none!important;}
    .a4-shell{width:297mm;height:210mm;box-shadow:none;margin:0;display:flex;flex-direction:row;}
    .blank-half{display:block!important;flex:1;background:#fff!important;}
    .blank-half *{display:none!important;}
    .inv-half{width:148.5mm;height:210mm;}
}
</style>
</head>
<body class="{{ $defaultLang === 'ta' ? 'lang-ta' : '' }}">

<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ url('/invoices') }}" class="back-btn">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <div>
            <div class="t-title">Print Preview &nbsp;&middot;&nbsp; <span id="previewTitle">{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</span></div>
            <div class="t-sub">{{ $invoice->invoice_number }} &nbsp;&middot;&nbsp; {{ $shopName }}</div>
        </div>
    </div>
    <div class="toolbar-actions">
        <div class="lang-picker">
            <button class="lang-btn {{ $defaultLang === 'en' ? 'active' : '' }}" onclick="switchLang('en')" id="btnEn">English</button>
            <button class="lang-btn {{ $defaultLang === 'ta' ? 'active' : '' }}" onclick="switchLang('ta')" id="btnTa">தமிழ்</button>
        </div>
        <button class="print-btn" onclick="window.print()">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print Invoice
        </button>
    </div>
</div>

<div class="a4-shell">
    <!-- LEFT HALF: blank -->
    <div class="blank-half">
        <div class="blank-circle">&#8629;</div>
        <div class="blank-title">Blank &mdash; Reuse</div>
        <div class="blank-sub">Cut vertically along dashed line<br>Reuse left half for next print</div>
    </div>

    <!-- VERTICAL CUT LINE (screen only) -->
    <div class="cut-zone">
        <div class="cut-label">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/>
                <line x1="20" y1="4" x2="8.12" y2="15.88"/><line x1="14.47" y1="14.48" x2="20" y2="20"/>
                <line x1="8.12" y1="8.12" x2="12" y2="12"/>
            </svg>
            Cut Here
        </div>
    </div>

    <!-- RIGHT HALF: INVOICE (148.5mm × 210mm) -->
    <div class="inv-half">
        <div class="inv">

            <div class="inv-hdr">
                @if($shopIcon)
                <div class="inv-logo"><img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}"></div>
                @else
                <div class="inv-logo"><div class="inv-logo-txt">REPAIR<br>BOX</div></div>
                @endif
                <div class="inv-shop">
                    <div class="inv-shop-name" data-en="{{ e($shopName) }}" data-ta="{{ e($shopNameTa) }}">{{ $defaultLang === 'ta' ? $shopNameTa : $shopName }}</div>
                    <div class="inv-shop-slogan" data-en="{{ e($shopSlogan) }}" data-ta="{{ e($shopSloganTa) }}">{{ $defaultLang === 'ta' ? $shopSloganTa : $shopSlogan }}</div>
                    <div class="inv-shop-contact"
                         data-en-addr="{{ e($shopAddress) }}"
                         data-ta-addr="{{ e($shopAddressTa) }}">
                        &#128205; {{ $defaultLang === 'ta' ? $shopAddressTa : $shopAddress }}<br>
                        &#128222; {{ $shopPhone }}@if($shopEmail) &middot; &#9993; {{ $shopEmail }}@endif
                    </div>
                </div>
                <div class="inv-badge">
                    <div class="inv-type" data-en="{{ e($headerTitleEn) }}" data-ta="{{ e($headerTitleTa) }}">{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</div>
                    <div class="inv-num">#{{ $invoice->invoice_number }}</div>
                    <div class="inv-date">{{ $invoice->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="inv-rule"></div>

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
                            <td>{{ $item['name'] }}@if($item['serial'])<div class="serial-sub"><span data-en="S/N" data-ta="வ.எண்">{{ $defaultLang === 'ta' ? 'வ.எண்' : 'S/N' }}</span>: {{ $item['serial'] }}</div>@endif</td>
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
                        <div class="sign-auth" data-en="{{ e($signLabelEn) }}" data-ta="{{ e($signLabelTa) }}">{{ $defaultLang === 'ta' ? $signLabelTa : $signLabelEn }}</div>
                    </div>
                </div>
            </div>

            <div class="inv-foot">
                <div class="inv-tc" data-en="{{ e($footerTextEn) }}" data-ta="{{ e($footerTextTa) }}">{{ $defaultLang === 'ta' ? $footerTextTa : $footerTextEn }}</div>
                <div class="inv-gen">
                    {{ $shopName }} &nbsp;|&nbsp; &#128222; {{ $shopPhone }}
                    @if($shopEmail) &nbsp;|&nbsp; &#9993; {{ $shopEmail }} @endif
                    &nbsp;|&nbsp; E &amp; O.E.
                </div>
            </div>

        </div>
    </div>
</div>

<script>
var shopPhone = @json($shopPhone);
var shopEmail = @json($shopEmail);
function switchLang(lang) {
    document.body.classList.toggle('lang-ta', lang === 'ta');
    document.getElementById('btnEn').classList.toggle('active', lang === 'en');
    document.getElementById('btnTa').classList.toggle('active', lang === 'ta');

    document.querySelectorAll('[data-' + lang + ']').forEach(function(el) {
        el.textContent = el.getAttribute('data-' + lang);
    });

    // Contact block needs special handling (HTML with <br>)
    document.querySelectorAll('[data-' + lang + '-addr]').forEach(function(el) {
        var addr = el.getAttribute('data-' + lang + '-addr');
        var html = '\uD83D\uDCCD ' + addr + '<br>\uD83D\uDCDE ' + shopPhone;
        if (shopEmail) html += ' \u00B7 \u2709 ' + shopEmail;
        el.innerHTML = html;
    });

    document.getElementById('previewTitle').textContent =
        lang === 'ta' ? @json($headerTitleTa) : @json($headerTitleEn);
}
</script>
</body>
</html>
