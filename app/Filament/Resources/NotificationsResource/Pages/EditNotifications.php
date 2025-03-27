<?php

namespace App\Filament\Resources\NotificationsResource\Pages;

use App\Filament\Resources\NotificationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNotifications extends EditRecord
{
    protected static string $resource = NotificationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye')
                ->color('info'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),  
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Kaydet')
                ->submit('save')
                ->keyBindings(['mod+s']),
            Actions\Action::make('cancel')
                ->label('İptal')
                ->url($this->previousUrl ?? static::getResource()::getUrl())
                ->color('secondary'),
        ];
}

    protected static ?string $title = 'Bildirim Düzenle';

    protected static ?string $breadcrumb = 'Bildirimler';

    protected static ?string $breadcrumbParent = 'Bildirimler';

}