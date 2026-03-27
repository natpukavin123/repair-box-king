@extends('layouts.app')
@section('page-title', 'Cost & Profit - ' . $repair->ticket_number)

@section('content')
<div class="max-w-5xl mx-auto">

    <!-- Breadcrumb & Header -->
    <div class="mb-5">
        <div class="flex items-center gap-2 text-sm mb-2">
            <a href="/admin/repairs" class="text-primary-600 hover:text-primary-800">Repairs</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="/admin/repairs/{{ $repair->id }}" class="text-primary-600 hover:text-primary-800">{{ $repair->ticket_number }}</a>
            <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-500">Cost & Profit</span>
        </div>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Cost & Profit Breakdown</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $repair->ticket_number }} &mdash;
                    {{ $repair->customer?->name ?? 'Walk-in' }} &mdash;
                    {{ $repair->device_brand }} {{ $repair->device_model }}
                </p>
            </div>
            <a href="/admin/repairs/{{ $repair->id }}" class="btn-secondary text-sm inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Repair
            </a>
        </div>
    </div>

    <!-- ===== SUMMARY CARDS ===== -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Grand Total</div>
            <div class="text-xl font-bold text-primary-600">₹{{ number_format($repair->grand_total, 2) }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Customer charged</div>
        </div>
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Total Cost</div>
            <div class="text-xl font-bold text-red-600">₹{{ number_format($repair->total_cost, 2) }}</div>
            <div class="text-xs text-gray-400 mt-0.5">Parts + Vendor</div>
        </div>
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Profit</div>
            <div class="text-xl font-bold {{ $repair->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($repair->profit, 2) }}</div>
            @if($repair->grand_total > 0)
                <div class="text-xs text-gray-400 mt-0.5">{{ number_format(($repair->profit / $repair->grand_total) * 100, 1) }}% margin</div>
            @endif
        </div>
        <div class="bg-white rounded-xl border shadow-sm p-4">
            <div class="text-xs font-semibold text-gray-500 uppercase mb-1">Collection</div>
            <div class="text-xl font-bold {{ $repair->balance_due > 0 ? 'text-orange-500' : 'text-green-600' }}">₹{{ number_format($repair->net_paid, 2) }}</div>
            @if($repair->balance_due > 0)
                <div class="text-xs text-red-500 mt-0.5">Due: ₹{{ number_format($repair->balance_due, 2) }}</div>
            @else
                <div class="text-xs text-green-500 mt-0.5">Fully Paid</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- ===== REVENUE BREAKDOWN ===== -->
        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Revenue (Customer Charges)
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Parts Charged ({{ $repair->parts->count() }} items)</span>
                    <span class="text-sm font-semibold">₹{{ number_format($repair->total_parts, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Services Charged ({{ $repair->repairServices->count() }} services)</span>
                    <span class="text-sm font-semibold">₹{{ number_format($repair->total_services, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Our Service Fee (Fixed)</span>
                    <span class="text-sm font-semibold">₹{{ number_format($repair->service_charge, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t-2">
                    <span class="text-sm font-bold text-gray-800">Grand Total</span>
                    <span class="text-base font-bold text-primary-600">₹{{ number_format($repair->grand_total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- ===== COST BREAKDOWN ===== -->
        <div class="bg-white rounded-xl border shadow-sm">
            <div class="px-5 py-4 border-b">
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                    Cost (Your Expenses)
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Parts Purchase Cost</span>
                    <span class="text-sm font-semibold text-red-600">₹{{ number_format($repair->parts_cost, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Vendor Charges</span>
                    <span class="text-sm font-semibold text-red-600">₹{{ number_format($repair->vendor_charges, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t-2">
                    <span class="text-sm font-bold text-gray-800">Total Cost</span>
                    <span class="text-base font-bold text-red-600">₹{{ number_format($repair->total_cost, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t">
                    <span class="text-sm font-bold text-gray-800">Profit</span>
                    <span class="text-base font-bold {{ $repair->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($repair->profit, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PARTS DETAIL TABLE ===== -->
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Parts Used ({{ $repair->parts->count() }})
            </h3>
        </div>
        @if($repair->parts->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Part Name</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Selling Price</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Customer Total</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Purchase Cost</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Cost Total</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($repair->parts as $i => $rp)
                    @php
                        $customerTotal = $rp->cost_price * $rp->quantity;
                        $actualCost = $rp->part ? $rp->part->cost_price : $rp->cost_price;
                        $costTotal = $actualCost * $rp->quantity;
                        $partProfit = $customerTotal - $costTotal;
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-800">{{ $rp->part?->name ?? 'Unknown Part' }}</div>
                            @if($rp->part?->sku)
                                <div class="text-xs text-gray-400">SKU: {{ $rp->part->sku }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center text-sm">{{ $rp->quantity }}</td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($rp->cost_price, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium">₹{{ number_format($customerTotal, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm text-red-600">₹{{ number_format($actualCost, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium text-red-600">₹{{ number_format($costTotal, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold {{ $partProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($partProfit, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-5 py-3 text-sm" colspan="4">Total</td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($repair->total_parts, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm"></td>
                        <td class="px-5 py-3 text-right text-sm text-red-600">₹{{ number_format($repair->parts_cost, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm {{ ($repair->total_parts - $repair->parts_cost) >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($repair->total_parts - $repair->parts_cost, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <p class="text-sm">No parts used in this repair</p>
        </div>
        @endif
    </div>

    <!-- ===== SERVICES DETAIL TABLE ===== -->
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Services ({{ $repair->repairServices->count() }})
            </h3>
        </div>
        @if($repair->repairServices->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Service</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Vendor</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Customer Charge</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Vendor Charge</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($repair->repairServices as $i => $svc)
                    @php
                        $svcProfit = (float)$svc->customer_charge - (float)$svc->vendor_charge;
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-800">{{ $svc->service_type_name }}</div>
                            @if($svc->reference_no)
                                <div class="text-xs text-gray-400">Ref: {{ $svc->reference_no }}</div>
                            @endif
                            @if($svc->description)
                                <div class="text-xs text-gray-400 mt-0.5">{{ $svc->description }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $svc->vendor?->name ?? '-' }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium">₹{{ number_format($svc->customer_charge, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-medium text-red-600">₹{{ number_format($svc->vendor_charge, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold {{ $svcProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($svcProfit, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-5 py-3 text-sm" colspan="5">Total</td>
                        <td class="px-5 py-3 text-right text-sm">₹{{ number_format($repair->total_services, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm text-red-600">₹{{ number_format($repair->vendor_charges, 2) }}</td>
                        <td class="px-5 py-3 text-right text-sm {{ ($repair->total_services - $repair->vendor_charges) >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($repair->total_services - $repair->vendor_charges, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <p class="text-sm">No services for this repair</p>
        </div>
        @endif
    </div>

    <!-- ===== PAYMENTS TABLE ===== -->
    <div class="bg-white rounded-xl border shadow-sm mb-6">
        <div class="px-5 py-4 border-b">
            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Payments ({{ $repair->payments->count() }})
            </h3>
        </div>
        @if($repair->payments->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Reference</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Direction</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($repair->payments as $i => $pay)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 text-sm font-medium text-gray-800">{{ ucfirst($pay->payment_type) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $pay->payment_method)) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $pay->reference_number ?? '-' }}</td>
                        <td class="px-5 py-3 text-sm">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ ($pay->direction ?? 'IN') === 'IN' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ($pay->direction ?? 'IN') === 'IN' ? 'Received' : 'Refund' }}</span>
                        </td>
                        <td class="px-5 py-3 text-right text-sm font-medium {{ ($pay->direction ?? 'IN') === 'IN' ? 'text-green-600' : 'text-red-600' }}">{{ ($pay->direction ?? 'IN') === 'IN' ? '+' : '-' }}₹{{ number_format($pay->amount, 2) }}</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $pay->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td class="px-5 py-3 text-sm" colspan="5">Net Collected</td>
                        <td class="px-5 py-3 text-right text-sm text-green-600">₹{{ number_format($repair->net_paid, 2) }}</td>
                        <td class="px-5 py-3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-8 text-center text-gray-400">
            <p class="text-sm">No payments recorded</p>
        </div>
        @endif
    </div>

    <!-- ===== FINAL PROFIT SUMMARY ===== -->
    <div class="bg-gradient-to-r {{ $repair->profit >= 0 ? 'from-green-50 to-emerald-50 border-green-200' : 'from-red-50 to-orange-50 border-red-200' }} rounded-xl border shadow-sm p-6 mb-6">
        <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide mb-4">Profit Summary</h3>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Revenue</div>
                <div class="text-lg font-bold text-gray-800">₹{{ number_format($repair->grand_total, 2) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Parts Cost</div>
                <div class="text-lg font-bold text-red-600">₹{{ number_format($repair->parts_cost, 2) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Vendor Cost</div>
                <div class="text-lg font-bold text-red-600">₹{{ number_format($repair->vendor_charges, 2) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Net Profit</div>
                <div class="text-lg font-bold {{ $repair->profit >= 0 ? 'text-green-600' : 'text-red-600' }}">₹{{ number_format($repair->profit, 2) }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500 mb-0.5">Collected</div>
                <div class="text-lg font-bold {{ $repair->balance_due > 0 ? 'text-orange-500' : 'text-green-600' }}">₹{{ number_format($repair->net_paid, 2) }}</div>
            </div>
        </div>
    </div>

</div>
@endsection
