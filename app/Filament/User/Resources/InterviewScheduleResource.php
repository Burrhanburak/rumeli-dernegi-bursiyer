<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\InterviewScheduleResource\Pages;
use App\Filament\User\Resources\InterviewScheduleResource\RelationManagers;
use App\Models\Interviews;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InterviewScheduleResource extends Resource
{
    protected static ?string $model = Interviews::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationLabel = 'Mülakat Programım';
    
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Burs İşlemleri';

    protected static ?string $title = 'Mülakat Programım';

    protected static ?string $breadcrumb = 'Mülakat Programım';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('application', function (Builder $query) {
                $query->where('user_id', Auth::id());
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('interview_date')
                    ->label('Mülakat Tarihi')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->label('Konum'),
                Forms\Components\TextInput::make('meeting_link')
                    ->label('Toplantı Linki')
                    ->url(fn (Interviews $record): ?string => $record->meeting_link),
                Forms\Components\Toggle::make('is_online')
                    ->label('Online Mülakat')
                    ->hidden(fn (Interviews $record): bool => $record->is_online),
            
                    Forms\Components\TextInput::make('status')
                    ->label('Durum')
                    ->default('scheduled')
                    ->disabled()
                    ->formatStateUsing(function ($state) {
                        $statusLabels = [
                            'scheduled' => 'Planlandı',
                            'completed' => 'Tamamlandı',
                            'canceled' => 'İptal Edildi',
                            'rescheduled' => 'Yeniden Planlandı',
                            'no_show' => 'Katılım Olmadı',
                            'confirmed' => 'Onaylandı',
                        ];
                        
                        return $statusLabels[$state] ?? $state;
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Mülakat planınız Yok')
        ->emptyStateDescription('Herhangi bir mulakat planınınız bulunamadı.')
            ->columns([
                Tables\Columns\TextColumn::make('interviewer.name')
                    ->label('Mülakatçı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('interview_date')
                ->label('Mülakat Tarihi')
                ->formatStateUsing(function ($state) {
                    $aylar = [
                        'January' => 'Ocak',
                        'February' => 'Şubat',
                        'March' => 'Mart',
                        'April' => 'Nisan',
                        'May' => 'Mayıs',
                        'June' => 'Haziran',
                        'July' => 'Temmuz',
                        'August' => 'Ağustos',
                        'September' => 'Eylül',
                        'October' => 'Ekim',
                        'November' => 'Kasım',
                        'December' => 'Aralık',
                    ];
                    $ay = $state->format('F'); // İngilizce ay adı (örneğin, "April")
                    $turkceAy = $aylar[$ay]; // Türkçe karşılığı (örneğin, "Nisan")
                    return $state->format('d') . ' ' . $turkceAy . ' ' . $state->format('Y H.i');
                })
                ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('Konum')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meeting_link')
                    ->label('Toplantı Linki')
                    ->toggleable(isToggledHiddenByDefault: true)
                    // ->url(fn (Interviews $record): ?string => $record->meeting_link)
                    // ->openUrlInNewTab()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled' => 'info',
                        'completed' => 'success',
                        'canceled' => 'danger',
                        'rescheduled' => 'warning',
                        'no_show' => 'gray',
                        'confirmed' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Planlandı',
                        'completed' => 'Tamamlandı',
                        'canceled' => 'İptal Edildi',
                        'rescheduled' => 'Yeniden Planlandı',
                        'no_show' => 'Katılım Olmadı',
                        'confirmed' => 'Onaylandı',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('application.program.name')
                    ->label('Burs Programı')
                    ->searchable(),
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
                        'confirmed' => 'Onaylandı',
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\Action::make('confirm')
                    ->label('Katılımı Onayla')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->action(function (Interviews $record) {
                        // Mülakat katılımı onaylama mantığı
                        $record->status = 'confirmed';
                        $record->save();
                    })
                    ->visible(fn (Interviews $record): bool => $record->status === 'scheduled'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('interview_date', 'asc');
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
            'index' => Pages\ListInterviewSchedules::route('/'),
            'view' => Pages\ViewInterviewSchedule::route('/{record}'),
        ];
    }
}
