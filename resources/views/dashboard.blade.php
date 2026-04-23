@extends('layouts.app')
@section('page-title', 'Dashboard')

@section('content')
<style>
    .dashboard-wrap {
        --dashboard-card-bg: rgba(255, 255, 255, 0.94);
        --dashboard-card-border: rgba(148, 163, 184, 0.14);
        --dashboard-muted: var(--app-text-faint);
        --dashboard-title: var(--app-heading);
        color: var(--app-text);
    }

    body[data-theme='graphite'] .dashboard-wrap {
        --dashboard-card-bg: rgba(248, 250, 252, 0.96);
        --dashboard-card-border: rgba(148, 163, 184, 0.18);
    }

    .dashboard-wrap > div {
        animation: dashboardFade 0.6s cubic-bezier(0.22, 1, 0.36, 1);
    }

    @keyframes dashboardFade {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .nav-card {
        display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 14px;
        padding: 30px 18px; border-radius: 24px; text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        border: 1px solid rgba(255,255,255,0.22);
        position: relative; overflow: hidden;
        box-shadow: 0 24px 55px -32px rgba(15, 23, 42, 0.55);
    }
    .nav-card::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 58%);
        pointer-events: none;
    }
    .nav-card::after {
        content: '';
        position: absolute;
        inset: auto -15% -55% auto;
        width: 9rem;
        height: 9rem;
        border-radius: 9999px;
        background: rgba(255,255,255,0.13);
        filter: blur(6px);
    }
    .nav-card:hover { transform: translateY(-6px) scale(1.01); box-shadow: 0 26px 60px -28px rgba(0,0,0,0.28); }
    .nav-card-icon {
        width: 60px; height: 60px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        background: rgba(255,255,255,0.22);
        backdrop-filter: blur(12px);
        transition: transform 0.3s ease;
        box-shadow: inset 0 1px 0 rgba(255,255,255,0.28);
    }
    .nav-card:hover .nav-card-icon { transform: scale(1.1); }
    .nav-card-label { font-size: 15px; font-weight: 800; color: #fff; text-align: center; letter-spacing: 0.01em; line-height: 1.3; }
    .nav-card-sub { font-size: 11px; font-weight: 600; color: rgba(255,255,255,0.82); text-align: center; letter-spacing: 0.08em; text-transform: uppercase; }

    .stat-card-new {
        background: var(--dashboard-card-bg); border-radius: 22px; padding: 22px; border: 1px solid var(--dashboard-card-border);
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden;
        box-shadow: 0 24px 55px -40px rgba(15, 23, 42, 0.32);
        backdrop-filter: blur(12px);
    }
    .stat-card-new::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        border-radius: 22px 22px 0 0; opacity: 0; transition: opacity 0.3s ease;
    }
    .stat-card-new:hover { transform: translateY(-4px); box-shadow: 0 24px 55px -34px rgba(15,23,42,0.26); }
    .stat-card-new:hover::before { opacity: 1; }

    .pipeline-card {
        border-radius: 18px; padding: 18px; border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); text-decoration: none; display: block;
    }
    .pipeline-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -5px rgba(0,0,0,0.1); }
    .pipeline-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }

    .chart-card {
        background: var(--dashboard-card-bg); border-radius: 24px; border: 1px solid var(--dashboard-card-border);
        overflow: hidden; box-shadow: 0 20px 55px -40px rgba(0,0,0,0.2); backdrop-filter: blur(12px);
    }
    .chart-header {
        padding: 20px 24px; border-bottom: 1px solid var(--dashboard-card-border);
        display: flex; align-items: center; justify-content: space-between;
    }

    .reminder-item {
        display: flex; align-items: center; gap: 12px; padding: 10px 12px;
        border-radius: 10px; transition: background 0.2s ease;
    }
    .reminder-item:hover { background: rgba(255,255,255,0.58); }
    .reminder-check {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.2s ease; flex-shrink: 0;
    }
    .reminder-check.done { background: #10b981; border-color: #10b981; }
    .reminder-check:hover { border-color: var(--app-primary); }

    .section-title { font-size: 15px; font-weight: 800; color: var(--dashboard-title); letter-spacing: -0.02em; }
    .section-badge { font-size: 11px; font-weight: 700; color: var(--dashboard-muted); background: rgba(255,255,255,0.7); padding: 4px 11px; border-radius: 999px; border: 1px solid var(--dashboard-card-border); }

    .card-wrap { background: var(--dashboard-card-bg); border-radius: 24px; border: 1px solid var(--dashboard-card-border); overflow: hidden; box-shadow: 0 22px 55px -40px rgba(0,0,0,0.2); backdrop-filter: blur(12px); }
    .card-head { padding: 20px 24px; border-bottom: 1px solid var(--dashboard-card-border); display: flex; align-items: center; justify-content: space-between; }

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
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.7); border-radius: 9999px; }

    .count-metric-card {
        display: flex; flex-direction: column; align-items: flex-start;
        padding: 24px; border-radius: 24px; text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        border: 1px solid rgba(255,255,255,0.22);
        position: relative; overflow: hidden;
        box-shadow: 0 20px 50px -28px rgba(15, 23, 42, 0.52);
    }
    .count-metric-card::before {
        content: ''; position: absolute; inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent 60%);
        pointer-events: none;
    }
    .count-metric-card::after {
        content: '';
        position: absolute;
        inset: auto -12% -52% auto;
        width: 10rem; height: 10rem;
        border-radius: 9999px;
        background: rgba(255,255,255,0.1);
        filter: blur(10px);
    }
    .count-metric-card:hover { transform: translateY(-6px) scale(1.01); box-shadow: 0 26px 60px -24px rgba(0,0,0,0.32); }

    @media (max-width: 768px) {
        .nav-card {
            padding: 22px 14px;
            border-radius: 20px;
        }

        .nav-card-icon {
            width: 52px;
            height: 52px;
        }

        .card-head,
        .chart-header {
            padding: 16px 18px;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .pipeline-grid {
            display: grid !important;
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 8px !important;
            align-items: stretch !important;
        }
        .pipeline-grid .pipeline-card {
            flex: none !important;
            min-width: unset !important;
            padding: 12px !important;
        }
        .pipeline-grid .pipeline-icon {
            width: 34px !important;
            height: 34px !important;
            border-radius: 10px !important;
            flex-shrink: 0;
        }
        .pipeline-grid [style*='font-size:24px;font-weight:800'] {
            font-size: 20px !important;
        }

        .chart-card canvas {
            max-width: 100%;
        }

        .dashboard-wrap [style*='position:relative;height:260px;width:100%;'] {
            height: 220px !important;
        }

        .dashboard-wrap [style*='display:flex;gap:8px;'] {
            flex-direction: column;
        }

        .dashboard-wrap [style*='display:flex;align-items:center;gap:10px;margin-bottom:18px;'] {
            flex-wrap: wrap;
        }

        .dashboard-wrap [style*='display:flex;align-items:center;justify-content:space-between;gap:12px;'] {
            align-items: flex-start !important;
            flex-direction: column;
        }

        .dashboard-wrap [style*='display:flex;flex-wrap:wrap;align-items:center;gap:12px;'] {
            gap: 0.75rem !important;
        }

        .dashboard-wrap [style*='padding:20px 24px;'] {
            padding: 16px 18px !important;
        }
    }

    @media (max-width: 480px) {
        .dashboard-wrap .grid.grid-cols-2.sm\:grid-cols-3.lg\:grid-cols-6,
        .dashboard-wrap .grid.grid-cols-2.md\:grid-cols-3.lg\:grid-cols-6,
        .dashboard-wrap .grid.grid-cols-3.gap-3 {
            grid-template-columns: 1fr !important;
        }

        .dashboard-wrap [style*='font-size:24px;font-weight:800'],
        .dashboard-wrap [style*='font-size:26px;font-weight:800'] {
            font-size: 20px !important;
        }
    }

    @media (min-width: 640px) and (max-width: 1023px) {
        .pipeline-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 10px !important;
        }
        .pipeline-grid .pipeline-card {
            padding: 14px !important;
        }
        .pipeline-grid .pipeline-icon {
            width: 38px !important;
            height: 38px !important;
        }
    }

    @media (min-width: 1024px) {
        .pipeline-grid {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 12px !important;
        }
        .pipeline-grid .pipeline-card {
            flex: 1 !important;
            min-width: 130px !important;
            padding: 18px !important;
        }
        .pipeline-grid .pipeline-icon {
            width: 42px !important;
            height: 42px !important;
            border-radius: 12px !important;
        }
        .pipeline-grid [style*='font-size:24px;font-weight:800'] {
            font-size: 24px !important;
        }
    }
</style>

<div class="dashboard-wrap" x-data="dashboardPage()">

    {{-- ============ COUNT CARDS: Today's Activity ============ --}}
    <div style="margin-bottom:28px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <span class="section-title">Today's Activity</span>
            <span class="section-badge" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;border-color:transparent;">Live</span>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Sales --}}
            <a href="/admin/invoices" class="count-metric-card" style="background:linear-gradient(135deg,#22c55e,#15803d);">
                <div style="position:relative;z-index:1;width:100%;">
                    <div style="margin-bottom:18px;">
                        <div style="width:48px;height:48px;background:rgba(255,255,255,0.22);border-radius:14px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);box-shadow:inset 0 1px 0 rgba(255,255,255,0.3);">
                            <svg style="width:25px;height:25px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                        </div>
                    </div>
                    <p style="font-size:46px;font-weight:900;color:#fff;margin:0;letter-spacing:-0.04em;line-height:1;" x-text="stats.today_sales_count">0</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                        <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.88);margin:0;letter-spacing:0.02em;">Sales Orders</p>
                        <svg style="width:16px;height:16px;color:rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
            {{-- Repairs --}}
            <a href="/admin/repairs" class="count-metric-card" style="background:linear-gradient(135deg,#f97316,#c2410c);">
                <div style="position:relative;z-index:1;width:100%;">
                    <div style="margin-bottom:18px;">
                        <div style="width:48px;height:48px;background:rgba(255,255,255,0.22);border-radius:14px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);box-shadow:inset 0 1px 0 rgba(255,255,255,0.3);">
                            <svg style="width:25px;height:25px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <p style="font-size:46px;font-weight:900;color:#fff;margin:0;letter-spacing:-0.04em;line-height:1;" x-text="stats.today_repairs_count">0</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                        <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.88);margin:0;letter-spacing:0.02em;">Repair Jobs</p>
                        <svg style="width:16px;height:16px;color:rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
            {{-- Recharges --}}
            <a href="/admin/recharges" class="count-metric-card" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                <div style="position:relative;z-index:1;width:100%;">
                    <div style="margin-bottom:18px;">
                        <div style="width:48px;height:48px;background:rgba(255,255,255,0.22);border-radius:14px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);box-shadow:inset 0 1px 0 rgba(255,255,255,0.3);">
                            <svg style="width:25px;height:25px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p style="font-size:46px;font-weight:900;color:#fff;margin:0;letter-spacing:-0.04em;line-height:1;" x-text="stats.today_recharges_count">0</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                        <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.88);margin:0;letter-spacing:0.02em;">Recharges</p>
                        <svg style="width:16px;height:16px;color:rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
            {{-- Expenses --}}
            <a href="/admin/expenses" class="count-metric-card" style="background:linear-gradient(135deg,#ef4444,#b91c1c);">
                <div style="position:relative;z-index:1;width:100%;">
                    <div style="margin-bottom:18px;">
                        <div style="width:48px;height:48px;background:rgba(255,255,255,0.22);border-radius:14px;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(8px);box-shadow:inset 0 1px 0 rgba(255,255,255,0.3);">
                            <svg style="width:25px;height:25px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <p style="font-size:46px;font-weight:900;color:#fff;margin:0;letter-spacing:-0.04em;line-height:1;" x-text="stats.today_expenses_count">0</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:12px;">
                        <p style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.88);margin:0;letter-spacing:0.02em;">Expenses</p>
                        <svg style="width:16px;height:16px;color:rgba(255,255,255,0.6);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============ ROW 2: Today's Money ============ --}}
    <div style="margin-bottom:32px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <span class="section-title">Today's Money</span>
            <span class="section-badge">Live</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Total Income (big card) --}}
            <div class="card-wrap" style="padding:24px;background:linear-gradient(135deg,#4f46e5 0%,#7c3aed 100%);border:none;box-shadow:0 20px 50px -20px rgba(79,70,229,0.45);">
                <p style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:0.1em;margin:0 0 6px;">Total Income Today</p>
                <p style="font-size:42px;font-weight:900;color:#fff;margin:0;letter-spacing:-0.03em;line-height:1.1;"
                   x-text="formatCurrency((stats.sales_by_category?.total_sales ?? 0))">₹0</p>
                <div style="margin-top:16px;display:flex;gap:10px;">
                    <div style="flex:1;background:rgba(255,255,255,0.15);border-radius:12px;padding:12px;">
                        <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.75);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 4px;">🛒 POS Sales</p>
                        <p style="font-size:18px;font-weight:800;color:#fff;margin:0;"
                           x-text="formatCurrency(stats.sales_by_category?.pos_sales ?? 0)">₹0</p>
                    </div>
                    <div style="flex:1;background:rgba(255,255,255,0.15);border-radius:12px;padding:12px;">
                        <p style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.75);text-transform:uppercase;letter-spacing:0.08em;margin:0 0 4px;">🔧 Repair</p>
                        <p style="font-size:18px;font-weight:800;color:#fff;margin:0;"
                           x-text="formatCurrency(stats.sales_by_category?.repair_sales ?? 0)">₹0</p>
                    </div>
                </div>
            </div>

            {{-- Customers Today --}}
            <div class="card-wrap" style="padding:24px;">
                <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.1em;margin:0 0 16px;">👥 Customers Today</p>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#f0fdf4;border-radius:14px;border:1px solid #bbf7d0;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:linear-gradient(135deg,#22c55e,#15803d);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#15803d;margin:0;">New Customers</p>
                                <p style="font-size:10px;color:#6b7280;margin:2px 0 0;">First time visit today</p>
                            </div>
                        </div>
                        <p style="font-size:30px;font-weight:900;color:#15803d;margin:0;" x-text="stats.new_customers_today">0</p>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#eff6ff;border-radius:14px;border:1px solid #bfdbfe;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:36px;height:36px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:700;color:#1d4ed8;margin:0;">Repeat Customers</p>
                                <p style="font-size:10px;color:#6b7280;margin:2px 0 0;">Came back again today</p>
                            </div>
                        </div>
                        <p style="font-size:30px;font-weight:900;color:#1d4ed8;margin:0;" x-text="stats.repeat_customers_today">0</p>
                    </div>
                </div>
            </div>

            {{-- Repair Tickets --}}
            <div class="card-wrap" style="padding:24px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:0.1em;margin:0;">🔧 Repair Tickets</p>
                    <a href="/admin/repairs" style="font-size:11px;font-weight:700;color:#4f46e5;text-decoration:none;">View All →</a>
                </div>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <a href="/admin/repairs?status=received" style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:#eff6ff;border-radius:12px;border:1px solid #bfdbfe;text-decoration:none;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:14px;height:14px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#1e40af;">Received</span>
                        </div>
                        <span style="font-size:22px;font-weight:900;color:#1e40af;" x-text="stats.repair_counts.received">0</span>
                    </a>
                    <a href="/admin/repairs?status=in_progress" style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:#fffbeb;border-radius:12px;border:1px solid #fde68a;text-decoration:none;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:14px;height:14px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#92400e;">In Progress</span>
                        </div>
                        <span style="font-size:22px;font-weight:900;color:#92400e;" x-text="stats.repair_counts.in_progress">0</span>
                    </a>
                    <a href="/admin/repairs?status=completed" style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:#f0fdfa;border-radius:12px;border:1px solid #99f6e4;text-decoration:none;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;background:linear-gradient(135deg,#14b8a6,#0d9488);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:14px;height:14px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#134e4a;">Completed</span>
                        </div>
                        <span style="font-size:22px;font-weight:900;color:#134e4a;" x-text="stats.repair_counts.completed">0</span>
                    </a>
                    <a href="/admin/repairs?status=closed" style="display:flex;align-items:center;justify-content:space-between;padding:11px 14px;background:#f8fafc;border-radius:12px;border:1px solid #cbd5e1;text-decoration:none;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:30px;height:30px;background:linear-gradient(135deg,#64748b,#475569);border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <svg style="width:14px;height:14px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <span style="font-size:13px;font-weight:700;color:#334155;">Closed</span>
                        </div>
                        <span style="font-size:22px;font-weight:900;color:#334155;" x-text="stats.repair_counts.closed">0</span>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- ============ ROW 3: Charts ============ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- LEFT: 7-Day Revenue Trend --}}
        <div class="chart-card">
            <div class="chart-header" style="padding:20px 24px 0;border-bottom:none;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(16,185,129,0.3);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <div>
                        <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;line-height:1.2;">Shop Revenue Trend</p>
                        <p style="font-size:12px;color:#64748b;margin:4px 0 0;font-weight:500;">POS + Repair sales · last 7 days</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:11px;font-weight:600;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:0.06em;">7-Day Total</p>
                    <p style="font-size:18px;font-weight:900;color:#059669;margin:0;"
                       x-text="formatCurrency((stats.sales_chart?.data ?? []).reduce((a,b)=>a+b,0) + (stats.sales_chart?.repair_data ?? []).reduce((a,b)=>a+b,0))">₹0</p>
                </div>
            </div>
            <div style="padding:16px 24px 20px;">
                <div style="position:relative;height:260px;width:100%;">
                    <canvas id="salesByDayChart"></canvas>
                </div>
            </div>
        </div>

        {{-- RIGHT: Today's Revenue Split --}}
        <div class="chart-card">
            <div class="chart-header" style="padding:20px 24px 0;border-bottom:none;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#8b5cf6,#6d28d9);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(139,92,246,0.3);">
                        <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                    </div>
                    <div>
                        <p style="font-size:15px;font-weight:800;color:#0f172a;margin:0;line-height:1.2;">Today's Earnings Split</p>
                        <p style="font-size:12px;color:#64748b;margin:4px 0 0;font-weight:500;">Where your money came from today</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:11px;font-weight:600;color:#9ca3af;margin:0 0 2px;text-transform:uppercase;letter-spacing:0.06em;">Today Total</p>
                    <p style="font-size:18px;font-weight:900;color:#6d28d9;margin:0;" x-text="formatCurrency(stats.sales_by_category.total_sales)">₹0</p>
                </div>
            </div>
            <div style="padding:16px 24px 0;display:flex;align-items:center;justify-content:center;">
                <canvas id="salesByCategoryChart" width="220" height="220" style="display:block;"></canvas>
            </div>
            {{-- Revenue split bars --}}
            <div style="padding:12px 24px 20px;">
                <div style="display:flex;flex-direction:column;gap:10px;">
                    {{-- POS bar --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:10px;height:10px;border-radius:3px;background:#3b82f6;flex-shrink:0;"></div>
                                <span style="font-size:12px;font-weight:600;color:#374151;">POS Sales</span>
                            </div>
                            <div style="text-align:right;">
                                <span style="font-size:14px;font-weight:800;color:#1d4ed8;" x-text="formatCurrency(stats.sales_by_category.pos_sales)">₹0</span>
                                <span style="font-size:11px;font-weight:600;color:#93c5fd;margin-left:6px;"
                                      x-text="stats.sales_by_category.total_sales > 0 ? Math.round(stats.sales_by_category.pos_sales / stats.sales_by_category.total_sales * 100) + '%' : '0%'">0%</span>
                            </div>
                        </div>
                        <div style="height:7px;background:#e0e7ff;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;background:linear-gradient(90deg,#3b82f6,#6366f1);border-radius:99px;transition:width 0.6s ease;"
                                 :style="`width:${stats.sales_by_category.total_sales > 0 ? Math.round(stats.sales_by_category.pos_sales / stats.sales_by_category.total_sales * 100) : 0}%`"></div>
                        </div>
                    </div>
                    {{-- Repair bar --}}
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                            <div style="display:flex;align-items:center;gap:7px;">
                                <div style="width:10px;height:10px;border-radius:3px;background:#f97316;flex-shrink:0;"></div>
                                <span style="font-size:12px;font-weight:600;color:#374151;">Repair Sales</span>
                            </div>
                            <div style="text-align:right;">
                                <span style="font-size:14px;font-weight:800;color:#c2410c;" x-text="formatCurrency(stats.sales_by_category.repair_sales)">₹0</span>
                                <span style="font-size:11px;font-weight:600;color:#fdba74;margin-left:6px;"
                                      x-text="stats.sales_by_category.total_sales > 0 ? Math.round(stats.sales_by_category.repair_sales / stats.sales_by_category.total_sales * 100) + '%' : '0%'">0%</span>
                            </div>
                        </div>
                        <div style="height:7px;background:#ffedd5;border-radius:99px;overflow:hidden;">
                            <div style="height:100%;background:linear-gradient(90deg,#f97316,#ef4444);border-radius:99px;transition:width 0.6s ease;"
                                 :style="`width:${stats.sales_by_category.total_sales > 0 ? Math.round(stats.sales_by_category.repair_sales / stats.sales_by_category.total_sales * 100) : 0}%`"></div>
                        </div>
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
        stats: {
            today_sales: 0, monthly_sales: 0, today_repairs: 0, pending_repairs: 0,
            monthly_expenses: 0, monthly_purchases: 0, today_recharges: 0,
            monthly_revenue: 0, monthly_outflow: 0,
            new_customers_today: 0, repeat_customers_today: 0,
            gross_sale_payments: 0, customer_purchase_amount: 0,
            purchase_order_amount: 0, refund_amount: 0,
            today_sales_count: 0, today_repairs_count: 0,
            today_recharges_count: 0, today_expenses_count: 0,
            repair_counts: { received: 0, in_progress: 0, completed: 0, closed: 0 },
            sales_by_category: { pos_sales: 0, repair_sales: 0, total_sales: 0 },
            sales_chart: { labels: [], data: [], repair_data: [] },
            low_stock_count: 0,
        },
        salesChart: null,
        categoryChart: null,

        async init() {
            try {
                const res = await fetch('/admin/dashboard', {
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
                    today_sales_count:        data.today_sales_count ?? 0,
                    today_repairs_count:      data.today_repairs_count ?? 0,
                    today_recharges_count:    data.today_recharges_count ?? 0,
                    today_expenses_count:     data.today_expenses_count ?? 0,
                    repair_counts:            data.repair_counts ?? this.stats.repair_counts,
                    sales_by_category:        data.sales_by_category ?? this.stats.sales_by_category,
                    sales_chart:              data.sales_chart ?? this.stats.sales_chart,
                    low_stock_count:          data.low_stock_count ?? 0,
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

    };
}
</script>
@endpush
