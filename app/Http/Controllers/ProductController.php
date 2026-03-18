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
        $q           = request('q', '');
        $categoryIds = array_filter(explode(',', request('category_id', '')));
        $subcatIds   = array_filter(explode(',', request('subcategory_id', '')));
        $brandIds    = array_filter(explode(',', request('brand_id', '')));

        $products = Product::with('inventory', 'category', 'subcategory', 'brand')
            ->where('status', 'active')
            ->when($q, fn($query) => $query->where(fn($inner) => $inner->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%")->orWhere('barcode', 'like', "%{$q}%")))
            ->when(count($categoryIds), fn($query) => $query->whereIn('category_id',    $categoryIds))
            ->when(count($subcatIds),   fn($query) => $query->whereIn('subcategory_id', $subcatIds))
            ->when(count($brandIds),    fn($query) => $query->whereIn('brand_id',       $brandIds))
            ->orderBy('name')
            ->take(60)->get();

        return response()->json($products);
    }

    public function filterData()
    {
        $categoryIds = array_filter(explode(',', request('category_id', '')));
        $subcatIds   = array_filter(explode(',', request('subcategory_id', '')));

        $categories = \App\Models\Category::where('status', 'active')
            ->with(['subcategories' => fn($q) => $q->where('status', 'active')->orderBy('name')->select('id', 'category_id', 'name')])
            ->orderBy('name')->get(['id', 'name']);

        $brandsQuery = \App\Models\Brand::orderBy('name');
        if (count($categoryIds) || count($subcatIds)) {
            $brandsQuery->whereHas('products', function ($q) use ($categoryIds, $subcatIds) {
                $q->where('status', 'active');
                if (count($categoryIds)) $q->whereIn('category_id',    $categoryIds);
                if (count($subcatIds))   $q->whereIn('subcategory_id', $subcatIds);
            });
        }
        $brands = $brandsQuery->get(['id', 'name']);

        return response()->json(['categories' => $categories, 'brands' => $brands]);
    }

    public function uploadImage(\Illuminate\Http\Request $request, Product $product)
    {
        $request->validate([
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $updates = [];

        if ($request->hasFile('image')) {
            // Delete old
            if ($product->image) \Storage::disk('public')->delete($product->image);
            if ($product->thumbnail && !$request->hasFile('thumbnail')) \Storage::disk('public')->delete($product->thumbnail);

            $path = $request->file('image')->store('products', 'public');
            $updates['image'] = $path;

            // Auto-generate thumbnail from uploaded image (200×200 max)
            if (!$request->hasFile('thumbnail')) {
                $thumbPath = $this->makeThumb(
                    \Storage::disk('public')->path($path),
                    'products/thumbs',
                    pathinfo($path, PATHINFO_FILENAME) . '_thumb.jpg'
                );
                if ($thumbPath) $updates['thumbnail'] = $thumbPath;
            }
        }

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) \Storage::disk('public')->delete($product->thumbnail);
            $path = $request->file('thumbnail')->store('products/thumbs', 'public');
            $updates['thumbnail'] = $path;
        }

        if ($updates) $product->update($updates);

        return response()->json([
            'success'   => true,
            'image_url' => $product->fresh()->image ? \Storage::disk('public')->url($product->fresh()->image) : null,
            'thumb_url' => $product->fresh()->thumbnail ? \Storage::disk('public')->url($product->fresh()->thumbnail) : null,
        ]);
    }

    private function makeThumb(string $src, string $destFolder, string $filename): ?string
    {
        if (!function_exists('imagecreatefromjpeg')) return null;
        $ext = strtolower(pathinfo($src, PATHINFO_EXTENSION));
        $image = match($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($src),
            'png'         => @imagecreatefrompng($src),
            'gif'         => @imagecreatefromgif($src),
            'webp'        => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($src) : null,
            default       => null,
        };
        if (!$image) return null;
        [$sw, $sh] = getimagesize($src);
        $ratio = min(200 / $sw, 200 / $sh);
        $nw = max(1, (int)($sw * $ratio));
        $nh = max(1, (int)($sh * $ratio));
        $thumb = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $nw, $nh, $sw, $sh);
        $dir = \Storage::disk('public')->path($destFolder);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $destPath = $dir . DIRECTORY_SEPARATOR . $filename;
        imagejpeg($thumb, $destPath, 85);
        imagedestroy($image);
        imagedestroy($thumb);
        return $destFolder . '/' . $filename;
    }
}
