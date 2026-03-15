<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Http\Requests\ProductRequest;
use App\Models\ActivityLog;

class ProductController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $products = Product::with('category', 'subcategory', 'brand', 'inventory')
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")->orWhere('barcode', 'like', "%{$s}%"))
                ->when(request('category_id'), fn($q, $id) => $q->where('category_id', $id))
                ->when(request('brand_id'), fn($q, $id) => $q->where('brand_id', $id))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($products);
        }
        return view('modules.products.index');
    }

    public function create()
    {
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();
        $brands = \App\Models\Brand::orderBy('name')->get();
        return view('modules.products.create', compact('categories', 'brands'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        Inventory::create(['product_id' => $product->id, 'current_stock' => 0, 'reserved_stock' => 0]);
        ActivityLog::log('create', 'products', $product->id, "Created product: {$product->name}");
        return response()->json(['success' => true, 'data' => $product->load('category', 'brand', 'inventory'), 'message' => 'Product created']);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category', 'subcategory', 'brand', 'inventory'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        ActivityLog::log('update', 'products', $product->id, "Updated product: {$product->name}");
        return response()->json(['success' => true, 'data' => $product->load('category', 'brand', 'inventory'), 'message' => 'Product updated']);
    }

    public function destroy(Product $product)
    {
        ActivityLog::log('delete', 'products', $product->id, "Deleted product: {$product->name}");
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }

    public function search()
    {
        $q = request('q', '');
        $products = Product::with('inventory', 'taxRate')
            ->where('status', 'active')
            ->where(fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")->orWhere('barcode', 'like', "%{$q}%"))
            ->take(20)->get();
        return response()->json($products);
    }
}
