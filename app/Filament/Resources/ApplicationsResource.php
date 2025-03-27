<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApplicationsResource\Pages;
use App\Filament\Resources\ApplicationsResource\RelationManagers;
use App\Models\Applications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApplicationsResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationGroup = 'Başvuru Süreci';
    
    protected static ?string $navigationLabel = 'Başvurular';

    protected static ?string $modelLabel = 'Başvuru';

    protected static ?string $pluralModelLabel = 'Başvurular';

    protected static ?string $recordTitleAttribute = 'ad';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Başvuru Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Öğrenci')
                            ->relationship('user', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->name . ' ' . $record->surname)
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->preload()
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Durum')
                            ->options([
                                'burs_havuzu' => 'Burs Havuzu',
                                'on_kabul' => 'Ön Kabul',
                                'red_edildi' => 'Reddedildi',
                                'evrak_bekleniyor' => 'Evrak Bekleniyor',
                                'evrak_incelemede' => 'Evrak İncelemede',
                                'mulakat_havuzu' => 'Mülakat Havuzu',
                                'mulakat_planlandi' => 'Mülakat Planlandı',
                                'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                                'kabul_edildi' => 'Kabul Edildi',
                                'kesin_kabul' => 'Kesin Kabul',
                                'onceki_burslu' => 'Önceki Burslu',
                            ])
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Durum seçin')
                            ->selectablePlaceholder(false)
                            ->optionsLimit(10),
                        Forms\Components\DatePicker::make('application_date')
                            ->label('Başvuru Tarihi')
                            ->default(now()),
                    ])->columns(2),
                    
                Forms\Components\Section::make('Kişisel Bilgiler')
                
                    ->schema([
                        Forms\Components\TextInput::make('tc_kimlik_no')
                            ->label('TC Kimlik No')
                            ->placeholder('Örnek: 12345678901')
                            ->maxLength(11),
                        Forms\Components\TextInput::make('ad')
                            ->label('Ad'),
                        Forms\Components\TextInput::make('soyad')
                            ->label('Soyad'),
                        Forms\Components\DatePicker::make('dogum_tarihi')
                            ->label('Doğum Tarihi'),
                        Forms\Components\TextInput::make('dogum_yeri')
                            ->label('Doğum Yeri'),
                        Forms\Components\TextInput::make('uyrugu')
                            ->label('Uyruğu')
                            ->placeholder('T.C'),
                            
                        Forms\Components\Select::make('cinsiyet')
                            ->label('Cinsiyet')
                            ->options([
                                'Erkek' => 'Erkek',
                                'Kadın' => 'Kadın',
                                'Diğer' => 'Diğer',
                            ]),
                        Forms\Components\TextInput::make('telefon')
                            ->label('Telefon')
                            ->placeholder('Örnek: 0555 555 55 55')
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->tel(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->placeholder('Örnek: example@example.com')
                            ->email(),
                        Forms\Components\Textarea::make('beden_ozru')
                            ->label('Beden Özrünüz Varsa Yazınız'),
                        Forms\Components\TextInput::make('nufusa_kayitli_il')
                            ->label('Nüfusa Kayıtlı Olduğu İl'),
                        Forms\Components\TextInput::make('nufusa_kayitli_ilce')
                            ->label('Nüfusa Kayıtlı Olduğu İlçe'),
                        Forms\Components\FileUpload::make('image')
                            ->label('Profil Resmi')
                            ->image()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory('application-images')
                            ->visibility('public')
                            ->downloadable()
                            ->placeholder('Profil resmi yükleyin veya sürükleyin')
                            ->imagePreviewHeight('250')
                            ->loadingIndicatorPosition('left')
                            ->panelAspectRatio('4:1')
                            ->panelLayout('integrated')
                            ->disk('public'),
                    ])->columns(2),

                Forms\Components\Section::make('Eğitim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('okul_adi')
                            ->label('Okul Adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('okul_bolumu')
                            ->label('Okul Bölümü')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sinif')
                            ->label('Sınıf'),
                        Forms\Components\TextInput::make('giris_yili')
                            ->label('Giriş Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('burs_orani')
                            ->label('Burs Oranı %'),
                        Forms\Components\TextInput::make('universiteye_giris_puani')
                            ->label('Üniversiteye Giriş Puanı')
                            ->numeric(),
                        Forms\Components\TextInput::make('ogrenci_numarasi')
                            ->label('Öğrenci Numarası'),
                        Forms\Components\TextInput::make('ilkokul_adi')
                            ->label('Bitirdiğiniz Okulların adı ve derecesi (İlkokul)'),
                        Forms\Components\TextInput::make('ilkokul_mezuniyet_yili')
                            ->label('İlkokul Mezuniyet Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('lise_adi')
                            ->label('Bitirdiğiniz Okulların adı ve derecesi (Lise veya dengi okul)'),
                        Forms\Components\TextInput::make('lise_mezuniyet_yili')
                            ->label('Lise Mezuniyet Yılı')
                            ->numeric(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Aile Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('baba_adi')
                            ->label('Baba Adı'),
                        Forms\Components\TextInput::make('baba_soyadi')
                            ->label('Baba Soyadı'),
                        Forms\Components\TextInput::make('baba_dogum_yili')
                            ->label('Baba Doğum Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('baba_dogum_yeri')
                            ->label('Baba Doğum Yeri'),
                        Forms\Components\TextInput::make('baba_meslegi')
                            ->label('Babanızın Mesleği'),
                        Forms\Components\TextInput::make('baba_vefat_yili')
                            ->label('Baba Vefat etmişse Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('anne_adi')
                            ->label('Anne Adı'),
                        Forms\Components\TextInput::make('anne_soyadi')
                            ->label('Anne Soyadı'),
                        Forms\Components\TextInput::make('anne_dogum_yili')
                            ->label('Anne Doğum Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('anne_dogum_yeri')
                            ->label('Anne Doğum Yeri'),
                        Forms\Components\TextInput::make('anne_meslegi')
                            ->label('Annenizin Mesleği'),
                        Forms\Components\TextInput::make('anne_vefat_yili')
                            ->label('Anne Vefat etmişse Yılı')
                            ->numeric(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Kardeş Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('kardes1_adi')
                            ->label('Kardeş 1 Adı'),
                        Forms\Components\TextInput::make('kardes1_soyadi')
                            ->label('Kardeş 1 Soyadı'),
                        Forms\Components\TextInput::make('kardes1_yasi')
                            ->label('Kardeş 1 Yaşı')
                            ->numeric(),
                        Forms\Components\TextInput::make('kardes1_ogrenim_durumu')
                            ->label('Kardeş 1 Öğrenim Durumu'),
                        Forms\Components\TextInput::make('kardes2_adi')
                            ->label('Kardeş 2 Adı'),
                        Forms\Components\TextInput::make('kardes2_soyadi')
                            ->label('Kardeş 2 Soyadı'),
                        Forms\Components\TextInput::make('kardes2_yasi')
                            ->label('Kardeş 2 Yaşı')
                            ->numeric(),
                        Forms\Components\TextInput::make('kardes2_ogrenim_durumu')
                            ->label('Kardeş 2 Öğrenim Durumu'),
                        Forms\Components\TextInput::make('kardes3_adi')
                            ->label('Kardeş 3 Adı'),
                        Forms\Components\TextInput::make('kardes3_soyadi')
                            ->label('Kardeş 3 Soyadı'),
                        Forms\Components\TextInput::make('kardes3_yasi')
                            ->label('Kardeş 3 Yaşı')
                            ->numeric(),
                        Forms\Components\TextInput::make('kardes3_ogrenim_durumu')
                            ->label('Kardeş 3 Öğrenim Durumu'),
                        Forms\Components\TextInput::make('kardes4_adi')
                            ->label('Kardeş 4 Adı'),
                        Forms\Components\TextInput::make('kardes4_soyadi')
                            ->label('Kardeş 4 Soyadı'),
                        Forms\Components\TextInput::make('kardes4_yasi')
                            ->label('Kardeş 4 Yaşı')
                            ->numeric(),
                        Forms\Components\TextInput::make('kardes4_ogrenim_durumu')
                            ->label('Kardeş 4 Öğrenim Durumu'),
                        Forms\Components\TextInput::make('kardes5_adi')
                            ->label('Kardeş 5 Adı'),
                        Forms\Components\TextInput::make('kardes5_soyadi')
                            ->label('Kardeş 5 Soyadı'),
                        Forms\Components\TextInput::make('kardes5_yasi')
                            ->label('Kardeş 5 Yaşı')
                            ->numeric(),
                        Forms\Components\TextInput::make('kardes5_ogrenim_durumu')
                            ->label('Kardeş 5 Öğrenim Durumu'),
                    ])->columns(4),
                
                Forms\Components\Section::make('İkamet Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('aile_reisinin_baktigi_fert_sayisi')
                            ->label('Aile Reisinin Bakmakta Olduğu Fert Sayısı')
                            ->numeric(),
                        Forms\Components\TextInput::make('ailenin_geçim_sorumlulugu')
                            ->label('Sizin ve Ailenizin Geçim Sorumluluğu Kimin Üzerinedir'),
                        Forms\Components\Textarea::make('aile_ikametgah_adresi')
                            ->label('Ailenizin İkametgah Adresi'),
                        Forms\Components\TextInput::make('ikametgah_ili')
                            ->label('İkametgah İli'),
                        Forms\Components\TextInput::make('ikametgah_ilcesi')
                            ->label('İkametgah İlçesi'),
                        Forms\Components\TextInput::make('aile_telefon')
                            ->label('Ailenizin Telefon Numarası')
                            ->tel(),
                        Forms\Components\Select::make('ailenin_yaninda_kalarak_okula_devam')
                            ->label('Ailenizin Yanında Kalarak mı Okula Devam Ediyorsunuz?')
                            ->options([
                                'Evet' => 'Evet',
                                'Hayır' => 'Hayır',
                            ]),
                        Forms\Components\TextInput::make('yurtta_kaliyorsa_aylik_odeme')
                            ->label('Yurtta Kalıyorsanız Aylık Ne Kadar Ödüyorsunuz?')
                            ->numeric(),
                        Forms\Components\Textarea::make('ogrenim_sirasinda_kaldigi_adres')
                            ->label('Öğrenim Sırasında Kaldığınız İkametgah Adresiniz'),
                        Forms\Components\TextInput::make('ogrenim_ili')
                            ->label('Öğrenim İli'),
                        Forms\Components\TextInput::make('ogrenim_ilcesi')
                            ->label('Öğrenim İlçesi'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Mali Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('baba_aylik_net_gelir')
                            ->label('Babanızın Aylık Net Geliri (TL)')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\TextInput::make('anne_aylik_net_gelir')
                            ->label('Annenizin Aylık Net Geliri (TL)')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\TextInput::make('kardes_aylik_net_gelir')
                            ->label('Kardeşinizin Aylık Net Geliri (TL)')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\TextInput::make('es_aylik_net_gelir')
                            ->label('Varsa Eşinizin Aylık Net Geliri (TL)')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\TextInput::make('vefat_ile_bagli_maas_yillik_net_gelir')
                            ->label('Babanızın yada Annenizin Vefatı ile Size Bağlanan Maaşın Yıllık Net Geliri (TL)')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\Select::make('ailenin_evi_var_mi')
                            ->label('Ailenizin Evi Var Mı?')
                            ->options([
                                'Evet' => 'Evet',
                                'Hayır' => 'Hayır',
                            ]),
                        Forms\Components\TextInput::make('kirada_oturuyor_ise_kira_miktari')
                            ->label('Kirada Oturuyor ise Kira Miktarı')
                            ->numeric()
                            ->prefix('₺'),
                        Forms\Components\Textarea::make('gayrimenkul_degeri_ve_geliri')
                            ->label('Oturulan Evden Başka Ailenizin Gayrimenkulu Varsa Takribi Değeri ve Geliri'),
                        Forms\Components\TextInput::make('otomobil_model_yili')
                            ->label('Aileye Ait Otomobil Varsa Model Yılı')
                            ->numeric(),
                        Forms\Components\TextInput::make('baska_gelir_miktari')
                            ->label('Ailenin Başka Geliri Varsa Miktarı')
                            ->numeric()
                            ->prefix('₺'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Diğer Bilgiler')
                    ->schema([
                        Forms\Components\Textarea::make('brans_secimi')
                            ->label('Öğrenim gördüğünüz bölümde branş seçme söz konusu ise hangi branşı seçmeyi düşünüyorsunuz?'),
                        Forms\Components\Textarea::make('kulup_uyeligi')
                            ->label('Okulunuzda herhangi bir klübe üye misiniz? Henüz üye değilseniz üye olmayı düşündüğünüz bir klüp var mı?'),
                        Forms\Components\Textarea::make('kutuphane_kullanimi')
                            ->label('Üniversitenizin veya başka üniversitelerin kütüphanelerinden veya devlet kütüphanesi vb. kütüphanelerden faydalanıyor musunuz? Nasıl faydalanıyorsunuz?'),
                        Forms\Components\Textarea::make('hobi')
                            ->label('Resim, müzik, edebiyat, sinema vb. bir hobiniz var mı?'),
                        Forms\Components\Select::make('burs_verme_sozu')
                            ->label('İleride bu çatı altında siz de bir kişiye burs olarak yardımcı olmak isteyip bunun sözünü verir misiniz?')
                            ->options([
                                'Evet' => 'Evet',
                                'Hayır' => 'Hayır',
                            ]),
                        Forms\Components\Textarea::make('sosyal_medya_kullanimi')
                            ->label('Sosyal medyayı (Facebook, instagram, linkedin, twitter vb.) ne yoğunlukta kullanıyorsunuz ve hangisini daha çok kullanıyorsunuz?'),
                        Forms\Components\Textarea::make('sosyal_sorumluluk_projesi')
                            ->label('Bugüne kadar herhangi bir sosyal sorumluluk projesinde yer aldınız mı? Cevanızı Evet ise; Ne tür bir sosyal sorumluluk projesiydi ve bu projedeki göreviniz neydi? Cevabınız Hayır ise; Bir sosyal sorumluluk projesinde yer almak ister miydiniz? Bu projenin ne tür bir proje olmasını tercih ederdiniz?'),
                        Forms\Components\Textarea::make('is_hayatinda_basari_gorusu')
                            ->label('Sizce iş hayatında başarılı olmak için sadece akademik başarı yeterli mi?'),
                        Forms\Components\Textarea::make('mezuniyet_sonrasi_hedef')
                            ->label('Mezuniyetinizden 10 yıl sonra kendinizi iş hayatında nerede / hangi pozisyonda görmeyi planlıyorsunuz?'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Referans Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('referans1_adi')
                            ->label('Referans 1 Adı'),
                        Forms\Components\TextInput::make('referans1_tel')
                            ->label('Referans 1 Tel No')
                            ->tel(),
                        Forms\Components\TextInput::make('referans2_adi')
                            ->label('Referans 2 Adı'),
                        Forms\Components\TextInput::make('referans2_tel')
                            ->label('Referans 2 Tel No')
                            ->tel(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Burs Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('baska_burs_kredi_alimi')
                            ->label('Başka herhangi bir resmi veya özel kurumdan burs veya kredi alıyor musunuz?')
                            ->options([
                                'Evet' => 'Evet',
                                'Hayır' => 'Hayır',
                            ]),
                        Forms\Components\TextInput::make('baska_burs_kredi_kurumu')
                            ->label('Alıyorsanız nereden?')
                            ->visible(fn (Forms\Get $get) => $get('baska_burs_kredi_alimi') === 'Evet'),
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->placeholder('Örnek: TR123456789012345678901234')
                            ->maxLength(26),
                    ])->columns(2),
                
                Forms\Components\Section::make('Notlar ve Değerlendirme')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notlar')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reddetme Sebebi')
                            ->columnSpanFull()
                            ->visible(fn (Forms\Get $get) => $get('status') === 'red_edildi'),
                        Forms\Components\Toggle::make('are_documents_approved')
                            ->label('Belgeler Onaylandı mı?')
                            ->default(false),
                        Forms\Components\Toggle::make('is_interview_completed')
                            ->label('Mülakat Tamamlandı mı?')
                            ->default(false),
                        Forms\Components\DatePicker::make('approval_date')
                            ->label('Onay Tarihi')
                            ->visible(fn (Forms\Get $get) => in_array($get('status'), ['kabul_edildi', 'kesin_kabul'])),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->emptyStateHeading('Başvuru bulunamadı')
            ->emptyStateDescription('Yeni bir başvuru eklemek için "Yeni Başvuru" düğmesine tıklayın.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Yeni Başvuru')
            ])
            ->searchable()
            ->searchPlaceholder('Ara...')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Profil')
                    ->circular()
                    ->defaultImageUrl(function ($record) {
                        $name = trim(($record->name ?? '') . ' ' . ($record->surname ?? ''));
                        if (empty($name)) $name = 'Kullanıcı';
                        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&size=128&background=808080&color=ffffff&bold=true&length=2';
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Öğrenci Adı')
                    ->formatStateUsing(fn ($record) => $record->user->name . ' ' . $record->user->surname)
                    ->searchable(),
                Tables\Columns\TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'burs_havuzu' => 'Burs Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        'evrak_bekleniyor' => 'Evrak Bekleniyor',
                        'evrak_incelemede' => 'Evrak İncelemede',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi' => 'Kabul Edildi',
                        'kesin_kabul' => 'Kesin Kabul',
                        'onceki_burslu' => 'Önceki Burslu',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'burs_havuzu',
                        'primary' => 'on_kabul',
                        'danger' => 'red_edildi',
                        'secondary' => ['evrak_bekleniyor', 'evrak_incelemede'],
                        'info' => ['mulakat_havuzu', 'mulakat_planlandi'],
                        'success' => ['mulakat_tamamlandi', 'kabul_edildi', 'kesin_kabul'],
                        'gray' => 'onceki_burslu',
                    ]),
                Tables\Columns\TextColumn::make('okul_adi')
                    ->label('Üniversite')
                    ->searchable(),
                Tables\Columns\TextColumn::make('okul_bolumu')
                    ->label('Bölüm')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sinif')
                    ->label('Sınıf')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Evraklar Onaylı')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı')
                    ->boolean()
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
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'burs_havuzu' => 'Burs Havuzu',
                        'on_kabul' => 'Ön Kabul',
                        'red_edildi' => 'Reddedildi',
                        'evrak_bekleniyor' => 'Evrak Bekleniyor',
                        'evrak_incelemede' => 'Evrak İncelemede',
                        'mulakat_havuzu' => 'Mülakat Havuzu',
                        'mulakat_planlandi' => 'Mülakat Planlandı',
                        'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                        'kabul_edildi' => 'Kabul Edildi',
                        'kesin_kabul' => 'Kesin Kabul',
                        'onceki_burslu' => 'Önceki Burslu',
                    ]),
                Tables\Filters\SelectFilter::make('program')
                    ->label('Program')
                    ->relationship('program', 'name'),
                Tables\Filters\Filter::make('documents_approved')
                    ->label('Evrakları Onaylı')
                    ->query(fn (Builder $query): Builder => $query->where('are_documents_approved', true))
                    ->toggle(),
                Tables\Filters\Filter::make('interview_completed')
                    ->label('Mülakatı Tamamlanmış')
                    ->query(fn (Builder $query): Builder => $query->where('is_interview_completed', true))
                    ->toggle(),
                Tables\Filters\Filter::make('recent_applications')
                    ->label('Son Başvurular')
                    ->query(fn (Builder $query): Builder => $query->where('application_date', '>=', now()->subDays(30)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\Action::make('review')
                    ->label('Değerlendir')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn (Applications $record): string => route('filament.admin.resources.applications.edit', ['record' => $record, 'reviewing' => true])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Sil'),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label('Durumu Güncelle')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Durum')
                                ->options([
                                    'burs_havuzu' => 'Burs Havuzu',
                                    'on_kabul' => 'Ön Kabul',
                                    'red_edildi' => 'Reddedildi',
                                    'evrak_bekleniyor' => 'Evrak Bekleniyor',
                                    'evrak_incelemede' => 'Evrak İncelemede',
                                    'mulakat_havuzu' => 'Mülakat Havuzu',
                                    'mulakat_planlandi' => 'Mülakat Planlandı',
                                    'mulakat_tamamlandi' => 'Mülakat Tamamlandı',
                                    'kabul_edildi' => 'Kabul Edildi',
                                    'kesin_kabul' => 'Kesin Kabul',
                                    'onceki_burslu' => 'Önceki Burslu',
                                ])
                                ->required(),
                        ])
                        ->action(function (array $data, Collection $records): void {
                            foreach ($records as $record) {
                                $record->update([
                                    'status' => $data['status'],
                                ]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::make(),
            RelationManagers\InterviewsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplications::route('/create'),
            'view' => Pages\ViewApplications::route('/{record}'),
            'edit' => Pages\EditApplications::route('/{record}/edit'),
        ];
    }
}

