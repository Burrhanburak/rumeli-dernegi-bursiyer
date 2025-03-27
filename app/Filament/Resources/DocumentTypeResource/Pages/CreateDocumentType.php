<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected static ?string $title = 'Yeni Belge Türü';

    protected static ?string $breadcrumb = 'Yeni Belge Türü';

    protected static ?string $breadcrumbParent = 'Belge Türleri';

}
