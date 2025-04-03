<?php

namespace App\Filament\Resources\ScholarshipProgramResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\DocumentType;
use App\Models\ProgramDocumentRequirement;
use Illuminate\Support\Facades\Auth;

class DocumentRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentRequirements';
    
    protected static ?string $title = 'Evrak Gereksinimleri';
    protected static ?string $label = 'Evrak Gereksinimi';
    protected static ?string $pluralLabel = 'Evrak Gereksinimleri';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('document_type_id')
                    ->label('Belge Türü')
                    ->options(DocumentType::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Toggle::make('is_required')
                    ->label('Zorunlu mu?')
                    ->default(true)
                    ->required(),
                Forms\Components\Hidden::make('created_by')
                    ->default(Auth::id()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('documentType.name')
                    ->label('Belge Türü')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_required')
                    ->label('Zorunlu')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Evrak Ekle'),
            ])
            ->actions([
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
