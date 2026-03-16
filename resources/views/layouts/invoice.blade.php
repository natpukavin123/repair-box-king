@php
    $shopName        = \App\Models\Setting::getValue('shop_name',        'Shree Mobile Shop');
    $shopAddress     = \App\Models\Setting::getValue('shop_address',     'Your shop address');
    $shopPhone       = \App\Models\Setting::getValue('shop_phone',       'your-phone');
    $shopEmail       = \App\Models\Setting::getValue('shop_email',       'your-email');
    $shopIcon        = \App\Models\Setting::getValue('shop_icon',        '');
    $shopSlogan      = \App\Models\Setting::getValue('shop_slogan',      'Your Trusted Mobile Partner');
    $shopUpiId       = \App\Models\Setting::getValue('upi_id',           '');
    $invoiceHeaderBanner = \App\Models\Setting::getValue('invoice_header_banner', 'Get All Your Desired Smart Phones From Apple To Vivo On Huge Discounts And Easy EMI');
    $invoiceFooterText   = \App\Models\Setting::getValue('invoice_footer_text', 'Subject to jurisdiction. Our Responsibility Ceases as soon as goods leave our Premises. Goods once sold will not be taken back.');

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
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Invoice')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 10.5px;
            color: #111;
            background: #ccc;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .page {
            width: 210mm;
            height: auto;
            margin: 8mm auto;
            background: #f0f0f0;
            box-shadow: 0 8px 40px rgba(0,0,0,0.18);
            padding: 10mm;
        }

        .invoice-card {
            background: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }

        @page { size: A4; margin: 0; }
        @media print {
            body { background: #fff; }
            .page {
                margin: 0; padding: 8mm;
                box-shadow: none; width: 100%;
                min-height: unset !important;
                height: auto !important;
                background: #fff;
            }
            .invoice-card { box-shadow: none; border: 1px solid #ccc; }
        }

        /* ═══════ HEADER ═══════ */
        .hdr {
            background: #111;
            padding: 18px 22px 16px;
            display: flex;
            align-items: center;
            gap: 18px;
        }
        .logo-ring {
            width: 60px; height: 60px;
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            flex-shrink: 0;
            background: rgba(255,255,255,0.06);
            overflow: hidden;
        }
        .logo-ring .lr1 { font-size: 8px; font-weight: 700; color: #fff; letter-spacing: 1px; text-align: center; line-height: 1.3; }
        .logo-ring .lr2 { font-size: 6px; color: rgba(255,255,255,0.4); text-align: center; margin-top: 2px; }
        .hdr-text { flex: 1; }
        .hdr-text .shop-name {
            font-family: 'Playfair Display', serif;
            font-size: 26px; font-weight: 900;
            color: #fff; letter-spacing: -0.3px; line-height: 1;
        }
        .hdr-text .shop-tag {
            font-size: 9px; color: rgba(255,255,255,0.45);
            letter-spacing: 1.5px; text-transform: uppercase; margin-top: 4px;
        }
        .hdr-text .shop-contact {
            font-size: 9.5px; color: rgba(255,255,255,0.65);
            margin-top: 8px; line-height: 1.8;
        }
        .hdr-brands { display: flex; flex-direction: column; gap: 5px; align-items: flex-end; }
        .brand-row  { display: flex; gap: 4px; flex-wrap: wrap; justify-content: flex-end; }
        .bp { padding: 2px 7px; border-radius: 2px; font-size: 8px; font-weight: 700; color: #fff; letter-spacing: 0.3px; }
        .bp-apple   { background: #2c2c2e; border: 1px solid #444; }
        .bp-samsung { background: #1428a0; }
        .bp-mi      { background: #f47920; }
        .bp-oneplus { background: #eb0029; }
        .bp-google  { background: #4285f4; }
        .bp-oppo    { background: #1d6b36; }
        .bp-huawei  { background: #c0392b; }
        .bp-vivo    { background: #415fff; }
        .bp-nokia   { background: #124191; }
        .bp-asus    { background: #444; border: 1px solid #666; }
        .bp-sony    { background: #555; }
        .bp-honor   { background: #7d1515; }

        .hdr-rule { height: 4px; background: #fff; border-bottom: 2px solid #111; }
        .banner {
            background: #f5f5f5; border-bottom: 1px solid #ddd;
            text-align: center; font-size: 9.5px; font-style: italic;
            color: #555; padding: 5px 20px; letter-spacing: 0.2px;
        }

        /* ═══════ TITLE ROW ═══════ */
        .title-row {
            display: flex; align-items: stretch;
            border-top: 2px solid #111; border-bottom: 2px solid #111;
        }
        .title-mid {
            flex: 1; display: flex; align-items: center;
            justify-content: center; padding: 6px; background: #fff;
        }
        .title-mid h2 {
            font-family: 'Playfair Display', serif;
            font-size: 20px; font-weight: 900; color: #111;
            letter-spacing: 5px; text-transform: uppercase;
        }
        .orig-blk {
            padding: 9px 14px; font-size: 9px; font-weight: 700;
            color: #111; border-left: 1.5px solid #111;
            background: #f5f5f5;
            display: flex; align-items: center; justify-content: center;
            letter-spacing: 0.3px; text-align: center; line-height: 1.5;
        }

        /* ═══════ INFO GRID ═══════ */
        .info-grid { display: flex; border-bottom: 2px solid #111; }
        .info-col  { flex: 1; }
        .info-col + .info-col { border-left: 1.5px solid #bbb; }
        .col-hdr {
            background: #111; color: #fff;
            font-size: 8.5px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            padding: 5px 14px;
        }
        .col-body { padding: 10px 14px; }
        .irow { display: flex; padding: 2.5px 0; }
        .irow .lb { font-weight: 600; color: #666; font-size: 9.5px; min-width: 95px; flex-shrink: 0; }
        .irow .vl { color: #111; font-size: 10.5px; }
        .inv-num {
            font-family: 'Playfair Display', serif;
            font-size: 26px; font-weight: 900; color: #111; line-height: 1;
        }

        /* ═══════ ITEMS TABLE ═══════ */
        .tbl-wrap { border-bottom: 2px solid #111; }
        table.items { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.items thead th {
            background: #111; color: #fff;
            font-weight: 600; font-size: 8.5px;
            letter-spacing: 0.8px; text-transform: uppercase;
            padding: 8px 8px;
            border-right: 1px solid #333;
            text-align: center;
        }
        table.items thead th:first-child { border-left: none; }
        table.items thead th.tl { text-align: left; }
        table.items thead tr.th2 th {
            background: #333; font-size: 8px; padding: 3px 8px;
            border-right: 1px solid #555;
        }
        table.items tbody tr { border-bottom: 1px solid #e8e8e8; }
        table.items tbody tr:nth-child(even) { background: #f9f9f9; }
        table.items tbody td {
            padding: 7px 8px;
            border-right: 1px solid #e8e8e8;
            vertical-align: top;
        }
        table.items tbody td:last-child { border-right: none; }
        .tc { text-align: center; }
        .tr { text-align: right; font-feature-settings: "tnum"; }
        .item-sub { font-size: 8.5px; color: #888; font-style: italic; margin-top: 2px; }
        table.items tfoot tr { border-top: 2px solid #111; }
        table.items tfoot td {
            background: #111; color: #fff;
            font-weight: 700; font-size: 10px;
            padding: 8px 8px; text-align: right;
            border-right: 1px solid #333;
        }
        table.items tfoot td.tc { text-align: center; }
        table.items tfoot td:last-child { border-right: none; }

        /* ═══════ BOTTOM ═══════ */
        .bottom { display: flex; border-bottom: 2px solid #111; }
        .b-left  { flex: 1; border-right: 1.5px solid #bbb; }
        .b-right { width: 230px; }
        .s-hdr {
            background: #111; color: #fff;
            font-size: 8.5px; font-weight: 700;
            letter-spacing: 1.5px; text-transform: uppercase;
            padding: 5px 13px;
        }
        .s-body { padding: 10px 13px; }
        .words-box {
            border: 1px solid #111; padding: 8px 11px;
            font-size: 10px; font-weight: 600; color: #111;
            letter-spacing: 0.3px; line-height: 1.5; background: #f9f9f9;
        }
        .pay-item {
            display: flex; justify-content: space-between;
            padding: 4px 0; font-size: 10px;
            border-bottom: 1px dashed #ddd;
        }
        .pay-item:last-child { border-bottom: none; }
        .p-in  { color: #1a6e3a; font-weight: 700; }
        .p-out { color: #c0392b; font-weight: 700; }
        .qr-area {
            margin-top: 10px;
            display: flex; align-items: center; gap: 10px;
            padding: 8px; background: #f5f5f5; border: 1px solid #ddd;
        }
        .qr-box {
            width: 72px; height: 72px; border: 1.5px solid #111;
            display: flex; align-items: center; justify-content: center;
            font-size: 8px; color: #888; text-align: center;
            flex-shrink: 0; background: #fff;
        }
        .qr-meta .qr-upi  { font-weight: 700; font-size: 10.5px; color: #111; }
        .qr-meta .qr-scan { font-size: 8.5px; color: #666; margin-top: 3px; }

        /* Summary table */
        .summary-tbl { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        .summary-tbl td { padding: 5px 13px; border-bottom: 1px solid #eee; }
        .summary-tbl td:last-child { text-align: right; font-weight: 600; font-feature-settings: "tnum"; }
        .summary-tbl .sep td { border-top: 1.5px solid #bbb; }
        .summary-tbl .grand td {
            background: #111; color: #fff;
            font-family: 'Playfair Display', serif;
            font-size: 13px; font-weight: 700;
            padding: 10px 13px; border: none;
        }
        .summary-tbl .note  td { font-size: 8px; color: #aaa; border: none; padding-top: 1px; }
        .summary-tbl .bal   td { color: #c0392b; font-weight: 700; font-size: 12px; }
        .summary-tbl .green td { color: #1a6e3a; font-weight: 700; }
        .summary-tbl .full  td { color: #1a6e3a; font-weight: 700; text-align: center; font-size: 11px; }

        /* Sign box */
        .sign-box { padding: 10px 13px; text-align: center; }
        .sign-cert { font-size: 8px; color: #aaa; line-height: 1.5; }
        .sign-for  {
            font-family: 'Playfair Display', serif;
            font-size: 12px; font-weight: 700; color: #111; margin-top: 8px;
        }
        .sign-line { border-top: 1.5px solid #111; margin: 34px 10px 4px; }
        .sign-auth { font-size: 8.5px; font-weight: 700; color: #555; letter-spacing: 1px; text-transform: uppercase; }

        /* T&C + Footer */
        .tc-row { display: flex; border-bottom: 1px solid #ddd; }
        .tc-col { flex: 1; padding: 9px 14px; font-size: 9.5px; color: #555; line-height: 1.7; }
        .tc-col + .tc-col { border-left: 1.5px solid #ddd; }
        .tc-hdr { font-weight: 700; color: #111; margin-bottom: 4px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        .doc-foot {
            background: #111; color: rgba(255,255,255,0.5);
            text-align: center; font-size: 8.5px;
            padding: 6px 20px; letter-spacing: 0.3px;
        }
        .doc-foot strong { color: #fff; }

        .mt4 { margin-top: 4px; }
        .mt8 { margin-top: 8px; }

        @yield('extra-styles')
    </style>
</head>
<body>
<div class="page">
<div class="invoice-card">

    {{-- ══ HEADER ══ --}}
    <div class="hdr">
        @if($shopIcon)
            <div class="logo-ring" style="background: #fff; border: 2px solid #333; border-radius: 50%;">
                <img src="{{ asset('storage/' . $shopIcon) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;">
            </div>
        @else
            <div class="logo-ring">
                <div class="lr1">PHONE<br>SHOP</div>
                <div class="lr2">{{ $shopSlogan }}</div>
            </div>
        @endif

        <div class="hdr-text">
            <div class="shop-name">{{ $shopName }}</div>
            <div class="shop-tag">{{ $shopSlogan }}</div>
            <div class="shop-contact">
                📍 {{ $shopAddress }}<br>
                📞 {{ $shopPhone }}@if($shopEmail) &nbsp;·&nbsp; ✉ {{ $shopEmail }}@endif
            </div>
        </div>

        <div class="hdr-brands">
            <div class="brand-row">
                <span class="bp bp-apple">Apple</span>
                <span class="bp bp-samsung">SAMSUNG</span>
                <span class="bp bp-mi">mi</span>
                <span class="bp bp-oneplus">OnePlus</span>
                <span class="bp bp-google">Google</span>
                <span class="bp bp-oppo">oppo</span>
            </div>
            <div class="brand-row">
                <span class="bp bp-huawei">HUAWEI</span>
                <span class="bp bp-vivo">vivo</span>
                <span class="bp bp-nokia">NOKIA</span>
                <span class="bp bp-asus">ASUS</span>
                <span class="bp bp-sony">SONY</span>
                <span class="bp bp-honor">HONOR</span>
            </div>
        </div>
    </div>

    <div class="hdr-rule"></div>
    <div class="banner">{{ $invoiceHeaderBanner }}</div>

    {{-- ══ TITLE ROW ══ --}}
    <div class="title-row">
        <div class="title-mid"><h2>@yield('invoice-title', 'Invoice')</h2></div>
        <div class="orig-blk">ORIGINAL<br>FOR RECIPIENT</div>
    </div>

    {{-- ══ TYPE-SPECIFIC CONTENT ══ --}}
    @yield('invoice-content')

    {{-- ══ TERMS ══ --}}
    <div class="tc-row">
        <div class="tc-col">
            <div class="tc-hdr">Terms &amp; Conditions</div>
            {{ $invoiceFooterText }}
        </div>
        <div class="tc-col" style="max-width:185px;display:flex;align-items:center;justify-content:center;text-align:center;font-size:9px;color:#bbb;">
            This is a computer<br>generated invoice
        </div>
    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="doc-foot">
        @yield('footer-extra')
        {{ $shopName }} &nbsp;·&nbsp; {{ $shopPhone }}
    </div>

</div><!-- .invoice-card -->
</div><!-- .page -->
<script>
    window.onload = function () {
        setTimeout(function () {
            window.print();
            window.onfocus = function () { window.close(); };
        }, 300);
    };
</script>
</body>
</html>
