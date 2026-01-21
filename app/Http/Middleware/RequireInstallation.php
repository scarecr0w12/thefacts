<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class RequireInstallation extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow installer routes
        if ($request->is('install') || $request->is('install/*')) {
            return $next($request);
        }

        // Check if installation is complete by looking for installer config file
        if (!File::exists(config_path('installer.php'))) {
            return redirect()->route('installer.show');
        }

        return $next($request);
    }
}
