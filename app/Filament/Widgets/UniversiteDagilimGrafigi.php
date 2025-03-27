<?php

namespace App\Filament\Widgets;

use App\Models\Applications;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UniversiteDagilimGrafigi extends ChartWidget
{
    protected static ?string $heading = 'Üniversite Dağılım Grafiği';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $universityCounts = Applications::query()
            ->select('okul_adi', DB::raw('count(*) as total'))
            ->groupBy('okul_adi')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->pluck('total', 'okul_adi')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Başvuru Sayısı',
                    'data' => array_values($universityCounts),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                    ],
                    'borderColor' => [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                        'rgb(153, 102, 255)',
                        'rgb(255, 159, 64)',
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(255, 206, 86)',
                        'rgb(75, 192, 192)',
                    ],
                    'borderWidth' => 1
                ],
            ],
            'labels' => array_keys($universityCounts),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
} 