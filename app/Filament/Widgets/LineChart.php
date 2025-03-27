<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class LineChart extends ChartWidget
{
    protected static ?string $heading = 'Mülakat Planlananlar';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Mülakat Planlananlar',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],

            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            'options' => [
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                    ],
                ],
            ],
            
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
