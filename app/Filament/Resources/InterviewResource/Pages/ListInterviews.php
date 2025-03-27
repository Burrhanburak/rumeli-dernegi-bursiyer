<?php

namespace App\Filament\Resources\InterviewResource\Pages;

use App\Filament\Resources\InterviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterviews extends ListRecords
{
    protected static string $resource = InterviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Mülakat'),
        ];
    }

    protected static ?string $title = 'Mülakatlar';

    protected static ?string $breadcrumb = 'Mülakatlar listesi';

    protected static ?string $breadcrumbParent = 'Görüşmeler';

    protected static ?string $createButtonLabel = 'Yeni Mülakat';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';
}
