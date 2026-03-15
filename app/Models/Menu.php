<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['name', 'route', 'icon', 'module', 'section', 'parent_id', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get menus for a user based on their role permissions.
     * Admin gets all active menus.
     */
    public static function getMenusForUser(User $user): array
    {
        $menus = static::active()
            ->topLevel()
            ->with(['children' => fn($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        if ($user->isAdmin()) {
            return $menus->groupBy('section')->toArray();
        }

        // Get user's permitted modules
        $permittedModules = $user->role
            ? $user->role->permissions()->pluck('module')->unique()->toArray()
            : [];

        $filtered = $menus->filter(function ($menu) use ($permittedModules) {
            // If menu has no module restriction, show it
            if (empty($menu->module)) return true;
            return in_array($menu->module, $permittedModules);
        })->map(function ($menu) use ($permittedModules) {
            // Also filter children
            if ($menu->children && $menu->children->count() > 0) {
                $menu->setRelation('children', $menu->children->filter(function ($child) use ($permittedModules) {
                    if (empty($child->module)) return true;
                    return in_array($child->module, $permittedModules);
                }));
            }
            return $menu;
        });

        return $filtered->groupBy('section')->toArray();
    }
}
