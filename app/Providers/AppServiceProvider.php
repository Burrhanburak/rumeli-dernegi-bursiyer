<?php

namespace App\Providers;

use App\Filament\Resources\ApplicationsResource;
use App\Models\Applications;
use App\Observers\ApplicationObserver;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentLabel;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Config;
use Filament\Tables\Actions\ExportAction;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Remove this binding completely as we're using OTP verification
        // $this->app->bind(
        //     \Illuminate\Auth\Listeners\SendEmailVerificationNotification::class,
        //     \App\Listeners\SendEmailVerificationNotification::class
        // );
        
        // Configure database notifications for compatibility
        config(['database.connections.sqlite.foreign_key_constraints' => true]);
        
        // Use our custom LaravelNotification class
        $this->app->bind(
            \Illuminate\Notifications\DatabaseNotification::class,
            \App\Models\LaravelNotification::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Applications::observe(ApplicationObserver::class);

        // Check and create storage symlink if it doesn't exist
        $publicPath = public_path('storage');
        $storagePath = storage_path('app/public');
        
        if (!file_exists($publicPath)) {
            try {
                // Create the symbolic link
                if (PHP_OS_FAMILY === 'Windows') {
                    // Windows needs different symlink handling
                    exec('mklink /D "' . $publicPath . '" "' . $storagePath . '"');
                } else {
                    symlink($storagePath, $publicPath);
                }
                Log::info("Created storage symlink automatically");
            } catch (\Exception $e) {
                Log::error("Failed to create storage symlink: " . $e->getMessage());
            }
        }
        
        // Add unread method to MorphMany relation
        MorphMany::macro('unread', function () {
            return $this->where('is_read', false);
        });

        // We need a different approach for Filament export notifications
        // The previous approach with notifyUsing method doesn't work

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

        // Configure DomPDF for better image handling
        if (class_exists('\\Barryvdh\\DomPDF\\ServiceProvider')) {
            Config::set('dompdf.options.enable_remote', true);
            Config::set('dompdf.options.enable_php', true);
            Config::set('dompdf.options.enable_html5_parser', true);
            Config::set('dompdf.options.isRemoteEnabled', true);
            Config::set('dompdf.options.isPhpEnabled', true);
        }
    }

    public $singletons = [
        // \Filament\Http\Responses\Auth\Contracts\LoginResponse::class => \App\Http\Response\Auth\Contracts\LoginResponse::class,
        // \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class => \App\Http\Response\Auth\Contracts\LogoutResponse::class,
    ];
}
