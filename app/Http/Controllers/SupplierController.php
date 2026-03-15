<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;

class SupplierController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Supplier::withCount('purchases')
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%"))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.suppliers.index');
    }

    public function create()
    {
        return view('modules.suppliers.create');
    }

    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());
        return response()->json(['success' => true, 'data' => $supplier, 'message' => 'Supplier created']);
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier->load('purchases'));
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());
        return response()->json(['success' => true, 'data' => $supplier, 'message' => 'Supplier updated']);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return response()->json(['success' => true, 'message' => 'Supplier deleted']);
    }
}
