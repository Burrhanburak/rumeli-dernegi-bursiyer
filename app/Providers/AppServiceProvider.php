<?php

namespace App\Providers;

use App\Filament\Resources\ApplicationsResource;
use Filament\Tables\Table;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentLabel;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
    }

    public $singletons = [
        \Filament\Http\Responses\Auth\Contracts\LoginResponse::class => \App\Http\Response\Auth\Contracts\LoginResponse::class,
        \Filament\Http\Responses\Auth\Contracts\LogoutResponse::class => \App\Http\Response\Auth\Contracts\LogoutResponse::class,
    ];

}
