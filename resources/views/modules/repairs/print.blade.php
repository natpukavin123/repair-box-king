@php
    // Shop (General)
    $shopName    = \App\Models\Setting::getValue('shop_name',    'RepairBox');
    $shopAddress = \App\Models\Setting::getValue('shop_address', 'Your shop address');
    $shopPhone   = \App\Models\Setting::getValue('shop_phone',   '');
    $shopEmail   = \App\Models\Setting::getValue('shop_email',   '');
    $shopSlogan  = \App\Models\Setting::getValue('shop_slogan',  'Your Trusted Mobile Partner');
    $shopIcon    = \App\Models\Setting::getValue('shop_icon',    '');

    // Receipt print settings (dynamic)
    $headerTitleEn = \App\Models\Setting::getValue('receipt_header_title_en', 'Repair Receipt');
    $headerTitleTa = \App\Models\Setting::getValue('receipt_header_title_ta', 'பழுதுபார்ப்பு ரசீது');
    $shopNameTa    = \App\Models\Setting::getValue('receipt_shop_name_ta', '') ?: $shopName;
    $shopSloganTa  = \App\Models\Setting::getValue('receipt_shop_slogan_ta', '') ?: $shopSlogan;
    $shopAddressTa = \App\Models\Setting::getValue('receipt_shop_address_ta', '') ?: $shopAddress;
    $signLabelEn   = \App\Models\Setting::getValue('receipt_sign_label_en', 'Authorised Signatory');
    $signLabelTa   = \App\Models\Setting::getValue('receipt_sign_label_ta', 'அங்கீகரிக்கப்பட்ட கையொப்பம்');
    $footerTextEn  = \App\Models\Setting::getValue('receipt_footer_text',
        'Keep this receipt to claim your device. Unclaimed devices after 30 days are not our responsibility.');
    $footerTextTa  = \App\Models\Setting::getValue('receipt_footer_text_ta',
        'உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள். 30 நாட்களுக்குப் பிறகு உரிமை கோரப்படாத சாதனங்களுக்கு நாங்கள் பொறுப்பல்ல.');
    $notesEn       = \App\Models\Setting::getValue('receipt_notes_en',
        "Keep this receipt to claim your device.\nEstimated cost may change upon diagnosis.\nData backup is customer's responsibility.\nUnclaimed devices after 30 days — not our liability.");
    $notesTa       = \App\Models\Setting::getValue('receipt_notes_ta',
        "உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.\nமதிப்பீட்டுச் செலவு ஆய்வுக்குப் பிறகு மாறலாம்.\nதரவு காப்புப்பிரதி வாடிக்கையாளரின் பொறுப்பு.\n30 நாட்களுக்குப் பிறகு உரிமை கோரப்படாத சாதனங்கள் — எங்கள் பொறுப்பல்ல.");
    $defaultLang   = \App\Models\Setting::getValue('invoice_default_language', 'en');

    $advancePaid   = $repair->payments->where('direction','IN')->where('payment_type','advance')->sum('amount');
    $repairStatus  = ucfirst(str_replace('_',' ',$repair->status ?? 'pending'));
    $notesEnArr    = array_filter(explode("\n", $notesEn));
    $notesTaArr    = array_filter(explode("\n", $notesTa));
    $methodsTa     = ['cash'=>'பணம்','card'=>'அட்டை','upi'=>'UPI','bank_transfer'=>'வங்கி மாற்றம்','cheque'=>'காசோலை'];

    // Tamil status map
    $statusTa = [
        'received'=>'பெறப்பட்டது','in_progress'=>'பணியில்','completed'=>'முடிந்தது',
        'payment'=>'கட்டணம்','closed'=>'மூடப்பட்டது','cancelled'=>'ரத்து',
        'Received'=>'பெறப்பட்டது','In Progress'=>'பணியில்','Completed'=>'முடிந்தது',
        'Payment'=>'கட்டணம்','Closed'=>'மூடப்பட்டது','Cancelled'=>'ரத்து',
    ];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Repair {{ $repair->ticket_number }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=Noto+Sans+Tamil:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#000;background:#ccc;}
body.lang-ta .inv,body.lang-ta .inv *:not(.inv-logo-txt):not(.inv-num):not(.track-id){font-family:'Noto Sans Tamil','DM Sans',sans-serif;}
body.lang-ta .inv-shop-name{font-size:18px;}

/* ═══════ TOOLBAR ═══════ */
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

.cut-zone{width:0;flex-shrink:0;border-left:1.5px dashed #999;position:relative;}
.cut-label{position:absolute;top:50%;left:-1px;transform:translate(-50%,-50%) rotate(-90deg);
    background:#ddd;border:1px dashed #999;border-radius:2px;padding:1px 12px;font-size:8px;
    font-weight:800;letter-spacing:2px;color:#666;text-transform:uppercase;white-space:nowrap;display:flex;align-items:center;gap:6px;}
.blank-half{flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;background:#f5f5f5;}
.blank-circle{width:44px;height:44px;border:2px dashed #bbb;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#bbb;font-size:20px;}
.blank-title{font-size:11px;font-weight:800;color:#aaa;letter-spacing:2px;text-transform:uppercase;}
.blank-sub{font-size:9px;color:#aaa;text-align:center;line-height:1.65;}

/* ══════════════════ REPAIR CARD ══════════════════ */
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
.inv-num{font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:900;color:#fff;background:#000;padding:2px 8px;display:inline-block;letter-spacing:.4px;line-height:1.6;}
.inv-date{font-size:9px;color:#000;margin-top:3px;}
.inv-rule{height:0;flex-shrink:0;}

.inv-info{display:flex;border-bottom:2px solid #000;flex-shrink:0;}
.inf-cell{flex:1;padding:8px 10px;}
.inf-cell+.inf-cell{border-left:1.5px solid #000;}
.inf-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:3px;}
.inf-val{font-size:12px;font-weight:700;color:#000;line-height:1.3;}
.inf-sub{font-size:9px;color:#000;margin-top:2px;line-height:1.55;}
.status-badge{display:inline-block;padding:3px 10px;border:1.5px solid #000;font-size:9px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;background:#fff;color:#000;}

.cost-banner{background:repeating-linear-gradient(45deg,#f6f6f6 0,#f6f6f6 1px,#fff 1px,#fff 9px);border-bottom:2px solid #000;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-shrink:0;}
.cost-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#666;margin-bottom:4px;}
.cost-val{font-family:'DM Sans',Arial,sans-serif;font-size:26px;font-weight:900;color:#000;line-height:1;letter-spacing:-.5px;}
.adv-val{font-family:'DM Sans',Arial,sans-serif;font-size:18px;font-weight:900;color:#000;line-height:1;}
.adv-zero{font-size:8px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:2px;border:1px dashed #ccc;padding:2px 7px;display:inline-block;}

.prob-row{border-bottom:2px solid #000;padding:7px 12px;flex-shrink:0;}
.prob-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:3px;}
.prob-text{font-size:11px;color:#000;line-height:1.6;white-space:pre-line;}

.inv-bottom{display:flex;flex:1;border-bottom:2px solid #000;min-height:0;overflow:hidden;}
.inv-bl{flex:1;border-right:1.5px solid #000;padding:8px 10px;display:flex;flex-direction:column;gap:6px;overflow:hidden;}
.inv-br{width:155px;flex-shrink:0;display:flex;flex-direction:column;}
.sec-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:2px;}

.track-box{border:2px solid #000;padding:7px 10px;text-align:center;background:#fff;}
.track-id{font-family:'Courier New',Courier,monospace;font-size:17px;font-weight:700;color:#000;letter-spacing:2px;line-height:1.2;word-break:break-all;}
.track-hint{font-size:8px;color:#000;margin-top:3px;}

.pay-row{display:flex;justify-content:space-between;font-size:10px;padding:2px 0;border-bottom:1px solid #000;color:#000;}
.pay-row:last-child{border-bottom:none;}
.p-green{color:#000;font-weight:700;}

.note-list{display:flex;flex-direction:column;gap:2px;}
.note-item{font-size:9px;color:#000;line-height:1.55;padding:2px 0;border-bottom:1px solid #000;}
.note-item:last-child{border-bottom:none;}

.sign-area{padding:5px 9px 7px;text-align:center;border-top:1.5px solid #000;margin-top:auto;}
.sign-blank{height:18px;}
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
<body class="{{ $defaultLang === 'ta' ? 'lang-ta' : '' }}" data-default-lang="{{ $defaultLang }}">

<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ url('/admin/repairs') }}" class="back-btn">
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
            Print Receipt
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

    <!-- RIGHT HALF: REPAIR RECEIPT -->
    <div class="inv-half">
        <div class="inv">

            <div class="inv-hdr">
                @if($shopIcon)
                <div class="inv-logo"><img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}"></div>
                @else
                <div class="inv-logo"><div class="inv-logo-txt">REPAIR<br>BOX</div></div>
                @endif
                <div class="inv-shop">
                    <div class="inv-shop-name" data-en="{{ e($shopName) }}" data-ta="{{ e($shopNameTa) }}" data-setting-ta="receipt_shop_name_ta">{{ $defaultLang === 'ta' ? $shopNameTa : $shopName }}</div>
                    <div class="inv-shop-slogan" data-en="{{ e($shopSlogan) }}" data-ta="{{ e($shopSloganTa) }}" data-setting-ta="receipt_shop_slogan_ta">{{ $defaultLang === 'ta' ? $shopSloganTa : $shopSlogan }}</div>
                    <div class="inv-shop-contact" data-en-addr="{{ e($shopAddress) }}" data-ta-addr="{{ e($shopAddressTa) }}">
                        &#128205; {{ $defaultLang === 'ta' ? $shopAddressTa : $shopAddress }}<br>
                        &#128222; {{ $shopPhone }}@if($shopEmail) &middot; &#9993; {{ $shopEmail }}@endif
                    </div>
                </div>
                <div class="inv-badge">
                    <div class="inv-type" data-en="{{ e($headerTitleEn) }}" data-ta="{{ e($headerTitleTa) }}" data-setting-en="receipt_header_title_en" data-setting-ta="receipt_header_title_ta">{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</div>
                    <div class="inv-num">#{{ $repair->ticket_number }}</div>
                    <div class="inv-date">{{ $repair->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="inv-rule"></div>

            <!-- Info -->
            <div class="inv-info">
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Customer" data-ta="வாடிக்கையாளர்">{{ $defaultLang === 'ta' ? 'வாடிக்கையாளர்' : 'Customer' }}</div>
                    <div class="inf-val" data-en="{{ e($repair->customer?->name ?? 'Walk-in Customer') }}" data-ta="{{ e($repair->customer?->name ?? 'நடை வாடிக்கையாளர்') }}">{{ $repair->customer?->name ?? ($defaultLang === 'ta' ? 'நடை வாடிக்கையாளர்' : 'Walk-in Customer') }}</div>
                    @if($repair->customer)
                    <div class="inf-sub">
                        @if($repair->customer->mobile_number)&#128222; {{ $repair->customer->mobile_number }}@endif
                        @if($repair->customer->address)<br>{{ $repair->customer->address }}@endif
                    </div>
                    @endif
                </div>
                <div class="inf-cell">
                    <div class="inf-lbl" data-en="Device" data-ta="சாதனம்">{{ $defaultLang === 'ta' ? 'சாதனம்' : 'Device' }}</div>
                    <div class="inf-val">{{ $repair->device_brand }} {{ $repair->device_model }}</div>
                    <div class="inf-sub">
                        @if($repair->imei)<span data-en="IMEI" data-ta="IMEI">IMEI</span>: {{ $repair->imei }}<br>@endif
                        {{ $repair->created_at->format('d M Y, g:i A') }}<br>
                        @if($repair->expected_delivery_date)<span data-en="Est" data-ta="எதிர்பார்ப்பு">{{ $defaultLang === 'ta' ? 'எதிர்பார்ப்பு' : 'Est' }}</span>: {{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d M Y') }}@endif
                    </div>
                </div>
            </div>

            <!-- Cost + Status -->
            <div class="cost-banner">
                <div>
                    <div class="cost-lbl" data-en="Estimated Repair Cost" data-ta="மதிப்பீட்டு பழுது செலவு">{{ $defaultLang === 'ta' ? 'மதிப்பீட்டு பழுது செலவு' : 'Estimated Repair Cost' }}</div>
                    <div class="cost-val">&#8377;{{ number_format($repair->estimated_cost, 2) }}</div>
                </div>
                <div style="text-align:center;">
                    <div class="cost-lbl" data-en="Status" data-ta="நிலை">{{ $defaultLang === 'ta' ? 'நிலை' : 'Status' }}</div>
                    <span class="status-badge" data-en="{{ $repairStatus }}" data-ta="{{ $statusTa[$repairStatus] ?? $repairStatus }}">{{ $defaultLang === 'ta' ? ($statusTa[$repairStatus] ?? $repairStatus) : $repairStatus }}</span>
                </div>
                <div style="text-align:right;">
                    <div class="cost-lbl" data-en="Advance Paid" data-ta="முன்பணம்">{{ $defaultLang === 'ta' ? 'முன்பணம்' : 'Advance Paid' }}</div>
                    @if($advancePaid > 0)
                    <div class="adv-val">&#8377;{{ number_format($advancePaid, 2) }}</div>
                    @else
                    <div class="adv-zero">NIL</div>
                    @endif
                </div>
            </div>

            <!-- Problem -->
            @if($repair->problem_description)
            <div class="prob-row">
                <div class="prob-lbl" data-en="Problem Description" data-ta="சிக்கல் விவரணை">{{ $defaultLang === 'ta' ? 'சிக்கல் விவரணை' : 'Problem Description' }}</div>
                <div class="prob-text">{{ $repair->problem_description }}</div>
            </div>
            @endif

            <!-- Bottom -->
            <div class="inv-bottom">
                <div class="inv-bl">
                    <div>
                        <div class="sec-lbl" data-en="Tracking ID" data-ta="கண்காணிப்பு எண்">{{ $defaultLang === 'ta' ? 'கண்காணிப்பு எண்' : 'Tracking ID' }}</div>
                        <div class="track-box">
                            <div class="track-id">{{ $repair->tracking_id ?? $repair->ticket_number }}</div>
                            <div class="track-hint" data-en="Use this ID to track your repair status" data-ta="உங்கள் பழுது நிலையை கண்காணிக்க இந்த எண்ணைப் பயன்படுத்தவும்">{{ $defaultLang === 'ta' ? 'உங்கள் பழுது நிலையை கண்காணிக்க இந்த எண்ணைப் பயன்படுத்தவும்' : 'Use this ID to track your repair status' }}</div>
                        </div>
                    </div>
                    @if($repair->payments->where('direction','IN')->count())
                    <div>
                        <div class="sec-lbl" data-en="Advance Payments" data-ta="முன்பண விவரங்கள்">{{ $defaultLang === 'ta' ? 'முன்பண விவரங்கள்' : 'Advance Payments' }}</div>
                        @foreach($repair->payments->where('direction','IN') as $p)
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
                    <div style="padding:7px 10px;flex:1;" data-setting-en="receipt_notes_en" data-setting-ta="receipt_notes_ta">
                        <div class="sec-lbl" data-en="Important Notes" data-ta="முக்கிய குறிப்புகள்">{{ $defaultLang === 'ta' ? 'முக்கிய குறிப்புகள்' : 'Important Notes' }}</div>
                        <div class="note-list" id="notesList">
                            @foreach(($defaultLang === 'ta' ? $notesTaArr : $notesEnArr) as $note)
                            <div class="note-item">&#10033; {{ $note }}</div>
                            @endforeach
                        </div>
                    </div>
                    <div class="sign-area">
                        <div class="sign-blank"></div>
                        <div class="sign-line"></div>
                        <div class="sign-for" data-en="For {{ e($shopName) }}" data-ta="{{ e($shopNameTa) }} சார்பாக">{{ $defaultLang === 'ta' ? $shopNameTa . ' சார்பாக' : 'For ' . $shopName }}</div>
                        <div class="sign-auth" data-en="{{ e($signLabelEn) }}" data-ta="{{ e($signLabelTa) }}" data-setting-en="receipt_sign_label_en" data-setting-ta="receipt_sign_label_ta">{{ $defaultLang === 'ta' ? $signLabelTa : $signLabelEn }}</div>
                    </div>
                </div>
            </div>

            <div class="inv-foot">
                <div class="inv-tc" data-en="{{ e($footerTextEn) }}" data-ta="{{ e($footerTextTa) }}" data-setting-en="receipt_footer_text" data-setting-ta="receipt_footer_text_ta">{{ $defaultLang === 'ta' ? $footerTextTa : $footerTextEn }}</div>
                <div class="inv-gen">
                    {{ $shopName }} &nbsp;|&nbsp; &#128222; {{ $shopPhone }}
                    @if($shopEmail) &nbsp;|&nbsp; &#9993; {{ $shopEmail }} @endif
                    &nbsp;|&nbsp; #{{ $repair->ticket_number }}
                </div>
            </div>

        </div>
    </div>
</div>

<script>
var shopPhone = @json($shopPhone);
var shopEmail = @json($shopEmail);
var notesEn = @json($notesEnArr);
var notesTa = @json($notesTaArr);

function switchLang(lang) {
    document.body.classList.toggle('lang-ta', lang === 'ta');
    document.getElementById('btnEn').classList.toggle('active', lang === 'en');
    document.getElementById('btnTa').classList.toggle('active', lang === 'ta');

    document.querySelectorAll('[data-' + lang + ']').forEach(function(el) {
        el.textContent = el.getAttribute('data-' + lang);
    });

    // Contact block
    document.querySelectorAll('[data-' + lang + '-addr]').forEach(function(el) {
        var addr = el.getAttribute('data-' + lang + '-addr');
        var html = '\uD83D\uDCCD ' + addr + '<br>\uD83D\uDCDE ' + shopPhone;
        if (shopEmail) html += ' \u00B7 \u2709 ' + shopEmail;
        el.innerHTML = html;
    });

    // Notes list
    var notes = lang === 'ta' ? notesTa : notesEn;
    var container = document.getElementById('notesList');
    container.innerHTML = '';
    (Array.isArray(notes) ? notes : Object.values(notes)).forEach(function(note) {
        var div = document.createElement('div');
        div.className = 'note-item';
        div.textContent = '\u2733 ' + note;
        container.appendChild(div);
    });

    document.getElementById('previewTitle').textContent =
        lang === 'ta' ? @json($headerTitleTa) : @json($headerTitleEn);
}

// ── Edit Mode (iframe embedding in settings page) ──
(function() {
    var params = new URLSearchParams(window.location.search);
    if (params.get('edit') !== '1') return;

    var style = document.createElement('style');
    style.textContent = '.toolbar{display:none!important;}body{background:#e8eaed;margin:0;display:flex;justify-content:center;padding:20px 0;min-height:100vh;}.a4-shell{width:148.5mm;height:210mm;margin:0 auto;box-shadow:0 2px 16px rgba(0,0,0,.18);flex-direction:column;}.blank-half,.cut-zone{display:none!important;}.inv-half{width:148.5mm;height:210mm;flex-shrink:0;padding:4mm 5mm;}@media print{body{background:#fff!important;padding:0!important;display:block!important;}html,body{width:297mm;height:210mm;margin:0;padding:0;}.a4-shell{width:297mm;height:210mm;box-shadow:none;display:flex;flex-direction:row;margin:0;}.blank-half{display:block!important;flex:1;background:#fff!important;}.blank-half *{display:none!important;}.inv-half{width:148.5mm;height:210mm;}.cut-zone{display:none!important;}[data-setting-en],[data-setting-ta],[data-setting-en]:focus,[data-setting-ta]:focus{background:transparent!important;border:none!important;border-radius:0!important;box-shadow:none!important;padding:0!important;cursor:default!important;}}';
    document.head.appendChild(style);

    var currentLang = document.querySelector('[data-default-lang]')?.dataset.defaultLang || 'en';

    document.querySelectorAll('[data-setting-en], [data-setting-ta]').forEach(function(el) {
        el.setAttribute('contenteditable', 'true');
        el.style.cursor = 'text';
        el.style.outline = 'none';
        el.style.background = 'rgba(59,130,246,0.06)';
        el.style.border = '1.5px dashed rgba(59,130,246,0.45)';
        el.style.borderRadius = '3px';
        el.style.padding = '1px 5px';
        el.style.transition = 'all 0.2s';

        el.addEventListener('focus', function() {
            el.style.background = 'rgba(59,130,246,0.12)';
            el.style.borderColor = 'rgba(59,130,246,0.8)';
            el.style.boxShadow = '0 0 0 3px rgba(59,130,246,0.15)';
        });
        el.addEventListener('blur', function() {
            el.style.background = 'rgba(59,130,246,0.06)';
            el.style.borderColor = 'rgba(59,130,246,0.45)';
            el.style.boxShadow = 'none';
            var settingKey = el.getAttribute('data-setting-' + currentLang);
            if (settingKey) {
                var value = el.innerText.trim();
                el.setAttribute('data-' + currentLang, value);
                window.parent.postMessage({ type: 'setting-changed', key: settingKey, value: value }, '*');
            }
        });
    });

    var origSwitchLang = window.switchLang;
    window.switchLang = function(lang) {
        currentLang = lang;
        if (origSwitchLang) origSwitchLang(lang);
    };

    window.addEventListener('message', function(event) {
        if (!event.data) return;
        if (event.data.type === 'switch-lang') { window.switchLang(event.data.lang); }
        if (event.data.type === 'init-edit-mode') { currentLang = event.data.lang; if (origSwitchLang) origSwitchLang(event.data.lang); }
    });
})();
</script>
</body>
</html>
