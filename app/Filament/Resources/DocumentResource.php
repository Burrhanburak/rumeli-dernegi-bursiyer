<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Filament\Resources\DocumentResource\RelationManagers;
use App\Models\Documents;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentResource extends Resource
{
    protected static ?string $model = Documents::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Belge Yönetimi';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Belgeler';
    
    protected static ?string $title = 'Belgeler';

    protected static ?string $breadcrumb = 'Belgeler';

    protected static ?string $breadcrumbParent = 'Belgeler';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Belge Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Kullanıcı')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('document_type_id')
                            ->relationship('documentType', 'name')
                            ->label('Belge Türü')
                            ->required()
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('Belge Adı')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('file_path')
                            ->label('Dosya')
                            ->required()
                            ->disk('public')
                            ->directory('documents')
                            ->visibility('private')
                            ->maxSize(5120), // 5MB
                        Forms\Components\Textarea::make('description')
                            ->label('Açıklama')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Doğrulandı mı')
                            ->default(false),
                        Forms\Components\DatePicker::make('verification_date')
                            ->label('Doğrulama Tarihi'),
                        Forms\Components\Select::make('verified_by')
                            ->label('Doğrulayan')
                            ->relationship('verifier', 'name')
                            ->searchable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belgeler bulunamadı')
            ->emptyStateDescription('Yeni bir belge oluşturmak için "Yeni Belgeler" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Belgeler')
                    ->icon('heroicon-o-document-plus')
            ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Kullanıcı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Belge Türü')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Adı')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Doğrulandı')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yükleme Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('verification_date')
                    ->label('Doğrulama Tarihi')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Doğrulayan')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('is_verified')
                    ->label('Doğrulama Durumu')
                    ->options([
                        true => 'Doğrulandı',
                        false => 'Doğrulanmadı',
                    ]),
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
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'view' => Pages\ViewDocument::route('/{record}'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}
