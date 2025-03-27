<?php

namespace App\Filament\User\Resources\NotificationResource\Pages;

use App\Filament\User\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
    protected static ?string $title = 'Yeni Bildirim';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';

    protected static ?string $createButtonIcon = 'heroicon-o-bell';

}
