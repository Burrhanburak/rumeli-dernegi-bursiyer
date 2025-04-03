<?php

namespace App\Providers;

use App\Filament\Resources\ApplicationsResource;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentLabel;
use Illuminate\Support\Facades\URL;
use App\Listeners\SendEmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // We no longer need this binding since we're overriding the method in the User model
        $this->app->bind(
            \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
            \App\Listeners\SendEmailVerificationNotification::class
        );
        
        // Admin ve kullanıcı panelleri için özel login response sınıflarını bağlama
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Özel boş tablo başlığı ve açıklaması
        Table::configureUsing(function (Table $table): void {
            $table->emptyStateHeading('Başvuru Bulunamadı')
                ->emptyStateDescription('Yeni bir başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.');
        });

        // Force HTTPS in production or when APP_URL starts with HTTPS
        if (str_starts_with(config('app.url'), 'https://') || config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Activity log için event listener'lar
        // Event::listen(Login::class, function ($event) {
        //     activity('auth')
        //         ->causedBy($event->user)
        //         ->withProperties([
        //             'ip' => request()->ip(),
        //             'user_agent' => request()->userAgent(),
        //         ])
        //         ->log('Giriş yapıldı');
        // });

        // Event::listen(Logout::class, function ($event) {
        //     activity('auth')
        //         ->causedBy($event->user)
        //         ->withProperties([
        //             'ip' => request()->ip(),
        //             'user_agent' => request()->userAgent(),
        //         ])
        //         ->log('Çıkış yapıldı');
        // });

        // Event::listen(Failed::class, function ($event) {
        //     if (isset($event->user)) {
        //         activity('auth')
        //             ->causedBy($event->user)
        //             ->withProperties([
        //                 'ip' => request()->ip(),
        //                 'user_agent' => request()->userAgent(),
        //             ])
        //             ->log('Giriş başarısız');
        //     } else {
        //         activity('auth')
        //             ->withProperties([
        //                 'ip' => request()->ip(),
        //                 'user_agent' => request()->userAgent(),
        //                 'email' => $event->credentials['email'] ?? 'bilinmiyor',
        //             ])
        //             ->log('Giriş başarısız');
        //     }
        // });
    }

    public $singletons = [
        // \Filament\Http\Responses\Auth\Contracts\LoginResponse::class => \App\Http\Response\Auth\Contracts\LoginResponse::class,
        // \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class => \App\Http\Response\Auth\Contracts\LogoutResponse::class,
    ];
}
