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
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\AdminDashboard;
use Filament\Enums\ThemeMode;
use App\Filament\Widgets\AplicationsChart;
use App\Filament\Pages\Auth\Login as AdminLogin;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            
            ->login(AdminLogin::class)

            ->brandName('Admin Panel')
            
            // ->brandLogo(asset('images/logo.svg'))
            ->favicon(asset('images/favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'background' => Color::Blue,
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
              
                'success' => Color::Emerald,
                'warning' => Color::Orange,
               
            ])
            ->defaultThemeMode(ThemeMode::Light)
            // ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Pages\Dashboard::class,
                
                AdminDashboard::class,
            ])
            ->navigationGroups([
                'Başvuru Yönetimi',
                'Burs Yönetimi',
                'Mülakat Yönetimi',
                'Belge Yönetimi',
                'Kullanıcı Yönetimi',
                'İletişim',
                'Sistem',
            ])
            ->databaseNotifications()
         

            // ->navigationGroups([
            //     NavigationGroup::make()
            //         ->label('Başvuru Yönetimi')
            //         ->collapsible()
            //         ->icon('heroicon-o-document-text')
            //         ->items([
            //             NavigationItem::make('Başvurular')
            //                 ->icon('heroicon-o-document'),
            //                 // ->url(),
            //             NavigationItem::make('Ön Değerlendirme')
            //                 ->icon('heroicon-o-clipboard-document-list'),
            //                 // ->url(route('filament.admin.resources.on-degerlendirme.index')),
            //             NavigationItem::make('Kabul Edilen Başvurular')
            //                 ->icon('heroicon-o-check-circle'),
            //                 // ->url(route('filament.admin.resources.kabul-edilen-basvurular.index')),
            //             NavigationItem::make('Reddedilen Başvurular')
            //                 ->icon('heroicon-o-x-circle'),
            //                 // ->url(route('filament.admin.resources.reddedilen-basvurular.index')),
            //         ]),
                   

            //         ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
                \App\Http\Middleware\CustomFilamentAuthenticate::class,
            ])
            ->authGuard('web')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth('full');
            
           
    }
}
