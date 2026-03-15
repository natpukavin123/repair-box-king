<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $data = User::with('role')
                ->when(request('search'), fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
                ->when(request('role_id'), fn($q, $id) => $q->where('role_id', $id))
                ->when(request('status'), fn($q, $s) => $q->where('status', $s))
                ->latest()
                ->paginate(request('per_page', 15));
            return response()->json($data);
        }
        return view('modules.users.index');
    }

    public function create()
    {
        $roles = Role::all();
        return view('modules.users.create', compact('roles'));
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        return response()->json(['success' => true, 'data' => $user->load('role'), 'message' => 'User created']);
    }

    public function show(User $user)
    {
        return response()->json($user->load('role'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        // Super admin: cannot change role or status — only name/email/password allowed
        if ($user->is_super_admin) {
            unset($data['role_id'], $data['status']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return response()->json(['success' => true, 'data' => $user->load('role'), 'message' => 'User updated']);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Cannot delete yourself'], 403);
        }
        if ($user->is_super_admin) {
            return response()->json(['success' => false, 'message' => 'Super admin account cannot be deleted'], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted']);
    }

    public function roles()
    {
        return response()->json(Role::all());
    }
}
