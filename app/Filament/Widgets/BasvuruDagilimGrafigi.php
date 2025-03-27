<?php

namespace App\Filament\Widgets;

use App\Models\Applications;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Illuminate\Support\Facades\DB;

class BasvuruDagilimGrafigi extends ChartWidget
{
    protected static ?string $heading = 'Başvuru Durumu Dağılımı';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $statusLabels = [
            'burs_havuzu' => 'Burs Havuzu',
            'on_kabul' => 'Ön Kabul',
            'red_edildi' => 'Reddedildi',
            'evrak_bekleniyor' => 'Evrak Bekleniyor',
            'evrak_incelemede' => 'Evrak İncelemede',
            'mulakat_havuzu' => 'Mülakat Havuzu',
            'mulakat_planlandi' => 'Mülakat Planlandı',
            'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
            'kabul_edildi' => 'Kabul Edildi',
            'kesin_kabul' => 'Kesin Kabul',
            'onceki_burslu' => 'Önceki Burslu',
        ];

        $statusCounts = Applications::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $colors = [
            'burs_havuzu' => '#F59E0B',
            'on_kabul' => '#3B82F6',
            'red_edildi' => '#EF4444',
            'evrak_bekleniyor' => '#6B7280',
            'evrak_incelemede' => '#6B7280',
            'mulakat_havuzu' => '#06B6D4',
            'mulakat_planlandi' => '#06B6D4',
            'mulakat_tamamlandi' => '#10B981',
            'kabul_edildi' => '#10B981',
            'kesin_kabul' => '#10B981',
            'onceki_burslu' => '#6B7280',
        ];

        $datasets = [];
        $labels = [];

        foreach ($statusCounts as $status => $count) {
            $labels[] = $statusLabels[$status] ?? $status;
            $datasets[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Başvuru Sayısı',
                    'data' => $datasets,
                    'backgroundColor' => array_values(array_intersect_key($colors, array_flip(array_keys($statusCounts)))),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
} 