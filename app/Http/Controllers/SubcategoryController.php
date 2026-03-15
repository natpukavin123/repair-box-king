<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Http\Requests\SubcategoryRequest;

class SubcategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = Subcategory::with('category')
                ->when(request('category_id'), fn($q, $id) => $q->where('category_id', $id))
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.subcategories.index');
    }

    public function create()
    {
        $categories = \App\Models\Category::where('status', 'active')->orderBy('name')->get();
        return view('modules.subcategories.create', compact('categories'));
    }

    public function store(SubcategoryRequest $request)
    {
        $sub = Subcategory::create($request->validated());
        return response()->json(['success' => true, 'data' => $sub->load('category'), 'message' => 'Subcategory created']);
    }

    public function update(SubcategoryRequest $request, Subcategory $subcategory)
    {
        $subcategory->update($request->validated());
        return response()->json(['success' => true, 'data' => $subcategory->load('category'), 'message' => 'Subcategory updated']);
    }

    public function destroy(Subcategory $subcategory)
    {
        $subcategory->delete();
        return response()->json(['success' => true, 'message' => 'Subcategory deleted']);
    }

    public function byCategory($categoryId)
    {
        return response()->json(Subcategory::where('category_id', $categoryId)->where('status', 'active')->get());
    }
}
