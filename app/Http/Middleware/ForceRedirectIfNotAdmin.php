<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ForceRedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check admin routes
        if (str_starts_with($request->path(), 'admin')) {
            Log::info('ForceRedirectIfNotAdmin check', [
                'path' => $request->path(),
                'user_id' => Auth::id(),
                'is_admin' => Auth::check() ? (Auth::user()->is_admin ? 'yes' : 'no') : 'guest'
            ]);
            
            // If user is authenticated but not admin, force redirect
            if (Auth::check() && !Auth::user()->is_admin) {
                Log::info('Forcing redirect for non-admin user', [
                    'user_id' => Auth::id(),
                    'to' => '/user/user-dashboard'
                ]);
                
                // Start output buffering to catch any output
                ob_start();
                
                // Return an HTML response with meta refresh
                return response('
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta http-equiv="refresh" content="0;url=/user/user-dashboard">
                        <script>window.location.href = "/user/user-dashboard";</script>
                    </head>
                    <body>
                        <p>Redirecting to user dashboard...</p>
                    </body>
                    </html>
                ', 200, ['Content-Type' => 'text/html']);
            }
        }
        
        return $next($request);
    }
}
