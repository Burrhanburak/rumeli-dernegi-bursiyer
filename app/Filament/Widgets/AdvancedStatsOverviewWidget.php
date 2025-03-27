<?php

namespace App\Filament\Widgets;

use App\Models\Application;
use App\Models\Interviews;
use App\Models\Notifications;
use App\Models\ScholarshipProgram;
use App\Models\Scholarships;
use App\Models\Applications;
use App\Models\ActivityLogs;
use App\Models\User;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget as BaseWidget;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class AdvancedStatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Stat::make('Toplam Öğrenciler', Scholarships::count())
            //     ->icon('heroicon-o-academic-cap')
            //     ->iconColor('primary'),
            // Stat::make('Total Applications', Application::count()),
            // Stat::make('Total Universities', University::count()),

            // Stat::make('Total Users', User::count())->icon('heroicon-o-user')
        
            // ->progress(69)
            // ->progressBarColor('success')
            // ->iconBackgroundColor('success')
            // ->chartColor('success')
            // ->iconPosition('start')
            // ->description('The users in this period')
            // ->descriptionIcon('heroicon-o-chevron-up', 'before')
            // ->descriptionColor('success')
            // ->iconColor('success'),
        Stat::make('Toplam Aktiviteler', ActivityLogs::count())->icon('heroicon-o-newspaper')
            ->description('The posts in this period')
            ->descriptionIcon('heroicon-o-chevron-up', 'before')               
            ->descriptionColor('primary')
            ->iconColor('warning'),
        Stat::make('Toplam Görüşmeler', Interviews::count())->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->description("The comments in this period")
            ->descriptionIcon('heroicon-o-chevron-down', 'before')
            ->descriptionColor('danger')
            ->iconColor('danger'),
        Stat::make('Toplam Bildirimler', Notifications::count())->icon('heroicon-o-bell')
            ->description("The notifications in this period")
            ->descriptionIcon('heroicon-o-chevron-up', 'before')
            ->descriptionColor('success')
            ->iconColor('primary'),
        // Stat::make('Toplam Başvurular', Applications::count())->icon('heroicon-o-document-text')
        //     ->description("The applications in this period")
        //     ->descriptionIcon('heroicon-o-chevron-up', 'before')
        //     ->descriptionColor('primary')
        //     ->iconColor('success'),
        Stat::make('Toplam Basvuru Programı', ScholarshipProgram::count())
            ->icon('heroicon-o-building-library')
            ->description("All universities")
            ->iconColor('success')
        ];
    }
}
