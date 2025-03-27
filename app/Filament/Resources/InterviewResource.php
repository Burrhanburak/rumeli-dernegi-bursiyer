<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InterviewResource\Pages;
use App\Filament\Resources\InterviewResource\RelationManagers;
use App\Models\Interviews;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterviewResource extends Resource
{
    protected static ?string $model = Interviews::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Başvuru Süreci';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Mülakatlar';


    protected static ?string $modelLabel = 'Mülakat';

    protected static ?string $pluralModelLabel = 'Mülakatlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mülakat Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('application_id')
                            ->relationship('application', 'id')
                            ->label('Başvuru')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('interviewer_admin_id')
                            ->relationship('interviewer', 'name')
                            ->label('Mülakatçı')
                            ->required()
                            ->searchable(),
                        Forms\Components\DateTimePicker::make('interview_date')
                            ->label('Planlanan Tarih')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Konum')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->label('Online Mülakat mı?')
                            ->default(false),
                        Forms\Components\TextInput::make('meeting_link')
                            ->label('Toplantı Linki')
                            ->url()
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('is_online')),

                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            
                            ->options([
                                'scheduled' => 'Planlandı',
                                'completed' => 'Tamamlandı',
                                'canceled' => 'İptal Edildi',
                                'rescheduled' => 'Yeniden Planlandı',
                                'no_show' => 'Katılım Olmadı',
                            ])
                            ->required()
                            ->default('scheduled')
                            ->searchable()
                            ->preload()
                            ->placeholder('Durum seçin')
                            ->selectablePlaceholder(false),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('interview_questions')
                            ->label('Mülakat Soruları')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('interview_answers')
                            ->label('Mülakat Cevapları')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('interview_score')
                            ->label('Puan')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                        Forms\Components\Select::make('interview_result')
                            ->label('Sonuç')
                            ->options([
                                'passed' => 'Başarılı',
                                'failed' => 'Başarısız',
                                'pending' => 'Beklemede',
                            ])
                            ->searchable()
                            ->preload()
                            ->placeholder('Sonuç seçin')
                            ->selectablePlaceholder(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Mülakat bulunamadı')
            ->emptyStateDescription('Yeni bir mülakat eklemek için "Yeni Mülakat" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Mülakat')
            ])
            ->searchable()
            ->searchPlaceholder('Ara...')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application.user.name')
                    ->label('Başvuru Sahibi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->label('Mülakatçı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interview_date')
                    ->label('Planlanan Tarih')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_online')
                    ->label('Online')
                    ->boolean(),
                Tables\Columns\IconColumn::make('status_icon')
                    ->label('')
                    ->options([
                        'heroicon-o-clock' => fn ($record): bool => $record->status === 'scheduled',
                        'heroicon-o-check-circle' => fn ($record): bool => $record->status === 'completed',
                        'heroicon-o-x-circle' => fn ($record): bool => $record->status === 'canceled',
                        'heroicon-o-arrow-path' => fn ($record): bool => $record->status === 'rescheduled',
                        'heroicon-o-x-mark' => fn ($record): bool => $record->status === 'no_show',
                    ])
                    ->colors([
                        'info' => fn ($record): bool => $record->status === 'scheduled',
                        'success' => fn ($record): bool => $record->status === 'completed',
                        'danger' => fn ($record): bool => $record->status === 'canceled',
                        'warning' => fn ($record): bool => $record->status === 'rescheduled',
                        'gray' => fn ($record): bool => $record->status === 'no_show',
                    ]),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        'rescheduled' => 'warning',
                        'no_show' => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('interview_score')
                    ->label('Puan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('result_icon')
                    ->label('')
                    ->options([
                        'heroicon-o-check-badge' => fn ($record): bool => $record->interview_result === 'passed',
                        'heroicon-o-x-circle' => fn ($record): bool => $record->interview_result === 'failed',
                        'heroicon-o-clock' => fn ($record): bool => $record->interview_result === 'pending',
                    ])
                    ->colors([
                        'success' => fn ($record): bool => $record->interview_result === 'passed',
                        'danger' => fn ($record): bool => $record->interview_result === 'failed',
                        'warning' => fn ($record): bool => $record->interview_result === 'pending',
                        'gray' => fn ($record): bool => $record->interview_result === null,
                    ]),
                Tables\Columns\TextColumn::make('interview_result')
                    ->label('Sonuç')
                    ->badge()
                    ->color(fn (string $state): string => match ($state ?? '') {
                        'passed' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state = null): string => match ($state ?? '') {
                        'passed' => 'Başarılı',
                        'failed' => 'Başarısız',
                        'pending' => 'Beklemede',
                        default => 'Belirtilmemiş',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                    ]),
                Tables\Filters\Filter::make('interview_date')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('interview_date', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('interview_date', '<=', $date),
                            );
                    }),
                Tables\Filters\SelectFilter::make('interview_result')
                    ->label('Sonuç')
                    ->options([
                        'passed' => 'Başarılı',
                        'failed' => 'Başarısız',
                        'pending' => 'Beklemede',
                    ])
                    ->indicator('Sonuç')
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListInterviews::route('/'),
            'create' => Pages\CreateInterview::route('/create'),
            'view' => Pages\ViewInterview::route('/{record}'),
            'edit' => Pages\EditInterview::route('/{record}/edit'),
        ];
    }
}
