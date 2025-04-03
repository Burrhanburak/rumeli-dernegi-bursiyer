<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\RejectedApplicationsResource\Pages;
use App\Filament\Resources\User\RejectedApplicationsResource\RelationManagers;
use App\Models\Applications;
use App\Models\ScholarshipProgram;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class RejectedApplicationsResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    
    protected static ?string $navigationLabel = 'Reddedilen Başvurular';
    
    protected static ?int $navigationSort = 3;
    
    protected static ?string $navigationGroup = 'Başvuru Yönetimi';

    protected static ?string $title = 'Reddedilen Başvurular';

    protected static ?string $breadcrumb = 'Reddedilen Başvurular';

    protected static ?string $breadcrumbParent = 'Başvuru Yönetimi';
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'reddedildi')
            ->orWhere('status', 'rejected')
            ->orWhere('status', 'red_edildi')
            ->orderBy('updated_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->label('Başvuran')
                            ->disabled(),
                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->required()
                            ->label('Program')
                            ->disabled(),
                        Forms\Components\TextInput::make('application_id')
                            ->label('Başvuru ID')
                            ->disabled(),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'reddedildi' => 'Reddedildi',
                                'rejected' => 'Reddedildi',
                                'red_edildi' => 'Reddedildi',
                            ])
                            ->label('Durum')
                            ->disabled(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Ret Nedeni')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('rejected_at')
                            ->label('Ret Tarihi')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull()
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Reddedilmiş başvuru bulunamadı')
            ->emptyStateDescription('Başvuru değerlendirmesinde reddedilmiş başvurular burada görüntülenecektir.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru ID')
                    ->searchable(),
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
                    ->color('danger')
                    ->formatStateUsing(fn (string $state): string => 'Reddedildi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Ret Nedeni')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    }),
                Tables\Columns\TextColumn::make('rejected_at')
                    ->label('Ret Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->date()
                    ->sortable()
                    ->label('Başvuru Tarihi'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Son Güncelleme'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program')
                    ->relationship('program', 'name')
                    ->label('Program'),
                Tables\Filters\Filter::make('rejected_at')
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
                                fn (Builder $query, $date): Builder => $query->where('rejected_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('rejected_at', '<=', $date),
                            );
                    })
                    ->label('Ret Tarihi'),
                Tables\Filters\Filter::make('application_date')
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
                                fn (Builder $query, $date): Builder => $query->where('application_date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->where('application_date', '<=', $date),
                            );
                    })
                    ->label('Başvuru Tarihi'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\Action::make('reactivate_application')
                    ->label('Başvuruyu Yeniden Aktifleştir')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (Applications $record) {
                        $record->status = 'awaiting_evaluation';
                        $record->rejection_reason = null;
                        $record->rejected_at = null;
                        $record->save();
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Başvuru Aktifleştirildi')
                            ->body('Başvuru yeniden değerlendirme havuzuna alındı.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Başvuru yeniden aktifleştirilsin mi?')
                    ->modalDescription('Bu başvuruyu yeniden değerlendirmeye almak istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Aktifleştir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_reactivate')
                        ->label('Toplu Aktifleştir')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            foreach ($records as $record) {
                                $record->status = 'awaiting_evaluation';
                                $record->rejection_reason = null;
                                $record->rejected_at = null;
                                $record->save();
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Başvurular Aktifleştirildi')
                                ->body('Seçilen başvurular yeniden değerlendirme havuzuna alındı.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Başvurular yeniden aktifleştirilsin mi?')
                        ->modalDescription('Seçilen başvuruları yeniden değerlendirmeye almak istediğinizden emin misiniz?')
                        ->modalSubmitActionLabel('Evet, Aktifleştir'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
         
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRejectedApplications::route('/'),
            'view' => Pages\ViewRejectedApplications::route('/{record}'),
        ];
    }
} 