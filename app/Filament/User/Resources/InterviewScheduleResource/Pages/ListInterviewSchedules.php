<?php

namespace App\Filament\User\Resources\InterviewScheduleResource\Pages;

use App\Filament\User\Resources\InterviewScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterviewSchedules extends ListRecords
{
    protected static string $resource = InterviewScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected static ?string $title = 'Mülakat Tarihi';

    protected static ?string $breadcrumb = 'Mülakat Tarihi';

  
}
