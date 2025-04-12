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
use Filament\Notifications\Notification;

class DocumentRequirementsRelationManager extends RelationManager
{
    protected static string $relationship = 'documentRequirements';
    
    protected static ?string $title = 'Program İçin Evrak Gereksinimleri';
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

            ->emptyStateHeading('Evrak Gereksinimi bulunamadı')
            ->emptyStateDescription('Yeni bir evrak gereksinimi eklemek için "Evrak Ekle" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Evrak Ekle'),
            ])
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
                    ->label('Evrak Ekle')
                    ->modalHeading('Evrak Ekle')
                    ->modalDescription('Bu evrak gereksinimini eklemek istediğinizden emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Evet, Ekle')
                    ->createAnother(false, 'Oluştur ve başka ekle')
                    ->successNotification(
                        Notification::make()
                            ->title('Evrak Gereksinimi Eklendi')
                            ->body('Evrak gereksinimi başarıyla eklendi.')
                            ->success()
                    )
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle')
                    ->modalHeading('Evrak Gereksinimi Düzenle')
                    ->modalDescription('Bu evrak gereksinimini düzenlemek istediğinizden emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Evet, Düzenle')
                    
                    ->successNotification(
                        Notification::make()
                            ->title('Evrak Gereksinimi Düzenlendi')
                            ->body('Evrak gereksinimi başarıyla düzenlendi.')
                            ->success()
                    ),
                Tables\Actions\DeleteAction::make()
                    ->label('Sil')
                    ->requiresConfirmation()
                    ->modalHeading('Evrak Gereksinimi Sil')
                    ->modalDescription('Bu evrak gereksinimini silmek istediğinizden emin misiniz?')
                    ->modalCancelActionLabel('İptal')
                    ->modalSubmitActionLabel('Evet, Sil')
                    ->successNotification(
                        Notification::make()
                            ->title('Evrak Gereksinimi Silindi')
                            ->body('Evrak gereksinimi başarıyla silindi.')
                            ->success()
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                ]),
            ]);
    }
}