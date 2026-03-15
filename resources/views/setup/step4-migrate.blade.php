@extends('setup.layout')
@php $step = 4; @endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="card">
    <div class="card-title">🚀 Installing Application</div>
    <div class="card-sub">Click the button below to run database migrations and seed initial data. This may take a moment.</div>

    <div id="start-section">
        <div style="background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.3); border-radius:10px; padding:1.25rem; margin-bottom:1.5rem;">
            <p style="font-size:0.875rem; color:#a5b4fc; font-weight:500; margin-bottom:0.5rem;">📋 What will be installed:</p>
            <ul style="list-style:none; font-size:0.85rem; color:var(--muted); line-height:1.8;">
                <li>✔ All database tables (35 migrations)</li>
                <li>✔ Roles & Permissions (Admin, Technician, Billing Staff, Stock Manager)</li>
                <li>✔ Default Settings (shop name, currencies, prefixes)</li>
                <li>✔ Service Types (Screen replacement, Battery, etc.)</li>
                <li>✔ Email templates</li>
                <li>✔ Admin user account</li>
            </ul>
        </div>

        <div class="btn-row right">
            <button class="btn btn-primary" id="runBtn" onclick="runInstall()">⚡ Run Installation</button>
        </div>
    </div>

    <!-- Log output area (hidden initially) -->
    <div id="log-section" style="display:none;">
        <div id="log-box" style="
            background: #0d1117;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            line-height: 1.7;
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 1.5rem;
        "></div>

        <div id="progress-bar-wrap" style="background:var(--bg); border-radius:99px; height:6px; margin-bottom:1.5rem; overflow:hidden;">
            <div id="progress-bar" style="background:var(--primary); height:6px; width:0%; border-radius:99px; transition:width 0.5s;"></div>
        </div>

        <div id="done-section" style="display:none;" class="btn-row right">
            <a href="/setup/complete" class="btn btn-success">✅ Go to Dashboard →</a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
async function runInstall() {
    document.getElementById('start-section').style.display = 'none';
    document.getElementById('log-section').style.display = 'block';

    const logBox = document.getElementById('log-box');
    const bar    = document.getElementById('progress-bar');

    function appendLog(entry) {
        const icons = { info: '⬡', success: '✅', warning: '⚠️', error: '❌' };
        const colors = { info: '#94a3b8', success: '#6ee7b7', warning: '#fcd34d', error: '#fca5a5' };
        const div = document.createElement('div');
        div.style.color = colors[entry.status] || '#94a3b8';
        div.textContent = (icons[entry.status] || '•') + ' ' + entry.msg;
        logBox.appendChild(div);
        logBox.scrollTop = logBox.scrollHeight;
    }

    try {
        bar.style.width = '15%';
        const res  = await fetch('/setup/migrate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({}),
        });

        bar.style.width = '80%';
        const data = await res.json();

        (data.log || []).forEach(entry => appendLog(entry));
        bar.style.width = '100%';

        if (data.success) {
            appendLog({ status: 'success', msg: '🎉 Installation completed successfully!' });
            setTimeout(() => {
                document.getElementById('done-section').style.display = 'flex';
                if (data.redirect) { window.location.href = data.redirect; }
            }, 800);
        } else {
            appendLog({ status: 'error', msg: 'Installation failed. Please check errors above and try again.' });
        }
    } catch(e) {
        appendLog({ status: 'error', msg: 'Network error: ' + e.message });
        bar.style.background = '#ef4444';
    }
}
</script>
@endsection
