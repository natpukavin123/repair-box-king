<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use App\Models\ActivityLog;

class CategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $categories = Category::with('subcategories:id,name,category_id')->withCount('products')
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($categories);
        }
        return view('modules.categories.index');
    }

    public function create()
    {
        return view('modules.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        ActivityLog::log('create', 'categories', $category->id, "Created category: {$category->name}");
        return response()->json(['success' => true, 'data' => $category, 'message' => 'Category created successfully']);
    }

    public function show(Category $category)
    {
        return response()->json($category->load('subcategories'));
    }

    public function subcategories(Category $category)
    {
        if (request()->ajax()) {
            $data = $category->subcategories()
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.categories.subcategories', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        ActivityLog::log('update', 'categories', $category->id, "Updated category: {$category->name}");
        return response()->json(['success' => true, 'data' => $category, 'message' => 'Category updated']);
    }

    public function destroy(Category $category)
    {
        ActivityLog::log('delete', 'categories', $category->id, "Deleted category: {$category->name}");
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}
