<?php

namespace App\Filament\Widgets;

use App\Models\Applications;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Carbon\Carbon;

class BasvuruZamanGrafigi extends ChartWidget
{
    protected static ?string $heading = 'Aylık Başvuru Grafiği';

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Trend::model(Applications::class)
            ->between(
                start: now()->subMonths(12),
                end: now(),
            )
            ->perMonth()
            ->count();

        $turkishMonths = [
            1 => 'Ocak',
            2 => 'Şubat',
            3 => 'Mart',
            4 => 'Nisan',
            5 => 'Mayıs',
            6 => 'Haziran',
            7 => 'Temmuz',
            8 => 'Ağustos',
            9 => 'Eylül',
            10 => 'Ekim',
            11 => 'Kasım',
            12 => 'Aralık',
        ];

        $labels = $data->map(function (TrendValue $value) use ($turkishMonths) {
            $date = Carbon::parse($value->date);
            return $turkishMonths[$date->month] . ' ' . $date->year;
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Aylık Başvuru Sayısı',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
} 