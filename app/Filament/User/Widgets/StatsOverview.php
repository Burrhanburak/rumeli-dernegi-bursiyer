<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;


class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        
        return [
            Stat::make('Mulakat Sayısı', $user->interviews->count())
                ->description('Toplam planlanan görüşmeler')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('primary'),
                
            Stat::make('Evrak Durumu', $user->documents->count())
                ->description('Toplam evrak durumu')
                ->descriptionIcon('heroicon-o-document-text')
                ->color($this->getStatusColor($user->documents->first()?->status)),
                
                Stat::make('Bildirimler', $user->notifications->count())
                ->description('Toplam bildirim sayısı')
                ->descriptionIcon('heroicon-o-bell')
                ->color('secondary'),
       
        ];
    }
    
    protected function getStatusColor(?string $status): string
    {
        return match($status) {
            'İnceleniyor' => 'warning',
            'Onaylandı' => 'success',
            'Reddedildi' => 'danger',
            default => 'gray',
        };
    }
    
    protected function getCompletionPercentage($user): array
    {
        
        $totalSteps = $user->totalSteps;
        
        if (!$totalSteps) {
            return [0, 0, 0, 0, 0, 0, 0];
        }
        
        $percentage = ($completedCount / $totalSteps) * 100;
        
        // Simple chart data
        return [
            $percentage * 0.4, 
            $percentage * 0.5, 
            $percentage * 0.6,
            $percentage * 0.7,
            $percentage * 0.8,
            $percentage * 0.9,
            $percentage,
        ];
    }
}
