<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $roles = Role::withCount(['users', 'permissions'])->latest()->get();
            return response()->json($roles);
        }
        return view('modules.roles.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            $role->permissions()->attach($data['permissions']);
        }

        return response()->json([
            'success' => true,
            'data' => $role->loadCount(['users', 'permissions']),
            'message' => 'Role created successfully',
        ]);
    }

    public function show(Role $role)
    {
        return response()->json(
            $role->load('permissions')->loadCount(['users', 'permissions'])
        );
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $role->permissions()->sync($data['permissions'] ?? []);

        return response()->json([
            'success' => true,
            'data' => $role->loadCount(['users', 'permissions']),
            'message' => 'Role updated successfully',
        ]);
    }

    public function destroy(Role $role)
    {
        if ($role->isSystemRole()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete system role',
            ], 403);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete role with assigned users. Reassign users first.',
            ], 422);
        }

        $role->permissions()->detach();
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Get all permissions grouped by module.
     */
    public function allPermissionsGrouped()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $grouped = $permissions->groupBy('module')->map(function ($perms, $module) {
            return [
                'module' => $module,
                'display_name' => ucfirst(str_replace('_', ' ', $module)),
                'permissions' => $perms->values(),
            ];
        })->values();

        return response()->json($grouped);
    }
}
