<?php

namespace App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;

use App\Filament\Resources\User\ApplicationPreEvaluationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApplicationPreEvaluation extends CreateRecord
{
    protected static string $resource = ApplicationPreEvaluationResource::class;

    protected static ?string $title = 'Yeni Başvuru Oluştur';

    protected static ?string $breadcrumb = 'Yeni Başvuru';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
} 