<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Http\Requests\InvoiceRequest;
use App\Services\InvoiceService;

class InvoiceController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Invoice::with('customer', 'creator')->withCount('items')
                ->when(request('search'), fn($q, $s) => $q->where('invoice_number', 'like', "%{$s}%")->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%")))
                ->when(request('payment_status'), fn($q, $s) => $q->where('payment_status', $s))
                ->when(request('date_from'), fn($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when(request('date_to'), fn($q, $d) => $q->whereDate('created_at', '<=', $d))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.invoices.index');
    }

    public function create()
    {
        return view('modules.pos.billing');
    }

    public function store(InvoiceRequest $request, InvoiceService $service)
    {
        $invoice = $service->create($request->validated());
        return response()->json(['success' => true, 'data' => $invoice, 'message' => 'Invoice created']);
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice->load('items', 'payments', 'customer', 'creator'));
    }

    public function print(Invoice $invoice)
    {
        $invoice->load('items', 'payments', 'customer', 'creator');
        return view('modules.invoices.print', compact('invoice'));
    }
}
