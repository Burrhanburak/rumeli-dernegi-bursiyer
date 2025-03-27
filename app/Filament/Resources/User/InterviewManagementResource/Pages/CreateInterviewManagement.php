<?php

namespace App\Filament\Resources\User\InterviewManagementResource\Pages;

use App\Filament\Resources\User\InterviewManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInterviewManagement extends CreateRecord
{
    protected static string $resource = InterviewManagementResource::class;

    protected static ?string $title = 'Yeni Mülakat Yönetimi';

    protected static ?string $breadcrumb = 'Mülakat Yönetimi';

    protected static ?string $breadcrumbParent = 'Mülakat Yönetimi';
}
