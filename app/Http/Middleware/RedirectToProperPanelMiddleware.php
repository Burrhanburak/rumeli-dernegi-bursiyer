<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Pages\Auth\AdminDashboard;
class RedirectToProperPanelMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return redirect()->to(AdminDashboard::getUrl(panel: 'admin'));
        }
        return $next($request);
    }
}