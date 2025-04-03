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
use Illuminate\Support\Facades\Auth;

class ScholarshipStatusResource extends Resource
{
    protected static ?string $model = Scholarships::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'Burs Durumum';
    
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Burs İşlemleri';
    
    protected static ?string $title = 'Burs Durumum';

    protected static ?string $breadcrumb = 'Burs Durumum';

   

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required()
                    ->searchable()
                    ->label('Burs Programı'),
                Forms\Components\Select::make('application_id')
                    ->relationship('application', 'id')
                    ->required()
                    ->searchable()
                    ->label('Başvuru'),
                Forms\Components\TextInput::make('name')
                    ->label('Burs Adı')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('Bitiş Tarihi'),
                Forms\Components\TextInput::make('amount')
                    ->label('Tutar')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'durduruldu' => 'Durduruldu',
                        'sonlandirildi' => 'Sonlandırıldı',
                    ])
                    ->default('aktif')
                    ->required()
                    ->label('Durum'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Burs durumunuz bulunamadı.')
        ->emptyStateDescription('Lütfen tum belgelerinizi yukledikten sonra tekrar deneyiniz ve Mülakat sonucunda burs durumunuzu görüntüleyebilirsiniz.')
       
            ->columns([
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('TRY')
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
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Başlangıç Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Bitiş Tarihi')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('start_date', 'desc');
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
