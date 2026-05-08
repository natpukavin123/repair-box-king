<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\{Repair, RepairPayment, Invoice, InvoicePayment, Recharge, Expense, Purchase, Customer};

class ReportController extends Controller
{
    public function index()
    {
        return view('modules.reports.index');
    }

    // ─── Repairs ────────────────────────────────────────────────────────────
    public function repairs(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], Carbon::parse($data['to'])->endOfDay()];

        $repairs = Repair::with('customer', 'payments')
            ->where('record_type', 'original')
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->get();

        $customerIds  = $repairs->pluck('customer_id')->filter()->unique();
        $newCustIds   = Customer::whereIn('id', $customerIds)->whereDate('created_at', '>=', $from)->pluck('id');
        $repeatCustIds = $customerIds->diff($newCustIds);

        $totalRevenue = RepairPayment::whereHas('repair', fn($q) => $q->whereBetween('created_at', [$from, $to])->where('record_type','original'))
            ->where('direction', 'IN')->sum('amount');
        $totalRefunded = RepairPayment::whereHas('repair', fn($q) => $q->whereBetween('created_at', [$from, $to])->where('record_type','original'))
            ->where('direction', 'OUT')->sum('amount');

        $statusCounts = $repairs->groupBy('status')->map->count();

        return response()->json([
            'summary' => [
                'total_tickets'   => $repairs->count(),
                'total_revenue'   => round($totalRevenue, 2),
                'total_refunded'  => round($totalRefunded, 2),
                'net_revenue'     => round($totalRevenue - $totalRefunded, 2),
                'avg_ticket_value'=> $repairs->count() ? round($totalRevenue / $repairs->count(), 2) : 0,
                'new_customers'   => $newCustIds->count(),
                'repeat_customers'=> $repeatCustIds->count(),
                'walk_in_count'   => $repairs->whereNull('customer_id')->count(),
                'status_counts'   => $statusCounts,
            ],
            'list' => $repairs->map(fn($r) => [
                'id'             => $r->id,
                'ticket'         => $r->ticket_number,
                'tracking'       => $r->tracking_id,
                'customer'       => $r->customer?->name ?? 'Walk-in',
                'mobile'         => $r->customer?->mobile_number ?? '—',
                'device'         => trim(($r->device_brand ?? '') . ' ' . ($r->device_model ?? '')),
                'status'         => $r->status,
                'estimated'      => $r->estimated_cost,
                'final'          => $r->final_cost,
                'paid'           => $r->total_paid,
                'balance'        => $r->balance_due,
                'is_new_cust'    => $newCustIds->contains($r->customer_id),
                'date'           => $r->created_at->format('d M Y'),
            ]),
        ]);
    }

    // ─── Sales (Invoices) ────────────────────────────────────────────────────
    public function sales(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], Carbon::parse($data['to'])->endOfDay()];

        $invoices = Invoice::with('customer', 'items')
            ->whereBetween('created_at', [$from, $to])
            ->latest()->get();

        $customerIds  = $invoices->pluck('customer_id')->filter()->unique();
        $newCustIds   = Customer::whereIn('id', $customerIds)->whereDate('created_at', '>=', $from)->pluck('id');
        $repeatCustIds = $customerIds->diff($newCustIds);

        $totalSales    = $invoices->sum('final_amount');
        $totalDiscount = $invoices->sum('discount');
        $totalItems    = $invoices->sum(fn($i) => $i->items->sum('quantity'));

        return response()->json([
            'summary' => [
                'total_invoices'  => $invoices->count(),
                'total_sales'     => round($totalSales, 2),
                'total_discount'  => round($totalDiscount, 2),
                'total_items_sold'=> (int) $totalItems,
                'avg_sale'        => $invoices->count() ? round($totalSales / $invoices->count(), 2) : 0,
                'new_customers'   => $newCustIds->count(),
                'repeat_customers'=> $repeatCustIds->count(),
                'walk_in_count'   => $invoices->whereNull('customer_id')->count(),
            ],
            'list' => $invoices->map(fn($inv) => [
                'id'         => $inv->id,
                'invoice_no' => $inv->invoice_number,
                'customer'   => $inv->customer?->name ?? 'Walk-in',
                'mobile'     => $inv->customer?->mobile_number ?? '—',
                'items'      => $inv->items->sum('quantity'),
                'amount'     => $inv->final_amount,
                'discount'   => $inv->discount,
                'status'     => $inv->payment_status,
                'is_new_cust'=> $newCustIds->contains($inv->customer_id),
                'date'       => Carbon::parse($inv->created_at)->format('d M Y'),
            ]),
        ]);
    }

    // ─── Recharges ───────────────────────────────────────────────────────────
    public function recharges(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], Carbon::parse($data['to'])->endOfDay()];

        $recharges = Recharge::with('customer')
            ->whereBetween('created_at', [$from, $to])
            ->latest()->get();

        $customerIds  = $recharges->pluck('customer_id')->filter()->unique();
        $newCustIds   = Customer::whereIn('id', $customerIds)->whereDate('created_at', '>=', $from)->pluck('id');

        return response()->json([
            'summary' => [
                'total_recharges'  => $recharges->count(),
                'total_amount'     => round($recharges->sum('recharge_amount'), 2),
                'total_commission' => round($recharges->sum('commission'), 2),
                'new_customers'    => $newCustIds->count(),
                'repeat_customers' => $customerIds->diff($newCustIds)->count(),
            ],
            'list' => $recharges->map(fn($r) => [
                'id'         => $r->id,
                'customer'   => $r->customer?->name ?? '—',
                'mobile'     => $r->mobile_number,
                'plan'       => $r->plan_name,
                'amount'     => $r->recharge_amount,
                'commission' => $r->commission,
                'method'     => $r->payment_method,
                'status'     => $r->status,
                'date'       => Carbon::parse($r->created_at)->format('d M Y'),
            ]),
        ]);
    }

    // ─── Expenses ────────────────────────────────────────────────────────────
    public function expenses(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], Carbon::parse($data['to'])->endOfDay()];

        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$from, $data['to']])
            ->latest('expense_date')->get();

        $byCategory = $expenses->groupBy(fn($e) => $e->category?->name ?? 'Uncategorized')
            ->map(fn($g) => ['count' => $g->count(), 'total' => round($g->sum('amount'), 2)])
            ->sortByDesc('total');

        return response()->json([
            'summary' => [
                'total_expenses' => $expenses->count(),
                'total_amount'   => round($expenses->sum('amount'), 2),
                'avg_expense'    => $expenses->count() ? round($expenses->sum('amount') / $expenses->count(), 2) : 0,
                'by_category'    => $byCategory,
            ],
            'list' => $expenses->map(fn($e) => [
                'id'          => $e->id,
                'category'    => $e->category?->name ?? 'Uncategorized',
                'description' => $e->description,
                'amount'      => $e->amount,
                'method'      => $e->payment_method,
                'date'        => Carbon::parse($e->expense_date)->format('d M Y'),
            ]),
        ]);
    }

    // ─── PO / Purchases ──────────────────────────────────────────────────────
    public function po(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], $data['to']];

        $purchases = Purchase::with('supplier')
            ->whereBetween('purchase_date', [$from, $to])
            ->latest('purchase_date')->get();

        $byStatus = $purchases->groupBy('status')
            ->map(fn($g) => ['count' => $g->count(), 'total' => round($g->sum('total_amount'), 2)]);

        return response()->json([
            'summary' => [
                'total_orders'  => $purchases->count(),
                'total_amount'  => round($purchases->sum('total_amount'), 2),
                'avg_order'     => $purchases->count() ? round($purchases->sum('total_amount') / $purchases->count(), 2) : 0,
                'by_status'     => $byStatus,
            ],
            'list' => $purchases->map(fn($p) => [
                'id'         => $p->id,
                'invoice_no' => $p->invoice_number,
                'supplier'   => $p->supplier?->name ?? '—',
                'amount'     => $p->total_amount,
                'status'     => $p->status,
                'notes'      => $p->notes,
                'date'       => Carbon::parse($p->purchase_date)->format('d M Y'),
            ]),
        ]);
    }

    // ─── Overview (all modules combined) ─────────────────────────────────────
    public function overview(Request $request)
    {
        $data = $request->validate(['from' => 'required|date', 'to' => 'required|date']);
        [$from, $to] = [$data['from'], Carbon::parse($data['to'])->endOfDay()];

        $repairRevenue  = RepairPayment::whereHas('repair', fn($q) => $q->whereBetween('created_at', [$from, $to])->where('record_type','original'))->where('direction','IN')->sum('amount');
        $salesRevenue   = Invoice::whereBetween('created_at', [$from, $to])->sum('final_amount');
        $rechargeRevenue= Recharge::whereBetween('created_at', [$from, $to])->sum('recharge_amount');
        $totalExpenses  = Expense::whereBetween('expense_date', [$data['from'], $data['to']])->sum('amount');
        $totalPO        = Purchase::whereBetween('purchase_date', [$data['from'], $data['to']])->sum('total_amount');

        $totalRevenue   = $repairRevenue + $salesRevenue + $rechargeRevenue;

        return response()->json([
            'repair_revenue'   => round($repairRevenue, 2),
            'sales_revenue'    => round($salesRevenue, 2),
            'recharge_revenue' => round($rechargeRevenue, 2),
            'total_revenue'    => round($totalRevenue, 2),
            'total_expenses'   => round($totalExpenses, 2),
            'total_po'         => round($totalPO, 2),
            'net_profit'       => round($totalRevenue - $totalExpenses, 2),
            'repair_count'     => Repair::where('record_type','original')->whereBetween('created_at',[$from,$to])->count(),
            'sales_count'      => Invoice::whereBetween('created_at',[$from,$to])->count(),
            'recharge_count'   => Recharge::whereBetween('created_at',[$from,$to])->count(),
            'expense_count'    => Expense::whereBetween('expense_date',[$data['from'],$data['to']])->count(),
            'po_count'         => Purchase::whereBetween('purchase_date',[$data['from'],$data['to']])->count(),
        ]);
    }

    // Legacy endpoints kept for backward compat
    public function profit(Request $request)
    {
        return $this->overview($request);
    }
}
