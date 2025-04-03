<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationsResource\Pages;
use App\Filament\Resources\NotificationsResource\RelationManagers;
use App\Models\Notifications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\User;

class NotificationsResource extends Resource
{
    protected static ?string $model = Notifications::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationGroup = 'İletişim';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $navigationLabel = 'Bildirimler';

    protected static ?string $title = 'Bildirimler';

    protected static ?string $breadcrumb = 'Bildirim';

    protected static ?string $breadcrumbParent = 'İletişim';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bildirim Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('notifiable_type')
                            ->label('Bildirim Alıcı Tipi')
                            ->options([
                                User::class => 'Kullanıcı',
                            ])
                            ->default(User::class)
                            ->required(),
                        Forms\Components\Select::make('notifiable_id')
                            ->label('Alıcı')
                            ->options(function (callable $get) {
                                $type = $get('notifiable_type');
                                if (!$type) return [];
                                
                                return $type::all()->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('Başlık')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('message')
                            ->label('İçerik')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('type')
                            ->label('Tür')
                            ->options([
                                'document_required' => 'Belge - Belge Gerekli',
                                'document_approved' => 'Belge - Belge Onaylandı',
                                'document_rejected' => 'Belge - Belge Reddedildi',
                                'interview_scheduled' => 'Mülakat - Mülakat Planlandı',
                                'interview_reminder' => 'Mülakat - Mülakat Hatırlatma',
                                'application_status' => 'Başvuru - Başvuru Durumu',
                                'scholarship_awarded' => 'Burs - Burs Verildi',
                                'scholarship_changed' => 'Burs - Burs Değişti',
                                'system' => 'Sistem',
                            ])
                            ->required(),
                        Forms\Components\Checkbox::make('is_read')
                            ->label('Okundu')
                            ->default(false),
                        Forms\Components\DateTimePicker::make('read_at')
                            ->label('Okunma Tarihi')
                            ->displayFormat('d.m.Y H:i:s')
                            ->default(now())
                            
                            ->hidden(),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Gönderilme Tarihi')
                            ->hidden(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->emptyStateHeading('Bildirim bulunamadı')
        ->emptyStateDescription('Yeni bir bildirim oluşturmak için "Yeni Bildirim" düğmesine tıklayın.')
        ->emptyStateActions([
            Tables\Actions\CreateAction::make()
                ->label('Yeni Bildirim')
                ->icon('heroicon-o-bell')
        ])
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notifiable.name')
                    ->label('Alıcı')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Başlık')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'document_required' => 'Belge Gerekli',
                        'document_approved' => 'Belge Onaylandı',
                        'document_rejected' => 'Belge Reddedildi',
                        'interview_scheduled' => 'Mülakat Planlandı',
                        'interview_reminder' => 'Mülakat Hatırlatma',
                        'application_status' => ' Başvuru Durumu',
                        'scholarship_awarded' => 'Burs Verildi',
                        'scholarship_changed' => 'Burs Değişti',
                        'system' => 'Sistem',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_read')
                    ->label('Okundu')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Gönderilme Tarihi')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->label('Okunma Tarihi')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncellenme Tarihi')
                    ->dateTime('d.m.Y H:i:s')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_read')
                    ->label('Okunma Durumu')
                    ->options([
                        true => 'Okundu',
                        false => 'Okunmadı',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tür')
                    ->options([
                        'document_required' => 'Belge - Belge Gerekli',
                        'document_approved' => 'Belge - Belge Onaylandı',
                        'document_rejected' => 'Belge - Belge Reddedildi',
                        'interview_scheduled' => 'Mülakat - Mülakat Planlandı',
                        'interview_reminder' => 'Mülakat - Mülakat Hatırlatma',
                        'application_status' => 'Başvuru - Başvuru Durumu',
                        'scholarship_awarded' => 'Burs - Burs Verildi',
                        'scholarship_changed' => 'Burs - Burs Değişti',
                        'system' => 'Sistem',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotifications::route('/create'),
            'view' => Pages\ViewNotifications::route('/{record}'),
            'edit' => Pages\EditNotifications::route('/{record}/edit'),
        ];
    }
}
