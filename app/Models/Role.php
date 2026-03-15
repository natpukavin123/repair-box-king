<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Check if this is a system role that cannot be deleted.
     */
    public function isSystemRole(): bool
    {
        return $this->name === 'Admin';
    }

    /**
     * Get permissions grouped by module for this role.
     */
    public function permissionsGroupedByModule(): array
    {
        return $this->permissions()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module')
            ->toArray();
    }
}
