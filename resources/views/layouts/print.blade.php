@php
    $invoicePaperSize = \App\Models\Setting::getValue('invoice_paper_size', '80mm auto');
    $invoiceDesignVariant = \App\Models\Setting::getValue('invoice_design_variant', 'default');
    $shopIcon = \App\Models\Setting::getValue('shop_icon', '');
    $shopName = \App\Models\Setting::getValue('shop_name', 'RepairBox');
    $shopAddress = \App\Models\Setting::getValue('shop_address', 'Your shop address');
    $shopPhone = \App\Models\Setting::getValue('shop_phone', 'your-phone');
    $shopPhone2 = \App\Models\Setting::getValue('shop_phone2', '');
    $shopEmail = \App\Models\Setting::getValue('shop_email', 'your-email');
    $invoiceHeaderTitle = \App\Models\Setting::getValue('invoice_header_title', $shopName);
    $invoiceHeaderSubtitle = \App\Models\Setting::getValue('invoice_header_subtitle', $shopAddress);
    $invoiceFooterText = \App\Models\Setting::getValue('invoice_footer_text', $shopPhone);

    $isA4 = str_starts_with(strtoupper($invoicePaperSize), 'A4');
    $isA5 = str_starts_with(strtoupper($invoicePaperSize), 'A5');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Print')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 0;
            padding: 0;
            background: #f9fafb;
        }
        .print-wrapper {
            margin: 10mm auto;
            padding: {{ $isA4 ? '18mm 20mm 20mm' : ($isA5 ? '12mm 14mm 14mm' : '16px') }};
            max-width: {{ $isA4 ? '180mm' : ($isA5 ? '138mm' : '76mm') }};
            width: 100%;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
        }
        @page { margin: 5mm; size: {{ $invoicePaperSize }}; }

        /* Header */
        .header { text-align: center; border-bottom: 2px dashed #333; padding-bottom: 10px; margin-bottom: 10px; }
        .header h1 { font-size: 18px; font-weight: bold; }
        .header p { font-size: 10px; color: #666; }
        .header .doc-type { font-size: 11px; font-weight: 600; color: #444; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        /* Info rows */
        .info { margin-bottom: 10px; }
        .info div { display: flex; justify-content: space-between; padding: 1px 0; }
        .info span.label { color: #666; font-size: 10px; }
        .info span.value { font-weight: 600; font-size: 11px; }

        /* Sections */
        .section { margin-bottom: 10px; }
        .section h3 { font-size: 12px; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin-bottom: 5px; }
        .row { display: flex; justify-content: space-between; padding: 2px 0; }
        .row .label { color: #666; font-size: 10px; }
        .row .value { font-weight: 600; font-size: 11px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin: 8px 0; }
        th { text-align: left; border-bottom: 1px solid #333; padding: 3px 0; font-size: 11px; }
        td { padding: 3px 0; font-size: 11px; }
        td:last-child, th:last-child { text-align: right; }

        /* Totals */
        .totals { border-top: 1px dashed #333; padding-top: 5px; }
        .totals div { display: flex; justify-content: space-between; padding: 2px 0; }
        .totals .grand { font-size: 16px; font-weight: bold; border-top: 2px solid #333; padding-top: 5px; margin-top: 5px; }

        /* Payments */
        .payments { margin-top: 8px; font-size: 10px; }

        /* Tracking */
        .tracking { text-align: center; margin: 10px 0; padding: 8px; background: #f0f0f0; border-radius: 5px; }
        .tracking .code { font-size: 16px; font-weight: bold; letter-spacing: 2px; }

        /* Footer */
        .footer { text-align: center; border-top: 2px dashed #333; padding-top: 10px; margin-top: 15px; font-size: 10px; color: #666; }

        /* Design variants */
        @if($invoiceDesignVariant === 'modern')
        .header { border-bottom: 2px solid #1d4ed8; background: #eff6ff; padding: 10px; border-radius: 5px 5px 0 0; }
        .header h1 { color: #1d4ed8; }
        .totals .grand { color: #065f46; }
        @elseif($invoiceDesignVariant === 'minimal')
        .header { border-bottom: 1px solid #ccc; background: transparent; }
        .header h1 { font-size: 16px; color: #111; }
        .totals .grand { color: #111; }
        @endif

        @media print {
            body { padding: 0; background: #fff; }
            .print-wrapper { margin: 0; border: none; box-shadow: none; border-radius: 0; }
            @page { margin: 5mm; size: {{ $invoicePaperSize }}; }
        }

        @yield('extra-styles')
    </style>
</head>
<body>
    <div class="print-wrapper">
        {{-- Common Header --}}
        <div class="header">
            @if($shopIcon)
                <div style="margin-bottom: 8px; display: flex; justify-content: center;">
                    <img src="{{ image_url($shopIcon) }}" style="height: 40px; object-fit: contain;">
                </div>
            @endif
            <h1>{{ $invoiceHeaderTitle }}</h1>
            <p>{{ $shopAddress }}</p>
            <p>{{ $shopPhone }}@if($shopPhone2) / {{ $shopPhone2 }}@endif | {{ $shopEmail }}</p>
            @hasSection('doc-type')
                <div class="doc-type">@yield('doc-type')</div>
            @endif
        </div>

        {{-- Type-specific content --}}
        @yield('print-content')

        {{-- Common Footer --}}
        <div class="footer">
            <p>{{ $invoiceFooterText }}</p>
            <p>Terms & Conditions Apply</p>
        </div>
    </div>

    <script>window.onload = function() { window.print(); }</script>
</body>
</html>
