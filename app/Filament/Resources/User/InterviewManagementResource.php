<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\InterviewManagementResource\Pages;
use App\Filament\Resources\User\InterviewManagementResource\RelationManagers;
use App\Models\Interviews;
use App\Models\Applications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class InterviewManagementResource extends Resource
{
    protected static ?string $model = Interviews::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['application', 'interviewer'])
            ->whereNotNull('application_id');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static ?string $navigationLabel = 'Mülakat Havuzu';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $navigationGroup = 'Mülakat Yönetimi';

    protected static ?string $title = 'Mülakat Havuzu';

    protected static ?string $breadcrumb = 'Mülakat Havuzu';

    protected static ?string $breadcrumbParent = 'Mülakat Havuzu';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mülakat Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('application_id')
                            ->label('Başvuru')
                            ->options(function () {
                                return Applications::whereIn('status', ['mulakat_havuzu', 'mulakat_planlandi', 'mulakat_tamamlandi'])
                                    ->get()
                                    ->pluck('id', 'id')
                                    ->map(function ($id) {
                                        $application = Applications::find($id);
                                        return 'ID: ' . $id . ' - ' . $application->user->name . ' - ' . $application->program->name;
                                    });
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('interviewer_id')
                            ->label('Mülakatçı')
                            ->relationship('interviewer', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\DateTimePicker::make('scheduled_date')
                            ->label('Planlanan Tarih')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->label('Online Mülakat mı?')
                            ->inline(false)
                            ->onIcon('heroicon-m-globe-alt')
                            ->offIcon('heroicon-m-building-office')
                            ->onColor('success')
                            ->offColor('gray')
                            ->default(fn (Interviews $record) => $record->is_online ?? false)
                            ->live(),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->prefix('https://')
                            ->placeholder('Örn: zoom.us/j/123456789')
                            ->visible(fn (Forms\Get $get) => $get('is_online'))
                            ->default(function (Interviews $record) {
                                if ($record->meeting_link) {
                                    // https:// prefix'ini kaldır
                                    return preg_replace('~^https?://~i', '', $record->meeting_link);
                                }
                                return null;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (empty($state)) {
                                    return null;
                                }
                                
                                // URL'e http veya https ön eki yoksa ekle
                                if (!preg_match('~^(?:f|ht)tps?://~i', $state)) {
                                    return 'https://' . $state;
                                }
                                
                                return $state;
                            }),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'awaiting_schedule' => 'Planlama Bekliyor',
                                'scheduled' => 'Planlandı',
                                'completed' => 'Tamamlandı',
                                'canceled' => 'İptal Edildi',
                                'rescheduled' => 'Yeniden Planlandı',
                                'no_show' => 'Katılım Olmadı',
                                'confirmed' => 'Katılım Onaylandı',
                            ])
                            ->required()
                            ->default('scheduled'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('feedback')
                            ->label('Geri Bildirim')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('score')
                            ->label('Puan (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Mülakat yönetiminde mülakat bulunamadı')
            ->emptyStateDescription('Yeni bir mülakat oluşturmak için "Yeni Mülakat Yönetimi" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Mülakat Yönetimi')
                    ->icon('heroicon-o-user-group'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application.user.name')
                    ->label('Aday')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application.program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->label('Mülakatçı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Tarih & Saat')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        'rescheduled' => 'warning',
                        'no_show' => 'gray',
                        'confirmed' => 'primary',
                        'awaiting_schedule' => 'secondary',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                        'confirmed' => 'Katılım Onaylandı',
                        'awaiting_schedule' => 'Planlama Bekliyor',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Konum')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('meeting_link')
                    ->label('Toplantı Linki')
                    ->url(fn (string $state): string => $state)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('score')
                    ->label('Puan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'awaiting_schedule' => 'Planlama Bekliyor',
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                        'confirmed' => 'Katılım Onaylandı',
                    ]),
                Tables\Filters\SelectFilter::make('interviewer_id')
                    ->label('Mülakatçı')
                    ->relationship('interviewer', 'name'),
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->relationship('application.program', 'name'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Yaklaşan Mülakatlar')
                    ->query(fn (Builder $query): Builder => $query->where('scheduled_date', '>=', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('scheduled_date')
                    ->label('Tarih Aralığı')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('scheduled_until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mülakat Silme')
                    ->modalDescription('Bu mülakatı silmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->modalCancelActionLabel('İptal')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Silme Sebebi')
                            ->required(),
                    ])
                    ->action(function (Interviews $record) {
                        $record->delete();
                    }),
                Tables\Actions\Action::make('complete_interview')
                    ->label('Mülakatı Tamamla')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->form([
                        Forms\Components\Textarea::make('feedback')
                            ->label('Geri Bildirim')
                            ->required(),
                        Forms\Components\TextInput::make('score')
                            ->label('Puan (0-100)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\Select::make('interview_result')
                            ->label('Mülakat Sonucu')
                            ->options([
                                'passed' => 'Başarılı',
                                'failed' => 'Başarısız',
                                'pending' => 'Değerlendirmede',
                            ])
                            ->required()
                            ->default('pending'),
                    ])
                    ->modalHeading('Mülakat Tamamla')
                    ->modalDescription('Mülakatı tamamlamak istediğinize emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Tamamla')
                    ->action(function (Interviews $record, array $data) {
                        // Mülakat bilgilerini güncelle
                        $record->status = 'completed';
                        $record->feedback = $data['feedback'];
                        $record->score = $data['score'];
                        $record->interview_score = $data['score'];
                        $record->interview_result = $data['interview_result'];
                        $record->completion_date = now();
                        $record->save();
                        
                        // Başvuru durumunu güncelle
                        if ($record->application) {
                            // Mülakat sonucuna göre başvuru durumunu ayarla
                            if ($data['interview_result'] === 'passed') {
                                $record->application->status = 'mulakat_tamamlandi';
                                $record->application->interview_result = 'passed';
                            } else if ($data['interview_result'] === 'failed') {
                                $record->application->status = 'red_edildi';
                                $record->application->interview_result = 'failed';
                                $record->application->rejection_reason = 'Mülakat başarısız: ' . $data['feedback'];
                                $record->application->rejected_by = auth()->id();
                                $record->application->rejected_at = now();
                            } else {
                                $record->application->status = 'mulakat_tamamlandi';
                                $record->application->interview_result = 'pending';
                            }
                            
                            $record->application->is_interview_completed = true;
                            $record->application->interview_score = $data['score'];
                            $record->application->save();
                        }
                    })
                    ->visible(fn (Interviews $record): bool => in_array($record->status, ['scheduled', 'confirmed'])),
                Tables\Actions\Action::make('transfer_to_scholarship')
                    ->label('Bursa Aktar')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('success')
                    ->action(function (Interviews $record) {
                        if ($record->application) {
                            $record->application->status = 'accepted';
                            $record->application->accepted_by = auth()->id();
                            $record->application->accepted_at = now();
                            $record->application->save();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Burs Onayına Aktarıldı')
                                ->body('Başvuru burs onay sürecine aktarıldı.')
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (Interviews $record): bool => 
                    $record->status === 'completed' && 
                    $record->application &&  
                    $record->application->status !== 'accepted' && 
                    $record->application->status !== 'final_acceptance' && 
                    $record->application->status !== 'mulakat_havuzu' && 
                    $record->application->status !== 'mulakat_planlandi' && 
                    // $record->application->status !== 'mulakat_tamamlandi' && 
                    $record->application->status !== 'dogrulama_tamamlandı' && 
                    $record->application->status !== 'dogrulama_tamamlandi' && 
                    $record->application->status !== 'Mülakat Planlandı' && 
                    $record->application->status !== 'interview_completed' && 
                    $record->application->status !== 'interview_scheduled' && 
                    $record->application->status !== 'rejected' && 
                    $record->application->status !== 'interview_pool'
                ),
                Tables\Actions\Action::make('cancel')
                    ->label('İptal Et')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('İptal Sebebi')
                            ->required(),
                    ])
                    ->action(function (Interviews $record, array $data) {
                        $record->status = 'canceled';
                        $record->notes = 'İptal Sebebi: ' . $data['cancellation_reason'] . "\n\n" . $record->notes;
                        $record->save();
                    })
                    ->visible(fn (Interviews $record): bool => in_array($record->status, ['scheduled', 'confirmed'])),
                Tables\Actions\Action::make('reschedule')
                    ->label('Yeniden Planla')
                    ->modalDescription('Lütfen yeni tarih ve saat bilgilerini giriniz.')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Yeniden Planla')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('new_date')
                            ->label('Yeni Tarih & Saat')
                        ->required()
                        ->minDate(now())
                        ->seconds(false)
                        ->displayFormat('d/m/Y H:i')
                        ->native(false),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->label('Online Mülakat mı?')
                            ->inline(false)
                            ->onIcon('heroicon-m-globe-alt')
                            ->offIcon('heroicon-m-building-office')
                            ->onColor('success')
                            ->offColor('gray')
                            ->default(fn (Interviews $record) => $record->is_online ?? false)
                            ->live(),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->prefix('https://')
                            ->placeholder('Örn: zoom.us/j/123456789')
                            ->visible(fn (Forms\Get $get) => $get('is_online'))
                            ->default(function (Interviews $record) {
                                if ($record->meeting_link) {
                                    // https:// prefix'ini kaldır
                                    return preg_replace('~^https?://~i', '', $record->meeting_link);
                                }
                                return null;
                            })
                            ->dehydrateStateUsing(function ($state) {
                                if (empty($state)) {
                                    return null;
                                }
                                
                                // URL'e http veya https ön eki yoksa ekle
                                if (!preg_match('~^(?:f|ht)tps?://~i', $state)) {
                                    return 'https://' . $state;
                                }
                                
                                return $state;
                            }),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->placeholder('Yeniden planlama sebebi ve açıklama')
                            ->maxLength(65535),
                    ])
                    ->action(function (Interviews $record, array $data) {
                        // Eski tarih bilgisini not olarak ekle
                        $oldDate = $record->scheduled_date ? $record->scheduled_date->format('d.m.Y H:i') : 'Belirsiz';
                        $notes = "Yeniden Planlama: Eski Tarih: {$oldDate}\n";
                        $notes .= isset($data['notes']) ? $data['notes'] . "\n\n" : "\n";
                        $notes .= $record->notes ?? '';
                        
                        // Mevcut kaydı güncelle
                        $record->status = 'scheduled'; // Direkt olarak planlanmış durumuna çevir
                        $record->scheduled_date = $data['new_date'];
                        $record->location = $data['location'] ?? $record->location;
                        $record->is_online = $data['is_online'] ?? false;
                        
                        // Online değilse meeting_link'i temizle, online ise güncelle
                        if (!$data['is_online']) {
                            $record->meeting_link = null;
                        } else if (isset($data['meeting_link'])) {
                            $record->meeting_link = $data['meeting_link'];
                        }
                        
                        $record->notes = $notes;
                        $record->save();
                        
                        // Başvuru durumunu güncelle
                        if ($record->application) {
                            $record->application->status = 'mulakat_planlandi';
                            $record->application->is_interview_scheduled = true;
                            $record->application->save();
                        }
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Mülakat Yeniden Planlandı')
                            ->body('Mülakat tarihi ve bilgileri başarıyla güncellendi.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Interviews $record): bool => in_array($record->status, ['scheduled', 'confirmed'])),
                Tables\Actions\Action::make('schedule_interview')
                    ->label('Planla')
                    ->icon('heroicon-o-calendar')
                    ->color('success')
                    ->form([
                        Forms\Components\DateTimePicker::make('scheduled_date')
                            ->label('Mülakat Tarihi ve Saati')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\Select::make('interviewer_id')
                            ->label('Mülakatçı')
                            ->options(
                                \App\Models\User::query()
                                    ->where('is_admin', true)
                                    ->get()
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->label('Online Mülakat mı?')
                            ->default(false)
                            ->live(),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->prefix('https://')
                            ->placeholder('Örn: zoom.us/j/123456789')
                            ->visible(fn (Forms\Get $get) => $get('is_online'))
                            ->dehydrateStateUsing(function ($state) {
                                if (empty($state)) {
                                    return null;
                                }
                                
                                // URL'e http veya https ön eki yoksa ekle
                                if (!preg_match('~^(?:f|ht)tps?://~i', $state)) {
                                    return 'https://' . $state;
                                }
                                
                                return $state;
                            }),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535),
                    ])
                    ->action(function (Interviews $record, array $data) {
                        // Mülakat bilgilerini güncelle
                        $record->status = 'scheduled';
                        $record->scheduled_date = $data['scheduled_date'];
                        $record->interviewer_admin_id = $data['interviewer_id'];
                        $record->location = $data['location'] ?? null;
                        $record->is_online = $data['is_online'] ?? false;
                        $record->meeting_link = $data['is_online'] ? $data['meeting_link'] : null;
                        
                        // Eski notları sakla
                        $existingNotes = $record->notes ?? '';
                        $record->notes = ($data['notes'] ?? '') . "\n\n" . $existingNotes;
                        $record->save();
                        
                        // Başvuru durumunu güncelle
                        if ($record->application) {
                            $record->application->status = 'mulakat_planlandi';
                            $record->application->is_interview_scheduled = true;
                            $record->application->save();
                        }
                        
                        // Bildirimi göster
                        \Filament\Notifications\Notification::make()
                            ->title('Mülakat Planlandı')
                            ->body('Başvuru için mülakat başarıyla planlandı.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Interviews $record): bool => $record->status === 'awaiting_schedule'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_complete')
                        ->label('Toplu Tamamla')
                        ->icon('heroicon-o-check-badge')
                        ->form([
                            Forms\Components\TextInput::make('score')
                                ->label('Puan (0-100)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(100)
                                ->required(),
                            Forms\Components\Select::make('interview_result')
                                ->label('Mülakat Sonucu')
                                ->options([
                                    'passed' => 'Başarılı',
                                    'failed' => 'Başarısız',
                                    'pending' => 'Değerlendirmede',
                                ])
                                ->required()
                                ->default('pending'),
                            Forms\Components\Textarea::make('feedback')
                                ->label('Toplu Geri Bildirim')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if (in_array($record->status, ['scheduled', 'confirmed'])) {
                                    // Mülakat bilgilerini güncelle
                                    $record->status = 'completed';
                                    $record->feedback = $data['feedback'];
                                    $record->score = $data['score'];
                                    $record->interview_score = $data['score'];
                                    $record->interview_result = $data['interview_result'];
                                    $record->completion_date = now();
                                    $record->save();
                                    
                                    // Başvuru durumunu güncelle
                                    if ($record->application) {
                                        // Mülakat sonucuna göre başvuru durumunu ayarla
                                        if ($data['interview_result'] === 'passed') {
                                            $record->application->status = 'mulakat_tamamlandi';
                                            $record->application->interview_result = 'passed';
                                        } else if ($data['interview_result'] === 'failed') {
                                            $record->application->status = 'red_edildi';
                                            $record->application->interview_result = 'failed';
                                            $record->application->rejection_reason = 'Mülakat başarısız: ' . $data['feedback'];
                                            $record->application->rejected_by = auth()->id();
                                            $record->application->rejected_at = now();
                                        } else {
                                            $record->application->status = 'mulakat_tamamlandi';
                                            $record->application->interview_result = 'pending';
                                        }
                                        
                                        $record->application->is_interview_completed = true;
                                        $record->application->interview_score = $data['score'];
                                        $record->application->save();
                                    }
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_cancel')
                        ->label('Toplu İptal')
                        ->icon('heroicon-o-x-mark')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if (in_array($record->status, ['scheduled', 'confirmed'])) {
                                    $record->status = 'canceled';
                                    $record->save();
                                }
                            }
                        }),
                    Tables\Actions\BulkAction::make('bulk_transfer_to_scholarship')
                        ->label('Toplu Bursa Aktar')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->color('success')
                        ->action(function (Collection $records) {
                            $transferredCount = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === 'completed' && 
                                    $record->application) {
                                    
                                    $record->application->status = 'accepted';
                                    $record->application->accepted_by = auth()->id();
                                    $record->application->accepted_at = now();
                                    $record->application->save();
                                    
                                    $transferredCount++;
                                }
                            }
                            
                            if ($transferredCount > 0) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Burs Onayına Aktarıldı')
                                    ->body($transferredCount . ' başvuru burs onay sürecine aktarıldı.')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('İşlem Yapılmadı')
                                    ->body('Aktarılabilecek uygun başvuru bulunamadı.')
                                    ->warning()
                                    ->send();
                            }
                        }),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                    Tables\Actions\BulkAction::make('bulk_schedule')
                        ->label('Toplu Planla')
                        ->icon('heroicon-o-calendar')
                        ->color('success')
                        ->form([
                            Forms\Components\DateTimePicker::make('scheduled_date')
                                ->label('Mülakat Tarihi ve Saati')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\Select::make('interviewer_id')
                                ->label('Mülakatçı')
                                ->options(
                                    \App\Models\User::query()
                                        ->where('is_admin', true)
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray()
                                )
                                ->searchable()
                                ->required(),
                            Forms\Components\TextInput::make('location')
                                ->label('Konum')
                                ->maxLength(255),
                            Forms\Components\Toggle::make('is_online')
                                ->label('Online Mülakat mı?')
                                ->default(false)
                                ->live(),
                            Forms\Components\TextInput::make('meeting_link')
                                ->label('Toplantı Linki')
                                ->prefix('https://')
                                ->placeholder('Örn: zoom.us/j/123456789')
                                ->visible(fn (Forms\Get $get) => $get('is_online'))
                                ->dehydrateStateUsing(function ($state) {
                                    if (empty($state)) {
                                        return null;
                                    }
                                    
                                    // URL'e http veya https ön eki yoksa ekle
                                    if (!preg_match('~^(?:f|ht)tps?://~i', $state)) {
                                        return 'https://' . $state;
                                    }
                                    
                                    return $state;
                                }),
                            Forms\Components\Textarea::make('notes')
                                ->label('Notlar')
                                ->maxLength(65535),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status === 'awaiting_schedule') {
                                    // Mülakat bilgilerini güncelle
                                    $record->status = 'scheduled';
                                    $record->scheduled_date = $data['scheduled_date'];
                                    $record->interviewer_admin_id = $data['interviewer_id'];
                                    $record->location = $data['location'] ?? null;
                                    $record->is_online = $data['is_online'] ?? false;
                                    $record->meeting_link = $data['is_online'] ? $data['meeting_link'] : null;
                                    
                                    // Eski notları sakla
                                    $existingNotes = $record->notes ?? '';
                                    $record->notes = ($data['notes'] ?? '') . "\n\n" . $existingNotes;
                                    $record->save();
                                    
                                    // Başvuru durumunu güncelle
                                    if ($record->application) {
                                        $record->application->status = 'mulakat_planlandi';
                                        $record->application->is_interview_scheduled = true;
                                        $record->application->save();
                                    }
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Mülakatlar Planlandı')
                                ->body('Seçili mülakatlar başarıyla planlandı.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->label('Mülakat İşlemleri')
            ->defaultSort('scheduled_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterviewManagement::route('/'),
            'create' => Pages\CreateInterviewManagement::route('/create'),
            'view' => Pages\ViewInterviewManagement::route('/{record}'),
            'edit' => Pages\EditInterviewManagement::route('/{record}/edit'),
        ];
    }
}
