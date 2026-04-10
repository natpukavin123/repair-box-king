@extends('layouts.app')
@section('title', 'WhatsApp')
@section('page-title', 'WhatsApp')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .wa-workspace { gap: 0.7rem; }
    .wa-card {
        border-radius: 1.35rem;
        border: 1px solid rgba(148,163,184,0.16);
        background: linear-gradient(180deg,rgba(255,255,255,0.9),rgba(250,252,255,0.82));
        box-shadow: 0 26px 60px -42px rgba(15,23,42,0.38);
        overflow: hidden;
    }
    .wa-card .card-header {
        padding: 0.9rem 1.1rem;
        border-bottom: 1px solid rgba(148,163,184,0.12);
        background: linear-gradient(180deg,rgba(255,255,255,0.72),rgba(241,245,255,0.48));
        display: flex; align-items: center; gap: 0.6rem;
    }
    .wa-card .card-body { padding: 1rem 1.1rem; }
    .wa-stat {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        border-radius: 1.1rem; padding: 0.85rem 0.5rem;
        background: rgba(255,255,255,0.7);
        border: 1px solid rgba(148,163,184,0.14);
        transition: transform 0.15s, box-shadow 0.15s;
    }
    .wa-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(15,23,42,0.08); }
    .wa-nav-tab {
        display: flex; gap: 0.25rem; padding: 0.25rem;
        background: rgba(148,163,184,0.1); border-radius: 0.9rem;
        border: 1px solid rgba(148,163,184,0.14);
        flex-shrink: 0;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .wa-nav-tab a {
        flex: 1; text-align: center; padding: 0.5rem 0.7rem;
        border-radius: 0.7rem; font-size: 0.78rem; font-weight: 500;
        color: #64748b; text-decoration: none; transition: all 0.18s;
        white-space: nowrap; min-width: 0;
    }
    .wa-nav-tab a.active {
        background: white; color: #16a34a;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem;
        padding: 0.28rem 0.75rem; border-radius: 99px; font-size: 0.78rem; font-weight: 600;
    }
    .status-connected { background: #dcfce7; color: #16a34a; }
    .status-qr        { background: #fef3c7; color: #d97706; }
    .status-disconnected { background: #fee2e2; color: #dc2626; }
    .qr-wrap {
        display: flex; flex-direction: column; align-items: center; gap: 0.75rem;
        padding: 1rem; background: white; border-radius: 1rem;
        border: 2px dashed rgba(22,163,74,0.3);
    }
    .qr-wrap img { width: 200px; height: 200px; border-radius: 0.5rem; max-width: 100%; }

    /* Quick action cards */
    .wa-action-card {
        display: flex; align-items: center; gap: 0.85rem;
        padding: 0.85rem 1rem; border-radius: 1rem;
        border: 1px solid rgba(148,163,184,0.14);
        background: rgba(255,255,255,0.7);
        text-decoration: none; color: inherit;
        transition: all 0.15s;
    }
    .wa-action-card:hover {
        background: white;
        border-color: rgba(22,163,74,0.25);
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(15,23,42,0.07);
    }
    .wa-action-icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Recent activity */
    .wa-activity-item {
        display: flex; align-items: start; gap: 0.7rem;
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(226,232,240,0.5);
    }
    .wa-activity-item:last-child { border-bottom: none; }
    .wa-activity-dot {
        width: 0.5rem; height: 0.5rem; border-radius: 50%;
        flex-shrink: 0; margin-top: 0.35rem;
    }

    @media (max-width: 767px) {
        .wa-nav-tab a { font-size: 0.74rem; padding: 0.45rem 0.5rem; }
        .wa-stat { padding: 0.65rem 0.4rem; }
        .wa-stat .text-2xl { font-size: 1.35rem; }
        .wa-card .card-body { padding: 0.85rem 0.9rem; }
        .qr-wrap img { width: 180px; height: 180px; }
    }
</style>

<div class="flex flex-col wa-workspace" style="min-height:0;">

    {{-- Tab Nav --}}
    <div class="wa-nav-tab">
        <a href="/admin/whatsapp"         class="active">Dashboard</a>
        <a href="/admin/whatsapp/groups">Groups</a>
        <a href="/admin/whatsapp/schedules">Schedules</a>
        <a href="/admin/whatsapp/history">History</a>
    </div>

    <div class="flex flex-col lg:flex-row gap-3 lg:flex-1 lg:min-h-0">

        {{-- LEFT: Device Status --}}
        <div class="w-full lg:w-80 flex-shrink-0 flex flex-col gap-3" x-data="waDevice()" x-init="init()">
            <div class="wa-card">
                <div class="card-header">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">WhatsApp Device</span>
                </div>
                <div class="card-body">
                    <div class="flex flex-col gap-3">

                        {{-- Status --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-slate-500 font-medium">Status</span>
                            <span :class="statusClass()" class="status-badge">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                <span x-text="statusLabel()"></span>
                            </span>
                        </div>

                        {{-- Device Info --}}
                        <template x-if="device">
                            <div class="bg-green-50 border border-green-100 rounded-xl p-3 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-green-800" x-text="device.name"></div>
                                        <div class="text-green-600 text-xs mt-0.5" x-text="'+' + device.number"></div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- QR Code --}}
                        <template x-if="status === 'qr_pending' && qr">
                            <div class="qr-wrap">
                                <p class="text-xs text-slate-500 font-medium text-center">Open WhatsApp on your phone → Settings → Linked Devices → Link a Device</p>
                                <img :src="qr" alt="WA QR Code">
                                <p class="text-xs text-slate-400">QR refreshes automatically</p>
                            </div>
                        </template>

                        {{-- Not connected --}}
                        <template x-if="status === 'disconnected' && !qr">
                            <div class="bg-amber-50 border border-amber-100 rounded-xl p-3">
                                <div class="flex items-center gap-2 mb-1.5">
                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                                    <span class="text-sm font-semibold text-amber-700">Not Connected</span>
                                </div>
                                <p class="text-xs text-amber-600">WhatsApp service is not reachable. Please contact your administrator if this persists.</p>
                            </div>
                        </template>

                        {{-- Refresh --}}
                        <div class="flex gap-2">
                            <button @click="refresh()" class="btn btn-sm flex-1">
                                <svg class="w-3.5 h-3.5" :class="loading ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                            <template x-if="status === 'connected'">
                                <button @click="logoutDevice()"
                                    class="btn btn-sm text-red-500 border-red-200 hover:bg-red-50">
                                    Logout
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Connection Tips (only when not connected) --}}
            <div class="wa-card" x-show="status !== 'connected'" x-cloak>
                <div class="card-body">
                    <p class="text-xs font-semibold text-slate-700 mb-2">How to connect</p>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                            <span class="text-xs text-slate-600">Open <strong>WhatsApp</strong> on your phone</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                            <span class="text-xs text-slate-600">Go to <strong>Settings → Linked Devices</strong></span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-5 h-5 rounded-full bg-green-100 text-green-600 text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                            <span class="text-xs text-slate-600">Tap <strong>Link a Device</strong> and scan the QR code</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Stats + Quick Actions + Activity --}}
        <div class="flex-1 flex flex-col gap-3 min-w-0">

            {{-- Stats row --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3">
                <div class="wa-stat">
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total_groups'] }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Active Groups</div>
                </div>
                <div class="wa-stat">
                    <div class="text-2xl font-bold text-slate-800">{{ $stats['total_schedules'] }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Active Schedules</div>
                </div>
                <div class="wa-stat">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['sent_today'] }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Sent Today</div>
                </div>
                <div class="wa-stat">
                    <div class="text-2xl font-bold text-red-500">{{ $stats['failed_today'] }}</div>
                    <div class="text-xs text-slate-500 mt-0.5">Failed Today</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="wa-card">
                <div class="card-header">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">Quick Actions</span>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <a href="/admin/whatsapp/groups" class="wa-action-card">
                            <div class="wa-action-icon bg-blue-50">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800">Manage Groups</div>
                                <div class="text-xs text-slate-500 mt-0.5">Add, edit or fetch WhatsApp groups</div>
                            </div>
                        </a>
                        <a href="/admin/whatsapp/schedules" class="wa-action-card">
                            <div class="wa-action-icon bg-green-50">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800">Create Schedule</div>
                                <div class="text-xs text-slate-500 mt-0.5">Set up automated message templates</div>
                            </div>
                        </a>
                        <a href="/admin/whatsapp/history" class="wa-action-card">
                            <div class="wa-action-icon bg-purple-50">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800">Message History</div>
                                <div class="text-xs text-slate-500 mt-0.5">View sent & failed message logs</div>
                            </div>
                        </a>
                        <a href="/admin/whatsapp/groups" class="wa-action-card">
                            <div class="wa-action-icon bg-amber-50">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800">Fetch Groups</div>
                                <div class="text-xs text-slate-500 mt-0.5">Import groups from your WhatsApp</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="wa-card lg:flex-1">
                <div class="card-header">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold text-slate-700">Recent Activity</span>
                </div>
                <div class="card-body" x-data="waActivity()" x-init="load()">
                    <template x-if="items.length === 0">
                        <div class="flex flex-col items-center justify-center py-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <p class="text-sm text-slate-500 font-medium">No messages sent yet</p>
                            <p class="text-xs text-slate-400 mt-1">Create a schedule to start sending messages</p>
                            <a href="/admin/whatsapp/schedules" class="btn btn-sm btn-primary mt-3">Create Schedule</a>
                        </div>
                    </template>
                    <template x-if="items.length > 0">
                        <div>
                            <template x-for="item in items" :key="item.id">
                                <div class="wa-activity-item">
                                    <div class="wa-activity-dot mt-1" :class="item.status === 'sent' ? 'bg-green-400' : 'bg-red-400'"></div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm font-medium text-slate-700 truncate" x-text="item.schedule_name || 'Manual Send'"></span>
                                            <span class="text-xs text-slate-400 flex-shrink-0" x-text="timeAgo(item.sent_at)"></span>
                                        </div>
                                        <div class="text-xs text-slate-500 mt-0.5 truncate" x-text="item.group_name || 'Unknown group'"></div>
                                        <div class="flex items-center gap-1.5 mt-1">
                                            <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded-full"
                                                :class="item.status === 'sent' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'">
                                                <span class="w-1 h-1 rounded-full bg-current"></span>
                                                <span x-text="item.status === 'sent' ? 'Delivered' : 'Failed'"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <a href="/admin/whatsapp/history" class="block text-center text-xs text-green-600 font-medium mt-2 hover:underline">View all history →</a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function waDevice() {
    return {
        status: 'disconnected',
        qr: null,
        device: null,
        loading: false,
        pollInterval: null,

        init() {
            this.refresh();
            this.pollInterval = setInterval(() => {
                if (this.status !== 'connected') this.refresh();
            }, 8000);
        },

        async refresh() {
            this.loading = true;
            const r = await RepairBox.ajax('/admin/whatsapp/status', 'GET');
            this.loading = false;
            if (r && r.data) {
                this.status = r.data.status ?? 'disconnected';
                this.qr     = r.data.qr    ?? null;
                this.device = r.data.device ?? null;
            }
        },

        statusClass() {
            return {
                'connected':    'status-badge status-connected',
                'qr_pending':   'status-badge status-qr',
                'disconnected': 'status-badge status-disconnected',
            }[this.status] ?? 'status-badge status-disconnected';
        },

        statusLabel() {
            return {
                'connected':    'Connected',
                'qr_pending':   'Scan QR',
                'disconnected': 'Disconnected',
            }[this.status] ?? 'Unknown';
        },

        async logoutDevice() {
            if (!await RepairBox.confirm('Logout from WhatsApp? You will need to scan QR again.')) return;
            await RepairBox.ajax('/admin/whatsapp/logout', 'POST');
            this.status = 'disconnected';
            this.device = null;
            RepairBox.toast('Logged out', 'success');
            setTimeout(() => this.refresh(), 2000);
        }
    };
}

function waActivity() {
    return {
        items: [],

        async load() {
            try {
                const r = await RepairBox.ajax('/admin/whatsapp/history', 'GET');
                const list = r.data || r || [];
                this.items = (Array.isArray(list) ? list : list.data || []).slice(0, 8);
            } catch(e) {
                this.items = [];
            }
        },

        timeAgo(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'Just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
            return date.toLocaleDateString();
        }
    };
}
</script>
@endsection
