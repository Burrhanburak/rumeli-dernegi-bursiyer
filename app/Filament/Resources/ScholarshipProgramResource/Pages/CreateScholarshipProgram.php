<?php

namespace App\Filament\Resources\ScholarshipProgramResource\Pages;

use App\Filament\Resources\ScholarshipProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScholarshipProgram extends CreateRecord
{
    protected static string $resource = ScholarshipProgramResource::class;

    protected static ?string $title = 'Yeni Program';

    protected static ?string $breadcrumb = 'Programlar';

    protected static ?string $breadcrumbParent = 'Programlar';

}
