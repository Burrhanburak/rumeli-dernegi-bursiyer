<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScholarshipProgramResource\Pages;
use App\Filament\Resources\ScholarshipProgramResource\RelationManagers;
use App\Models\ScholarshipProgram;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScholarshipProgramResource extends Resource
{
    protected static ?string $model = ScholarshipProgram::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    
    protected static ?string $navigationGroup = 'Burs Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Burs Programları';

    protected static ?string $title = 'Burs Programları';

    protected static ?string $breadcrumb = 'Burs Yönetimi';

    protected static ?string $breadcrumbParent = 'Burs Yönetimi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Program Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Program Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('default_amount')
                            ->label('Burs Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        Forms\Components\TextInput::make('requirements')
                            ->label('Gereksinimler')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tarihler ve Durum')
                    ->schema([
                        Forms\Components\DatePicker::make('application_start_date')
                            ->label('Başvuru Başlangıç Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('application_end_date')
                            ->label('Başvuru Bitiş Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required()
                            ->after('application_start_date'),
                        Forms\Components\DatePicker::make('program_start_date')
                            ->label('Program Başlangıç Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('program_end_date')
                            ->label('Program Bitiş Tarihi')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('program_start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif mi')
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Kontenjan Bilgisi')
                    ->schema([
                        Forms\Components\TextInput::make('max_recipients')
                            ->label('Maksimum Alıcı Sayısı')
                            ->numeric()
                            ->nullable(),
                     
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Burs Programları bulunamadı')
            ->emptyStateDescription('Yeni bir burs programı oluşturmak için "Yeni Program" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Burs Programı')
                    ->icon('heroicon-o-academic-cap'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Program Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('default_amount')
                    ->label('Tutar')
                    ->money('try')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_recipients')
                    ->label('Kontenjan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),
                Tables\Columns\TextColumn::make('application_start_date')
                    ->label('Başvuru Başlangıcı')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_end_date')
                    ->label('Başvuru Bitişi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_start_date')
                    ->label('Program Başlangıcı')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('program_end_date')
                    ->label('Program Bitişi')
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
                Tables\Filters\Filter::make('is_active')
                    ->label('Aktif Programlar')
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true))
                    ->toggle(),
                Tables\Filters\Filter::make('current')
                    ->label('Güncel Programlar')
                    ->query(fn (Builder $query): Builder => $query->where('program_start_date', '<=', now())->where('program_end_date', '>=', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('accepting_applications')
                    ->label('Başvuruya Açık')
                    ->query(fn (Builder $query): Builder => $query->where('application_end_date', '>=', now()))
                    ->toggle(),
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
            RelationManagers\ApplicationsRelationManager::make(),
            RelationManagers\ScholarshipsRelationManager::make(),
            RelationManagers\DocumentRequirementsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListScholarshipPrograms::route('/'),
            'create' => Pages\CreateScholarshipProgram::route('/create'),
            'view' => Pages\ViewScholarshipProgram::route('/{record}'),
            'edit' => Pages\EditScholarshipProgram::route('/{record}/edit'),
        ];
    }
}