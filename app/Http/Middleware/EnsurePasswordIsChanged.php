<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->must_change_password) {
            // Kullanıcının zaten şifre değiştirme sayfasında olup olmadığını kontrol et
            // Döngüye girmesini engellemek için.
            // Filament sayfasının slug'ını kullanarak rota adını oluşturuyoruz.
            $changePasswordPageRouteName = 'filament.' . filament()->getCurrentPanel()->getId() . '.pages.change-password';

            if (! $request->routeIs($changePasswordPageRouteName) && ! $request->routeIs('filament.user.auth.logout')) { // Logout isteğine izin ver
                return redirect()->route($changePasswordPageRouteName)
                                 ->with('warning', 'Güvenliğiniz için lütfen şifrenizi güncelleyiniz.');
            }
        }

        return $next($request);
    }
}
