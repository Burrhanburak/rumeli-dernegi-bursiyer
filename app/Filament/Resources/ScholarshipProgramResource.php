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
    
    protected static ?int $navigationSort = 1;
    
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
                        Forms\Components\TextInput::make('code')
                            ->label('Program Kodu')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('amount')
                            ->label('Burs Tutarı')
                            ->numeric()
                            ->prefix('₺')
                            ->required(),
                        Forms\Components\Select::make('frequency')
                            ->label('Ödeme Sıklığı')
                            ->options([
                                'monthly' => 'Aylık',
                                'quarterly' => '3 Aylık',
                                'biannual' => '6 Aylık',
                                'annual' => 'Yıllık',
                                'one-time' => 'Tek Seferlik',
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Açıklama')
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('eligibility_criteria')
                            ->label('Uygunluk Kriterleri')
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Tarihler ve Durum')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Başlangıç Tarihi')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Bitiş Tarihi')
                            ->required()
                            ->after('start_date'),
                        Forms\Components\DatePicker::make('application_deadline')
                            ->label('Son Başvuru Tarihi')
                            ->required()
                            ->beforeOrEqual('start_date'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif mi')
                            ->required(),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Kontenjan Bilgisi')
                    ->schema([
                        Forms\Components\TextInput::make('quota')
                            ->label('Kontenjan')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('min_gpa')
                            ->label('Minimum Not Ortalaması')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(4.0),
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
                Tables\Columns\TextColumn::make('code')
                    ->label('Program Kodu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Tutar')
                    ->money('try')
                    ->sortable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Ödeme Sıklığı')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Aylık',
                        'quarterly' => '3 Aylık',
                        'biannual' => '6 Aylık',
                        'annual' => 'Yıllık',
                        'one-time' => 'Tek Seferlik',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('quota')
                    ->label('Kontenjan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Durum')
                    ->boolean(),
                Tables\Columns\TextColumn::make('application_deadline')
                    ->label('Son Başvuru')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Başlangıç')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Bitiş')
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
                    ->query(fn (Builder $query): Builder => $query->where('start_date', '<=', now())->where('end_date', '>=', now()))
                    ->toggle(),
                Tables\Filters\Filter::make('accepting_applications')
                    ->label('Başvuruya Açık')
                    ->query(fn (Builder $query): Builder => $query->where('application_deadline', '>=', now()))
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
            RelationManagers\DocumentRequirementsRelationManager::make(),
            RelationManagers\ScholarshipsRelationManager::make(),
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
