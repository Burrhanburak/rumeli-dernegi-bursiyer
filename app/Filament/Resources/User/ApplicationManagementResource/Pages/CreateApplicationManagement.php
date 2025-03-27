<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\Pages;

use App\Filament\Resources\User\ApplicationManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationManagement extends CreateRecord
{
    protected static string $resource = ApplicationManagementResource::class;

    protected static ?string $title = 'Yeni Başvuru Yönetimi';

    protected static ?string $breadcrumb = 'Başvuru Yönetimi';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';
}
