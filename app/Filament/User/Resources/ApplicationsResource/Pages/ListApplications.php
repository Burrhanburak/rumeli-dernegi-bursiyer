<?php

namespace App\Filament\User\Resources\ApplicationsResource\Pages;

use App\Filament\User\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Başvuru Oluştur')
            ->icon('heroicon-o-plus'),
           
        
        ];
    }
    protected static ?string $title = 'Başvurular';
    protected static ?string $breadcrumb = 'Başvurular';
}
