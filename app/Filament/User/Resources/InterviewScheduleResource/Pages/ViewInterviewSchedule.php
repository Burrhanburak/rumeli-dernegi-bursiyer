<?php

namespace App\Filament\User\Resources\InterviewScheduleResource\Pages;

use App\Filament\User\Resources\InterviewScheduleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInterviewSchedule extends ViewRecord
{
    protected static string $resource = InterviewScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\EditAction::make(),
        ];
    }
    protected static ?string $title = 'Mülakat Tarihiniz';

    protected static ?string $breadcrumb = 'Mülakat Tarihiniz';


}
