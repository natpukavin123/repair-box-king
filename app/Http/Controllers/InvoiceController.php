<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Recharge;
use App\Models\Repair;
use App\Http\Requests\InvoiceRequest;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Invoice::with(['customer', 'creator', 'payments'])->withCount('items')
                ->when(request('search'), fn($q, $s) => $q->where('invoice_number', 'like', "%{$s}%")->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")))
                ->when(request('payment_status'), fn($q, $s) => $q->where('payment_status', $s))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 15));

            // append computed paid_amount and balance_due
            $data->getCollection()->transform(fn($inv) => $this->appendPaymentSummary($inv));

            return response()->json($data);
        }
        return view('modules.invoices.index');
    }

    public function create()
    {
        $canViewCostPrice = auth()->user()->isAdmin()
            || auth()->user()->isSuperAdmin()
            || auth()->user()->hasPermission('pos.view_cost_price');

        $brandModelMap = Brand::where('status', 'active')->orderBy('name')->get(['name', 'models'])
            ->map(fn($b) => ['name' => $b->name, 'models' => $b->models ?? []])->values();
        $brands = $brandModelMap->pluck('name');

        // Gather unique issues from past repairs for auto-suggest
        $dbProblemSuggestions = \App\Models\Repair::whereNotNull('problem_description')
            ->where('problem_description', '!=', '')
            ->pluck('problem_description')
            ->flatMap(fn($desc) => array_map('trim', explode(',', $desc)))
            ->filter(fn($s) => strlen($s) > 1)
            ->unique()
            ->values()
            ->toArray();

        return view('modules.pos.billing', compact('canViewCostPrice', 'brands', 'brandModelMap', 'dbProblemSuggestions'));
    }

    public function store(InvoiceRequest $request, InvoiceService $service)
    {
        $invoice = $service->create($request->validated());
        return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Invoice created']);
    }

    public function pay(Invoice $invoice, Request $request, InvoiceService $service)
    {
        if ($invoice->isLocked()) {
            return response()->json(['success' => false, 'message' => 'Invoice is already fully paid and locked'], 422);
        }

        $validated = $request->validate([
            'payments'                          => 'required|array|min:1',
            'payments.*.payment_method'         => 'required|string|max:50',
            'payments.*.amount'                 => 'required|numeric|min:0.01',
            'payments.*.transaction_reference'  => 'nullable|string|max:100',
        ]);

        $invoice = $service->addPayment($invoice, $validated['payments']);
        return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Payment recorded successfully']);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('items', 'payments', 'customer', 'creator');
        return response()->json($this->appendPaymentSummary($invoice));
    }

    private function appendPaymentSummary(Invoice $invoice): Invoice
    {
        $paid = $invoice->payments->sum('amount');
        $invoice->setAttribute('paid_amount', round((float) $paid, 2));
        $invoice->setAttribute('balance_due', round(max(0, (float) $invoice->final_amount - (float) $paid), 2));
        return $invoice;
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items', 'payments', 'customer', 'creator');
        return view('modules.invoices.print', compact('invoice'));
    }

    public function customerRecharges(Request $request)
    {
        $customerId = $request->input('customer_id');
        $mobile     = $request->input('mobile');

        if (!$customerId && !$mobile) return response()->json(['success' => true, 'data' => []]);

        // Exclude recharges already linked to an invoice
        $alreadyBilled = InvoiceItem::where('item_type', 'recharge')
            ->where('is_linked', true)->whereNotNull('linked_id')
            ->pluck('linked_id');

        $recharges = Recharge::with('provider')
            ->where('status', 'success')
            ->whereNotIn('id', $alreadyBilled)
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($mobile, fn($q, $m) => $q->where('mobile_number', 'like', "%{$m}%"))
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'mobile'      => $r->mobile_number,
                'label'       => ($r->provider?->name ?? 'Recharge') . ' — ' . $r->mobile_number,
                'description' => ($r->plan_name ?: 'Recharge') . ' · ₹' . number_format($r->recharge_amount, 2),
                'amount'      => (float) $r->recharge_amount,
                'date'        => $r->created_at->format('d M Y'),
            ]);

        return response()->json(['success' => true, 'data' => $recharges]);
    }

    public function customerRepairs(Request $request)
    {
        $customerId = $request->input('customer_id');
        $search     = $request->input('search');

        if (!$customerId && !$search) return response()->json(['success' => true, 'data' => []]);

        // Exclude repairs already linked to an invoice
        $alreadyBilled = InvoiceItem::where('item_type', 'repair')
            ->where('is_linked', true)->whereNotNull('linked_id')
            ->pluck('linked_id');

        $repairs = Repair::where(function ($q) use ($customerId, $search) {
                if ($customerId) $q->where('customer_id', $customerId);
                if ($search) {
                    $q->where(function ($sq) use ($search) {
                        $sq->where('ticket_number', 'like', "%{$search}%")
                           ->orWhere('device_brand', 'like', "%{$search}%")
                           ->orWhere('device_model', 'like', "%{$search}%")
                           ->orWhere('imei', 'like', "%{$search}%");
                    });
                }
            })
            ->whereNotIn('status', ['cancelled'])
            ->whereNotIn('id', $alreadyBilled)
            ->latest()
            ->limit(50)
            ->get()
            ->map(fn($r) => [
                'id'          => $r->id,
                'label'       => $r->ticket_number . ' — ' . $r->device_brand . ' ' . $r->device_model,
                'description' => $r->problem_description,
                'amount'      => (float) ($r->service_charge ?? $r->estimated_cost ?? 0),
                'status'      => $r->status,
                'date'        => $r->created_at->format('d M Y'),
            ]);

        return response()->json(['success' => true, 'data' => $repairs]);
    }
}
