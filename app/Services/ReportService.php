<?php

namespace App\Services;

use App\Models\{Invoice, InvoicePayment, Repair, RepairPayment, Recharge, Expense, LedgerTransaction, Purchase, Customer, Refund, Inventory};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $today = Carbon::today();

        // ---------- Sales Progression (Today) ----------
        $newCustomersToday = Customer::whereDate('created_at', $today)->count();

        // Repeat customers: customers who existed before today AND have invoice/repair today
        $repeatCustomerIds = Customer::where('created_at', '<', $today)
            ->whereHas('invoices', fn($q) => $q->whereDate('created_at', $today))
            ->pluck('id')
            ->merge(
                Customer::where('created_at', '<', $today)
                    ->whereHas('repairs', fn($q) => $q->whereDate('created_at', $today)->where('record_type', 'original'))
                    ->pluck('id')
            )
            ->unique()
            ->count();

        $grossSalePayments = InvoicePayment::whereDate('created_at', $today)->sum('amount');
        $customerPurchaseAmount = Invoice::whereDate('created_at', $today)->whereNotNull('customer_id')->sum('final_amount');
        $purchaseOrderAmount = Purchase::whereDate('purchase_date', $today)->sum('total_amount');
        $refundAmount = Refund::whereDate('created_at', $today)->sum('refund_amount');

        // ---------- Repair Ticket Counts by Status ----------
        $repairCounts = Repair::where('record_type', 'original')
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // ---------- Sales by Category ----------
        $posSalesToday = Invoice::whereDate('created_at', $today)->sum('final_amount');
        $repairSalesToday = RepairPayment::whereDate('created_at', $today)
            ->where('direction', 'IN')->sum('amount');
        $totalSalesToday = $posSalesToday + $repairSalesToday;

        // ---------- Today's Activity Counts ----------
        $todaySalesCount    = Invoice::whereDate('created_at', $today)->count();
        $todayRepairsCount  = Repair::where('record_type', 'original')->whereDate('created_at', $today)->count();
        $todayRechargesCount = Recharge::whereDate('created_at', $today)->count();
        $todayExpensesCount = Expense::whereDate('expense_date', $today)->count();

        // ---------- Low Stock ----------
        $lowStockCount = Inventory::where('current_stock', '<=', 5)->count();

        return [
            'sales_chart'             => $this->getSalesChart(),

            // Sales Progression (Today)
            'new_customers_today'     => $newCustomersToday,
            'repeat_customers_today'  => $repeatCustomerIds,
            'gross_sale_payments'     => $grossSalePayments,
            'customer_purchase_amount'=> $customerPurchaseAmount,
            'purchase_order_amount'   => $purchaseOrderAmount,
            'refund_amount'           => $refundAmount,

            // Repair ticket counts
            'repair_counts' => [
                'received'    => $repairCounts['received'] ?? 0,
                'in_progress' => $repairCounts['in_progress'] ?? 0,
                'completed'   => $repairCounts['completed'] ?? 0,
                'payment'     => $repairCounts['payment'] ?? 0,
                'closed'      => $repairCounts['closed'] ?? 0,
            ],

            // Sales by category
            'sales_by_category' => [
                'pos_sales'    => $posSalesToday,
                'repair_sales' => $repairSalesToday,
                'total_sales'  => $totalSalesToday,
            ],

            // Today's activity counts
            'today_sales_count'     => $todaySalesCount,
            'today_repairs_count'   => $todayRepairsCount,
            'today_recharges_count' => $todayRechargesCount,
            'today_expenses_count'  => $todayExpensesCount,

            // Low stock
            'low_stock_count' => $lowStockCount,
        ];
    }

    private function getSalesChart(): array
    {
        $labels = [];
        $invoiceSales = [];
        $repairSales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $invoiceSales[] = (float) Invoice::whereDate('created_at', $date)->sum('final_amount');
            $repairSales[] = (float) RepairPayment::whereDate('created_at', $date)
                ->where('direction', 'IN')->sum('amount');
        }
        return [
            'labels' => $labels,
            'data' => $invoiceSales,
            'repair_data' => $repairSales,
        ];
    }

    public function getSalesReport(string $from, string $to): array
    {
        return [
            'invoices' => Invoice::with('customer', 'items')
                ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
                ->get(),
            'total' => Invoice::whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])->sum('final_amount'),
        ];
    }

    public function getProfitReport(string $from, string $to): array
    {
        $revenue = LedgerTransaction::where('direction', 'IN')
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->sum('amount');
        $expenses = LedgerTransaction::where('direction', 'OUT')
            ->whereBetween('created_at', [$from, Carbon::parse($to)->endOfDay()])
            ->sum('amount');

        return [
            'revenue' => $revenue,
            'expenses' => $expenses,
            'profit' => $revenue - $expenses,
        ];
    }
}
