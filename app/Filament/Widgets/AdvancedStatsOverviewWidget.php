<?php

namespace App\Filament\Widgets;


use App\Models\Interviews;
use App\Models\Notifications;
use App\Models\ScholarshipProgram;
use App\Models\Scholarships;
use App\Models\Applications;
use App\Models\Documents;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class AdvancedStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Toplam Kullanıcılar', User::count())
                ->icon('heroicon-o-user')
                ->iconColor('primary')
                ->description('Tüm sistem kullanıcıları')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('primary'),
                
            Stat::make('Toplam Başvurular', Applications::count())
                ->icon('heroicon-o-document-text')
                ->description('Tüm burs başvuruları')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('success')
                ->iconColor('success'),
                
            Stat::make('Toplam Burs Programı', ScholarshipProgram::count())
                ->icon('heroicon-o-building-library')
                ->description('Aktif burs programları')
                ->iconColor('warning'),
                
            Stat::make('Toplam Görüşmeler', Interviews::count())
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->description('Planlanmış görüşmeler')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('primary')
                ->iconColor('primary'),
                
            // Stat::make('Toplam İşlemler', ActivityLogs::count())
            //     ->icon('heroicon-o-newspaper')
            //     ->description('Son yapılan işlemler')
            //     ->descriptionIcon('heroicon-o-chevron-up', 'before')
            //     ->descriptionColor('primary')
            //     ->iconColor('success'),
                
            Stat::make('Toplam Bildirimler', Notifications::count())
                ->icon('heroicon-o-bell')
                ->description('Sistem bildirimleri')
                ->descriptionIcon('heroicon-o-chevron-up', 'before')
                ->descriptionColor('success')
                ->iconColor('danger'),
        ];
    }
}
