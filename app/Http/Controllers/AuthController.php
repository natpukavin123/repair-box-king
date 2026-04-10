<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    /** Max login attempts before lockout */
    private const MAX_ATTEMPTS = 5;

    /** Lockout duration in seconds (15 minutes) */
    private const DECAY_SECONDS = 900;

    private function throttleKey(Request $request): string
    {
        return 'login:' . Str::lower($request->input('email', '')) . '|' . $request->ip();
    }

    public function showLogin()
    {
        if (Auth::check()) return redirect('/admin/dashboard');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|max:255',
            'password' => 'required|string|max:128',
        ]);

        $key = $this->throttleKey($request);

        // Block if rate limit exceeded
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = (int) ceil($seconds / 60);
            ActivityLog::log('login_locked', 'auth', null,
                "Blocked login for {$request->input('email')} from {$request->ip()}");
            return response()->json([
                'success' => false,
                'message' => "Too many failed attempts. Try again in {$minutes} minute(s).",
            ], 429);
        }

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email'    => $request->input('email'),
            'password' => $request->input('password'),
        ], $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            // Extend session cookie lifetime when "remember me" is checked
            if ($remember) {
                config(['session.lifetime' => 43200]); // 30 days
            }

            ActivityLog::log('login', 'auth', null, 'User logged in');
            return response()->json(['success' => true, 'redirect' => '/admin/dashboard']);
        }

        // Record failed attempt
        RateLimiter::hit($key, self::DECAY_SECONDS);
        $remaining = max(0, self::MAX_ATTEMPTS - RateLimiter::attempts($key));

        ActivityLog::log('login_failed', 'auth', null,
            "Failed login for {$request->input('email')} from {$request->ip()}");

        $message = $remaining > 0
            ? "Invalid credentials. {$remaining} attempt(s) remaining before lockout."
            : 'Account temporarily locked. Please try again in 15 minutes.';

        return response()->json(['success' => false, 'message' => $message], 422);
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
