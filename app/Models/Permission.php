<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'module', 'display_name', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get all permissions grouped by module.
     */
    public static function groupedByModule(): array
    {
        return static::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module')
            ->toArray();
    }
}
