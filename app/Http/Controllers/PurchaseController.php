<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Http\Requests\PurchaseRequest;
use App\Services\PurchaseService;

class PurchaseController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Purchase::with('supplier')
                ->when(request('search'), fn($q, $s) => $q->where('invoice_number', 'like', "%{$s}%")->orWhereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$s}%")))
                ->when(request('supplier_id'), fn($q, $id) => $q->where('supplier_id', $id))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.purchases.index');
    }

    public function create()
    {
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('name')->get();
        $products = \App\Models\Product::where('status', 'active')->orderBy('name')->get();
        return view('modules.purchases.create', compact('suppliers', 'products'));
    }

    public function store(PurchaseRequest $request, PurchaseService $service)
    {
        $purchase = $service->create($request->validated());
        return response()->json(['success' => true, 'data' => $purchase, 'message' => 'Purchase recorded']);
    }

    public function show(Purchase $purchase)
    {
        return response()->json($purchase->load('items.product', 'supplier'));
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return response()->json(['success' => true, 'message' => 'Purchase deleted']);
    }
}
