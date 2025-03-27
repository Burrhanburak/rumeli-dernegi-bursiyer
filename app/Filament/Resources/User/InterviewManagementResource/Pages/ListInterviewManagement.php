<?php

namespace App\Filament\Resources\User\InterviewManagementResource\Pages;

use App\Filament\Resources\User\InterviewManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterviewManagement extends ListRecords
{
    protected static string $resource = InterviewManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Mülakat Yönetimi')
                ->icon('heroicon-o-user-group'),
        ];
    }

    protected static ?string $title = 'Mülakat Yönetimi';

    protected static ?string $breadcrumb = 'Mülakat Yönetimi';

    protected static ?string $breadcrumbParent = 'Mülakat Yönetimi';
}
