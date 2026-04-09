@extends('layouts.app')
@section('page-title', 'AePS Banking')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .aeps-page { gap: 0.5rem; }
    .aeps-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0ea5e9 50%, #06b6d4 100%);
        border-radius: 0.8rem;
        color: white;
        padding: 1rem;
        position: relative;
        overflow: hidden;
    }
    .aeps-hero::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -15%;
        width: 180px;
        height: 180px;
        background: rgba(255,255,255,0.06);
        border-radius: 50%;
    }
    .aeps-grid-btn {
        border-radius: 0.8rem;
        padding: 0.8rem 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 2px solid transparent;
        font-weight: 700;
        font-size: 0.72rem;
        text-align: center;
        min-height: 0;
    }
    .aeps-grid-btn:active { transform: scale(0.97); }
    .aeps-grid-btn .gi {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .aeps-card {
        border-radius: 0.8rem;
        background: white;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .aeps-card-hdr {
        padding: 0.6rem 0.8rem;
        font-weight: 700;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .aeps-li {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 0.8rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .aeps-li:last-child { border-bottom: none; }
    .aeps-li-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .aeps-form {
        border-radius: 0.8rem;
        background: white;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        width: 100%;
        max-width: 480px;
        margin: 0 auto;
        box-sizing: border-box;
    }
    .aeps-inp {
        width: 100%;
        padding: 0.65rem 0.8rem;
        border: 2px solid #e2e8f0;
        border-radius: 0.6rem;
        font-size: 0.9rem;
        font-weight: 600;
        transition: border-color 0.15s;
        outline: none;
        box-sizing: border-box;
    }
    .aeps-inp:focus { border-color: #0ea5e9; }
    .aeps-pill {
        padding: 0.5rem 0.6rem;
        border-radius: 0.6rem;
        font-weight: 700;
        font-size: 0.75rem;
        cursor: pointer;
        border: 2px solid #e2e8f0;
        transition: all 0.15s;
        text-align: center;
    }
    .aeps-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 0.7rem;
        font-weight: 800;
        font-size: 0.95rem;
        color: white;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }
    .aeps-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .aeps-back {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.78rem;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 0.4rem;
    }
    .aeps-back:hover { background: #f1f5f9; }
    .aeps-badge {
        display: inline-flex;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        font-size: 0.62rem;
        font-weight: 700;
        text-transform: uppercase;
    }
    .aeps-stat {
        border-radius: 0.7rem;
        padding: 0.6rem;
        text-align: center;
    }
    .aeps-filter-bar {
        padding: 0.6rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .aeps-filter-row {
        display: grid;
        gap: 0.5rem;
    }
    .aeps-filter-row.cols-3 {
        grid-template-columns: 1fr 1fr 1fr;
    }
    .aeps-filter-inp {
        width: 100%;
        padding: 0.5rem 0.6rem;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.5rem;
        font-size: 0.78rem;
        font-weight: 600;
        outline: none;
        box-sizing: border-box;
        background: white;
        color: #334155;
        transition: border-color 0.15s;
    }
    .aeps-filter-inp:focus { border-color: #0ea5e9; }
    .aeps-list-header {
        display: grid;
        grid-template-columns: 2.5rem 1fr auto;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        background: #f1f5f9;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
    }
    .aeps-li {
        display: grid;
        grid-template-columns: 2.5rem 1fr auto;
        gap: 0.5rem;
        padding: 0.6rem 0.8rem;
        border-bottom: 1px solid #f1f5f9;
        align-items: center;
    }
    .aeps-li:last-child { border-bottom: none; }
    .aeps-li:hover { background: #fafbfd; }
    .aeps-li-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.6rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .aeps-pager {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.5rem 0.8rem;
        border-top: 1px solid #e2e8f0;
        background: #fafbfc;
    }
    .aeps-pager-btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.72rem;
        font-weight: 700;
        border: 1.5px solid #e2e8f0;
        border-radius: 0.4rem;
        background: white;
        cursor: pointer;
        transition: all 0.15s;
    }
    .aeps-pager-btn:hover:not(:disabled) { border-color: #0ea5e9; color: #0ea5e9; }
    .aeps-pager-btn:disabled { opacity: 0.35; cursor: not-allowed; }
    @media (max-width: 400px) {
        .aeps-hero { padding: 0.8rem; }
        .aeps-grid-btn { padding: 0.6rem 0.3rem; font-size: 0.65rem; }
        .aeps-grid-btn .gi { width: 34px; height: 34px; font-size: 1.1rem; }
        .aeps-form { padding: 0.8rem; }
        .aeps-inp { padding: 0.55rem 0.7rem; font-size: 0.85rem; }
        .aeps-filter-row.cols-3 { grid-template-columns: 1fr; }
    }
    @media (min-width: 640px) {
        .aeps-filter-bar { flex-direction: row; flex-wrap: wrap; }
        .aeps-filter-bar > input,
        .aeps-filter-bar > .aeps-filter-row { flex: 1; min-width: 0; }
    }
</style>

<div x-data="aepsApp()" x-init="init()" class="flex flex-col h-full aeps-page">

    {{-- ══ HOME ══ --}}
    <template x-if="currentView === 'home'">
        <div class="flex flex-col gap-2.5 overflow-y-auto pb-4">

            {{-- Wallet --}}
            <div class="aeps-hero">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <div class="text-xs font-semibold opacity-80">AePS Wallet</div>
                        <div class="text-2xl sm:text-3xl font-extrabold mt-0.5" x-text="'\u20b9' + Number(stats.balance || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></div>
                    </div>
                    <div class="text-4xl opacity-20">&#x1f3e6;</div>
                </div>
                <div class="grid grid-cols-3 gap-1.5 mt-2.5 relative z-10">
                    <div class="bg-white/15 rounded-md px-2 py-1 text-center">
                        <div class="text-base font-extrabold" x-text="stats.total_customers || 0"></div>
                        <div class="text-[9px] font-semibold opacity-75">Customers</div>
                    </div>
                    <div class="bg-white/15 rounded-md px-2 py-1 text-center">
                        <div class="text-base font-extrabold" x-text="stats.total_transactions || 0"></div>
                        <div class="text-[9px] font-semibold opacity-75">Transactions</div>
                    </div>
                    <div class="bg-white/15 rounded-md px-2 py-1 text-center">
                        <div class="text-base font-extrabold" x-text="'\u20b9' + Number(stats.total_amount || 0).toLocaleString('en-IN')"></div>
                        <div class="text-[9px] font-semibold opacity-75">Total Amt</div>
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div class="grid grid-cols-5 gap-1.5">
                <button @click="startNewEntry('cash_withdrawal')" class="aeps-grid-btn bg-gradient-to-b from-purple-500 to-purple-700 text-white">
                    <div class="gi bg-white/20">&#x1f4b8;</div>
                    <span>Cash W/D</span>
                </button>
                <button @click="startNewEntry('balance_enquiry')" class="aeps-grid-btn bg-gradient-to-b from-blue-500 to-blue-700 text-white">
                    <div class="gi bg-white/20">&#x1f50d;</div>
                    <span>Bal Enq</span>
                </button>
                <button @click="startNewEntry('mini_statement')" class="aeps-grid-btn bg-gradient-to-b from-teal-500 to-teal-700 text-white">
                    <div class="gi bg-white/20">&#x1f4c4;</div>
                    <span>Mini Stmt</span>
                </button>
                <button @click="startNewEntry('cash_deposit')" class="aeps-grid-btn bg-gradient-to-b from-green-500 to-green-700 text-white">
                    <div class="gi bg-white/20">&#x1f4b0;</div>
                    <span>Cash Dep</span>
                </button>
                <button @click="startNewEntry('aadhaar_pay')" class="aeps-grid-btn bg-gradient-to-b from-orange-500 to-orange-600 text-white">
                    <div class="gi bg-white/20">&#x1faaa;</div>
                    <span>Aadhaar Pay</span>
                </button>
            </div>

            {{-- Quick Actions --}}
            <div class="grid grid-cols-4 gap-1.5">
                <button @click="currentView = 'topup'" class="aeps-grid-btn bg-sky-50 text-sky-800 border-sky-200">
                    <div class="gi bg-sky-200" style="width:32px;height:32px;font-size:1rem;">&#x2795;</div>
                    <span>Top Up</span>
                </button>
                <button @click="currentView = 'withdraw'" class="aeps-grid-btn bg-red-50 text-red-800 border-red-200">
                    <div class="gi bg-red-200" style="width:32px;height:32px;font-size:1rem;">&#x1f4b3;</div>
                    <span>Withdraw</span>
                </button>
                <button @click="currentView = 'history'; loadServices()" class="aeps-grid-btn bg-amber-50 text-amber-800 border-amber-200">
                    <div class="gi bg-amber-200" style="width:32px;height:32px;font-size:1rem;">&#x1f4cb;</div>
                    <span>Entries</span>
                </button>
                <button @click="currentView = 'wallet_history'; loadWalletTxns()" class="aeps-grid-btn bg-indigo-50 text-indigo-800 border-indigo-200">
                    <div class="gi bg-indigo-200" style="width:32px;height:32px;font-size:1rem;">&#x1f3e6;</div>
                    <span>Wallet</span>
                </button>
            </div>

            {{-- Recent --}}
            <div class="aeps-card">
                <div class="aeps-card-hdr bg-gray-50">
                    <span>&#x1f4cb;</span>
                    <span class="text-gray-800">Recent Entries</span>
                    <span class="ml-auto text-[11px] text-sky-600 font-semibold cursor-pointer" @click="currentView = 'history'; loadServices()">View All &rarr;</span>
                </div>
                <div class="aeps-list-header">
                    <span></span>
                    <span>Customer / Type</span>
                    <span>Amount</span>
                </div>
                <div>
                    <template x-for="s in servicesList.slice(0,5)" :key="s.id">
                        <div class="aeps-li">
                            <div class="aeps-li-icon" :class="svcIconBg(s.service_type)" x-text="svcEmoji(s.service_type)"></div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-gray-800 text-[13px] truncate" x-text="s.customer ? s.customer.name : s.customer_name"></span>
                                    <span class="aeps-badge" :class="s.status==='success' ? 'bg-green-100 text-green-700' : s.status==='failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'" x-text="s.status"></span>
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5 truncate">
                                    <span x-text="svcTypeLabel(s.service_type)"></span> &bull; <span x-text="fmtDate(s.created_at)"></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-gray-800 text-sm whitespace-nowrap" x-text="'\u20b9' + Number(s.amount).toLocaleString('en-IN')"></div>
                                <div x-show="s.bank_name" class="text-[9px] text-gray-400 truncate" x-text="s.bank_name"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="servicesList.length === 0 && !loading" class="text-center py-6 text-gray-400">
                        <div class="text-2xl mb-1">&#x1f4ed;</div>
                        <div class="text-xs font-medium">No entries yet</div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div x-show="stats.by_service_type && stats.by_service_type.length > 0" class="aeps-card">
                <div class="aeps-card-hdr bg-gray-50">
                    <span>&#x1f4ca;</span>
                    <span class="text-gray-800">Summary</span>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5 p-2">
                    <template x-for="st in stats.by_service_type" :key="st.service_type">
                        <div class="aeps-stat" :class="svcStatBg(st.service_type)">
                            <div class="text-lg" x-text="svcEmoji(st.service_type)"></div>
                            <div class="text-lg font-extrabold" x-text="st.count"></div>
                            <div class="text-[9px] font-bold uppercase opacity-70 leading-tight" x-text="svcTypeLabel(st.service_type)"></div>
                            <div class="text-[10px] font-bold opacity-60" x-text="'\u20b9' + Number(st.total_amount).toLocaleString('en-IN')"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ NEW ENTRY ══ --}}
    <template x-if="currentView === 'new_entry'">
        <div class="flex flex-col gap-2 overflow-y-auto pb-4">
            <button @click="currentView = 'home'" class="aeps-back self-start">&larr; Back</button>
            <div class="aeps-form">
                <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl" :class="svcIconBg(svcForm.service_type)" x-text="svcEmoji(svcForm.service_type)"></div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-800" x-text="svcTypeLabel(svcForm.service_type)"></h3>
                        <p class="text-[11px] text-gray-400">Fill details below</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Customer Name <span class="text-red-500">*</span></label>
                        <div class="relative" @click.away="custDropOpen = false">
                            <input x-model="svcForm.customer_name" @focus="custDropOpen = true" @input="custDropOpen = true; searchCustomers()" type="text" class="aeps-inp" placeholder="Type customer name...">
                            <div x-show="custDropOpen && custResults.length > 0" x-cloak class="absolute left-0 right-0 mt-1 rounded-lg border bg-white shadow-lg z-50 max-h-36 overflow-y-auto">
                                <template x-for="c in custResults" :key="c.id">
                                    <button type="button" @click="pickCustomer(c)" class="w-full text-left px-3 py-2 hover:bg-sky-50 text-sm border-b last:border-0 font-medium">
                                        <span x-text="c.name"></span>
                                        <span class="text-[11px] text-gray-400 ml-1" x-text="c.mobile_number"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Aadhaar Last 4</label>
                        <input x-model="svcForm.aadhaar_last4" type="text" class="aeps-inp" placeholder="1234" maxlength="4" inputmode="numeric" @input="svcForm.aadhaar_last4 = svcForm.aadhaar_last4.replace(/\D/g,'').slice(0,4)">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Amount (\u20b9) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">\u20b9</span>
                            <input x-model.number="svcForm.amount" type="number" min="0" step="1" class="aeps-inp pl-8 text-right text-lg" placeholder="0" @keydown="['e','E','+','-','.'].includes($event.key) && $event.preventDefault()">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Bank Name</label>
                        <input x-model="svcForm.bank_name" type="text" class="aeps-inp" placeholder="SBI, PNB, BOI..." maxlength="100">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Transaction Ref</label>
                        <input x-model="svcForm.transaction_ref" type="text" class="aeps-inp" placeholder="Ref number" maxlength="100">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1.5 block">Status</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            <button type="button" @click="svcForm.status = 'success'" :class="svcForm.status === 'success' ? 'bg-green-500 text-white border-green-500' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x2705; Success</button>
                            <button type="button" @click="svcForm.status = 'failed'" :class="svcForm.status === 'failed' ? 'bg-red-500 text-white border-red-500' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x274c; Failed</button>
                            <button type="button" @click="svcForm.status = 'pending'" :class="svcForm.status === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x23f3; Pending</button>
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Notes</label>
                        <textarea x-model="svcForm.notes" class="aeps-inp" rows="2" placeholder="Optional notes..." maxlength="500" style="resize:none;"></textarea>
                    </div>
                    <p x-show="svcError" x-text="svcError" class="text-xs text-red-600 font-bold bg-red-50 p-2 rounded-lg"></p>
                    <p x-show="svcSuccess" x-text="svcSuccess" class="text-xs text-green-600 font-bold bg-green-50 p-2 rounded-lg"></p>
                    <button @click="saveService()" :disabled="svcSaving" class="aeps-btn" :class="svcBtnColor(svcForm.service_type)">
                        <span x-show="svcSaving" class="spinner"></span>
                        <span x-text="svcSaving ? 'Saving...' : '&#x2705; Save Entry'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ TOP UP ══ --}}
    <template x-if="currentView === 'topup'">
        <div class="flex flex-col gap-2 overflow-y-auto pb-4">
            <button @click="currentView = 'home'" class="aeps-back self-start">&larr; Back</button>
            <div class="aeps-form">
                <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-sky-100 flex items-center justify-center text-xl">&#x2795;</div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-800">Wallet Top Up</h3>
                        <p class="text-[11px] text-gray-400">Add money to wallet</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">\u20b9</span>
                            <input x-model.number="topupForm.amount" type="number" min="1" step="1" class="aeps-inp pl-8 text-right text-lg" placeholder="0" @keydown="['e','E','+','-','.'].includes($event.key) && $event.preventDefault()">
                        </div>
                    </div>
                    <div class="grid grid-cols-4 gap-1.5">
                        <template x-for="a in [500, 1000, 2000, 5000]" :key="a">
                            <button type="button" @click="topupForm.amount = a" :class="topupForm.amount === a ? 'bg-sky-500 text-white border-sky-500' : 'bg-sky-50 text-sky-700 border-sky-200'" class="aeps-pill text-xs" x-text="'\u20b9' + a.toLocaleString('en-IN')"></button>
                        </template>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1.5 block">Payment Method <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 gap-1.5">
                            <button type="button" @click="topupForm.payment_method = 'cash'" :class="topupForm.payment_method === 'cash' ? 'bg-green-100 text-green-800 border-green-400 ring-1 ring-green-400' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x1f4b5; Cash</button>
                            <button type="button" @click="topupForm.payment_method = 'upi'" :class="topupForm.payment_method === 'upi' ? 'bg-purple-100 text-purple-800 border-purple-400 ring-1 ring-purple-400' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x1f4f2; UPI</button>
                            <button type="button" @click="topupForm.payment_method = 'bank_transfer'" :class="topupForm.payment_method === 'bank_transfer' ? 'bg-blue-100 text-blue-800 border-blue-400 ring-1 ring-blue-400' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x1f3e6; Bank</button>
                            <button type="button" @click="topupForm.payment_method = 'card'" :class="topupForm.payment_method === 'card' ? 'bg-orange-100 text-orange-800 border-orange-400 ring-1 ring-orange-400' : 'bg-white text-gray-600 border-gray-200'" class="aeps-pill">&#x1f4b3; Card</button>
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Reference No.</label>
                        <input x-model="topupForm.reference" type="text" class="aeps-inp" placeholder="Transaction reference" maxlength="150">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Notes</label>
                        <input x-model="topupForm.notes" type="text" class="aeps-inp" placeholder="Optional notes" maxlength="500">
                    </div>
                    <p x-show="topupError" x-text="topupError" class="text-xs text-red-600 font-bold bg-red-50 p-2 rounded-lg"></p>
                    <p x-show="topupSuccess" x-text="topupSuccess" class="text-xs text-green-600 font-bold bg-green-50 p-2 rounded-lg"></p>
                    <button @click="doTopUp()" :disabled="topupSaving" class="aeps-btn bg-gradient-to-r from-sky-500 to-cyan-500">
                        <span x-show="topupSaving" class="spinner"></span>
                        <span x-text="topupSaving ? 'Processing...' : '&#x2795; Add Funds'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ WITHDRAW ══ --}}
    <template x-if="currentView === 'withdraw'">
        <div class="flex flex-col gap-2 overflow-y-auto pb-4">
            <button @click="currentView = 'home'" class="aeps-back self-start">&larr; Back</button>
            <div class="aeps-form">
                <div class="flex items-center gap-2.5 mb-3 pb-2.5 border-b border-gray-100">
                    <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center text-xl">&#x1f4b3;</div>
                    <div>
                        <h3 class="text-base font-extrabold text-gray-800">Withdraw</h3>
                        <p class="text-[11px] text-gray-400">Bal: <strong class="text-sky-600" x-text="'\u20b9' + Number(stats.balance || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></strong></p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Amount <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">\u20b9</span>
                            <input x-model.number="withdrawForm.amount" type="number" min="1" step="1" class="aeps-inp pl-8 text-right text-lg" placeholder="0" @keydown="['e','E','+','-','.'].includes($event.key) && $event.preventDefault()">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Reference No.</label>
                        <input x-model="withdrawForm.reference" type="text" class="aeps-inp" placeholder="Transaction reference" maxlength="150">
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-gray-500 mb-1 block">Notes</label>
                        <input x-model="withdrawForm.notes" type="text" class="aeps-inp" placeholder="Optional notes" maxlength="500">
                    </div>
                    <p x-show="withdrawError" x-text="withdrawError" class="text-xs text-red-600 font-bold bg-red-50 p-2 rounded-lg"></p>
                    <p x-show="withdrawSuccess" x-text="withdrawSuccess" class="text-xs text-green-600 font-bold bg-green-50 p-2 rounded-lg"></p>
                    <button @click="doWithdraw()" :disabled="withdrawSaving" class="aeps-btn bg-gradient-to-r from-red-500 to-pink-500">
                        <span x-show="withdrawSaving" class="spinner"></span>
                        <span x-text="withdrawSaving ? 'Processing...' : '&#x1f4b3; Withdraw'"></span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ ALL ENTRIES ══ --}}
    <template x-if="currentView === 'history'">
        <div class="flex flex-col gap-2 overflow-y-auto pb-4">
            <button @click="currentView = 'home'" class="aeps-back self-start">&larr; Back</button>
            <div class="aeps-card">
                <div class="aeps-card-hdr bg-amber-50">
                    <span>&#x1f4cb;</span>
                    <span class="text-gray-800">All Entries</span>
                    <span class="ml-auto text-[11px] text-gray-400 font-semibold" x-text="'Total: ' + (svcPagination.total || 0)"></span>
                </div>
                <div class="aeps-filter-bar">
                    <input x-model="svcSearch" @input.debounce.300ms="loadServices()" type="text" class="aeps-filter-inp" placeholder="&#x1f50d; Search customer, ref...">
                    <div class="aeps-filter-row cols-3">
                        <select x-model="svcTypeFilter" @change="loadServices()" class="aeps-filter-inp">
                            <option value="">All Types</option>
                            <option value="cash_withdrawal">Cash W/D</option>
                            <option value="balance_enquiry">Bal Enq</option>
                            <option value="mini_statement">Mini Stmt</option>
                            <option value="cash_deposit">Cash Dep</option>
                            <option value="aadhaar_pay">Aadhaar Pay</option>
                        </select>
                        <input x-model="svcDateFrom" @change="loadServices()" type="date" class="aeps-filter-inp" title="From date">
                        <input x-model="svcDateTo" @change="loadServices()" type="date" class="aeps-filter-inp" title="To date">
                    </div>
                </div>
                <div class="aeps-list-header">
                    <span></span>
                    <span>Customer / Type</span>
                    <span>Amount</span>
                </div>
                <div>
                    <template x-for="s in servicesList" :key="s.id">
                        <div class="aeps-li">
                            <div class="aeps-li-icon" :class="svcIconBg(s.service_type)" x-text="svcEmoji(s.service_type)"></div>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-gray-800 text-[13px] truncate" x-text="s.customer ? s.customer.name : s.customer_name"></span>
                                    <span class="aeps-badge" :class="s.status==='success' ? 'bg-green-100 text-green-700' : s.status==='failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'" x-text="s.status"></span>
                                </div>
                                <div class="text-[11px] text-gray-400 mt-0.5 truncate">
                                    <span x-text="svcTypeLabel(s.service_type)"></span>
                                    <template x-if="s.bank_name"><span> &bull; <span x-text="s.bank_name"></span></span></template>
                                    &bull; <span x-text="fmtDate(s.created_at)"></span>
                                </div>
                                <div x-show="s.transaction_ref" class="text-[9px] text-gray-300 font-mono mt-0.5 truncate" x-text="'Ref: ' + s.transaction_ref"></div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-gray-800 text-sm whitespace-nowrap" x-text="'\u20b9' + Number(s.amount).toLocaleString('en-IN')"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="servicesList.length === 0 && !loading" class="text-center py-8 text-gray-400">
                        <div class="text-2xl mb-1">&#x1f4ed;</div>
                        <div class="text-xs font-medium">No entries found</div>
                    </div>
                </div>
                <div x-show="svcPagination.last_page > 1" class="aeps-pager">
                    <span class="text-[11px] text-gray-500 font-semibold">Page <span x-text="svcPagination.current_page"></span> of <span x-text="svcPagination.last_page"></span></span>
                    <div class="flex gap-1.5">
                        <button @click="svcPage--; loadServices()" :disabled="svcPage <= 1" class="aeps-pager-btn">&larr; Prev</button>
                        <button @click="svcPage++; loadServices()" :disabled="svcPage >= svcPagination.last_page" class="aeps-pager-btn">Next &rarr;</button>
                    </div>
                </div>
            </div>
        </div>
    </template>

    {{-- ══ WALLET HISTORY ══ --}}
    <template x-if="currentView === 'wallet_history'">
        <div class="flex flex-col gap-2 overflow-y-auto pb-4">
            <button @click="currentView = 'home'" class="aeps-back self-start">&larr; Back</button>
            <div class="aeps-card">
                <div class="aeps-card-hdr bg-indigo-50">
                    <span>&#x1f3e6;</span>
                    <span class="text-gray-800">Wallet History</span>
                    <span class="ml-auto text-xs font-extrabold text-sky-600" x-text="'Bal: \u20b9' + Number(stats.balance || 0).toLocaleString('en-IN', {minimumFractionDigits:2})"></span>
                </div>
                <div class="aeps-filter-bar">
                    <input x-model="wtSearch" @input.debounce.300ms="loadWalletTxns()" type="text" class="aeps-filter-inp" placeholder="&#x1f50d; Search ref, notes...">
                    <div class="aeps-filter-row cols-3">
                        <select x-model="wtTypeFilter" @change="loadWalletTxns()" class="aeps-filter-inp">
                            <option value="">All Types</option>
                            <option value="topup">Top Up</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="adjustment">Adjustment</option>
                        </select>
                        <input x-model="wtDateFrom" @change="loadWalletTxns()" type="date" class="aeps-filter-inp" title="From date">
                        <input x-model="wtDateTo" @change="loadWalletTxns()" type="date" class="aeps-filter-inp" title="To date">
                    </div>
                </div>
                <div class="aeps-list-header">
                    <span></span>
                    <span>Transaction</span>
                    <span>Amount</span>
                </div>
                <div>
                    <template x-for="t in walletTxns" :key="t.id">
                        <div class="aeps-li">
                            <div class="aeps-li-icon" :class="t.direction === 'IN' ? 'bg-green-100' : 'bg-red-100'">
                                <span x-text="t.direction === 'IN' ? '\u2b06\ufe0f' : '\u2b07\ufe0f'"></span>
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-gray-800 text-[13px] capitalize" x-text="t.type === 'topup' ? 'Top Up' : t.type === 'withdrawal' ? 'Withdrawal' : t.type"></div>
                                <div class="text-[11px] text-gray-400 mt-0.5 truncate">
                                    <span x-text="fmtDate(t.created_at)"></span>
                                    <template x-if="t.payment_method"><span> &bull; <span class="capitalize" x-text="t.payment_method"></span></span></template>
                                </div>
                                <div x-show="t.reference" class="text-[9px] text-gray-300 font-mono mt-0.5 truncate" x-text="'Ref: ' + t.reference"></div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-sm whitespace-nowrap" :class="t.direction==='IN' ? 'text-green-600' : 'text-red-600'" x-text="(t.direction==='IN' ? '+' : '-') + '\u20b9' + Number(t.amount).toLocaleString('en-IN', {minimumFractionDigits:2})"></div>
                                <div class="text-[9px] text-gray-400 whitespace-nowrap" x-text="'Bal: \u20b9' + Number(t.balance_after).toLocaleString('en-IN', {minimumFractionDigits:2})"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="walletTxns.length === 0 && !loading" class="text-center py-8 text-gray-400">
                        <div class="text-2xl mb-1">&#x1f3e6;</div>
                        <div class="text-xs font-medium">No transactions yet</div>
                    </div>
                </div>
                <div x-show="wtPagination.last_page > 1" class="aeps-pager">
                    <span class="text-[11px] text-gray-500 font-semibold">Page <span x-text="wtPagination.current_page"></span> of <span x-text="wtPagination.last_page"></span></span>
                    <div class="flex gap-1.5">
                        <button @click="wtPage--; loadWalletTxns()" :disabled="wtPage <= 1" class="aeps-pager-btn">&larr; Prev</button>
                        <button @click="wtPage++; loadWalletTxns()" :disabled="wtPage >= wtPagination.last_page" class="aeps-pager-btn">Next &rarr;</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function aepsApp() {
    return {
        currentView: 'home',
        loading: false,
        stats: {},
        servicesList: [],
        svcSearch: '', svcTypeFilter: '', svcDateFrom: '', svcDateTo: '', svcPage: 1, svcPagination: {},
        walletTxns: [],
        wtSearch: '', wtTypeFilter: '', wtDateFrom: '', wtDateTo: '', wtPage: 1, wtPagination: {},
        topupForm: { amount: '', payment_method: 'cash', reference: '', notes: '' },
        topupSaving: false, topupError: '', topupSuccess: '',
        withdrawForm: { amount: '', reference: '', notes: '' },
        withdrawSaving: false, withdrawError: '', withdrawSuccess: '',
        svcForm: { customer_id: null, customer_name: '', aadhaar_last4: '', service_type: 'cash_withdrawal', amount: '', bank_name: '', transaction_ref: '', status: 'success', notes: '' },
        svcSaving: false, svcError: '', svcSuccess: '',
        custDropOpen: false, custResults: [],

        async init() { await this.loadStats(); this.loadServices(); },

        startNewEntry(type) {
            this.svcForm = { customer_id: null, customer_name: '', aadhaar_last4: '', service_type: type, amount: '', bank_name: '', transaction_ref: '', status: 'success', notes: '' };
            this.svcError = ''; this.svcSuccess = '';
            this.currentView = 'new_entry';
        },

        async loadStats() { try { const r = await RepairBox.ajax('/admin/aeps/stats'); this.stats = r.data || {}; } catch(e){ console.error('loadStats', e); } },

        async loadServices() {
            this.loading = true;
            try {
                const p = new URLSearchParams({ page: this.svcPage, per_page: 20 });
                if (this.svcSearch) p.set('search', this.svcSearch);
                if (this.svcTypeFilter) p.set('service_type', this.svcTypeFilter);
                if (this.svcDateFrom) p.set('date_from', this.svcDateFrom);
                if (this.svcDateTo) p.set('date_to', this.svcDateTo);
                const r = await RepairBox.ajax('/admin/aeps/customer-services?' + p);
                this.servicesList = r.data || [];
                this.svcPagination = r.meta || { current_page: 1, last_page: 1, total: 0 };
            } catch(e){}
            this.loading = false;
        },

        async loadWalletTxns() {
            this.loading = true;
            try {
                const p = new URLSearchParams({ page: this.wtPage, per_page: 20 });
                if (this.wtSearch) p.set('search', this.wtSearch);
                if (this.wtTypeFilter) p.set('type', this.wtTypeFilter);
                if (this.wtDateFrom) p.set('date_from', this.wtDateFrom);
                if (this.wtDateTo) p.set('date_to', this.wtDateTo);
                const r = await RepairBox.ajax('/admin/aeps/wallet-transactions?' + p);
                this.walletTxns = r.data || [];
                this.wtPagination = r.meta || { current_page: 1, last_page: 1, total: 0 };
            } catch(e){}
            this.loading = false;
        },

        async doTopUp() {
            this.topupError = ''; this.topupSuccess = '';
            if (!this.topupForm.amount || this.topupForm.amount < 1) { this.topupError = 'Enter amount (min \u20b91)'; return; }
            this.topupSaving = true;
            try {
                const r = await RepairBox.ajax('/admin/aeps/top-up', 'POST', this.topupForm);
                if (r.success) {
                    this.topupSuccess = 'Added \u20b9' + Number(this.topupForm.amount).toLocaleString('en-IN');
                    this.topupForm = { amount: '', payment_method: 'cash', reference: '', notes: '' };
                    await this.loadStats();
                    setTimeout(() => { this.topupSuccess = ''; this.currentView = 'home'; }, 1500);
                } else { this.topupError = r.message || 'Failed'; }
            } catch(e) { this.topupError = e.message || 'Failed'; }
            this.topupSaving = false;
        },

        async doWithdraw() {
            this.withdrawError = ''; this.withdrawSuccess = '';
            if (!this.withdrawForm.amount || this.withdrawForm.amount < 1) { this.withdrawError = 'Enter amount (min \u20b91)'; return; }
            if (this.withdrawForm.amount > Number(this.stats.balance || 0)) { this.withdrawError = 'Not enough balance'; return; }
            this.withdrawSaving = true;
            try {
                const r = await RepairBox.ajax('/admin/aeps/withdraw', 'POST', this.withdrawForm);
                if (r.success) {
                    this.withdrawSuccess = 'Withdrawn \u20b9' + Number(this.withdrawForm.amount).toLocaleString('en-IN');
                    this.withdrawForm = { amount: '', reference: '', notes: '' };
                    await this.loadStats();
                    setTimeout(() => { this.withdrawSuccess = ''; this.currentView = 'home'; }, 1500);
                } else { this.withdrawError = r.message || 'Failed'; }
            } catch(e) { this.withdrawError = e.message || 'Failed'; }
            this.withdrawSaving = false;
        },

        async searchCustomers() {
            if (this.svcForm.customer_name.length < 2) { this.custResults = []; return; }
            try {
                const r = await RepairBox.ajax('/admin/customers?search=' + encodeURIComponent(this.svcForm.customer_name) + '&per_page=8');
                this.custResults = r.data || [];
            } catch(e) { this.custResults = []; }
        },
        pickCustomer(c) { this.svcForm.customer_id = c.id; this.svcForm.customer_name = c.name; this.custDropOpen = false; this.custResults = []; },

        async saveService() {
            this.svcError = ''; this.svcSuccess = '';
            if (!this.svcForm.customer_name.trim()) { this.svcError = 'Customer name required'; return; }
            if (!this.svcForm.service_type) { this.svcError = 'Select service type'; return; }
            if (this.svcForm.amount === '' || this.svcForm.amount < 0) { this.svcError = 'Enter amount'; return; }
            this.svcSaving = true;
            try {
                const r = await RepairBox.ajax('/admin/aeps/customer-services', 'POST', this.svcForm);
                if (r.success) {
                    this.svcSuccess = 'Saved!';
                    this.loadStats(); this.loadServices();
                    setTimeout(() => { this.svcSuccess = ''; this.currentView = 'home'; }, 1200);
                } else { this.svcError = r.message || 'Failed'; }
            } catch(e) { this.svcError = e.message || 'Failed'; }
            this.svcSaving = false;
        },

        fmtDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' }); },
        svcTypeLabel(t) { return { cash_withdrawal:'Cash Withdrawal', balance_enquiry:'Balance Enquiry', mini_statement:'Mini Statement', cash_deposit:'Cash Deposit', aadhaar_pay:'Aadhaar Pay' }[t] || t; },
        svcEmoji(t) { return { cash_withdrawal:'\u{1f4b8}', balance_enquiry:'\u{1f50d}', mini_statement:'\u{1f4c4}', cash_deposit:'\u{1f4b0}', aadhaar_pay:'\u{1faaa}' }[t] || '\u{1f4cb}'; },
        svcIconBg(t) { return { cash_withdrawal:'bg-purple-100', balance_enquiry:'bg-blue-100', mini_statement:'bg-teal-100', cash_deposit:'bg-green-100', aadhaar_pay:'bg-orange-100' }[t] || 'bg-gray-100'; },
        svcStatBg(t) { return { cash_withdrawal:'bg-purple-50', balance_enquiry:'bg-blue-50', mini_statement:'bg-teal-50', cash_deposit:'bg-green-50', aadhaar_pay:'bg-orange-50' }[t] || 'bg-gray-50'; },
        svcBtnColor(t) { return { cash_withdrawal:'bg-gradient-to-r from-purple-500 to-purple-700', balance_enquiry:'bg-gradient-to-r from-blue-500 to-blue-700', mini_statement:'bg-gradient-to-r from-teal-500 to-teal-700', cash_deposit:'bg-gradient-to-r from-green-500 to-green-700', aadhaar_pay:'bg-gradient-to-r from-orange-500 to-orange-600' }[t] || 'bg-gradient-to-r from-sky-500 to-cyan-500'; },
    };
}
</script>
@endsection
