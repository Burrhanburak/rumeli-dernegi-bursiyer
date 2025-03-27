<?php

namespace App\Filament\Resources\NotificationsResource\Pages;

use App\Filament\Resources\NotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNotifications extends ViewRecord
{
    protected static string $resource = NotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Düzenle')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

  

    protected static ?string $title = 'Bildirim Görüntüle';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';
}
