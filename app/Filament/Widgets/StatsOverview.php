<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Applications;
use App\Models\User;

class StatsOverview extends BaseWidget
{
    protected ?string $heading = 'Genel İstatistikler';
    
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Assume these models and methods exist, adjust according to your actual schema
        return [
            Stat::make('Toplam Başvuru', Applications::count())
                ->description('Tüm başvurular')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Aktif Kullanıcılar', User::count())
                ->description('Son 30 gün')
                ->descriptionIcon('heroicon-m-user')
                ->color('success')
                ->chart([3, 5, 7, 6, 9, 12, 15]),

            Stat::make('Kabul Edilen Başvurular', Applications::where('status', 'kabul_edildi')->count())
                ->description('Toplam kabul')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Bekleyen Başvurular', Applications::whereIn('status', ['burs_havuzu', 'on_kabul', 'evrak_bekleniyor'])->count())
                ->description('İşlem bekliyor')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
