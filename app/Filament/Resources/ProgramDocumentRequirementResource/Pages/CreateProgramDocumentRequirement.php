<?php

namespace App\Filament\Resources\ProgramDocumentRequirementResource\Pages;

use App\Filament\Resources\ProgramDocumentRequirementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgramDocumentRequirement extends CreateRecord
{
    protected static string $resource = ProgramDocumentRequirementResource::class;

    protected static ?string $createButtonIcon = 'heroicon-o-document-plus';

    protected static ?string $title = 'Yeni Belgeler';

    protected static ?string $breadcrumb = 'Belgeler';

    protected static ?string $breadcrumbParent = 'Belgeler';
}
