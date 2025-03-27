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

class ScholarshipApprovalResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    
    protected static ?string $navigationLabel = 'Burs Onayları';
    
    protected static ?int $navigationSort = 4;
    
    protected static ?string $navigationGroup = 'Başvuru İşlemleri';

    protected static ?string $title = 'Burs Onayları';

    protected static ?string $breadcrumb = 'Başvuru İşlemleri';

    protected static ?string $breadcrumbParent = 'Burs Onayları';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'mulakat_tamamlandi')
            ->where('is_interview_completed', true)
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
                        Forms\Components\TextInput::make('application_number')
                            ->label('Başvuru Numarası')
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
                                'kabul_edildi' => 'Kabul Edildi',
                                'reddedildi' => 'Reddedildi',
                            ])
                            ->disabled(),
                        Forms\Components\Toggle::make('is_document_verified')
                            ->label('Belgeler Doğrulandı')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_interview_scheduled')
                            ->label('Mülakat Planlandı')
                            ->disabled(),
                        Forms\Components\Toggle::make('is_interview_completed')
                            ->label('Mülakat Tamamlandı')
                            ->disabled(),
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
                            ->default(function ($get) {
                                $applicationId = $get('id');
                                $interview = \App\Models\Interviews::where('application_id', $applicationId)
                                    ->where('status', 'completed')
                                    ->first();
                                
                                return $interview ? $interview->score : null;
                            }),
                        Forms\Components\Textarea::make('interview_feedback')
                            ->label('Mülakat Geri Bildirimi')
                            ->disabled()
                            ->columnSpanFull()
                            ->default(function ($get) {
                                $applicationId = $get('id');
                                $interview = \App\Models\Interviews::where('application_id', $applicationId)
                                    ->where('status', 'completed')
                                    ->first();
                                
                                return $interview ? $interview->feedback : null;
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
                            ->required()
                            ->visible(fn ($get) => $get('approval_status') === 'approve'),
                        Forms\Components\DatePicker::make('scholarship_start_date')
                            ->label('Burs Başlangıç Tarihi')
                            ->required()
                            ->minDate(now())
                            ->visible(fn ($get) => $get('approval_status') === 'approve'),
                        Forms\Components\DatePicker::make('scholarship_end_date')
                            ->label('Burs Bitiş Tarihi')
                            ->required()
                            ->minDate(function ($get) {
                                $startDate = $get('scholarship_start_date');
                                return $startDate ? Carbon::parse($startDate)->addMonths(1) : now()->addMonths(1);
                            })
                            ->visible(fn ($get) => $get('approval_status') === 'approve'),
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
                Tables\Columns\TextColumn::make('application_number')
                    ->label('Başvuru No')
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
                        'kabul_edildi' => 'success',
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
                        'kabul_edildi' => 'Kabul Edildi',
                        'reddedildi' => 'Reddedildi',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('interviews.score')
                    ->label('Mülakat Puanı')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Average::make()
                            ->label('Ortalama'),
                        Tables\Columns\Summarizers\Range::make()
                            ->label('Aralık'),
                    ]),
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
                            $query->where('score', '>=', 80);
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
                        Forms\Components\DatePicker::make('scholarship_start_date')
                            ->label('Burs Başlangıç Tarihi')
                            ->required()
                            ->minDate(now()),
                        Forms\Components\DatePicker::make('scholarship_end_date')
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
                    ->action(function (Applications $record, array $data) {
                        $record->status = 'kabul_edildi';
                        $record->scholarship_amount = $data['scholarship_amount'];
                        $record->scholarship_start_date = $data['scholarship_start_date'];
                        $record->scholarship_end_date = $data['scholarship_end_date'];
                        $record->approval_notes = $data['approval_notes'] ?? null;
                        $record->approval_date = Carbon::now();
                        $record->save();
                        
                        // Öğrenci statüsüne yükselt
                        if ($record->user) {
                            $record->user->assignRole('student');
                            $record->user->save();
                        }
                    }),
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
                        $record->status = 'reddedildi';
                        $record->rejection_reason = $data['rejection_reason'];
                        $record->rejection_date = Carbon::now();
                        $record->save();
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
                            Forms\Components\DatePicker::make('scholarship_start_date')
                                ->label('Burs Başlangıç Tarihi')
                                ->required()
                                ->minDate(now()),
                            Forms\Components\DatePicker::make('scholarship_end_date')
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
                            foreach ($records as $record) {
                                $record->status = 'kabul_edildi';
                                $record->scholarship_amount = $data['scholarship_amount'];
                                $record->scholarship_start_date = $data['scholarship_start_date'];
                                $record->scholarship_end_date = $data['scholarship_end_date'];
                                $record->approval_notes = $data['approval_notes'] ?? null;
                                $record->approval_date = Carbon::now();
                                $record->save();
                                
                                // Öğrenci statüsüne yükselt
                                if ($record->user) {
                                    $record->user->assignRole('student');
                                    $record->user->save();
                                }
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
                                $record->status = 'reddedildi';
                                $record->rejection_reason = $data['rejection_reason'];
                                $record->rejection_date = Carbon::now();
                                $record->save();
                            }
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular reddedilsin mi?')
                        ->modalDescription('Seçilen başvuruları reddetmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')
                        ->modalSubmitActionLabel('Evet, Reddet'),
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
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
}
