<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

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
    protected static ?string $title = 'Belgeler';
    protected static ?string $breadcrumb = 'Belgeler';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->disk('public')
                    ->directory('documents')
                    ->visibility('private')
                    ->maxSize(5120), // 5MB
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_verified')
                    ->label('Doğrulandı mı')
                    ->default(false),
                Forms\Components\DatePicker::make('verification_date')
                    ->label('Doğrulama Tarihi'),
                Forms\Components\DatePicker::make('created_at')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('verified_by')
                    ->label('Doğrulayan')
                    ->relationship('verifier', 'name')
                    ->searchable(),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Belgeler bulunamadı')
            ->emptyStateDescription('Yeni bir belge eklemek için "Yeni Belgeler" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Belgeler')
            ])
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
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
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_verified')
                    ->options([
                        true => 'Verified',
                        false => 'Not Verified',
                    ]),
                Tables\Filters\SelectFilter::make('document_type_id')
                    ->relationship('documentType', 'name')
                    ->label('Document Type'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 