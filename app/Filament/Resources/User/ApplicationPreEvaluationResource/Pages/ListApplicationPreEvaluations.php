<?php

namespace App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;

use App\Filament\Resources\User\ApplicationPreEvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicationPreEvaluations extends ListRecords
{
    protected static string $resource = ApplicationPreEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->label('Yeni Başvuru')
            //     ->icon('heroicon-o-plus'),
        ];
    }

    protected static ?string $title = 'Başvuru Ön Değerlendirme';

    protected static ?string $breadcrumb = 'Ön Değerlendirme';
} 