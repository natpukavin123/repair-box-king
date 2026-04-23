@extends('layouts.app')
@section('page-title', 'Repair #' . $repair->ticket_number)

@section('content')
<div x-data="repairDetail()" x-init="init()" x-cloak>

    {{-- ===== HEADER ===== --}}
    <div class="mb-5">
        <a href="/admin/repairs" class="text-sm text-primary-600 hover:text-primary-800 inline-flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Repairs
        </a>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-2xl font-bold text-gray-800" x-text="repair.ticket_number"></h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide"
                      :class="statusBadgeClass(repair.status)" x-text="statusLabel(repair.status)"></span>
            </div>

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <template x-if="!repair.is_locked && repair.status !== 'cancelled'">
                    <button @click="openEditModal()"
                        class="btn-secondary text-sm inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        <span class="hidden sm:inline">Edit</span>
                    </button>
                </template>
                <a :href="'/admin/repairs/' + repair.id + '/print'" target="_blank"
                   class="btn-secondary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span class="hidden sm:inline">Print Receipt</span>
                </a>
                <a :href="'/admin/repairs/' + repair.id + '/invoice'" target="_blank"
                   class="btn-primary text-sm inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span class="hidden sm:inline">Invoice</span>
                </a>
            </div>
        </div>
    </div>

    {{-- ===== PROGRESS BAR ===== --}}
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-5" x-show="repair.status !== 'cancelled'">
        <div class="flex items-center justify-center max-w-xs mx-auto">
            <template x-for="(step, idx) in progressSteps" :key="step.key">
                <div class="flex items-center" :class="idx < progressSteps.length - 1 ? 'flex-1' : ''">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold border-2 transition-all"
                             :class="stepReached(step.key)
                                 ? (repair.status === step.key ? stepCurrentClass(step.key) : 'bg-green-500 border-green-500 text-white')
                                 : 'bg-white border-gray-200 text-gray-300'">
                            <template x-if="stepReached(step.key) && repair.status !== step.key">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="!stepReached(step.key) || repair.status === step.key">
                                <span x-text="idx + 1"></span>
                            </template>
                        </div>
                        <span class="text-xs mt-1.5 font-semibold whitespace-nowrap"
                              :class="stepReached(step.key) ? 'text-gray-700' : 'text-gray-300'"
                              x-text="step.label"></span>
                    </div>
                    <div x-show="idx < progressSteps.length - 1"
                         class="flex-1 h-0.5 mx-3 mt-[-14px]"
                         :class="stepReached(step.key) && stepReached(progressSteps[idx+1]?.key) ? 'bg-green-500' : 'bg-gray-200'"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- Cancelled banner --}}
    <div x-show="repair.status === 'cancelled'" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-5 flex items-center gap-3">
        <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
        <div>
            <p class="font-bold text-red-800">Repair Cancelled</p>
            <p class="text-sm text-red-600" x-show="repair.cancel_reason" x-text="'Reason: ' + repair.cancel_reason"></p>
        </div>
    </div>

    {{-- ===== TWO COLUMN LAYOUT ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT COLUMN --}}
        <div class="lg:col-span-2 space-y-5 order-2 lg:order-1">

            {{-- Closed banner --}}
            <template x-if="repair.status === 'closed'">
                <div>
                    {{-- Payment due warning (if closed without full payment) --}}
                    <template x-if="balanceDue() > 0">
                        <div class="mb-3 bg-red-50 border border-red-200 rounded-xl p-4">
                            <div class="flex items-start justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="font-bold text-red-800">Payment Pending</p>
                                        <p class="text-sm text-red-600">Balance of <span class="font-bold" x-text="'₹' + balanceDue().toFixed(2)"></span> is still due.</p>
                                    </div>
                                </div>
                                <button @click="showLatePayment = !showLatePayment"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold bg-red-600 hover:bg-red-700 text-white transition whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Record Payment
                                </button>
                            </div>

                            {{-- Inline late payment form --}}
                            <div x-show="showLatePayment" x-cloak class="mt-4 border-t border-red-200 pt-4 space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Amount (₹)</label>
                                        <input x-model="latePayForm.amount" type="number" step="0.01"
                                               :placeholder="balanceDue().toFixed(2)"
                                               class="form-input-custom w-full text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-red-700 mb-1.5">Payment Method</label>
                                        <div class="flex gap-1.5">
                                            <template x-for="m in [{key:'cash',label:'Cash'},{key:'upi',label:'UPI'},{key:'card',label:'Card'}]" :key="m.key">
                                                <button type="button" @click="latePayForm.method = m.key"
                                                    class="flex-1 py-2 rounded-lg border text-[10px] font-semibold transition text-center"
                                                    :class="latePayForm.method === m.key ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-400 hover:border-primary-300'">
                                                    <span x-text="m.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <button @click="recordLatePayment()" :disabled="savingLatePay"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-sm font-bold bg-red-600 hover:bg-red-700 text-white transition">
                                    <span x-show="savingLatePay" class="spinner"></span>
                                    <span x-text="savingLatePay ? 'Saving...' : '✓ Confirm Payment'"></span>
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- Refund due notice (advance exceeds final cost) --}}
                    <template x-if="refundDue() > 0">
                        <div class="mb-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-amber-800">Refund Due</p>
                                    <p class="text-sm text-amber-600">Customer overpaid by <span class="font-bold" x-text="'₹' + refundDue().toFixed(2)"></span>. Please refund the excess amount.</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Normal closed banner --}}
                    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-green-800">Repair Closed</p>
                                <p class="text-sm" :class="balanceDue() > 0 ? 'text-orange-600' : (refundDue() > 0 ? 'text-amber-600' : 'text-green-600')" x-text="balanceDue() > 0 ? 'Closed with pending payment.' : (refundDue() > 0 ? 'Closed — refund of ₹' + refundDue().toFixed(2) + ' is due.' : 'Repair complete and payment settled.')"></p>
                            </div>
                        </div>
                        <a :href="'/admin/repairs/' + repair.id + '/invoice'" target="_blank"
                           class="btn-primary !bg-green-600 hover:!bg-green-700 !border-0 shadow-sm inline-flex items-center gap-2 text-sm whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Download Invoice
                        </a>
                    </div>
                </div>
            </template>

            {{-- Tabs --}}
            <div class="bg-white rounded-xl shadow-sm border border-b-0 rounded-b-none p-2">
                <div class="flex gap-1">
                    <button @click="activeTab = 'details'"
                        class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition text-center"
                        :class="activeTab === 'details' ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-50'">
                        Repair Details
                    </button>
                    <button @click="activeTab = 'history'"
                        class="flex-1 py-2.5 px-4 rounded-lg text-sm font-semibold transition text-center"
                        :class="activeTab === 'history' ? 'bg-primary-50 text-primary-700' : 'text-gray-500 hover:bg-gray-50'">
                        Activity Log
                    </button>
                </div>
            </div>

            <div class="bg-white shadow-sm border border-t-0 rounded-b-xl min-h-[400px]">

                {{-- DETAILS TAB --}}
                <div x-show="activeTab === 'details'" class="p-5 space-y-5">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Customer</div>
                            <div class="text-sm font-semibold text-gray-800" x-text="repair.customer?.name || 'Walk-in'"></div>
                            <div class="text-xs text-gray-400" x-text="repair.customer?.mobile_number || ''"></div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Device</div>
                            <div class="text-sm font-semibold text-gray-800" x-text="[repair.device_brand, repair.device_model].filter(Boolean).join(' ') || '—'"></div>
                            <div class="text-xs text-gray-400" x-text="repair.imei ? 'IMEI: ' + repair.imei : ''"></div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Tracking ID</div>
                            <div class="text-sm font-semibold text-primary-600" x-text="repair.tracking_id"></div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Estimated Cost</div>
                            <div class="text-sm font-semibold text-gray-800" x-text="'₹' + Number(repair.estimated_cost || 0).toFixed(2)"></div>
                        </div>
                    </div>

                    <div class="border-t pt-4" x-show="repair.problem_description">
                        <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-2">Problem Description</div>
                        <div class="text-sm text-gray-700 leading-relaxed whitespace-pre-line bg-amber-50 border border-amber-100 rounded-lg p-3"
                             x-text="repair.problem_description"></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 border-t pt-4">
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Created</div>
                            <div class="text-sm text-gray-600" x-text="formatDateTime(repair.created_at)"></div>
                        </div>
                        <div x-show="repair.expected_delivery_date">
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Expected Delivery</div>
                            <div class="text-sm text-gray-600" x-text="formatDate(repair.expected_delivery_date)"></div>
                        </div>
                        <div x-show="repair.closed_at">
                            <div class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold mb-1">Closed At</div>
                            <div class="text-sm text-gray-600" x-text="formatDateTime(repair.closed_at)"></div>
                        </div>
                    </div>

                    {{-- Payment collection removed — payments handled at Close Repair modal --}}

                </div>

                {{-- ACTIVITY TAB --}}
                <div x-show="activeTab === 'history'" class="p-4 sm:p-5" x-cloak>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activity &amp; Status Log
                    </h4>
                    <div class="relative">
                        <div class="absolute left-3 top-0 bottom-0 w-0.5 bg-gray-100"></div>
                        <div class="space-y-3">
                            <template x-for="sh in (repair.status_history || []).slice().reverse()" :key="sh.id">
                                <div class="relative flex items-start gap-3 pl-8">
                                    <div class="absolute left-1.5 top-2 w-3 h-3 rounded-full border-2 border-white shadow-sm"
                                         :class="statusDotBg(sh.status)"></div>
                                    <div class="flex-1 bg-gray-50 rounded-lg px-3 py-2.5 border border-gray-100">
                                        <div class="flex items-start justify-between gap-2 flex-wrap">
                                            <span class="text-xs font-bold text-gray-800" x-text="statusLabel(sh.status)"></span>
                                            <span class="text-[10px] text-gray-400 whitespace-nowrap" x-text="formatDateTime(sh.created_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 mt-1 leading-relaxed" x-show="sh.notes" x-text="sh.notes"></p>
                                        <p class="text-[10px] text-gray-400 mt-1" x-show="sh.updater" x-text="'by ' + (sh.updater?.name || '')"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="space-y-5 order-1 lg:order-2">

            {{-- STATUS CHANGE PANEL --}}
            <template x-if="repair.status !== 'cancelled' && !repair.is_locked">
                <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">

                    {{-- Next action button --}}
                    <div class="px-5 pt-5 pb-4">

                        {{-- Received → Start Repair --}}
                        <template x-if="repair.status === 'received'">
                            <button @click="changeStatus('in_progress')"
                                class="group relative w-full overflow-hidden rounded-xl px-4 py-4 text-sm font-bold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0"
                                style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                                <span class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200" style="background:linear-gradient(135deg,#fbbf24 0%,#f59e0b 100%);"></span>
                                <span class="relative flex items-center justify-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <span class="flex flex-col text-left">
                                        <span class="text-base font-extrabold leading-tight">Start Repair</span>
                                        <span class="text-[11px] font-normal opacity-80">Move to In Progress</span>
                                    </span>
                                    <svg class="w-5 h-5 ml-auto opacity-60 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </button>
                        </template>

                        {{-- In Progress → Mark as Completed --}}
                        <template x-if="repair.status === 'in_progress'">
                            <button @click="changeStatus('completed')"
                                class="group relative w-full overflow-hidden rounded-xl px-4 py-4 text-sm font-bold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0"
                                style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);">
                                <span class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200" style="background:linear-gradient(135deg,#2dd4bf 0%,#14b8a6 100%);"></span>
                                <span class="relative flex items-center justify-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                    <span class="flex flex-col text-left">
                                        <span class="text-base font-extrabold leading-tight">Mark as Completed</span>
                                        <span class="text-[11px] font-normal opacity-80">Repair work is done</span>
                                    </span>
                                    <svg class="w-5 h-5 ml-auto opacity-60 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </button>
                        </template>

                        {{-- Completed → Close Repair --}}
                        <template x-if="repair.status === 'completed'">
                            <button @click="openCloseModal()"
                                class="group relative w-full overflow-hidden rounded-xl px-4 py-4 text-sm font-bold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0"
                                style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                                <span class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-200" style="background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%);"></span>
                                <span class="relative flex items-center justify-center gap-2.5">
                                    <span class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                    <span class="flex flex-col text-left">
                                        <span class="text-base font-extrabold leading-tight">Close Repair</span>
                                        <span class="text-[11px] font-normal opacity-80">Confirm &amp; settle payment</span>
                                    </span>
                                    <svg class="w-5 h-5 ml-auto opacity-60 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </button>
                        </template>

                    </div>

                    {{-- Cancel section --}}
                    <div class="border-t border-gray-100 px-5 py-3">
                        <div x-show="!cancelOpen">
                            <button @click="cancelOpen = true"
                                class="group w-full flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-semibold text-red-400 hover:text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 transition-all duration-150">
                                <svg class="w-3.5 h-3.5 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Cancel this Repair
                            </button>
                        </div>
                        <div x-show="cancelOpen" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-2.5">
                            <div class="flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <label class="text-xs font-bold text-red-700">Cancellation Reason</label>
                            </div>
                            <textarea x-model="cancelReason" rows="2"
                                class="form-input-custom w-full text-sm resize-none"
                                placeholder="Why is this repair being cancelled?"></textarea>
                            <div class="flex gap-2">
                                <button @click="cancelOpen = false; cancelReason = ''"
                                    class="flex-1 py-2 px-3 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    Back
                                </button>
                                <button @click="doCancel()"
                                    class="flex-1 py-2 px-3 rounded-lg text-xs font-bold bg-red-600 hover:bg-red-700 text-white transition shadow-sm">
                                    Confirm Cancel
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </template>

            {{-- FINANCIAL SUMMARY --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b px-4 py-3">
                    <h3 class="font-bold text-sm text-gray-900 uppercase tracking-wider">Financial Summary</h3>
                </div>
                <div class="p-4 space-y-3">
                    {{-- Final Cost (primary billing amount) --}}
                    <div class="flex justify-between items-center py-2.5 px-3 rounded-lg border"
                         :class="repair.final_cost !== null && repair.final_cost !== undefined ? 'bg-green-50 border-green-200' : 'bg-indigo-50 border-indigo-100'">
                        <span class="text-sm font-semibold"
                              :class="repair.final_cost !== null && repair.final_cost !== undefined ? 'text-green-800' : 'text-indigo-800'"
                              x-text="repair.final_cost !== null && repair.final_cost !== undefined ? 'Final Cost' : 'Billing Amount'"></span>
                        <span class="text-base font-bold"
                              :class="repair.final_cost !== null && repair.final_cost !== undefined ? 'text-green-900' : 'text-indigo-900'"
                              x-text="'₹' + grandTotal().toFixed(2)"></span>
                    </div>

                    {{-- Estimated Cost (info only - shown as secondary when different from final) --}}
                    <template x-if="Number(repair.estimated_cost || 0) > 0 && (repair.final_cost === null || repair.final_cost === undefined || Number(repair.final_cost) !== Number(repair.estimated_cost))">
                        <div class="flex justify-between items-center py-1.5 px-3 text-xs text-amber-600">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Initial Estimate
                            </span>
                            <span class="font-semibold" x-text="'₹' + Number(repair.estimated_cost || 0).toFixed(2)"></span>
                        </div>
                    </template>

                    <div class="border-t-2 border-gray-100 pt-3 space-y-2">
                        <template x-if="advancePaid() > 0">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                                    Advance
                                </span>
                                <span class="font-bold text-amber-600" x-text="'₹' + advancePaid().toFixed(2)"></span>
                            </div>
                        </template>
                        <template x-if="finalPaid() > 0">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    Final Paid
                                </span>
                                <span class="font-bold text-green-600" x-text="'₹' + finalPaid().toFixed(2)"></span>
                            </div>
                        </template>
                        <template x-if="totalRefunded() > 0">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                    Refunded
                                </span>
                                <span class="font-bold text-red-500" x-text="'-₹' + totalRefunded().toFixed(2)"></span>
                            </div>
                        </template>
                        <div class="flex justify-between text-sm pt-1 border-t border-gray-100">
                            <span class="font-semibold text-gray-700">Net Paid</span>
                            <span class="font-bold text-indigo-600" x-text="'₹' + netPaid().toFixed(2)"></span>
                        </div>
                    </div>

                    {{-- Balance Due or Refund Due --}}
                    <template x-if="refundDue() > 0">
                        <div class="rounded-lg p-3 border bg-amber-50 border-amber-200">
                            <div class="flex justify-between items-center">
                                <span class="font-bold uppercase text-sm text-amber-800">Refund Due</span>
                                <span class="text-xl font-bold text-amber-600" x-text="'₹' + refundDue().toFixed(2)"></span>
                            </div>
                            <p class="text-xs text-amber-700 mt-1">⟲ Customer overpaid — refund needed</p>
                        </div>
                    </template>
                    <template x-if="refundDue() <= 0">
                        <div class="rounded-lg p-3 border"
                             :class="balanceDue() > 0 ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'">
                            <div class="flex justify-between items-center">
                                <span class="font-bold uppercase text-sm"
                                      :class="balanceDue() > 0 ? 'text-red-800' : 'text-green-800'">Balance Due</span>
                                <span class="text-xl font-bold"
                                      :class="balanceDue() > 0 ? 'text-red-600' : 'text-green-600'"
                                      x-text="'₹' + balanceDue().toFixed(2)"></span>
                            </div>
                            <template x-if="balanceDue() <= 0 && grandTotal() > 0">
                                <p class="text-xs text-green-700 mt-1">✓ Fully Paid</p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- PAYMENT HISTORY --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Payments</h3></div>
                <div class="p-4">
                    <template x-if="(repair.payments || []).length > 0">
                        <div class="space-y-0">
                            <template x-for="p in repair.payments" :key="p.id">
                                <div class="flex items-center justify-between text-sm py-2.5 border-b last:border-0">
                                    <div>
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-medium capitalize" x-text="p.payment_type"></span>
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full"
                                                  :class="{'bg-green-100 text-green-700': p.payment_method === 'cash', 'bg-blue-100 text-blue-700': p.payment_method === 'upi', 'bg-purple-100 text-purple-700': p.payment_method === 'card', 'bg-gray-100 text-gray-700': ['bank_transfer','bank'].includes(p.payment_method)}"
                                                  x-text="p.payment_method === 'bank_transfer' ? 'Bank' : (p.payment_method || '').toUpperCase()"></span>
                                        </div>
                                        <div class="text-[10px] text-gray-400 mt-0.5" x-show="p.reference_number" x-text="'Ref: ' + p.reference_number"></div>
                                        <div class="text-[10px] text-gray-400" x-text="formatDate(p.created_at)"></div>
                                    </div>
                                    <span class="font-semibold"
                                          :class="p.direction === 'OUT' ? 'text-red-600' : 'text-green-600'"
                                          x-text="(p.direction === 'OUT' ? '-' : '+') + '₹' + Number(p.amount).toFixed(2)"></span>
                                </div>
                            </template>
                            <div class="pt-3 mt-1 border-t flex justify-between text-sm font-bold">
                                <span class="text-gray-700">Net Paid</span>
                                <span class="text-primary-600" x-text="'₹' + netPaid().toFixed(2)"></span>
                            </div>
                        </div>
                    </template>
                    <template x-if="(repair.payments || []).length === 0">
                        <p class="text-sm text-gray-400 text-center py-4">No payments yet</p>
                    </template>
                </div>
            </div>

            {{-- RELATED REPAIRS --}}
            <template x-if="(repair.child_repairs || []).length > 0">
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b"><h3 class="font-semibold text-sm text-gray-600">Related Repairs</h3></div>
                    <div class="p-3">
                        <template x-for="child in repair.child_repairs" :key="child.id">
                            <a :href="'/admin/repairs/' + child.id"
                               class="flex items-center justify-between py-2.5 border-b last:border-0 text-sm hover:bg-gray-50 rounded px-2 -mx-2 transition">
                                <span class="font-medium text-primary-600" x-text="child.ticket_number"></span>
                                <span class="text-xs text-gray-400" x-text="formatDate(child.created_at)"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

        </div>
    </div>

    {{-- ===== CLOSE REPAIR MODAL ===== --}}
    <div x-show="showCloseModal" class="modal-overlay" x-cloak>
        <div class="modal-container max-w-sm" @click.away="showCloseModal = false">

            {{-- Header --}}
            <div class="modal-header border-b">
                <div>
                    <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Close Repair
                    </h3>
                </div>
                <button @click="showCloseModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="modal-body space-y-4">

                {{-- Editable Final Amount --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Final Amount (₹)</label>
                    <input x-model="closeFinalAmount" type="number" step="0.01" min="0"
                           class="form-input-custom w-full text-lg font-bold text-center" placeholder="0.00">
                </div>

                {{-- Auto-calculated breakdown --}}
                <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-3 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Advance Paid</span>
                        <span class="font-semibold text-blue-700" x-text="'₹' + advancePaid().toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 font-bold text-base">
                        <span class="text-gray-800">Amount to Collect</span>
                        <span :class="closeAmountToCollect() > 0 ? 'text-red-600' : 'text-green-600'"
                              x-text="'₹' + closeAmountToCollect().toFixed(2)"></span>
                    </div>
                </div>

                {{-- Fully paid notice --}}
                <div x-show="closeAmountToCollect() <= 0" x-cloak
                     class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-3 py-2">
                    <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-xs font-semibold text-green-700">Fully covered by advance. No collection needed.</span>
                </div>

                {{-- Payment method — only when there's something to collect --}}
                <div x-show="closeAmountToCollect() > 0" x-cloak>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Payment Method</label>
                    <div class="flex gap-1.5">
                        <template x-for="m in [{key:'cash',label:'Cash'},{key:'upi',label:'UPI'},{key:'card',label:'Card'}]" :key="m.key">
                            <button type="button" @click="closePayMethod = m.key"
                                class="flex-1 py-2 rounded-lg border text-xs font-semibold transition text-center"
                                :class="closePayMethod === m.key ? 'bg-primary-600 border-primary-600 text-white' : 'bg-white border-gray-200 text-gray-500 hover:border-primary-300'">
                                <span x-text="m.label"></span>
                            </button>
                        </template>
                    </div>
                </div>

            </div>

            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button @click="showCloseModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="confirmClose()" :disabled="closingRepair"
                    class="btn-primary w-full sm:w-auto bg-green-600 hover:bg-green-700 border-green-600 inline-flex items-center justify-center gap-2">
                    <span x-show="closingRepair" class="spinner"></span>
                    <svg x-show="!closingRepair" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="closingRepair ? 'Closing...' : 'Confirm & Close'"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- ===== EDIT MODAL ===== --}}
    <div x-show="showEditModal" class="modal-overlay" x-cloak>
        <div class="modal-container modal-xl" @click.away="showEditModal = false">
            <div class="modal-header">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Edit Repair Intake</h3>
                    <p class="text-sm text-slate-500 mt-1">Update the intake details for this repair.</p>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">&times;</button>
            </div>
            <div class="modal-body max-h-[75vh] overflow-y-auto space-y-5">

                <div class="rounded-2xl border border-indigo-100 bg-indigo-50/40 p-4">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Customer</label>
                    <div x-show="editForm.customer_id"
                         class="mb-3 inline-flex items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-2 text-sm text-indigo-800">
                        <span class="truncate" x-text="editSelectedCustomer?.name + (editSelectedCustomer?.mobile_number ? ' · ' + editSelectedCustomer.mobile_number : '')"></span>
                        <button @click="editForm.customer_id = null; editSelectedCustomer = null; editCustSearch = ''" class="text-indigo-400 hover:text-red-500 text-lg leading-none">&times;</button>
                    </div>
                    <div class="relative" @click.away="editCustOpen = false; editCustResults = []">
                        <input x-model="editCustSearch"
                               @focus="searchEditCustomers(1)"
                               @input.debounce.300ms="searchEditCustomers(1)"
                               type="text" class="form-input-custom text-sm w-full"
                               placeholder="Search by name or mobile">
                        <div x-show="editCustOpen && editCustResults.length > 0" x-cloak
                             class="absolute z-50 mt-1 w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <div class="max-h-52 overflow-y-auto">
                                <template x-for="c in editCustResults" :key="c.id">
                                    <button @click="selectEditCustomer(c)"
                                        class="flex w-full items-center justify-between gap-3 border-b border-slate-100 px-3 py-3 text-left hover:bg-indigo-50 transition">
                                        <div>
                                            <div class="text-sm font-medium text-slate-800" x-text="c.name"></div>
                                            <div class="text-xs text-slate-400" x-text="c.mobile_number || ''"></div>
                                        </div>
                                    </button>
                                </template>
                                <div x-show="editCustLoading" class="px-3 py-3 text-center text-xs text-slate-400">Loading…</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div x-data="brandDropdown(brandList, (v) => { editForm.device_brand = v; })"
                         x-effect="syncValue(editForm.device_brand)"
                         @click.outside="open = false" class="relative">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Device Brand <span class="text-red-500">*</span></label>
                        <input type="text" x-model="query"
                               @focus="open = true"
                               @input="open = true; selected = query; updateValue(query)"
                               @keydown.arrow-down.prevent="highlightNext()"
                               @keydown.arrow-up.prevent="highlightPrev()"
                               @keydown.enter.prevent="selectHighlighted()"
                               @keydown.escape="open = false"
                               class="form-input-custom w-full text-sm" placeholder="e.g. Samsung, Apple" autocomplete="off">
                        <div x-show="open && filtered.length > 0" x-cloak
                             class="absolute z-50 mt-1 w-full max-h-48 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                            <template x-for="(brand, idx) in filtered" :key="brand">
                                <div @click="pick(brand)"
                                     :class="idx === highlighted ? 'bg-blue-50 text-blue-700' : 'text-slate-700 hover:bg-slate-50'"
                                     class="cursor-pointer px-3 py-2 text-sm" x-text="brand"></div>
                            </template>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Device Model <span class="text-red-500">*</span></label>
                        <input x-model="editForm.device_model" type="text"
                               class="form-input-custom w-full text-sm" placeholder="Galaxy S24, iPhone 15">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">IMEI / Serial No.</label>
                        <input x-model="editForm.imei" type="text"
                               class="form-input-custom w-full text-sm" placeholder="Optional">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Estimated Cost</label>
                        <input x-model="editForm.estimated_cost" type="number" step="0.01"
                               class="form-input-custom w-full text-sm" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Problem Description <span class="text-red-500">*</span></label>
                    <textarea x-model="editForm.problem_description" rows="4"
                              class="form-input-custom w-full text-sm"
                              placeholder="Describe the issue clearly"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Expected Delivery Date</label>
                    <input x-model="editForm.expected_delivery_date" type="date"
                           class="form-input-custom w-full text-sm">
                </div>

            </div>
            <div class="modal-footer flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                <button @click="showEditModal = false" class="btn-secondary w-full sm:w-auto">Cancel</button>
                <button @click="saveRepairDetails()"
                        class="btn-primary w-full sm:w-auto inline-flex items-center justify-center gap-2" :disabled="savingEdit">
                    <span x-show="savingEdit" class="spinner"></span>
                    <span x-text="savingEdit ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>

    <script>
    const brandList = @json($brands);
    function brandDropdown(brands, onChange) {
        return {
            open: false, query: '', selected: '', highlighted: -1, brands: brands, filtered: [],
            init() {
                this.filtered = this.brands ? this.brands.slice() : [];
                this.$watch('query', (val) => {
                    const q = val.trim().toLowerCase();
                    this.filtered = q ? this.brands.filter(b => b.toLowerCase().includes(q)) : this.brands.slice();
                });
            },
            syncValue(val) {
                if ((val || '') !== this.selected) { this.query = val || ''; this.selected = val || ''; }
            },
            pick(brand) { this.query = brand; this.selected = brand; this.open = false; this.highlighted = -1; onChange(brand); },
            updateValue(val) { onChange(val); },
            highlightNext() { if (!this.filtered.length) return; this.highlighted = (this.highlighted + 1) % this.filtered.length; },
            highlightPrev() { if (!this.filtered.length) return; this.highlighted = this.highlighted <= 0 ? this.filtered.length - 1 : this.highlighted - 1; },
            selectHighlighted() { if (this.highlighted >= 0) this.pick(this.filtered[this.highlighted]); },
        };
    }
    </script>

</div>
@endsection

@push('scripts')
<script>
function repairDetail() {
    return {
        repair: @json($repair),
        statusMeta: @json($statusMeta),
        activeTab: 'details',
        showEditModal: false,
        savingEdit: false,
        cancelOpen: false,
        cancelReason: '',

        // Close repair modal
        showCloseModal: false,
        closingRepair: false,
        closeFinalAmount: '',
        closePayMethod: 'cash',

        // Late payment (closed with balance)
        showLatePayment: false,
        savingLatePay: false,
        latePayForm: { amount: '', method: 'cash', ref: '' },

        progressSteps: [
            { key: 'received',    label: 'Received' },
            { key: 'in_progress', label: 'In Progress' },
            { key: 'completed',   label: 'Completed' },
            { key: 'closed',      label: 'Closed' },
        ],

        editForm: {},
        editSelectedCustomer: null,
        editCustSearch: '', editCustResults: [], editCustOpen: false,
        editCustLoading: false, editCustHasMore: false, editCustPage: 1,

        payForm: { payment_method: 'cash', amount: '', reference_number: '' },

        brandList: @json($brands),

        init() {
            this.syncEditForm();
        },

        async reload() {
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id);
            if (r.data || r.id) {
                this.repair = r.data || r;
                this.syncEditForm();
            }
        },

        syncEditForm() {
            this.editForm = {
                customer_id:             this.repair.customer_id || '',
                device_brand:            this.repair.device_brand || '',
                device_model:            this.repair.device_model || '',
                imei:                    this.repair.imei || '',
                problem_description:     this.repair.problem_description || '',
                estimated_cost:          this.repair.estimated_cost || '',
                expected_delivery_date:  this.repair.expected_delivery_date
                    ? String(this.repair.expected_delivery_date).substring(0, 10) : '',
            };
            this.editSelectedCustomer = this.repair.customer || null;
            this.editCustSearch       = '';
            this.editCustResults      = [];
            this.editCustOpen         = false;
        },

        openEditModal() {
            this.syncEditForm();
            this.showEditModal = true;
        },

        statusLabel(status) {
            return this.statusMeta[status]?.label || (status || '').replace('_', ' ');
        },
        statusBadgeClass(status) {
            const map = {
                received:    'bg-blue-100 text-blue-700',
                in_progress: 'bg-amber-100 text-amber-700',
                completed:   'bg-teal-100 text-teal-700',
                closed:      'bg-green-100 text-green-800',
                cancelled:   'bg-red-100 text-red-700',
            };
            return map[status] || 'bg-gray-100 text-gray-700';
        },
        statusDotBg(status) {
            const map = {
                received:    'bg-blue-500',
                in_progress: 'bg-amber-500',
                completed:   'bg-teal-500',
                closed:      'bg-green-600',
                cancelled:   'bg-red-500',
            };
            return map[status] || 'bg-gray-400';
        },
        stepCurrentClass(key) {
            const map = {
                received:    'bg-blue-500 border-blue-500 text-white ring-2 ring-blue-200',
                in_progress: 'bg-amber-500 border-amber-500 text-white ring-2 ring-amber-200',
                completed:   'bg-teal-500 border-teal-500 text-white ring-2 ring-teal-200',
                closed:      'bg-green-600 border-green-600 text-white ring-2 ring-green-200',
            };
            return map[key] || 'bg-primary-600 border-primary-600 text-white ring-2 ring-primary-200';
        },
        stepReached(key) {
            const order = ['received', 'in_progress', 'completed', 'closed'];
            return order.indexOf(this.repair.status) >= order.indexOf(key);
        },

        formatDate(d) {
            if (!d) return '';
            return new Date(d).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },
        formatDateTime(d) {
            if (!d) return '';
            return new Date(d).toLocaleString('en-IN', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
        },

        async changeStatus(status) {
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/status', 'PUT', { status });
            if (r.success !== false) {
                RepairBox.toast('Status: ' + this.statusLabel(status), 'success');
                await this.reload();
            }
        },

        openCloseModal() {
            // Pre-fill with estimated cost
            this.closeFinalAmount = this.grandTotal().toFixed(2);
            this.closePayMethod = 'cash';
            this.showCloseModal = true;
        },

        closeAmountToCollect() {
            const finalAmt = Number(this.closeFinalAmount) || 0;
            return Math.max(0, finalAmt - this.advancePaid());
        },

        async confirmClose() {
            const finalAmt = Number(this.closeFinalAmount) || 0;
            if (finalAmt < 0) {
                RepairBox.toast('Final amount cannot be negative', 'error');
                return;
            }

            this.closingRepair = true;

            // Record final payment if there's an amount to collect
            const toCollect = this.closeAmountToCollect();
            if (toCollect > 0) {
                const pr = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/payment', 'POST', {
                    payment_type:     'final',
                    payment_method:   this.closePayMethod,
                    amount:           toCollect,
                    reference_number: null,
                });
                if (pr.success === false) {
                    this.closingRepair = false;
                    return;
                }
            }

            // Close the repair — send final_cost to backend
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/status', 'PUT', { status: 'closed', final_cost: finalAmt });

            if (r.success !== false) {
                // Auto-refund if customer overpaid
                const totalPaidNow = this.totalPaid() + (toCollect > 0 ? toCollect : 0);
                const refundAmt = totalPaidNow - finalAmt;
                if (refundAmt > 0) {
                    await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/payment', 'POST', {
                        payment_type:     'refund',
                        payment_method:   this.closePayMethod,
                        amount:           refundAmt,
                        direction:        'OUT',
                        notes:            'Auto-refund on close (overpayment)',
                    });
                }

                RepairBox.toast('Repair closed' + (refundAmt > 0 ? ' — ₹' + refundAmt.toFixed(2) + ' refunded' : ''), 'success');
                this.showCloseModal = false;
                this.closingRepair = false;
                await this.reload();
            } else {
                this.closingRepair = false;
            }
        },

        async recordLatePayment() {
            const amt = Number(this.latePayForm.amount) || this.balanceDue();
            if (amt <= 0) { RepairBox.toast('Enter a valid amount', 'error'); return; }
            this.savingLatePay = true;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/payment', 'POST', {
                payment_type:     'final',
                payment_method:   this.latePayForm.method,
                amount:           amt,
                reference_number: this.latePayForm.ref || null,
            });
            this.savingLatePay = false;
            if (r.success !== false) {
                RepairBox.toast('Payment recorded', 'success');
                this.latePayForm = { amount: '', method: 'cash', ref: '' };
                this.showLatePayment = false;
                await this.reload();
            }
        },

        async doCancel() {
            if (!this.cancelReason.trim()) {
                RepairBox.toast('Please provide a cancellation reason', 'error');
                return;
            }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/cancel', 'POST', {
                reason: this.cancelReason,
            });
            if (r.success !== false) {
                RepairBox.toast('Repair cancelled', 'success');
                this.cancelOpen = false;
                this.cancelReason = '';
                await this.reload();
            }
        },

        grandTotal()    { return Number(this.repair.final_cost ?? this.repair.estimated_cost ?? 0); },
        totalPaid()     { return (this.repair.payments || []).filter(p => p.direction !== 'OUT').reduce((s, p) => s + Number(p.amount), 0); },
        totalRefunded() { return (this.repair.payments || []).filter(p => p.direction === 'OUT').reduce((s, p) => s + Number(p.amount), 0); },
        netPaid()       { return this.totalPaid() - this.totalRefunded(); },
        advancePaid()   { return (this.repair.payments || []).filter(p => p.direction !== 'OUT' && p.payment_type === 'advance').reduce((s, p) => s + Number(p.amount), 0); },
        finalPaid()     { return (this.repair.payments || []).filter(p => p.direction !== 'OUT' && p.payment_type === 'final').reduce((s, p) => s + Number(p.amount), 0); },
        balanceDue()    { return Math.max(0, this.grandTotal() - this.netPaid()); },
        refundDue()     { return Math.max(0, this.netPaid() - this.grandTotal()); },

        async collectPayment(type) {
            if (!this.payForm.amount || Number(this.payForm.amount) <= 0) {
                RepairBox.toast('Enter a valid amount', 'error');
                return;
            }
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id + '/payment', 'POST', {
                payment_type:     type,
                payment_method:   this.payForm.payment_method,
                amount:           this.payForm.amount,
                reference_number: this.payForm.reference_number || null,
            });
            if (r.success !== false) {
                RepairBox.toast('Payment recorded', 'success');
                this.payForm = { payment_method: 'cash', amount: '', reference_number: '' };
                await this.reload();
            }
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
            this.editCustPage    = page;
            this.editCustOpen    = true;
        },

        selectEditCustomer(c) {
            this.editSelectedCustomer = c;
            this.editForm.customer_id = c.id;
            this.editCustSearch       = '';
            this.editCustResults      = [];
            this.editCustOpen         = false;
        },

        async saveRepairDetails() {
            if (!this.editForm.customer_id)          { RepairBox.toast('Customer is required', 'error'); return; }
            if (!this.editForm.device_brand)         { RepairBox.toast('Device brand is required', 'error'); return; }
            if (!this.editForm.device_model)         { RepairBox.toast('Device model is required', 'error'); return; }
            if (!this.editForm.problem_description)  { RepairBox.toast('Problem description is required', 'error'); return; }

            this.savingEdit = true;
            const r = await RepairBox.ajax('/admin/repairs/' + this.repair.id, 'PUT', this.editForm);
            this.savingEdit = false;

            if (r.success !== false) {
                RepairBox.toast('Repair updated', 'success');
                this.showEditModal = false;
                await this.reload();
            }
        },
    };
}
</script>
@endpush
