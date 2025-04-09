<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInterview extends CreateRecord
{
    protected static string $resource = InterviewResource::class;
    
    protected static ?string $title = 'Yeni Mülakat';

    protected static ?string $breadcrumb = 'Yeni Mülakat';

    protected static ?string $breadcrumbParent = 'Mülakatlar';

    protected static ?string $createButtonLabel = 'Yeni Mülakat';

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Oluştur');
    }

}
