<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $faqs = Faq::with('category:id,name')
                ->when($request->search, fn($q, $s) => $q->where('question', 'like', "%{$s}%"))
                ->when($request->category_id, fn($q, $c) => $q->where('faq_category_id', $c))
                ->orderBy('sort_order')
                ->paginate($request->per_page ?? 30);
            return response()->json($faqs);
        }
        return view('modules.faq.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question'        => 'required|string|max:1000',
            'answer'          => 'required|string',
            'faq_category_id' => 'nullable|exists:faq_categories,id',
            'sort_order'      => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
            'page_slug'       => 'nullable|string|max:255',
        ]);

        $faq = Faq::create($data);
        return response()->json(['success' => true, 'data' => $faq->load('category:id,name'), 'message' => 'FAQ created']);
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question'        => 'required|string|max:1000',
            'answer'          => 'required|string',
            'faq_category_id' => 'nullable|exists:faq_categories,id',
            'sort_order'      => 'nullable|integer',
            'is_active'       => 'nullable|boolean',
            'page_slug'       => 'nullable|string|max:255',
        ]);

        $faq->update($data);
        return response()->json(['success' => true, 'data' => $faq->load('category:id,name'), 'message' => 'FAQ updated']);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return response()->json(['success' => true, 'message' => 'FAQ deleted']);
    }

    // ── FAQ Categories ────────────────────────────────────────────
    public function categories(Request $request)
    {
        $categories = FaqCategory::withCount('faqs')
            ->orderBy('sort_order')
            ->get();
        return response()->json(['data' => $categories]);
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:150',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['slug'] = FaqCategory::generateSlug($data['name']);
        $category = FaqCategory::create($data);
        return response()->json(['success' => true, 'data' => $category, 'message' => 'Category created']);
    }

    public function updateCategory(Request $request, FaqCategory $faqCategory)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:150',
            'sort_order' => 'nullable|integer',
            'is_active'  => 'nullable|boolean',
        ]);

        $data['slug'] = FaqCategory::generateSlug($data['name'], $faqCategory->id);
        $faqCategory->update($data);
        return response()->json(['success' => true, 'data' => $faqCategory, 'message' => 'Category updated']);
    }

    public function destroyCategory(FaqCategory $faqCategory)
    {
        $faqCategory->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted']);
    }
}
