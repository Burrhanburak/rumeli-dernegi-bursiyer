<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CheckUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log for debugging
        Log::info('CheckUserIsAdmin middleware running', [
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'authenticated' => Auth::check(),
            'user_id' => Auth::id(),
        ]);

        if (!Auth::check()) {
            return $next($request);
        }

        if (!Auth::user()->is_admin) {
            Log::info('Non-admin user redirected', [
                'user_id' => Auth::id(),
            ]);
            
            // Try using JavaScript redirection
            return response(
                '<script>window.location.href = "/user/user-dashboard";</script>',
                200,
                ['Content-Type' => 'text/html']
            );
        }

        return $next($request);
    }
}
