<?php

namespace App\Filament\Resources\User\ScholarshipApprovalResource\Pages;

use App\Filament\Resources\User\ScholarshipApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use App\Models\Scholarships;
use App\Models\Notifications;

class EditScholarshipApproval extends EditRecord
{
    protected static string $resource = ScholarshipApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Sil')
                ->icon('heroicon-o-trash')
                ->color('danger'),
        ];
    }

    protected static ?string $title = 'Burs Kabulü Düzenle';

    protected static ?string $breadcrumb = 'Burs Kabulü';

    protected static ?string $breadcrumbParent = 'Burs Kabulü';

    protected function afterSave(): void
    {
        try {
            \DB::beginTransaction();
            
            $record = $this->record;
            $data = $this->data;
            
            // Onay durumuna göre işlem yap
            if ($data['approval_status'] === 'approve') {
                // Kullanıcı onaylandı
                $record->status = 'kabul_edildi';
                $record->scholarship_amount = $data['scholarship_amount'];
                $record->scholarship_start_date = $data['scholarship_start_date'];
                $record->scholarship_end_date = $data['scholarship_end_date'];
                $record->approval_notes = $data['approval_notes'] ?? null;
                $record->approval_date = \Carbon\Carbon::now();
                $record->save();
                
                // Kullanıcı rolünü düzenle
                if ($record->user) {
                    $record->user->is_admin = false;
                    $record->user->save();
                }
                
                // Bildirim oluştur
                \App\Models\Notifications::create([
                    'notifiable_id' => $record->user_id,
                    'notifiable_type' => \App\Models\User::class,
                    'title' => 'Bursunuz Onaylandı',
                    'message' => 'Başvurunuz kabul edildi ve bursunuz onaylandı. Burs tutarı: ' . $data['scholarship_amount'] . ' ₺',
                    'type' => 'application_status',
                    'application_id' => $record->id,
                    'is_read' => false,
                ]);
                
                // Varsa eski burs kaydını kontrol et
                $existingScholarship = \App\Models\Scholarships::where('application_id', $record->id)->first();
                
                if ($existingScholarship) {
                    // Varolan burs kaydını güncelle
                    $existingScholarship->update([
                        'amount' => (float) $data['scholarship_amount'],
                        'start_date' => \Carbon\Carbon::parse($data['scholarship_start_date'])->format('Y-m-d'),
                        'end_date' => \Carbon\Carbon::parse($data['scholarship_end_date'])->format('Y-m-d'),
                        'status' => 'active',
                        'notes' => $data['approval_notes'] ?? null,
                        'last_updated_by' => auth()->id(),
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Kaydı Güncellendi')
                        ->body('Öğrenci için burs kaydı başarıyla güncellendi')
                        ->success()
                        ->send();
                } else {
                    // Yeni burs kaydı oluştur
                    $scholarship = new \App\Models\Scholarships([
                        'user_id' => $record->user_id,
                        'program_id' => $record->program_id,
                        'application_id' => $record->id,
                        'approved_by' => auth()->id(),
                        'name' => 'Standart Burs',
                        'start_date' => \Carbon\Carbon::parse($data['scholarship_start_date'])->format('Y-m-d'),
                        'end_date' => \Carbon\Carbon::parse($data['scholarship_end_date'])->format('Y-m-d'),
                        'amount' => (float) $data['scholarship_amount'],
                        'status' => 'active',
                        'notes' => $data['approval_notes'] ?? null,
                    ]);
                    
                    $scholarship->save();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('Burs Kaydı Oluşturuldu')
                        ->body('Öğrenci için burs kaydı başarıyla oluşturuldu')
                        ->success()
                        ->send();
                }
                
            } else if ($data['approval_status'] === 'reject') {
                // Kullanıcı reddedildi
                $record->status = 'red_edildi';
                $record->rejection_reason = $data['rejection_reason'];
                $record->rejected_by = auth()->id();
                $record->rejected_at = now();
                $record->save();
                
                // Bildirim oluştur
                \App\Models\Notifications::create([
                    'notifiable_id' => $record->user_id,
                    'notifiable_type' => \App\Models\User::class,
                    'title' => 'Başvurunuz Reddedildi',
                    'message' => 'Başvurunuz değerlendirilmiş ve reddedilmiştir. Red nedeni: ' . $data['rejection_reason'],
                    'type' => 'application_status',
                    'application_id' => $record->id,
                    'is_read' => false,
                ]);
                
                // Kullanıcıya bildirim gönder
                \Filament\Notifications\Notification::make()
                    ->title('Bildirim Gönderildi')
                    ->body('Kullanıcıya ret bildirimi gönderildi')
                    ->success()
                    ->send();
            }
            
            \DB::commit();
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            \Filament\Notifications\Notification::make()
                ->title('Hata')
                ->body('Burs işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}
