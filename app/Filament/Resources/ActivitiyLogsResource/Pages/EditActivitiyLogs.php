<?php

namespace App\Filament\Resources\ActivitiyLogsResource\Pages;

use App\Filament\Resources\ActivitiyLogsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditActivitiyLogs extends EditRecord
{
    protected static string $resource = ActivitiyLogsResource::class;

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
    

    protected static ?string $title = 'Etkinlik Kaydı Düzenle';

    protected static ?string $breadcrumb = 'Etkinlik Kaydı Düzenle';

    protected static ?string $breadcrumbParent = 'Etkinlik Kayıtları';
}
