<?php

namespace App\Filament\Resources\User\ApplicationManagementResource\RelationManagers;

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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Belge Adı'),
                Forms\Components\FileUpload::make('file_path')
                    ->required()
                    ->label('Dosya')
                    ->disk('public')
                    ->directory('documents'),
                Forms\Components\Toggle::make('is_approved')
                    ->label('Onaylandı mı?')
                    ->default(false),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(1000),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Belge Adı')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yüklenme Tarihi')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label('Onaylandı mı?')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Onaylanma Tarihi')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('is_approved')
                    ->label('Sadece Onaylananlar')
                    ->query(fn (Builder $query): Builder => $query->where('is_approved', true))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Onayla')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($record) {
                        $record->is_approved = true;
                        $record->approved_at = now();
                        $record->save();
                    })
                    ->visible(fn ($record) => !$record->is_approved),
                Tables\Actions\Action::make('download')
                    ->label('İndir')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Seçilenleri Onayla')
                        ->icon('heroicon-o-check')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                if (!$record->is_approved) {
                                    $record->is_approved = true;
                                    $record->approved_at = now();
                                    $record->save();
                                }
                            }
                        }),
                ]),
            ]);
    }
} 