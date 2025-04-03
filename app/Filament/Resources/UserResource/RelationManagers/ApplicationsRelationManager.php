<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'applications';
    protected static ?string $title = 'Başvurular';
    protected static ?string $breadcrumb = 'Başvurular';
    protected static ?string $breadcrumbParent = 'Kullanıcılar';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('program_id')
                    ->relationship('program', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->options([
                        'scholarship_pool' => 'Burs Havuzu',
                        'pre_approved' => 'Ön Kabul',
                        'rejected' => 'Reddedildi',
                        'awaiting_documents' => 'Evrak Bekleniyor',
                        'documents_under_review' => 'Evrak İncelemede',
                        'interview_pool' => 'Mülakat Havuzu',
                        'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_completed' => 'Mülakat Tamamlandı',
                        'accepted' => 'Kabul Edildi',
                        'final_acceptance' => 'Kesin Kabul',
                        'previous_scholar' => 'Önceki Burslu',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->required()
                    ->default(now()),
                Forms\Components\Toggle::make('are_documents_approved')
                    ->label('Evraklar Onaylandı'),
                Forms\Components\Toggle::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı'),
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Reddetme Nedeni')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Başvuru bulunamadı')
            ->emptyStateDescription('Yeni bir başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru')
            ])
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'scholarship_pool' => 'primary',
                        'pre_approved' => 'success',
                        'rejected' => 'danger',
                        'awaiting_documents' => 'warning',
                        'documents_under_review' => 'info',
                        'interview_pool' => 'primary',
                        'awaiting_evaluation' => 'warning',
                        'interview_scheduled' => 'primary',
                        'interview_completed' => 'success',
                        'accepted' => 'success',
                        'final_acceptance' => 'success',
                        'previous_scholar' => 'success',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scholarship_pool' => 'Burs Havuzu',
                        'pre_approved' => 'Ön Kabul',
                        'rejected' => 'Reddedildi',
                        'awaiting_documents' => 'Evrak Bekleniyor',
                        'documents_under_review' => 'Evrak İncelemede',
                        'interview_pool' => 'Mülakat Havuzu',
                        'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_completed' => 'Mülakat Tamamlandı',
                        'accepted' => 'Kabul Edildi',
                        'final_acceptance' => 'Kesin Kabul',
                        'previous_scholar' => 'Önceki Burslu',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Evraklar Onaylı')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Son Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'scholarship_pool' => 'Burs Havuzu',
                        'pre_approved' => 'Ön Kabul',
                        'rejected' => 'Reddedildi',
                        'awaiting_documents' => 'Evrak Bekleniyor',
                        'documents_under_review' => 'Evrak İncelemede',
                        'interview_pool' => 'Mülakat Havuzu',
                        'awaiting_evaluation' => 'Değerlendirme Bekleniyor',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_completed' => 'Mülakat Tamamlandı',
                        'accepted' => 'Kabul Edildi',
                        'final_acceptance' => 'Kesin Kabul',
                        'previous_scholar' => 'Önceki Burslu',
                    ]),
                Tables\Filters\Filter::make('documents_approved')
                    ->label('Evrakları Onaylı')
                    ->query(fn (Builder $query): Builder => $query->where('are_documents_approved', true))
                    ->toggle(),
                Tables\Filters\Filter::make('interview_completed')
                    ->label('Mülakatı Tamamlanmış')
                    ->query(fn (Builder $query): Builder => $query->where('is_interview_completed', true))
                    ->toggle(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
} 