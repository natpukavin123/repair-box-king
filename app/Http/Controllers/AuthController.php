<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return redirect('/admin/dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            ActivityLog::log('login', 'auth', null, 'User logged in');
            return response()->json(['success' => true, 'redirect' => '/admin/dashboard']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 422);
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'auth', null, 'User logged out');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
