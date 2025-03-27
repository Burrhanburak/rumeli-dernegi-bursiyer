<?php

namespace App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;

use App\Filament\Resources\User\ApplicationPreEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicationPreEvaluation extends EditRecord
{
    protected static string $resource = ApplicationPreEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make()
                ->label('Görüntüle')
                ->icon('heroicon-o-eye'),
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Başvuru Düzenle';

    protected static ?string $breadcrumb = 'Düzenle';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 