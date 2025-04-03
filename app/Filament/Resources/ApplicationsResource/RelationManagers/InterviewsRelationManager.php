<?php

namespace App\Filament\Resources\ApplicationsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InterviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'interviews';

    protected static ?string $navigationLabel = 'Mülakatlar';
    

    protected static ?string $title = 'Mülakatlar';

    protected static ?string $breadcrumb = 'Mülakatlar';

    protected static ?string $breadcrumbParent = 'Mülakatlar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('interviewer_id')
                    ->relationship('interviewer', 'name')
                    ->label('Mülakatçı')
                    ->required()
                    ->searchable(),
                Forms\Components\DateTimePicker::make('scheduled_date')
                    ->label('Planlanan Tarih')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label('Konum')
                    ->maxLength(255),
                Forms\Components\TextInput::make('meeting_link')
                    ->label('Toplantı Linki')
                    ->url()
                    ->maxLength(255),
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
                    ->default('scheduled'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535),
                Forms\Components\Textarea::make('feedback')
                    ->label('Geri Bildirim')
                    ->maxLength(65535),
                Forms\Components\TextInput::make('score')
                    ->label('Puan')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->label('Mülakatçı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scheduled_date')
                    ->label('Planlanan Tarih')
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
                Tables\Columns\TextColumn::make('score')
                    ->label('Puan')
                    ->numeric()
                    ->sortable(),
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Mülakat Ekle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }
} 
