@extends('layouts.app')
@section('title', 'WA Schedules')
@section('page-title', 'WA Schedules')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .wa-workspace { gap: 0.65rem; }

    /* ── Nav tabs ───────────────────────────────────────────────── */
    .wa-nav-tab {
        display: flex; gap: 0.25rem; padding: 0.25rem;
        background: rgba(148,163,184,0.1); border-radius: 0.9rem;
        border: 1px solid rgba(148,163,184,0.14); flex-shrink: 0;
        overflow-x: auto; -webkit-overflow-scrolling: touch;
    }
    .wa-nav-tab a {
        flex: 1; text-align: center; padding: 0.5rem 0.7rem;
        border-radius: 0.7rem; font-size: 0.78rem; font-weight: 500;
        color: #64748b; text-decoration: none; transition: all 0.18s;
        white-space: nowrap; min-width: 0;
    }
    .wa-nav-tab a.active {
        background: white; color: #16a34a;
        box-shadow: 0 2px 10px rgba(22,163,74,0.15);
    }
    @media (max-width: 767px) {
        .wa-nav-tab a { font-size: 0.74rem; padding: 0.45rem 0.5rem; }
    }

    /* ── Main panel ─────────────────────────────────────────────── */
    .wa-panel {
        border-radius: 1.35rem;
        border: 1px solid rgba(148,163,184,0.15);
        background: linear-gradient(160deg, rgba(255,255,255,0.97) 0%, rgba(248,252,255,0.92) 100%);
        box-shadow: 0 20px 60px -30px rgba(15,23,42,0.22), 0 1px 0 rgba(255,255,255,0.9) inset;
        overflow: hidden;
    }
    .wa-panel-header {
        padding: 0.85rem 1.1rem;
        border-bottom: 1px solid rgba(148,163,184,0.1);
        background: linear-gradient(180deg,rgba(255,255,255,0.85),rgba(243,248,255,0.6));
        display: flex; align-items: center; justify-content: space-between; gap: 0.6rem;
        flex-wrap: wrap;
    }
    @media (max-width: 639px) {
        .wa-panel-header {
            flex-direction: column; align-items: stretch; gap: 0.6rem;
            padding: 0.75rem 0.85rem;
        }
        .wa-panel-header .header-actions {
            display: flex; gap: 0.4rem; flex-wrap: wrap;
        }
        .wa-panel-header .header-actions .btn { flex: 1; justify-content: center; font-size: 0.72rem; padding: 0.42rem 0.55rem; min-width: 0; white-space: nowrap; }
    }

    /* ── Schedule row card ───────────────────────────────────────── */
    .sch-row {
        display: flex; align-items: center; gap: 0.85rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.7);
        transition: background 0.15s;
    }
    .sch-row:last-child { border-bottom: none; }
    .sch-row:hover { background: rgba(37,211,102,0.035); }
    .sch-avatar {
        width: 2.4rem; height: 2.4rem; border-radius: 0.75rem; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.78rem; font-weight: 700; color: white;
        box-shadow: 0 3px 10px rgba(37,211,102,0.25);
    }
    .sch-avatar.once   { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 3px 10px rgba(245,158,11,0.3); }
    .sch-avatar.daily  { background: linear-gradient(135deg, #25d366, #128c7e); }
    .sch-avatar.weekly { background: linear-gradient(135deg, #6366f1, #4f46e5); box-shadow: 0 3px 10px rgba(99,102,241,0.3); }
    .sch-avatar.cron   { background: linear-gradient(135deg, #ec4899, #db2777); box-shadow: 0 3px 10px rgba(236,72,153,0.3); }
    .sch-info { flex: 1; min-width: 0; }
    .sch-name { font-size: 0.88rem; font-weight: 600; color: #1e293b; }
    .sch-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 0.1rem; }
    .sch-right {
        display: flex; align-items: center; gap: 0.7rem;
        margin-left: auto; flex-shrink: 0;
    }
    .sch-badges { display: flex; gap: 0.3rem; flex-shrink: 0; align-items: center; flex-wrap: wrap; }
    .sch-badge {
        padding: 0.18rem 0.55rem; border-radius: 99px; font-size: 0.68rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.25rem;
    }
    .sch-badge-type   { background: #dbeafe; color: #1d4ed8; }
    .sch-badge-active  { background: #dcfce7; color: #15803d; }
    .sch-badge-paused  { background: #fee2e2; color: #dc2626; }
    .sch-badge-sent    { background: #f0fdf4; color: #16a34a; font-variant-numeric: tabular-nums; }
    .sch-stats { display: flex; gap: 0.5rem; flex-shrink: 0; align-items: center; }
    .sch-stat {
        text-align: center; padding: 0.25rem 0.5rem;
        background: rgba(241,245,249,0.7); border-radius: 0.5rem;
        min-width: 3.75rem;
        border: 1px solid rgba(226,232,240,0.9);
    }
    .sch-stat-val  { font-size: 0.85rem; font-weight: 700; color: #16a34a; line-height: 1.2; }
    .sch-stat-label{ font-size: 0.6rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.04em; }
    .sch-actions {
        display: flex; gap: 0.45rem; flex-shrink: 0; flex-wrap: wrap;
        justify-content: flex-end; max-width: 23rem;
    }
    .sch-action-btn {
        display: inline-flex; align-items: center; gap: 0.35rem;
        min-height: 2rem; padding: 0.38rem 0.7rem;
        border-radius: 0.7rem; border: 1px solid rgba(226,232,240,0.95);
        background: #fff; color: #475569; cursor: pointer;
        font-size: 0.72rem; font-weight: 700; line-height: 1;
        transition: all 0.16s ease;
        box-shadow: 0 2px 8px rgba(15,23,42,0.04);
        white-space: nowrap;
    }
    .sch-action-btn svg { width: 0.85rem; height: 0.85rem; flex-shrink: 0; }
    .sch-action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(15,23,42,0.08);
        border-color: rgba(148,163,184,0.9);
    }
    .sch-action-btn.import { color: #15803d; background: #f0fdf4; border-color: rgba(34,197,94,0.22); }
    .sch-action-btn.sample { color: #475569; background: #f8fafc; }
    .sch-action-btn.send { color: #047857; background: #ecfdf5; border-color: rgba(16,185,129,0.22); }
    .sch-action-btn.edit { color: #1d4ed8; background: #eff6ff; border-color: rgba(59,130,246,0.22); }
    .sch-action-btn.delete { color: #dc2626; background: #fef2f2; border-color: rgba(239,68,68,0.22); }
    .sch-action-btn.delete:hover { border-color: rgba(239,68,68,0.4); }
    .sch-action-btn input { display: none; }
    .sch-action-label {
        overflow: hidden; text-overflow: ellipsis;
    }

    @media (max-width: 767px) {
        .sch-row {
            flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem 0.85rem;
            position: relative;
        }
        .sch-avatar { width: 2.2rem; height: 2.2rem; font-size: 0.7rem; }
        .sch-right {
            order: 4; width: 100%; margin-left: 0; padding-left: 2.7rem;
            justify-content: space-between; align-items: flex-start; flex-wrap: wrap;
        }
        .sch-badges { width: auto; padding-left: 0; margin-top: 0; }
        .sch-stats { width: auto; padding-left: 0; justify-content: flex-start; }
        .sch-actions { width: 100%; margin-left: 0; gap: 0.35rem; }
        .sch-action-btn { flex: 1 1 calc(50% - 0.35rem); justify-content: center; }
    }

    /* ── Empty state ────────────────────────────────────────────── */
    .wa-empty {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        padding: 3rem 1rem; gap: 0.75rem;
    }
    .wa-empty-icon {
        width: 3.5rem; height: 3.5rem; border-radius: 1rem;
        background: linear-gradient(135deg, #f0fdf4, #dcfce7);
        border: 1px solid rgba(22,163,74,0.12);
        display: flex; align-items: center; justify-content: center;
    }

    /* ── Rich Editor ───────────────────────────────────────────── */
    .wa-editor-wrap {
        border: 1.5px solid #e2e8f0; border-radius: 1rem;
        overflow: hidden; background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .wa-editor-wrap:focus-within {
        border-color: #25d366;
        box-shadow: 0 0 0 3px rgba(37,211,102,0.13);
    }
    .wa-editor-toolbar {
        display: flex; align-items: center; gap: 0.15rem;
        padding: 0.45rem 0.6rem;
        border-bottom: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #fafbfc, #f8fafc);
        flex-wrap: wrap;
    }
    .wa-editor-toolbar .tb-sep {
        width: 1px; height: 1.3rem; background: #e2e8f0; margin: 0 0.25rem; flex-shrink: 0;
    }
    .wa-tb-btn {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; border-radius: 0.5rem;
        border: none; background: transparent; cursor: pointer;
        color: #64748b; font-size: 0.82rem; transition: all 0.12s;
        flex-shrink: 0; position: relative;
    }
    .wa-tb-btn:hover { background: #f1f5f9; color: #1e293b; }
    .wa-tb-btn.active { background: #dcfce7; color: #16a34a; }
    .wa-editor-textarea {
        width: 100%; min-height: 8rem; max-height: 22rem; padding: 0.75rem 0.9rem;
        border: none; outline: none; resize: vertical;
        font-size: 0.9rem; line-height: 1.6; color: #1e293b;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: transparent;
    }
    .wa-editor-textarea::placeholder { color: #94a3b8; }
    .wa-editor-footer {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.4rem 0.75rem; border-top: 1px solid #f1f5f9;
        background: linear-gradient(180deg, #f8fafc, #fafbfc); gap: 0.5rem; flex-wrap: wrap;
    }
    .wa-editor-footer .char-count {
        font-size: 0.7rem; color: #94a3b8; font-variant-numeric: tabular-nums;
    }
    .wa-var-chips { display: flex; gap: 0.3rem; flex-wrap: wrap; align-items: center; }
    .wa-var-chip {
        display: inline-flex; align-items: center; gap: 0.2rem;
        padding: 0.2rem 0.5rem; border-radius: 0.4rem; cursor: pointer;
        background: linear-gradient(135deg,#f0fdf4,#dcfce7);
        border: 1px solid rgba(22,163,74,0.18);
        color: #15803d; font-size: 0.72rem; font-weight: 600;
        font-family: 'SF Mono', SFMono-Regular, Consolas, monospace;
        transition: all 0.12s; user-select: none;
    }
    .wa-var-chip:hover { background: #dcfce7; border-color: rgba(22,163,74,0.35); transform: translateY(-1px); box-shadow: 0 2px 6px rgba(22,163,74,0.12); }

    /* ── Emoji picker ──────────────────────────────────────────── */
    .emoji-picker-wrap { position: relative; display: inline-flex; }
    .emoji-picker-dropdown {
        position: absolute; top: calc(100% + 6px); left: 50%; transform: translateX(-50%);
        width: 290px; max-height: 270px;
        background: white; border-radius: 1rem; z-index: 30;
        box-shadow: 0 16px 48px rgba(15,23,42,0.2), 0 0 0 1px rgba(0,0,0,0.04);
        overflow: hidden; display: flex; flex-direction: column;
    }
    @media (max-width: 639px) {
        .emoji-picker-dropdown {
            position: fixed; left: 50% !important; top: auto !important;
            bottom: 4rem; transform: translateX(-50%);
            width: calc(100vw - 2rem); max-width: 320px;
        }
    }
    .emoji-picker-search {
        padding: 0.5rem 0.6rem; border-bottom: 1px solid #f1f5f9; flex-shrink: 0;
    }
    .emoji-picker-search input {
        width: 100%; padding: 0.38rem 0.6rem; border: 1px solid #e2e8f0;
        border-radius: 0.5rem; font-size: 0.78rem; outline: none;
    }
    .emoji-picker-search input:focus { border-color: #25d366; }
    .emoji-picker-tabs {
        display: flex; gap: 0.15rem; padding: 0.3rem 0.5rem;
        border-bottom: 1px solid #f1f5f9; flex-shrink: 0; overflow-x: auto;
    }
    .emoji-picker-tabs button {
        width: auto !important; height: auto !important; padding: 0.2rem 0.35rem;
        font-size: 1rem; border-radius: 0.35rem;
    }
    .emoji-picker-tabs button.active { background: #f0fdf4; }
    .emoji-picker-grid {
        flex: 1; overflow-y: auto; padding: 0.4rem;
        display: grid; grid-template-columns: repeat(8, 1fr); gap: 0.15rem;
        align-content: start;
    }
    .emoji-picker-grid button {
        width: auto !important; height: auto !important;
        padding: 0.25rem; font-size: 1.2rem; border-radius: 0.35rem;
        display: flex; align-items: center; justify-content: center;
    }
    .emoji-picker-grid button:hover { background: #f0fdf4; transform: scale(1.15); }

    /* ── WhatsApp Preview Bubble ───────────────────────────────── */
    .wa-preview-bubble {
        position: relative;
        background: #dcf8c6; border-radius: 0.5rem 0.85rem 0.85rem 0.85rem;
        padding: 0.6rem 0.75rem 1.4rem;
        font-size: 0.85rem; line-height: 1.5; color: #111b21;
        max-width: 100%; word-break: break-word;
        box-shadow: 0 1px 1px rgba(0,0,0,0.06);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .wa-preview-bubble::before {
        content: ''; position: absolute; top: 0; left: -8px;
        width: 0; height: 0;
        border-top: 0 solid transparent; border-bottom: 8px solid transparent;
        border-right: 8px solid #dcf8c6;
    }
    .wa-preview-bubble .wa-time {
        position: absolute; bottom: 0.3rem; right: 0.6rem;
        font-size: 0.65rem; color: #667781;
    }
    .wa-preview-bg {
        background-image: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23e2e8f0' fill-opacity='0.3'%3E%3Ccircle cx='10' cy='10' r='1.5'/%3E%3Ccircle cx='40' cy='10' r='1'/%3E%3Ccircle cx='70' cy='30' r='1.2'/%3E%3Ccircle cx='20' cy='50' r='1'/%3E%3Ccircle cx='60' cy='60' r='1.5'/%3E%3Ccircle cx='35' cy='75' r='1'/%3E%3C/g%3E%3C/svg%3E");
        background-color: #efeae2;
        border-radius: 0.65rem;
        padding: 1rem;
    }

    /* Mobile schedule cards */
    .sch-mobile-card {
        padding: 0.85rem;
        border-bottom: 1px solid rgba(226,232,240,0.7);
    }
    .sch-mobile-card:last-child { border-bottom: none; }

    /* ── Modal ──────────────────────────────────────────────────── */
    .sch-modal-overlay {
        position: fixed; inset: 0; z-index: 50;
        display: flex; align-items: flex-start; justify-content: center;
        background: rgba(15,23,42,0.55); backdrop-filter: blur(6px);
        overflow-y: auto; padding: 1.5rem 0.75rem;
    }
    .sch-modal {
        width: 100%; max-width: 42rem;
        background: white; border-radius: 1.5rem;
        box-shadow: 0 40px 80px -20px rgba(15,23,42,0.4), 0 0 0 1px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .sch-modal-head {
        padding: 1.1rem 1.35rem;
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        color: white;
        display: flex; align-items: center; justify-content: space-between;
    }
    .sch-modal-head-icon {
        width: 2.5rem; height: 2.5rem; border-radius: 0.75rem;
        background: rgba(255,255,255,0.2); flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        color: white;
    }
    .sch-modal-body { padding: 1.25rem 1.35rem; }
    .sch-modal-foot {
        padding: 0.9rem 1.35rem;
        border-top: 1px solid rgba(226,232,240,0.8);
        background: rgba(248,250,252,0.7);
        display: flex; gap: 0.6rem;
        border-radius: 0 0 1.5rem 1.5rem;
    }
    .sch-section {
        padding: 1rem; margin-bottom: 0.85rem;
        border-radius: 1rem; border: 1px solid rgba(148,163,184,0.12);
        background: rgba(248,250,252,0.6);
    }
    .sch-section-title {
        font-size: 0.72rem; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 0.7rem;
        display: flex; align-items: center; gap: 0.4rem;
    }
    .sch-section-title svg { width: 0.9rem; height: 0.9rem; opacity: 0.6; }

    @media (max-width: 639px) {
        .sch-modal { border-radius: 1.25rem; }
        .sch-modal-head { padding: 0.85rem 1rem; }
        .sch-modal-body { padding: 1rem; }
        .sch-section { padding: 0.75rem; }
    }

    @media (max-width: 767px) {
        .wa-nav-tab a { font-size: 0.74rem; padding: 0.45rem 0.5rem; }
    }
</style>

<div class="flex flex-col wa-workspace" style="min-height:0;" x-data="waSchedules()" x-init="init()">

    {{-- Tabs --}}
    <div class="wa-nav-tab">
        <a href="/admin/whatsapp">Dashboard</a>
        <a href="/admin/whatsapp/groups">Groups</a>
        <a href="/admin/whatsapp/schedules" class="active">Schedules</a>
        <a href="/admin/whatsapp/history">History</a>
    </div>

    <div class="flex flex-col lg:flex-row gap-3 lg:flex-1 lg:min-h-0">

        {{-- Schedule list --}}
        <div class="flex-1 wa-panel flex flex-col lg:min-h-0" style="min-height:200px;">
            <div class="wa-panel-header">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#dbeafe,#bfdbfe);border:1px solid rgba(59,130,246,0.15)">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Message Schedules</div>
                        <div class="text-xs text-slate-400" x-text="schedules.length + ' schedules'"></div>
                    </div>
                </div>
                <div class="flex gap-2 header-actions">
                    <button @click="openForm()" class="btn btn-sm btn-primary gap-1.5" style="border-radius:0.6rem">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Schedule
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto">
                {{-- Empty state --}}
                <template x-if="schedules.length === 0">
                    <div class="wa-empty">
                        <div class="wa-empty-icon">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-slate-600">No schedules yet</div>
                        <div class="text-xs text-slate-400 text-center max-w-52">Create your first schedule to start sending automated WhatsApp messages.</div>
                    </div>
                </template>

                {{-- Schedule rows --}}
                <template x-for="s in schedules" :key="s.id">
                    <div class="sch-row">
                        {{-- Avatar --}}
                        <div class="sch-avatar" :class="s.schedule_type"
                             x-text="s.schedule_type === 'once' ? '1x' : s.schedule_type === 'daily' ? 'D' : s.schedule_type === 'weekly' ? 'W' : '*'">
                        </div>

                        {{-- Info --}}
                        <div class="sch-info">
                            <div class="sch-name" x-text="s.name"></div>
                            <div class="sch-meta">
                                <span x-show="s.schedule_type==='once'" x-text="s.scheduled_at ? new Date(s.scheduled_at).toLocaleString() : 'Not scheduled'"></span>
                                <span x-show="s.schedule_type==='daily'" x-text="'Daily at ' + (s.schedule_time||'-')"></span>
                                <span x-show="s.schedule_type==='weekly'" x-text="days[s.schedule_day] + ' at ' + (s.schedule_time||'')"></span>
                                <span x-show="s.schedule_type==='cron'" x-text="s.cron_expression"></span>
                                <span class="mx-1">&middot;</span>
                                <span x-text="(s.groups_data||[]).map(g => g.name).join(', ') || 'No groups'"></span>
                            </div>
                        </div>

                        <div class="sch-right">
                            {{-- Badges --}}
                            <div class="sch-badges">
                                <span class="sch-badge sch-badge-type" x-text="s.schedule_type"></span>
                                <button @click="toggleSchedule(s)"
                                    :class="s.is_active ? 'sch-badge sch-badge-active' : 'sch-badge sch-badge-paused'"
                                    class="cursor-pointer hover:opacity-75 border-0"
                                    x-text="s.is_active ? 'Active' : 'Paused'">
                                </button>
                            </div>

                            {{-- Stats --}}
                            <div class="sch-stats hidden md:flex">
                                <div class="sch-stat">
                                    <div class="sch-stat-val" x-text="s.sent_count"></div>
                                    <div class="sch-stat-label">Sent</div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="sch-actions">
                                <label class="sch-action-btn import" title="Import Excel">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <span class="sch-action-label">Import</span>
                                    <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="importExcelForSchedule(s, $event)">
                                </label>
                                <button @click="downloadSampleExcelFor(s)" class="sch-action-btn sample" title="Download Sample">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="sch-action-label">Sample</span>
                                </button>
                                <button @click="sendNow(s)" class="sch-action-btn send" title="Send Now">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    <span class="sch-action-label">Send</span>
                                </button>
                                <button @click="editSchedule(s)" class="sch-action-btn edit" title="Edit Schedule">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span class="sch-action-label">Edit</span>
                                </button>
                                <button @click="deleteSchedule(s)" class="sch-action-btn delete" title="Delete Schedule">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span class="sch-action-label">Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Schedule Form Modal --}}
    <div x-show="showModal" class="sch-modal-overlay" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="sch-modal" @click.outside="showModal=false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Green gradient header --}}
            <div class="sch-modal-head">
                <div class="flex items-center gap-3">
                    <div class="sch-modal-head-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold" x-text="editId ? 'Edit Schedule' : 'New Schedule'"></div>
                        <div class="text-xs opacity-80" x-text="editId ? 'Update your schedule settings' : 'Set up a new automated message'"></div>
                    </div>
                </div>
                <button @click="showModal=false" class="w-8 h-8 rounded-xl flex items-center justify-center hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="sch-modal-body">

                {{-- Basic Info Section --}}
                <div class="sch-section">
                    <div class="sch-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        Basic Info
                    </div>
                    <input x-model="form.name" type="text" class="form-input" placeholder="e.g. Monthly Trust Donors Update" required>
                </div>

                {{-- Recipients Section --}}
                <div class="sch-section">
                    <div class="sch-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Recipients
                    </div>
                    <div class="flex flex-wrap gap-x-4 gap-y-2">
                        @foreach($groups as $grp)
                        <label class="flex items-center gap-1.5 cursor-pointer group">
                            <input type="checkbox" value="{{ $grp->id }}" x-model="form.group_ids" class="rounded border-slate-300 text-green-600 focus:ring-green-500/30">
                            <span class="text-sm text-slate-600 group-hover:text-slate-800 transition-colors">{{ $grp->name }}</span>
                        </label>
                        @endforeach
                        @if($groups->isEmpty())
                        <p class="text-xs text-slate-400">No active groups. <a href="/admin/whatsapp/groups" class="text-green-600 underline">Add groups first</a>.</p>
                        @endif
                    </div>
                </div>

                {{-- Message Template Section --}}
                <div class="sch-section">
                    <div class="sch-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        Message Template
                    </div>
                    <div class="wa-editor-wrap">
                        {{-- Toolbar --}}
                        <div class="wa-editor-toolbar">
                            <button type="button" class="wa-tb-btn" @click="editorWrap('*')" title="Bold">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                            </button>
                            <button type="button" class="wa-tb-btn" @click="editorWrap('_')" title="Italic">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="19" y1="4" x2="10" y2="4"/><line x1="14" y1="20" x2="5" y2="20"/><line x1="15" y1="4" x2="9" y2="20"/></svg>
                            </button>
                            <button type="button" class="wa-tb-btn" @click="editorWrap('~')" title="Strikethrough">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="12" x2="20" y2="12"/><path d="M17.5 7.5A4 4 0 0012 4H7v8h5a4 4 0 001.8-7.5"/><path d="M7 12h5a4 4 0 010 8H7v-8"/></svg>
                            </button>
                            <button type="button" class="wa-tb-btn" @click="editorWrap('```')" title="Monospace">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                            </button>

                            <div class="tb-sep"></div>

                            <button type="button" class="wa-tb-btn" @click="editorUppercase()" title="UPPERCASE">
                                <span style="font-weight:800;font-size:0.72rem;letter-spacing:0.03em">Aa</span>
                            </button>
                            <button type="button" class="wa-tb-btn" @click="editorNewline()" title="New Line ↵">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 01-4 4H4"/></svg>
                            </button>

                            <div class="tb-sep"></div>

                            {{-- Emoji picker --}}
                            <div class="emoji-picker-wrap" @keydown.escape.prevent="emojiOpen = false">
                                <button type="button" class="wa-tb-btn" @click="emojiOpen = !emojiOpen"
                                        :class="emojiOpen ? 'active' : ''" title="Emoji">
                                    <span style="font-size:1.05rem">😊</span>
                                </button>
                                <div x-show="emojiOpen" x-cloak
                                     @click.outside="emojiOpen = false"
                                     class="emoji-picker-dropdown"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100">
                                    <div class="emoji-picker-search">
                                        <input type="text" x-model="emojiSearch" placeholder="Search emoji…" @click.stop>
                                    </div>
                                    <div class="emoji-picker-tabs">
                                        <template x-for="(cat, ci) in emojiCategories" :key="ci">
                                            <button type="button"
                                                    @click="emojiTab = ci; emojiSearch = ''"
                                                    :class="emojiTab === ci ? 'active' : ''"
                                                    x-text="cat.icon"></button>
                                        </template>
                                    </div>
                                    <div class="emoji-picker-grid">
                                        <template x-for="em in filteredEmojis" :key="em">
                                            <button type="button" @click="editorInsertEmoji(em)" x-text="em"></button>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="tb-sep"></div>

                            {{-- Preview toggle --}}
                            <button type="button" class="wa-tb-btn" @click="showPreview = !showPreview"
                                    :class="showPreview ? 'active' : ''" title="WhatsApp Preview">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>

                        {{-- Textarea --}}
                        <textarea x-ref="tplTextarea"
                            x-model="form.message_template"
                            @input="form.message_template = $event.target.value"
                            class="wa-editor-textarea" rows="5"
                            placeholder="Type your WhatsApp message template here...&#10;&#10;Use {variable} placeholders for dynamic content.&#10;e.g. Dear *{name}*, your amount is ₹{amount}"></textarea>

                        {{-- Footer with var chips + char count --}}
                        <div class="wa-editor-footer">
                            <div class="wa-var-chips">
                                <span class="text-xs text-slate-400 mr-1" style="line-height:1.6">Insert:</span>
                                <template x-for="col in columns" :key="col">
                                    <button type="button" class="wa-var-chip" @click="editorInsertVar(col)" x-text="'{' + col + '}'"></button>
                                </template>
                            </div>
                            <div class="char-count" x-text="(form.message_template || '').length + ' chars'"></div>
                        </div>
                    </div>

                    {{-- WhatsApp-style live preview --}}
                    <div x-show="showPreview" x-cloak class="mt-3"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 2C6.477 2 2 6.477 2 12c0 1.89.525 3.66 1.438 5.168L2 22l4.832-1.438A9.955 9.955 0 0012 22c5.523 0 10-4.477 10-10S17.523 2 12 2zm0 18a7.96 7.96 0 01-4.113-1.14l-.287-.172-2.986.783.797-2.907-.188-.299A7.96 7.96 0 014 12c0-4.411 3.589-8 8-8s8 3.589 8 8-3.589 8-8 8z"/></svg>
                            <span class="text-xs font-semibold text-green-700">WhatsApp Preview</span>
                        </div>
                        <div class="wa-preview-bg">
                            <div class="wa-preview-bubble" x-html="renderWaPreview()">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">
                        <p class="text-xs text-slate-400">
                            Formatting: <code class="text-xs bg-slate-100 px-1 rounded">*bold*</code>
                            <code class="text-xs bg-slate-100 px-1 rounded">_italic_</code>
                            <code class="text-xs bg-slate-100 px-1 rounded">~strike~</code>
                            <code class="text-xs bg-slate-100 px-1 rounded">```mono```</code>
                        </p>
                    </div>
                </div>

                {{-- Data Rows Section --}}
                <div class="sch-section">
                    <div class="sch-section-title" style="margin-bottom:0.35rem">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                        Data Rows
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-slate-400">Upload .xlsx/.csv — first row = headers, rest = data.</p>
                        <div class="flex gap-2">
                            <label class="btn btn-sm text-xs gap-1 cursor-pointer" style="border-color:rgba(37,211,102,0.3);color:#16a34a;background:rgba(220,252,231,0.6)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Import Excel
                                <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="importExcelRows($event)">
                            </label>
                            <button type="button" @click="downloadSampleExcel()" class="btn btn-sm text-xs gap-1" title="Download sample Excel">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Sample
                            </button>
                            <button type="button" @click="detectColumns()" class="btn btn-sm text-xs">Auto-detect columns</button>
                            <button type="button" @click="addRow()" class="btn btn-sm btn-primary text-xs">+ Add Row</button>
                        </div>
                    </div>

                    {{-- Column headers --}}
                    <div class="flex gap-2 mb-1 px-1">
                        <template x-for="col in columns" :key="col">
                            <div class="flex-1 text-xs font-semibold text-slate-500 uppercase" x-text="col"></div>
                        </template>
                        <div class="w-7"></div>
                    </div>

                    {{-- Rows --}}
                    <div class="flex flex-col gap-1.5 max-h-56 overflow-y-auto pr-1">
                        <template x-for="(row, ri) in form.data_rows" :key="ri">
                            <div class="flex gap-2 items-center">
                                <template x-for="col in columns" :key="col">
                                    <input :placeholder="col"
                                        :value="row[col] || ''"
                                        @input="row[col] = $event.target.value"
                                        type="text" class="form-input flex-1 text-sm py-1.5">
                                </template>
                                <button type="button" @click="removeRow(ri)" class="icon-action icon-action-danger flex-shrink-0 w-7 h-7">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>

                    {{-- Preview --}}
                    <template x-if="form.data_rows.length > 0 && form.message_template">
                        <div class="mt-2 p-2.5 bg-green-50 border border-green-100 rounded-lg">
                            <p class="text-xs font-semibold text-green-700 mb-1">Preview (first row):</p>
                            <p class="text-sm text-green-800 whitespace-pre-wrap" x-text="previewMessage()"></p>
                        </div>
                    </template>
                </div>

                {{-- Schedule Section --}}
                <div class="sch-section">
                    <div class="sch-section-title">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Schedule
                    </div>

                    <div class="mb-3">
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Type</label>
                        <select x-model="form.schedule_type" class="form-input">
                            <option value="once">Once (at specific date & time)</option>
                            <option value="daily">Daily (repeat every day)</option>
                            <option value="weekly">Weekly (repeat every week)</option>
                            <option value="cron">Custom Cron Expression</option>
                        </select>
                    </div>

                    {{-- Once: datetime --}}
                    <div x-show="form.schedule_type === 'once'" class="mb-3">
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Send At</label>
                        <input x-model="form.scheduled_at" type="datetime-local" class="form-input">
                    </div>

                    {{-- Daily: time --}}
                    <div x-show="form.schedule_type === 'daily'" class="mb-3">
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Send Time (HH:MM)</label>
                        <input x-model="form.schedule_time" type="time" class="form-input" placeholder="08:00">
                    </div>

                    {{-- Weekly: day + time --}}
                    <div x-show="form.schedule_type === 'weekly'" class="flex gap-3 mb-3">
                        <div class="flex-1">
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Day of Week</label>
                            <select x-model="form.schedule_day" class="form-input">
                                <option value="0">Sunday</option>
                                <option value="1">Monday</option>
                                <option value="2">Tuesday</option>
                                <option value="3">Wednesday</option>
                                <option value="4">Thursday</option>
                                <option value="5">Friday</option>
                                <option value="6">Saturday</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-medium text-slate-600 mb-1 block">Send Time</label>
                            <input x-model="form.schedule_time" type="time" class="form-input">
                        </div>
                    </div>

                    {{-- Cron --}}
                    <div x-show="form.schedule_type === 'cron'" class="mb-3">
                        <label class="text-xs font-medium text-slate-600 mb-1 block">Cron Expression</label>
                        <input x-model="form.cron_expression" type="text" class="form-input font-mono" placeholder="0 8 * * *">
                        <p class="text-xs text-slate-400 mt-1">e.g. <code class="px-1 bg-slate-100 rounded text-xs">0 8 * * *</code> = every day at 8:00 AM</p>
                    </div>

                    {{-- Active toggle --}}
                    <div class="flex items-center gap-2.5 pt-1">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input x-model="form.is_active" type="checkbox" class="sr-only peer">
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-500"></div>
                        </label>
                        <span class="text-sm text-slate-600">Active <span class="text-xs text-slate-400">(will be picked by scheduler)</span></span>
                    </div>
                </div>

            </div>

            {{-- Footer --}}
            <div class="sch-modal-foot">
                <button @click="showModal=false" class="btn flex-1" style="border-radius:0.65rem">Cancel</button>
                <button @click="saveSchedule()" class="btn btn-primary flex-1" style="border-radius:0.65rem" :disabled="saving">
                    <svg x-show="saving" class="animate-spin w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="saving ? 'Saving...' : (editId ? 'Update Schedule' : 'Create Schedule')"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
function waSchedules() {
    return {
        schedules: [],
        showModal: false,
        editId: null,
        saving: false,
        columns: ['name', 'amount'],
        days: ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'],
        form: {
            name: '', group_ids: [], message_template: '{name} GAVE MONEY TO {amount} TRUST',
            data_rows: [{ name: '', amount: '' }],
            schedule_type: 'once', cron_expression: '', scheduled_at: '',
            schedule_time: '08:00', schedule_day: 1, is_active: true,
        },

        // ── Editor state ──────────────────────────────
        emojiOpen: false,
        emojiSearch: '',
        emojiTab: 0,
        showPreview: false,
        emojiCategories: [
            { icon: '😀', name: 'Smileys', emojis: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','😊','😇','🥰','😍','🤩','😘','😗','😚','😋','😛','😜','🤪','😝','🤑','🤗','🤭','🤫','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','🤥','😌','😔','😪','🤤','😴','😷','🤒','🤕','🤢','🤮','🥵','🥶','🥴','😵','🤯','🤠','🥳','🥸','😎','🤓','🧐'] },
            { icon: '👋', name: 'Gestures', emojis: ['👋','🤚','🖐️','✋','🖖','👌','🤌','🤏','✌️','🤞','🤟','🤘','🤙','👈','👉','👆','🖕','👇','☝️','👍','👎','✊','👊','🤛','🤜','👏','🙌','👐','🤲','🤝','🙏','✍️','💪','🦾','🦿'] },
            { icon: '❤️', name: 'Symbols', emojis: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','⭐','🌟','✨','⚡','🔥','💯','✅','❌','❓','❗','💰','💵','💸','🎯','🏆','🎉','🎊','📌','📍','💡','📢','📣'] },
            { icon: '🕐', name: 'Objects', emojis: ['📱','💻','⌨️','🖥️','📞','☎️','📧','✉️','📮','📪','📬','📭','📦','📋','📝','📄','📃','📑','🗓️','📅','📆','🕐','🕑','🕒','⏰','⏳','⌛','🔔','🔕','📣','📢'] },
            { icon: '🙏', name: 'Religion', emojis: ['🙏','🕉️','☪️','✝️','☦️','🕎','🔯','☸️','☯️','🕌','🛕','⛪','🕍','🕋','🛐','💒','🤲','📿','🪔','🎆'] },
            { icon: '💰', name: 'Money', emojis: ['💰','💵','💴','💶','💷','💸','💳','🧾','💹','📈','📉','📊','🏦','🏧','💱','💲','🪙','💎','⚖️','🏷️'] },
        ],

        get filteredEmojis() {
            const cat = this.emojiCategories[this.emojiTab];
            if (!cat) return [];
            return cat.emojis;
        },

        // ── Editor methods ────────────────────────────
        _ta() { return this.$refs.tplTextarea; },

        editorWrap(marker) {
            const ta = this._ta(); if (!ta) return;
            const s = ta.selectionStart, e = ta.selectionEnd, t = ta.value;
            const sel = t.substring(s, e);
            const wrapped = sel ? marker + sel + marker : marker + 'text' + marker;
            this.form.message_template = t.substring(0, s) + wrapped + t.substring(e);
            this.$nextTick(() => {
                ta.focus();
                if (sel) { ta.setSelectionRange(s, s + wrapped.length); }
                else { ta.setSelectionRange(s + marker.length, s + marker.length + 4); }
            });
        },

        editorUppercase() {
            const ta = this._ta(); if (!ta) return;
            const s = ta.selectionStart, e = ta.selectionEnd, t = ta.value;
            if (s === e) {
                this.form.message_template = t.toUpperCase();
                this.$nextTick(() => { ta.focus(); ta.setSelectionRange(s, e); });
            } else {
                const upper = t.substring(s, e).toUpperCase();
                this.form.message_template = t.substring(0, s) + upper + t.substring(e);
                this.$nextTick(() => { ta.focus(); ta.setSelectionRange(s, s + upper.length); });
            }
        },

        editorNewline() {
            const ta = this._ta(); if (!ta) return;
            const pos = ta.selectionStart, t = ta.value;
            this.form.message_template = t.substring(0, pos) + '\n' + t.substring(pos);
            this.$nextTick(() => { ta.focus(); ta.setSelectionRange(pos + 1, pos + 1); });
        },

        editorInsertEmoji(emoji) {
            const ta = this._ta(); if (!ta) return;
            const pos = ta.selectionStart || ta.value.length;
            const t = ta.value;
            this.form.message_template = t.substring(0, pos) + emoji + t.substring(pos);
            this.emojiOpen = false;
            this.$nextTick(() => { ta.focus(); ta.setSelectionRange(pos + emoji.length, pos + emoji.length); });
        },

        editorInsertVar(col) {
            const ta = this._ta(); if (!ta) return;
            const pos = ta.selectionStart || ta.value.length;
            const t = ta.value;
            const v = '{' + col + '}';
            this.form.message_template = t.substring(0, pos) + v + t.substring(pos);
            this.$nextTick(() => { ta.focus(); ta.setSelectionRange(pos + v.length, pos + v.length); });
        },

        // ── WhatsApp preview renderer ─────────────────
        renderWaPreview() {
            let msg = this.form.message_template || '';
            // Replace variables with first data row values or highlight
            if (this.form.data_rows.length > 0) {
                const row = this.form.data_rows[0];
                Object.entries(row).forEach(([k, v]) => {
                    const val = v || '{' + k + '}';
                    msg = msg.replace(new RegExp('\\{' + k + '\\}', 'gi'), val);
                });
            }
            // Escape HTML first
            msg = msg.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            // Apply WhatsApp formatting
            msg = msg.replace(/\*([^*\n]+)\*/g, '<strong>$1</strong>');
            msg = msg.replace(/_([^_\n]+)_/g, '<em>$1</em>');
            msg = msg.replace(/~([^~\n]+)~/g, '<del>$1</del>');
            msg = msg.replace(/```([^`]+)```/g, '<code style="background:#f1f5f9;padding:0.1em 0.3em;border-radius:3px;font-family:monospace;font-size:0.85em">$1</code>');
            // Highlight remaining variables
            msg = msg.replace(/\{(\w+)\}/g, '<span style="background:#fff3cd;padding:0.1em 0.3em;border-radius:3px;color:#856404;font-weight:600;font-size:0.85em">{$1}</span>');
            // Newlines
            msg = msg.replace(/\n/g, '<br>');
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            return msg + '<span class="wa-time">' + time + ' ✓✓</span>';
        },

        // ── Core schedule methods ─────────────────────
        async init() { await this.load(); },

        async load() {
            const r = await RepairBox.ajax('/admin/whatsapp/schedules', 'GET');
            this.schedules = r.data || [];
        },

        openForm() {
            window.location.href = '/admin/whatsapp/schedules/create';
        },

        editSchedule(s) {
            window.location.href = `/admin/whatsapp/schedules/${s.id}/edit`;
        },

        detectColumns() {
            this.detectColumnsFromTemplate();
        },

        detectColumnsFromTemplate() {
            const matches = this.form.message_template.matchAll(/\{(\w+)\}/g);
            const cols = [...new Set([...matches].map(m => m[1]))];
            if (cols.length > 0) {
                this.columns = cols;
                this.form.data_rows = this.form.data_rows.map(row => {
                    const newRow = { ...row };
                    cols.forEach(c => { if (!(c in newRow)) newRow[c] = ''; });
                    return newRow;
                });
            }
        },

        addRow() {
            const row = {};
            this.columns.forEach(c => row[c] = '');
            this.form.data_rows.push(row);
        },

        removeRow(i) {
            this.form.data_rows.splice(i, 1);
        },

        // ── Excel import into form data rows ─────────
        _parseExcelFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const wb = XLSX.read(data, { type: 'array' });
                        const ws = wb.Sheets[wb.SheetNames[0]];
                        const json = XLSX.utils.sheet_to_json(ws, { defval: '' });
                        resolve(json);
                    } catch (err) {
                        reject(err);
                    }
                };
                reader.onerror = reject;
                reader.readAsArrayBuffer(file);
            });
        },

        async importExcelRows(event) {
            const file = event.target.files[0];
            if (!file) return;
            event.target.value = '';
            try {
                const rows = await this._parseExcelFile(file);
                if (!rows.length) {
                    RepairBox.toast('No data rows found in file', 'error');
                    return;
                }
                const cols = Object.keys(rows[0]).map(c => c.trim().toLowerCase().replace(/\s+/g, '_'));
                const originalKeys = Object.keys(rows[0]);
                this.columns = cols;
                this.form.data_rows = rows.map(r => {
                    const row = {};
                    originalKeys.forEach((k, i) => {
                        row[cols[i]] = String(r[k] ?? '');
                    });
                    return row;
                });
                // Update template to use detected columns
                const templateVars = this.form.message_template.match(/\{(\w+)\}/g) || [];
                if (templateVars.length === 0) {
                    this.form.message_template = cols.map(c => '{' + c + '}').join(' ');
                }
                RepairBox.toast(`Imported ${rows.length} rows with ${cols.length} columns`, 'success');
            } catch (err) {
                RepairBox.toast('Failed to read Excel file', 'error');
            }
        },

        async importExcelForSchedule(schedule, event) {
            const file = event.target.files[0];
            if (!file) return;
            event.target.value = '';
            try {
                const rows = await this._parseExcelFile(file);
                if (!rows.length) {
                    RepairBox.toast('No data rows found in file', 'error');
                    return;
                }
                const cols = Object.keys(rows[0]).map(c => c.trim().toLowerCase().replace(/\s+/g, '_'));
                const originalKeys = Object.keys(rows[0]);
                const dataRows = rows.map(r => {
                    const row = {};
                    originalKeys.forEach((k, i) => {
                        row[cols[i]] = String(r[k] ?? '');
                    });
                    return row;
                });
                // Update the schedule via API
                const r = await RepairBox.ajax(`/admin/whatsapp/schedules/${schedule.id}`, 'PUT', {
                    name: schedule.name,
                    group_ids: schedule.group_ids,
                    message_template: schedule.message_template,
                    data_rows: dataRows,
                    schedule_type: schedule.schedule_type,
                    cron_expression: schedule.cron_expression || '',
                    scheduled_at: schedule.scheduled_at || '',
                    schedule_time: schedule.schedule_time || '',
                    schedule_day: schedule.schedule_day,
                    is_active: schedule.is_active,
                });
                if (r.success) {
                    RepairBox.toast(`Imported ${rows.length} rows into "${schedule.name}"`, 'success');
                    await this.load();
                }
            } catch (err) {
                RepairBox.toast('Failed to read Excel file', 'error');
            }
        },

        // ── Download sample Excel ─────────────────────
        _generateSampleXlsx(cols, filename) {
            const sampleRow = {};
            cols.forEach(c => sampleRow[c] = 'Sample ' + c);
            const ws = XLSX.utils.json_to_sheet([sampleRow], { header: cols });
            ws['!cols'] = cols.map(() => ({ wch: 20 }));
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Data');
            XLSX.writeFile(wb, filename);
        },

        downloadSampleExcel() {
            const cols = this.columns.length ? this.columns : ['name', 'amount'];
            this._generateSampleXlsx(cols, 'sample-data-rows.xlsx');
        },

        downloadSampleExcelFor(schedule) {
            const matches = schedule.message_template.matchAll(/\{(\w+)\}/g);
            let cols = [...new Set([...matches].map(m => m[1]))];
            if (!cols.length) cols = ['name', 'amount'];
            const safeName = schedule.name.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 30);
            this._generateSampleXlsx(cols, `sample-${safeName}.xlsx`);
        },

        previewMessage() {
            if (!this.form.data_rows.length) return '';
            let msg = this.form.message_template;
            const row = this.form.data_rows[0];
            Object.entries(row).forEach(([k, v]) => {
                msg = msg.replace(new RegExp('\\{' + k + '\\}', 'gi'), v || `{${k}}`);
            });
            return msg;
        },

        async saveSchedule() {
            this.saving = true;
            const url    = this.editId ? `/admin/whatsapp/schedules/${this.editId}` : '/admin/whatsapp/schedules';
            const method = this.editId ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);
            this.saving = false;
            if (r.success) {
                RepairBox.toast(this.editId ? 'Updated' : 'Created', 'success');
                this.showModal = false;
                await this.load();
            }
        },

        async deleteSchedule(s) {
            if (!await RepairBox.confirm(`Delete schedule "${s.name}"?`)) return;
            const r = await RepairBox.ajax(`/admin/whatsapp/schedules/${s.id}`, 'DELETE');
            if (r.success) { RepairBox.toast('Deleted', 'success'); await this.load(); }
        },

        async toggleSchedule(s) {
            const r = await RepairBox.ajax(`/admin/whatsapp/schedules/${s.id}/toggle`, 'POST');
            if (r.success) { s.is_active = r.data.is_active; }
        },

        async sendNow(s) {
            if (!await RepairBox.confirm(`Send "${s.name}" NOW to all groups?`)) return;
            const r = await RepairBox.ajax(`/admin/whatsapp/schedules/${s.id}/send-now`, 'POST');
            if (r.success) {
                RepairBox.toast(r.message, 'success');
                await this.load();
            }
        },
    };
}
</script>
@endsection
