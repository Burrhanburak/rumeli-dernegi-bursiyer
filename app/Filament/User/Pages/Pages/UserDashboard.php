<?php

namespace App\Filament\User\Pages\Pages;

use Filament\Pages\Page;
use App\Filament\User\Widgets\StatsOverview;

class UserDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static string $view = 'filament.user.pages.pages.user-dashboard';

    protected static ?string $slug = 'user-dashboard';
    protected static ?string $title = 'Rumeli Türkleri Derneği';

    protected function getHeaderWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }
}
