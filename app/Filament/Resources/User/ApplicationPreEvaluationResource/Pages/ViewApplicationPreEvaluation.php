<?php

namespace App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;

use App\Filament\Resources\User\ApplicationPreEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewApplicationPreEvaluation extends ViewRecord
{
    protected static string $resource = ApplicationPreEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Düzenle')
                ->icon('heroicon-o-pencil'),
        ];
    }

    protected static ?string $title = 'Başvuru Detayı';

    protected static ?string $breadcrumb = 'Görüntüle';
} 