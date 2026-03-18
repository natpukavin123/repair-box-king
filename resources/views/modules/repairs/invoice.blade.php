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
    if (!function_exists('numWordsTa')) {
        function numWordsTa(float $n): string {
            // ones 1-19
            $u=['','ஒன்று','இரண்டு','மூன்று','நான்கு','ஐந்து',
                'ஆறு','ஏழு','எட்டு','ஒன்பது','பத்து',
                'பதினொன்று','பன்னிரண்டு','பதின்மூன்று','பதினான்கு','பதினைந்து',
                'பதினாறு','பதினேழு','பதினெட்டு','பத்தொன்பது'];
            // tens 20-90
            $t=['','','இருபது','முப்பது','நாற்பது','ஐம்பது',
                'அறுபது','எழுபது','எண்பது','தொண்ணூறு'];
            // hundreds 100-900 (proper Tamil forms, not unit + நூறு)
            $h=['','நூறு','இருநூறு','முந்நூறு','நானூறு','ஐந்நூறு',
                'அறுநூறு','எழுநூறு','எண்ணூறு','தொள்ளாயிரம்'];
            $c=function(int $x)use($u,$t,$h,&$c):string{
                if($x<20)return $u[$x];
                if($x<100)return $t[(int)($x/10)].($x%10?' '.$u[$x%10]:'');
                if($x<1000){$hr=(int)($x/100);$rem=$x%100;return $h[$hr].($rem?' '.$c($rem):'');}
                if($x<100000){$th=(int)($x/1000);$rem=$x%1000;
                    // with remainder → ஆயிரத்து (connector), without → ஆயிரம்
                    $thBase=$th===1?'ஆயிரம்':$c($th).' ஆயிரம்';
                    $thConn=$th===1?'ஆயிரத்து':$c($th).' ஆயிரத்து';
                    return ($rem?$thConn.' '.$c($rem):$thBase);}
                if($x<10000000){$l=(int)($x/100000);$rem=$x%100000;return($l===1?'ஒரு இலட்சம்':$c($l).' இலட்சம்').($rem?' '.$c($rem):'');}
                $cr=(int)($x/10000000);$rem=$x%10000000;return($cr===1?'ஒரு கோடி':$c($cr).' கோடி').($rem?' '.$c($rem):'');
            };
            return $c((int)$n).' ரூபாய் மட்டுமே';
        }
    }
    // Shop (General)
    $shopName    = \App\Models\Setting::getValue('shop_name',    'RepairBox');
    $shopAddress = \App\Models\Setting::getValue('shop_address', 'Your shop address');
    $shopPhone   = \App\Models\Setting::getValue('shop_phone',   '');
    $shopEmail   = \App\Models\Setting::getValue('shop_email',   '');
    $shopSlogan  = \App\Models\Setting::getValue('shop_slogan',  'Your Trusted Mobile Partner');
    $shopIcon    = \App\Models\Setting::getValue('shop_icon',    '');
    $shopUpiId   = \App\Models\Setting::getValue('upi_id',       '');

    // Repair invoice print settings
    $headerTitleEn = \App\Models\Setting::getValue('repair_invoice_header_title_en', 'Repair Invoice');
    $headerTitleTa = \App\Models\Setting::getValue('repair_invoice_header_title_ta', 'பழுதுபார்ப்பு விலைப்பட்டியல்');
    $shopNameTa    = \App\Models\Setting::getValue('receipt_shop_name_ta', '') ?: $shopName;
    $shopSloganTa  = \App\Models\Setting::getValue('receipt_shop_slogan_ta', '') ?: $shopSlogan;
    $shopAddressTa = \App\Models\Setting::getValue('receipt_shop_address_ta', '') ?: $shopAddress;
    $signLabelEn   = \App\Models\Setting::getValue('receipt_sign_label_en', 'Authorised Signatory');
    $signLabelTa   = \App\Models\Setting::getValue('receipt_sign_label_ta', 'அங்கீகரிக்கப்பட்ட கையொப்பம்');
    $footerTextEn  = \App\Models\Setting::getValue('repair_invoice_footer_text',
        'Subject to jurisdiction. Our responsibility ceases as soon as goods leave our premises. Goods once sold will not be taken back.');
    $footerTextTa  = \App\Models\Setting::getValue('repair_invoice_footer_text_ta',
        'நீதிமன்ற அதிகார வரம்புக்கு உட்பட்டது. பொருட்கள் எங்கள் வளாகத்தை விட்டு வெளியேறியவுடன் எங்கள் பொறுப்பு முடிவடைகிறது.');
    $defaultLang   = \App\Models\Setting::getValue('invoice_default_language', 'en');

    // Line items
    $lineItems = collect();
    foreach ($repair->parts as $part) {
        // Derive MRP: products have mrp field; parts use selling_price as listed price
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

    $methodsTa = ['cash'=>'பணம்','card'=>'அட்டை','upi'=>'UPI','bank_transfer'=>'வங்கி மாற்றம்','cheque'=>'காசோலை'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Repair Invoice – {{ $repair->ticket_number }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=Noto+Sans+Tamil:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',Arial,sans-serif;font-size:10px;color:#111;background:#bec2c8;
    -webkit-print-color-adjust:exact;print-color-adjust:exact;}
body.lang-ta .inv,body.lang-ta .inv *:not(.inv-logo-txt):not(.inv-num):not(.serial-sub){font-family:'Noto Sans Tamil','DM Sans',sans-serif;}
body.lang-ta .inv-shop-name{font-size:15px;}

/* ═══════ TOOLBAR ═══════ */
.toolbar{width:297mm;margin:0 auto;padding:14px 4px 10px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.toolbar-left{display:flex;align-items:center;gap:14px;}
.back-btn{display:inline-flex;align-items:center;gap:5px;color:#4b5563;font-size:11px;font-weight:500;
    text-decoration:none;padding:6px 10px;background:rgba(255,255,255,.75);border-radius:6px;border:1px solid rgba(255,255,255,.5);}
.back-btn:hover{background:#fff;color:#111;}
.t-title{font-size:13px;font-weight:700;color:#1f2937;}
.t-sub{font-size:10px;color:#6b7280;margin-top:1px;}
.toolbar-actions{display:flex;align-items:center;gap:10px;}
.lang-picker{display:flex;gap:0;border-radius:8px;overflow:hidden;border:2px solid #e5e7eb;}
.lang-btn{padding:8px 18px;font-size:12px;font-weight:600;border:none;cursor:pointer;
    background:#f9fafb;color:#6b7280;transition:all .15s;font-family:inherit;}
.lang-btn:hover{background:#f3f4f6;}
.lang-btn.active{background:#111;color:#fff;}
.print-btn{display:inline-flex;align-items:center;gap:8px;background:#111;color:#fff;
    font-family:'DM Sans',Arial,sans-serif;font-size:12px;font-weight:600;
    padding:9px 22px 9px 18px;border:none;border-radius:7px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.25);}
.print-btn:hover{background:#2d2d2d;}

/* ═══════ A4 LANDSCAPE SHELL ═══════ */
.a4-shell{width:297mm;height:210mm;margin:0 auto 20mm;background:#fff;display:flex;flex-direction:row;
    box-shadow:0 0 0 1px rgba(0,0,0,.08),0 14px 55px rgba(0,0,0,.4),0 2px 8px rgba(0,0,0,.1);}
.inv-half{width:148.5mm;height:210mm;flex-shrink:0;overflow:hidden;padding:4mm 5mm;background:#fff;}

.cut-zone{width:0;flex-shrink:0;border-left:1.5px dashed #b0b5be;position:relative;}
.cut-label{position:absolute;top:50%;left:-1px;transform:translate(-50%,-50%) rotate(-90deg);
    background:#dde0e5;border:1px dashed #b0b5be;border-radius:2px;
    padding:1px 12px;font-size:7.5px;font-weight:800;letter-spacing:2px;
    color:#8b909a;text-transform:uppercase;white-space:nowrap;display:flex;align-items:center;gap:6px;}
.blank-half{flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;background:#f2f3f5;}
.blank-circle{width:44px;height:44px;border:2px dashed #cdd0d5;border-radius:50%;
    display:flex;align-items:center;justify-content:center;color:#cdd0d5;font-size:20px;}
.blank-title{font-size:10px;font-weight:800;color:#c2c6cb;letter-spacing:2px;text-transform:uppercase;}
.blank-sub{font-size:8.5px;color:#cbd0d6;text-align:center;line-height:1.65;}

/* ══════════════════ INVOICE CARD ══════════════════ */
.inv{width:100%;height:100%;display:flex;flex-direction:column;border:1px solid #c5c9cf;border-radius:3px;overflow:hidden;}

.inv-hdr{background:#111;padding:12px 14px 10px;display:flex;align-items:center;gap:12px;flex-shrink:0;}
.inv-logo{width:44px;height:44px;border:1.5px solid rgba(255,255,255,.22);border-radius:50%;
    overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.05);}
.inv-logo img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
.inv-logo-txt{font-size:6px;font-weight:700;color:rgba(255,255,255,.5);text-align:center;line-height:1.4;}
.inv-shop{flex:1;min-width:0;}
.inv-shop-name{font-family:'Playfair Display',Georgia,serif;font-size:17px;font-weight:900;color:#fff;line-height:1;
    white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.inv-shop-slogan{font-size:6.5px;color:rgba(255,255,255,.32);letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;}
.inv-shop-contact{font-size:7.5px;color:rgba(255,255,255,.48);margin-top:4px;line-height:1.7;}
.inv-badge{text-align:right;flex-shrink:0;}
.inv-type{display:inline-block;background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.16);
    color:rgba(255,255,255,.55);font-size:6px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:2px 6px;border-radius:2px;margin-bottom:2px;}
.inv-num{font-family:'Playfair Display',Georgia,serif;font-size:14px;font-weight:900;color:#fff;line-height:1;}
.inv-date{font-size:7.5px;color:rgba(255,255,255,.4);margin-top:2px;}
.inv-rule{height:3px;background:linear-gradient(90deg,#b8936a,#e5c98a,#b8936a);flex-shrink:0;}

.inv-info{display:flex;border-bottom:1.5px solid #111;flex-shrink:0;}
.inf-cell{flex:1;padding:7px 10px;}
.inf-cell+.inf-cell{border-left:1px solid #e4e7eb;}
.inf-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}
.inf-val{font-size:10px;font-weight:700;color:#111;line-height:1.3;}
.inf-sub{font-size:7.5px;color:#6b7280;margin-top:2px;line-height:1.55;}

.inv-tbl-wrap{border-bottom:1.5px solid #111;flex-shrink:0;}
table.inv-tbl{width:100%;border-collapse:collapse;}
table.inv-tbl thead th{background:#111;color:#fff;font-size:6.5px;font-weight:700;
    letter-spacing:.8px;text-transform:uppercase;padding:4.5px 6px;text-align:center;border-right:1px solid #2d2d2d;}
table.inv-tbl thead th.tl{text-align:left;}
table.inv-tbl thead th:last-child{border-right:none;}
table.inv-tbl tbody td{padding:4px 6px;font-size:9px;border-bottom:1px solid #f0f0f0;border-right:1px solid #f0f0f0;vertical-align:middle;}
table.inv-tbl tbody td:last-child{border-right:none;}
table.inv-tbl tbody tr:nth-child(even){background:#fafafa;}
table.inv-tbl tbody tr.erow{height:16px;}
table.inv-tbl tfoot td{background:#111;color:#fff;font-size:8px;font-weight:700;
    padding:5px 6px;text-align:right;border-right:1px solid #2d2d2d;}
table.inv-tbl tfoot td:last-child{border-right:none;}
.tc{text-align:center;}.tr{text-align:right;}.tl{text-align:left;}
.serial-sub{font-size:6.5px;color:#9ca3af;font-style:italic;margin-top:1px;}

.inv-bottom{display:flex;flex:1;border-bottom:1.5px solid #111;min-height:0;overflow:hidden;}
.inv-bl{flex:1;border-right:1px solid #e4e7eb;padding:7px 10px;display:flex;flex-direction:column;gap:5px;overflow:hidden;}
.inv-br{width:155px;flex-shrink:0;display:flex;flex-direction:column;}
.sec-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}
.words-box{border:1px solid #d1d5db;padding:4px 7px;font-size:7.5px;font-weight:600;color:#111;line-height:1.55;background:#f9fafb;}
.pay-row{display:flex;justify-content:space-between;font-size:8px;padding:2px 0;border-bottom:1px dashed #efefef;color:#374151;}
.pay-row:last-child{border-bottom:none;}
.p-green{color:#059669;font-weight:700;}

.qr-area{display:flex;align-items:center;gap:8px;padding:4px 0;}
.qr-box{width:36px;height:36px;border:1.5px solid #d1d5db;display:flex;align-items:center;justify-content:center;
    font-size:6.5px;font-weight:700;color:#9ca3af;text-align:center;line-height:1.3;background:#f9fafb;}
.qr-meta{flex:1;min-width:0;}
.qr-upi{font-size:7.5px;font-weight:700;color:#111;word-break:break-all;}
.qr-scan{font-size:6.5px;color:#9ca3af;margin-top:1px;}

table.sum-tbl{width:100%;border-collapse:collapse;}
table.sum-tbl td{padding:4px 9px;font-size:8.5px;border-bottom:1px solid #f0f0f0;color:#374151;}
table.sum-tbl td:last-child{text-align:right;font-weight:600;}
table.sum-tbl .row-grand td{background:#111;color:#fff;font-family:'Playfair Display',Georgia,serif;
    font-size:10.5px;font-weight:700;padding:6px 9px;border-bottom:none;}
table.sum-tbl .row-paid td{color:#059669;font-weight:700;border-bottom:none;padding-top:4px;}
table.sum-tbl .row-bal  td{color:#dc2626;font-weight:700;font-size:9.5px;border-bottom:none;}
table.sum-tbl .row-full td{color:#059669;font-weight:700;text-align:center;border-bottom:none;}

.sign-area{padding:5px 9px 7px;text-align:center;border-top:1px solid #f0f0f0;margin-top:auto;}
.sign-blank{height:20px;}
.sign-line{border-top:1px solid #bbb;margin:0 12px 2px;}
.sign-for{font-family:'Playfair Display',Georgia,serif;font-size:8px;font-weight:700;color:#111;}
.sign-auth{font-size:6.5px;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;margin-top:1px;}

.inv-foot{background:#111;padding:4px 10px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.inv-tc{font-size:6.5px;color:rgba(255,255,255,.34);flex:1;margin-right:8px;line-height:1.5;}
.inv-gen{font-size:6.5px;color:rgba(255,255,255,.22);white-space:nowrap;}

@page{size:A4 landscape;margin:0;}
@media print{
    html,body{margin:0;padding:0;background:#fff;width:297mm;height:210mm;}
    .toolbar,.cut-zone,.blank-half{display:none!important;}
    .a4-shell{width:148.5mm;height:210mm;box-shadow:none;margin:0;display:block;}
    .inv-half{width:148.5mm;height:210mm;}
}
</style>
</head>
<body class="{{ $defaultLang === 'ta' ? 'lang-ta' : '' }}">

<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ url('/repairs') }}" class="back-btn">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <div>
            <div class="t-title">Print Preview &nbsp;&middot;&nbsp; <span id="previewTitle">{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</span></div>
            <div class="t-sub">{{ $repair->ticket_number }} &nbsp;&middot;&nbsp; {{ $shopName }}</div>
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
    <!-- LEFT HALF: REPAIR INVOICE -->
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
                    <div class="inv-shop-contact" data-en-addr="{{ e($shopAddress) }}" data-ta-addr="{{ e($shopAddressTa) }}">
                        &#128205; {{ $defaultLang === 'ta' ? $shopAddressTa : $shopAddress }}<br>
                        &#128222; {{ $shopPhone }}@if($shopEmail) &middot; &#9993; {{ $shopEmail }}@endif
                    </div>
                </div>
                <div class="inv-badge">
                    <div class="inv-type" data-en="{{ e($headerTitleEn) }}" data-ta="{{ e($headerTitleTa) }}">{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</div>
                    <div class="inv-num">#{{ $repair->ticket_number }}</div>
                    <div class="inv-date">{{ $repair->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="inv-rule"></div>

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
                            <td class="tr" style="color:#6b7280;">@if($item['mrp'] > $item['rate']){{ number_format($item['mrp'],2) }}@else&mdash;@endif</td>
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
                        <div class="sign-auth" data-en="{{ e($signLabelEn) }}" data-ta="{{ e($signLabelTa) }}">{{ $defaultLang === 'ta' ? $signLabelTa : $signLabelEn }}</div>
                    </div>
                </div>
            </div>

            <div class="inv-foot">
                <div class="inv-tc" data-en="{{ e($footerTextEn) }}" data-ta="{{ e($footerTextTa) }}">{{ $defaultLang === 'ta' ? $footerTextTa : $footerTextEn }}</div>
                <div class="inv-gen">Tracking: {{ $repair->tracking_id ?? '—' }} &nbsp;|&nbsp; E &amp; O.E.</div>
            </div>

        </div>
    </div>

    <!-- CUT LINE -->
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

    <!-- RIGHT HALF: blank -->
    <div class="blank-half">
        <div class="blank-circle">&#8629;</div>
        <div class="blank-title">Blank &mdash; Reuse</div>
        <div class="blank-sub">Cut vertically along dashed line<br>Reuse right half for next print</div>
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

    // Amount in words box (has data-en / data-ta already handled above, just ensure)
    var awb = document.getElementById('amtWordsBox');
    if (awb) awb.textContent = awb.getAttribute('data-' + lang);

    // Contact block
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
