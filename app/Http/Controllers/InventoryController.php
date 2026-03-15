<?php

namespace App\Http\Controllers;

use App\Models\{Inventory, StockAdjustment, Product, ActivityLog};
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Inventory::with('product.category', 'product.brand')
                ->when(request('search'), fn($q, $s) => $q->whereHas('product', fn($pq) => $pq->where('name', 'like', "%{$s}%")))
                ->when(request('low_stock'), fn($q) => $q->where('current_stock', '<=', 5))
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.inventory.index');
    }

    public function adjust(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'adjustment_type' => 'required|in:addition,subtraction,correction',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
        ]);

        $data['created_by'] = auth()->id();
        StockAdjustment::create($data);

        $inventory = Inventory::firstOrCreate(['product_id' => $data['product_id']], ['current_stock' => 0, 'reserved_stock' => 0]);
        if ($data['adjustment_type'] === 'addition') {
            $inventory->increment('current_stock', $data['quantity']);
        } elseif ($data['adjustment_type'] === 'subtraction') {
            $inventory->decrement('current_stock', $data['quantity']);
        } else {
            $inventory->update(['current_stock' => $data['quantity']]);
        }

        ActivityLog::log('adjust', 'inventory', $data['product_id'], "{$data['adjustment_type']} {$data['quantity']} units");

        return response()->json(['success' => true, 'message' => 'Stock adjusted successfully']);
    }

    public function adjustments()
    {
        $data = StockAdjustment::with('product', 'creator')
            ->when(request('product_id'), fn($q, $id) => $q->where('product_id', $id))
            ->latest()
            ->paginate(20);
        return response()->json($data);
    }
}
