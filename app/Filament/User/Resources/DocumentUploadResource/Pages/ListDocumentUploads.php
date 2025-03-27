<?php

namespace App\Filament\User\Resources\DocumentUploadResource\Pages;

use App\Filament\User\Resources\DocumentUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDocumentUploads extends ListRecords
{
    protected static string $resource = DocumentUploadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Evrak Yükle')
                ->icon('heroicon-o-document-arrow-up')
                ->color('success'),
        ];
    }
    protected static ?string $title = 'Evrak Yükleme';

    protected static ?string $breadcrumb = 'Evrak Yükleme';


}
