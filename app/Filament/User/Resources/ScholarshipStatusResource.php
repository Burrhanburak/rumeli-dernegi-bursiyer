<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ScholarshipStatusResource\Pages;
use App\Filament\User\Resources\ScholarshipStatusResource\RelationManagers;
use App\Models\Scholarships;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ScholarshipStatusResource extends Resource
{
    protected static ?string $model = Scholarships::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationLabel = 'Burs Durumum';
    
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Burs İşlemleri';
    
    protected static ?string $title = 'Burs Durumum';

    protected static ?string $breadcrumb = 'Burs Durumum';

   

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id())
            ->latest();
    }

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
                Forms\Components\Section::make('Burs Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Burs Adı')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Burs Miktarı (₺)')
                            ->disabled(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Başlangıç Tarihi')
                            ->disabled(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Bitiş Tarihi')
                            ->disabled(),
                            Forms\Components\TextInput::make('status')
                            ->label('Durum')
                            ->default('active')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                $statusLabels = [
                                    'active' => 'Aktif',
                                    'suspended' => 'Askıya Alındı',
                                    'completed' => 'Tamamlandı',
                                    'terminated' => 'Sonlandırıldı',
                                ];
                                
                                return $statusLabels[$state] ?? $state;
                            }),
                        Forms\Components\Textarea::make('status_reason')
                            ->label('Durum Nedeni')
                            ->disabled()
                            ->visible(fn (Scholarships $record) => !empty($record->status_reason)),
                    ])->columns(2),
                
                Forms\Components\Section::make('Program Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('program_name')
                        ->label('Program')
                        ->disabled()
                        ->formatStateUsing(function ($state, $record) {
                            if ($record && $record->program) {
                                return $record->program->name;
                            }
                            return ''; 
                        })
                        ->dehydrated(false), // Form gönderildiğinde bu alanı işleme alma
                        Forms\Components\TextInput::make('application_id')
                            ->label('Başvuru ID')
                            ->disabled(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Aktif bursunuz bulunmamaktadır')
            ->emptyStateDescription('Henüz bir burs kazanmadınız veya bursunuz onaylanmadı. Başvurunuzun durumunu takip edin.')
            ->columns([
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Burs Programı Adı')
                    ->searchable(),
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
                    }),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program Adı')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detay Gör'),
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
            'index' => Pages\ListScholarshipStatuses::route('/'),
            'view' => Pages\ViewScholarshipStatus::route('/{record}'),
        ];
    }
}
