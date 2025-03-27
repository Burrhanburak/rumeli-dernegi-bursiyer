<?php

namespace App\Filament\Resources\User\ScholarshipApprovalResource\Pages;

use App\Filament\Resources\User\ScholarshipApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateScholarshipApproval extends CreateRecord
{
    protected static string $resource = ScholarshipApprovalResource::class;

    protected static ?string $title = 'Yeni Burs Kabulü';

    protected static ?string $breadcrumb = 'Burs Kabulü';

    protected static ?string $breadcrumbParent = 'Burs Kabulü';
}
