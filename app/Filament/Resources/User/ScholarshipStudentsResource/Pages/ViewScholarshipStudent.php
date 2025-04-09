<?php

namespace App\Filament\Resources\User\ScholarshipStudentsResource\Pages;

use App\Filament\Resources\User\ScholarshipStudentsResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewScholarshipStudent extends ViewRecord
{
    protected static string $resource = ScholarshipStudentsResource::class;

    protected static ?string $title = 'Burslu Öğrenci Detayları';

    protected static ?string $breadcrumb = 'Öğrenci Detayları';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('suspend')
                ->label('Askıya Al')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Askıya Alma Sebebi')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $record = $this->record;
                    $record->status = 'suspended';
                    $record->status_reason = $data['reason'];
                    $record->last_updated_by = auth()->id();
                    $record->save();
                    
                    // Bildirim gönder
                    \App\Models\Notifications::create([
                        'notifiable_id' => $record->user_id,
                        'notifiable_type' => \App\Models\User::class,
                        'title' => 'Bursunuz Askıya Alındı',
                        'message' => 'Bursunuz askıya alındı. Sebep: ' . $data['reason'],
                        'type' => 'scholarship_status',
                        'application_id' => $record->application_id,
                        'is_read' => false,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Askıya Alındı')
                        ->body('Öğrencinin bursu askıya alındı ve bildirim gönderildi')
                        ->success()
                        ->send();
                        
                    $this->redirect(ScholarshipStudentsResource::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'active'),
            Actions\Action::make('terminate')
                ->label('Sonlandır')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Sonlandırma Sebebi')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $record = $this->record;
                    $record->status = 'terminated';
                    $record->status_reason = $data['reason'];
                    $record->last_updated_by = auth()->id();
                    $record->save();
                    
                    // Bildirim gönder
                    \App\Models\Notifications::create([
                        'notifiable_id' => $record->user_id,
                        'notifiable_type' => \App\Models\User::class,
                        'title' => 'Bursunuz Sonlandırıldı',
                        'message' => 'Bursunuz sonlandırıldı. Sebep: ' . $data['reason'],
                        'type' => 'scholarship_status',
                        'application_id' => $record->application_id,
                        'is_read' => false,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Sonlandırıldı')
                        ->body('Öğrencinin bursu sonlandırıldı ve bildirim gönderildi')
                        ->success()
                        ->send();
                        
                    $this->redirect(ScholarshipStudentsResource::getUrl('index'));
                })
                ->visible(fn () => in_array($this->record->status, ['active', 'suspended'])),
            Actions\Action::make('activate')
                ->label('Aktifleştir')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $record = $this->record;
                    $record->status = 'active';
                    $record->status_reason = null;
                    $record->last_updated_by = auth()->id();
                    $record->save();
                    
                    // Bildirim gönder
                    \App\Models\Notifications::create([
                        'notifiable_id' => $record->user_id,
                        'notifiable_type' => \App\Models\User::class,
                        'title' => 'Bursunuz Aktifleştirildi',
                        'message' => 'Bursunuz tekrar aktifleştirildi.',
                        'type' => 'scholarship_status',
                        'application_id' => $record->application_id,
                        'is_read' => false,
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Aktifleştirildi')
                        ->body('Öğrencinin bursu aktifleştirildi ve bildirim gönderildi')
                        ->success()
                        ->send();
                        
                    $this->redirect(ScholarshipStudentsResource::getUrl('index'));
                })
                ->visible(fn () => $this->record->status === 'suspended'),
        ];
    }


    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Burslu Öğrenci Düzenlendi')
            ->body('Burslu öğrenci başarıyla düzenlendi.')
            ->send();
    }

    protected function getViewFormAction(): Actions\Action
    {
        return parent::getViewFormAction()
            ->label('Görüntüle');
    }

    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('İptal');
    }

    
    
} 