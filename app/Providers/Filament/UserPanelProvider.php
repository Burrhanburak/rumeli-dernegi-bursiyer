<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\User\Pages\Auth\Register;
use App\Filament\User\Pages\Auth\Login as BaseLogin;
use Illuminate\View\View;
use Filament\View\FilamentView;
use Filament\Enums\ThemeMode;
use App\Http\Middleware\RedirectToProperPanelMiddleware;
use App\Filament\User\Pages\Pages\UserDashboard;
use App\Filament\User\Widgets\StatsOverview;    

class UserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel

    {
        return $panel
            ->default()
            ->id('user')
            ->path('user')
            ->login(BaseLogin::class)
            ->profile()
           
            ->defaultThemeMode(ThemeMode::Light)
            ->brandName('Rumeli Türkleri Derneği')
            
            ->registration(Register::class)
            ->brandLogo(asset('images/logo.svg'))
            ->passwordResetRoutePrefix('password-reset')
            ->passwordReset()

            ->emailVerification()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->discoverResources(in: app_path('Filament/User/Resources'), for: 'App\\Filament\\User\\Resources')
            ->discoverPages(in: app_path('Filament/User/Pages'), for: 'App\\Filament\\User\\Pages')
            ->pages([
                // Pages\Dashboard::class
               UserDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/User/Widgets'), for: 'App\\Filament\\User\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                StatsOverview::class,             
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                RedirectToProperPanelMiddleware::class,
                Authenticate::class,
                
            ])
            ->authGuard('web')
            ->viteTheme('resources/css/bursiyer-auth.css');

        
    }
}
