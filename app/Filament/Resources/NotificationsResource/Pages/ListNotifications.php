<?php

namespace App\Filament\Resources\NotificationsResource\Pages;

use App\Filament\Resources\NotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Bildirim')
                ->icon('heroicon-o-bell')
                
        ];
    }
    protected static ?string $title = 'Bildirimler';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';

    protected static ?string $createButtonIcon = 'heroicon-o-bell';

    protected static ?string $createButtonColor = 'success';

    protected static ?string $createButtonTooltip = 'Yeni Bildirim Oluştur';

}
