<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\Pages;

use App\Filament\Resources\User\ApplicationManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicationManagement extends ViewRecord
{
    protected static string $resource = ApplicationManagementResource::class;

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
} 