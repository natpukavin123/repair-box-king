<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Http\Requests\PartRequest;

class PartController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $parts = Part::when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"))
                ->orderBy('name')
                ->paginate(request('per_page', 15));
            return response()->json($parts);
        }
        return view('modules.parts.index');
    }

    public function create()
    {
        return view('modules.parts.create');
    }

    public function store(PartRequest $request)
    {
        $part = Part::create($request->validated());
        return response()->json(['success' => true, 'data' => $part, 'message' => 'Part created']);
    }

    public function update(PartRequest $request, Part $part)
    {
        $part->update($request->validated());
        return response()->json(['success' => true, 'data' => $part, 'message' => 'Part updated']);
    }

    public function destroy(Part $part)
    {
        $part->delete();
        return response()->json(['success' => true, 'message' => 'Part deleted']);
    }

    public function search()
    {
        $parts = Part::with('taxRate')->where('status', 'active')
            ->when(request('q'), fn($q, $s) => $q->where(function($w) use ($s) {
                $w->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%");
            }))
            ->orderBy('name')
            ->paginate(15);
        return response()->json([
            'success' => true,
            'data' => $parts->items(),
            'has_more' => $parts->hasMorePages(),
            'page' => $parts->currentPage(),
        ]);
    }
}
