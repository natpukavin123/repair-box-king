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

        {{-- Reset Module Transactions --}}
        <div style="background: rgba(168,85,247,0.07); border:1px solid rgba(168,85,247,0.25); border-radius:14px; padding:1.5rem;">
            <div style="font-size:1.5rem; margin-bottom:0.5rem;">🧹</div>
            <h3 style="font-size:1rem; font-weight:600; color:#d8b4fe; margin-bottom:0.4rem;">Reset Module Transactions</h3>
            <p style="font-size:0.825rem; color:#6b7280; line-height:1.5; margin-bottom:1.25rem;">
                Clears only <strong style="color:#d8b4fe;">transactional records</strong> — Sales, Repairs, Recharges, Expenses, PO/Purchases, Returns & Credit Notes.<br>
                <strong style="color:#9ca3af;">Master data (customers, products, brands, categories, etc.) is kept intact.</strong>
            </p>
            <button class="dev-btn dev-btn-purple" onclick="runAction('reset-modules', this)">
                🧹 Reset Module Data
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

    </div>{{-- /Action Cards grid --}}

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

    {{-- Delete Single Repair Ticket --}}
    <div style="background: rgba(239,68,68,0.05); border:1px solid rgba(239,68,68,0.2); border-radius:14px; padding:1.5rem; margin-bottom:1.5rem;"
         x-data="deleteRepairTool()">
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.6rem;">
            <span style="font-size:1.3rem;">🗑️</span>
            <h3 style="font-size:1rem; font-weight:600; color:#fca5a5;">Delete Repair Ticket</h3>
        </div>
        <p style="font-size:0.825rem; color:#6b7280; margin-bottom:1rem; line-height:1.5;">
            Search for a wrongly-added ticket and <strong style="color:#fca5a5;">permanently remove</strong> it — including all payments, parts, services, status history, ledger entries, and other related records.
        </p>

        {{-- Search input --}}
        <div style="position:relative; margin-bottom:0.75rem;">
            <input
                x-model="query"
                @input.debounce.350ms="search()"
                @focus="search()"
                @keydown.escape="results = []; open = false"
                @click.away="open = false"
                type="text"
                placeholder="Type ticket number or customer name…"
                style="width:100%; padding:0.6rem 1rem; border-radius:8px; border:1px solid rgba(239,68,68,0.3); background:rgba(255,255,255,0.05); color:inherit; font-size:0.875rem; outline:none; box-sizing:border-box;"
            >
            {{-- Dropdown results --}}
            <div x-show="open && results.length > 0" x-cloak
                 style="position:absolute; left:0; right:0; top:calc(100% + 4px); background:#1e293b; border:1px solid rgba(100,116,139,0.3); border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.4); z-index:99; max-height:220px; overflow-y:auto;">
                <template x-for="r in results" :key="r.id">
                    <button @click="selectRepair(r)"
                            style="width:100%; text-align:left; padding:0.65rem 1rem; border:none; background:transparent; cursor:pointer; border-bottom:1px solid rgba(100,116,139,0.15); color:#e2e8f0; font-size:0.85rem;"
                            onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                        <strong x-text="r.ticket_number" style="color:#fca5a5;"></strong>
                        <template x-if="r.tracking_id">
                            <span style="color:#94a3b8; font-size:0.75rem;" x-text="' · Track: ' + r.tracking_id"></span>
                        </template>
                        &nbsp;—&nbsp;
                        <span x-text="r.customer_name || 'Walk-in'"></span>
                        <span style="color:#6b7280; font-size:0.75rem;" x-text="' · ' + (r.device_brand||'') + ' ' + (r.device_model||'')"></span>
                    </button>
                </template>
            </div>
            <div x-show="searching" style="position:absolute; right:0.75rem; top:50%; transform:translateY(-50%); font-size:0.75rem; color:#6b7280;">searching…</div>
        </div>

        {{-- Selected ticket preview --}}
        <div x-show="selected" x-cloak
             style="background:rgba(239,68,68,0.08); border:1px solid rgba(239,68,68,0.25); border-radius:10px; padding:0.875rem 1rem; margin-bottom:0.875rem;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:0.5rem;">
                <div>
                    <div style="font-size:0.95rem; font-weight:700; color:#fca5a5;" x-text="selected?.ticket_number"></div>
                    <div style="font-size:0.8rem; color:#94a3b8; margin-top:0.2rem;">
                        <span x-text="selected?.customer_name || 'Walk-in'"></span>
                        <span style="color:#475569;" x-show="selected?.device_brand"> · <span x-text="(selected?.device_brand||'') + ' ' + (selected?.device_model||'')"></span></span>
                    </div>
                    <div style="margin-top:0.3rem; display:flex; gap:0.5rem; flex-wrap:wrap;">
                        <span style="font-size:0.72rem; background:rgba(99,102,241,0.15); color:#818cf8; border-radius:99px; padding:0.15rem 0.6rem;" x-text="'Status: ' + (selected?.status || '—')"></span>
                        <span style="font-size:0.72rem; background:rgba(16,185,129,0.12); color:#6ee7b7; border-radius:99px; padding:0.15rem 0.6rem;" x-text="'ID: ' + selected?.id"></span>
                        <template x-if="selected?.tracking_id">
                            <span style="font-size:0.72rem; background:rgba(245,158,11,0.15); color:#fcd34d; border-radius:99px; padding:0.15rem 0.6rem;" x-text="'Tracking: ' + selected.tracking_id"></span>
                        </template>
                    </div>
                </div>
                <button @click="clearSelection()" style="font-size:0.78rem; color:#6b7280; background:none; border:none; cursor:pointer; padding:0.25rem 0.5rem; border-radius:6px;" onmouseover="this.style.color='#fca5a5'" onmouseout="this.style.color='#6b7280'">✕ Clear</button>
            </div>
        </div>

        {{-- Delete button --}}
        <button @click="deleteSelected()"
                :disabled="!selected || deleting"
                class="dev-btn dev-btn-danger"
                style="width:100%; justify-content:center;">
            <span x-show="!deleting">🗑️ Permanently Delete This Ticket</span>
            <span x-show="deleting">⏳ Deleting…</span>
        </button>
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
.dev-btn-purple  { background: rgba(168,85,247,0.2); color: #d8b4fe; border: 1px solid rgba(168,85,247,0.4); }
.dev-btn-purple:hover:not(:disabled)  { background: rgba(168,85,247,0.35); }
</style>
@endsection

@push('scripts')
<script>
const actionLabels = {
    reset:           ['⚠️ Reset All Data',       '⏳ Resetting...'],
    'reset-modules': ['🧹 Reset Module Data',    '⏳ Clearing...'],
    seed:            ['🌱 Seed Demo Data',        '⏳ Seeding...'],
    'reset-seed':    ['⚡ Reset + Seed Demo',     '⏳ Running...'],
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

<script>
function deleteRepairTool() {
    return {
        query:    '',
        results:  [],
        open:     false,
        searching:false,
        selected: null,
        deleting: false,

        async search() {
            const q = this.query.trim();
            if (!q) { this.results = []; this.open = false; return; }
            this.searching = true;
            try {
                const res  = await fetch(`/admin/repairs?search=${encodeURIComponent(q)}&per_page=20`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                const list = data.data ?? data.repairs ?? data ?? [];
                this.results = list.map(r => ({
                    id:            r.id,
                    ticket_number: r.ticket_number,
                    tracking_id:   r.tracking_id ?? null,
                    status:        r.status,
                    device_brand:  r.device_brand,
                    device_model:  r.device_model,
                    customer_name: r.customer?.name ?? null,
                }));
                this.open = this.results.length > 0;
            } catch(e) {
                this.results = [];
            }
            this.searching = false;
        },

        selectRepair(r) {
            this.selected = r;
            this.query    = r.ticket_number;
            this.open     = false;
            this.results  = [];
        },

        clearSelection() {
            this.selected = null;
            this.query    = '';
        },

        async deleteSelected() {
            if (!this.selected) return;
            const ticket = this.selected.ticket_number;
            const sure = window.confirm(
                `⚠️ Permanently delete "${ticket}"?\n\nThis will remove the ticket AND all related payments, parts, services, status history, ledger entries, etc.\n\nThis CANNOT be undone.`
            );
            if (!sure) return;

            this.deleting = true;

            // Show shared log box
            const logContainer = document.getElementById('log-container');
            const logBox       = document.getElementById('log-box');
            const bar          = document.getElementById('progress-bar');
            logBox.innerHTML   = '';
            logContainer.style.display = 'block';
            bar.style.width    = '15%';
            bar.style.background = '#ef4444';

            const appendLog = entry => {
                const colors = { info:'#94a3b8', success:'#6ee7b7', warning:'#fcd34d', error:'#fca5a5' };
                const icons  = { info:'›', success:'✅', warning:'⚠', error:'❌' };
                const div = document.createElement('div');
                div.style.color = colors[entry.status] || '#94a3b8';
                div.textContent = (icons[entry.status] || '›') + ' ' + entry.msg;
                logBox.appendChild(div);
                logBox.scrollTop = logBox.scrollHeight;
            };

            try {
                const res  = await fetch('/admin/dev-tools/delete-repair', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ repair_id: this.selected.id }),
                });
                bar.style.width = '90%';
                const data = await res.json();
                (data.log || []).forEach(appendLog);
                bar.style.width = '100%';
                if (data.success) {
                    bar.style.background = '#10b981';
                    this.clearSelection();
                    setTimeout(() => location.reload(), 1800);
                } else {
                    bar.style.background = '#ef4444';
                }
            } catch(e) {
                appendLog({ status: 'error', msg: 'Request failed: ' + e.message });
                bar.style.background = '#ef4444';
            }

            this.deleting = false;
        }
    };
}
</script>
@endpush

