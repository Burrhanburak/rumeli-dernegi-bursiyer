<?php

namespace App\Filament\User\Resources\DocumentUploadResource\Pages;

use App\Filament\User\Resources\DocumentUploadResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentUpload extends CreateRecord
{
    protected static string $resource = DocumentUploadResource::class;

    protected static ?string $title = 'Evrak Yükle';

    protected static ?string $breadcrumb = 'Evrak Yükle';

    protected static ?string $breadcrumbParent = 'Kullanıcı';
}
