@extends('layouts.app')
@section('title', 'WA Groups')
@section('page-title', 'WA Groups')
@section('content-class', 'workspace-content')

@section('content')
<style>
    /* ── Layout ─────────────────────────────────────────────────── */
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
        .wa-panel-header .header-actions .header-search-wrap { width: 100%; order: -1; }
        .wa-panel-header .header-actions .header-search-wrap input { width: 100%; }
    }

    /* ── Group row card ─────────────────────────────────────────── */
    .grp-row {
        display: flex; align-items: center; gap: 0.85rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid rgba(226,232,240,0.7);
        transition: background 0.15s;
    }
    .grp-row:last-child { border-bottom: none; }
    .grp-row:hover { background: rgba(37,211,102,0.035); }
    @media (max-width: 639px) {
        .grp-row {
            flex-wrap: wrap; gap: 0.5rem; padding: 0.7rem 0.75rem;
            position: relative;
        }
        .grp-avatar { width: 2.2rem; height: 2.2rem; font-size: 0.72rem; }
        .grp-name { font-size: 0.82rem; }
        .grp-id { font-size: 0.65rem; max-width: 180px; }
        .grp-badges {
            order: 4; width: 100%;
            padding-left: 2.7rem;
            margin-top: -0.15rem;
        }
        .grp-actions {
            position: absolute; right: 0.75rem; top: 0.7rem;
            gap: 0.15rem;
        }
    }
    .grp-avatar {
        width: 2.4rem; height: 2.4rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 700; color: white;
        background: linear-gradient(135deg, #25d366, #128c7e);
        box-shadow: 0 3px 10px rgba(37,211,102,0.35);
        letter-spacing: 0.03em;
    }
    .grp-avatar.number-type {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        box-shadow: 0 3px 10px rgba(99,102,241,0.35);
    }
    .grp-info { flex: 1; min-width: 0; }
    .grp-name { font-size: 0.88rem; font-weight: 600; color: #1e293b; }
    .grp-id   { font-size: 0.72rem; color: #94a3b8; font-family: monospace; margin-top: 0.1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .grp-badges { display: flex; gap: 0.3rem; flex-shrink: 0; align-items: center; }
    .grp-badge {
        padding: 0.18rem 0.55rem; border-radius: 99px; font-size: 0.7rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.25rem;
    }
    .grp-badge-group  { background: #dbeafe; color: #1d4ed8; }
    .grp-badge-number { background: #ede9fe; color: #6d28d9; }
    .grp-badge-active { background: #dcfce7; color: #15803d; }
    .grp-badge-paused { background: #fee2e2; color: #dc2626; }
    .grp-actions { display: flex; gap: 0.25rem; flex-shrink: 0; }

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

    /* ── Form panel / Mobile overlay ────────────────────────────── */
    .wa-form-panel {
        border-radius: 1.35rem;
        border: 1px solid rgba(148,163,184,0.15);
        background: linear-gradient(160deg, rgba(255,255,255,0.98), rgba(248,252,255,0.94));
        box-shadow: 0 20px 60px -30px rgba(15,23,42,0.22);
        overflow: hidden;
    }
    @media (max-width: 1023px) {
        .form-panel-wrapper {
            position: fixed; inset: 0; z-index: 55;
            background: rgba(15,23,42,0.45);
            backdrop-filter: blur(4px);
            display: flex; align-items: flex-end; justify-content: center;
            padding: 0;
        }
        .form-panel-wrapper .wa-form-panel {
            width: 100%; max-width: 100%;
            border-radius: 1.35rem 1.35rem 0 0;
            max-height: 85vh; max-height: 85dvh;
            overflow-y: auto;
            box-shadow: 0 -10px 40px rgba(15,23,42,0.25);
        }
    }
    @media (min-width: 1024px) {
        .form-panel-wrapper {
            display: contents;
        }
    }
    .wa-form-header {
        padding: 0.85rem 1rem;
        display: flex; align-items: center; gap: 0.6rem;
        border-bottom: 1px solid rgba(148,163,184,0.1);
    }
    .wa-form-header-icon {
        width: 2rem; height: 2rem; border-radius: 0.6rem; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
    }
    .wa-field-label {
        font-size: 0.75rem; font-weight: 600; color: #475569;
        margin-bottom: 0.35rem; letter-spacing: 0.02em;
    }
    .wa-field-hint { font-size: 0.7rem; color: #94a3b8; margin-top: 0.3rem; line-height: 1.4; }

    /* ── Toggle switch ──────────────────────────────────────────── */
    .wa-toggle { position: relative; display: inline-flex; align-items: center; cursor: pointer; gap: 0.5rem; }
    .wa-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
    .wa-toggle-track {
        width: 2.2rem; height: 1.2rem; background: #e2e8f0;
        border-radius: 99px; transition: background 0.2s; flex-shrink: 0;
        position: relative;
    }
    .wa-toggle input:checked ~ .wa-toggle-track { background: #22c55e; }
    .wa-toggle-track::after {
        content: ''; position: absolute; top: 0.15rem; left: 0.15rem;
        width: 0.9rem; height: 0.9rem; background: white;
        border-radius: 50%; transition: transform 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    }
    .wa-toggle input:checked ~ .wa-toggle-track::after { transform: translateX(1rem); }

    /* ── Fetch modal ────────────────────────────────────────────── */
    .fetch-modal-overlay {
        position: fixed; inset: 0; z-index: 60;
        display: flex; align-items: center; justify-content: center;
        padding: 1rem;
        background: rgba(15,23,42,0.55);
        backdrop-filter: blur(6px);
    }
    .fetch-modal {
        width: 100%; max-width: 38rem;
        background: white; border-radius: 1.5rem;
        box-shadow: 0 40px 80px -20px rgba(15,23,42,0.4), 0 0 0 1px rgba(0,0,0,0.04);
        overflow: hidden; display: flex; flex-direction: column;
        max-height: calc(100vh - 2rem);
        max-height: calc(100dvh - 2rem);
    }
    @media (max-width: 639px) {
        .fetch-modal {
            border-radius: 1rem;
            max-height: calc(100dvh - 6rem);
        }
        .fetch-modal-head { padding: 1rem; }
        .fetch-group-card { padding: 0.6rem 0.7rem; gap: 0.65rem; }
        .fetch-group-avatar { width: 1.85rem; height: 1.85rem; font-size: 0.7rem; }
        .fetch-modal-foot { padding: 0.7rem 0.85rem; flex-wrap: wrap; }
        .fetch-import-btn { font-size: 0.78rem; padding: 0.55rem 0.85rem; }
    }
    .fetch-modal-head {
        padding: 1.25rem 1.35rem 1rem;
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        position: relative; flex-shrink: 0;
    }
    .fetch-modal-head::after {
        content: ''; position: absolute; bottom: -1px; left: 0; right: 0;
        height: 1px; background: rgba(255,255,255,0.15);
    }
    .fetch-modal-search-wrap {
        padding: 0.85rem 1.1rem 0;
        flex-shrink: 0;
        border-bottom: 1px solid rgba(226,232,240,0.8);
        padding-bottom: 0.85rem;
    }
    .fetch-modal-list { flex: 1; overflow-y: auto; padding: 0.5rem 0.6rem; }
    .fetch-group-card {
        display: flex; align-items: center; gap: 0.85rem;
        padding: 0.75rem 0.85rem; border-radius: 1rem; cursor: pointer;
        border: 1.5px solid transparent;
        transition: all 0.15s; margin-bottom: 0.35rem;
        background: rgba(248,250,252,0.8);
        user-select: none;
    }
    .fetch-group-card:hover { background: rgba(240,253,244,0.9); border-color: rgba(22,163,74,0.12); }
    .fetch-group-card.selected {
        background: linear-gradient(135deg, rgba(220,252,231,0.9), rgba(209,250,229,0.7));
        border-color: rgba(22,163,74,0.35);
        box-shadow: 0 2px 12px rgba(22,163,74,0.1);
    }
    .fetch-group-card.already-added {
        opacity: 0.55; cursor: not-allowed;
        background: rgba(241,245,249,0.5);
    }
    .fetch-check {
        width: 1.25rem; height: 1.25rem; flex-shrink: 0; border-radius: 50%;
        border: 2px solid #cbd5e1; display: flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }
    .fetch-group-card.selected .fetch-check {
        background: #16a34a; border-color: #16a34a;
    }
    .fetch-group-avatar {
        width: 2.25rem; height: 2.25rem; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 700; color: white;
        background: linear-gradient(135deg,#25d366,#128c7e);
    }
    .fetch-modal-foot {
        padding: 0.9rem 1.1rem;
        border-top: 1px solid rgba(226,232,240,0.8);
        display: flex; align-items: center; gap: 0.7rem;
        flex-shrink: 0;
        background: rgba(248,250,252,0.7);
    }
    .fetch-sel-pill {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.65rem; border-radius: 99px;
        background: linear-gradient(135deg,#dcfce7,#bbf7d0);
        color: #15803d; font-size: 0.75rem; font-weight: 700;
        border: 1px solid rgba(22,163,74,0.2);
    }
    .fetch-import-btn {
        flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.45rem;
        padding: 0.6rem 1rem; border-radius: 0.85rem; font-size: 0.85rem; font-weight: 600;
        color: white; border: none; cursor: pointer; transition: all 0.18s;
        background: linear-gradient(135deg, #25d366, #128c7e);
        box-shadow: 0 4px 14px rgba(37,211,102,0.3);
    }
    .fetch-import-btn:hover:not(:disabled) { box-shadow: 0 6px 20px rgba(37,211,102,0.4); transform: translateY(-1px); }
    .fetch-import-btn:disabled { opacity: 0.45; cursor: not-allowed; transform: none; box-shadow: none; }
    .importing-bar {
        height: 3px; background: linear-gradient(90deg,#25d366,#128c7e);
        border-radius: 99px; transition: width 0.3s ease;
    }
</style>

<div class="flex flex-col wa-workspace" style="min-height:0;" x-data="waGroups()" x-init="init()">

    {{-- Tab Nav --}}
    <div class="wa-nav-tab">
        <a href="/admin/whatsapp">Dashboard</a>
        <a href="/admin/whatsapp/groups" class="active">Groups</a>
        <a href="/admin/whatsapp/schedules">Schedules</a>
        <a href="/admin/whatsapp/history">History</a>
    </div>

    <div class="flex flex-col lg:flex-row gap-3 lg:flex-1 lg:min-h-0">

        {{-- ── Group List Panel ── --}}
        <div class="flex-1 wa-panel flex flex-col lg:min-h-0" style="min-height:200px;">
            <div class="wa-panel-header">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#dcfce7,#bbf7d0);border:1px solid rgba(22,163,74,0.15)">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-slate-800">Groups & Numbers</div>
                        <div class="text-xs text-slate-400" x-text="groups.length + ' saved'"></div>
                    </div>
                </div>
                <div class="flex gap-2 header-actions items-center">
                    {{-- Search --}}
                    <div class="relative header-search-wrap">
                        <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                        </svg>
                        <input x-model="search" type="text" placeholder="Search…"
                            class="form-input pl-8 py-1.5 text-xs w-36 h-8 rounded-lg">
                    </div>
                    <button @click="fetchFromDevice()" class="btn btn-sm gap-1.5" :disabled="fetching"
                        style="border-color:rgba(37,211,102,0.3);color:#16a34a;background:rgba(220,252,231,0.6);border-radius:0.6rem">
                        <svg class="w-3.5 h-3.5" :class="fetching?'animate-spin':''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span x-text="fetching ? 'Fetching…' : 'Sync Device'"></span>
                    </button>
                    <button @click="openAdd()" class="btn btn-sm btn-primary gap-1.5" style="border-radius:0.6rem">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add
                    </button>
                </div>
            </div>



            {{-- List --}}
            <div class="flex-1 overflow-y-auto">
                <template x-if="filteredGroups.length === 0">
                    <div class="wa-empty">
                        <div class="wa-empty-icon">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="text-sm font-semibold text-slate-600" x-text="search ? 'No matches found' : 'No groups yet'"></div>
                        <div class="text-xs text-slate-400 text-center max-w-48" x-text="search ? 'Try a different keyword' : 'Click &quot;Sync Device&quot; to import your WhatsApp groups, or add one manually.'"></div>
                    </div>
                </template>

                <template x-for="g in filteredGroups" :key="g.id">
                    <div class="grp-row">
                        {{-- Avatar --}}
                        <div :class="g.type==='number' ? 'grp-avatar number-type' : 'grp-avatar'"
                             x-text="g.name.slice(0,2).toUpperCase()"></div>

                        {{-- Info --}}
                        <div class="grp-info">
                            <div class="grp-name" x-text="g.name"></div>
                            <div class="grp-id" x-text="g.wa_id"></div>
                        </div>

                        {{-- Badges --}}
                        <div class="grp-badges">
                            <span :class="g.type==='group' ? 'grp-badge grp-badge-group' : 'grp-badge grp-badge-number'"
                                  class="grp-badge hidden sm:inline-flex">
                                <template x-if="g.type==='group'">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"/></svg>
                                </template>
                                <template x-if="g.type==='number'">
                                    <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                                </template>
                                <span x-text="g.type"></span>
                            </span>

                            <button @click="toggleActive(g)"
                                :class="g.is_active ? 'grp-badge grp-badge-active' : 'grp-badge grp-badge-paused'"
                                class="grp-badge cursor-pointer hover:opacity-75 border-0 bg-transparent">
                                <span x-text="g.is_active ? 'Active' : 'Paused'"></span>
                            </button>
                        </div>

                        {{-- Actions --}}
                        <div class="grp-actions">
                            <button @click="editGroup(g)" class="icon-action" title="Edit">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button @click="deleteGroup(g)" class="icon-action icon-action-danger" title="Delete">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Add / Edit Form Panel ── --}}
        <div class="form-panel-wrapper" x-show="showForm" x-cloak
             @click.self="showForm=false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
            <div class="w-full lg:w-72 flex-shrink-0"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 lg:translate-y-0 lg:translate-x-4"
                 x-transition:enter-end="opacity-100 translate-y-0 lg:translate-x-0">
            <div class="wa-form-panel">
                <div class="wa-form-header">
                    <div class="wa-form-header-icon"
                         :style="editId ? 'background:linear-gradient(135deg,#dbeafe,#bfdbfe);' : 'background:linear-gradient(135deg,#dcfce7,#bbf7d0);'">
                        <template x-if="!editId">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </template>
                        <template x-if="editId">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-slate-800" x-text="editId ? 'Edit Group' : 'New Group'"></div>
                        <div class="text-xs text-slate-400" x-text="editId ? 'Update group details' : 'Add a group or number'"></div>
                    </div>
                    <button @click="showForm=false" class="icon-action flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form @submit.prevent="saveGroup()" class="p-4 flex flex-col gap-3.5">
                    {{-- Name --}}
                    <div>
                        <div class="wa-field-label">Display Name *</div>
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-350 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:#94a3b8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                            <input x-model="form.name" type="text" class="form-input pl-8" placeholder="Trust Donors Group" required>
                        </div>
                    </div>

                    {{-- Type --}}
                    <div>
                        <div class="wa-field-label">Type *</div>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 cursor-pointer transition-all"
                                   :class="form.type==='group' ? 'border-green-400 bg-green-50' : 'border-slate-200 bg-slate-50 hover:border-slate-300'">
                                <input type="radio" x-model="form.type" value="group" class="hidden">
                                <svg class="w-4 h-4 flex-shrink-0" :class="form.type==='group'?'text-green-600':'text-slate-400'" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"/></svg>
                                <span class="text-xs font-semibold" :class="form.type==='group'?'text-green-700':'text-slate-500'">Group</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border-2 cursor-pointer transition-all"
                                   :class="form.type==='number' ? 'border-violet-400 bg-violet-50' : 'border-slate-200 bg-slate-50 hover:border-slate-300'">
                                <input type="radio" x-model="form.type" value="number" class="hidden">
                                <svg class="w-4 h-4 flex-shrink-0" :class="form.type==='number'?'text-violet-600':'text-slate-400'" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                                <span class="text-xs font-semibold" :class="form.type==='number'?'text-violet-700':'text-slate-500'">Number</span>
                            </label>
                        </div>
                    </div>

                    {{-- WA ID --}}
                    <div>
                        <div class="wa-field-label">WhatsApp ID *</div>
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" style="color:#94a3b8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <input x-model="form.wa_id" type="text" class="form-input pl-8 font-mono text-xs" required
                                :placeholder="form.type==='group' ? '120363xxxxxxx@g.us' : '91xxxxxxxxxx@c.us'">
                        </div>
                        <div class="wa-field-hint" x-text="form.type==='group' ? 'Group ID ends with @g.us — get it from Sync Device.' : 'Phone number with country code, ends with @c.us'"></div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <div class="wa-field-label">Note (optional)</div>
                        <textarea x-model="form.description" class="form-input text-sm resize-none" rows="2"
                            placeholder="e.g. Main trust donors group"></textarea>
                    </div>

                    {{-- Active toggle --}}
                    <div class="flex items-center justify-between py-1 px-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div>
                            <div class="text-xs font-semibold text-slate-600">Active</div>
                            <div class="text-xs text-slate-400">Include in scheduled sends</div>
                        </div>
                        <label class="wa-toggle">
                            <input type="checkbox" x-model="form.is_active">
                            <span class="wa-toggle-track"></span>
                        </label>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex gap-2 pt-0.5">
                        <button type="button" @click="showForm=false" class="btn flex-1 text-sm">Cancel</button>
                        <button type="submit" class="btn btn-primary flex-1 text-sm" :disabled="saving">
                            <span x-show="!saving" x-text="editId ? 'Update' : 'Save Group'"></span>
                            <span x-show="saving" class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                Saving…
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         FETCH FROM DEVICE MODAL
         ══════════════════════════════════════════════════════════════ --}}
    <div x-show="showFetchModal" class="fetch-modal-overlay" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         @keydown.escape.window="showFetchModal=false">

        <div class="fetch-modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             @click.stop>

            {{-- Header --}}
            <div class="fetch-modal-head">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347zM12 0C5.373 0 0 5.373 0 12c0 2.135.561 4.14 1.541 5.874L.057 23.877a.75.75 0 00.918.964l6.18-1.617A11.95 11.95 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.93 0-3.74-.517-5.293-1.42l-.38-.222-3.93 1.028.99-3.837-.247-.394A9.953 9.953 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-white font-bold text-base leading-tight">Import from WhatsApp</div>
                            <div class="text-white/70 text-xs mt-0.5">
                                <span x-text="deviceGroups.length"></span> groups found on device ·
                                <span x-text="alreadyAddedIds.size"></span> already saved
                            </div>
                        </div>
                    </div>
                    <button @click="showFetchModal=false"
                        class="w-7 h-7 rounded-lg bg-white/15 hover:bg-white/25 flex items-center justify-center transition-colors flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Progress bar (during import) --}}
                <template x-if="importing">
                    <div class="mt-3">
                        <div class="flex justify-between text-white/80 text-xs mb-1.5">
                            <span>Importing…</span>
                            <span x-text="importProgress + '/' + selectedDeviceGroups.length"></span>
                        </div>
                        <div class="h-1.5 bg-white/20 rounded-full overflow-hidden">
                            <div class="h-full bg-white rounded-full transition-all duration-300"
                                 :style="`width:${selectedDeviceGroups.length ? (importProgress/selectedDeviceGroups.length*100) : 0}%`"></div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Search + Select-all bar --}}
            <div class="fetch-modal-search-wrap">
                <div class="flex gap-2 items-center">
                    <div class="relative flex-1">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        <input x-model="fetchSearch" type="text" placeholder="Search groups by name…"
                               class="form-input pl-9 py-2 text-sm w-full">
                    </div>
                    <button @click="toggleSelectAll()"
                        class="flex-shrink-0 text-xs font-semibold px-3 py-2 rounded-xl border transition-all"
                        :class="allNewSelected ? 'bg-green-50 border-green-300 text-green-700' : 'bg-slate-50 border-slate-200 text-slate-600 hover:border-slate-300'"
                        x-text="allNewSelected ? 'Deselect All' : 'Select All'">
                    </button>
                </div>
            </div>

            {{-- Group list --}}
            <div class="fetch-modal-list">
                <template x-if="filteredDeviceGroups.length === 0">
                    <div class="flex flex-col items-center justify-center py-10 gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                        </div>
                        <p class="text-sm text-slate-500 font-medium">No groups match your search</p>
                    </div>
                </template>

                <template x-for="dg in filteredDeviceGroups" :key="dg.id">
                    <div @click="!alreadyAddedIds.has(dg.id) && toggleSelect(dg.id)"
                         :class="{
                            'selected': selectedDeviceGroups.includes(dg.id),
                            'already-added': alreadyAddedIds.has(dg.id)
                         }"
                         class="fetch-group-card">

                        {{-- Check circle --}}
                        <div class="fetch-check" :class="selectedDeviceGroups.includes(dg.id) ? 'selected' : ''">
                            <template x-if="selectedDeviceGroups.includes(dg.id)">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="alreadyAddedIds.has(dg.id)">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                        </div>

                        {{-- Avatar --}}
                        <div class="fetch-group-avatar" x-text="dg.name.slice(0,2).toUpperCase()"></div>

                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-slate-800 truncate" x-text="dg.name"></div>
                            <div class="text-xs text-slate-400 font-mono truncate mt-0.5" x-text="dg.id"></div>
                        </div>

                        {{-- Already saved tag --}}
                        <template x-if="alreadyAddedIds.has(dg.id)">
                            <span class="flex-shrink-0 text-xs font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">Saved</span>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Footer --}}
            <div class="fetch-modal-foot">
                <template x-if="selectedDeviceGroups.length > 0">
                    <div class="fetch-sel-pill">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="selectedDeviceGroups.length + ' selected'"></span>
                    </div>
                </template>
                <button @click="showFetchModal=false" class="btn text-sm py-2 flex-shrink-0">Cancel</button>
                <button @click="importSelected()" class="fetch-import-btn" :disabled="selectedDeviceGroups.length === 0 || importing">
                    <template x-if="!importing">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v8"/></svg>
                    </template>
                    <template x-if="importing">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                    </template>
                    <span x-text="importing ? 'Importing…' : (selectedDeviceGroups.length ? 'Import ' + selectedDeviceGroups.length + ' Groups' : 'Select Groups')"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function waGroups() {
    return {
        groups: [],
        showForm: false,
        editId: null,
        saving: false,
        fetching: false,
        search: '',
        showFetchModal: false,
        fetchSearch: '',
        deviceGroups: [],
        selectedDeviceGroups: [],
        alreadyAddedIds: new Set(),
        importing: false,
        importProgress: 0,
        form: { name:'', wa_id:'', type:'group', description:'', is_active:true },

        get filteredGroups() {
            if (!this.search.trim()) return this.groups;
            const q = this.search.toLowerCase();
            return this.groups.filter(g =>
                g.name.toLowerCase().includes(q) || g.wa_id.toLowerCase().includes(q)
            );
        },

        get filteredDeviceGroups() {
            if (!this.fetchSearch.trim()) return this.deviceGroups;
            const q = this.fetchSearch.toLowerCase();
            return this.deviceGroups.filter(dg => dg.name.toLowerCase().includes(q));
        },

        get allNewSelected() {
            const newGroups = this.filteredDeviceGroups.filter(dg => !this.alreadyAddedIds.has(dg.id));
            return newGroups.length > 0 && newGroups.every(dg => this.selectedDeviceGroups.includes(dg.id));
        },

        async init() {
            await this.load();
        },

        async load() {
            const r = await RepairBox.ajax('/admin/whatsapp/groups', 'GET');
            this.groups = r.data || [];
        },

        openAdd() {
            this.editId = null;
            this.form = { name:'', wa_id:'', type:'group', description:'', is_active:true };
            this.showForm = true;
        },

        editGroup(g) {
            this.editId = g.id;
            this.form = { name:g.name, wa_id:g.wa_id, type:g.type, description:g.description||'', is_active:!!g.is_active };
            this.showForm = true;
        },

        async toggleActive(g) {
            g.is_active = !g.is_active;
            await RepairBox.ajax(`/admin/whatsapp/groups/${g.id}`, 'PUT', {
                name: g.name, wa_id: g.wa_id, type: g.type,
                description: g.description, is_active: g.is_active
            });
        },

        async saveGroup() {
            this.saving = true;
            const url    = this.editId ? `/admin/whatsapp/groups/${this.editId}` : '/admin/whatsapp/groups';
            const method = this.editId ? 'PUT' : 'POST';
            const r = await RepairBox.ajax(url, method, this.form);
            this.saving = false;
            if (r.success) {
                RepairBox.toast(this.editId ? 'Updated successfully' : 'Group added', 'success');
                this.showForm = false;
                await this.load();
            }
        },

        async deleteGroup(g) {
            if (!await RepairBox.confirm(`Delete "${g.name}"? This cannot be undone.`)) return;
            const r = await RepairBox.ajax(`/admin/whatsapp/groups/${g.id}`, 'DELETE');
            if (r.success) { RepairBox.toast('Deleted', 'success'); await this.load(); }
        },

        async fetchFromDevice() {
            this.fetching = true;
            const r = await RepairBox.ajax('/admin/whatsapp/fetch-groups', 'GET');
            this.fetching = false;
            if (r.success && r.data) {
                this.deviceGroups = r.data || [];
                // Build set of already-saved WA IDs
                this.alreadyAddedIds = new Set(this.groups.map(g => g.wa_id));
                this.selectedDeviceGroups = [];
                this.fetchSearch = '';
                this.importing = false;
                this.importProgress = 0;
                this.showFetchModal = true;
            } else {
                RepairBox.toast('WhatsApp device not connected or no groups found', 'error');
            }
        },

        toggleSelect(id) {
            const idx = this.selectedDeviceGroups.indexOf(id);
            if (idx === -1) this.selectedDeviceGroups.push(id);
            else this.selectedDeviceGroups.splice(idx, 1);
        },

        toggleSelectAll() {
            const newGroups = this.filteredDeviceGroups.filter(dg => !this.alreadyAddedIds.has(dg.id));
            if (this.allNewSelected) {
                // Deselect all visible new groups
                const ids = new Set(newGroups.map(g => g.id));
                this.selectedDeviceGroups = this.selectedDeviceGroups.filter(id => !ids.has(id));
            } else {
                // Select all visible new groups
                newGroups.forEach(dg => {
                    if (!this.selectedDeviceGroups.includes(dg.id)) this.selectedDeviceGroups.push(dg.id);
                });
            }
        },

        async importSelected() {
            const toImport = this.deviceGroups.filter(dg => this.selectedDeviceGroups.includes(dg.id));
            if (!toImport.length) return;
            this.importing = true;
            this.importProgress = 0;
            for (const dg of toImport) {
                await RepairBox.ajax('/admin/whatsapp/groups', 'POST', {
                    name: dg.name, wa_id: dg.id, type: 'group', is_active: true
                });
                this.importProgress++;
            }
            RepairBox.toast(`${toImport.length} group${toImport.length > 1 ? 's' : ''} imported`, 'success');
            this.importing = false;
            this.showFetchModal = false;
            await this.load();
        }
    };
}
</script>
@endsection
