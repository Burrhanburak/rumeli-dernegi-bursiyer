<?php

namespace App\Filament\Resources\User\InterviewManagementResource\Pages;

use App\Filament\Resources\User\InterviewManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInterviewManagement extends ViewRecord
{
    protected static string $resource = InterviewManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Düzenle')
                ->icon('heroicon-o-pencil')
                ->color('warning'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Mülakat Yönetimi';

    protected static ?string $breadcrumb = 'Mülakat Yönetimi';

    protected static ?string $breadcrumbParent = 'Mülakat Yönetimi';
} 