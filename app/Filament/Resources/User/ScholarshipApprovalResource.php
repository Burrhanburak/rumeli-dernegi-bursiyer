<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ScholarshipApprovalResource\Pages;
use App\Filament\Resources\User\ScholarshipApprovalResource\RelationManagers;
use App\Models\Applications;
use App\Models\User;
use App\Models\ScholarshipProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ScholarshipApprovalResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    
    protected static ?string $navigationLabel = 'Öğrenci Burs Onayları';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $navigationGroup = 'Burs Yönetimi';

    protected static ?string $title = 'Öğrenci Burs Onayları';

    protected static ?string $breadcrumb = 'Burs Yönetimi';

    protected static ?string $breadcrumbParent = 'Burs Onayları';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function($query) {
                $query->where('status', 'accepted')
                    ->orWhere('status', 'interview_completed')
                    ->orWhere('status', 'mulakat_tamamlandi')
                    ->orWhere('status', 'on_kabul')
                    ->orWhere('status', 'pre_approved');
            })
            ->whereDoesntHave('scholarships')
            ->orderBy('updated_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Başvuran')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(),
                        Forms\Components\TextInput::make('application_id')
                            ->label('Başvuru ID')
                            ->disabled(),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'beklemede' => 'Beklemede',
                                'belgeler_yuklendi' => 'Belgeler Yüklendi',
                                'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                                'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                                'mulakat_havuzu' => 'Mülakat Havuzu',
                                'mulakat_planlandi' => 'Mülakat Planlandı',
                                'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                                'accepted' => 'Kabul Edildi',
                                'red_edildi' => 'Reddedildi',
                            ])
                            ->disabled(),
                        Forms\Components\Checkbox::make('are_documents_approved')
                            ->label('Belgeler Doğrulandı')
                            ->disabled()
                            ->afterStateHydrated(function ($component, $record) {
                                // Belgeler doğrulandı olarak işaretle
                                $component->state(true);
                            }),
                        Forms\Components\Checkbox::make('is_interview_completed')
                            ->label('Mülakat Tamamlandı')
                            ->disabled()
                            ->afterStateHydrated(function ($component, $record) {
                                // Mülakat tamamlandı olarak işaretle
                                $component->state(true);
                            }),
                    ])->columns(2),
                
                Forms\Components\Section::make('Mülakat Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('interview')
                            ->label('Mülakat')
                            ->relationship('interviews', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "Mülakat ID: {$record->id} - Tarih: {$record->scheduled_date} - Mülakatçı: {$record->interviewer->name}")
                            ->disabled(),
                        Forms\Components\TextInput::make('interview_score')
                            ->label('Mülakat Puanı')
                            ->numeric()
                            ->disabled()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $interview = \App\Models\Interviews::where('application_id', $record->id)
                                        ->where('status', 'completed')
                                        ->first();
                                    
                                    if ($interview) {
                                        $component->state($interview->score);
                                    }
                                }
                            }),
                        Forms\Components\Textarea::make('interview_feedback')
                            ->label('Mülakat Geri Bildirimi')
                            ->disabled()
                            ->columnSpanFull()
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record) {
                                    $interview = \App\Models\Interviews::where('application_id', $record->id)
                                        ->where('status', 'completed')
                                        ->first();
                                    
                                    if ($interview) {
                                        $component->state($interview->feedback);
                                    }
                                }
                            }),
                    ])->columns(2),
                
                Forms\Components\Section::make('Burs Onayı')
                    ->schema([
                        Forms\Components\Radio::make('approval_status')
                            ->label('Onay Durumu')
                            ->options([
                                'approve' => 'Kabul Et',
                                'reject' => 'Reddet',
                            ])
                            ->default('approve')
                            ->inline()
                            ->required(),
                        Forms\Components\Select::make('scholarship_amount')
                            ->label('Burs Miktarı (₺)')
                            ->options([
                                '500' => '500 ₺',
                                '750' => '750 ₺',
                                '1000' => '1000 ₺',
                                '1500' => '1500 ₺',
                                '2000' => '2000 ₺',
                                '2500' => '2500 ₺',
                                '3000' => '3000 ₺',
                                '3500' => '3500 ₺',
                                '4000' => '4000 ₺',
                                '5000' => '5000 ₺',
                            ])
                            ->default('1000')
                            ->required()
                            ->helperText('Mülakat puanına göre otomatik önerilmiştir')
                            ->afterStateHydrated(function ($component, $state, $record) {
                                if ($record && !$state) {
                                    $interview = \App\Models\Interviews::where('application_id', $record->id)
                                        ->where('status', 'completed')
                                        ->first();
                                    
                                    if ($interview && $interview->score) {
                                        // Puana göre burs miktarı öner
                                        $score = $interview->score;
                                        if ($score >= 90) {
                                            $component->state('5000');
                                        } elseif ($score >= 80) {
                                            $component->state('3500');
                                        } elseif ($score >= 70) {
                                            $component->state('2500');
                                        } elseif ($score >= 60) {
                                            $component->state('1500');
                                        } else {
                                            $component->state('1000');
                                        }
                                    }
                                }
                            }),
                        Forms\Components\DateTimePicker::make('scholarship_start_date')
                            ->label('Burs Başlangıç Tarihi')
                            ->required()
                            ->minDate(now())
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                           
                            ->native(false),
                        Forms\Components\DateTimePicker::make('scholarship_end_date')
                            ->label('Burs Bitiş Tarihi')
                            ->minDate(now())
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->native(false)
                            ->required()
                            ->minDate(function ($get) {
                                $startDate = $get('scholarship_start_date');
                                return $startDate ? Carbon::parse($startDate)->addMonths(1) : now()->addMonths(1);
                            })
                            ->validationMessages([
                                'minDate' => 'Burs bitiş tarihi, başlangıç tarihinden sonra olmalıdır.',
                            ]),
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Onay Notları')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Sebebi')
                            ->required()
                            ->maxLength(65535)
                            ->visible(fn ($get) => $get('approval_status') === 'reject')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burs onaylarında başvuru bulunamadı')
            ->emptyStateDescription('Yeni bir başvuru oluşturmak için "Yeni Burs Onayı" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Burs Onayı')
                    ->icon('heroicon-o-user-group'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),

               
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beklemede' => 'gray',
                        'belgeler_yuklendi' => 'info',
                        'dogrulama_bekliyor' => 'warning',
                        'dogrulama_tamamlandi' => 'success',
                        'mulakat_havuzu' => 'primary',
                        'mulakat_planlandi' => 'primary',
                        'mulakat_tamamlandi' => 'success',
                        'kabul_edildi', 'accepted' => 'success',
                        'reddedildi' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                        'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi', 'accepted' => 'Kabul Edildi',
                        'reddedildi' => 'Reddedildi',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('interview_score')
                    ->label('Mülakat Puanı')
                    ->numeric()
                    ->getStateUsing(function ($record) {
                        $interview = \App\Models\Interviews::where('application_id', $record->id)
                            ->where('status', 'completed')
                            ->first();
                        
                        return $interview ? $interview->score : null;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('scholarship_amount')
                    ->label('Burs Miktarı')
                    ->formatStateUsing(fn ($state) => $state ? $state . ' ₺' : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scholarship_start_date')
                    ->label('Başlangıç Tarihi')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('scholarship_end_date')
                    ->label('Bitiş Tarihi')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->options(ScholarshipProgram::all()->pluck('name', 'id')),
                Tables\Filters\Filter::make('high_score')
                    ->label('Yüksek Puanlı Adaylar')
                    ->query(function (Builder $query) {
                        $query->whereHas('interviews', function ($query) {
                            $query->where('interview_score', '>=', 80);
                        });
                    })
                    ->toggle(),
                Tables\Filters\Filter::make('interview_date')
                    ->label('Mülakat Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                function (Builder $query, $date) {
                                    $query->whereHas('interviews', function ($query) use ($date) {
                                        $query->whereDate('scheduled_date', '>=', $date);
                                    });
                                },
                            )
                            ->when(
                                $data['until'] ?? null,
                                function (Builder $query, $date) {
                                    $query->whereHas('interviews', function ($query) use ($date) {
                                        $query->whereDate('scheduled_date', '<=', $date);
                                    });
                                },
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Değerlendir'),
                Tables\Actions\Action::make('approve')
                    ->label('Kabul Et')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('scholarship_amount')
                            ->label('Burs Miktarı (₺)')
                            ->options([
                                '500' => '500 ₺',
                                '750' => '750 ₺',
                                '1000' => '1000 ₺',
                                '1500' => '1500 ₺',
                                '2000' => '2000 ₺',
                                '2500' => '2500 ₺',
                                '3000' => '3000 ₺',
                                '3500' => '3500 ₺',
                                '4000' => '4000 ₺',
                                '5000' => '5000 ₺',
                            ])
                            ->required(),
                        Forms\Components\DateTimePicker::make('scholarship_start_date')
                            ->label('Burs Başlangıç Tarihi')
                            ->required()
                            ->minDate(now())
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->native(false),
                        Forms\Components\DateTimePicker::make('scholarship_end_date')
                            ->label('Burs Bitiş Tarihi')
                            ->minDate(now())
                            ->seconds(false)
                            ->displayFormat('d/m/Y H:i')
                            ->format('Y-m-d H:i')
                            ->native(false)
                            ->required()
                            ->minDate(function ($get) {
                                $startDate = $get('scholarship_start_date');
                                return $startDate ? Carbon::parse($startDate)->addMonths(1) : now()->addMonths(1);
                            }),
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Onay Notları')
                            ->maxLength(65535),
                    ])
                    ->action(function (Applications $record, array $data) {
                        try {
                            DB::beginTransaction();
                            
                            $record->status = 'kabul_edildi';
                            $record->scholarship_amount = $data['scholarship_amount'];
                            $record->scholarship_start_date = $data['scholarship_start_date'];
                            $record->scholarship_end_date = $data['scholarship_end_date'];
                            $record->approval_notes = $data['approval_notes'] ?? null;
                            $record->approval_date = Carbon::now();
                            $record->save();
                            
                            // Create a notification for the user about the scholarship approval
                            \App\Models\Notifications::create([
                                'notifiable_id' => $record->user_id,
                                'notifiable_type' => \App\Models\User::class,
                                'title' => 'Bursunuz Onaylandı',
                                'message' => 'Başvurunuz kabul edildi ve bursunuz onaylandı. Burs tutarı: ' . $data['scholarship_amount'] . ' ₺',
                                'type' => 'application_status',
                                'application_id' => $record->id,
                                'is_read' => false,
                            ]);
                            
                            // Kullanıcıya bildirim gönder
                            \Filament\Notifications\Notification::make()
                                ->title('Bildirim Gönderildi')
                                ->body('Kullanıcıya burs onay bildirimi gönderildi')
                                ->success()
                                ->send();
                            
                            // Kullanıcının rolünü düzenle (is_admin = false)
                            if ($record->user) {
                                $record->user->is_admin = false;
                                $record->user->save();
                            }
                            
                            // Varsa mevcut burs kaydını kontrol et
                            $existingScholarship = \App\Models\Scholarships::where('application_id', $record->id)->first();
                            
                            if ($existingScholarship) {
                                // Mevcut kaydı güncelle
                                $existingScholarship->update([
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
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Burs Kaydı Güncellendi')
                                    ->body('Öğrenci için burs kaydı başarıyla güncellendi')
                                    ->success()
                                    ->send();
                            } else {
                                // Yeni burs kaydı oluştur
                                $newScholarship = new \App\Models\Scholarships([
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
                                
                                $newScholarship->save();
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Burs Kaydı Oluşturuldu')
                                    ->body('Öğrenci için burs kaydı başarıyla oluşturuldu')
                                    ->success()
                                    ->send();
                            }
                            
                            DB::commit();
                            
                        } catch (\Exception $e) {
                            DB::rollBack();
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Hata')
                                ->body('Burs onaylama işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Burs Onayı')
                    ->modalDescription('Lütfen burs onayı için gerekli bilgileri giriniz.')
                    ->modalSubmitActionLabel('Onayla')
                    ->modalCancelActionLabel('İptal'),
                Tables\Actions\Action::make('reject')
                    ->label('Reddet')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Sebebi')
                            ->required(),
                    ])
                    ->action(function (Applications $record, array $data) {
                        $record->status = 'red_edildi';
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->rejected_by = auth()->id();
                        $record->rejected_at = now();
                        $record->save();
                        
                        // Create a notification for the user about the rejection
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
                    })
                    
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru reddedilsin mi?')
                    ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Reddet'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Toplu Kabul')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('scholarship_amount')
                                ->label('Burs Miktarı (₺)')
                                ->options([
                                    '500' => '500 ₺',
                                    '750' => '750 ₺',
                                    '1000' => '1000 ₺',
                                    '1500' => '1500 ₺',
                                    '2000' => '2000 ₺',
                                    '2500' => '2500 ₺',
                                    '3000' => '3000 ₺',
                                    '3500' => '3500 ₺',
                                    '4000' => '4000 ₺',
                                    '5000' => '5000 ₺',
                                ])
                                ->required(),
                            Forms\Components\DateTimePicker::make('scholarship_start_date')
                                ->label('Burs Başlangıç Tarihi')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\DateTimePicker::make('scholarship_end_date')
                                ->label('Burs Bitiş Tarihi')
                                ->required()
                                ->minDate(function ($get) {
                                    $startDate = $get('scholarship_start_date');
                                    return $startDate ? Carbon::parse($startDate)->addMonths(1) : now()->addMonths(1);
                                }),
                            Forms\Components\Textarea::make('approval_notes')
                                ->label('Onay Notları')
                                ->maxLength(65535),
                        ])
                        ->action(function (Collection $records, array $data) {
                            try {
                                \DB::beginTransaction();
                                
                                foreach ($records as $record) {
                                    $record->status = 'kabul_edildi';
                                    $record->scholarship_amount = $data['scholarship_amount'];
                                    $record->scholarship_start_date = $data['scholarship_start_date'];
                                    $record->scholarship_end_date = $data['scholarship_end_date'];
                                    $record->approval_notes = $data['approval_notes'] ?? null;
                                    $record->approval_date = Carbon::now();
                                    $record->save();
                                    
                                    // Create a notification for the user about the scholarship approval
                                    \App\Models\Notifications::create([
                                        'notifiable_id' => $record->user_id,
                                        'notifiable_type' => \App\Models\User::class,
                                        'title' => 'Bursunuz Onaylandı',
                                        'message' => 'Başvurunuz kabul edildi ve bursunuz onaylandı. Burs tutarı: ' . $data['scholarship_amount'] . ' ₺',
                                        'type' => 'application_status',
                                        'application_id' => $record->id,
                                        'is_read' => false,
                                    ]);
                                    
                                    // Kullanıcıya bildirim gönder
                                    \Filament\Notifications\Notification::make()
                                        ->title('Bildirim Gönderildi')
                                        ->body('Kullanıcıya burs onay bildirimi gönderildi')
                                        ->success()
                                        ->send();
                                    
                                    // Kullanıcının rolünü düzenle (is_admin = false)
                                    if ($record->user) {
                                        $record->user->is_admin = false;
                                        $record->user->save();
                                    }
                                    
                                    // Varsa mevcut burs kaydını kontrol et
                                    $existingScholarship = \App\Models\Scholarships::where('application_id', $record->id)->first();
                                    
                                    if ($existingScholarship) {
                                        // Mevcut kaydı güncelle
                                        $existingScholarship->update([
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
                                    } else {
                                        // Yeni burs kaydı oluştur
                                        $newScholarship = new \App\Models\Scholarships([
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
                                        
                                        $newScholarship->save();
                                    }
                                }
                                
                                \DB::commit();
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Burs Kayıtları Oluşturuldu')
                                    ->body('Seçilen başvurular için burs kayıtları başarıyla oluşturuldu')
                                    ->success()
                                    ->send();
                                
                            } catch (\Exception $e) {
                                \DB::rollBack();
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('Hata')
                                    ->body('Toplu burs onaylama işlemi sırasında bir hata oluştu: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular kabul edilsin mi?')
                        ->modalDescription('Seçilen başvuruları kabul etmek istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Kabul Et'),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Toplu Ret')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Ret Sebebi')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                $record->status = 'red_edildi';
                                $record->rejection_reason = $data['rejection_reason'];
                                $record->rejected_by = auth()->id();
                                $record->rejected_at = now();
                                $record->save();
                                
                                // Create a notification for the user about the rejection
                                \App\Models\Notifications::create([
                                    'notifiable_id' => $record->user_id,
                                    'notifiable_type' => \App\Models\User::class,
                                    'title' => 'Başvurunuz Reddedildi',
                                    'message' => 'Başvurunuz değerlendirilmiş ve reddedilmiştir. Red nedeni: ' . $data['rejection_reason'],
                                    'type' => 'application_status',
                                    'application_id' => $record->id,
                                    'is_read' => false,
                                ]);
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular reddedilsin mi?')
                        ->modalDescription('Seçilen başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Reddet'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Tümünü Sil')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular silinsin mi?')
                        ->modalDescription('Seçilen başvuruları silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Sil'),
                ])
                 
                ->label('Burs Onay İşlemleri'),
            ]);
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
            'index' => Pages\ListScholarshipApprovals::route('/'),
            'create' => Pages\CreateScholarshipApproval::route('/create'),
            'view' => Pages\ViewScholarshipApproval::route('/{record}'),
            'edit' => Pages\EditScholarshipApproval::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['approval_status'] === 'approve') {
            $data['status'] = 'kabul_edildi';
        } else {
            $data['status'] = 'red_edildi';
        }
        
        return $data;
    }
    
    public static function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['approval_status'] === 'approve') {
            $data['status'] = 'kabul_edildi';
        } else {
            $data['status'] = 'red_edildi';
        }
        
        return $data;
    }
}
