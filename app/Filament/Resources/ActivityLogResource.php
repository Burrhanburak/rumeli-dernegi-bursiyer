<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationLabel = 'Aktivite Logları';
    
    protected static ?int $navigationSort = 7;
    
    protected static ?string $navigationGroup = 'Sistem';

    protected static ?string $modelLabel = 'Aktivite Logu';

    protected static ?string $pluralModelLabel = 'Aktivite Logları';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('log_name')
                    ->label('Log Adı'),
                Forms\Components\TextInput::make('description')
                    ->label('Açıklama'),
                Forms\Components\KeyValue::make('properties')
                    ->label('Özellikler'),
                Forms\Components\TextInput::make('subject_type')
                    ->label('Konu Türü'),
                Forms\Components\TextInput::make('subject_id')
                    ->label('Konu ID'),
                Forms\Components\TextInput::make('causer_type')
                    ->label('Neden Olan Türü'),
                Forms\Components\TextInput::make('causer_id')
                    ->label('Neden Olan ID'),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Oluşturulma Tarihi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Log Adı')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'auth' => 'success',
                        'application' => 'primary',
                        'default' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('İşlem')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Kullanıcı')
                    ->description(fn (Activity $record): string => $record->causer ? $record->causer->email : '')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Konu Türü')
                    ->formatStateUsing(fn (string $state): string => 
                        str_replace('App\\Models\\', '', $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('properties')
                    ->label('IP Adresi')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state) && isset($state['ip'])) {
                            return $state['ip'];
                        }
                        return '';
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tarih')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Log Türü')
                    ->options([
                        'auth' => 'Giriş/Çıkış',
                        'application' => 'Başvuru',
                        'default' => 'Genel',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->label('Tarih')
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListActivityLogs::route('/'),
            'view' => Pages\ViewActivityLog::route('/{record}'),
        ];
    }
} 