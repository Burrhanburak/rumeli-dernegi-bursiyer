<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDocumentType extends ViewRecord
{
    protected static string $resource = DocumentTypeResource::class;

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

    protected static ?string $title = 'Belge Türü Görüntüle';

    protected static ?string $breadcrumb = 'Belge Türü Görüntüle';

    protected static ?string $breadcrumbParent = 'Belge Türleri';

}