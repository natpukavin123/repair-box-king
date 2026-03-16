<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $emailSubject ?? '' }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #374151; }
        a { color: #4f46e5; }
    </style>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;">

    @if(!empty($previewText))
    <div style="display:none;max-height:0;overflow:hidden;font-size:1px;line-height:1px;color:#f3f4f6;white-space:nowrap;">
        {{ $previewText }}
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>
    @endif

    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#f3f4f6;min-height:100vh;">
        <tr>
            <td align="center" style="padding:32px 16px;">

                {{-- Wrapper --}}
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;width:100%;">

                    {{-- Header --}}
                    <tr>
                        <td style="background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);border-radius:12px 12px 0 0;padding:32px 40px;text-align:center;">
                            @php $shopName    = \App\Models\Setting::getValue('shop_name', 'RepairBox');
                                 $shopPhone   = \App\Models\Setting::getValue('shop_phone', '');
                                 $shopIcon    = \App\Models\Setting::getValue('shop_icon', '');
                            @endphp

                            @if($shopIcon)
                                @php
                                    $iconUrl = $shopIcon;
                                    if (!str_starts_with($shopIcon, 'http') && !str_starts_with($shopIcon, 'data:')) {
                                        $iconUrl = url('/storage/' . ltrim($shopIcon, '/'));
                                    }
                                @endphp
                                <img src="{{ $iconUrl }}" alt="{{ $shopName }}" style="height:48px;max-width:160px;object-fit:contain;margin-bottom:14px;display:block;margin-left:auto;margin-right:auto;" onerror="this.style.display='none'">
                            @endif

                            <h1 style="margin:0;font-size:22px;font-weight:700;color:#ffffff;letter-spacing:-0.3px;">{{ $shopName }}</h1>
                            @if($shopPhone)
                            <p style="margin:6px 0 0 0;font-size:13px;color:#c4b5fd;">📞 {{ $shopPhone }}</p>
                            @endif
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background:#ffffff;padding:36px 40px;">
                            {!! $htmlBody !!}
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background:#f9fafb;border-top:1px solid #e5e7eb;border-radius:0 0 12px 12px;padding:20px 40px;text-align:center;">
                            <p style="margin:0;font-size:12px;color:#9ca3af;">
                                &copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.
                            </p>
                            <p style="margin:6px 0 0 0;font-size:11px;color:#d1d5db;">
                                This is an automated message. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>

                </table>
                {{-- /Wrapper --}}

            </td>
        </tr>
    </table>

</body>
</html>
