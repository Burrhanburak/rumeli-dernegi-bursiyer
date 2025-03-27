<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplications extends EditRecord
{
    protected static string $resource = ApplicationsResource::class;

    protected static ?string $title = 'Başvuru Düzenle';

    protected static ?string $breadcrumb = 'Düzenle';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle'),
            Actions\DeleteAction::make()
                ->label('Sil'),
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
}