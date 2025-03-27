<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Belgeler')
                ->icon('heroicon-o-document-plus')
                
        ];
    }

    protected static ?string $title = 'Belgeler';

    protected static ?string $breadcrumb = 'Belgeler listesi';

    protected static ?string $breadcrumbParent = 'Belgeler';
    
}
