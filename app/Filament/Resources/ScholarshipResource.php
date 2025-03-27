<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipResource\Pages;
use App\Filament\Resources\ScholarshipResource\RelationManagers;
use App\Models\Scholarships;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScholarshipResource extends Resource
{
    protected static ?string $model = Scholarships::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Burs Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Burslar';

    protected static ?string $title = 'Burslar';

    protected static ?string $breadcrumb = 'Burs Yönetimi';

    protected static ?string $breadcrumbParent = 'Burs Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Burs Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->label('Program')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('student_id')
                            ->relationship('student', 'name')
                            ->label('Öğrenci')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Tutar')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'active' => 'Aktif',
                                'suspended' => 'Askıya Alındı',
                                'completed' => 'Tamamlandı',
                                'terminated' => 'Sonlandırıldı',
                            ])
                            ->required()
                            ->default('active'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Başlangıç Tarihi')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Bitiş Tarihi')
                            ->required()
                            ->after('start_date'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burslar bulunamadı')
            ->emptyStateDescription('Yeni bir burs oluşturmak için "Yeni Burs" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Burs')
                    ->icon('heroicon-o-cursor-arrow-ripple'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Öğrenci')
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
                        'active' => 'Aktif',
                        'suspended' => 'Askıya Alındı',
                        'completed' => 'Tamamlandı',
                        'terminated' => 'Sonlandırıldı',
                    ]),
                Tables\Filters\SelectFilter::make('program_id')
                    ->relationship('program', 'name')
                    ->label('Program'),
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
            'index' => Pages\ListScholarships::route('/'),
            'create' => Pages\CreateScholarship::route('/create'),
            'view' => Pages\ViewScholarship::route('/{record}'),
            'edit' => Pages\EditScholarship::route('/{record}/edit'),
        ];
    }
}
