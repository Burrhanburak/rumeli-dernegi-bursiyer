<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ApplicationPreEvaluationResource\Pages;
use App\Models\Applications;
use App\Models\ScholarshipProgram;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class ApplicationPreEvaluationResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationLabel = 'Ön Değerlendirme';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationGroup = 'Başvuru Yönetimi';

    protected static ?string $title = 'Başvuru Ön Değerlendirmesi';

    protected static ?string $breadcrumb = 'Ön Değerlendirme';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereIn('status', ['awaiting_evaluation', 'belgeler_yuklendi', 'dogrulama_bekliyor', 'beklemede'])
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
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('application_number')
                            ->label('Başvuru Numarası')
                            ->placeholder("Örn: BV-00001")
                            ->helperText("Boş bırakılırsa otomatik oluşturulacaktır"),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->default(now()),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'awaiting_evaluation' => 'Değerlendirme Bekliyor',
                                'beklemede' => 'Beklemede',
                                'belgeler_yuklendi' => 'Belgeler Yüklendi',
                                'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                                'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                                'burs_havuzu' => 'Başvuru Havuzu',
                                'on_kabul' => 'Ön Kabul',
                                'accepted' => 'Kabul Edildi',
                                'red_edildi' => 'Reddedildi',
                                'scholarship_pool' => 'Burs Havuzu',
                                'pre_approved' => 'Ön Kabul',
                                'rejected' => 'Reddedildi',
                                'awaiting_documents' => 'Evrak Bekleniyor',
                                'documents_under_review' => 'Evrak İncelemede',
                                'interview_pool' => 'Mülakat Havuzu',
                                'interview_scheduled' => 'Mülakat Planlandı',
                                'interview_completed' => 'Mülakat Tamamlandı',
                                'final_acceptance' => 'Kesin Kabul'
                            ]),
                    ])->columns(2),
                
                Forms\Components\Section::make('Değerlendirme')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Sebebi')
                            ->maxLength(65535)
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('status') === 'red_edildi'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Bekleyen başvuru bulunamadı')
            ->emptyStateDescription('Tüm başvurular değerlendirilmiş.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_number')
                    ->label('Başvuru No')
                    ->getStateUsing(fn (Applications $record): string => $record->application_number ?: "BV-" . str_pad($record->id, 5, '0', STR_PAD_LEFT))
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
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'awaiting_evaluation' => 'Değerlendirme Bekliyor',
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                        'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                        'burs_havuzu' => 'Başvuru Havuzu',
                        'scholarship_pool' => 'Burs Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'pre_approved' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        'rejected' => 'Reddedildi',
                        'awaiting_documents' => 'Evrak Bekleniyor',
                        'documents_under_review' => 'Evrak İncelemede',
                        'interview_pool' => 'Mülakat Havuzu',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_completed' => 'Mülakat Tamamlandı',
                        'accepted' => 'Kabul Edildi',
                        'final_acceptance' => 'Kesin Kabul',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'awaiting_evaluation' => 'warning',
                        'dogrulama_bekliyor' => 'warning',
                        'beklemede' => 'gray', 
                        'belgeler_yuklendi' => 'info',
                        'dogrulama_tamamlandi' => 'success',
                        'burs_havuzu' => 'primary',
                        'scholarship_pool' => 'primary',
                        'awaiting_documents' => 'primary',
                        'on_kabul' => 'success',
                        'pre_approved' => 'success',
                        'red_edildi' => 'danger',
                        'rejected' => 'danger',
                        'documents_under_review' => 'secondary',
                        'interview_pool' => 'secondary',
                        'interview_scheduled' => 'secondary',
                        'interview_completed' => 'success',
                        'accepted' => 'success',
                        'final_acceptance' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'awaiting_evaluation' => 'Değerlendirme Bekliyor',
                        'beklemede' => 'Beklemede',
                        'belgeler_yuklendi' => 'Belgeler Yüklendi',
                        'dogrulama_bekliyor' => 'Doğrulama Bekliyor',
                        'dogrulama_tamamlandi' => 'Doğrulama Tamamlandı',
                        'burs_havuzu' => 'Başvuru Havuzu',
                        'scholarship_pool' => 'Burs Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'pre_approved' => 'Ön Kabul',
                    ]),
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->relationship('program', 'name'),
                Tables\Filters\Filter::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Başlangıç'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Bitiş'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->where('application_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\Action::make('verify_documents')
                    ->label('Belgeleri Doğrula')
                    ->icon('heroicon-o-document-check')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'dogrulama_tamamlandi';
                        $record->are_documents_approved = true;
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Belgeler doğrulansın mı?')
                    ->modalDescription('Bu başvurunun belgelerini doğrulamak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Doğrula')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_bekliyor'),
                Tables\Actions\Action::make('move_to_pool')
                    ->label('Havuza Taşı')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->action(function (Applications $record) {
                        $record->status = 'burs_havuzu';
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru havuza taşınsın mı?')
                    ->modalDescription('Bu başvuruyu başvuru havuzuna taşımak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Havuza Taşı')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved),
                Tables\Actions\Action::make('pre_approve')
                    ->label('Ön Kabul')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function (Applications $record) {
                        $record->status = 'on_kabul';
                        $record->pre_approved_by = Auth::id();
                        $record->pre_approved_at = now();
                        $record->save();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru ön kabul edilsin mi?')
                    ->modalDescription('Bu başvuruyu ön kabul etmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Ön Kabul Et')
                    ->visible(fn (Applications $record): bool => $record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved),
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Applications $record) {
                        // Belgelerin doğrulanıp doğrulanmadığını kontrol et
                        if (!$record->are_documents_approved) {
                            // Belgeler doğrulanmamış, önce doğrula
                            $record->are_documents_approved = true;
                        }
                        
                        $record->status = 'accepted';
                        $record->reviewed_by = auth()->id();
                        $record->reviewed_at = now();
                        $record->save();
                        
                        // Create a notification for the user about the approval
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => \App\Models\User::class,
                            'title' => 'Başvurunuz Kabul Edildi',
                            'message' => 'Başvurunuz değerlendirilmiş ve kabul edilmiştir.',
                            'type' => 'application_status',
                            'application_id' => $record->id,
                            'is_read' => false,
                        ]);
                        
                        // Başarılı bir şekilde kaydedildiğinde kullanıcıya bir bildirim göster
                        \Filament\Notifications\Notification::make()
                            ->title('Başvuru Onaylandı')
                            ->body('Başvuru başarıyla onaylandı ve Kabul Edilen Başvurular listesine taşındı.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru onaylansın mı?')
                    ->modalDescription('Bu başvuruyu onaylamak istediğinizden emin misiniz? Kabul Edilen Başvurular sayfasına taşınacaktır.')
                    ->modalSubmitActionLabel('Evet, Onayla'),
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
                        $record->rejected_by = Auth::id();
                        $record->rejected_at = now();
                        $record->rejection_reason = $data['rejection_reason'];
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
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru reddedilsin mi?')
                    ->modalDescription('Bu başvuruyu reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                    ->modalSubmitActionLabel('Evet, Reddet'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_verify_documents')
                        ->label('Belgeleri Doğrula')
                        ->icon('heroicon-o-document-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_bekliyor') {
                                    $record->status = 'dogrulama_tamamlandi';
                                    $record->are_documents_approved = true;
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Belgeler doğrulansın mı?')
                        ->modalDescription('Seçili başvuruların belgelerini doğrulamak istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Doğrula'),
                    Tables\Actions\BulkAction::make('bulk_move_to_pool')
                        ->label('Havuza Taşı')
                        ->icon('heroicon-o-arrow-right-circle')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved) {
                                    $record->status = 'burs_havuzu';
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular havuza taşınsın mı?')
                        ->modalDescription('Seçili başvuruları başvuru havuzuna taşımak istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Havuza Taşı'),
                    Tables\Actions\BulkAction::make('bulk_pre_approve')
                        ->label('Ön Kabul')
                        ->icon('heroicon-o-check')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'dogrulama_tamamlandi' && $record->are_documents_approved) {
                                    $record->status = 'on_kabul';
                                    $record->pre_approved_by = Auth::id();
                                    $record->pre_approved_at = now();
                                    $record->save();
                                }
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular ön kabul edilsin mi?')
                        ->modalDescription('Seçili başvuruları ön kabul etmek istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Ön Kabul Et'),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reddet')
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
                                $record->rejected_by = Auth::id();
                                $record->rejected_at = now();
                                $record->rejection_reason = $data['rejection_reason'];
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
                        ->modalDescription('Seçili başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Reddet'),
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Toplu Onayla')
                        ->icon('heroicon-o-check-circle')
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                // Belgelerin doğrulanıp doğrulanmadığını kontrol et
                                if (!$record->are_documents_approved) {
                                    // Belgeler doğrulanmamış, önce doğrula
                                    $record->are_documents_approved = true;
                                }
                                
                                $record->status = 'accepted';
                                $record->reviewed_by = auth()->id();
                                $record->reviewed_at = now();
                                $record->save();
                                
                                // Create a notification for the user about the approval
                                \App\Models\Notifications::create([
                                    'notifiable_id' => $record->user_id,
                                    'notifiable_type' => \App\Models\User::class,
                                    'title' => 'Başvurunuz Kabul Edildi',
                                    'message' => 'Başvurunuz değerlendirilmiş ve kabul edilmiştir.',
                                    'type' => 'application_status',
                                    'application_id' => $record->id,
                                    'is_read' => false,
                                ]);
                            }
                            
                            // Başarılı bir şekilde kaydedildiğinde kullanıcıya bir bildirim göster
                            \Filament\Notifications\Notification::make()
                                ->title('Başvurular Onaylandı')
                                ->body('Seçili başvurular başarıyla onaylandı ve Kabul Edilen Başvurular listesine taşındı.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular onaylansın mı?')
                        ->modalDescription('Seçili başvuruları onaylamak istediğinizden emin misiniz? Kabul Edilen Başvurular sayfasına taşınacaktır.')
                        ->modalSubmitActionLabel('Evet, Onayla'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ])

                ->label('Başvuru İşlemleri'),
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
            'index' => Pages\ListApplicationPreEvaluations::route('/'),
            'view' => Pages\ViewApplicationPreEvaluation::route('/{record}'),
            'edit' => Pages\EditApplicationPreEvaluation::route('/{record}/edit'),
        ];
    }
} 