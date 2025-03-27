<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\Pages;

use App\Filament\Resources\User\ApplicationManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicationManagement extends ListRecords
{
    protected static string $resource = ApplicationManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Başvuru Yönetimi')
                ->icon('heroicon-o-user-group'),
        ];
    }

    protected static ?string $title = 'Başvuru Yönetimi';

    protected static ?string $breadcrumb = 'Başvuru Yönetimi';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';
}
