<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\ApplicationsResource\Pages;
use App\Filament\User\Resources\ApplicationsResource\RelationManagers;
use App\Models\Applications;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class ApplicationsResource extends Resource
{
    protected static ?string $model = Applications::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Başvurular';
        
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Burs İşlemleri';

    protected static ?string $title = 'Başvuru Oluştur';

    protected static ?string $breadcrumb = 'Başvuru Oluştur';

    protected static ?string $breadcrumbParent = 'Başvurular';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Personal Information / Kişisel Bilgiler
                Forms\Components\Section::make('Kişisel Bilgiler')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Fotoğraf')
                            ->placeholder('Fotoğrafınızı yükleyin')
                            ->image()
                            ->columnSpanFull()
                            ->directory('applications/images')
                            ->required(),
                        Forms\Components\TextInput::make('national_id')
                            ->label('T.C. Kimlik No')
                            ->placeholder('T.C. Kimlik Numaranız')
                            ->required()
                            ->maxLength(11),
                        Forms\Components\TextInput::make('name')
                            ->label('Ad')
                            ->placeholder('Adınız')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('surname')
                            ->label('Soyad')
                            ->placeholder('Soyadınız')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Doğum Tarihi')
                            ->placeholder('Doğum Tarihiniz')
                            ->required(),
                        Forms\Components\TextInput::make('birth_place')
                            ->label('Doğum Yeri')
                            ->placeholder('Doğum Yeriniz')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Uyruk')
                            ->placeholder('Uyruğunuz')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('Cinsiyet')
                            ->placeholder('Cinsiyetinizi seçin')
                            ->required()
                            ->options([
                                'Male' => 'Erkek',
                                'Female' => 'Kadın',
                                'Other' => 'Diğer'
                            ]),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->placeholder('Telefon numaranız')
                            ->required()
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\TextInput::make('email')
                            ->label('E-posta')
                            ->placeholder('E-posta adresiniz')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label('Adres')
                            ->required()
                            ->placeholder('Adresiniz')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('city')
                            ->label('Şehir')
                            ->required()
                            ->placeholder('Şehir')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('postal_code')
                            ->label('Posta Kodu')
                            ->required()
                            ->placeholder('Posta kodunuz')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('physical_disability')
                            ->label('Fiziksel Engeliniz')
                            ->placeholder('Fiziksel engeliniz varsa açıklayınız')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('disability_description')
                            ->label('Engel Açıklaması')
                            ->placeholder('Engel durumunuzu detaylı açıklayınız')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('disability_status')
                            ->label('Engel Durumu')
                            ->placeholder('Engel durumunuzu seçin')
                            ->options([
                                'None' => 'Engel Yok',
                                'Mild' => 'Hafif',
                                'Moderate' => 'Orta',
                                'Severe' => 'Ağır'
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(false)
                    ->columns(2)
                    ->columnSpanFull(),
                    
                // Registration Information / Kayıt Bilgileri
                Forms\Components\Section::make('Kayıt Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('registered_province')
                            ->label('Nüfusa Kayıtlı İl')
                            ->required()
                            ->placeholder('Nüfusa kayıtlı olduğunuz il')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('registered_district')
                            ->label('Nüfusa Kayıtlı İlçe')
                            ->required()
                            ->placeholder('Nüfusa kayıtlı olduğunuz ilçe')
                            ->maxLength(255),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->columns(2)
                    ->columnSpanFull(),
                
                // Education Information / Eğitim Bilgileri
                Forms\Components\Section::make('Eğitim Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('school_name')
                            ->label('Okul Adı')
                            ->required()
                            ->placeholder('Okuduğunuz okulun adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('school_department')
                            ->label('Bölüm')
                            ->required()
                            ->placeholder('Okuduğunuz bölüm')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('grade')
                            ->label('Sınıf')
                            ->required()
                            ->placeholder('Kaçıncı sınıfta olduğunuz')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('enrollment_year')
                            ->label('Giriş Yılı')
                            ->placeholder('Üniversiteye giriş yılınız')
                            ->numeric(),
                        Forms\Components\TextInput::make('scholarship_rate')
                            ->label('Burs Oranı')
                            ->placeholder('Mevcut burs oranınız (yoksa boş bırakın)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('university_entrance_score')
                            ->label('Üniversite Giriş Puanı')
                            ->placeholder('Üniversite giriş puanınız')
                            ->numeric(),
                        Forms\Components\TextInput::make('student_id')
                            ->label('Öğrenci Numarası')
                            ->placeholder('Öğrenci numaranız')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('primary_school_name')
                            ->label('İlkokul Adı')
                            ->placeholder('Mezun olduğunuz ilkokulun adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('primary_school_graduation_year')
                            ->label('İlkokul Mezuniyet Yılı')
                            ->placeholder('İlkokuldan mezun olduğunuz yıl')
                            ->numeric(),
                        Forms\Components\TextInput::make('high_school_name')
                            ->label('Lise Adı')
                            ->placeholder('Mezun olduğunuz lisenin adı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('high_school_graduation_year')
                            ->label('Lise Mezuniyet Yılı')
                            ->placeholder('Liseden mezun olduğunuz yıl')
                            ->numeric(),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->columns(2)
                    ->columnSpanFull(),
                
                // Family Information / Aile Bilgileri
                Forms\Components\Section::make('Aile Bilgileri')
                    ->schema([
                        // Father Info / Baba Bilgileri
                        Forms\Components\Section::make('Baba Bilgileri')
                            ->schema([
                                Forms\Components\TextInput::make('father_name')
                                    ->label('Baba Adı')
                                    ->placeholder('Babanızın adı')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('father_surname')
                                    ->label('Baba Soyadı')
                                    ->placeholder('Babanızın soyadı')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('father_birth_year')
                                    ->label('Baba Doğum Yılı')
                                    ->placeholder('Babanızın doğum yılı')
                                    ->numeric(),
                                Forms\Components\TextInput::make('father_birth_place')
                                    ->label('Baba Doğum Yeri')
                                    ->placeholder('Babanızın doğum yeri')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('father_occupation')
                                    ->label('Baba Mesleği')
                                    ->placeholder('Babanızın mesleği')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('father_death_year')
                                    ->label('Baba Vefat Yılı')
                                    ->placeholder('Vefat ettiyse yılı (yoksa boş bırakın)')
                                    ->numeric(),
                                Forms\Components\TextInput::make('father_monthly_income')
                                    ->label('Baba Aylık Net Gelir')
                                    ->placeholder('Babanızın aylık net geliri (TL)')
                                    ->numeric(),
                            ])->columns(2),
                            
                        // Mother Info / Anne Bilgileri
                        Forms\Components\Section::make('Anne Bilgileri')
                            ->schema([
                                Forms\Components\TextInput::make('mother_name')
                                    ->label('Anne Adı')
                                    ->placeholder('Annenizin adı')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mother_surname')
                                    ->label('Anne Soyadı')
                                    ->placeholder('Annenizin soyadı')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mother_birth_year')
                                    ->label('Anne Doğum Yılı')
                                    ->placeholder('Annenizin doğum yılı')
                                    ->numeric(),
                                Forms\Components\TextInput::make('mother_birth_place')
                                    ->label('Anne Doğum Yeri')
                                    ->placeholder('Annenizin doğum yeri')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mother_occupation')
                                    ->label('Anne Mesleği')
                                    ->placeholder('Annenizin mesleği')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('mother_death_year')
                                    ->label('Anne Vefat Yılı')
                                    ->placeholder('Vefat ettiyse yılı (yoksa boş bırakın)')
                                    ->numeric(),
                                Forms\Components\TextInput::make('mother_monthly_income')
                                    ->label('Anne Aylık Net Gelir')
                                    ->placeholder('Annenizin aylık net geliri (TL)')
                                    ->numeric(),
                            ])->columns(2),
                            
                        // Siblings Info / Kardeş Bilgileri
                        Forms\Components\Section::make('Kardeş Bilgileri')
                            ->schema([
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sibling1_name')
                                            ->label('1. Kardeş Adı')
                                            ->placeholder('1. kardeşinizin adı')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling1_surname')
                                            ->label('1. Kardeş Soyadı')
                                            ->placeholder('1. kardeşinizin soyadı')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling1_age')
                                            ->label('1. Kardeş Yaşı')
                                            ->placeholder('1. kardeşinizin yaşı')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('sibling1_education')
                                            ->label('1. Kardeş Eğitim Durumu')
                                            ->placeholder('1. kardeşinizin eğitim durumu')
                                            ->maxLength(255),
                                    ])->columns(4),

                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sibling2_name')
                                            ->label('2. Kardeş Adı')
                                            ->placeholder('2. kardeşinizin adı (yoksa boş bırakın)')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling2_surname')
                                            ->label('2. Kardeş Soyadı')
                                            ->placeholder('2. kardeşinizin soyadı')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling2_age')
                                            ->label('2. Kardeş Yaşı')
                                            ->placeholder('2. kardeşinizin yaşı')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('sibling2_education')
                                            ->label('2. Kardeş Eğitim Durumu')
                                            ->placeholder('2. kardeşinizin eğitim durumu')
                                            ->maxLength(255),
                                    ])->columns(4),
                                    
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sibling3_name')
                                            ->label('3. Kardeş Adı')
                                            ->placeholder('3. kardeşinizin adı (yoksa boş bırakın)')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling3_surname')
                                            ->label('3. Kardeş Soyadı')
                                            ->placeholder('3. kardeşinizin soyadı')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('sibling3_age')
                                            ->label('3. Kardeş Yaşı')
                                            ->placeholder('3. kardeşinizin yaşı')
                                            ->numeric(),
                                        Forms\Components\TextInput::make('sibling3_education')
                                            ->label('3. Kardeş Eğitim Durumu')
                                            ->placeholder('3. kardeşinizin eğitim durumu')
                                            ->maxLength(255),
                                    ])->columns(4),
                                    
                                Forms\Components\Grid::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sibling_monthly_income')
                                            ->label('Kardeşlerin Toplam Aylık Geliri')
                                            ->placeholder('Kardeşlerin toplam aylık geliri (TL)')
                                            ->numeric(),
                                    ])->columns(1),
                            ]),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->columnSpanFull(),
                  

                // Residence Information / İkamet Bilgileri
                Forms\Components\Section::make('İkamet Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('family_head_dependent_count')
                            ->label('Aile Reisinin Baktığı Kişi Sayısı')
                            ->placeholder('Aile reisinin bakmakla yükümlü olduğu kişi sayısı')
                            ->numeric(),
                        Forms\Components\TextInput::make('family_subsistence_responsibility')
                            ->label('Ailenin Geçim Sorumluluğu')
                            ->placeholder('Ailenin geçimini kim sağlıyor?')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('family_residence_address')
                            ->label('Aile İkametgah Adresi')
                            ->placeholder('Ailenizin ikamet ettiği adres')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('residence_province')
                            ->label('İkametgah İli')
                            ->placeholder('İkamet edilen il')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('residence_district')
                            ->label('İkametgah İlçesi')
                            ->placeholder('İkamet edilen ilçe')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('family_phone')
                            ->label('Aile Telefon')
                            ->placeholder('Ailenizin telefon numarası')
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\Select::make('resides_with_family')
                            ->label('Ailenin Yanında Kalarak Okula Devam')
                            ->placeholder('Ailenizle birlikte mi yaşıyorsunuz?')
                            ->options([
                                'Yes' => 'Evet',
                                'No' => 'Hayır'
                            ]),
                        Forms\Components\TextInput::make('dormitory_monthly_payment')
                            ->label('Yurtta Kalıyorsa Aylık Ödeme')
                            ->placeholder('Yurtta kalıyorsanız aylık ödediğiniz tutar (TL)')
                            ->numeric(),
                        Forms\Components\Textarea::make('education_residence_address')
                            ->label('Öğrenim Sırasında Kaldığı Adres')
                            ->placeholder('Öğrenim sırasında kaldığınız adres')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('education_province')
                            ->label('Öğrenim İli')
                            ->placeholder('Öğrenim gördüğünüz il')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('education_district')
                            ->label('Öğrenim İlçesi')
                            ->placeholder('Öğrenim gördüğünüz ilçe')
                            ->maxLength(255),
                    ])
                    ->collapsible()
                    ->collapsed(true)
                    ->columns(2)
                    ->columnSpanFull(),
                
                // Financial Information / Mali Bilgiler
                Forms\Components\Section::make('Mali Bilgiler')
                    ->schema([
                        Forms\Components\TextInput::make('spouse_monthly_income')
                            ->label('Eş Aylık Net Gelir')
                            ->placeholder('Evliyseniz eşinizin aylık net geliri (TL)')
                            ->numeric(),
                        Forms\Components\TextInput::make('death_benefit_annual_income')
                            ->label('Vefat ile Bağlı Maaş Yıllık Net Gelir')
                            ->placeholder('Vefat ile bağlanan yıllık net maaş geliri (TL)')
                            ->numeric(),
                        Forms\Components\Select::make('family_owns_house')
                            ->label('Ailenin Evi Var mı?')
                            ->placeholder('Ailenize ait ev var mı?')
                            ->options([
                                'Yes' => 'Evet',
                                'No' => 'Hayır'
                            ]),
                        Forms\Components\TextInput::make('rent_payment_amount')
                            ->label('Kirada Oturuyor İse Kira Miktarı')
                            ->placeholder('Kirada oturuyorsanız aylık kira miktarı (TL)')
                            ->numeric(),
                        Forms\Components\Textarea::make('real_estate_value_and_income')
                            ->label('Gayrimenkul Değeri ve Geliri')
                            ->placeholder('Sahip olduğunuz gayrimenkullerin değeri ve geliri')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('car_model_year')
                            ->label('Otomobil Model Yılı')
                            ->placeholder('Arabanız varsa model yılı (yoksa boş bırakın)')
                            ->numeric(),
                        Forms\Components\TextInput::make('other_income_amount')
                            ->label('Başka Gelir Miktarı')
                            ->placeholder('Varsa diğer gelir miktarınız (TL)')
                            ->numeric(),
                    ]),
                
                // Other Information / Diğer Bilgiler
                Forms\Components\Section::make('Diğer Bilgiler')
                    ->schema([
                        Forms\Components\Textarea::make('field_selection')
                            ->label('Branş Seçimi')
                            ->placeholder('İlgilendiğiniz branşlar')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('club_membership')
                            ->label('Kulüp Üyeliği')
                            ->placeholder('Üye olduğunuz kulüpler')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('library_usage')
                            ->label('Kütüphane Kullanımı')
                            ->placeholder('Kütüphane kullanım alışkanlıklarınız')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hobby')
                            ->label('Hobi')
                            ->placeholder('Hobileriniz')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('scholarship_commitment')
                            ->label('Burs Verme Sözü')
                            ->placeholder('İleride başka öğrencilere burs vermeyi düşünür müsünüz?')
                            ->options([
                                'Yes' => 'Evet',
                                'No' => 'Hayır'
                            ]),
                        Forms\Components\Textarea::make('social_media_usage')
                            ->label('Sosyal Medya Kullanımı')
                            ->placeholder('Sosyal medya kullanım alışkanlıklarınız')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('social_responsibility_project')
                            ->label('Sosyal Sorumluluk Projesi')
                            ->placeholder('Katıldığınız sosyal sorumluluk projeleri')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('professional_success_opinion')
                            ->label('İş Hayatında Başarı Görüşü')
                            ->placeholder('İş hayatında başarılı olmak için neler gerektiği hakkındaki görüşleriniz')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('post_graduation_goal')
                            ->label('Mezuniyet Sonrası Hedef')
                            ->placeholder('Mezuniyet sonrası hedefleriniz')
                            ->columnSpanFull(),
                    ]),
                
                // Reference Information / Referans Bilgileri
                Forms\Components\Section::make('Referans Bilgileri')
                    ->schema([
                        Forms\Components\TextInput::make('reference1_name')
                            ->label('1. Referans Adı')
                            ->placeholder('1. referansınızın adı soyadı')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('reference1_phone')
                            ->label('1. Referans Telefon')
                            ->placeholder('1. referansınızın telefon numarası')
                            ->tel()
                            ->maxLength(15),
                        Forms\Components\TextInput::make('reference2_name')
                            ->label('2. Referans Adı')
                            ->placeholder('2. referansınızın adı soyadı (varsa)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('reference2_phone')
                            ->label('2. Referans Telefon')
                            ->placeholder('2. referansınızın telefon numarası (varsa)')
                            ->tel()
                            ->maxLength(15),
                    ]),
                
                // Scholarship Information / Burs Bilgileri
                Forms\Components\Section::make('Burs Bilgileri')
                    ->schema([
                        Forms\Components\Select::make('receiving_other_scholarship')
                            ->label('Başka Burs/Kredi Alımı')
                            ->placeholder('Başka bir yerden burs/kredi alıyor musunuz?')
                            ->options([
                                'Yes' => 'Evet',
                                'No' => 'Hayır'
                            ]),
                        Forms\Components\TextInput::make('other_scholarship_institution')
                            ->label('Başka Burs/Kredi Kurumu')
                            ->placeholder('Burs/kredi aldığınız kurum (varsa)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('iban')
                            ->label('IBAN')
                            ->placeholder('Banka IBAN numaranız')
                            ->maxLength(255),
                    ]),
                    
                // Notes / Notlar
                Forms\Components\Textarea::make('notes')
                    ->label('Notlar')
                    ->placeholder('Eklemek istediğiniz notlar')
                    ->columnSpanFull(),
            ])
            ->collapsible()
            ->collapsed(true)
            ->statePath('data')
            ->model(Applications::class);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('application_id')
                    ->label('Başvuru No')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Başvuran Adı')
                    ->formatStateUsing(fn ($state, $record) => $record->user ? $record->user->name . ' ' . $record->user->surname : 'Kullanıcı Bulunamadı')
                    ->description(fn ($record) => $record->user ? $record->user->email : '')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Fotoğraf'),
                    
                Tables\Columns\TextColumn::make('application_date')
                    ->label('Başvuru Tarihi')
                    ->date('d.m.Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
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
                    ->colors([
                        'warning' => 'awaiting_evaluation',
                        'primary' => ['scholarship_pool', 'pre_approved', 'awaiting_documents'],
                        'secondary' => ['documents_under_review', 'interview_pool', 'interview_scheduled'],
                        'success' => ['interview_completed', 'accepted', 'final_acceptance', 'previous_scholar'],
                        'danger' => 'rejected',
                    ]),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Ad')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('surname')
                    ->label('Soyad')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('school_name')
                    ->label('Okul')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('school_department')
                    ->label('Bölüm')
                    ->searchable(),
                    
                Tables\Columns\IconColumn::make('are_documents_approved')
                    ->label('Evraklar Onaylı')
                    ->boolean(),
                    
                Tables\Columns\IconColumn::make('is_interview_completed')
                    ->label('Mülakat Tamamlandı')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Düzenle'),
                Tables\Actions\ViewAction::make()
                    ->label('Görüntüle'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Seçilenleri Sil'),
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
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplications::route('/create'),
            'edit' => Pages\EditApplications::route('/{record}/edit'),
        ];
    }
}
