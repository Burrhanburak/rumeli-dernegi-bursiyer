<?php

namespace App\Filament\User\Resources\ScholarshipStatusResource\Pages;

use App\Filament\User\Resources\ScholarshipStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListScholarshipStatuses extends ListRecords
{
    protected static string $resource = ScholarshipStatusResource::class;

    protected function getHeaderActions(): array
    {
         return [
        //     Actions\CreateAction::make()
        //         ->label('Oluştur')
        //         ->icon('heroicon-o-plus')
        //         ->color('success'),
         ];
    }
    protected static ?string $title = 'Burs Durumum';

    protected static ?string $breadcrumb = 'Burs Durumum';

    
    
}
