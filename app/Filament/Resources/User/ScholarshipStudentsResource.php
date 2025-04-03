<?php

namespace App\Filament\Resources\User;

use App\Filament\Resources\User\ScholarshipStudentsResource\Pages;
use App\Models\Scholarships;
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

class ScholarshipStudentsResource extends Resource
{
    protected static ?string $model = Scholarships::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Burs Alan Öğrenciler';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $navigationGroup = 'Burs Yönetimi';

    protected static ?string $title = 'Burs Alan Öğrenciler';

    protected static ?string $breadcrumb = 'Burs Yönetimi';

    protected static ?string $breadcrumbParent = 'Burs Alan Öğrenciler';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->whereIn('status', ['active', 'suspended', 'completed', 'terminated'])
            ->with(['user', 'program', 'application'])
            ->orderBy('created_at', 'desc');
            
        // SQL sorgusu debug için
        \Illuminate\Support\Facades\Log::info('ScholarshipStudents Query: ' . $query->toSql());
        \Illuminate\Support\Facades\Log::info('ScholarshipStudents Bindings: ' . json_encode($query->getBindings()));
        
        // Kayıt sayısını kontrol et
        $recordCount = $query->count();
        \Illuminate\Support\Facades\Log::info('ScholarshipStudents Count: ' . $recordCount);
        
        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Öğrenci Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Öğrenci')
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
                    ])->columns(2),
                
                Forms\Components\Section::make('Burs Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Burs Adı')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Burs Miktarı (₺)')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Başlangıç Tarihi')
                            ->disabled(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Bitiş Tarihi')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Askıya Alındı',
                                'completed' => 'Tamamlandı',
                                'terminated' => 'Sonlandırıldı',
                            ])
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burs alan öğrenci bulunamadı')
            ->emptyStateDescription('Burs alan öğrenciler buradan takip edilebilir. Burs onaylandığında öğrenciler otomatik olarak burada listelenir.')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Öğrenci')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Burs Miktarı')
                    ->formatStateUsing(fn ($state) => $state . ' ₺')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Bitiş Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'suspended' => 'warning',
                        'completed' => 'info',
                        'terminated' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->options(ScholarshipProgram::all()->pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->label('Tarih Aralığı')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Başlangıç Tarihi'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Bitiş Tarihi'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Silme Sebebi')
                            ->required(),
                    ])
                    ->action(function (Scholarships $record) {
                        $record->delete();
                    }),
                Tables\Actions\Action::make('suspend')
                    ->label('Askıya Al')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Askıya Alma Sebebi')
                            ->required(),
                    ])
                    ->action(function (Scholarships $record, array $data) {
                        $record->status = 'suspended';
                        $record->status_reason = $data['reason'];
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        // Bildirim gönder
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => 'App\Models\User',
                            'title' => 'Bursunuz Askıya Alındı',
                            'message' => 'Bursunuz askıya alındı. Sebep: ' . $data['reason'],
                            'type' => 'b',
                            'application_id' => $record->application_id,
                            'is_read' => false,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Burs Askıya Alındı')
                            ->body('Öğrencinin bursu askıya alındı ve bildirim gönderildi')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Scholarships $record): bool => $record->status === 'active'),
                Tables\Actions\Action::make('terminate')
                    ->label('Sonlandır')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Sonlandırma Sebebi')
                            ->required(),
                    ])
                    ->action(function (Scholarships $record, array $data) {
                        $record->status = 'terminated';
                        $record->status_reason = $data['reason'];
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        // Bildirim gönder
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => 'App\Models\User',
                            'title' => 'Bursunuz Sonlandırıldı',
                            'message' => 'Bursunuz sonlandırıldı. Sebep: ' . $data['reason'],
                            'type' => 'b',
                            'application_id' => $record->application_id,
                            'is_read' => false,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Burs Sonlandırıldı')
                            ->body('Öğrencinin bursu sonlandırıldı ve bildirim gönderildi')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Scholarships $record): bool => in_array($record->status, ['active', 'suspended'])),
                Tables\Actions\Action::make('activate')
                    ->label('Aktifleştir')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Scholarships $record) {
                        $record->status = 'active';
                        $record->status_reason = null;
                        $record->last_updated_by = auth()->id();
                        $record->save();
                        
                        // Bildirim gönder
                        \App\Models\Notifications::create([
                            'notifiable_id' => $record->user_id,
                            'notifiable_type' => 'App\Models\User',
                            'title' => 'Bursunuz Aktifleştirildi',
                            'message' => 'Bursunuz tekrar aktifleştirildi.',
                            'type' => 'b',
                            'application_id' => $record->application_id,
                            'is_read' => false,
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Burs Aktifleştirildi')
                            ->body('Öğrencinin bursu aktifleştirildi ve bildirim gönderildi')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Scholarships $record): bool => $record->status === 'suspended'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_suspend')
                        ->label('Toplu Askıya Al')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Askıya Alma Sebebi')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if ($record->status === 'active') {
                                    $record->status = 'suspended';
                                    $record->status_reason = $data['reason'];
                                    $record->last_updated_by = auth()->id();
                                    $record->save();
                                    
                                    // Bildirim gönder
                                    \App\Models\Notifications::create([
                                        'notifiable_id' => $record->user_id,
                                        'notifiable_type' => 'App\Models\User',
                                        'title' => 'Bursunuz Askıya Alındı',
                                        'message' => 'Bursunuz askıya alındı. Sebep: ' . $data['reason'],
                                        'type' => 'b',
                                        'application_id' => $record->application_id,
                                        'is_read' => false,
                                    ]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Burslar Askıya Alındı')
                                ->body('Seçilen öğrencilerin bursları askıya alındı ve bildirimler gönderildi')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_terminate')
                        ->label('Toplu Sonlandır')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Textarea::make('reason')
                                ->label('Sonlandırma Sebebi')
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            foreach ($records as $record) {
                                if (in_array($record->status, ['active', 'suspended'])) {
                                    $record->status = 'terminated';
                                    $record->status_reason = $data['reason'];
                                    $record->last_updated_by = auth()->id();
                                    $record->save();
                                    
                                    // Bildirim gönder
                                    \App\Models\Notifications::create([
                                        'notifiable_id' => $record->user_id,
                                        'notifiable_type' => 'App\Models\User',
                                        'title' => 'Bursunuz Sonlandırıldı',
                                        'message' => 'Bursunuz sonlandırıldı. Sebep: ' . $data['reason'],
                                        'type' => 'b',
                                        'application_id' => $record->application_id,
                                        'is_read' => false,
                                    ]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Burslar Sonlandırıldı')
                                ->body('Seçilen öğrencilerin bursları sonlandırıldı ve bildirimler gönderildi')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('bulk_activate')
                        ->label('Toplu Aktifleştir')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            foreach ($records as $record) {
                                if ($record->status === 'suspended') {
                                    $record->status = 'active';
                                    $record->status_reason = null;
                                    $record->last_updated_by = auth()->id();
                                    $record->save();
                                    
                                    // Bildirim gönder
                                    \App\Models\Notifications::create([
                                        'notifiable_id' => $record->user_id,
                                        'notifiable_type' => 'App\Models\User',
                                        'title' => 'Bursunuz Aktifleştirildi',
                                        'message' => 'Bursunuz tekrar aktifleştirildi.',
                                        'type' => 'b',
                                        'application_id' => $record->application_id,
                                        'is_read' => false,
                                    ]);
                                }
                            }
                            
                            \Filament\Notifications\Notification::make()
                                ->title('Burslar Aktifleştirildi')
                                ->body('Seçilen öğrencilerin bursları aktifleştirildi ve bildirimler gönderildi')
                                ->success()
                                ->send();
                        }),
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
            'index' => Pages\ListScholarshipStudents::route('/'),
            'view' => Pages\ViewScholarshipStudent::route('/{record}'),
        ];
    }
} 