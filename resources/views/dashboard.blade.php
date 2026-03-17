@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    .dashboard-wrap { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .dashboard-wrap * { font-family: inherit; }

    .nav-card {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px;
        padding: 28px 16px; border-radius: 20px; text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        border: 1px solid rgba(255,255,255,0.2);
        position: relative; overflow: hidden;
    }
    .nav-card::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.15), transparent);
        pointer-events: none;
    }
    .nav-card:hover { transform: translateY(-5px); box-shadow: 0 12px 30px -5px rgba(0,0,0,0.2); }
    .nav-card-icon {
        width: 56px; height: 56px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.25);
        backdrop-filter: blur(4px);
        transition: transform 0.3s ease;
    }
    .nav-card:hover .nav-card-icon { transform: scale(1.1); }
    .nav-card-label { font-size: 15px; font-weight: 700; color: #fff; text-align: center; letter-spacing: 0.01em; line-height: 1.3; }
    .nav-card-sub { font-size: 11px; font-weight: 500; color: rgba(255,255,255,0.8); text-align: center; }

    .stat-card-new {
        background: #fff; border-radius: 16px; padding: 20px; border: 1px solid #f3f4f6;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden;
    }
    .stat-card-new::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: 16px 16px 0 0; opacity: 0; transition: opacity 0.3s ease;
    }
    .stat-card-new:hover { transform: translateY(-2px); box-shadow: 0 8px 25px -5px rgba(0,0,0,0.08); }
    .stat-card-new:hover::before { opacity: 1; }

    .pipeline-card {
        border-radius: 14px; padding: 18px; border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); text-decoration: none; display: block;
    }
    .pipeline-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -5px rgba(0,0,0,0.1); }
    .pipeline-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }

    .chart-card {
        background: #fff; border-radius: 20px; border: 1px solid #f3f4f6;
        overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .chart-header {
        padding: 20px 24px; border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between;
    }

    .reminder-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 12px;
        border-radius: 10px; transition: background 0.2s ease;
    }
    .reminder-item:hover { background: #f9fafb; }
    .reminder-check {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid #d1d5db;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
    }
    .reminder-check.done { background: #10b981; border-color: #10b981; }
    .reminder-check:hover { border-color: #6366f1; }

    .section-title { font-size: 15px; font-weight: 700; color: #1f2937; letter-spacing: -0.01em; }
    .section-badge { font-size: 11px; font-weight: 500; color: #6b7280; background: #f3f4f6; padding: 3px 10px; border-radius: 20px; }

    .card-wrap { background: #fff; border-radius: 20px; border: 1px solid #f3f4f6; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
    .card-head { padding: 18px 24px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; }

    .cat-summary {
        text-align: center; padding: 14px; border-radius: 12px; border: 1px solid transparent;
    }

    .low-stock-badge {
        position: absolute; top: -4px; right: -4px; width: 20px; height: 20px;
        background: #ef4444; color: #fff; font-size: 10px; font-weight: 700;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.15); }
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 9999px; }
</style>

<div class="dashboard-wrap" x-data="dashboardPage()">

    {{-- ============ ROW 1: Main Nav Cards + Reminders ============ --}}
    {{-- Navigation Cards --}}
    <div style="margin-bottom:28px;">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            {{-- Sales / POS --}}
            <a href="/pos" class="nav-card" style="background:linear-gradient(135deg,#22c55e,#15803d);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <span class="nav-card-label">Sales</span>
                <span class="nav-card-sub">POS Billing</span>
            </a>
            {{-- Repairs --}}
            <a href="/repairs" class="nav-card" style="background:linear-gradient(135deg,#f97316,#c2410c);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="nav-card-label">Repairs</span>
                <span class="nav-card-sub">Service Jobs</span>
            </a>
            {{-- Recharge --}}
            <a href="/recharges" class="nav-card" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <span class="nav-card-label">Recharge</span>
                <span class="nav-card-sub">Mobile Plans</span>
            </a>
            {{-- Expenses --}}
            <a href="/expenses" class="nav-card" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <span class="nav-card-label">Expenses</span>
                <span class="nav-card-sub">Track Costs</span>
            </a>
            {{-- Invoices --}}
            <a href="/invoices" class="nav-card" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <span class="nav-card-label">Invoices</span>
                <span class="nav-card-sub">Bills & Receipts</span>
            </a>
            {{-- Returns --}}
            <a href="/returns" class="nav-card" style="background:linear-gradient(135deg,#f59e0b,#b45309);">
                <div class="nav-card-icon">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                </div>
                <span class="nav-card-label">Returns</span>
                <span class="nav-card-sub">Refunds</span>
            </a>
        </div>
    </div>

    {{-- Reminders --}}
    <div class="mb-8">
        <div class="card-wrap">
            <div class="card-head">
                <div class="flex items-center gap-2.5">
                    <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <span class="section-title">Reminders</span>
                </div>
                <button @click="showReminderForm = !showReminderForm"
                    style="display:inline-flex;align-items:center;gap:6px;padding:7px 16px;background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;font-size:12px;font-weight:600;border-radius:10px;border:none;cursor:pointer;box-shadow:0 2px 8px rgba(99,102,241,0.3);transition:all 0.2s ease;">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Add Reminder
                </button>
            </div>
            <div style="padding:16px 20px;">
                {{-- Add Reminder Form --}}
                <div x-show="showReminderForm" x-transition style="margin-bottom:16px;padding:16px;background:#f8fafc;border-radius:14px;border:1px solid #e2e8f0;">
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <input x-model="newReminder.title" type="text" placeholder="What do you need to remember?"
                            style="width:100%;padding:10px 14px;font-size:13px;border:1px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:#fff;">
                        <div style="display:flex;gap:8px;">
                            <input x-model="newReminder.due_date" type="date"
                                style="flex:1;padding:9px 12px;font-size:13px;border:1px solid #e2e8f0;border-radius:10px;outline:none;font-family:inherit;background:#fff;">
                            <button @click="addReminder()"
                                style="padding:9px 20px;background:#4f46e5;color:#fff;font-size:12px;font-weight:600;border-radius:10px;border:none;cursor:pointer;">Save</button>
                            <button @click="showReminderForm = false"
                                style="padding:9px 14px;color:#6b7280;font-size:12px;border-radius:10px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;">Cancel</button>
                        </div>
                    </div>
                </div>

                {{-- Reminder list --}}
                <div class="custom-scrollbar" style="max-height:200px;overflow-y:auto;">
                    <template x-for="rem in stats.reminders" :key="rem.id">
                        <div class="reminder-item" :style="rem.is_completed ? 'opacity:0.5' : ''">
                            <div class="reminder-check" :class="rem.is_completed ? 'done' : ''" @click="toggleReminder(rem.id)">
                                <svg x-show="rem.is_completed" style="width:12px;height:12px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <p style="font-size:13px;font-weight:500;color:#374151;margin:0;" :style="rem.is_completed ? 'text-decoration:line-through;color:#9ca3af' : ''" x-text="rem.title"></p>
                                <p x-show="rem.due_date" style="font-size:11px;color:#9ca3af;margin:2px 0 0;" x-text="rem.due_date ? new Date(rem.due_date).toLocaleDateString('en-IN', {day:'numeric',month:'short',year:'numeric'}) : ''"></p>
                            </div>
                            <button @click="deleteReminder(rem.id)" style="opacity:0;transition:opacity 0.2s;background:none;border:none;cursor:pointer;color:#ef4444;padding:4px;" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=0">
                                <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="!stats.reminders || stats.reminders.length === 0" style="text-align:center;padding:30px 10px;">
                        <svg style="width:40px;height:40px;color:#e5e7eb;margin:0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <p style="font-size:12px;color:#9ca3af;">No reminders yet. Click "Add Reminder" to get started.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ ROW 2: Sales Progression / Today's Sales ============ --}}
    <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <span class="section-title">Sales Progression</span>
            <span class="section-badge">Today</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            {{-- New Customers --}}
            <div class="stat-card-new" style="border-left:3px solid #22c55e;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#22c55e,#16a34a);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                </div>
                <p style="font-size:26px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="stats.new_customers_today">0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">New Customers</p>
            </div>
            {{-- Repeat Customers --}}
            <div class="stat-card-new" style="border-left:3px solid #3b82f6;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <p style="font-size:26px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="stats.repeat_customers_today">0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">Repeat Customers</p>
            </div>
            {{-- Gross Sale Payments --}}
            <div class="stat-card-new" style="border-left:3px solid #10b981;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#10b981,#059669);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p style="font-size:24px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="formatCurrency(stats.gross_sale_payments)">₹0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">Gross Sales</p>
            </div>
            {{-- Customer Purchase Amount --}}
            <div class="stat-card-new" style="border-left:3px solid #8b5cf6;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                    </div>
                </div>
                <p style="font-size:24px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="formatCurrency(stats.customer_purchase_amount)">₹0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">Cust. Purchase</p>
            </div>
            {{-- Purchase Order Amount --}}
            <div class="stat-card-new" style="border-left:3px solid #f59e0b;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                </div>
                <p style="font-size:24px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="formatCurrency(stats.purchase_order_amount)">₹0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">Purchase Orders</p>
            </div>
            {{-- Refund Amount --}}
            <div class="stat-card-new" style="border-left:3px solid #ef4444;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <div style="width:40px;height:40px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/></svg>
                    </div>
                </div>
                <p style="font-size:24px;font-weight:800;color:#111827;margin:0;letter-spacing:-0.02em;" x-text="formatCurrency(stats.refund_amount)">₹0</p>
                <p style="font-size:10px;font-weight:600;color:#6b7280;margin:6px 0 0;text-transform:uppercase;letter-spacing:0.08em;">Refunds</p>
            </div>
        </div>
    </div>

    {{-- ============ ROW 3: Repair Tickets Activity ============ --}}
    <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <span class="section-title">Repair Tickets Activity</span>
            <a href="/repairs" style="margin-left:auto;font-size:12px;font-weight:600;color:#4f46e5;text-decoration:none;">View All →</a>
        </div>
        <div class="card-wrap" style="padding:24px;">
            <div style="display:flex;flex-wrap:wrap;align-items:center;gap:12px;">
                {{-- Received --}}
                <a href="/repairs?status=received" class="pipeline-card" style="flex:1;min-width:130px;background:#eff6ff;border-color:#bfdbfe;text-decoration:none;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="pipeline-icon" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <div>
                            <p style="font-size:24px;font-weight:800;color:#1e40af;margin:0;" x-text="stats.repair_counts.received">0</p>
                            <p style="font-size:10px;font-weight:700;color:#3b82f6;text-transform:uppercase;letter-spacing:0.08em;margin:2px 0 0;">Received</p>
                        </div>
                    </div>
                </a>
                {{-- Arrow --}}
                <div style="display:none;color:#d1d5db;" class="lg:!block">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                {{-- In Progress --}}
                <a href="/repairs?status=in_progress" class="pipeline-card" style="flex:1;min-width:130px;background:#fffbeb;border-color:#fde68a;text-decoration:none;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="pipeline-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:24px;font-weight:800;color:#92400e;margin:0;" x-text="stats.repair_counts.in_progress">0</p>
                            <p style="font-size:10px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:0.08em;margin:2px 0 0;">In Progress</p>
                        </div>
                    </div>
                </a>
                <div style="display:none;color:#d1d5db;" class="lg:!block">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                {{-- Completed --}}
                <a href="/repairs?status=completed" class="pipeline-card" style="flex:1;min-width:130px;background:#ecfdf5;border-color:#a7f3d0;text-decoration:none;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="pipeline-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:24px;font-weight:800;color:#065f46;margin:0;" x-text="stats.repair_counts.completed">0</p>
                            <p style="font-size:10px;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.08em;margin:2px 0 0;">Completed</p>
                        </div>
                    </div>
                </a>
                <div style="display:none;color:#d1d5db;" class="lg:!block">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                {{-- Payment --}}
                <a href="/repairs?status=payment" class="pipeline-card" style="flex:1;min-width:130px;background:#f5f3ff;border-color:#c4b5fd;text-decoration:none;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="pipeline-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:24px;font-weight:800;color:#4c1d95;margin:0;" x-text="stats.repair_counts.payment">0</p>
                            <p style="font-size:10px;font-weight:700;color:#8b5cf6;text-transform:uppercase;letter-spacing:0.08em;margin:2px 0 0;">Payment</p>
                        </div>
                    </div>
                </a>
                <div style="display:none;color:#d1d5db;" class="lg:!block">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </div>
                {{-- Closed --}}
                <a href="/repairs?status=closed" class="pipeline-card" style="flex:1;min-width:130px;background:#f8fafc;border-color:#cbd5e1;text-decoration:none;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div class="pipeline-icon" style="background:linear-gradient(135deg,#64748b,#475569);">
                            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <p style="font-size:24px;font-weight:800;color:#334155;margin:0;" x-text="stats.repair_counts.closed">0</p>
                            <p style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.08em;margin:2px 0 0;">Closed</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- ============ ROW 4: Charts ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- LEFT: Gross Sales by Day --}}
        <div class="chart-card">
            <div class="chart-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <span class="section-title">Gross Sales by Day</span>
                </div>
                <span class="section-badge">Last 7 days</span>
            </div>
            <div style="padding:20px 24px;">
                <div style="position:relative;height:260px;width:100%;">
                    <canvas id="salesByDayChart"></canvas>
                </div>
            </div>
        </div>

        {{-- RIGHT: Gross Sales by Category --}}
        <div class="chart-card">
            <div class="chart-header">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);display:flex;align-items:center;justify-content:center;">
                        <svg style="width:16px;height:16px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <span class="section-title">Gross Sales by Category</span>
                </div>
                <span class="section-badge">Today</span>
            </div>
            <div style="padding:20px 24px;display:flex;align-items:center;justify-content:center;">
                <canvas id="salesByCategoryChart" width="240" height="240" style="display:block;"></canvas>
            </div>
            {{-- Category summary cards --}}
            <div style="padding:0 24px 24px;">
                <div class="grid grid-cols-3 gap-3">
                    <div class="cat-summary" style="background:#eff6ff;border-color:#bfdbfe;">
                        <p style="font-size:16px;font-weight:800;color:#1d4ed8;margin:0;" x-text="formatCurrency(stats.sales_by_category.pos_sales)">₹0</p>
                        <p style="font-size:9px;font-weight:700;color:#3b82f6;text-transform:uppercase;letter-spacing:0.08em;margin:4px 0 0;">POS Sales</p>
                    </div>
                    <div class="cat-summary" style="background:#fff7ed;border-color:#fed7aa;">
                        <p style="font-size:16px;font-weight:800;color:#c2410c;margin:0;" x-text="formatCurrency(stats.sales_by_category.repair_sales)">₹0</p>
                        <p style="font-size:9px;font-weight:700;color:#f97316;text-transform:uppercase;letter-spacing:0.08em;margin:4px 0 0;">Repair Sales</p>
                    </div>
                    <div class="cat-summary" style="background:#ecfdf5;border-color:#a7f3d0;">
                        <p style="font-size:16px;font-weight:800;color:#065f46;margin:0;" x-text="formatCurrency(stats.sales_by_category.total_sales)">₹0</p>
                        <p style="font-size:9px;font-weight:700;color:#10b981;text-transform:uppercase;letter-spacing:0.08em;margin:4px 0 0;">Total Sales</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function dashboardPage() {
    return {
        showReminderForm: false,
        newReminder: { title: '', description: '', due_date: '' },
        stats: {
            today_sales: 0, monthly_sales: 0, today_repairs: 0, pending_repairs: 0,
            monthly_expenses: 0, monthly_purchases: 0, today_recharges: 0,
            monthly_revenue: 0, monthly_outflow: 0,
            new_customers_today: 0, repeat_customers_today: 0,
            gross_sale_payments: 0, customer_purchase_amount: 0,
            purchase_order_amount: 0, refund_amount: 0,
            repair_counts: { received: 0, in_progress: 0, completed: 0, payment: 0, closed: 0 },
            sales_by_category: { pos_sales: 0, repair_sales: 0, total_sales: 0 },
            sales_chart: { labels: [], data: [], repair_data: [] },
            low_stock_count: 0,
            reminders: [],
        },
        salesChart: null,
        categoryChart: null,

        async init() {
            try {
                const res = await fetch('/dashboard', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (!data || data.success === false) return;

                this.stats = {
                    ...this.stats,
                    today_sales:              data.today_sales ?? 0,
                    monthly_sales:            data.monthly_sales ?? 0,
                    today_repairs:            data.today_repairs ?? 0,
                    pending_repairs:          data.pending_repairs ?? 0,
                    monthly_expenses:         data.monthly_expenses ?? 0,
                    monthly_purchases:        data.monthly_purchases ?? 0,
                    today_recharges:          data.today_recharges ?? 0,
                    monthly_revenue:          data.monthly_revenue ?? 0,
                    monthly_outflow:          data.monthly_outflow ?? 0,
                    new_customers_today:      data.new_customers_today ?? 0,
                    repeat_customers_today:   data.repeat_customers_today ?? 0,
                    gross_sale_payments:      data.gross_sale_payments ?? 0,
                    customer_purchase_amount: data.customer_purchase_amount ?? 0,
                    purchase_order_amount:    data.purchase_order_amount ?? 0,
                    refund_amount:            data.refund_amount ?? 0,
                    repair_counts:            data.repair_counts ?? this.stats.repair_counts,
                    sales_by_category:        data.sales_by_category ?? this.stats.sales_by_category,
                    sales_chart:              data.sales_chart ?? this.stats.sales_chart,
                    low_stock_count:          data.low_stock_count ?? 0,
                    reminders:                data.reminders ?? [],
                };

                // Wait for Alpine to finish DOM update, then render charts
                this.$nextTick(() => {
                    requestAnimationFrame(() => {
                        this.renderSalesByDayChart();
                        this.renderCategoryChart();
                    });
                });
            } catch (e) {
                console.error('Dashboard init error:', e);
            }
        },

        renderSalesByDayChart() {
            const canvas = document.getElementById('salesByDayChart');
            if (!canvas) { console.warn('salesByDayChart canvas not found'); return; }
            if (this.salesChart) { this.salesChart.destroy(); this.salesChart = null; }

            this.salesChart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.stats.sales_chart.labels,
                    datasets: [
                        {
                            label: 'POS Sales',
                            data: this.stats.sales_chart.data,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59,130,246,0.12)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        },
                        {
                            label: 'Repair Sales',
                            data: this.stats.sales_chart.repair_data || [],
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249,115,22,0.10)',
                            borderWidth: 2.5,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#f97316',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: {
                            display: true, position: 'top',
                            labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12, family: 'Inter', weight: '500' } }
                        },
                        tooltip: {
                            backgroundColor: '#1e293b', titleFont: { size: 13, family: 'Inter' },
                            bodyFont: { size: 12, family: 'Inter' }, padding: 12, cornerRadius: 10,
                            callbacks: { label: (ctx) => `${ctx.dataset.label}: ₹${Number(ctx.raw).toLocaleString('en-IN')}` }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.04)' },
                            ticks: {
                                font: { size: 11, family: 'Inter' }, color: '#9ca3af',
                                callback: (v) => '₹' + (v >= 1000 ? (v/1000).toFixed(0) + 'k' : v)
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, family: 'Inter' }, color: '#9ca3af' }
                        }
                    }
                }
            });
        },


        renderCategoryChart() {
            const canvas = document.getElementById('salesByCategoryChart');
            if (!canvas) { console.warn('salesByCategoryChart canvas not found'); return; }
            if (this.categoryChart) { this.categoryChart.destroy(); this.categoryChart = null; }

            const cat = this.stats.sales_by_category;
            const hasData = cat.pos_sales > 0 || cat.repair_sales > 0;

            // Pass canvas element directly (not context) — required for Chart.js 4
            this.categoryChart = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: ['POS Sales', 'Repair Sales'],
                    datasets: [{
                        data: hasData ? [cat.pos_sales, cat.repair_sales] : [1, 1],
                        backgroundColor: hasData
                            ? ['#3b82f6', '#f97316']
                            : ['#e5e7eb', '#d1d5db'],
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    width: 240,
                    height: 240,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            display: true, position: 'bottom',
                            labels: { usePointStyle: true, pointStyle: 'circle', padding: 16, font: { size: 12, family: 'Inter', weight: '500' } }
                        },
                        tooltip: {
                            enabled: hasData, backgroundColor: '#1e293b', padding: 12, cornerRadius: 10,
                            titleFont: { family: 'Inter' }, bodyFont: { family: 'Inter' },
                            callbacks: { label: (ctx) => `${ctx.label}: ₹${Number(ctx.raw).toLocaleString('en-IN')}` }
                        }
                    }
                }
            });
        },

        formatCurrency(val) {
            return '₹' + Number(val || 0).toLocaleString('en-IN', { minimumFractionDigits: 2 });
        },

        async addReminder() {
            if (!this.newReminder.title.trim()) return;
            try {
                const res = await fetch('/dashboard/reminders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.newReminder)
                });
                const result = await res.json();
                if (result.success) {
                    this.stats.reminders.unshift(result.data);
                    this.newReminder = { title: '', description: '', due_date: '' };
                    this.showReminderForm = false;
                }
            } catch (e) { console.error('Error adding reminder:', e); }
        },

        async toggleReminder(id) {
            try {
                const res = await fetch(`/dashboard/reminders/${id}/toggle`, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const result = await res.json();
                if (result.success) {
                    const rem = this.stats.reminders.find(r => r.id === id);
                    if (rem) rem.is_completed = result.data.is_completed;
                }
            } catch (e) { console.error('Error toggling reminder:', e); }
        },

        async deleteReminder(id) {
            try {
                const res = await fetch(`/dashboard/reminders/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                const result = await res.json();
                if (result.success) {
                    this.stats.reminders = this.stats.reminders.filter(r => r.id !== id);
                }
            } catch (e) { console.error('Error deleting reminder:', e); }
        },
    };
}
</script>
@endpush
