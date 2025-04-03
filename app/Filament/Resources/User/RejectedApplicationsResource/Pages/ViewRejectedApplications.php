<?php

namespace App\Filament\Resources\User\RejectedApplicationsResource\Pages;

use App\Filament\Resources\User\RejectedApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRejectedApplications extends ViewRecord
{
    protected static string $resource = RejectedApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reactivate_application')
                ->label('Başvuruyu Yeniden Aktifleştir')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->action(function () {
                    $record = $this->getRecord();
                    $record->status = 'awaiting_evaluation';
                    $record->rejection_reason = null;
                    $record->rejection_date = null;
                    $record->save();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Başvuru Aktifleştirildi')
                        ->body('Başvuru yeniden değerlendirme havuzuna alındı.')
                        ->success()
                        ->send();
                    
                    return redirect()->route('filament.admin.resources.user.rejected-applications.index');
                })
                ->requiresConfirmation()
                ->modalHeading('Başvuru yeniden aktifleştirilsin mi?')
                ->modalDescription('Bu başvuruyu yeniden değerlendirmeye almak istediğinizden emin misiniz?')
                ->modalSubmitActionLabel('Evet, Aktifleştir'),
        ];
    }

    protected static ?string $title = 'Reddedilen Başvuru Detayı';

    protected static ?string $breadcrumb = 'Reddedilen Başvuru';
} 