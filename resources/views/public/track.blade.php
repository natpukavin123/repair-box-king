@php
    $statusOrder  = ['received','in_progress','completed','payment','closed'];
    $statusLabels = [
        'received'    => 'Received',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'payment'     => 'Payment',
        'closed'      => 'Closed',
        'cancelled'   => 'Cancelled',
    ];
    $statusColors = [
        'received'    => ['bg'=>'#dbeafe','text'=>'#1d4ed8','border'=>'#93c5fd','dot'=>'#3b82f6'],
        'in_progress' => ['bg'=>'#fef3c7','text'=>'#b45309','border'=>'#fcd34d','dot'=>'#f59e0b'],
        'completed'   => ['bg'=>'#d1fae5','text'=>'#065f46','border'=>'#6ee7b7','dot'=>'#10b981'],
        'payment'     => ['bg'=>'#ede9fe','text'=>'#6d28d9','border'=>'#c4b5fd','dot'=>'#8b5cf6'],
        'closed'      => ['bg'=>'#dcfce7','text'=>'#166534','border'=>'#86efac','dot'=>'#22c55e'],
        'cancelled'   => ['bg'=>'#fee2e2','text'=>'#991b1b','border'=>'#fca5a5','dot'=>'#ef4444'],
    ];
    $currentRepair   = $repair ?? null;
    $currentStatus   = $currentRepair ? ($currentRepair->status ?? 'received') : null;
    $currentStepIdx  = $currentStatus ? array_search($currentStatus, $statusOrder) : -1;
    $isCancelled     = $currentStatus === 'cancelled';
    $totalPaid       = $currentRepair ? ($currentRepair->payments->where('direction','IN')->sum('amount') ?? 0) : 0;
    $balance         = $currentRepair ? max(0, ($currentRepair->estimated_cost + ($currentRepair->service_charge ?? 0)) - $totalPaid) : 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Track Repair &mdash; {{ $shopName }}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Inter',system-ui,sans-serif;background:#f1f5f9;color:#1e293b;min-height:100vh;}

/* ── Nav ── */
.nav{background:#fff;border-bottom:1px solid #e2e8f0;position:sticky;top:0;z-index:50;}
.nav-inner{max-width:900px;margin:0 auto;padding:0 20px;height:64px;display:flex;align-items:center;justify-content:space-between;}
.nav-brand{display:flex;align-items:center;gap:12px;text-decoration:none;}
.nav-logo{width:40px;height:40px;border-radius:10px;overflow:hidden;background:#0f172a;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.nav-logo img{width:100%;height:100%;object-fit:cover;}
.nav-logo-txt{font-size:8px;font-weight:800;color:#fff;text-align:center;line-height:1.3;letter-spacing:.5px;}
.nav-shop-name{font-size:18px;font-weight:800;color:#0f172a;line-height:1;}
.nav-slogan{font-size:11px;color:#64748b;margin-top:1px;}
.nav-contact{font-size:13px;color:#475569;display:flex;align-items:center;gap:16px;}
.nav-contact a{color:#475569;text-decoration:none;}
.nav-contact a:hover{color:#0f172a;}

/* ── Hero ── */
.hero{background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0f172a 100%);padding:56px 20px 48px;}
.hero-inner{max-width:600px;margin:0 auto;text-align:center;}
.hero-chip{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);color:#93c5fd;font-size:12px;font-weight:600;letter-spacing:.5px;text-transform:uppercase;padding:5px 14px;border-radius:99px;margin-bottom:20px;}
.hero-title{font-size:36px;font-weight:900;color:#fff;line-height:1.15;margin-bottom:10px;}
.hero-sub{font-size:15px;color:#94a3b8;margin-bottom:36px;line-height:1.6;}

/* ── Search box ── */
.search-card{background:#fff;border-radius:16px;padding:8px;display:flex;gap:8px;box-shadow:0 20px 60px rgba(0,0,0,.3);}
.search-input{flex:1;border:none;outline:none;font-family:inherit;font-size:16px;font-weight:600;padding:12px 16px;color:#0f172a;letter-spacing:.5px;background:transparent;}
.search-input::placeholder{font-weight:400;color:#94a3b8;letter-spacing:0;}
.search-btn{background:#0f172a;color:#fff;border:none;font-family:inherit;font-size:14px;font-weight:700;padding:12px 24px;border-radius:10px;cursor:pointer;display:flex;align-items:center;gap:8px;white-space:nowrap;transition:background .15s;}
.search-btn:hover{background:#1e293b;}
.search-hint{font-size:12px;color:#64748b;margin-top:12px;}

/* ── Content ── */
.content{max-width:900px;margin:32px auto;padding:0 20px 48px;}

/* ── Alert ── */
.alert{background:#fff;border:1px solid #fca5a5;border-left:4px solid #ef4444;border-radius:12px;padding:20px 24px;display:flex;align-items:flex-start;gap:14px;}
.alert-icon{width:24px;height:24px;flex-shrink:0;color:#ef4444;margin-top:1px;}
.alert-title{font-size:15px;font-weight:700;color:#991b1b;}
.alert-sub{font-size:13px;color:#ef4444;margin-top:3px;}

/* ── Status Hero card ── */
.result-header{background:#fff;border-radius:16px;padding:28px 32px;display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);}
.repair-id-block .label{font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;}
.repair-id-block .ticket{font-size:22px;font-weight:900;color:#0f172a;margin-top:4px;letter-spacing:-.5px;}
.repair-id-block .tracking{font-size:13px;color:#64748b;margin-top:4px;font-family:monospace;letter-spacing:.5px;}
.status-hero{text-align:right;}
.status-pill{display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:99px;font-size:14px;font-weight:700;border:2px solid;}
.status-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}

/* ── Grid layout ── */
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px;}
@media(max-width:640px){.grid-2{grid-template-columns:1fr;}}

/* ── Card ── */
.card{background:#fff;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.08);}
.card-title{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:16px;display:flex;align-items:center;gap:8px;}
.card-title svg{opacity:.7;}

/* ── Info rows ── */
.info-row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;}
.info-row:last-child{border-bottom:none;padding-bottom:0;}
.info-row:first-child{padding-top:0;}
.info-label{font-size:13px;color:#64748b;flex-shrink:0;}
.info-value{font-size:13px;font-weight:600;color:#0f172a;text-align:right;}

/* ── Progress stepper ── */
.stepper{background:#fff;border-radius:16px;padding:24px 28px;margin-bottom:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);}
.stepper-title{font-size:12px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;color:#64748b;margin-bottom:24px;}
.steps{display:flex;align-items:flex-start;position:relative;}
.step{flex:1;display:flex;flex-direction:column;align-items:center;position:relative;z-index:1;}
.step-line{position:absolute;top:18px;left:50%;right:-50%;height:2px;background:#e2e8f0;z-index:0;}
.step:last-child .step-line{display:none;}
.step-circle{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;border:2px solid;transition:all .3s;position:relative;z-index:1;background:#fff;}
.step-circle.done{background:#0f172a;border-color:#0f172a;color:#fff;}
.step-circle.active{border-color:#3b82f6;background:#eff6ff;color:#3b82f6;box-shadow:0 0 0 4px rgba(59,130,246,.2);}
.step-circle.inactive{border-color:#e2e8f0;color:#cbd5e1;}
.step-label{font-size:11px;font-weight:600;color:#64748b;margin-top:10px;text-align:center;line-height:1.4;}
.step-label.done{color:#0f172a;}
.step-label.active{color:#3b82f6;}
.step-line.done{background:#0f172a;}

/* cancelled banner */
.cancelled-banner{background:#fee2e2;border:1px solid #fca5a5;border-radius:12px;padding:16px 20px;display:flex;align-items:center;gap:12px;margin-bottom:20px;}
.cancelled-banner svg{color:#ef4444;flex-shrink:0;}
.cancelled-banner p{font-size:14px;font-weight:600;color:#991b1b;}
.cancelled-banner span{font-size:13px;color:#b91c1c;font-weight:400;}

/* ── Timeline ── */
.timeline{position:relative;padding-left:28px;}
.timeline::before{content:'';position:absolute;left:7px;top:8px;bottom:8px;width:2px;background:#e2e8f0;border-radius:1px;}
.timeline-item{position:relative;margin-bottom:20px;}
.timeline-item:last-child{margin-bottom:0;}
.timeline-dot{position:absolute;left:-28px;top:5px;width:14px;height:14px;border-radius:50%;border:2px solid #fff;box-shadow:0 0 0 2px #e2e8f0;}
.timeline-dot.dot-active{box-shadow:0 0 0 3px;}
.timeline-meta{font-size:12px;color:#94a3b8;margin-bottom:3px;}
.timeline-status{font-size:13px;font-weight:700;color:#0f172a;}
.timeline-note{font-size:12px;color:#64748b;margin-top:2px;}

/* ── cost card ── */
.cost-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #f1f5f9;}
.cost-row:last-child{border-bottom:none;}
.cost-row-label{font-size:13px;color:#475569;}
.cost-row-value{font-size:14px;font-weight:700;color:#0f172a;}
.cost-row-value.green{color:#059669;}
.cost-row-value.amber{color:#d97706;}
.cost-row-value.muted{color:#94a3b8;font-weight:500;}
.cost-total-row{padding-top:16px;margin-top:4px;border-top:2px solid #0f172a;}
.cost-total-label{font-size:14px;font-weight:700;color:#0f172a;}
.cost-total-value{font-size:20px;font-weight:900;color:#0f172a;}

/* ── Footer ── */
.footer{background:#0f172a;color:#94a3b8;padding:28px 20px;text-align:center;}
.footer-inner{max-width:600px;margin:0 auto;}
.footer-name{font-size:16px;font-weight:800;color:#fff;margin-bottom:6px;}
.footer-contact{font-size:13px;display:flex;justify-content:center;gap:20px;flex-wrap:wrap;}
.footer-contact a{color:#94a3b8;text-decoration:none;}
.footer-contact a:hover{color:#fff;}
.footer-copy{font-size:12px;margin-top:12px;color:#475569;}
</style>
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="nav">
    <div class="nav-inner">
        <a href="{{ route('track.landing') }}" class="nav-brand">
            <div class="nav-logo">
                @if($shopIcon)
                    <img src="{{ image_url($shopIcon) }}" alt="{{ $shopName }}">
                @else
                    <div class="nav-logo-txt">REPAIR<br>BOX</div>
                @endif
            </div>
            <div>
                <div class="nav-shop-name">{{ $shopName }}</div>
                <div class="nav-slogan">{{ $shopSlogan }}</div>
            </div>
        </a>
        <div class="nav-contact">
            @if($shopPhone)
                <a href="tel:{{ $shopPhone }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $shopPhone }}
                </a>
            @endif
            @if($shopEmail)
                <a href="mailto:{{ $shopEmail }}">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $shopEmail }}
                </a>
            @endif
        </div>
    </div>
</nav>

{{-- ── Hero / Search ── --}}
<div class="hero">
    <div class="hero-inner">
        <div class="hero-chip">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Repair Status
        </div>
        <h1 class="hero-title">Track Your Repair</h1>
        <p class="hero-sub">Enter the Tracking ID from your repair receipt to check the latest status of your device.</p>

        <form method="GET" action="" id="trackForm" onsubmit="submitTrack(event)">
            <div class="search-card">
                <input
                    type="text"
                    name="q"
                    id="trackInput"
                    class="search-input"
                    placeholder="e.g. TRK-C06C030E"
                    value="{{ !empty($repair) ? $repair->tracking_id : (isset($notFound) && $notFound ? request()->segment(2) : '') }}"
                    autocomplete="off"
                    spellcheck="false"
                    maxlength="20"
                    style="text-transform:uppercase;"
                >
                <button type="submit" class="search-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    Track
                </button>
            </div>
        </form>
        <p class="search-hint">Your Tracking ID is printed on your repair receipt</p>
    </div>
</div>

{{-- ── Results Section ── --}}
<div class="content">

    @if(isset($notFound) && $notFound)
    {{-- Not found --}}
    <div class="alert">
        <svg class="alert-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <div class="alert-title">Tracking ID not found</div>
            <div class="alert-sub">We couldn't find a repair with that tracking ID. Please check the ID on your receipt and try again.</div>
        </div>
    </div>

    @elseif(!empty($repair))
    @php $sc = $statusColors[$currentStatus] ?? $statusColors['received']; @endphp

    {{-- ── Status Header ── --}}
    <div class="result-header">
        <div class="repair-id-block">
            <div class="label">Repair Ticket</div>
            <div class="ticket">#{{ $repair->ticket_number }}</div>
            <div class="tracking">{{ $repair->tracking_id }}</div>
        </div>
        <div class="status-hero">
            <div class="label" style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#64748b;margin-bottom:8px;">Current Status</div>
            <div class="status-pill" style="background:{{ $sc['bg'] }};color:{{ $sc['text'] }};border-color:{{ $sc['border'] }};">
                <div class="status-dot" style="background:{{ $sc['dot'] }};"></div>
                {{ $statusLabels[$currentStatus] ?? ucfirst(str_replace('_',' ',$currentStatus)) }}
            </div>
        </div>
    </div>

    {{-- ── Cancelled Banner ── --}}
    @if($isCancelled)
    <div class="cancelled-banner">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <div>
            <p>This repair has been cancelled.
            @if($repair->cancel_reason)
                <span>&nbsp;&mdash;&nbsp;{{ $repair->cancel_reason }}</span>
            @endif
            </p>
        </div>
    </div>

    @else
    {{-- ── Progress Stepper ── --}}
    <div class="stepper">
        <div class="stepper-title">Repair Progress</div>
        <div class="steps">
            @foreach($statusOrder as $si => $stepKey)
            @php
                $isDone   = $currentStepIdx > $si;
                $isActive = $currentStepIdx === $si;
                $lineClass = $isDone ? 'done' : '';
            @endphp
            <div class="step">
                {{-- connector line to next step --}}
                @if(!$loop->last)
                <div class="step-line {{ $lineClass }}"></div>
                @endif

                <div class="step-circle {{ $isDone ? 'done' : ($isActive ? 'active' : 'inactive') }}">
                    @if($isDone)
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    @elseif($isActive)
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
                    @else
                        <span style="font-size:12px;font-weight:800;">{{ $si + 1 }}</span>
                    @endif
                </div>
                <div class="step-label {{ $isDone ? 'done' : ($isActive ? 'active' : '') }}">
                    {{ $statusLabels[$stepKey] }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Device + Customer Grid ── --}}
    <div class="grid-2">
        {{-- Device Info --}}
        <div class="card">
            <div class="card-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18" stroke-width="3" stroke-linecap="round"/></svg>
                Device Details
            </div>
            <div class="info-row">
                <span class="info-label">Brand / Model</span>
                <span class="info-value">{{ $repair->device_brand }} {{ $repair->device_model }}</span>
            </div>
            @if($repair->imei)
            <div class="info-row">
                <span class="info-label">IMEI</span>
                <span class="info-value" style="font-family:monospace;">{{ $repair->imei }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Received On</span>
                <span class="info-value">{{ $repair->created_at->format('d M Y') }}</span>
            </div>
            @if($repair->expected_delivery_date)
            <div class="info-row">
                <span class="info-label">Expected Delivery</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($repair->expected_delivery_date)->format('d M Y') }}</span>
            </div>
            @endif
            @if($repair->completed_at)
            <div class="info-row">
                <span class="info-label">Completed On</span>
                <span class="info-value">{{ $repair->completed_at->format('d M Y') }}</span>
            </div>
            @endif
        </div>

        {{-- Payment Summary --}}
        <div class="card">
            <div class="card-title">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Cost Summary
            </div>
            <div class="cost-row" style="padding-top:0;">
                <span class="cost-row-label">Estimated Cost</span>
                <span class="cost-row-value">&#8377;{{ number_format($repair->estimated_cost, 2) }}</span>
            </div>
            @if($repair->service_charge > 0)
            <div class="cost-row">
                <span class="cost-row-label">Service Charge</span>
                <span class="cost-row-value">&#8377;{{ number_format($repair->service_charge, 2) }}</span>
            </div>
            @endif
            <div class="cost-row">
                <span class="cost-row-label">Advance Paid</span>
                <span class="cost-row-value green">
                    @if($totalPaid > 0)
                        &minus; &#8377;{{ number_format($totalPaid, 2) }}
                    @else
                        <span class="muted">&#8377;0.00</span>
                    @endif
                </span>
            </div>
            <div class="cost-row cost-total-row" style="display:flex;justify-content:space-between;align-items:center;">
                <span class="cost-total-label">Balance Due</span>
                <span class="cost-total-value {{ $balance > 0 ? 'amber' : 'green' }}" style="color:{{ $balance > 0 ? '#d97706' : '#059669' }};">
                    &#8377;{{ number_format($balance, 2) }}
                </span>
            </div>
        </div>
    </div>

    {{-- ── Problem Description ── --}}
    @if($repair->problem_description)
    <div class="card" style="margin-bottom:20px;">
        <div class="card-title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Problem Reported
        </div>
        <p style="font-size:14px;color:#334155;line-height:1.7;white-space:pre-line;">{{ $repair->problem_description }}</p>
    </div>
    @endif

    {{-- ── Status History ── --}}
    @if($repair->statusHistory && $repair->statusHistory->count())
    <div class="card">
        <div class="card-title">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            Status History
        </div>
        <div class="timeline">
            @foreach($repair->statusHistory->sortByDesc('created_at') as $hist)
            @php $hsc = $statusColors[$hist->status] ?? $statusColors['received']; @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $loop->first ? 'dot-active' : '' }}"
                     style="background:{{ $hsc['dot'] }};{{ $loop->first ? 'box-shadow:0 0 0 3px '.$hsc['border'].';' : '' }}"></div>
                <div class="timeline-meta">{{ \Carbon\Carbon::parse($hist->created_at)->format('d M Y, g:i A') }}</div>
                <div class="timeline-status">
                    <span style="display:inline-block;background:{{ $hsc['bg'] }};color:{{ $hsc['text'] }};border:1px solid {{ $hsc['border'] }};padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;">
                        {{ $statusLabels[$hist->status] ?? ucfirst(str_replace('_',' ',$hist->status)) }}
                    </span>
                </div>
                @if($hist->notes)
                <div class="timeline-note">{{ $hist->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif {{-- end repair found --}}
</div>

{{-- ── Footer ── --}}
<footer class="footer">
    <div class="footer-inner">
        <div class="footer-name">{{ $shopName }}</div>
        <div class="footer-contact">
            @if($shopPhone)
            <a href="tel:{{ $shopPhone }}">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>{{ $shopPhone }}
            </a>
            @endif
            @if($shopEmail)
            <a href="mailto:{{ $shopEmail }}">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;vertical-align:middle;margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>{{ $shopEmail }}
            </a>
            @endif
        </div>
        <div class="footer-copy">&copy; {{ date('Y') }} {{ $shopName }}. All rights reserved.</div>
    </div>
</footer>

<script>
function submitTrack(e) {
    e.preventDefault();
    var val = document.getElementById('trackInput').value.trim().toUpperCase();
    if (!val) return;
    window.location.href = '/track/' + encodeURIComponent(val);
}
// Auto-uppercase input
document.getElementById('trackInput').addEventListener('input', function() {
    var pos = this.selectionStart;
    this.value = this.value.toUpperCase();
    this.setSelectionRange(pos, pos);
});
</script>
</body>
</html>
