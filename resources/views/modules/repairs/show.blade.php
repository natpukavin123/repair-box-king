@extends('layouts.app')
@section('page-title', 'Repair #' . $repair->ticket_number)

@section('content')
<!-- Skeleton Loader (shows instantly, hidden once Alpine initializes) -->
<div x-data="{ ready: false }" x-init="ready = true" x-show="!ready" class="animate-pulse">
    <div class="mb-5">
        <div class="h-4 w-28 bg-gray-200 rounded mb-3"></div>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-40 bg-gray-200 rounded"></div>
                <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
            </div>
            <div class="flex items-center gap-2">
                <div class="h-9 w-20 bg-gray-200 rounded-lg"></div>
                <div class="h-9 w-20 bg-gray-200 rounded-lg"></div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5">
        <div class="flex items-center justify-between max-w-2xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
                <div class="w-8 h-0.5 bg-gray-200"></div>
                <div class="w-9 h-9 bg-gray-200 rounded-full"></div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-32 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="flex justify-between"><div class="h-4 w-24 bg-gray-200 rounded"></div><div class="h-4 w-32 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-20 bg-gray-200 rounded"></div><div class="h-4 w-28 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-28 bg-gray-200 rounded"></div><div class="h-4 w-36 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-16 bg-gray-200 rounded"></div><div class="h-4 w-24 bg-gray-200 rounded"></div></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-24 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-2">
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                    <div class="h-10 bg-gray-200 rounded"></div>
                </div>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="h-5 w-28 bg-gray-200 rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="flex justify-between"><div class="h-4 w-20 bg-gray-200 rounded"></div><div class="h-4 w-16 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-4 w-24 bg-gray-200 rounded"></div><div class="h-4 w-16 bg-gray-200 rounded"></div></div>
                    <div class="flex justify-between"><div class="h-5 w-28 bg-gray-200 rounded"></div><div class="h-5 w-20 bg-gray-200 rounded"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div x-data="repairDetail()" x-init="init()" x-cloak>

    <!-- ===== BREADCRUMB & HEADER ===== -->
    <div class="mb-5">
        <a href="/admin/repairs" class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Repairs
        </a>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800" x-text="repair.ticket_number"></h2>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold" :class="statusBadgeClass(repair.status)" x-text="statusLabel(repair.status)"></span>
                <template x-if="repair.record_type !== 'original'">
                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded bg-blue-100 text-blue-700" x-text="repair.record_type"></span>
                </template>
                <template x-if="repair.is_locked">
                    <span class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Locked
                    </span>
                </template>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <button x-show="!repair.is_locked" @click="openEditModal()" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Intake
                </button>
                <a :href="'/admin/repairs/' + repair.id + '/print'" target="_blank" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </a>
                <a x-show="repair.is_fully_paid" :href="'/admin/repairs/' + repair.id + '/invoice'" target="_blank" class="btn-primary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Invoice
                </a>
                <a x-show="(repair.repair_returns || []).length > 0" @click.prevent="document.getElementById('returns-section')?.scrollIntoView({behavior:'smooth'})" href="#returns-section" class="btn-secondary text-sm inline-flex items-center gap-1.5 !border-orange-300 !text-orange-700 hover:!bg-orange-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                    Returns
                </a>
            </div>
        </div>
    </div>

    <div class="rounded-[28px] border border-slate-200 bg-[linear-gradient(135deg,rgba(79,70,229,0.07),rgba(14,165,233,0.04),rgba(255,255,255,0.96))] p-5 shadow-[0_24px_70px_-42px_rgba(15,23,42,0.34)] mb-5">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.26em] text-slate-500">Intake Overview</p>
                <h3 class="mt-2 text-lg font-semibold text-slate-900">Core intake details stay visible</h3>
                <p class="mt-1 max-w-2xl text-sm text-slate-600">Customer, device, and issue summary are shown upfront so the repair record matches the new guided intake flow.</p>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4 xl:min-w-[620px]">
                <div class="rounded-2xl border border-white/70 bg-white/85 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Customer</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900" x-text="repair.customer?.name || 'Walk-in'"></div>
                    <div class="mt-1 text-xs text-slate-500" x-text="repair.customer?.mobile_number || 'No mobile'"></div>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/85 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Device</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900" x-text="[repair.device_brand, repair.device_model].filter(Boolean).join(' ') || 'Not recorded'"></div>
                    <div class="mt-1 text-xs text-slate-500" x-text="repair.imei || 'IMEI / Serial optional'"></div>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/85 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Estimate</div>
                    <div class="mt-2 text-sm font-semibold text-slate-900" x-text="'₹' + Number(repair.estimated_cost || 0).toFixed(2)"></div>
                    <div class="mt-1 text-xs text-slate-500" x-text="repair.expected_delivery_date ? 'Due ' + formatDate(repair.expected_delivery_date) : 'No due date'"></div>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/85 p-4 shadow-sm">
                    <div class="text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-400">Issue</div>
                    <div class="mt-2 text-sm leading-6 text-slate-700 line-clamp-4" x-text="repair.problem_description || 'No description recorded'"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PROGRESS BAR ===== -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5" x-show="repair.status !== 'cancelled'">
        <div class="mobile-scroll">
        <div class="flex items-center justify-between max-w-2xl mx-auto min-w-[640px] sm:min-w-0">
            <template x-for="(step, idx) in progressSteps" :key="step.key">
                <div class="flex items-center" :class="idx < progressSteps.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition-all border-2"
                            :class="stepReached(repair.status, step.key)
                                ? (repair.status === step.key ? statusDotCurrent(step.key) : 'bg-green-500 border-green-500 text-white')
                                : 'bg-white border-gray-200 text-gray-300'"
                            x-text="idx + 1">
                        </div>
                        <span class="text-[10px] mt-1 font-medium" :class="stepReached(repair.status, step.key) ? 'text-gray-700' : 'text-gray-300'" x-text="step.label"></span>
                    </div>
                    <div x-show="idx < progressSteps.length - 1" class="flex-1 h-0.5 mx-2 mt-[-14px]"
                        :class="stepReached(repair.status, step.key) && stepReached(repair.status, progressSteps[idx+1]?.key) ? 'bg-green-500' : 'bg-gray-200'"></div>
                </div>
            </template>
        </div>
        </div>
    </div>
    <div x-show="repair.status === 'cancelled'" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        <div>
            <p class="font-bold text-red-800">This repair has been cancelled</p>
            <p class="text-sm text-red-600" x-show="repair.cancel_reason" x-text="'Reason: ' + repair.cancel_reason"></p>
            <p x-show="repair.total_refunded > 0" class="text-sm text-red-700 mt-1">Refunded: <span class="font-bold" x-text="'₹' + Number(repair.total_refunded).toFixed(2)"></span></p>
        </div>
    </div>

    <!-- ===== TWO COLUMN LAYOUT ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- LEFT COLUMN (2/3) -->
        <div class="lg:col-span-2 space-y-5">

            <!-- ===== CLOSED - DOWNLOAD INVOICE ===== -->
            <template x-if="repair.status === 'closed'">
                <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 text-green-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-green-800">Repair Closed</p>
                            <p class="text-sm text-green-600">This repair is complete and payment is settled.</p>
                        </div>
                    </div>
                    <a :href="'/admin/repairs/' + repair.id + '/invoice'" target="_blank" class="btn-primary !bg-green-600 hover:!bg-green-700 !border-0 shadow-sm inline-flex items-center gap-2 text-sm px-4 py-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Download Invoice
                    </a>
                </div>
            </template>

            <!-- TABS NAVIGATION -->
            <div class="bg-white rounded-xl shadow-sm border border-b-0 rounded-b-none p-2 mobile-scroll">
                <div class="flex gap-1 min-w-max">
                <button @click="activeTab = 'work'" class="px-5 py-2.5 rounded-lg text-sm font-semibold transition" :class="activeTab === 'work' ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'">Work Order</button>
                <button @click="activeTab = 'history'" class="px-5 py-2.5 rounded-lg text-sm font-semibold transition" :class="activeTab === 'history' ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-700'">Status & History</button>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-t-0 rounded-b-xl min-h-[500px]">

                <!-- TAB 1: WORK ORDER -->
                <div x-show="activeTab === 'work'" class="p-5 space-y-6">

            <!-- Info Cards -->
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 mb-4">Repair Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Customer</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="repair.customer?.name || 'Walk-in'"></div>
                        <div class="text-xs text-gray-400" x-text="repair.customer?.mobile_number || ''"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Device</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="(repair.device_brand||'') + ' ' + (repair.device_model||'')"></div>
                        <div class="text-xs text-gray-400" x-text="repair.imei ? 'IMEI: ' + repair.imei : ''"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Tracking ID</div>
                        <div class="text-sm font-semibold text-primary-600" x-text="repair.tracking_id"></div>
                    </div>
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Estimated Cost</div>
                        <div class="text-sm font-semibold text-gray-800" x-text="'₹' + Number(repair.estimated_cost||0).toFixed(2)"></div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t" x-show="repair.problem_description">
                    <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Problem Description</div>
                    <div class="text-sm text-gray-700 whitespace-pre-line" x-text="repair.problem_description"></div>
                </div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 pt-3 border-t">
                    <div>
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Created</div>
                        <div class="text-sm text-gray-600" x-text="formatDateTime(repair.created_at)"></div>
                    </div>
                    <div x-show="repair.expected_delivery_date">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Expected Delivery</div>
                        <div class="text-sm text-gray-600" x-text="formatDate(repair.expected_delivery_date)"></div>
                    </div>
                    <div x-show="repair.completed_at">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Completed At</div>
                        <div class="text-sm text-gray-600" x-text="formatDateTime(repair.completed_at)"></div>
                    </div>
                </div>
            </div>



            <!-- ===== COMBINED PARTS + SERVICES (EDITABLE/READ-ONLY) ===== -->
            <template x-if="['in_progress'].includes(repair.status) && !repair.is_locked">
                <div class="bg-white rounded-xl shadow-sm border overflow-visible">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b px-5 py-4">
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 015.646 5.646 9.003 9.003 0 0020.354 15.354z"/></svg>
                            Work Items & Services
                        </h3>
                        <p class="text-xs text-gray-600 mt-1">Manage parts and services for this repair</p>
                    </div>

                    <div x-data="{ tab: 'parts' }" class="h-full">
                        <!-- Tab Buttons -->
                        <div class="flex border-b bg-gray-50 px-5">
                            <button @click="tab = 'parts'" :class="tab === 'parts' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-3 text-sm font-semibold transition-all">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0"/></svg>
                                    Parts
                                </span>
                            </button>
                            <button @click="tab = 'services'" :class="tab === 'services' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-3 text-sm font-semibold transition-all">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Services
                                </span>
                            </button>
                            <button @click="tab = 'charge'" :class="tab === 'charge' ? 'border-b-2 border-indigo-600 text-indigo-600 bg-white' : 'text-gray-500 hover:text-gray-700'" class="px-4 py-3 text-sm font-semibold transition-all">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    Our Service Fee
                                </span>
                            </button>
                        </div>

                        <!-- PARTS TAB CONTENT -->
                        <div x-show="tab === 'parts'" class="p-5 space-y-4">
                            <!-- Existing Parts -->
                            <div x-show="(repair.parts || []).length > 0">
                                <div class="mb-4">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b-2">
                                                <th class="text-left pb-3 font-semibold text-gray-700">Part Name</th>
                                                <th class="text-center pb-3 font-semibold text-gray-700">Qty</th>
                                                <th class="text-right pb-3 font-semibold text-gray-700">Unit Price</th>
                                                <th class="text-right pb-3 font-semibold text-gray-700">Total</th>
                                                <th class="pb-3"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="p in repair.parts || []" :key="p.id">
                                                <tr class="border-b hover:bg-gray-50 transition">
                                                    <td class="py-3 font-medium text-gray-800" x-text="p.part ? p.part.name : '-'"></td>
                                                    <td class="py-3 text-center text-gray-600" x-text="p.quantity"></td>
                                                    <td class="py-3 text-right text-gray-600" x-text="'₹' + Number(p.cost_price).toFixed(2)"></td>
                                                    <td class="py-3 text-right font-bold text-indigo-600" x-text="'₹' + (Number(p.cost_price) * p.quantity).toFixed(2)"></td>
                                                    <td class="py-3 text-right">
                                                        <button @click="removePart(p.id)" class="text-red-400 hover:text-red-600 transition p-1 hover:bg-red-50 rounded" title="Remove">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                                <td colspan="3" class="py-3 text-right font-bold text-gray-800">Parts Total:</td>
                                                <td class="py-3 text-right font-bold text-indigo-600 text-lg" x-text="'₹' + partsTotal().toFixed(2)"></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Add Part Form -->
                            <div x-show="repair.status === 'in_progress'" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border-2 border-dashed border-gray-300">
                                <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Add New Part</p>
                                <div class="relative mb-3">
                                    <input x-model="partSearch" @input.debounce.300ms="searchParts(1)" @focus="if(partResults.length === 0) searchParts(1)" @click.away="setTimeout(() => partResults = [], 200)" type="text" class="form-input-custom text-sm w-full" placeholder="Search parts by name...">
                                    <div x-show="partResults.length > 0 || (partSearch && partSearch.trim().length > 0 && !partLoading)" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto top-full" @scroll="handlePartScroll($event)">
                                        <template x-for="pr in partResults" :key="pr.id">
                                            <button @click="selectPart(pr)" class="w-full text-left px-3 py-2.5 hover:bg-indigo-50 text-sm border-b last:border-0 transition">
                                                <span class="font-medium text-gray-800" x-text="pr.name"></span>
                                                <span class="text-gray-400 text-xs ml-2" x-text="'₹' + Number(pr.cost_price).toFixed(2)"></span>
                                                <span class="text-gray-300 text-xs ml-1" x-text="'Stock: ' + (pr.stock_quantity || 0)"></span>
                                            </button>
                                        </template>
                                        <div x-show="partLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                        <button x-show="partSearch && partSearch.trim().length > 0 && !partLoading" @click="createAndSelectPart()" class="w-full text-left px-3 py-2.5 hover:bg-green-50 text-sm border-t transition flex items-center gap-2 text-green-700 font-semibold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                            Create "<span x-text="partSearch.trim()"></span>"
                                        </button>
                                    </div>
                                </div>
                                <div x-show="partForm.part_id" class="text-xs text-green-600 mb-2 flex items-center gap-1 px-2 py-1 bg-green-50 rounded-lg w-fit">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selected: <span class="font-semibold" x-text="partForm._name"></span>
                                    <button @click="partForm.part_id = null; partForm._name = ''" class="text-red-400 ml-1.5 hover:text-red-600">&times;</button>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <input x-model="partForm.quantity" type="number" min="1" class="form-input-custom text-sm" placeholder="Qty">
                                    <input x-model="partForm.cost_price" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Price ₹">
                                    <button @click="addPart()" class="btn-primary text-sm font-semibold">Add Part</button>
                                </div>
                            </div>
                            <div x-show="(repair.parts || []).length === 0" class="text-center py-8 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="text-sm">No parts added yet</p>
                            </div>
                        </div>

                        <!-- SERVICES TAB CONTENT -->
                        <div x-show="tab === 'services'" class="p-5 space-y-4">
                            <!-- Existing Services List -->
                            <div x-show="(repair.repair_services || []).length > 0">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="border-b-2">
                                            <th class="text-left pb-3 font-semibold text-gray-700">Service</th>
                                            <th class="text-left pb-3 font-semibold text-gray-700">Vendor</th>
                                            <th class="text-right pb-3 font-semibold text-gray-700">Charge</th>
                                            <th class="pb-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="svc in repair.repair_services || []" :key="svc.id">
                                            <tr class="border-b group hover:bg-gray-50 transition">
                                                <td class="py-3">
                                                    <div class="font-medium text-gray-800" x-text="svc.service_type_name"></div>
                                                    <div class="text-xs text-gray-400 mt-0.5" x-show="svc.description" x-text="svc.description"></div>
                                                </td>
                                                <td class="py-3 text-sm text-gray-600" x-text="svc.vendor ? svc.vendor.name : '-'"></td>
                                                <td class="py-3 text-right font-bold text-indigo-600" x-text="'₹' + Number(svc.customer_charge).toFixed(2)"></td>
                                                <td class="py-3 text-right">
                                                    <button @click="removeService(svc.id)" class="text-red-400 hover:text-red-600 transition p-1 hover:bg-red-50 rounded opacity-0 group-hover:opacity-100" title="Remove">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                            <td colspan="2" class="py-3 text-right font-bold text-gray-800">Services Total:</td>
                                            <td class="py-3 text-right font-bold text-indigo-600 text-lg" x-text="'₹' + servicesTotal().toFixed(2)"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Add Service Form -->
                            <div x-show="repair.status === 'in_progress'" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border-2 border-dashed border-gray-300">
                                <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Add New Service</p>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-3">
                                    <!-- Service Type -->
                                    <div class="relative">
                                        <input x-model="svcForm.service_type_name" @input.debounce.300ms="searchServiceTypes(1)" @focus="if(svcTypeResults.length === 0) searchServiceTypes(1)" @click.away="setTimeout(() => svcTypeResults = [], 200)" type="text" class="form-input-custom text-sm w-full" placeholder="Service type (search or custom)...">
                                        <div x-show="svcTypeResults.length > 0 || (svcForm.service_type_name && svcForm.service_type_name.trim().length > 0 && !svcTypeLoading && !svcForm.service_type_id)" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto top-full" @scroll="handleSvcTypeScroll($event)">
                                            <template x-for="st in svcTypeResults" :key="st.id">
                                                <button @click="selectServiceType(st)" class="w-full text-left px-3 py-2.5 hover:bg-indigo-50 text-sm border-b last:border-0 transition">
                                                    <span class="font-medium text-gray-800" x-text="st.name"></span>
                                                    <span class="text-gray-400 text-xs ml-2" x-show="st.default_price" x-text="'₹' + Number(st.default_price).toFixed(2)"></span>
                                                </button>
                                            </template>
                                            <div x-show="svcTypeLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                            <button x-show="svcForm.service_type_name && svcForm.service_type_name.trim().length > 0 && !svcTypeLoading && !svcForm.service_type_id" @click="createAndSelectServiceType()" class="w-full text-left px-3 py-2.5 hover:bg-green-50 text-sm border-t transition flex items-center gap-2 text-green-700 font-semibold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                Create "<span x-text="svcForm.service_type_name.trim()"></span>"
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Vendor -->
                                    <div class="relative">
                                        <input x-model="vendorSearch" @input.debounce.300ms="searchVendors(1)" @focus="if(vendorResults.length === 0) searchVendors(1)" @click.away="vendorResults = []" type="text" class="form-input-custom text-sm w-full" placeholder="Vendor (search)...">
                                        <div x-show="vendorResults.length > 0" class="absolute z-50 w-full bg-white border rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto top-full" @scroll="handleVendorScroll($event)">
                                            <template x-for="v in vendorResults" :key="v.id">
                                                <button @click="selectVendor(v)" class="w-full text-left px-3 py-2.5 hover:bg-indigo-50 text-sm border-b last:border-0 transition">
                                                    <span class="font-medium text-gray-800" x-text="v.name"></span>
                                                    <span class="text-gray-400 text-xs ml-2" x-show="v.specialization" x-text="v.specialization"></span>
                                                </button>
                                            </template>
                                            <div x-show="vendorLoading" class="px-3 py-2 text-xs text-gray-400 text-center">Loading...</div>
                                        </div>
                                        <div x-show="svcForm.vendor_id" class="text-xs text-green-600 mt-1.5 flex items-center gap-1 px-2 py-1 bg-green-50 rounded-lg w-fit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Vendor: <span class="font-semibold" x-text="svcForm._vendor_name"></span>
                                            <button @click="svcForm.vendor_id = null; svcForm._vendor_name = ''; vendorSearch = ''" class="text-red-400 ml-1 hover:text-red-600">&times;</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <input x-model="svcForm.customer_charge" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Cust. Charge ₹">
                                    <input x-model="svcForm.vendor_charge" type="number" step="0.01" class="form-input-custom text-sm" placeholder="Vendor Charge ₹">
                                    <input x-model="svcForm.reference_no" type="text" class="form-input-custom text-sm" placeholder="Ref No">
                                </div>
                                <div class="flex gap-2">
                                    <input x-model="svcForm.description" type="text" class="form-input-custom text-sm flex-1" placeholder="Description (optional)...">
                                    <button @click="addService()" class="btn-primary text-sm font-semibold whitespace-nowrap">Add Service</button>
                                </div>
                            </div>
                            <div x-show="(repair.repair_services || []).length === 0" class="text-center py-8 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="text-sm">No services added yet</p>
                            </div>
                        </div>

                        <!-- SERVICE CHARGE TAB CONTENT -->
                        <div x-show="tab === 'charge'" class="p-5 space-y-4">
                            <p class="text-xs font-semibold text-gray-600 uppercase mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Additional Service Fee
                            </p>
                            <div class="flex items-center gap-3 flex-wrap">
                                <label class="text-sm font-medium text-gray-700">Enter Charge (₹):</label>
                                <input x-model="serviceChargeInput" type="number" step="0.01" min="0" class="form-input-custom text-sm w-36" placeholder="0.00">
                                <button @click="saveServiceCharge()" class="btn-primary text-sm whitespace-nowrap">Apply Charge</button>
                                <span x-show="repair.service_charge > 0" class="text-xs text-green-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Applied
                                </span>
                            </div>
                            <div x-show="repair.service_charge > 0" class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Current Service Fee</span>
                                    <span class="text-lg font-bold text-emerald-600" x-text="'₹' + Number(repair.service_charge || 0).toFixed(2)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- PARTS READ-ONLY SUMMARY (completed/payment/closed/cancelled) -->
            <template x-if="['completed','payment','closed','cancelled'].includes(repair.status) && (repair.parts || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-visible">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b px-5 py-4">
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 015.646 5.646 9.003 9.003 0 0020.354 15.354z"/></svg>
                            Parts Used
                        </h3>
                    </div>
                    <div class="p-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2">
                                    <th class="text-left pb-3 font-semibold text-gray-700">Part Name</th>
                                    <th class="text-center pb-3 font-semibold text-gray-700">Qty</th>
                                    <th class="text-right pb-3 font-semibold text-gray-700">Unit Price</th>
                                    <th class="text-right pb-3 font-semibold text-gray-700">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="p in repair.parts || []" :key="p.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 font-medium text-gray-800" x-text="p.part ? p.part.name : '-'"></td>
                                        <td class="py-3 text-center text-gray-600" x-text="p.quantity"></td>
                                        <td class="py-3 text-right text-gray-600" x-text="'₹' + Number(p.cost_price).toFixed(2)"></td>
                                        <td class="py-3 text-right font-bold text-indigo-600" x-text="'₹' + (Number(p.cost_price) * p.quantity).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                    <td colspan="3" class="py-3 text-right font-bold text-gray-800">Total:</td>
                                    <td class="py-3 text-right font-bold text-indigo-600 text-lg" x-text="'₹' + partsTotal().toFixed(2)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </template>

            <!-- SERVICES READ-ONLY SUMMARY (completed/payment/closed/cancelled) -->
            <template x-if="['completed','payment','closed','cancelled'].includes(repair.status) && (repair.repair_services || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-visible">
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b px-5 py-4">
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Services
                        </h3>
                    </div>
                    <div class="p-5">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2">
                                    <th class="text-left pb-3 font-semibold text-gray-700">Service</th>
                                    <th class="text-left pb-3 font-semibold text-gray-700">Vendor</th>
                                    <th class="text-right pb-3 font-semibold text-gray-700">Charge</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="svc in repair.repair_services || []" :key="svc.id">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3">
                                            <div class="font-medium text-gray-800" x-text="svc.service_type_name"></div>
                                            <div class="text-xs text-gray-400 mt-0.5" x-show="svc.description" x-text="svc.description"></div>
                                        </td>
                                        <td class="py-3 text-sm text-gray-600" x-text="svc.vendor ? svc.vendor.name : '-'"></td>
                                        <td class="py-3 text-right font-bold text-indigo-600" x-text="'₹' + Number(svc.customer_charge).toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr class="bg-indigo-50 border-t-2 border-indigo-200">
                                    <td colspan="2" class="py-3 text-right font-bold text-gray-800">Total:</td>
                                    <td class="py-3 text-right font-bold text-indigo-600 text-lg" x-text="'₹' + servicesTotal().toFixed(2)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </template>







                </div> <!-- End Work Tab -->

                <!-- TAB 2: HISTORY -->
                <div x-show="activeTab === 'history'" class="p-5" x-cloak>
                    <!-- ===== STATUS HISTORY ===== -->
                    <div class="relative">
                        <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        <div class="space-y-4">
                            <template x-for="sh in (repair.status_history || []).slice().reverse()" :key="sh.id">
                                <div class="relative flex items-start gap-3 pl-8">
                                    <div class="absolute left-1.5 top-1 w-3.5 h-3.5 rounded-full border-2 border-white"
                                         :class="sh.notes && !['received','in_progress','completed','payment','closed','cancelled'].includes(sh.status)
                                                 ? 'bg-indigo-400'
                                                 : statusDotBg(sh.status)"
                                    ></div>
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <!-- Activity note (part/service log) -->
                                            <template x-if="sh.notes && sh.notes.startsWith('Part ') || sh.notes && sh.notes.startsWith('Service ')">
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    Activity
                                                </span>
                                            </template>
                                            <!-- Status change badge -->
                                            <template x-if="!(sh.notes && sh.notes.startsWith('Part ') || sh.notes && sh.notes.startsWith('Service '))">
                                                <span class="text-sm font-semibold" x-text="statusLabel(sh.status)"></span>
                                            </template>
                                            <span class="text-xs text-gray-400" x-text="formatDateTime(sh.created_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-0.5 font-medium" x-show="sh.notes" x-text="sh.notes"></p>
                                        <p class="text-xs text-gray-400" x-show="sh.updater" x-text="'by ' + (sh.updater?.name || '')"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div> <!-- End History Tab -->

            </div>
        </div>

        <!-- RIGHT COLUMN (1/3) -->
        <div class="space-y-5">

            <!-- ===== REPAIR OPERATIONS PANEL (Compact) ===== -->
            <template x-if="!repair.is_locked && repair.status !== 'cancelled'">
                <div class="bg-white rounded-xl shadow-sm border overflow-visible">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-3 flex items-center justify-between">
                        <h3 class="font-bold text-white text-xs uppercase tracking-wider">Repair Operations</h3>
                        <svg class="w-4 h-4 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>

                    <!-- Content -->
                    <div class="p-4 space-y-3">
                        <!-- Status Transitions -->
                        <div>
                            <label class="text-xs font-bold text-gray-600 uppercase block mb-2.5">Change Status</label>
                            <div class="space-y-2">
                                <template x-for="nextStatus in (repair.status === 'payment'
                                        ? (balanceDue() > 0
                                            ? (repair.allowed_transitions || []).filter(s => s === 'cancelled')
                                            : (repair.allowed_transitions || []).filter(s => ['closed', 'cancelled'].includes(s)))
                                        : (repair.allowed_transitions || []))" :key="nextStatus">
                                    <button @click="nextStatus === 'cancelled' ? (showCancel = true) : handleStatusTransition(nextStatus)" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all hover:scale-[1.02] hover:shadow-md" :class="statusTransitionBtnClass(nextStatus)">
                                        <template x-if="nextStatus === 'in_progress'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></template>
                                        <template x-if="nextStatus === 'completed'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></template>
                                        <template x-if="nextStatus === 'payment'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></template>
                                        <template x-if="nextStatus === 'closed'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg></template>
                                        <template x-if="nextStatus === 'cancelled'"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></template>
                                        <span x-text="statusLabel(nextStatus)"></span>
                                    </button>
                                </template>
                            </div>
                            <!-- Inline notes for status change -->
                            <div x-show="pendingTransition" class="mt-3 rounded-xl border border-blue-200 bg-blue-50 overflow-hidden" x-cloak>
                                <!-- Header bar -->
                                <div class="bg-blue-100 px-4 py-2.5 flex items-center gap-2 border-b border-blue-200">
                                    <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span class="text-xs font-bold text-blue-800 uppercase tracking-wide" x-text="'Confirm: ' + statusLabel(pendingTransition)"></span>
                                </div>
                                <!-- Body -->
                                <div class="p-4 space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-blue-700 mb-1.5">Notes <span class="font-normal text-blue-500">(optional)</span></label>
                                        <input x-model="statusForm.notes" type="text" class="form-input-custom text-sm w-full" placeholder="Add a note…">
                                    </div>
                                    <template x-if="pendingTransition === 'cancelled'">
                                        <div>
                                            <label class="block text-xs font-semibold text-red-700 mb-1.5">Cancellation Reason <span class="text-red-500">*</span></label>
                                            <input x-model="statusForm.cancel_reason" type="text" class="form-input-custom text-sm w-full" placeholder="Why is this being cancelled?">
                                        </div>
                                    </template>
                                    <div class="flex gap-2 pt-1">
                                        <button @click="confirmStatusChange()"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg text-sm font-bold text-white transition-all hover:opacity-90"
                                            style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Confirm
                                        </button>
                                        <button @click="pendingTransition = null"
                                            class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 rounded-lg text-sm font-semibold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Actions -->
                        <div x-show="repair.is_fully_paid && repair.has_returnable_items" class="border-t border-gray-200 pt-3">
                            <label class="text-xs font-bold text-gray-600 uppercase block mb-2.5">Actions</label>
                            <a :href="'/admin/repairs/' + repair.id + '/returns/create'" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-orange-50 text-orange-700 hover:bg-orange-100 border border-orange-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                <span>Return Items</span>
                            </a>
                        </div>
                        <div x-show="(repair.repair_returns || []).length > 0" class="border-t border-gray-200 pt-3">
                            <label class="text-xs font-bold text-gray-600 uppercase block mb-2.5">Returns</label>
                            <a @click.prevent="document.getElementById('returns-section')?.scrollIntoView({behavior:'smooth'})" href="#returns-section" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                <span>View Returns</span>
                                <span class="ml-auto text-xs font-bold bg-orange-100 text-orange-700 px-1.5 py-0.5 rounded-full" x-text="(repair.repair_returns || []).length"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </template>

            <!-- ===== FINANCIAL SUMMARY (Enhanced Design) ===== -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden z-10">
                <!-- Header -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b px-5 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Financial Summary
                        </h3>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700" x-text="repair.ticket_number"></span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-5 space-y-3">
                    <!-- Line Items -->
                    <div class="space-y-2.5">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 font-medium">Parts Cost</span>
                            <span class="text-sm font-bold text-gray-800" x-text="'₹' + partsTotal().toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 font-medium">Other Services</span>
                            <span class="text-sm font-bold text-gray-800" x-text="'₹' + servicesTotal().toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 font-medium">Our Service Fee</span>
                            <span class="text-sm font-bold text-gray-800" x-text="'₹' + Number(repair.service_charge || 0).toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-2 border-gray-200 my-3"></div>

                    <!-- Grand Total -->
                    <div class="bg-gradient-to-r from-indigo-50 to-blue-50 rounded-lg p-3 border border-indigo-200">
                        <div class="flex justify-between items-center">
                            <span class="font-bold uppercase text-sm text-gray-800">Grand Total</span>
                            <span class="text-xl font-bold text-indigo-600" x-text="'₹' + grandTotal().toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Payment Breakdown -->
                    <div class="space-y-2">
                        <!-- Advance Paid -->
                        <template x-if="advancePaid() > 0">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
                                    Advance Paid
                                </span>
                                <span class="font-bold text-amber-600" x-text="'₹' + advancePaid().toFixed(2)"></span>
                            </div>
                        </template>
                        <!-- Final Paid -->
                        <template x-if="finalPaid() > 0">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600 flex items-center gap-1">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
                                    Final Paid
                                </span>
                                <span class="font-bold text-green-600" x-text="'₹' + finalPaid().toFixed(2)"></span>
                            </div>
                        </template>
                        <!-- Net Total Paid -->
                        <div class="flex justify-between items-center text-sm pt-1 border-t border-gray-100">
                            <span class="text-gray-700 font-semibold">Total Paid</span>
                            <span class="font-bold text-indigo-600" x-text="'₹' + totalPaid().toFixed(2)"></span>
                        </div>
                        <div class="flex justify-between items-center text-sm" x-show="totalRefunded() > 0">
                            <span class="text-gray-600">Amount Refunded</span>
                            <span class="font-bold text-red-600" x-text="'₹' + totalRefunded().toFixed(2)"></span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-2 border-gray-200 my-3"></div>

                    <!-- Balance Due -->
                    <div x-show="grandTotal() > 0" :class="balanceDue() > 0 ? 'bg-gradient-to-br from-red-50 to-orange-50 border border-red-200' : 'bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200'">
                        <div class="rounded-lg p-3">
                            <div class="flex justify-between items-center">
                                <span class="font-bold uppercase text-sm" :class="balanceDue() > 0 ? 'text-red-800' : 'text-green-800'">Balance Due</span>
                                <span class="text-xl font-bold" :class="balanceDue() > 0 ? 'text-red-600' : 'text-green-600'" x-text="'₹' + balanceDue().toFixed(2)"></span>
                            </div>
                        </div>
                    </div>

                    <!-- ===== PAYMENT COLLECTION (payment status) ===== -->
                    <template x-if="repair.status === 'payment' && balanceDue() > 0">
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <!-- Attention Banner -->
                            <div class="mb-3 flex items-center gap-2 bg-amber-50 border border-amber-300 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                                <p class="text-xs font-bold text-amber-700">Payment required to close this repair</p>
                            </div>
                            <p class="text-xs font-semibold text-gray-600 uppercase mb-3">Collect Payment</p>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-xs text-gray-600 mb-1.5 block font-medium">Amount (₹)</label>
                                    <input x-model="payForm.amount" type="number" step="0.01" class="form-input-custom text-sm w-full" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600 mb-1.5 block font-medium">Payment Method</label>
                                    <select x-model="payForm.payment_method" class="form-select-custom text-sm w-full">
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank">Bank</option>
                                    </select>
                                </div>
                                <!-- Highlighted Process Payment Button -->
                                <button @click="collectPayment()"
                                    style="display:block;width:100%;margin-top:0.5rem;padding:0.65rem 1rem;background:linear-gradient(135deg,#10b981,#059669);color:#ffffff;font-weight:700;font-size:0.875rem;border:none;border-radius:0.5rem;cursor:pointer;box-shadow:0 4px 14px rgba(16,185,129,0.4);animation:paymentPulse 2s ease-in-out infinite;">
                                    <span style="display:flex;align-items:center;justify-content:center;gap:0.5rem;">
                                        <svg style="width:1rem;height:1rem;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Process Payment
                                    </span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Fully Paid Indicator -->
                    <template x-if="repair.status === 'payment' && balanceDue() <= 0 && grandTotal() > 0">
                        <div class="pt-3 border-t border-gray-200 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-600 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="font-bold text-green-700 text-sm">Fully Paid!</p>
                            <p class="text-xs text-gray-500 mt-1">Ready to be closed.</p>
                        </div>
                    </template>
                    <!-- Zero-cost repair summary -->
                    <template x-if="repair.status === 'payment' && grandTotal() === 0">
                        <div class="pt-3 border-t border-gray-200 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 mb-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="font-bold text-blue-700 text-sm">No payment required</p>
                            <p class="text-xs text-gray-500 mt-1">Grand total is ₹0. Close this repair when ready.</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ===== CHILD REPAIRS ===== -->
            <template x-if="(repair.child_repairs || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-visible">
                    <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Related Repairs</h3></div>
                    <div class="p-3">
                        <template x-for="child in repair.child_repairs" :key="child.id">
                            <a :href="'/admin/repairs/' + child.id" class="flex items-center justify-between py-2.5 border-b last:border-0 text-sm hover:bg-gray-50 rounded px-2 -mx-2 transition">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-primary-600" x-text="child.ticket_number"></span>
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-blue-100 text-blue-600" x-text="child.record_type"></span>
                                </div>
                                <span class="text-xs text-gray-400" x-text="formatDate(child.created_at)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ===== RETURNS ===== -->
            <template x-if="(repair.repair_returns || []).length > 0">
                <div id="returns-section" class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-orange-50 px-4 py-3 border-b flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            <h3 class="font-semibold text-sm text-orange-700">Returns</h3>
                        </div>
                        <template x-if="repair.return_status === 'partial'">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-amber-100 text-amber-700">Partial</span>
                        </template>
                        <template x-if="repair.return_status === 'fully_returned'">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700">Fully Returned</span>
                        </template>
                    </div>
                    <div class="p-3">
                        <template x-for="ret in repair.repair_returns" :key="ret.id">
                            <a :href="'/admin/repairs/' + repair.id + '/returns/' + ret.id" class="flex items-center justify-between py-2.5 border-b last:border-0 text-sm hover:bg-gray-50 rounded px-2 -mx-2 transition">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-orange-600" x-text="ret.return_number"></span>
                                    <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded"
                                        :class="{
                                            'bg-gray-100 text-gray-600': ret.status === 'draft',
                                            'bg-blue-100 text-blue-600': ret.status === 'confirmed',
                                            'bg-green-100 text-green-600': ret.status === 'refunded'
                                        }"
                                        x-text="ret.status"></span>
                                    <span class="text-sm font-semibold text-gray-700" x-text="'₹' + Number(ret.total_return_amount).toFixed(2)"></span>
                                </div>
                                <span class="text-xs text-gray-400" x-text="formatDate(ret.created_at)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- ===== PAYMENT HISTORY ===== -->
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Payments</h3></div>
                <div class="p-4">
                    <template x-if="(repair.payments || []).length > 0">
                        <div class="space-y-0">
                            <template x-for="p in repair.payments" :key="p.id">
                                <div class="flex items-center justify-between text-sm py-2.5 border-b last:border-0">
                                    <div>
                                        <span class="font-medium capitalize" x-text="p.payment_type"></span>
                                        <span class="text-gray-400 text-xs">via</span>
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-1.5 py-0.5 rounded-full" :class="{'bg-green-100 text-green-700': p.payment_method === 'cash', 'bg-blue-100 text-blue-700': p.payment_method === 'upi', 'bg-purple-100 text-purple-700': p.payment_method === 'card', 'bg-gray-100 text-gray-700': p.payment_method === 'bank_transfer'}">
                                            <template x-if="p.payment_method === 'cash'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg></template>
                                            <template x-if="p.payment_method === 'upi'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg></template>
                                            <template x-if="p.payment_method === 'card'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></template>
                                            <template x-if="p.payment_method === 'bank_transfer'"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></template>
                                            <span x-text="p.payment_method === 'bank_transfer' ? 'Bank' : (p.payment_method || '').toUpperCase()"></span>
                                        </span>
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-text="formatDate(p.created_at)"></div>
                                    </div>
                                    <div class="font-semibold" :class="p.direction === 'OUT' ? 'text-red-600' : 'text-green-600'" x-text="(p.direction === 'OUT' ? '-' : '+') + '₹' + Number(p.amount).toFixed(2)"></div>
                                </div>
                            </template>
                            <div class="pt-3 mt-2 border-t flex items-center justify-between text-sm font-bold">
                                <span>Net Paid</span>
                                <span class="text-primary-600" x-text="'₹' + (totalPaid() - totalRefunded()).toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="(repair.payments || []).length === 0">
                        <p class="text-sm text-gray-400 text-center py-4">No payments yet</p>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== CANCEL & REFUND MODAL ===== -->
    <div x-show="showCancel" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showCancel = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-red-700">
                    <span x-show="repair.net_paid > 0">Cancel & Refund</span>
                    <span x-show="repair.net_paid <= 0">Cancel Repair</span>
                </h3>
                <button @click="showCancel = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Refund Flow (if payment exists) -->
                <template x-if="repair.net_paid > 0">
                    <div>
                        <div class="bg-red-50 rounded-lg p-3 mb-4 text-sm text-red-700">
                            <p class="font-medium">This will cancel the repair and refund the advance payment.</p>
                            <p class="mt-1 font-bold text-red-800">Amount to refund: ₹<span x-text="Number(repair.net_paid).toFixed(2)"></span></p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                <textarea x-model="cancelForm.reason" class="form-input-custom" rows="2" placeholder="Why is this being cancelled?"></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Refund Method</label>
                                <select x-model="cancelForm.refund_method" class="form-select-custom w-full">
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="upi">UPI</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <!-- Parts handling -->
                            <template x-if="(repair.parts || []).length > 0">
                                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3">
                                    <label class="block text-sm font-medium text-amber-800 mb-2 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0"/></svg>
                                        Parts Used (<span x-text="(repair.parts || []).length"></span>) — Total: ₹<span x-text="partsTotal().toFixed(2)"></span>
                                    </label>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-amber-100 transition" :class="cancelForm.parts_action === 'return_stock' ? 'bg-amber-100 ring-1 ring-amber-400' : ''">
                                            <input type="radio" x-model="cancelForm.parts_action" value="return_stock" class="text-amber-600">
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Return to stock</span>
                                                <p class="text-xs text-gray-500">Parts are fine, add back to inventory</p>
                                            </div>
                                        </label>
                                        <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer hover:bg-amber-100 transition" :class="cancelForm.parts_action === 'write_off' ? 'bg-amber-100 ring-1 ring-amber-400' : ''">
                                            <input type="radio" x-model="cancelForm.parts_action" value="write_off" class="text-amber-600">
                                            <div>
                                                <span class="text-sm font-medium text-gray-800">Write off as loss</span>
                                                <p class="text-xs text-gray-500">Parts are damaged/used</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Direct cancel flow (if no payment) -->
                <template x-if="repair.net_paid <= 0">
                    <div>
                        <div>
                            <div class="bg-red-50 rounded-lg p-3 mb-4 text-sm text-red-700">
                                <p class="font-medium">This will cancel the repair.</p>
                                <p class="mt-1 text-xs">No advance payment to refund.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                                <textarea x-model="cancelForm.reason" class="form-input-custom" rows="2" placeholder="Why is this being cancelled?"></textarea>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="modal-footer">
                <button @click="showCancel = false" class="btn-secondary">Go Back</button>
                <button @click="handleCancel()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                    <span x-show="repair.net_paid > 0">Cancel & Refund</span>
                    <span x-show="repair.net_paid <= 0">Cancel Repair</span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="showEditModal" class="modal-overlay" x-cloak>
        <div class="modal-container modal-xl" @click.away="showEditModal = false">
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Repair Intake</h3>
                    <p class="text-sm text-slate-500 mt-1">Update the intake details without leaving the repair workflow.</p>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body max-h-[75vh] overflow-y-auto space-y-5">
                <div class="rounded-3xl border border-indigo-100 bg-[linear-gradient(135deg,#eef2ff,#ffffff)] p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-600 text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-slate-900">Customer</h4>
                            <p class="text-sm text-slate-500">Search and switch the customer if the intake was assigned to the wrong person.</p>
                        </div>
                    </div>

                    <div x-show="editForm.customer_id" class="mb-4 inline-flex max-w-full items-center gap-2 rounded-2xl border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm font-medium text-indigo-800">
                        <svg class="w-4 h-4 shrink-0 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="truncate" x-text="editSelectedCustomer?.name + (editSelectedCustomer?.mobile_number ? ' · ' + editSelectedCustomer.mobile_number : '')"></span>
                        <button type="button" @click="editForm.customer_id = null; editSelectedCustomer = null; editCustSearch = ''" class="text-indigo-400 hover:text-red-500 text-lg leading-none">&times;</button>
                    </div>

                    <div class="relative" @click.away="editCustOpen = false; editCustResults = []">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input x-model="editCustSearch" @focus="searchEditCustomers(1)" @input.debounce.300ms="searchEditCustomers(1)" type="text" class="form-input-custom pl-9 text-sm w-full" placeholder="Search by customer name or mobile">
                        <div x-show="editCustOpen && editCustResults.length > 0" x-cloak class="absolute z-50 mt-1 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                            <div class="max-h-64 overflow-y-auto" @scroll="handleEditCustScroll($event)">
                                <template x-for="customer in editCustResults" :key="customer.id">
                                    <button type="button" @click="selectEditCustomer(customer)" class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 text-left hover:bg-indigo-50 transition">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-800" x-text="customer.name"></div>
                                            <div class="text-xs text-slate-400" x-text="customer.mobile_number || ''"></div>
                                        </div>
                                        <svg class="w-4 h-4 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                </template>
                                <div x-show="editCustLoading" class="px-4 py-3 text-center text-xs text-slate-400">Loading…</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div class="rounded-3xl border border-emerald-100 bg-[linear-gradient(135deg,#ecfdf5,#ffffff)] p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-600 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Device identity</h4>
                                <p class="text-sm text-slate-500">Brand and model are required for every repair now.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div x-data="brandDropdown(brandList, (v) => editForm.device_brand = v)" x-effect="syncValue(editForm.device_brand)" @click.outside="open = false" class="relative">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Device Brand <span class="text-red-500">*</span></label>
                                <input type="text" x-model="query" @focus="open = true" @input="open = true; selected = query; updateValue(query)" @keydown.arrow-down.prevent="highlightNext()" @keydown.arrow-up.prevent="highlightPrev()" @keydown.enter.prevent="selectHighlighted()" @keydown.escape="open = false" class="form-input-custom w-full text-sm" placeholder="Type to search brands..." autocomplete="off">
                                <div x-show="open && filtered.length > 0" x-cloak class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                                    <template x-for="(brand, idx) in filtered" :key="brand">
                                        <div @click="pick(brand)" :class="idx === highlighted ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50'" class="cursor-pointer px-3 py-2 text-sm" x-text="brand"></div>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Device Model <span class="text-red-500">*</span></label>
                                <input x-model="editForm.device_model" type="text" class="form-input-custom w-full text-sm" placeholder="Galaxy S24, iPhone 15">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">IMEI / Serial No.</label>
                                <input x-model="editForm.imei" type="text" class="form-input-custom w-full text-sm" placeholder="Optional serial or IMEI reference">
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-amber-100 bg-[linear-gradient(135deg,#fff7ed,#ffffff)] p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500 text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-semibold text-slate-900">Issue summary</h4>
                                <p class="text-sm text-slate-500">Keep the intake description accurate for customer updates.</p>
                            </div>
                        </div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">Problem Description <span class="text-red-500">*</span></label>
                        <textarea x-model="editForm.problem_description" class="form-input-custom w-full text-sm" rows="9" placeholder="Describe the issue clearly"></textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Estimated Cost</label>
                        <input x-model="editForm.estimated_cost" type="number" step="0.01" class="form-input-custom w-full text-sm" placeholder="0.00">
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Expected Delivery Date</label>
                        <input x-model="editForm.expected_delivery_date" type="date" class="form-input-custom w-full text-sm">
                    </div>
                </div>
            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-end">
                <button @click="showEditModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="saveRepairDetails()" class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2">
                    <span x-show="savingEdit" class="spinner"></span>
                    <span x-text="savingEdit ? 'Saving...' : 'Save Intake Changes'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
    const brandList = @json($brands);
    console.log(brandList,'brandList');

    function brandDropdown(brands, onChange) {
        return {
            open: false,
            query: '',
            selected: '',
            highlighted: -1,
            brands: brands,
            filtered: [],
            init() {
                this.filtered = this.brands ? this.brands.slice() : [];
                this.$watch('query', (val) => {
                    const q = val.trim().toLowerCase();
                    this.filtered = q ? this.brands.filter(b => b.toLowerCase().includes(q)) : this.brands.slice();
                });
            },
            syncValue(val) {
                if ((val || '') !== this.selected) {
                    this.query = val || '';
                    this.selected = val || '';
                }
            },
            pick(brand) {
                this.query = brand;
                this.selected = brand;
                this.open = false;
                this.highlighted = -1;
                onChange(brand);
            },
            updateValue(val) { onChange(val); },
            highlightNext() {
                if (this.filtered.length === 0) return;
                this.highlighted = (this.highlighted + 1) % this.filtered.length;
                this.scrollToHighlighted();
            },
            highlightPrev() {
                if (this.filtered.length === 0) return;
                this.highlighted = this.highlighted <= 0 ? this.filtered.length - 1 : this.highlighted - 1;
                this.scrollToHighlighted();
            },
            selectHighlighted() {
                if (this.highlighted >= 0 && this.highlighted < this.filtered.length) {
                    this.pick(this.filtered[this.highlighted]);
                }
            },
            scrollToHighlighted() {
                this.$nextTick(() => {
                    const container = this.$el.querySelector('.overflow-y-auto');
                    const item = container?.children[this.highlighted];
                    if (item) item.scrollIntoView({ block: 'nearest' });
                });
            }
        };
    }
    </script>

    <!-- ===== COMPLETED CONFIRMATION MODAL ===== -->
    <div x-show="showCompletedConfirm" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-md" @click.away="showCompletedConfirm = false">
            <div class="modal-header">
                <h3 class="text-lg font-bold text-emerald-700">Confirm Repair Completed</h3>
                <button @click="showCompletedConfirm = false" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="modal-body">
                <div class="bg-emerald-50 rounded-lg p-4 mb-4 text-center">
                    <svg class="w-12 h-12 text-emerald-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-semibold text-emerald-800">Mark this repair as completed?</p>
                    <p class="text-sm text-emerald-600 mt-1">Once confirmed, you cannot change it back to in-progress.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <input x-model="statusForm.notes" type="text" class="form-input-custom text-sm" placeholder="Completion notes...">
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showCompletedConfirm = false" class="btn-secondary">Go Back</button>
                <button @click="confirmCompleted()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Yes, Mark Completed</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function repairDetail() {
    return {
        repair: @json($repair),
        statusMeta: @json($statusMeta),
        activeTab: 'work',

        // Progress steps
        progressSteps: [
            { key: 'received', label: 'Received' },
            { key: 'in_progress', label: 'In Progress' },
            { key: 'completed', label: 'Completed' },
            { key: 'payment', label: 'Payment' },
            { key: 'closed', label: 'Closed' },
        ],

        // Modals
        showCancel: false,
        showCompletedConfirm: false,
        showEditModal: false,
        savingEdit: false,

        // Status
        statusForm: { status: '', notes: '', cancel_reason: '', confirm: false },
        pendingTransition: null,

        editForm: { customer_id: '', device_brand: '', device_model: '', imei: '', problem_description: '', estimated_cost: '', expected_delivery_date: '' },
        editSelectedCustomer: null,
        editCustSearch: '', editCustResults: [], editCustOpen: false, editCustHasMore: false, editCustPage: 1, editCustLoading: false,

        // Parts
        partForm: { part_id: null, _name: '', quantity: 1, cost_price: '' },
        partSearch: '', partResults: [], partHasMore: false, partPage: 1, partLoading: false,

        // Payment
        payForm: { payment_type: 'final', payment_method: 'cash', amount: '' },

        // Service charge
        serviceChargeInput: '',

        // Services
        svcForm: { service_type_id: null, service_type_name: '', vendor_id: null, _vendor_name: '', customer_charge: '', vendor_charge: '', reference_no: '', description: '' },
        svcTypeResults: [], svcTypeHasMore: false, svcTypePage: 1, svcTypeLoading: false,
        vendorSearch: '', vendorResults: [], vendorHasMore: false, vendorPage: 1, vendorLoading: false,

        // Cancel form (handles refund and simple cancel)
        cancelForm: { reason: '', refund_method: 'cash', parts_action: 'return_stock' },

        init() {
            this.serviceChargeInput = this.repair.service_charge || '';
            if (this.repair.status === 'payment' && this.balanceDue() > 0) {
                this.payForm.amount = this.balanceDue().toFixed(2);
            }
            this.syncEditForm();
        },

        // Reload repair data from server
        async reload() {
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id);
            if (r.data) {
                this.repair = r.data;
                this.serviceChargeInput = this.repair.service_charge || '';
                if (this.repair.status === 'payment' && this.balanceDue() > 0) {
                    this.payForm.amount = this.balanceDue().toFixed(2);
                }
                this.syncEditForm();
            }
        },

        syncEditForm() {
            this.editForm = {
                customer_id: this.repair.customer_id || '',
                device_brand: this.repair.device_brand || '',
                device_model: this.repair.device_model || '',
                imei: this.repair.imei || '',
                problem_description: this.repair.problem_description || '',
                estimated_cost: this.repair.estimated_cost || '',
                expected_delivery_date: this.repair.expected_delivery_date ? String(this.repair.expected_delivery_date).substring(0, 10) : '',
            };
            this.editSelectedCustomer = this.repair.customer || null;
            this.editCustSearch = '';
            this.editCustResults = [];
            this.editCustOpen = false;
        },

        openEditModal() {
            this.syncEditForm();
            this.showEditModal = true;
        },

        async searchEditCustomers(page) {
            page = page || 1;
            if (page === 1) this.editCustPage = 1;
            this.editCustLoading = true;
            const r = await RepairBox.ajax('/admin/customers-search?page=' + page + '&q=' + encodeURIComponent(this.editCustSearch || ''));
            this.editCustLoading = false;
            const rows = Array.isArray(r.data) ? r.data : [];
            this.editCustResults = page === 1 ? rows : this.editCustResults.concat(rows);
            this.editCustHasMore = r.has_more || false;
            this.editCustPage = page;
            this.editCustOpen = true;
        },

        handleEditCustScroll(event) {
            const element = event.target;
            if (element.scrollTop + element.clientHeight >= element.scrollHeight - 10 && this.editCustHasMore && !this.editCustLoading) {
                this.searchEditCustomers(this.editCustPage + 1);
            }
        },

        selectEditCustomer(customer) {
            this.editSelectedCustomer = customer;
            this.editForm.customer_id = customer.id;
            this.editCustSearch = '';
            this.editCustResults = [];
            this.editCustOpen = false;
        },

        async saveRepairDetails() {
            if (!this.editForm.customer_id) { RepairBox.toast('Customer is required', 'error'); return; }
            if (!this.editForm.device_brand) { RepairBox.toast('Device brand is required', 'error'); return; }
            if (!this.editForm.device_model) { RepairBox.toast('Device model is required', 'error'); return; }
            if (!this.editForm.problem_description) { RepairBox.toast('Problem description is required', 'error'); return; }

            this.savingEdit = true;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id, 'PUT', this.editForm);
            this.savingEdit = false;

            if (r.success !== false) {
                RepairBox.toast('Repair intake updated', 'success');
                this.showEditModal = false;
                await this.reload();
            }
        },

        // ===== STATUS HELPERS =====
        statusLabel(status) {
            return this.statusMeta[status]?.label || status?.replace('_', ' ') || '';
        },
        statusBadgeClass(status) {
            const map = { received: 'bg-blue-100 text-blue-700', in_progress: 'bg-amber-100 text-amber-700', completed: 'bg-emerald-100 text-emerald-700', payment: 'bg-purple-100 text-purple-700', closed: 'bg-green-100 text-green-800', cancelled: 'bg-red-100 text-red-700' };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
        statusTransitionBtnClass(status) {
            const map = { in_progress: 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm', completed: 'bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm', payment: 'bg-purple-500 hover:bg-purple-600 text-white shadow-sm', closed: 'bg-green-600 hover:bg-green-700 text-white shadow-sm', cancelled: 'bg-red-100 hover:bg-red-200 text-red-700 border border-red-200' };
            return map[status] || 'bg-gray-500 hover:bg-gray-600 text-white';
        },
        statusDotCurrent(status) {
            const map = { received: 'bg-blue-500 border-blue-500 text-white ring-2 ring-blue-200', in_progress: 'bg-amber-500 border-amber-500 text-white ring-2 ring-amber-200', completed: 'bg-emerald-500 border-emerald-500 text-white ring-2 ring-emerald-200', payment: 'bg-purple-500 border-purple-500 text-white ring-2 ring-purple-200', closed: 'bg-green-600 border-green-600 text-white ring-2 ring-green-200' };
            return map[status] || 'bg-primary-600 border-primary-600 text-white ring-2 ring-primary-200';
        },
        statusDotBg(status) {
            const map = { received: 'bg-blue-500', in_progress: 'bg-amber-500', completed: 'bg-emerald-500', payment: 'bg-purple-500', closed: 'bg-green-600', cancelled: 'bg-red-500' };
            return map[status] || 'bg-gray-400';
        },
        stepReached(current, step) {
            const order = ['received', 'in_progress', 'completed', 'payment', 'closed'];
            return order.indexOf(current) >= order.indexOf(step);
        },

        // ===== DATE FORMATTING =====
        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }); },
        formatDateTime(d) { if (!d) return ''; return new Date(d).toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }); },

        // ===== STATUS TRANSITIONS =====
        handleStatusTransition(nextStatus) {
            if (nextStatus === 'completed') { this.statusForm.notes = ''; this.showCompletedConfirm = true; return; }
            this.pendingTransition = nextStatus;
            this.statusForm = { status: nextStatus, notes: '', cancel_reason: '', confirm: false };
        },
        async confirmStatusChange() {
            const status = this.pendingTransition;
            if (!status) return;
            if (status === 'cancelled' && !this.statusForm.cancel_reason) { RepairBox.toast('Please provide a cancellation reason', 'error'); return; }
            this.statusForm.status = status;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/status', 'PUT', this.statusForm);
            if (r.success !== false) {
                RepairBox.toast('Status updated to ' + this.statusLabel(status), 'success');
                this.pendingTransition = null;
                await this.reload();
            }
        },
        async confirmCompleted() {
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/status', 'PUT', { status: 'completed', notes: this.statusForm.notes || 'Repair completed', confirm: true });
            if (r.success !== false) {
                RepairBox.toast('Repair marked as completed', 'success');
                this.showCompletedConfirm = false;
                await this.reload();
            }
        },

        // ===== PARTS =====
        async searchParts(page) {
            page = page || 1;
            if (page === 1) { this.partResults = []; this.partPage = 1; }
            this.partLoading = true;
            const r = await RepairBox.ajax('/admin/parts-search?q=' + encodeURIComponent(this.partSearch || '') + '&page=' + page);
            this.partLoading = false;
            if (r.data) {
                this.partResults = page === 1 ? r.data : this.partResults.concat(r.data);
                this.partHasMore = r.has_more || false;
                this.partPage = page;
            }
        },
        handlePartScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.partHasMore && !this.partLoading) {
                this.searchParts(this.partPage + 1);
            }
        },
        selectPart(pr) {
            this.partForm.part_id = pr.id;
            this.partForm._name = pr.name;
            this.partForm.cost_price = pr.cost_price || '';
            this.partResults = [];
            this.partSearch = '';
            this.partHasMore = false;
        },
        async createAndSelectPart() {
            const name = (this.partSearch || '').trim();
            if (!name) return;
            const r = await RepairBox.ajax('/admin/parts', 'POST', { name: name, sku: '', cost_price: 0, selling_price: 0 });
            if (r.success !== false && r.data) {
                RepairBox.toast('Part "' + name + '" created', 'success');
                this.selectPart(r.data);
            }
        },
        async addPart() {
            if (!this.partForm.part_id) { RepairBox.toast('Please search & select a part', 'error'); return; }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/parts', 'POST', { part_id: this.partForm.part_id, quantity: this.partForm.quantity, cost_price: this.partForm.cost_price });
            if (r.success !== false) {
                RepairBox.toast('Part added', 'success');
                this.partForm = { part_id: null, _name: '', quantity: 1, cost_price: '' };
                await this.reload();
            }
        },
        async removePart(partId) {
            if (!confirm('Remove this part?')) return;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/parts/' + partId, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Part removed', 'success'); await this.reload(); }
        },

        // ===== SERVICES =====
        async searchServiceTypes(page) {
            page = page || 1;
            if (page === 1) { this.svcTypeResults = []; this.svcTypePage = 1; }
            this.svcTypeLoading = true;
            const r = await RepairBox.ajax('/admin/service-types-search?q=' + encodeURIComponent(this.svcForm.service_type_name || '') + '&page=' + page);
            this.svcTypeLoading = false;
            if (r.data) {
                this.svcTypeResults = page === 1 ? r.data : this.svcTypeResults.concat(r.data);
                this.svcTypeHasMore = r.has_more || false;
                this.svcTypePage = page;
            }
        },
        handleSvcTypeScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.svcTypeHasMore && !this.svcTypeLoading) {
                this.searchServiceTypes(this.svcTypePage + 1);
            }
        },
        selectServiceType(st) {
            this.svcForm.service_type_id = st.id;
            this.svcForm.service_type_name = st.name;
            if (st.default_price) this.svcForm.customer_charge = st.default_price;
            this.svcTypeResults = [];
            this.svcTypeHasMore = false;
        },
        async createAndSelectServiceType() {
            const name = (this.svcForm.service_type_name || '').trim();
            if (!name) return;
            // If an existing result matches exactly, select it instead of creating a duplicate
            const existing = this.svcTypeResults.find(st => st.name.toLowerCase() === name.toLowerCase());
            if (existing) {
                this.selectServiceType(existing);
                return;
            }
            const r = await RepairBox.ajax('/admin/service-types', 'POST', { name: name });
            if (r.success !== false && r.data) {
                const msg = r.data._existing ? 'Service type "' + name + '" already exists — selected' : 'Service type "' + name + '" created';
                RepairBox.toast(msg, 'success');
                this.selectServiceType(r.data);
            }
        },
        async searchVendors(page) {
            page = page || 1;
            if (page === 1) { this.vendorResults = []; this.vendorPage = 1; }
            this.vendorLoading = true;
            const r = await RepairBox.ajax('/admin/vendors-search?q=' + encodeURIComponent(this.vendorSearch || '') + '&page=' + page);
            this.vendorLoading = false;
            if (r.data) {
                this.vendorResults = page === 1 ? r.data : this.vendorResults.concat(r.data);
                this.vendorHasMore = r.has_more || false;
                this.vendorPage = page;
            }
        },
        handleVendorScroll(e) {
            const el = e.target;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 10 && this.vendorHasMore && !this.vendorLoading) {
                this.searchVendors(this.vendorPage + 1);
            }
        },
        selectVendor(v) {
            this.svcForm.vendor_id = v.id;
            this.svcForm._vendor_name = v.name;
            this.vendorResults = [];
            this.vendorSearch = '';
            this.vendorHasMore = false;
        },
        async addService() {
            if (!this.svcForm.service_type_name) { RepairBox.toast('Please enter a service type', 'error'); return; }
            if (!this.svcForm.customer_charge || Number(this.svcForm.customer_charge) < 0) { RepairBox.toast('Please enter customer charge', 'error'); return; }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/services', 'POST', {
                service_type_id: this.svcForm.service_type_id,
                service_type_name: this.svcForm.service_type_name,
                vendor_id: this.svcForm.vendor_id,
                customer_charge: this.svcForm.customer_charge,
                vendor_charge: this.svcForm.vendor_charge || 0,
                reference_no: this.svcForm.reference_no,
                description: this.svcForm.description,
            });
            if (r.success !== false) {
                RepairBox.toast('Service added', 'success');
                this.svcForm = { service_type_id: null, service_type_name: '', vendor_id: null, _vendor_name: '', customer_charge: '', vendor_charge: '', reference_no: '', description: '' };
                await this.reload();
            }
        },
        async removeService(serviceId) {
            if (!confirm('Remove this service?')) return;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/services/' + serviceId, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Service removed', 'success'); await this.reload(); }
        },


        // ===== SERVICE CHARGE =====
        async saveServiceCharge() {
            if (this.serviceChargeInput === '' || Number(this.serviceChargeInput) < 0) { RepairBox.toast('Enter valid service charge', 'error'); return; }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/service-charge', 'PUT', { service_charge: this.serviceChargeInput });
            if (r.success !== false) { RepairBox.toast('Service charge saved', 'success'); await this.reload(); }
        },

        // ===== PAYMENTS =====
        async collectPayment() {
            if (!this.payForm.amount || Number(this.payForm.amount) <= 0) { RepairBox.toast('Enter payment amount', 'error'); return; }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/payment', 'POST', { payment_type: 'final', payment_method: this.payForm.payment_method, amount: this.payForm.amount });
            if (r.success !== false) {
                RepairBox.toast('Payment collected', 'success');
                this.payForm = { payment_type: 'final', payment_method: 'cash', amount: '' };
                await this.reload();
            }
        },

        // ===== CANCEL WITH REFUND =====
        // ===== UNIFIED CANCEL HANDLER =====
        async handleCancel() {
            if (!this.cancelForm.reason) { RepairBox.toast('Please provide a reason', 'error'); return; }

            let endpoint = '/admin/repairs/' + this.repair.id + '/cancel';
            let successMsg = 'Repair cancelled';
            let payload = { reason: this.cancelForm.reason };

            // If payment exists, it's a refund flow
            if (this.repair.net_paid > 0) {
                endpoint = '/admin/repairs/' + this.repair.id + '/cancel-refund';
                successMsg = 'Repair cancelled and refund processed';
                payload = this.cancelForm; // includes refund_method and parts_action
            }
            const r = await RepairBox.ajax(endpoint, 'POST', payload);
            if (r.success !== false) {
                RepairBox.toast(successMsg, 'success');
                this.showCancel = false;
                await this.reload();
            }
        },

        // ===== DUPLICATE =====
        async duplicateRepair() {
            if (!confirm('Create a duplicate of this repair?')) return;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/duplicate', 'POST');
            if (r.success !== false) {
                RepairBox.toast('Duplicate created: ' + r.data.ticket_number, 'success');
                window.location.href = '/admin/repairs/' + r.data.id;
            }
        },

        // ===== CALCULATIONS =====
        partsTotal() { return (this.repair.parts || []).reduce((s, p) => s + Number(p.cost_price) * p.quantity, 0); },
        servicesTotal() { return (this.repair.repair_services || []).reduce((s, svc) => s + Number(svc.customer_charge), 0); },
        vendorChargesTotal() { return (this.repair.repair_services || []).reduce((s, svc) => s + Number(svc.vendor_charge), 0); },
        grandTotal() { return this.partsTotal() + Number(this.repair.service_charge || 0) + this.servicesTotal(); },
        totalPaid() { return (this.repair.payments || []).filter(p => p.direction === 'IN').reduce((s, p) => s + Number(p.amount), 0); },
        totalRefunded() { return (this.repair.payments || []).filter(p => p.direction === 'OUT').reduce((s, p) => s + Number(p.amount), 0); },
        advancePaid() { return (this.repair.payments || []).filter(p => p.direction === 'IN' && p.payment_type === 'advance').reduce((s, p) => s + Number(p.amount), 0); },
        finalPaid() { return (this.repair.payments || []).filter(p => p.direction === 'IN' && p.payment_type !== 'advance').reduce((s, p) => s + Number(p.amount), 0); },
        balanceDue() { return Math.max(0, this.grandTotal() - this.totalPaid()); },
    };
}
</script>
<style>
@keyframes paymentPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(52,211,153,0.5), 0 4px 14px rgba(52,211,153,0.3); }
    50%       { box-shadow: 0 0 0 6px rgba(52,211,153,0),  0 4px 20px rgba(52,211,153,0.4); }
}
</style>
@endpush
