<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $isSetupRoute = $request->is('setup') || $request->is('setup/*');

        if (!file_exists(storage_path('installed'))) {
            // App not yet installed – force to setup wizard
            if (!$isSetupRoute) {
                return redirect('/setup');
            }
        } else {
            // App is installed – block access to setup wizard
            if ($isSetupRoute) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
