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

        // If DB is not configured at all — always go to setup (step 2: Database)
        if (empty(config('database.connections.mysql.host')) ||
            empty(config('database.connections.mysql.database'))) {
            if (!$isSetupRoute) {
                return redirect('/setup/database');
            }
            return $next($request);
        }

        // DB is configured — check if app has been installed (super admin exists in DB)
        $installed = $this->isInstalled();

        if (!$installed) {
            // Not installed — force to setup wizard
            if (!$isSetupRoute) {
                return redirect('/setup');
            }
        } else {
            // Installed — block access to setup wizard
            if ($isSetupRoute) {
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }

    private function isInstalled(): bool
    {
        // First check the fast filesystem lock (local installs / non-ephemeral)
        if (file_exists(storage_path('installed'))) {
            return true;
        }

        // Fallback: check DB for a super admin (Railway has ephemeral filesystem)
        try {
            return \App\Models\User::where('is_super_admin', true)->exists();
        } catch (\Throwable) {
            // DB not reachable yet — treat as not installed, go to setup
            return false;
        }
    }
}
