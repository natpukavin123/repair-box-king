@extends('layouts.app')
@section('page-title', 'Expenses')
@section('content-class', 'workspace-content')

@section('content')
<style>
    .exp-workspace {
        gap: 0.7rem;
    }

    .exp-workspace .exp-toolbar,
    .exp-workspace .exp-filterbar {
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.96), rgba(244, 247, 255, 0.88));
        box-shadow: 0 18px 42px -34px rgba(15, 23, 42, 0.34);
        backdrop-filter: blur(16px);
    }

    .exp-workspace .exp-toolbar {
        padding: 0.55rem;
    }

    .exp-workspace .exp-filterbar {
        padding: 0.45rem;
        gap: 0.45rem;
    }

    .exp-workspace .exp-search-input,
    .exp-workspace .exp-form-input,
    .exp-workspace .exp-filter-control {
        min-height: 2.7rem;
        border-radius: 0.95rem;
        border-color: rgba(148, 163, 184, 0.22);
        background: rgba(255, 255, 255, 0.94);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7), 0 12px 28px -24px rgba(15, 23, 42, 0.28);
    }

    .exp-workspace .exp-search-input {
        padding-top: 0.72rem;
        padding-bottom: 0.72rem;
    }

    .exp-workspace .exp-filter-control {
        height: 2.5rem;
        min-height: 2.5rem;
        padding-top: 0.55rem;
        padding-bottom: 0.55rem;
    }

    .exp-workspace .exp-panel {
        border-radius: 1.35rem;
        border-color: rgba(148, 163, 184, 0.16);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(250, 252, 255, 0.82));
        box-shadow: 0 26px 60px -42px rgba(15, 23, 42, 0.38);
    }

    .exp-workspace .exp-panel .card-header {
        padding: 0.9rem 1rem;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(241, 245, 255, 0.48));
    }

    .exp-workspace .exp-panel .card-body {
        padding: 1rem;
    }

    .exp-workspace .exp-table-shell {
        padding: 0.35rem 0.4rem 0.15rem;
    }

    .exp-workspace .exp-table-shell .data-table thead {
        background: linear-gradient(180deg, rgba(248, 250, 252, 0.98), rgba(238, 242, 255, 0.9));
    }

    .exp-workspace .exp-table-shell .data-table th {
        padding: 0.75rem 0.9rem;
        font-size: 0.65rem;
        letter-spacing: 0.14em;
    }

    .exp-workspace .exp-table-shell .data-table td {
        padding: 0.8rem 0.9rem;
        font-size: 0.88rem;
    }

    .exp-workspace .exp-table-shell .data-table tbody tr {
        border-top-color: rgba(226, 232, 240, 0.92);
    }

    .exp-workspace .exp-table-shell .data-table tbody tr:hover {
        background: rgba(37, 99, 235, 0.04);
    }

    .exp-workspace .exp-form-scroll > div {
        padding: 0.95rem 1rem;
    }

    @media (max-width: 1023px) {
        .exp-workspace {
            gap: 0.6rem;
        }

        .exp-workspace .exp-toolbar,
        .exp-workspace .exp-filterbar {
            padding: 0.45rem;
        }

        .exp-workspace .exp-panel .card-header,
        .exp-workspace .exp-panel .card-body,
        .exp-workspace .exp-form-scroll > div {
            padding-left: 0.85rem;
            padding-right: 0.85rem;
        }

        .exp-workspace .exp-table-shell .data-table th,
        .exp-workspace .exp-table-shell .data-table td {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }
    }

    @media (max-width: 767px) {
        .exp-workspace {
            gap: 0.5rem;
        }

        .exp-workspace .exp-toolbar,
        .exp-workspace .exp-filterbar {
            padding: 0.35rem;
            border-radius: 1rem;
        }

        .exp-workspace .exp-search-input,
        .exp-workspace .exp-form-input,
        .exp-workspace .exp-filter-control {
            min-height: 2.5rem;
            border-radius: 0.82rem;
        }

        .exp-workspace .exp-filter-control {
            min-height: 2.3rem;
            height: 2.3rem;
        }

        .exp-workspace .exp-panel {
            border-radius: 1.1rem;
        }

        .exp-workspace .exp-panel .card-header,
        .exp-workspace .exp-panel .card-body,
        .exp-workspace .exp-form-scroll > div {
            padding-left: 0.72rem;
            padding-right: 0.72rem;
        }

        .exp-workspace .exp-table-shell .data-table th,
        .exp-workspace .exp-table-shell .data-table td {
            padding-left: 0.68rem;
            padding-right: 0.68rem;
        }
    }

    @media (min-width: 1024px) {
        .exp-workspace .exp-table-shell .data-table th {
            padding: 0.65rem 0.8rem;
        }

        .exp-workspace .exp-table-shell .data-table td {
            padding: 0.68rem 0.8rem;
        }
    }
</style>

<div x-data="expensesPage()" x-init="load()" class="workspace-screen exp-workspace w-full">
    <div class="grid w-full lg:flex-1 lg:min-h-0 grid-cols-1 gap-2 lg:grid-cols-3 lg:grid-rows-1">

        {{-- ===== LEFT: Expense List (table) ===== --}}
        <div class="flex lg:min-h-0 flex-col lg:overflow-hidden lg:col-span-2">

            {{-- Search toolbar --}}
            <div class="exp-toolbar mb-1 flex shrink-0 flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input x-model="filter.search" @input.debounce.400ms="load()" type="text"
                        class="form-input-custom exp-search-input pl-10 pr-10 w-full text-sm" placeholder="Search expenses, description...">
                    <button x-show="filter.search" @click="filter.search = ''; load()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Filter bar --}}
            <div class="exp-filterbar mb-1 flex shrink-0 flex-wrap items-center gap-1.5 relative z-20">
                {{-- Category dropdown --}}
                <div class="relative" x-data="{ catOpen: false }" @click.away="catOpen = false">
                    <button type="button" @click="catOpen = !catOpen"
                        :class="filter.category_id ? 'border-primary-400 bg-primary-50 text-primary-700' : 'border-gray-300 bg-white text-gray-700'"
                        class="exp-filter-control flex items-center gap-1.5 text-sm pl-3 pr-2 rounded-lg border shadow-sm hover:shadow transition-all cursor-pointer">
                        <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span x-text="filter.category_id ? categories.find(c => c.id == filter.category_id)?.name || 'Category' : 'All Categories'"></span>
                        <svg class="w-3 h-3 ml-0.5 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="catOpen" x-cloak x-transition.origin.top.left
                        class="absolute top-full left-0 mt-1 w-56 z-50 border border-gray-200 bg-white shadow-xl rounded-xl p-1 max-h-64 overflow-y-auto">
                        <button type="button" @click="filter.category_id = ''; load(); catOpen = false"
                            class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors"
                            :class="!filter.category_id ? 'bg-slate-900 text-white' : 'text-slate-700 hover:bg-slate-50'">
                            <span class="font-medium">All Categories</span>
                        </button>
                        <template x-for="c in categories" :key="c.id">
                            <button type="button" @click="filter.category_id = c.id; load(); catOpen = false"
                                class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-left text-sm transition-colors"
                                :class="filter.category_id == c.id ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-50'">
                                <span class="font-medium" x-text="c.name"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Date From --}}
                <input x-model="filter.date_from" @change="load()" type="date"
                    class="exp-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="From date">

                {{-- Date To --}}
                <input x-model="filter.date_to" @change="load()" type="date"
                    class="exp-filter-control text-sm pl-3 pr-2 rounded-lg border border-gray-300 bg-white shadow-sm hover:shadow transition-all cursor-pointer" title="To date">

                {{-- Manage Categories --}}
                <button @click="showCat = true; loadCategories()"
                    class="exp-filter-control flex items-center gap-1 text-xs text-gray-600 hover:text-gray-800 font-medium px-3 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Categories
                </button>

                {{-- Clear all filters --}}
                <button x-show="filter.search || filter.category_id || filter.date_from || filter.date_to"
                    @click="filter.search = ''; filter.category_id = ''; filter.date_from = ''; filter.date_to = ''; load()"
                    class="exp-filter-control flex items-center gap-1 text-xs text-red-600 hover:text-red-700 font-semibold px-3 rounded-lg border border-red-200 hover:bg-red-50 transition-colors cursor-pointer">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear
                </button>
            </div>

            {{-- Table --}}
            <div class="card exp-panel relative flex min-h-0 flex-1 flex-col" style="z-index:0;">
                {{-- Overlay loader --}}
                <div x-show="loading && !firstLoad" x-cloak x-transition.opacity
                    class="absolute inset-0 bg-white/70 flex items-center justify-center rounded-xl" style="z-index:10">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-7 h-7 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span class="text-xs text-gray-400 font-medium">Loading...</span>
                    </div>
                </div>

                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        Expense Ledger (<span x-text="items.length"></span>)
                    </h3>
                    <button @click="load()" class="text-xs text-primary-600 hover:text-primary-800 font-medium">Refresh</button>
                </div>

                <div class="exp-table-shell min-h-0 flex-1 overflow-hidden">
                    <div class="h-full overflow-y-auto overscroll-contain">
                    <table class="data-table w-full">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50">
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">#</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Category</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Description</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Amount</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Payment</th>
                                <th class="px-3 py-2 text-left text-[11px] font-semibold text-gray-600 uppercase">Date</th>
                                <th class="px-3 py-2 text-center text-[11px] font-semibold text-gray-600 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <template x-for="(e, i) in items" :key="e.id">
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-3 py-2">
                                        <span class="text-gray-400 text-sm" x-text="i+1"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="text-sm font-medium text-gray-800" x-text="e.category ? e.category.name : '-'"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <p class="text-sm text-gray-700 truncate max-w-[220px]" x-text="e.description || '-'"></p>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="font-semibold text-red-600" x-text="'₹' + Number(e.amount).toFixed(2)"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full" :class="{
                                            'bg-green-100 text-green-700': e.payment_method === 'cash',
                                            'bg-blue-100 text-blue-700': e.payment_method === 'upi',
                                            'bg-purple-100 text-purple-700': e.payment_method === 'card',
                                            'bg-gray-100 text-gray-700': e.payment_method === 'bank_transfer'
                                        }" x-text="e.payment_method === 'bank_transfer' ? 'Bank' : (e.payment_method || 'cash').toUpperCase()"></span>
                                    </td>
                                    <td class="px-3 py-2">
                                        <div class="text-sm leading-tight" x-text="fmtDate(e.expense_date)"></div>
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <button @click="edit(e)" class="p-1.5 rounded-lg text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition" title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button @click="remove(e)" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0 && !loading">
                                <td colspan="7" class="text-center py-12">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <p class="text-gray-400 font-medium">No expenses found</p>
                                    <p class="text-gray-300 text-sm mt-1">Add one using the form on the right</p>
                                </td>
                            </tr>
                            <template x-if="loading && firstLoad">
                                <template x-for="i in 8" :key="'sk'+i">
                                    <tr>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-8"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-28"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-40"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-20"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-16"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-24"></div></td>
                                        <td class="px-3 py-2"><div class="skeleton h-3 w-16"></div></td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT: Add Expense Form ===== --}}
        <div class="relative order-first flex lg:min-h-0 flex-col gap-1.5 lg:order-none" style="z-index:10;">

            {{-- Add Expense Form --}}
            <div class="card exp-panel relative flex min-h-0 flex-1 flex-col">
                <div class="card-header flex shrink-0 items-center justify-between py-1.5">
                    <h3 class="font-semibold text-gray-800 text-sm">
                        <svg class="w-4 h-4 inline mr-1 -mt-0.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Expense
                    </h3>
                    <button x-show="form.amount || form.description || form.category_id" @click="resetForm()" class="text-xs text-red-400 hover:text-red-600">Clear</button>
                </div>

                <div class="exp-form-scroll min-h-0 flex-1 overflow-y-auto overscroll-contain">
                    <div class="px-4 py-2 space-y-2">
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Category *</label>
                            <select x-model="form.category_id" class="form-select-custom exp-form-input text-sm">
                                <option value="">Select Category</option>
                                <template x-for="c in categories" :key="c.id">
                                    <option :value="c.id" x-text="c.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Amount *</label>
                            <input x-model="form.amount" type="number" step="0.01" class="form-input-custom exp-form-input text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Date *</label>
                            <input x-model="form.expense_date" type="date" class="form-input-custom exp-form-input text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Payment Method</label>
                            <div class="flex gap-2">
                                <template x-for="m in ['cash','upi','card']" :key="m">
                                    <button type="button" @click="form.payment_method = m" class="flex-1 py-2 px-2 text-xs font-medium rounded-lg border-2 transition-all flex items-center justify-center gap-1" :class="form.payment_method === m ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                        <span x-text="m.toUpperCase()"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-medium text-gray-600 mb-1 block">Description</label>
                            <textarea x-model="form.description" rows="3" class="form-input-custom exp-form-input text-sm"
                                placeholder="Optional note..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="shrink-0 border-t px-4 py-3">
                    <button @click="saveNew()" class="btn-primary w-full py-3 text-base font-semibold" :disabled="saving">
                        <span x-show="saving" class="spinner mr-2"></span>
                        <svg x-show="!saving" class="w-4 h-4 inline mr-1.5 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Expense
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT EXPENSE MODAL ==================== --}}
    <div x-show="showModal" class="modal-overlay" x-cloak @click.self="showModal = false" x-transition>
        <div class="modal-container modal-lg">
            <div class="modal-header">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Expense
                </h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select x-model="editForm.category_id" class="form-select-custom">
                            <option value="">Select</option>
                            <template x-for="c in categories" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input x-model="editForm.amount" type="number" step="0.01" class="form-input-custom">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                        <input x-model="editForm.expense_date" type="date" class="form-input-custom">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select x-model="editForm.payment_method" class="form-select-custom">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="upi">UPI</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea x-model="editForm.description" class="form-input-custom" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button @click="showModal = false" class="btn-secondary">Cancel</button>
                <button @click="saveEdit()" class="btn-primary" :disabled="saving">
                    <span x-show="saving" class="spinner mr-1"></span>
                    Update
                </button>
            </div>
        </div>
    </div>

    {{-- ==================== CATEGORIES MODAL ==================== --}}
    <div x-show="showCat" class="modal-overlay" x-cloak @click.self="showCat = false" x-transition>
        <div class="modal-container" style="max-width:500px">
            <div class="modal-header">
                <h3 class="text-lg font-semibold">Expense Categories</h3>
                <button @click="showCat = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="modal-body">
                <div class="space-y-2 mb-4 max-h-64 overflow-y-auto">
                    <template x-for="c in catList" :key="c.id">
                        <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded group">
                            <template x-if="editingCat !== c.id">
                                <div class="flex flex-col gap-2 w-full sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <span class="font-medium" x-text="c.name"></span>
                                        <span class="text-xs text-gray-400 ml-2" x-text="(c.expenses_count || 0) + ' expenses'"></span>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button @click="editingCat = c.id; editCatName = c.name" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button @click="deleteCat(c)" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="editingCat === c.id">
                                <div class="flex flex-col gap-2 w-full sm:flex-row sm:items-center">
                                    <input x-model="editCatName" type="text" class="form-input-custom flex-1 text-sm" @keydown.enter="updateCat(c)" @keydown.escape="editingCat = null">
                                    <button @click="updateCat(c)" class="btn-primary text-xs px-2 py-1 w-full sm:w-auto">Save</button>
                                    <button @click="editingCat = null" class="btn-secondary text-xs px-2 py-1 w-full sm:w-auto">Cancel</button>
                                </div>
                            </template>
                        </div>
                    </template>
                    <div x-show="catList.length === 0" class="text-center text-gray-400 py-4">No categories yet</div>
                </div>
                <div class="flex flex-col gap-2 pt-2 border-t sm:flex-row">
                    <input x-model="catForm.name" type="text" class="form-input-custom flex-1" placeholder="New category name" @keydown.enter="saveCat()">
                    <button @click="saveCat()" class="btn-primary w-full sm:w-auto">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function expensesPage() {
    return {
        items: [], categories: [], catList: [], showModal: false, showCat: false,
        editing: null, saving: false, loading: true, firstLoad: true,
        editingCat: null, editCatName: '',
        form: { category_id: '', amount: '', expense_date: new Date().toISOString().split('T')[0], payment_method: 'cash', description: '' },
        editForm: { category_id: '', amount: '', expense_date: '', payment_method: 'cash', description: '' },
        catForm: { name: '' },
        filter: { search: '', category_id: '', date_from: '', date_to: '' },

        fmtDate(d) {
            if (!d) return '-';
            const dt = new Date(d);
            if (isNaN(dt)) return d;
            return dt.toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' });
        },

        resetForm() {
            this.form = { category_id: '', amount: '', expense_date: new Date().toISOString().split('T')[0], payment_method: 'cash', description: '' };
        },

        async load() {
            this.loading = true;
            const params = new URLSearchParams();
            if (this.filter.category_id) params.set('category_id', this.filter.category_id);
            if (this.filter.date_from) params.set('date_from', this.filter.date_from);
            if (this.filter.date_to) params.set('date_to', this.filter.date_to);
            if (this.filter.search) params.set('search', this.filter.search);
            const qs = params.toString() ? '?' + params.toString() : '';
            const [r, c] = await Promise.all([
                RepairBox.ajax('/expenses' + qs),
                RepairBox.ajax('/expenses/categories')
            ]);
            if (r.data) this.items = r.data;
            if (Array.isArray(c)) this.categories = c;
            else if (c.data) this.categories = c.data;
            this.loading = false;
            this.firstLoad = false;
        },

        async loadCategories() {
            const c = await RepairBox.ajax('/expenses/categories');
            if (Array.isArray(c)) this.catList = c;
            else if (c.data) this.catList = c.data;
        },

        async saveNew() {
            if (!this.form.category_id) { RepairBox.toast('Please select a category', 'error'); return; }
            if (!this.form.amount || parseFloat(this.form.amount) <= 0) { RepairBox.toast('Enter a valid amount', 'error'); return; }
            if (!this.form.expense_date) { RepairBox.toast('Please select a date', 'error'); return; }
            this.saving = true;
            const r = await RepairBox.ajax('/expenses', 'POST', this.form);
            this.saving = false;
            if (r.success !== false) {
                RepairBox.toast('Expense recorded', 'success');
                this.resetForm();
                this.load();
            }
        },

        edit(e) {
            this.editing = e.id;
            this.editForm = {
                category_id: e.category_id,
                amount: e.amount,
                expense_date: e.expense_date ? e.expense_date.substring(0, 10) : '',
                payment_method: e.payment_method || 'cash',
                description: e.description || ''
            };
            this.showModal = true;
        },

        async saveEdit() {
            this.saving = true;
            const r = await RepairBox.ajax(`/expenses/${this.editing}`, 'PUT', this.editForm);
            this.saving = false;
            if (r.success !== false) { RepairBox.toast('Updated', 'success'); this.showModal = false; this.load(); }
        },

        async remove(e) {
            if (!await RepairBox.confirm('Delete this expense?')) return;
            const r = await RepairBox.ajax(`/expenses/${e.id}`, 'DELETE');
            if (r.success !== false) { RepairBox.toast('Deleted', 'success'); this.load(); }
        },

        async saveCat() {
            if (!this.catForm.name.trim()) return;
            const r = await RepairBox.ajax('/expenses/categories', 'POST', this.catForm);
            if (r.success !== false) {
                RepairBox.toast('Category added', 'success');
                this.catForm = { name: '' };
                this.loadCategories();
                this.load();
            }
        },

        async updateCat(c) {
            if (!this.editCatName.trim()) return;
            const r = await RepairBox.ajax(`/expenses/categories/${c.id}`, 'PUT', { name: this.editCatName });
            if (r.success !== false) {
                RepairBox.toast('Category updated', 'success');
                this.editingCat = null;
                this.loadCategories();
                this.load();
            }
        },

        async deleteCat(c) {
            if (!await RepairBox.confirm(`Delete category "${c.name}"?`)) return;
            const r = await RepairBox.ajax(`/expenses/categories/${c.id}`, 'DELETE');
            if (r.success !== false) {
                RepairBox.toast('Category deleted', 'success');
                this.loadCategories();
                this.load();
            } else {
                RepairBox.toast(r.message || 'Cannot delete category', 'error');
            }
        }
    };
}
</script>
@endpush
