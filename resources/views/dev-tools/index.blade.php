@extends('layouts.app')

@section('content')
<div style="padding: 2rem; max-width: 900px; margin: 0 auto;">

    {{-- Header --}}
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 700; display:flex; align-items:center; gap:0.6rem;">
            🛠️ Developer Tools
        </h1>
        <p style="color: var(--muted, #6b7280); font-size: 0.9rem; margin-top:0.3rem;">
            Manage demo data for development and testing. These actions are irreversible.
        </p>
    </div>

    {{-- Current Data Stats --}}
    <div style="background: rgba(99,102,241,0.07); border:1px solid rgba(99,102,241,0.25); border-radius:14px; padding:1.25rem 1.5rem; margin-bottom:2rem;">
        <p style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:#818cf8; margin-bottom:0.875rem;">📊 Current Data Overview</p>
        <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap:0.75rem;">
            @foreach($tableStats as $label => $count)
            <div style="background:rgba(255,255,255,0.04); border-radius:10px; padding:0.75rem 1rem; text-align:center;">
                <div style="font-size:1.4rem; font-weight:700; color:{{ $count > 0 ? '#818cf8' : '#4b5563' }};">{{ $count }}</div>
                <div style="font-size:0.75rem; color:#6b7280; margin-top:0.15rem;">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Action Cards --}}
    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.25rem; margin-bottom:1.25rem;">

        {{-- Reset Data --}}
        <div style="background: rgba(239,68,68,0.07); border:1px solid rgba(239,68,68,0.25); border-radius:14px; padding:1.5rem;">
            <div style="font-size:1.5rem; margin-bottom:0.5rem;">🗑️</div>
            <h3 style="font-size:1rem; font-weight:600; color:#fca5a5; margin-bottom:0.4rem;">Reset Data</h3>
            <p style="font-size:0.825rem; color:#6b7280; line-height:1.5; margin-bottom:1.25rem;">
                Truncates <strong style="color:#fca5a5;">all tables</strong> — invoices, repairs, purchases, customers, expenses, ledger <em>and</em> all master data (brands, categories, service types, products, parts, etc.).<br>
                <strong style="color:#9ca3af;">Only users & app settings are preserved.</strong>
            </p>
            <button class="dev-btn dev-btn-danger" onclick="runAction('reset', this)">
                ⚠️ Reset All Data
            </button>
        </div>

        {{-- Seed Demo Data --}}
        <div style="background: rgba(16,185,129,0.07); border:1px solid rgba(16,185,129,0.25); border-radius:14px; padding:1.5rem;">
            <div style="font-size:1.5rem; margin-bottom:0.5rem;">🌱</div>
            <h3 style="font-size:1rem; font-weight:600; color:#6ee7b7; margin-bottom:0.4rem;">Seed Demo Data</h3>
            <p style="font-size:0.825rem; color:#6b7280; line-height:1.5; margin-bottom:1.25rem;">
                Inserts sample customers, repairs, invoices, products, and expenses using the full DatabaseSeeder.<br>
                <strong style="color:#9ca3af;">Best used on a clean / reset database.</strong>
            </p>
            <button class="dev-btn dev-btn-success" onclick="runAction('seed', this)">
                🌱 Seed Demo Data
            </button>
        </div>
    </div>

    {{-- Reset + Seed Combined --}}
    <div style="background: rgba(245,158,11,0.07); border:1px solid rgba(245,158,11,0.25); border-radius:14px; padding:1.5rem; margin-bottom:1.5rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
            <div>
                <h3 style="font-size:1rem; font-weight:600; color:#fcd34d; margin-bottom:0.3rem;">⚡ Reset & Seed</h3>
                <p style="font-size:0.825rem; color:#6b7280;">Clears all transactional data then re-seeds with fresh demo data in one step. Perfect for a clean demo environment.</p>
            </div>
            <button class="dev-btn dev-btn-warning" onclick="runAction('reset-seed', this)" style="white-space:nowrap;">
                ⚡ Reset + Seed Demo
            </button>
        </div>
    </div>

    {{-- Log Output --}}
    <div id="log-container" style="display:none;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:0.5rem;">
            <p style="font-size:0.78rem; font-weight:600; text-transform:uppercase; letter-spacing:0.6px; color:#94a3b8;">📋 Output Log</p>
            <button onclick="document.getElementById('log-container').style.display='none'" style="background:none;border:none;color:#6b7280;cursor:pointer;font-size:0.8rem;">✕ Close</button>
        </div>
        <div id="log-box" style="
            background: #0d1117;
            border: 1px solid #1e293b;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            font-family: 'Courier New', monospace;
            font-size: 0.8rem;
            line-height: 1.8;
            max-height: 350px;
            overflow-y: auto;
        "></div>
        <div id="progress-wrap" style="margin-top:0.75rem; background:#1e293b; border-radius:99px; height:5px; overflow:hidden;">
            <div id="progress-bar" style="background:#6366f1; width:0%; height:5px; border-radius:99px; transition:width 0.5s;"></div>
        </div>
    </div>
</div>

<style>
.dev-btn {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.6rem 1.25rem;
    border-radius: 8px; border: none;
    font-size: 0.85rem; font-weight: 600;
    cursor: pointer; font-family: inherit;
    transition: all 0.2s;
}
.dev-btn:disabled { opacity: 0.5; cursor: not-allowed; }
.dev-btn-danger  { background: rgba(239,68,68,0.2); color: #fca5a5; border: 1px solid rgba(239,68,68,0.4); }
.dev-btn-danger:hover:not(:disabled)  { background: rgba(239,68,68,0.35); }
.dev-btn-success { background: rgba(16,185,129,0.2); color: #6ee7b7; border: 1px solid rgba(16,185,129,0.4); }
.dev-btn-success:hover:not(:disabled) { background: rgba(16,185,129,0.35); }
.dev-btn-warning { background: rgba(245,158,11,0.2); color: #fcd34d; border: 1px solid rgba(245,158,11,0.4); }
.dev-btn-warning:hover:not(:disabled) { background: rgba(245,158,11,0.35); }
</style>
@endsection

@push('scripts')
<script>
const actionLabels = {
    reset:       ['⚠️ Reset All Data',  '⏳ Resetting...'],
    seed:        ['🌱 Seed Demo Data',   '⏳ Seeding...'],
    'reset-seed':['⚡ Reset + Seed Demo','⏳ Running...'],
};

async function runAction(action, btn) {
    const confirm = window.confirm('Are you sure you want to run: ' + action + '?\nThis cannot be undone.');
    if (!confirm) return;

    const logContainer = document.getElementById('log-container');
    const logBox       = document.getElementById('log-box');
    const bar          = document.getElementById('progress-bar');

    logBox.innerHTML = '';
    logContainer.style.display = 'block';
    bar.style.width = '10%';
    bar.style.background = '#6366f1';

    btn.disabled = true;
    btn.textContent = actionLabels[action][1];

    function appendLog(entry) {
        const colors = { info:'#94a3b8', success:'#6ee7b7', warning:'#fcd34d', error:'#fca5a5' };
        const icons  = { info:'›', success:'✅', warning:'⚠', error:'❌' };
        const div = document.createElement('div');
        div.style.color = colors[entry.status] || '#94a3b8';
        div.textContent = (icons[entry.status] || '›') + ' ' + entry.msg;
        logBox.appendChild(div);
        logBox.scrollTop = logBox.scrollHeight;
    }

    try {
        const res  = await fetch(`/admin/dev-tools/${action}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
            body: JSON.stringify({}),
        });
        bar.style.width = '90%';
        const data = await res.json();
        (data.log || []).forEach(appendLog);
        bar.style.width = '100%';
        if (!data.success) bar.style.background = '#ef4444';
        setTimeout(() => { location.reload(); }, 2000);
    } catch(e) {
        appendLog({ status: 'error', msg: 'Request failed: ' + e.message });
        bar.style.background = '#ef4444';
    }

    btn.disabled = false;
    btn.textContent = actionLabels[action][0];
}
</script>
@endpush
