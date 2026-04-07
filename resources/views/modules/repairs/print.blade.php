@php
    $printKeys = [
        'headerTitleEn'=>'receipt_header_title_en', 'headerTitleTa'=>'receipt_header_title_ta',
        'shopNameTa'=>'receipt_shop_name_ta', 'shopSloganTa'=>'receipt_shop_slogan_ta',
        'shopAddressTa'=>'receipt_shop_address_ta',
        'signLabelEn'=>'receipt_sign_label_en', 'signLabelTa'=>'receipt_sign_label_ta',
    ];
    $printDefaults = [
        'headerTitleEn'=>'Repair Receipt', 'headerTitleTa'=>'பழுதுபார்ப்பு ரசீது',
    ];
    require_once resource_path('views/partials/print-a4-vars.php');

    // Receipt-only settings
    $notesEn = \App\Models\Setting::getValue('receipt_notes_en',
        "Keep this receipt to claim your device.\nEstimated cost may change upon diagnosis.\nData backup is customer's responsibility.\nUnclaimed devices after 30 days — not our liability.");
    $notesTa = \App\Models\Setting::getValue('receipt_notes_ta',
        "உங்கள் சாதனத்தை பெற இந்த ரசீதை வைத்திருங்கள்.\nமதிப்பீட்டுச் செலவு ஆய்வுக்குப் பிறகு மாறலாம்.\nதரவு காப்புப்பிரதி வாடிக்கையாளரின் பொறுப்பு.\n30 நாட்களுக்குப் பிறகு உரிமை கோரப்படாத சாதனங்கள் — எங்கள் பொறுப்பல்ல.");

    // Layout variables
    $pageTitle      = 'Repair ' . $repair->ticket_number;
    $backUrl        = url('/admin/repairs');
    $printBtnLabel  = 'Print Receipt';
    $docNumber      = $repair->ticket_number;
    $docDate        = $repair->created_at->format('d M Y');

    $advancePaid   = $repair->payments->where('direction','IN')->where('payment_type','advance')->sum('amount');
    $repairStatus  = ucfirst(str_replace('_',' ',$repair->status ?? 'pending'));
    $notesEnArr    = array_filter(explode("\n", $notesEn));
    $notesTaArr    = array_filter(explode("\n", $notesTa));

    $statusTa = [
        'received'=>'பெறப்பட்டது','in_progress'=>'பணியில்','completed'=>'முடிந்தது',
        'payment'=>'கட்டணம்','closed'=>'மூடப்பட்டது','cancelled'=>'ரத்து',
        'Received'=>'பெறப்பட்டது','In Progress'=>'பணியில்','Completed'=>'முடிந்தது',
        'Payment'=>'கட்டணம்','Closed'=>'மூடப்பட்டது','Cancelled'=>'ரத்து',
    ];
@endphp

@extends('layouts.print-a4')

@section('footerExtra')
#{{ $repair->ticket_number }}
@endsection

@section('printContent')
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
@endsection

@section('extraJs')
<script>
var notesEn = @json($notesEnArr);
var notesTa = @json($notesTaArr);
function onSwitchLang(lang) {
    var notes = lang === 'ta' ? notesTa : notesEn;
    var container = document.getElementById('notesList');
    if (!container) return;
    container.innerHTML = '';
    (Array.isArray(notes) ? notes : Object.values(notes)).forEach(function(note) {
        var div = document.createElement('div');
        div.className = 'note-item';
        div.textContent = '\u2733 ' + note;
        container.appendChild(div);
    });
}
</script>
@endsection
