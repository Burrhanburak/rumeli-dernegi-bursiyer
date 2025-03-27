<?php

namespace App\Filament\Resources\ApplicationsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->maxLength(65535),
                Forms\Components\Toggle::make('is_verified')
                    ->label('Doğrulandı mı')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Belge Ekle'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }
} 
