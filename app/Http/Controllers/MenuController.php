<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $menus = Menu::topLevel()
                ->with(['children' => fn($q) => $q->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();
            return response()->json($menus);
        }
        return view('modules.menus.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $menu = Menu::create($data);

        return response()->json([
            'success' => true,
            'data' => $menu,
            'message' => 'Menu item created successfully',
        ]);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $menu->update($data);

        return response()->json([
            'success' => true,
            'data' => $menu,
            'message' => 'Menu item updated successfully',
        ]);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu item deleted successfully',
        ]);
    }

    /**
     * Update sort order for menus.
     */
    public function reorder(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menus,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($data['items'] as $item) {
            Menu::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Menu order updated',
        ]);
    }
}
