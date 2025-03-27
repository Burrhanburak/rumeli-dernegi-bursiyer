<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTypes extends ListRecords
{
    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Belge Türü')
                ->icon('heroicon-o-plus')
               
        ];
    }

    protected static ?string $title = 'Belge Türleri';

    protected static ?string $breadcrumb = 'Belge Türleri listesi';

    protected static ?string $breadcrumbParent = 'Belge Yönetimi';

    
}
