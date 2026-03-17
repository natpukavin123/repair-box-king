@php
    $shopName    = \App\Models\Setting::getValue('shop_name',    'RepairBox');
    $shopAddress = \App\Models\Setting::getValue('shop_address', 'Your shop address');
    $shopPhone   = \App\Models\Setting::getValue('shop_phone',   '');
    $shopEmail   = \App\Models\Setting::getValue('shop_email',   '');
    $shopSlogan  = \App\Models\Setting::getValue('shop_slogan',  'Your Trusted Mobile Partner');
    $shopIcon    = \App\Models\Setting::getValue('shop_icon',    '');
    $footerText  = \App\Models\Setting::getValue('invoice_footer_text',
        'Keep this receipt to claim your device. Unclaimed devices after 30 days are not our responsibility.');

    $advancePaid = $repair->payments->where('direction','IN')->where('payment_type','advance')->sum('amount');
    $repairStatus = ucfirst(str_replace('_',' ',$repair->status ?? 'pending'));
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Repair {{ $repair->ticket_number }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',Arial,sans-serif;font-size:10px;color:#111;background:#bec2c8;
    -webkit-print-color-adjust:exact;print-color-adjust:exact;}

/* ═══════ TOOLBAR ═══════ */
.toolbar{width:297mm;margin:0 auto;padding:14px 4px 10px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.toolbar-left{display:flex;align-items:center;gap:14px;}
.back-btn{display:inline-flex;align-items:center;gap:5px;color:#4b5563;font-size:11px;font-weight:500;
    text-decoration:none;padding:6px 10px;background:rgba(255,255,255,.75);border-radius:6px;border:1px solid rgba(255,255,255,.5);}
.back-btn:hover{background:#fff;color:#111;}
.t-title{font-size:13px;font-weight:700;color:#1f2937;}
.t-sub{font-size:10px;color:#6b7280;margin-top:1px;}
.print-btn{display:inline-flex;align-items:center;gap:8px;background:#111;color:#fff;
    font-family:'DM Sans',Arial,sans-serif;font-size:12px;font-weight:600;
    padding:9px 22px 9px 18px;border:none;border-radius:7px;cursor:pointer;box-shadow:0 2px 8px rgba(0,0,0,.25);}
.print-btn:hover{background:#2d2d2d;}

/* ═══════ A4 LANDSCAPE SHELL ═══════ */
.a4-shell{width:297mm;height:210mm;margin:0 auto 20mm;background:#fff;display:flex;flex-direction:row;
    box-shadow:0 0 0 1px rgba(0,0,0,.08),0 14px 55px rgba(0,0,0,.4),0 2px 8px rgba(0,0,0,.1);}
.inv-half{width:148.5mm;height:210mm;flex-shrink:0;overflow:hidden;padding:4mm 5mm;background:#fff;}

/* CUT LINE */
.cut-zone{width:0;flex-shrink:0;border-left:1.5px dashed #b0b5be;position:relative;}
.cut-label{position:absolute;top:50%;left:-1px;transform:translate(-50%,-50%) rotate(-90deg);
    background:#dde0e5;border:1px dashed #b0b5be;border-radius:2px;
    padding:1px 12px;font-size:7.5px;font-weight:800;letter-spacing:2px;
    color:#8b909a;text-transform:uppercase;white-space:nowrap;display:flex;align-items:center;gap:6px;}

/* BLANK HALF */
.blank-half{flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;background:#f2f3f5;}
.blank-circle{width:44px;height:44px;border:2px dashed #cdd0d5;border-radius:50%;
    display:flex;align-items:center;justify-content:center;color:#cdd0d5;font-size:20px;}
.blank-title{font-size:10px;font-weight:800;color:#c2c6cb;letter-spacing:2px;text-transform:uppercase;}
.blank-sub{font-size:8.5px;color:#cbd0d6;text-align:center;line-height:1.65;}

/* ══════════════════ REPAIR CARD ══════════════════ */
.inv{width:100%;height:100%;display:flex;flex-direction:column;border:1px solid #c5c9cf;border-radius:3px;overflow:hidden;}

/* Header */
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

/* Info grid */
.inv-info{display:flex;border-bottom:1.5px solid #111;flex-shrink:0;}
.inf-cell{flex:1;padding:7px 10px;}
.inf-cell+.inf-cell{border-left:1px solid #e4e7eb;}
.inf-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}
.inf-val{font-size:10px;font-weight:700;color:#111;line-height:1.3;}
.inf-sub{font-size:7.5px;color:#6b7280;margin-top:2px;line-height:1.55;}
.status-badge{display:inline-block;padding:2.5px 9px;border-radius:3px;font-size:7px;font-weight:800;letter-spacing:.8px;
    text-transform:uppercase;background:#fff3cd;color:#856404;}

/* Cost banner */
.cost-banner{border-bottom:1.5px solid #111;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-shrink:0;}
.cost-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}
.cost-val{font-family:'Playfair Display',Georgia,serif;font-size:26px;font-weight:900;color:#111;line-height:1;}
.adv-val{font-size:12px;font-weight:700;color:#059669;}
.adv-zero{font-size:9px;color:#d1d5db;}

/* Problem */
.prob-row{border-bottom:1.5px solid #111;padding:7px 12px;flex-shrink:0;}
.prob-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}
.prob-text{font-size:9px;color:#374151;line-height:1.6;white-space:pre-line;}

/* Bottom */
.inv-bottom{display:flex;flex:1;border-bottom:1.5px solid #111;min-height:0;overflow:hidden;}
.inv-bl{flex:1;border-right:1px solid #e4e7eb;padding:8px 10px;display:flex;flex-direction:column;gap:6px;overflow:hidden;}
.inv-br{width:155px;flex-shrink:0;display:flex;flex-direction:column;}
.sec-lbl{font-size:6.5px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;}

/* Tracking */
.track-box{border:1.5px solid #111;padding:7px 10px;text-align:center;background:#f9fafb;}
.track-id{font-family:'Playfair Display',Georgia,serif;font-size:20px;font-weight:900;color:#111;letter-spacing:3px;line-height:1;}
.track-hint{font-size:7px;color:#9ca3af;margin-top:3px;}

/* Payments */
.pay-row{display:flex;justify-content:space-between;font-size:8px;padding:2px 0;border-bottom:1px dashed #efefef;color:#374151;}
.pay-row:last-child{border-bottom:none;}
.p-green{color:#059669;font-weight:700;}

/* Notes */
.note-list{display:flex;flex-direction:column;gap:2px;}
.note-item{font-size:8px;color:#4b5563;line-height:1.55;padding:2px 0;border-bottom:1px dashed #f0f0f0;}
.note-item:last-child{border-bottom:none;}

.sign-area{padding:5px 9px 7px;text-align:center;border-top:1px solid #f0f0f0;margin-top:auto;}
.sign-blank{height:18px;}
.sign-line{border-top:1px solid #bbb;margin:0 12px 2px;}
.sign-for{font-family:'Playfair Display',Georgia,serif;font-size:8px;font-weight:700;color:#111;}
.sign-auth{font-size:6.5px;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;margin-top:1px;}

.inv-foot{background:#111;padding:4px 10px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.inv-tc{font-size:6.5px;color:rgba(255,255,255,.34);flex:1;margin-right:8px;line-height:1.5;}
.inv-gen{font-size:6.5px;color:rgba(255,255,255,.22);white-space:nowrap;}

/* ═══════ PRINT ═══════ */
@page{size:A4 landscape;margin:0;}
@media print{
    html,body{margin:0;padding:0;background:#fff;width:297mm;height:210mm;}
    .toolbar,.cut-zone,.blank-half{display:none!important;}
    .a4-shell{width:148.5mm;height:210mm;box-shadow:none;margin:0;display:block;}
    .inv-half{width:148.5mm;height:210mm;}
}
</style>
</head>
<body>

<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ url('/repairs') }}" class="back-btn">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <div>
            <div class="t-title">Print Preview &nbsp;&middot;&nbsp; Repair Receipt</div>
            <div class="t-sub">{{ $repair->ticket_number }} &nbsp;&middot;&nbsp; {{ $shopName }} &nbsp;&middot;&nbsp; A4 landscape, left half only &mdash; cut vertically to reuse right half</div>
        </div>
    </div>
    <button class="print-btn" onclick="window.print()">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Print Receipt
    </button>
</div>

<div class="a4-shell">

    <!-- LEFT HALF: REPAIR RECEIPT (148.5mm × 210mm) -->
    <div class="inv-half">
        <div class="inv">

            <div class="inv-hdr">
                @if($shopIcon)
                <div class="inv-logo"><img src="{{ asset('storage/'.$shopIcon) }}" alt="{{ $shopName }}"></div>
                @else
                <div class="inv-logo"><div class="inv-logo-txt">REPAIR<br>BOX</div></div>
                @endif
                <div class="inv-shop">
                    <div class="inv-shop-name">{{ $shopName }}</div>
                    <div class="inv-shop-slogan">{{ $shopSlogan }}</div>
                    <div class="inv-shop-contact">
                        &#128205; {{ $shopAddress }}<br>
                        &#128222; {{ $shopPhone }}@if($shopEmail) &middot; &#9993; {{ $shopEmail }}@endif
                    </div>
                </div>
                <div class="inv-badge">
                    <div class="inv-type">Repair Receipt</div>
                    <div class="inv-num">#{{ $repair->ticket_number }}</div>
                    <div class="inv-date">{{ $repair->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div class="inv-rule"></div>

            <!-- Info: customer | device | status -->
            <div class="inv-info">
                <div class="inf-cell">
                    <div class="inf-lbl">Customer</div>
                    <div class="inf-val">{{ $repair->customer?->name ?? 'Walk-in Customer' }}</div>
                    @if($repair->customer)
                    <div class="inf-sub">
                        @if($repair->customer->mobile_number)&#128222; {{ $repair->customer->mobile_number }}@endif
                        @if($repair->customer->address)<br>{{ $repair->customer->address }}@endif
                    </div>
                    @endif
                </div>
                <div class="inf-cell">
                    <div class="inf-lbl">Device</div>
                    <div class="inf-val">{{ $repair->device_brand }} {{ $repair->device_model }}</div>
                    <div class="inf-sub">
                        @if($repair->imei)IMEI: {{ $repair->imei }}<br>@endif
                        {{ $repair->created_at->format('d M Y, g:i A') }}<br>
                        @if($repair->expected_delivery_date)Est: {{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d M Y') }}@endif
                    </div>
                </div>
            </div>

            <!-- Cost + Status -->
            <div class="cost-banner">
                <div>
                    <div class="cost-lbl">Estimated Repair Cost</div>
                    <div class="cost-val">&#8377;{{ number_format($repair->estimated_cost, 2) }}</div>
                </div>
                <div style="text-align:center;">
                    <div class="cost-lbl">Status</div>
                    <span class="status-badge">{{ $repairStatus }}</span>
                </div>
                <div style="text-align:right;">
                    <div class="cost-lbl">Advance Paid</div>
                    @if($advancePaid > 0)
                    <div class="adv-val">&#8377;{{ number_format($advancePaid, 2) }}</div>
                    @else
                    <div class="adv-zero">&#8377; 0.00</div>
                    @endif
                </div>
            </div>

            <!-- Problem -->
            @if($repair->problem_description)
            <div class="prob-row">
                <div class="prob-lbl">Problem Description</div>
                <div class="prob-text">{{ $repair->problem_description }}</div>
            </div>
            @endif

            <!-- Bottom: tracking+payments | notes+signature -->
            <div class="inv-bottom">
                <div class="inv-bl">
                    <div>
                        <div class="sec-lbl">Tracking ID</div>
                        <div class="track-box">
                            <div class="track-id">{{ $repair->tracking_id ?? $repair->ticket_number }}</div>
                            <div class="track-hint">Use this ID to track your repair status</div>
                        </div>
                    </div>
                    @if($repair->payments->where('direction','IN')->count())
                    <div>
                        <div class="sec-lbl">Advance Payments</div>
                        @foreach($repair->payments->where('direction','IN') as $p)
                        <div class="pay-row">
                            <span>{{ ucfirst(str_replace('_',' ',$p->payment_method)) }}
                                @if($p->transaction_reference)<span style="color:#9ca3af;font-size:6.5px;">({{ $p->transaction_reference }})</span>@endif
                            </span>
                            <span class="p-green">+&#8377;{{ number_format($p->amount,2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div class="inv-br">
                    <div style="padding:7px 10px;flex:1;">
                        <div class="sec-lbl">Important Notes</div>
                        <div class="note-list">
                            <div class="note-item">&#10033; Keep this receipt to claim your device.</div>
                            <div class="note-item">&#10033; Estimated cost may change upon diagnosis.</div>
                            <div class="note-item">&#10033; Data backup is customer&apos;s responsibility.</div>
                            <div class="note-item">&#10033; Unclaimed devices after 30 days &mdash; not our liability.</div>
                        </div>
                    </div>
                    <div class="sign-area">
                        <div class="sign-blank"></div>
                        <div class="sign-line"></div>
                        <div class="sign-for">For {{ $shopName }}</div>
                        <div class="sign-auth">Authorised Signatory</div>
                    </div>
                </div>
            </div>

            <div class="inv-foot">
                <div class="inv-tc">{{ $footerText }}</div>
                <div class="inv-gen">Ticket #{{ $repair->ticket_number }}</div>
            </div>

        </div>
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

    <!-- RIGHT HALF: blank (screen only) -->
    <div class="blank-half">
        <div class="blank-circle">&#8629;</div>
        <div class="blank-title">Blank &mdash; Reuse</div>
        <div class="blank-sub">Cut vertically along dashed line<br>Reuse right half for next print</div>
    </div>

</div>
</body>
</html>
