<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\Pages;

use App\Filament\Resources\User\ApplicationManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicationManagement extends EditRecord
{
    protected static string $resource = ApplicationManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Başvuru Yönetimi Düzenle';
}
