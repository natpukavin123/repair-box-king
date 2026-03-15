@extends('setup.layout')
@php $step = 5; @endphp

@section('content')
<div class="card" style="text-align:center; padding: 3rem 2.5rem;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
    <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem;">Installation Complete!</h1>
    <p style="color: var(--muted); font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left:auto; margin-right:auto;">
        Your RepairBox application has been successfully installed and is ready to use.
    </p>

    <div style="background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); border-radius:10px; padding:1.25rem; margin-bottom:2rem; text-align:left;">
        <p style="font-size:0.85rem; color:#6ee7b7; font-weight:500; margin-bottom:0.6rem;">✨ Next Steps:</p>
        <ul style="list-style:none; font-size:0.85rem; color:var(--muted); line-height:1.9;">
            <li>1️⃣ Log in with your admin credentials</li>
            <li>2️⃣ Go to <strong style="color:var(--text);">Settings</strong> to configure your shop details</li>
            <li>3️⃣ Add your categories, brands, and products</li>
            <li>4️⃣ Start accepting repairs and sales!</li>
        </ul>
    </div>

    <a href="/login" class="btn btn-primary" style="font-size:1rem; padding: 0.875rem 2.5rem;">
        🔐 Login to Dashboard →
    </a>
</div>
@endsection
