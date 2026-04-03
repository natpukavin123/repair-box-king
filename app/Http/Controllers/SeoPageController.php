<?php

namespace App\Http\Controllers;

use App\Models\SeoPage;
use App\Models\Faq;
use App\Services\ImageService;
use Illuminate\Http\Request;

class SeoPageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $pages = SeoPage::when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
                ->when($request->status, fn($q, $s) => $q->where('status', $s))
                ->orderBy('sort_order')
                ->paginate($request->per_page ?? 15);
            return response()->json($pages);
        }
        return view('modules.seo-pages.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:seo_pages,slug',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'canonical_url'    => 'nullable|url|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'robots'           => 'nullable|in:index,follow,noindex,follow,index,nofollow,noindex,nofollow',
            'schema_type'      => 'nullable|string|max:100',
            'status'           => 'nullable|in:draft,published,archived',
            'sort_order'       => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = SeoPage::generateSlug($data['title']);
        }

        $page = SeoPage::create($data);
        return response()->json(['success' => true, 'data' => $page, 'message' => 'SEO page created']);
    }

    public function show(SeoPage $seoPage)
    {
        return response()->json(['success' => true, 'data' => $seoPage]);
    }

    public function update(Request $request, SeoPage $seoPage)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:seo_pages,slug,' . $seoPage->id,
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'canonical_url'    => 'nullable|url|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'robots'           => 'nullable|in:index,follow,noindex,follow,index,nofollow,noindex,nofollow',
            'schema_type'      => 'nullable|string|max:100',
            'status'           => 'nullable|in:draft,published,archived',
            'sort_order'       => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = SeoPage::generateSlug($data['title'], $seoPage->id);
        }

        $seoPage->update($data);
        return response()->json(['success' => true, 'data' => $seoPage, 'message' => 'SEO page updated']);
    }

    public function destroy(SeoPage $seoPage)
    {
        $seoPage->delete();
        return response()->json(['success' => true, 'message' => 'SEO page deleted']);
    }

    public function uploadImage(Request $request, SeoPage $seoPage)
    {
        return response()->json(app(ImageService::class)->handleUpload($request, $seoPage, 'seo-pages'));
    }
}
