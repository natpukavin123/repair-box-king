<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $posts = BlogPost::with('author:id,name')
                ->when($request->search, fn($q, $s) => $q->where('title', 'like', "%{$s}%"))
                ->when($request->status, fn($q, $s) => $q->where('status', $s))
                ->orderByDesc('created_at')
                ->paginate($request->per_page ?? 15);
            return response()->json($posts);
        }
        return view('modules.blog.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'canonical_url'    => 'nullable|url|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'robots'           => 'nullable|in:index,follow,noindex,follow,index,nofollow,noindex,nofollow',
            'status'           => 'nullable|in:draft,published,archived',
            'published_at'     => 'nullable|date',
            'sort_order'       => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = BlogPost::generateSlug($data['title']);
        }

        $data['author_id'] = auth()->id();

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);
        return response()->json(['success' => true, 'data' => $post, 'message' => 'Blog post created']);
    }

    public function show(BlogPost $blog)
    {
        $blog->load('author:id,name');
        return response()->json(['success' => true, 'data' => $blog]);
    }

    public function update(Request $request, BlogPost $blog)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:blog_posts,slug,' . $blog->id,
            'excerpt'          => 'nullable|string|max:1000',
            'content'          => 'required|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
            'canonical_url'    => 'nullable|url|max:500',
            'og_title'         => 'nullable|string|max:255',
            'og_description'   => 'nullable|string|max:500',
            'robots'           => 'nullable|in:index,follow,noindex,follow,index,nofollow,noindex,nofollow',
            'status'           => 'nullable|in:draft,published,archived',
            'published_at'     => 'nullable|date',
            'sort_order'       => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = BlogPost::generateSlug($data['title'], $blog->id);
        }

        if (($data['status'] ?? $blog->status) === 'published' && !$blog->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blog->update($data);
        return response()->json(['success' => true, 'data' => $blog, 'message' => 'Blog post updated']);
    }

    public function destroy(BlogPost $blog)
    {
        $blog->delete();
        return response()->json(['success' => true, 'message' => 'Blog post deleted']);
    }

    public function uploadImage(Request $request, BlogPost $blog)
    {
        return response()->json(app(ImageService::class)->handleUpload($request, $blog, 'blog'));
    }
}
