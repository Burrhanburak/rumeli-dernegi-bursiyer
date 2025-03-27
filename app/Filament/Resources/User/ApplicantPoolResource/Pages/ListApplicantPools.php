<?php

namespace App\Filament\Resources\User\ApplicantPoolResource\Pages;

use App\Filament\Resources\User\ApplicantPoolResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicantPools extends ListRecords
{
    protected static string $resource = ApplicantPoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Başvuru Havuzu')
                ->icon('heroicon-o-user-group'),
        ];
    }

    protected static ?string $title = 'Başvuru Havuzu Yönetimi';

    protected static ?string $breadcrumb = 'Başvuru Havuzu Yönetimi';

    protected static ?string $breadcrumbParent = 'Başvuru Havuzu';
}
