<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramDocumentRequirementResource\Pages;
use App\Filament\Resources\ProgramDocumentRequirementResource\RelationManagers;
use App\Models\ProgramDocumentRequirement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProgramDocumentRequirementResource extends Resource
{
    protected static ?string $model = ProgramDocumentRequirement::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    
    protected static ?string $navigationGroup = 'Belge Yönetimi';
    
    protected static ?int $navigationSort = 2;
    
    protected static ?string $navigationLabel = 'Program Belge Gereksinimleri';

    protected static ?string $title = 'Program Belge Gereksinimleri';

    protected static ?string $breadcrumb = 'Program Belge Gereksinimleri';

    protected static ?string $breadcrumbParent = 'Program Belge Gereksinimleri';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Gereksinim Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('program_id')
                            ->relationship('program', 'name')
                            ->label('Program')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('document_type_id')
                            ->relationship('documentType', 'name')
                            ->label('Belge Türü')
                            ->required()
                            ->searchable(),
                        Forms\Components\Toggle::make('is_required')
                            ->label('Zorunlu mu')
                            ->required()
                            ->default(true),
                        Forms\Components\Hidden::make('created_by')
                            ->default(auth()->id()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Program Belge Gereksinimleri bulunamadı')
            ->emptyStateDescription('Yeni bir program belge gereksinimi oluşturmak için "Yeni Program Belge Gereksinimi" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Program Belge Gereksinimi')
                    ->icon('heroicon-o-clipboard-document-check'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('scholarship_programs.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('document_types.name')
                    ->label('Belge Türü')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Zorunlu')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Oluşturan')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('is_required')
                    ->label('Zorunluluk Durumu')
                    ->options([
                        true => 'Zorunlu',
                        false => 'İsteğe Bağlı',
                    ]),
                Tables\Filters\SelectFilter::make('program_id')
                    ->relationship('program', 'name')
                    ->label('Program'),
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Belge Türü'),
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
            'index' => Pages\ListProgramDocumentRequirements::route('/'),
            'create' => Pages\CreateProgramDocumentRequirement::route('/create'),
            'view' => Pages\ViewProgramDocumentRequirement::route('/{record}'),
            'edit' => Pages\EditProgramDocumentRequirement::route('/{record}/edit'),
        ];
    }
}
