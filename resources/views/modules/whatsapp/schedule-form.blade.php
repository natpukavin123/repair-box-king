@extends('layouts.app')
@section('title', $pageMode === 'edit' ? 'Edit WA Schedule' : 'New WA Schedule')
@section('page-title', $pageMode === 'edit' ? 'Edit WA Schedule' : 'New WA Schedule')
@section('content-class', '')

@section('content')
<style>
    .wa-compose {
        --wa-green: #16a34a;
        --wa-green-soft: #ecfdf3;
        --wa-blue-soft: #eff6ff;
        --wa-surface: rgba(255,255,255,0.92);
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }
    .wa-nav-tab {
        display: flex; gap: 0.25rem; padding: 0.25rem;
        background: rgba(148,163,184,0.1); border-radius: 0.9rem;
        border: 1px solid rgba(148,163,184,0.14); overflow-x: auto;
    }
    .wa-nav-tab a {
        flex: 1; min-width: 0; white-space: nowrap;
        text-align: center; padding: 0.5rem 0.7rem; border-radius: 0.7rem;
        font-size: 0.78rem; font-weight: 600; color: #64748b; text-decoration: none;
        transition: all 0.18s;
    }
    .wa-nav-tab a.active { background: #fff; color: var(--wa-green); box-shadow: 0 2px 10px rgba(22,163,74,0.12); }

    .compose-shell {
        display: grid; grid-template-columns: minmax(0, 1.55fr) minmax(320px, 0.9fr);
        gap: 1rem; align-items: start;
    }
    .compose-main,
    .compose-side,
    .compose-hero {
        border-radius: 1.4rem;
        border: 1px solid rgba(148,163,184,0.16);
        background: linear-gradient(180deg, rgba(255,255,255,0.97), rgba(248,250,252,0.92));
        box-shadow: 0 24px 60px -32px rgba(15,23,42,0.24);
    }
    .compose-hero {
        padding: 1rem 1.1rem;
        display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;
    }
    .compose-kicker {
        font-size: 0.72rem; font-weight: 800; letter-spacing: 0.08em;
        text-transform: uppercase; color: var(--wa-green);
    }
    .compose-title { font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1.15; }
    .compose-subtitle { font-size: 0.88rem; color: #64748b; margin-top: 0.2rem; }
    .compose-actions { display: flex; gap: 0.65rem; flex-wrap: wrap; }

    .compose-main { padding: 1rem; }
    .compose-side { padding: 1rem; position: static; }
    .compose-section {
        padding: 1rem; border-radius: 1.1rem; border: 1px solid rgba(226,232,240,0.95);
        background: rgba(248,250,252,0.76); margin-bottom: 0.85rem;
    }
    .compose-section:last-child { margin-bottom: 0; }
    .compose-section-title {
        display: flex; align-items: center; gap: 0.55rem;
        font-size: 0.76rem; font-weight: 800; letter-spacing: 0.06em;
        text-transform: uppercase; color: #64748b; margin-bottom: 0.75rem;
    }
    .compose-section-title svg { width: 0.95rem; height: 0.95rem; opacity: 0.65; }

    .compose-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.8rem; }
    .group-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.55rem; }
    .group-pill {
        display: flex; align-items: center; gap: 0.55rem; padding: 0.7rem 0.8rem;
        border-radius: 0.95rem; border: 1px solid rgba(226,232,240,0.95); background: #fff;
        cursor: pointer; transition: all 0.15s ease;
    }
    .group-pill:hover { border-color: rgba(34,197,94,0.28); background: #f8fffb; }
    .group-pill input { accent-color: #16a34a; }
    .group-pill-name { font-size: 0.84rem; font-weight: 600; color: #1e293b; }
    .group-pill-meta { font-size: 0.72rem; color: #94a3b8; }

    .type-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.65rem; }
    .type-card {
        border: 1px solid rgba(226,232,240,0.95); border-radius: 1rem; background: #fff;
        padding: 0.8rem 0.75rem; cursor: pointer; transition: all 0.16s ease;
    }
    .type-card.active {
        border-color: rgba(22,163,74,0.34); background: linear-gradient(180deg, #f0fdf4, #ffffff);
        box-shadow: 0 10px 24px -18px rgba(22,163,74,0.45);
    }
    .type-card-title { font-size: 0.82rem; font-weight: 700; color: #0f172a; }
    .type-card-text { font-size: 0.7rem; color: #64748b; margin-top: 0.25rem; line-height: 1.4; }

    .soft-note {
        display: flex; align-items: flex-start; gap: 0.55rem; padding: 0.8rem 0.9rem;
        border-radius: 0.95rem; background: var(--wa-green-soft); border: 1px solid rgba(34,197,94,0.18);
        color: #166534; font-size: 0.76rem;
    }
    .soft-note svg { width: 0.95rem; height: 0.95rem; flex-shrink: 0; margin-top: 0.05rem; }

    .quick-tools { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .mini-btn {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.48rem 0.75rem; border-radius: 0.8rem; border: 1px solid rgba(226,232,240,0.95);
        background: #fff; color: #475569; font-size: 0.74rem; font-weight: 700;
        cursor: pointer; transition: all 0.16s ease;
    }
    .mini-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 18px rgba(15,23,42,0.06); }
    .mini-btn.green { background: #f0fdf4; color: #15803d; border-color: rgba(34,197,94,0.22); }
    .mini-btn.blue { background: #eff6ff; color: #1d4ed8; border-color: rgba(59,130,246,0.22); }
    .mini-btn svg { width: 0.85rem; height: 0.85rem; }

    .row-grid { display: flex; flex-direction: column; gap: 0.55rem; }
    .row-header,
    .row-item {
        display: grid; grid-template-columns: repeat(var(--col-count, 2), minmax(0, 1fr)) 48px;
        gap: 0.55rem; align-items: center;
    }
    .row-header div {
        font-size: 0.68rem; color: #64748b; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        padding: 0 0.1rem;
    }
    .row-empty {
        padding: 1rem; border: 1px dashed rgba(148,163,184,0.4); border-radius: 0.9rem;
        text-align: center; color: #94a3b8; font-size: 0.8rem; background: rgba(255,255,255,0.55);
    }

    .wa-editor-wrap {
        border: 1.5px solid #e2e8f0; border-radius: 1rem; overflow: hidden; background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .wa-editor-wrap:focus-within { border-color: #25d366; box-shadow: 0 0 0 3px rgba(37,211,102,0.13); }
    .wa-editor-toolbar {
        display: flex; align-items: center; gap: 0.15rem; padding: 0.45rem 0.6rem; flex-wrap: wrap;
        border-bottom: 1px solid #f1f5f9; background: linear-gradient(180deg, #fafbfc, #f8fafc);
    }
    .wa-editor-toolbar .tb-sep { width: 1px; height: 1.3rem; background: #e2e8f0; margin: 0 0.25rem; flex-shrink: 0; }
    .wa-tb-btn {
        display: inline-flex; align-items: center; justify-content: center; width: 2rem; height: 2rem;
        border-radius: 0.55rem; border: none; background: transparent; cursor: pointer; color: #64748b;
        font-size: 0.82rem; transition: all 0.12s; flex-shrink: 0;
    }
    .wa-tb-btn:hover { background: #f1f5f9; color: #1e293b; }
    .wa-tb-btn.active { background: #dcfce7; color: #16a34a; }
    .wa-editor-textarea {
        width: 100%; min-height: 10rem; max-height: 24rem; padding: 0.85rem 0.95rem;
        border: none; outline: none; resize: vertical; background: transparent;
        font-size: 0.92rem; line-height: 1.65; color: #1e293b;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }
    .wa-editor-footer {
        display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;
        padding: 0.5rem 0.75rem; border-top: 1px solid #f1f5f9; background: linear-gradient(180deg, #f8fafc, #fafbfc);
    }
    .wa-var-chips { display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center; }
    .wa-var-chip {
        display: inline-flex; align-items: center; padding: 0.22rem 0.5rem; border-radius: 0.45rem;
        background: linear-gradient(135deg,#f0fdf4,#dcfce7); border: 1px solid rgba(22,163,74,0.18);
        color: #15803d; font-size: 0.72rem; font-weight: 700; font-family: 'SF Mono', SFMono-Regular, Consolas, monospace;
        cursor: pointer;
    }
    .char-count { font-size: 0.7rem; color: #94a3b8; }

    .emoji-picker-wrap { position: relative; display: inline-flex; }
    .emoji-picker-dropdown {
        position: absolute; top: calc(100% + 6px); left: 50%; transform: translateX(-50%);
        width: 290px; max-height: 270px; background: white; border-radius: 1rem; z-index: 30;
        box-shadow: 0 16px 48px rgba(15,23,42,0.2), 0 0 0 1px rgba(0,0,0,0.04);
        overflow: hidden; display: flex; flex-direction: column;
    }
    .emoji-picker-search { padding: 0.5rem 0.6rem; border-bottom: 1px solid #f1f5f9; }
    .emoji-picker-search input {
        width: 100%; padding: 0.38rem 0.6rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; font-size: 0.78rem; outline: none;
    }
    .emoji-picker-tabs { display: flex; gap: 0.15rem; padding: 0.3rem 0.5rem; border-bottom: 1px solid #f1f5f9; overflow-x: auto; }
    .emoji-picker-tabs button { padding: 0.2rem 0.35rem; font-size: 1rem; border-radius: 0.35rem; }
    .emoji-picker-tabs button.active { background: #f0fdf4; }
    .emoji-picker-grid {
        flex: 1; overflow-y: auto; padding: 0.4rem; display: grid; grid-template-columns: repeat(8, 1fr); gap: 0.15rem;
    }
    .emoji-picker-grid button {
        padding: 0.25rem; font-size: 1.2rem; border-radius: 0.35rem; display: flex; align-items: center; justify-content: center;
    }
    .emoji-picker-grid button:hover { background: #f0fdf4; transform: scale(1.12); }

    .phone-shell {
        border-radius: 1.4rem; padding: 0.8rem; background: linear-gradient(180deg, #dff8e8, #f8fafc);
        border: 1px solid rgba(34,197,94,0.15);
    }
    .phone-top {
        display: flex; align-items: center; gap: 0.55rem; padding: 0.6rem 0.75rem;
        border-radius: 1rem 1rem 0.6rem 0.6rem; background: #075e54; color: #fff;
    }
    .phone-avatar {
        width: 2rem; height: 2rem; border-radius: 999px; background: rgba(255,255,255,0.18);
        display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 800;
    }
    .phone-body {
        padding: 1rem; border-radius: 0.6rem 0.6rem 1rem 1rem; background: #efeae2;
        background-image: radial-gradient(rgba(148,163,184,0.18) 1px, transparent 1px);
        background-size: 18px 18px;
    }
    .wa-preview-bubble {
        position: relative; background: #dcf8c6; border-radius: 0.5rem 0.85rem 0.85rem 0.85rem;
        padding: 0.7rem 0.8rem 1.4rem; font-size: 0.88rem; line-height: 1.55; color: #111b21;
        max-width: 100%; word-break: break-word; box-shadow: 0 1px 1px rgba(0,0,0,0.06);
    }
    .wa-preview-bubble::before {
        content: ''; position: absolute; top: 0; left: -8px; width: 0; height: 0;
        border-bottom: 8px solid transparent; border-right: 8px solid #dcf8c6;
    }
    .wa-time { position: absolute; bottom: 0.3rem; right: 0.55rem; font-size: 0.66rem; color: #667781; }

    .summary-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.65rem; }
    .summary-card {
        padding: 0.8rem; border-radius: 1rem; border: 1px solid rgba(226,232,240,0.95); background: #fff;
    }
    .summary-label { font-size: 0.68rem; font-weight: 800; letter-spacing: 0.06em; text-transform: uppercase; color: #94a3b8; }
    .summary-value { font-size: 1rem; font-weight: 800; color: #0f172a; margin-top: 0.2rem; }
    .summary-text { font-size: 0.75rem; color: #64748b; margin-top: 0.2rem; }
    .guide-list { display: flex; flex-direction: column; gap: 0.65rem; }
    .guide-item {
        display: flex; align-items: flex-start; gap: 0.7rem; padding: 0.7rem 0.75rem;
        border-radius: 0.95rem; background: #fff; border: 1px solid rgba(226,232,240,0.95);
    }
    .guide-step {
        width: 1.55rem; height: 1.55rem; border-radius: 999px; background: var(--wa-blue-soft); color: #2563eb;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.72rem; font-weight: 800;
    }
    .guide-title { font-size: 0.8rem; font-weight: 700; color: #0f172a; }
    .guide-text { font-size: 0.75rem; color: #64748b; margin-top: 0.12rem; }

    .toggle-line {
        display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        padding: 0.8rem 0.9rem; border-radius: 1rem; background: #fff; border: 1px solid rgba(226,232,240,0.95);
    }

    @media (max-width: 1100px) {
        .compose-shell { grid-template-columns: minmax(0, 1fr); }
        .compose-side { position: static; }
    }
    @media (max-width: 767px) {
        .compose-hero, .compose-main, .compose-side { padding: 0.85rem; }
        .compose-grid, .group-grid, .type-grid, .summary-grid { grid-template-columns: minmax(0, 1fr); }
        .row-header, .row-item { grid-template-columns: minmax(0, 1fr); }
        .row-header div:last-child { display: none; }
    }
</style>

<div class="wa-compose" x-data="waScheduleComposer({
    mode: @js($pageMode),
    listUrl: '/admin/whatsapp/schedules',
    saveUrl: @js($pageMode === 'edit' ? '/admin/whatsapp/schedules/' . $scheduleForm['id'] : '/admin/whatsapp/schedules'),
    saveMethod: @js($pageMode === 'edit' ? 'PUT' : 'POST'),
    initialForm: @js($scheduleForm),
})" x-init="init()">

    <div class="wa-nav-tab">
        <a href="/admin/whatsapp">Dashboard</a>
        <a href="/admin/whatsapp/groups">Groups</a>
        <a href="/admin/whatsapp/schedules" class="active">Schedules</a>
        <a href="/admin/whatsapp/history">History</a>
    </div>

    <div class="compose-hero">
        <div>
            <div class="compose-kicker" x-text="mode === 'edit' ? 'Edit schedule page' : 'New schedule page'"></div>
            <div class="compose-title" x-text="mode === 'edit' ? 'Change your WhatsApp schedule' : 'Create a new WhatsApp schedule'"></div>
            <div class="compose-subtitle">Clear page layout with live preview, simple steps, and larger controls for faster use.</div>
        </div>
        <div class="compose-actions">
            <a href="/admin/whatsapp/schedules" class="btn" style="border-radius:0.9rem">Back to List</a>
            <button @click="saveSchedule()" class="btn btn-primary" style="border-radius:0.9rem" :disabled="saving">
                <span x-text="saving ? 'Saving...' : (mode === 'edit' ? 'Update Schedule' : 'Save Schedule')"></span>
            </button>
        </div>
    </div>

    <div class="compose-shell">
        <div class="compose-main">
            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Basic Details
                </div>
                <div class="compose-grid">
                    <div>
                        <label class="form-label">Schedule Name</label>
                        <input x-model="form.name" type="text" class="form-input" placeholder="Example: Monthly donor update">
                    </div>
                    <div>
                        <label class="form-label">Current Status</label>
                        <div class="toggle-line">
                            <div>
                                <div class="text-sm font-semibold text-slate-800" x-text="form.is_active ? 'Active schedule' : 'Paused schedule'"></div>
                                <div class="text-xs text-slate-500">Active schedules are picked by the scheduler automatically.</div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input x-model="form.is_active" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:w-5 after:h-5 after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Choose Groups
                </div>
                <div class="soft-note" style="margin-bottom:0.75rem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>Select one or more groups. Your message will go to all selected groups.</div>
                </div>
                <div class="group-grid">
                    @forelse($groups as $group)
                    <label class="group-pill">
                        <input type="checkbox" value="{{ $group->id }}" x-model="form.group_ids">
                        <div>
                            <div class="group-pill-name">{{ $group->name }}</div>
                            <div class="group-pill-meta">Group ID: {{ $group->wa_id }}</div>
                        </div>
                    </label>
                    @empty
                    <div class="row-empty" style="grid-column:1/-1">No active groups found. Please add groups first.</div>
                    @endforelse
                </div>
            </div>

            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    Write Message
                </div>

                <div class="soft-note" style="margin-bottom:0.75rem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Use words inside braces like <strong>{name}</strong> or <strong>{amount}</strong>. These will change for each data row.
                </div>

                <div class="wa-editor-wrap">
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

                        <button type="button" class="wa-tb-btn" @click="editorNewline()" title="New line">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 10 4 15 9 20"/><path d="M20 4v7a4 4 0 01-4 4H4"/></svg>
                        </button>

                        <div class="emoji-picker-wrap" @keydown.escape.prevent="emojiOpen = false">
                            <button type="button" class="wa-tb-btn" @click="emojiOpen = !emojiOpen" :class="emojiOpen ? 'active' : ''" title="Emoji">
                                <span style="font-size:1.05rem">:)</span>
                            </button>
                            <div x-show="emojiOpen" x-cloak @click.outside="emojiOpen = false" class="emoji-picker-dropdown">
                                <div class="emoji-picker-search">
                                    <input type="text" x-model="emojiSearch" placeholder="Search emoji">
                                </div>
                                <div class="emoji-picker-tabs">
                                    <template x-for="(cat, ci) in emojiCategories" :key="ci">
                                        <button type="button" @click="emojiTab = ci; emojiSearch = ''" :class="emojiTab === ci ? 'active' : ''" x-text="cat.icon"></button>
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

                        <button type="button" class="wa-tb-btn" @click="showPreview = !showPreview" :class="showPreview ? 'active' : ''" title="Preview">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>

                    <textarea x-ref="tplTextarea" x-model="form.message_template" class="wa-editor-textarea" rows="7" placeholder="Write the message here. Example: Dear *{name}*, thank you for donating {amount}."></textarea>

                    <div class="wa-editor-footer">
                        <div class="wa-var-chips">
                            <span class="text-xs text-slate-400 mr-1">Insert:</span>
                            <template x-for="col in columns" :key="col">
                                <button type="button" class="wa-var-chip" @click="editorInsertVar(col)" x-text="'{' + col + '}'"></button>
                            </template>
                        </div>
                        <div class="char-count" x-text="(form.message_template || '').length + ' chars'"></div>
                    </div>
                </div>
            </div>

            <div class="compose-section" :style="'--col-count:' + Math.max(columns.length, 1)">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                    Data Rows
                </div>

                <div class="soft-note" style="margin-bottom:0.75rem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>First row in your Excel file should contain column names. Remaining rows will be used to send personalized messages.</div>
                </div>

                <div class="quick-tools" style="margin-bottom:0.75rem">
                    <label class="mini-btn green">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Import Excel
                        <input type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="importExcelRows($event)">
                    </label>
                    <button type="button" @click="downloadSampleExcel()" class="mini-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download Sample
                    </button>
                    <button type="button" @click="detectColumns()" class="mini-btn blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                        Detect Columns
                    </button>
                    <button type="button" @click="addRow()" class="mini-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Row
                    </button>
                </div>

                <div class="row-grid">
                    <template x-if="columns.length">
                        <div class="row-header">
                            <template x-for="col in columns" :key="col">
                                <div x-text="col"></div>
                            </template>
                            <div>Remove</div>
                        </div>
                    </template>

                    <template x-if="!form.data_rows.length">
                        <div class="row-empty">No data rows yet. Add one row or import from Excel.</div>
                    </template>

                    <template x-for="(row, ri) in form.data_rows" :key="ri">
                        <div class="row-item">
                            <template x-for="col in columns" :key="col">
                                <input :placeholder="col" :value="row[col] || ''" @input="row[col] = $event.target.value" type="text" class="form-input">
                            </template>
                            <button type="button" @click="removeRow(ri)" class="btn" style="padding:0.55rem 0.45rem;border-radius:0.8rem;color:#dc2626">X</button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Time Settings
                </div>

                <div class="type-grid" style="margin-bottom:0.85rem">
                    <template x-for="type in scheduleTypes" :key="type.value">
                        <button type="button" class="type-card" :class="form.schedule_type === type.value ? 'active' : ''" @click="form.schedule_type = type.value">
                            <div class="type-card-title" x-text="type.label"></div>
                            <div class="type-card-text" x-text="type.help"></div>
                        </button>
                    </template>
                </div>

                <div x-show="form.schedule_type === 'once'">
                    <label class="form-label">Send on date and time</label>
                    <input x-model="form.scheduled_at" type="datetime-local" class="form-input">
                </div>

                <div x-show="form.schedule_type === 'daily'">
                    <label class="form-label">Send every day at</label>
                    <input x-model="form.schedule_time" type="time" class="form-input">
                </div>

                <div x-show="form.schedule_type === 'weekly'" class="compose-grid">
                    <div>
                        <label class="form-label">Day</label>
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
                    <div>
                        <label class="form-label">Time</label>
                        <input x-model="form.schedule_time" type="time" class="form-input">
                    </div>
                </div>

                <div x-show="form.schedule_type === 'cron'">
                    <label class="form-label">Cron Expression</label>
                    <input x-model="form.cron_expression" type="text" class="form-input font-mono" placeholder="0 8 * * *">
                    <div class="text-xs text-slate-500 mt-2">Example: 0 8 * * * means every day at 8:00 AM.</div>
                </div>
            </div>
        </div>

        <div class="compose-side">
            <div class="compose-section" style="background:transparent;border:none;padding:0;margin-bottom:0.85rem">
                <div class="compose-section-title" style="padding:0 0.1rem">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Live Preview
                </div>
                <div class="phone-shell">
                    <div class="phone-top">
                        <div class="phone-avatar">WA</div>
                        <div>
                            <div class="text-sm font-semibold">WhatsApp Message</div>
                            <div class="text-xs opacity-80">Preview using first data row</div>
                        </div>
                    </div>
                    <div class="phone-body">
                        <div class="wa-preview-bubble" x-html="renderWaPreview()"></div>
                    </div>
                </div>
                <template x-if="showPreview && previewMessage()">
                    <div class="soft-note" style="margin-top:0.75rem">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div x-text="previewMessage()"></div>
                    </div>
                </template>
            </div>

            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4V9m3 10H6a2 2 0 01-2-2V7a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2z"/></svg>
                    Quick Summary
                </div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <div class="summary-label">Groups</div>
                        <div class="summary-value" x-text="form.group_ids.length"></div>
                        <div class="summary-text">Selected groups</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Rows</div>
                        <div class="summary-value" x-text="form.data_rows.length"></div>
                        <div class="summary-text">Personalized records</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Type</div>
                        <div class="summary-value" x-text="currentTypeLabel()"></div>
                        <div class="summary-text">Schedule method</div>
                    </div>
                    <div class="summary-card">
                        <div class="summary-label">Status</div>
                        <div class="summary-value" x-text="form.is_active ? 'Active' : 'Paused'"></div>
                        <div class="summary-text">Run status</div>
                    </div>
                </div>
            </div>

            <div class="compose-section">
                <div class="compose-section-title">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Easy Steps
                </div>
                <div class="guide-list">
                    <div class="guide-item">
                        <div class="guide-step">1</div>
                        <div>
                            <div class="guide-title">Choose groups</div>
                            <div class="guide-text">Pick the WhatsApp groups that should receive this message.</div>
                        </div>
                    </div>
                    <div class="guide-item">
                        <div class="guide-step">2</div>
                        <div>
                            <div class="guide-title">Write your message</div>
                            <div class="guide-text">Use fields like {name} and {amount} for personalized messages.</div>
                        </div>
                    </div>
                    <div class="guide-item">
                        <div class="guide-step">3</div>
                        <div>
                            <div class="guide-title">Add data rows</div>
                            <div class="guide-text">Type data manually or import it from Excel.</div>
                        </div>
                    </div>
                    <div class="guide-item">
                        <div class="guide-step">4</div>
                        <div>
                            <div class="guide-title">Pick time and save</div>
                            <div class="guide-text">Choose once, daily, weekly, or cron, then save the schedule.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>
<script>
function waScheduleComposer(config) {
    return {
        mode: config.mode,
        saving: false,
        emojiOpen: false,
        emojiSearch: '',
        emojiTab: 0,
        showPreview: true,
        scheduleTypes: [
            { value: 'once', label: 'Once', help: 'Send one time on a fixed date.' },
            { value: 'daily', label: 'Daily', help: 'Send every day at one time.' },
            { value: 'weekly', label: 'Weekly', help: 'Send once every week.' },
            { value: 'cron', label: 'Custom', help: 'Use a cron rule for advanced timing.' },
        ],
        emojiCategories: [
            { icon: '😀', emojis: ['😀','😃','😄','😁','😆','🙂','😊','😇','🥰','😍','😘','😎','🤝','🙏'] },
            { icon: '📢', emojis: ['📢','📣','📌','📍','✅','❌','❗','❓','⭐','✨','🔥','🎉'] },
            { icon: '💰', emojis: ['💰','💵','💸','🧾','📈','📉','🏦','💳','🏷️','🎯'] },
        ],
        columns: ['name', 'amount'],
        form: {
            name: '', group_ids: [], message_template: '', data_rows: [], schedule_type: 'once',
            cron_expression: '', scheduled_at: '', schedule_time: '08:00', schedule_day: 1, is_active: true,
        },

        init() {
            this.form = {
                ...config.initialForm,
                group_ids: (config.initialForm.group_ids || []).map(Number),
                data_rows: (config.initialForm.data_rows && config.initialForm.data_rows.length)
                    ? config.initialForm.data_rows.map(row => ({ ...row }))
                    : [{ name: '', amount: '' }],
            };
            this.detectColumnsFromTemplate();
        },

        get filteredEmojis() {
            const cat = this.emojiCategories[this.emojiTab];
            if (!cat) return [];
            if (!this.emojiSearch) return cat.emojis;
            return cat.emojis.filter(em => em.includes(this.emojiSearch));
        },

        _ta() { return this.$refs.tplTextarea; },

        editorWrap(marker) {
            const ta = this._ta(); if (!ta) return;
            const s = ta.selectionStart, e = ta.selectionEnd, t = ta.value;
            const sel = t.substring(s, e);
            const wrapped = sel ? marker + sel + marker : marker + 'text' + marker;
            this.form.message_template = t.substring(0, s) + wrapped + t.substring(e);
            this.$nextTick(() => {
                ta.focus();
                if (sel) ta.setSelectionRange(s, s + wrapped.length);
                else ta.setSelectionRange(s + marker.length, s + marker.length + 4);
            });
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

        detectColumns() {
            this.detectColumnsFromTemplate();
            RepairBox.toast('Columns updated from message text', 'success');
        },

        detectColumnsFromTemplate() {
            const matches = this.form.message_template.matchAll(/\{(\w+)\}/g);
            const cols = [...new Set([...matches].map(m => m[1]))];
            if (!cols.length) {
                this.columns = ['name', 'amount'];
                return;
            }
            this.columns = cols;
            this.form.data_rows = this.form.data_rows.map(row => {
                const normalized = { ...row };
                cols.forEach(col => {
                    if (!(col in normalized)) normalized[col] = '';
                });
                return normalized;
            });
        },

        addRow() {
            const row = {};
            this.columns.forEach(col => { row[col] = ''; });
            this.form.data_rows.push(row);
        },

        removeRow(index) {
            this.form.data_rows.splice(index, 1);
        },

        _parseExcelFile(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    try {
                        const data = new Uint8Array(e.target.result);
                        const wb = XLSX.read(data, { type: 'array' });
                        const ws = wb.Sheets[wb.SheetNames[0]];
                        resolve(XLSX.utils.sheet_to_json(ws, { defval: '' }));
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
                    RepairBox.toast('No rows found in the file', 'error');
                    return;
                }
                const originalKeys = Object.keys(rows[0]);
                const cols = originalKeys.map(key => key.trim().toLowerCase().replace(/\s+/g, '_'));
                this.columns = cols;
                this.form.data_rows = rows.map(item => {
                    const row = {};
                    originalKeys.forEach((key, index) => {
                        row[cols[index]] = String(item[key] ?? '');
                    });
                    return row;
                });
                if (!(this.form.message_template.match(/\{(\w+)\}/g) || []).length) {
                    this.form.message_template = cols.map(col => '{' + col + '}').join(' ');
                }
                RepairBox.toast('Excel file imported successfully', 'success');
            } catch (err) {
                RepairBox.toast('Could not read the Excel file', 'error');
            }
        },

        _generateSampleXlsx(cols, filename) {
            const sampleRow = {};
            cols.forEach(col => { sampleRow[col] = 'Sample ' + col; });
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

        previewMessage() {
            if (!this.form.data_rows.length) return '';
            let msg = this.form.message_template || '';
            const row = this.form.data_rows[0] || {};
            Object.entries(row).forEach(([key, value]) => {
                msg = msg.replace(new RegExp('\\{' + key + '\\}', 'gi'), value || '{' + key + '}');
            });
            return msg;
        },

        renderWaPreview() {
            let msg = this.form.message_template || '';
            if (this.form.data_rows.length > 0) {
                const row = this.form.data_rows[0];
                Object.entries(row).forEach(([key, value]) => {
                    const finalValue = value || '{' + key + '}';
                    msg = msg.replace(new RegExp('\\{' + key + '\\}', 'gi'), finalValue);
                });
            }
            msg = msg.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            msg = msg.replace(/\*([^*\n]+)\*/g, '<strong>$1</strong>');
            msg = msg.replace(/_([^_\n]+)_/g, '<em>$1</em>');
            msg = msg.replace(/~([^~\n]+)~/g, '<del>$1</del>');
            msg = msg.replace(/```([^`]+)```/g, '<code style="background:#f1f5f9;padding:0.1em 0.3em;border-radius:3px;font-family:monospace;font-size:0.85em">$1</code>');
            msg = msg.replace(/\{(\w+)\}/g, '<span style="background:#fff3cd;padding:0.1em 0.3em;border-radius:3px;color:#856404;font-weight:600;font-size:0.85em">{$1}</span>');
            msg = msg.replace(/\n/g, '<br>');
            const now = new Date();
            const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
            return msg + '<span class="wa-time">' + time + ' ✓✓</span>';
        },

        currentTypeLabel() {
            const current = this.scheduleTypes.find(type => type.value === this.form.schedule_type);
            return current ? current.label : '-';
        },

        async saveSchedule() {
            this.saving = true;
            const response = await RepairBox.ajax(config.saveUrl, config.saveMethod, this.form);
            this.saving = false;
            if (response.success) {
                RepairBox.toast(this.mode === 'edit' ? 'Schedule updated' : 'Schedule created', 'success');
                window.location.href = config.listUrl;
            }
        },
    };
}
</script>
@endsection
