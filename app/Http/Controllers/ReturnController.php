<?php

namespace App\Http\Controllers;

use App\Models\{CustomerReturn, SupplierReturn, Refund};
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $type = request('type', 'customer');
            if ($type === 'customer') {
                $data = CustomerReturn::with('invoice.customer', 'product')
                    ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                    ->latest()->paginate(15);
            } else {
                $data = SupplierReturn::with('supplier', 'product')
                    ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                    ->latest()->paginate(15);
            }
            return response()->json($data);
        }
        return view('modules.returns.index');
    }

    public function storeCustomerReturn(Request $request)
    {
        $data = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'refund_type' => 'required|in:cash,credit,exchange',
        ]);
        $return = CustomerReturn::create($data);
        return response()->json(['success' => true, 'data' => $return, 'message' => 'Return recorded']);
    }

    public function storeSupplierReturn(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'return_amount' => 'required|numeric|min:0',
        ]);
        $return = SupplierReturn::create($data);
        return response()->json(['success' => true, 'data' => $return, 'message' => 'Supplier return recorded']);
    }

    public function updateStatus(Request $request, $type, $id)
    {
        $status = $request->validate(['status' => 'required|string'])['status'];
        $model = $type === 'customer' ? CustomerReturn::findOrFail($id) : SupplierReturn::findOrFail($id);
        $model->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => 'Status updated']);
    }

    public function refunds()
    {
        $data = Refund::latest()->paginate(15);
        return response()->json($data);
    }

    public function storeRefund(Request $request)
    {
        $data = $request->validate([
            'reference_type' => 'required|in:invoice,repair',
            'reference_id' => 'required|integer',
            'refund_amount' => 'required|numeric|min:0.01',
            'refund_method' => 'required|string|max:50',
            'reason' => 'nullable|string',
        ]);
        $refund = Refund::create($data);
        return response()->json(['success' => true, 'data' => $refund, 'message' => 'Refund processed']);
    }
}
