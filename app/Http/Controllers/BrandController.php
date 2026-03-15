<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Http\Requests\BrandRequest;

class BrandController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $brands = Brand::withCount('products')
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($brands);
        }
        return view('modules.brands.index');
    }

    public function create()
    {
        return view('modules.brands.create');
    }

    public function store(BrandRequest $request)
    {
        $brand = Brand::create($request->validated());
        return response()->json(['success' => true, 'data' => $brand, 'message' => 'Brand created']);
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());
        return response()->json(['success' => true, 'data' => $brand, 'message' => 'Brand updated']);
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->json(['success' => true, 'message' => 'Brand deleted']);
    }
}
