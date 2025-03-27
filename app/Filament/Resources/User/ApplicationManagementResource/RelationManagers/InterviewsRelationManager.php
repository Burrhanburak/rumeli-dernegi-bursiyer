<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InterviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'interviews';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('interview_date')
                    ->required()
                    ->label('Mülakat Tarihi'),
                Forms\Components\Select::make('interviewer_id')
                    ->relationship('interviewer', 'name')
                    ->required()
                    ->label('Mülakatçı')
                    ->default(Auth::id()),
                Forms\Components\Select::make('location')
                    ->options([
                        'online' => 'Online',
                        'office' => 'Ofis',
                        'other' => 'Diğer',
                    ])
                    ->required()
                    ->label('Lokasyon'),
                Forms\Components\TextInput::make('meeting_link')
                    ->label('Toplantı Linki')
                    ->url()
                    ->maxLength(255)
                    ->visible(fn (Forms\Get $get) => $get('location') === 'online'),
                Forms\Components\TextInput::make('address')
                    ->label('Adres')
                    ->maxLength(255)
                    ->visible(fn (Forms\Get $get) => $get('location') === 'other'),
                Forms\Components\Select::make('status')
                    ->options([
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                    ])
                    ->required()
                    ->label('Durum')
                    ->default('scheduled'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('interview_summary')
                    ->label('Mülakat Özeti')
                    ->maxLength(5000)
                    ->columnSpanFull()
                    ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                Forms\Components\Select::make('recommendation')
                    ->options([
                        'highly_recommended' => 'Kesinlikle Tavsiye Ediyorum',
                        'recommended' => 'Tavsiye Ediyorum',
                        'neutral' => 'Kararsızım',
                        'not_recommended' => 'Tavsiye Etmiyorum',
                        'strongly_not_recommended' => 'Kesinlikle Tavsiye Etmiyorum',
                    ])
                    ->label('Tavsiye')
                    ->visible(fn (Forms\Get $get) => $get('status') === 'completed'),
                Forms\Components\Textarea::make('cancellation_reason')
                    ->label('İptal Sebebi')
                    ->maxLength(1000)
                    ->visible(fn (Forms\Get $get) => $get('status') === 'cancelled')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('interview_date')
            ->columns([
                Tables\Columns\TextColumn::make('interview_date')
                    ->label('Mülakat Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->label('Mülakatçı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasyon')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'online' => 'Online',
                        'office' => 'Ofis',
                        'other' => 'Diğer',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'scheduled',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'warning' => 'rescheduled',
                    ]),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Tavsiye')
                    ->formatStateUsing(fn (string $state = null): string => match ($state) {
                        'highly_recommended' => 'Kesinlikle Tavsiye Ediyorum',
                        'recommended' => 'Tavsiye Ediyorum',
                        'neutral' => 'Kararsızım',
                        'not_recommended' => 'Tavsiye Etmiyorum',
                        'strongly_not_recommended' => 'Kesinlikle Tavsiye Etmiyorum',
                        null => '-',
                        default => $state,
                    })
                    ->colors([
                        'success' => ['highly_recommended', 'recommended'],
                        'gray' => 'neutral',
                        'danger' => ['not_recommended', 'strongly_not_recommended'],
                    ]),
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
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'cancelled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                    ]),
                Tables\Filters\SelectFilter::make('location')
                    ->label('Lokasyon')
                    ->options([
                        'online' => 'Online',
                        'office' => 'Ofis',
                        'other' => 'Diğer',
                    ]),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Yaklaşan Mülakatlar')
                    ->query(fn (Builder $query): Builder => $query->where('interview_date', '>=', now()))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('complete')
                    ->label('Tamamlandı')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->form([
                        Forms\Components\RichEditor::make('interview_summary')
                            ->label('Mülakat Özeti')
                            ->required()
                            ->maxLength(5000),
                        Forms\Components\Select::make('recommendation')
                            ->options([
                                'highly_recommended' => 'Kesinlikle Tavsiye Ediyorum',
                                'recommended' => 'Tavsiye Ediyorum',
                                'neutral' => 'Kararsızım',
                                'not_recommended' => 'Tavsiye Etmiyorum',
                                'strongly_not_recommended' => 'Kesinlikle Tavsiye Etmiyorum',
                            ])
                            ->label('Tavsiye')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->status = 'completed';
                        $record->interview_summary = $data['interview_summary'];
                        $record->recommendation = $data['recommendation'];
                        $record->completed_at = now();
                        $record->completed_by = Auth::id();
                        $record->save();
                    })
                    ->visible(fn ($record) => $record->status === 'scheduled'),
                Tables\Actions\Action::make('cancel')
                    ->label('İptal Et')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('cancellation_reason')
                            ->label('İptal Sebebi')
                            ->required()
                            ->maxLength(1000),
                    ])
                    ->action(function ($record, array $data) {
                        $record->status = 'cancelled';
                        $record->cancellation_reason = $data['cancellation_reason'];
                        $record->cancelled_at = now();
                        $record->cancelled_by = Auth::id();
                        $record->save();
                    })
                    ->visible(fn ($record) => $record->status === 'scheduled'),
                Tables\Actions\Action::make('reschedule')
                    ->label('Yeniden Planla')
                    ->icon('heroicon-o-calendar')
                    ->color('warning')
                    ->form([
                        Forms\Components\DateTimePicker::make('interview_date')
                            ->label('Yeni Mülakat Tarihi')
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $record->status = 'rescheduled';
                        $record->rescheduled_at = now();
                        $record->rescheduled_by = Auth::id();
                        $record->old_interview_date = $record->interview_date;
                        $record->interview_date = $data['interview_date'];
                        $record->save();
                    })
                    ->visible(fn ($record) => $record->status === 'scheduled'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('cancel_selected')
                        ->label('Seçilenleri İptal Et')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('cancellation_reason')
                                ->label('İptal Sebebi')
                                ->required()
                                ->maxLength(1000),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status === 'scheduled') {
                                    $record->status = 'cancelled';
                                    $record->cancellation_reason = $data['cancellation_reason'];
                                    $record->cancelled_at = now();
                                    $record->cancelled_by = Auth::id();
                                    $record->save();
                                }
                            }
                        }),
                ]),
            ]);
    }
} 