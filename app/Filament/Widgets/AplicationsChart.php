<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AplicationsChart extends ChartWidget
{
    protected static ?string $heading = 'Başvuru Sayısı';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Başvuru Sayısı',
                    'data' => [10, 20, 30, 40, 50],
                ],
            ],
            'labels' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs'],
            'colors' => ['#000000'],
            'borderColor' => '#000000',
            'fill' => false,
            'tension' => 0.4,
            'borderWidth' => 1,
            'borderRadius' => 4,
            'borderSkipped' => false,
            
            
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
