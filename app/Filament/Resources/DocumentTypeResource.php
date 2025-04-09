<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentTypeResource\Pages;
use App\Filament\Resources\DocumentTypeResource\RelationManagers;
use App\Models\DocumentType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Belge Türleri';

    protected static ?string $navigationGroup = 'Belge Yönetimi';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Belge Türleri ';

    protected static ?string $breadcrumb = 'Belge Türleri';

    protected static ?string $breadcrumbParent = 'Belge Yönetimi';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Belge Türü')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Türü')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle')    ,
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Belge Türünü Sil')
                    ->modalDescription('Bu belge türünü silmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Sil')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->label('Sil')
                    ->requiresConfirmation()
                    ->modalHeading('Belge Türlerini Sil')
                    ->modalDescription('Seçili belge türlerini silmek istediğinizden emin misiniz?')
                    ->modalSubmitActionLabel('Evet, Sil')
                ])

                ->label('Belge Türü İşlemleri') 
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
            'index' => Pages\ListDocumentTypes::route('/'),
            'create' => Pages\CreateDocumentType::route('/create'),
            'view' => Pages\ViewDocumentType::route('/{record}'),
            'edit' => Pages\EditDocumentType::route('/{record}/edit'),
        ];
    }
}
