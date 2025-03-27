<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use Filament\Pages\Auth\AdminDashboard;
class RedirectToProperPanelMiddleware

{
    public function handle(Request $request, Closure $next)
    {
        // Debugging logs
        \Log::info('Panel Redirect Middleware', [
            'user' => auth()->user(),
            'is_admin' => auth()->user()->is_admin ?? null,
            'current_path' => $request->path()
        ]);

        if (auth()->check()) {
            $user = auth()->user();
            
            // Explicit panel redirection logic
            if ($user->is_admin && Filament::getCurrentPanel()->getId() !== 'admin') {
                return redirect()->to(Filament::getLoginUrl('admin'));
            }
            
            if (!$user->is_admin && Filament::getCurrentPanel()->getId() !== 'user') {
                return redirect()->to(Filament::getLoginUrl('user'));
            }
        }

        return $next($request);
    }
}