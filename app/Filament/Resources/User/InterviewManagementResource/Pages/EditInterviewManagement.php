<?php

namespace App\Filament\Resources\User\InterviewManagementResource\Pages;

use App\Filament\Resources\User\InterviewManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterviewManagement extends EditRecord
{
    protected static string $resource = InterviewManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Mülakat Yönetimi Düzenle';

    protected static ?string $breadcrumb = 'Mülakat Yönetimi';

    protected static ?string $breadcrumbParent = 'Mülakat Yönetimi';
}
