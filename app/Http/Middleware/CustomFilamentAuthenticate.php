<?php

namespace App\Http\Middleware;

use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CustomFilamentAuthenticate extends FilamentAuthenticate
{
    /**
     * @param  array<string>  $guards
     */
    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);
            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentPanel();

        // If the user is not an admin, redirect them to the user dashboard
        if (!$user->is_admin) {
            Log::info('Admin access denied, redirecting', [
                'user_id' => $user->id,
                'is_admin' => $user->is_admin ? 'yes' : 'no',
                'path' => $request->path()
            ]);
            
            // Instead of abort(403), redirect to user dashboard
            redirect('/user/user-dashboard')->send();
            exit;
        }

        // If they're an admin but don't meet FilamentUser requirements
        if (
            $user instanceof FilamentUser && 
            !$user->canAccessPanel($panel) && 
            config('app.env') !== 'local'
        ) {
            redirect('/admin/dashboard')->send();
            exit;
        }
    }
} 