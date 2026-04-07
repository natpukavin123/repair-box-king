{{--
    Shared A4 landscape print layout for: Sales Invoice, Repair Invoice, Repair Receipt.
    Usage: @extends('layouts.print-a4') with @section('printContent')
    Required vars set by each child @php block:
      $pageTitle, $backUrl, $previewTitleEn, $previewTitleTa, $printBtnLabel,
      $docNumber, $docDate, $defaultLang,
      $shopName, $shopNameTa, $shopSlogan, $shopSloganTa, $shopAddress, $shopAddressTa,
      $shopPhone, $shopPhone2, $shopEmail, $shopIcon,
      $headerTitleEn, $headerTitleTa, $footerTextEn, $footerTextTa,
      Optional: $shopNameTaKey, $shopSloganTaKey, $headerTitleEnKey, $headerTitleTaKey,
                $footerTextEnKey, $footerTextTaKey
--}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>{{ $pageTitle }}</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600;700&family=Noto+Sans+Tamil:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'DM Sans',Arial,sans-serif;font-size:12px;color:#000;background:#ccc;}
body.lang-ta .inv,body.lang-ta .inv *:not(.inv-logo-txt):not(.inv-num):not(.serial-sub):not(.track-id){font-family:'Noto Sans Tamil','DM Sans',sans-serif;}
body.lang-ta .inv-shop-name{font-size:18px;}

/* ═══════ TOOLBAR (screen) ═══════ */
.toolbar{width:297mm;margin:0 auto;padding:14px 4px 10px;display:flex;align-items:center;justify-content:space-between;gap:12px;}
.toolbar-left{display:flex;align-items:center;gap:14px;}
.back-btn{display:inline-flex;align-items:center;gap:5px;color:#000;font-size:12px;font-weight:600;
    text-decoration:none;padding:7px 12px;background:#fff;border-radius:7px;border:1px solid #000;}
.back-btn:hover{background:#eee;}
.t-title{font-size:14px;font-weight:700;color:#000;}
.t-sub{font-size:11px;color:#000;margin-top:2px;}
.toolbar-actions{display:flex;align-items:center;gap:10px;}
.lang-picker{display:flex;gap:0;border-radius:8px;overflow:hidden;border:2px solid #000;}
.lang-btn{padding:8px 18px;font-size:12px;font-weight:600;border:none;cursor:pointer;
    background:#fff;color:#000;transition:all .15s;font-family:inherit;}
.lang-btn:hover{background:#eee;}
.lang-btn.active{background:#000;color:#fff;}
.print-btn{display:inline-flex;align-items:center;gap:8px;color:#fff;background:#000;
    font-family:'DM Sans',Arial,sans-serif;font-size:13px;font-weight:600;
    padding:10px 22px 10px 18px;border:none;border-radius:8px;cursor:pointer;}
.print-btn:hover{background:#333;}

/* ═══════ A4 LANDSCAPE SHELL ═══════ */
.a4-shell{width:297mm;height:210mm;margin:0 auto 20mm;background:#fff;display:flex;flex-direction:row;
    box-shadow:0 2px 12px rgba(0,0,0,.3);}
.inv-half{width:148.5mm;height:210mm;flex-shrink:0;overflow:hidden;padding:4mm 5mm;background:#fff;}

.cut-zone{width:0;flex-shrink:0;border-left:1.5px dashed #999;position:relative;}
.cut-label{position:absolute;top:50%;left:-1px;transform:translate(-50%,-50%) rotate(-90deg);
    background:#ddd;border:1px dashed #999;border-radius:2px;
    padding:1px 12px;font-size:8px;font-weight:800;letter-spacing:2px;
    color:#666;text-transform:uppercase;white-space:nowrap;display:flex;align-items:center;gap:6px;}
.blank-half{flex:1;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;background:#f5f5f5;}
.blank-circle{width:44px;height:44px;border:2px dashed #bbb;border-radius:50%;
    display:flex;align-items:center;justify-content:center;color:#bbb;font-size:20px;}
.blank-title{font-size:11px;font-weight:800;color:#aaa;letter-spacing:2px;text-transform:uppercase;}
.blank-sub{font-size:9px;color:#aaa;text-align:center;line-height:1.65;}

/* ══════════════════ INVOICE / RECEIPT CARD ══════════════════ */
.inv{width:100%;height:100%;display:flex;flex-direction:column;border:2px solid #000;overflow:hidden;}

.inv-hdr{background:#fff;padding:12px 14px 10px;display:flex;align-items:center;gap:12px;flex-shrink:0;border-bottom:2px solid #000;}
.inv-logo{width:44px;height:44px;border:2px solid #000;border-radius:50%;
    overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:#fff;}
.inv-logo img{width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;}
.inv-logo-txt{font-size:7px;font-weight:700;color:#000;text-align:center;line-height:1.4;}
.inv-shop{flex:1;min-width:0;}
.inv-shop-name{font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:900;color:#000;line-height:1.15;
    white-space:nowrap;}
.inv-shop-slogan{font-size:8px;color:#000;letter-spacing:1.5px;text-transform:uppercase;margin-top:2px;}
.inv-shop-contact{font-size:9px;color:#000;margin-top:4px;line-height:1.7;}
.inv-badge{text-align:right;flex-shrink:0;}
.inv-type{display:inline-block;border:1.5px solid #000;
    color:#000;font-size:7px;font-weight:700;letter-spacing:2px;text-transform:uppercase;padding:3px 8px;margin-bottom:3px;white-space:nowrap;}
.inv-num{font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:900;color:#000;line-height:1;}
.inv-date{font-size:9px;color:#000;margin-top:2px;}
.inv-rule{height:0;flex-shrink:0;}

.inv-info{display:flex;border-bottom:2px solid #000;flex-shrink:0;}
.inf-cell{flex:1;padding:8px 10px;}
.inf-cell+.inf-cell{border-left:1.5px solid #000;}
.inf-lbl{font-size:8px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;
    color:#000;margin-bottom:3px;}
.inf-lbl::before{display:none;}
.inf-val{font-size:12px;font-weight:700;color:#000;line-height:1.3;}
.inf-sub{font-size:9px;color:#000;margin-top:3px;line-height:1.65;}

.inv-tbl-wrap{border-bottom:2px solid #000;flex-shrink:0;}
table.inv-tbl{width:100%;border-collapse:collapse;}
table.inv-tbl thead th{background:#fff;
    color:#000;font-size:8px;font-weight:700;
    letter-spacing:1px;text-transform:uppercase;padding:5px 6px;
    text-align:center;border:1.5px solid #000;}
table.inv-tbl thead th.tl{text-align:left;}
table.inv-tbl tbody td{padding:5px 6px;font-size:11px;border:1px solid #000;vertical-align:middle;}
table.inv-tbl tbody tr.erow{height:18px;}
table.inv-tbl tfoot td{background:#fff;
    color:#000;font-size:10px;font-weight:700;
    padding:5px 6px;text-align:right;border:1.5px solid #000;}
.tc{text-align:center;}.tr{text-align:right;}.tl{text-align:left;}
.serial-sub{font-size:8px;color:#000;font-style:italic;margin-top:1px;}

.inv-bottom{display:flex;flex:1;border-bottom:2px solid #000;min-height:0;overflow:hidden;}
.inv-bl{flex:1;border-right:1.5px solid #000;padding:7px 10px;display:flex;flex-direction:column;gap:5px;overflow:hidden;}
.inv-br{width:155px;flex-shrink:0;display:flex;flex-direction:column;}
.sec-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:2px;}
.words-box{border:1.5px solid #000;
    padding:5px 8px;font-size:9px;font-weight:600;color:#000;line-height:1.6;
    background:#fff;}
.pay-row{display:flex;justify-content:space-between;font-size:10px;padding:2px 0;border-bottom:1px solid #000;color:#000;}
.pay-row:last-child{border-bottom:none;}
.p-green{color:#000;font-weight:700;}

.qr-area{display:flex;align-items:center;gap:8px;padding:4px 0;}
.qr-box{width:36px;height:36px;border:1.5px solid #000;display:flex;align-items:center;justify-content:center;
    font-size:8px;font-weight:700;color:#000;text-align:center;line-height:1.3;background:#fff;}
.qr-meta{flex:1;min-width:0;}
.qr-upi{font-size:9px;font-weight:700;color:#000;word-break:break-all;}
.qr-scan{font-size:8px;color:#000;margin-top:1px;}

table.sum-tbl{width:100%;border-collapse:collapse;}
table.sum-tbl td{padding:5px 9px;font-size:10px;border-bottom:1px solid #000;color:#000;}
table.sum-tbl td:last-child{text-align:right;font-weight:600;}
table.sum-tbl .row-sub td{color:#000;font-size:9px;}
table.sum-tbl .row-disc td{color:#000;font-weight:700;}
table.sum-tbl .row-grand td{background:#fff;
    color:#000;font-family:'Playfair Display',Georgia,serif;
    font-size:12px;font-weight:900;padding:7px 9px;border:1.5px solid #000;}
table.sum-tbl .row-paid td{color:#000;font-weight:700;border-bottom:1px solid #000;padding:5px 9px;}
table.sum-tbl .row-bal  td{color:#000;font-weight:900;font-size:11px;border-bottom:none;text-decoration:underline;padding:5px 9px;}
table.sum-tbl .row-full td{color:#000;font-weight:900;text-align:center;border-bottom:none;
    font-size:10px;letter-spacing:.5px;padding:6px 9px;}

.sign-area{padding:5px 9px 7px;text-align:center;border-top:1.5px solid #000;margin-top:auto;}
.sign-blank{height:22px;}
.sign-line{border-top:1.5px solid #000;margin:0 14px 3px;}
.sign-for{font-family:'Playfair Display',Georgia,serif;font-size:10px;font-weight:700;color:#000;}
.sign-auth{font-size:8px;color:#000;letter-spacing:1.2px;text-transform:uppercase;margin-top:2px;}

.inv-foot{background:#fff;border-top:2px solid #000;
    padding:5px 10px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.inv-tc{font-size:8px;color:#000;flex:1;margin-right:8px;line-height:1.6;}
.inv-gen{font-size:8px;color:#000;white-space:nowrap;}

/* ═══════ RECEIPT-ONLY STYLES ═══════ */
.status-badge{display:inline-block;padding:3px 10px;border:1.5px solid #000;font-size:9px;font-weight:800;letter-spacing:.8px;text-transform:uppercase;background:#fff;color:#000;}
.cost-banner{background:repeating-linear-gradient(45deg,#f6f6f6 0,#f6f6f6 1px,#fff 1px,#fff 9px);border-bottom:2px solid #000;padding:10px 12px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-shrink:0;}
.cost-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#666;margin-bottom:4px;}
.cost-val{font-family:'DM Sans',Arial,sans-serif;font-size:26px;font-weight:900;color:#000;line-height:1;letter-spacing:-.5px;}
.adv-val{font-family:'DM Sans',Arial,sans-serif;font-size:18px;font-weight:900;color:#000;line-height:1;}
.adv-zero{font-size:8px;font-weight:700;color:#999;text-transform:uppercase;letter-spacing:2px;border:1px dashed #ccc;padding:2px 7px;display:inline-block;}
.prob-row{border-bottom:2px solid #000;padding:7px 12px;flex-shrink:0;}
.prob-lbl{font-size:8px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:#000;margin-bottom:3px;}
.prob-text{font-size:11px;color:#000;line-height:1.6;white-space:pre-line;}
.track-box{border:2px solid #000;padding:7px 10px;text-align:center;background:#fff;}
.track-id{font-family:'Courier New',Courier,monospace;font-size:17px;font-weight:700;color:#000;letter-spacing:2px;line-height:1.2;word-break:break-all;}
.track-hint{font-size:8px;color:#000;margin-top:3px;}
.note-list{display:flex;flex-direction:column;gap:2px;}
.note-item{font-size:9px;color:#000;line-height:1.55;padding:2px 0;border-bottom:1px solid #000;}
.note-item:last-child{border-bottom:none;}

@page{size:A4 landscape;margin:0;}
@media print{
    html,body{margin:0;padding:0;background:#fff;width:297mm;height:210mm;}
    .toolbar,.cut-zone{display:none!important;}
    .a4-shell{width:297mm;height:210mm;box-shadow:none;margin:0;display:flex;flex-direction:row;}
    .blank-half{display:block!important;flex:1;background:#fff!important;}
    .blank-half *{display:none!important;}
    .inv-half{width:148.5mm;height:210mm;}
}
@yield('extraCss')
</style>
</head>
<body class="{{ $defaultLang === 'ta' ? 'lang-ta' : '' }}" data-default-lang="{{ $defaultLang }}">

<div class="toolbar">
    <div class="toolbar-left">
        <a href="{{ $backUrl }}" class="back-btn">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            Back
        </a>
        <div>
            <div class="t-title">Print Preview &nbsp;&middot;&nbsp; <span id="previewTitle">{{ $defaultLang === 'ta' ? $previewTitleTa : $previewTitleEn }}</span></div>
            <div class="t-sub">{{ $docNumber }} &nbsp;&middot;&nbsp; {{ $shopName }}</div>
        </div>
    </div>
    <div class="toolbar-actions">
        <div class="lang-picker">
            <button class="lang-btn {{ $defaultLang === 'en' ? 'active' : '' }}" onclick="switchLang('en')" id="btnEn">English</button>
            <button class="lang-btn {{ $defaultLang === 'ta' ? 'active' : '' }}" onclick="switchLang('ta')" id="btnTa">தமிழ்</button>
        </div>
        <button class="print-btn" onclick="window.print()">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            {{ $printBtnLabel }}
        </button>
    </div>
</div>

<div class="a4-shell">
    <div class="blank-half">
        <div class="blank-circle">&#8629;</div>
        <div class="blank-title">Blank &mdash; Reuse</div>
        <div class="blank-sub">Cut vertically along dashed line<br>Reuse left half for next print</div>
    </div>

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

    <div class="inv-half">
        <div class="inv">

            {{-- COMMON HEADER --}}
            <div class="inv-hdr">
                @if($shopIcon)
                <div class="inv-logo"><img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}"></div>
                @else
                <div class="inv-logo"><div class="inv-logo-txt">REPAIR<br>BOX</div></div>
                @endif
                <div class="inv-shop">
                    <div class="inv-shop-name" data-en="{{ e($shopName) }}" data-ta="{{ e($shopNameTa) }}" @if(!empty($shopNameTaKey)) data-setting-ta="{{ $shopNameTaKey }}" @endif>{{ $defaultLang === 'ta' ? $shopNameTa : $shopName }}</div>
                    <div class="inv-shop-slogan" data-en="{{ e($shopSlogan) }}" data-ta="{{ e($shopSloganTa) }}" @if(!empty($shopSloganTaKey)) data-setting-ta="{{ $shopSloganTaKey }}" @endif>{{ $defaultLang === 'ta' ? $shopSloganTa : $shopSlogan }}</div>
                    <div class="inv-shop-contact" data-en-addr="{{ e($shopAddress) }}" data-ta-addr="{{ e($shopAddressTa) }}">
                        &#128205; {{ $defaultLang === 'ta' ? $shopAddressTa : $shopAddress }}<br>
                        &#128222; {{ $shopPhone }}@if($shopPhone2) &nbsp;/&nbsp; {{ $shopPhone2 }}@endif @if($shopEmail) &middot; &#9993; {{ $shopEmail }}@endif
                    </div>
                </div>
                <div class="inv-badge">
                    <div class="inv-type" data-en="{{ e($headerTitleEn) }}" data-ta="{{ e($headerTitleTa) }}" @if(!empty($headerTitleEnKey)) data-setting-en="{{ $headerTitleEnKey }}" @endif @if(!empty($headerTitleTaKey)) data-setting-ta="{{ $headerTitleTaKey }}" @endif>{{ $defaultLang === 'ta' ? $headerTitleTa : $headerTitleEn }}</div>
                    <div class="inv-num">#{{ $docNumber }}</div>
                    <div class="inv-date">{{ $docDate }}</div>
                </div>
            </div>
            <div class="inv-rule"></div>

            {{-- TYPE-SPECIFIC CONTENT (info, table, bottom section) --}}
            @yield('printContent')

            {{-- COMMON FOOTER --}}
            <div class="inv-foot">
                <div class="inv-tc">
                    <div data-en="1. If a phone is given for service, please collect it within 15 days." data-ta="1. போன் சர்வீஸ்க்கு கொடுத்தால் 15 நாட்களுக்குள் பெற்றுக் கொள்ளவும்.">{{ $defaultLang === 'ta' ? '1. போன் சர்வீஸ்க்கு கொடுத்தால் 15 நாட்களுக்குள் பெற்றுக் கொள்ளவும்.' : '1. If a phone is given for service, please collect it within 15 days.' }}</div>
                    <div data-en="2. Sold items cannot be taken back or exchanged." data-ta="2. விற்ற பொருள் திரும்ப வாங்கவோ, மாற்றித் தரவோ இயலாது.">{{ $defaultLang === 'ta' ? '2. விற்ற பொருள் திரும்ப வாங்கவோ, மாற்றித் தரவோ இயலாது.' : '2. Sold items cannot be taken back or exchanged.' }}</div>
                    <div data-en="3. The given receipt (bill) must be brought back without fail." data-ta="3. கொடுக்கப்பட்ட ரசீதை (பில்) கண்டிப்பாக மீண்டும் கொண்டு வரவும்.">{{ $defaultLang === 'ta' ? '3. கொடுக்கப்பட்ட ரசீதை (பில்) கண்டிப்பாக மீண்டும் கொண்டு வரவும்.' : '3. The given receipt (bill) must be brought back without fail.' }}</div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
var shopPhone = @json($shopPhone);
var shopPhone2 = @json($shopPhone2);
var shopEmail = @json($shopEmail);
var previewTitleEn = @json($previewTitleEn);
var previewTitleTa = @json($previewTitleTa);

function switchLang(lang) {
    document.body.classList.toggle('lang-ta', lang === 'ta');
    document.getElementById('btnEn').classList.toggle('active', lang === 'en');
    document.getElementById('btnTa').classList.toggle('active', lang === 'ta');

    document.querySelectorAll('[data-' + lang + ']').forEach(function(el) {
        el.textContent = el.getAttribute('data-' + lang);
    });

    document.querySelectorAll('[data-' + lang + '-addr]').forEach(function(el) {
        var addr = el.getAttribute('data-' + lang + '-addr');
        var html = '\uD83D\uDCCD ' + addr + '<br>\uD83D\uDCDE ' + shopPhone;
        if (shopPhone2) html += ' / ' + shopPhone2;
        if (shopEmail) html += ' \u00B7 \u2709 ' + shopEmail;
        el.innerHTML = html;
    });

    document.getElementById('previewTitle').textContent =
        lang === 'ta' ? previewTitleTa : previewTitleEn;

    if (typeof onSwitchLang === 'function') onSwitchLang(lang);
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
@yield('extraJs')
</body>
</html>
