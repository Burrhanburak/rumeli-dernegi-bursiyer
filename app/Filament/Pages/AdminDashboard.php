<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BasvuruDagilimGrafigi;
use App\Filament\Widgets\BasvuruZamanGrafigi;
use App\Filament\Widgets\UniversiteDagilimGrafigi;
use App\Filament\Widgets\AplicationsChart;
use App\Filament\Widgets\AdvancedStatsOverviewWidget;
use App\Filament\Widgets\LineChart;
class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.admin-dashboard';

    protected static ?string $title = 'Yönetim Paneli';

    protected static ?string $navigationLabel = 'Admin Paneli';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
            AplicationsChart::class,
            LineChart::class,
            AdvancedStatsOverviewWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BasvuruDagilimGrafigi::class,
         
        ];
    }

    public function getColumns(): int | array
    {
        return 3;
    }
}
