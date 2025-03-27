<?php

namespace App\Filament\Resources\User\ApplicantPoolResource\Pages;

use App\Filament\Resources\User\ApplicantPoolResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicantPool extends CreateRecord
{
    protected static string $resource = ApplicantPoolResource::class;

    protected static ?string $title = 'Yeni Başvuru Havuzu';

    protected static ?string $breadcrumb = 'Başvuru Havuzu';

    protected static ?string $breadcrumbParent = 'Başvuru Havuzu';
}
