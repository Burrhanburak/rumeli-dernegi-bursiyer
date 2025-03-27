<?php

namespace App\Filament\User\Resources\NotificationResource\Pages;

use App\Filament\User\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }

    protected static ?string $title = 'Bildirim Görüntüle';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';
}
