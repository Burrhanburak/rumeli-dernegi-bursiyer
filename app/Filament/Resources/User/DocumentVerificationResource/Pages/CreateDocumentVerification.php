<?php

namespace App\Filament\Resources\User\DocumentVerificationResource\Pages;

use App\Filament\Resources\User\DocumentVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentVerification extends CreateRecord
{
    protected static string $resource = DocumentVerificationResource::class;

    protected static ?string $title = 'Yeni Belgelerin Doğrulanması';

    protected static ?string $breadcrumb = 'Belgelerin Doğrulanması';

    protected static ?string $breadcrumbParent = 'Belgelerin Doğrulanması';
}
