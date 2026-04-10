<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExtendRememberedSession
{
    /**
     * Extend session lifetime when user was authenticated via "remember me" cookie.
     * This ensures the session cookie and GC both respect the longer lifetime.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::viaRemember()) {
            config(['session.lifetime' => 43200]); // 30 days
        }

        return $next($request);
    }
}
