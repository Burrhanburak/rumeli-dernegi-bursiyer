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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;
use App\Models\User;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use App\Models\ScholarshipProgram;
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
->description('Başvuru sahibinin kişisel bilgilerini girin')
->icon('heroicon-o-user')
->schema([
    // Önce Burs Programı seçimi
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\Select::make('program_id')
                ->relationship('program', 'name')
                ->label('Burs Programı')
                ->placeholder('Başvurmak istediğiniz burs programını seçin')
                ->options(ScholarshipProgram::all()->pluck('name', 'id'))
                ->preload()
                ->required()
                ->searchable(),
        ])->columns(1),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\FileUpload::make('image')
                ->label('Fotoğraf')
                ->placeholder('Fotoğrafınızı yükleyin')
                ->image()
                ->imageEditor()
                ->directory('applications/images')
                ->required()
                ->default(function () {
                    if (Auth::user()->image) {
                        // Just use the path without Storage::url()
                        return Auth::user()->image;
                    }
                    return null;
                }),
        ])->columns(1),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\TextInput::make('national_id')
                ->label('T.C. Kimlik No')
                ->placeholder('T.C. Kimlik Numaranız')
                ->required()
                ->numeric()
                ->length(11)
                ->default(Auth::user()->national_id),
                
            Forms\Components\TextInput::make('name')
                ->label('Ad')
                ->placeholder('Adınız')
                ->required()
                ->maxLength(255)
                ->default(Auth::user()->name),
                
            Forms\Components\TextInput::make('surname')
                ->label('Soyad')
                ->placeholder('Soyadınız')
                ->required()
                ->maxLength(255)
                ->default(Auth::user()->surname),
        ])->columns(3),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\DatePicker::make('birth_date')
                ->label('Doğum Tarihi')
                ->native(false)
                ->displayFormat('d/m/Y')
                ->placeholder('Doğum Tarihiniz')
                ->required()
                ->default(Auth::user()->birth_date),
                
            Forms\Components\TextInput::make('birth_place')
                ->label('Doğum Yeri')
                ->placeholder('Doğum Yeriniz')
                ->required()
                ->maxLength(255),
                
            Forms\Components\TextInput::make('nationality')
                ->label('Uyruk')
                ->placeholder('Uyruğunuz')
                ->required()
                ->default('T.C')
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
        ])->columns(4),
        
    Forms\Components\Grid::make()
        ->schema([
                 
            PhoneInput::make('phone')
            ->defaultCountry('tr')
            ->initialCountry('tr')
            ->placeholder('Telefon numaranızı giriniz')
            ->locale('tr')
            ->countrySearch(false)
            ->label('Telefon Numarası')
            ->required()
            ->unique(ignoreRecord: true, column: 'phone')
            ->default(Auth::user()->phone),
         
                
            Forms\Components\TextInput::make('email')
                ->label('E-posta')
                ->placeholder('E-posta adresiniz')
                ->required()
                ->email()
                ->maxLength(255)
                ->default(Auth::user()->email),
        ])->columns(2),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\Textarea::make('address')
                ->label('Adres')
                ->required()
                ->placeholder('Adresiniz')
                ->rows(2)
                ->default(Auth::user()->address),
        ])->columns(1),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\TextInput::make('city')
                ->label('Şehir')
                ->required()
                ->placeholder('Şehir')
                ->maxLength(255)
                ->default(Auth::user()->city),
                
            Forms\Components\TextInput::make('postal_code')
                ->label('Posta Kodu')
                ->required()
                ->placeholder('Posta kodunuz')
                ->maxLength(255)
                ->default(Auth::user()->postal_code),
        ])->columns(2),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\Select::make('disability_status')
                ->label('Engel Durumu')
                ->placeholder('Engel durumunuzu seçin')
                ->options([
                    'None' => 'Engel Yok',
                    'Mild' => 'Hafif',
                    'Moderate' => 'Orta',
                    'Severe' => 'Ağır'
                ])
                ->live()
                ->afterStateUpdated(function ($state, $set) {
                    if ($state === 'None') {
                        $set('physical_disability', null);
                        $set('disability_description', null);
                    }
                }),
        ])->columns(1),
        
    Forms\Components\Grid::make()
        ->schema([
            Forms\Components\Textarea::make('physical_disability')
                ->label('Fiziksel Engeliniz')
                ->placeholder('Fiziksel engeliniz varsa açıklayınız')
                ->rows(2),
                
            Forms\Components\Textarea::make('disability_description')
                ->label('Engel Açıklaması')
                ->placeholder('Engel durumunuzu detaylı açıklayınız')
                ->rows(2)
        ])->columns(1),
])
->collapsible()
->collapsed(false),
                    
                // Registration Information / Kayıt Bilgileri
                Forms\Components\Section::make('Kayıt Bilgileri')
                ->description('Nüfus kayıt bilgilerinizi girin')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\Grid::make()
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
                        ])->columns(2),
                ])
                ->collapsible()
                ->collapsed(true),
                
                // Education Information / Eğitim Bilgileri
                Forms\Components\Section::make('Eğitim Bilgileri')
                ->description('Eğitim durumunuz ile ilgili bilgileri girin')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    Forms\Components\Grid::make()
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
                        ])->columns(2),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('grade')
                                ->label('Sınıf')
                                ->required()
                                
                                ->placeholder('Kaçıncı sınıfta olduğunuz')
                                ->maxLength(255),
                                
                            Forms\Components\TextInput::make('enrollment_year')
                                ->label('Giriş Yılı')
                                ->placeholder('Üniversiteye giriş yılınız')
                                ->numeric(),
                                
                            Forms\Components\TextInput::make('student_id')
                                ->label('Öğrenci Numarası')
                                ->placeholder('Öğrenci numaranız')
                                ->maxLength(255),
                        ])->columns(3),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('scholarship_rate')
                                ->label('Burs Oranı')
                                ->placeholder('Mevcut burs oranınız (yoksa boş bırakın)')
                                ->suffix('%')
                                ->maxLength(255),
                                
                            Forms\Components\TextInput::make('university_entrance_score')
                                ->label('Üniversite Giriş Puanı')
                                ->maxvalue(500)
                                ->placeholder('Üniversite giriş puanınız')
                                ->numeric(),
                        ])->columns(2),
                        
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('primary_school_name')
                                        ->label('İlkokul Adı')
                                        ->placeholder('Mezun olduğunuz ilkokulun adı')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('primary_school_graduation_year')
                                        ->label('İlkokul Mezuniyet Yılı')
                                        ->placeholder('İlkokuldan mezun olduğunuz yıl')
                                        ->numeric()
                                        ->nullable(),
                                ])->columns(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('high_school_name')
                                        ->label('Lise Adı')
                                        ->placeholder('Mezun olduğunuz lisenin adı')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('high_school_graduation_year')
                                        ->label('Lise Mezuniyet Yılı')
                                        ->placeholder('Liseden mezun olduğunuz yıl')
                                        ->numeric()
                                        ->nullable(),
                                ])->columns(2),
                        ])
                        ->columns(1)
                        ->heading('Mezuniyet Bilgileri'),
                ])
                ->collapsible()
                ->collapsed(true),
                
                // Family Information / Aile Bilgileri
                Forms\Components\Section::make('Aile Bilgileri')
                ->description('Aileniz hakkında bilgileri girin')
                ->icon('heroicon-o-users')
                ->schema([
                    // Father Info / Baba Bilgileri
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('father_name')
                                        ->label('Baba Adı')
                                        ->placeholder('Babanızın adı')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('father_surname')
                                        ->label('Baba Soyadı')
                                        ->placeholder('Babanızın soyadı')
                                        ->maxLength(255),
                                ])->columns(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('father_birth_year')
                                        ->label('Baba Doğum Yılı')
                                        ->placeholder('Babanızın doğum yılı')
                                        ->numeric()
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('father_birth_place')
                                        ->label('Baba Doğum Yeri')
                                        ->placeholder('Babanızın doğum yeri')
                                        ->maxLength(255),
                                ])->columns(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('father_occupation')
                                        ->label('Baba Mesleği')
                                        ->placeholder('Babanızın mesleği')
                                        ->maxLength(255),
                                        
                                        TextInput::make('father_monthly_income')
                                        ->label('Baba Aylık Net Gelir')
                                        ->placeholder('Babanızın aylık net geliri')
                                        ->prefix('₺')
                                        ->maxValue(42949672.95)
                                        ->numeric(),
                                        
                                    Forms\Components\TextInput::make('father_death_year')
                                        ->label('Baba Vefat Yılı')
                                        ->placeholder('Vefat ettiyse yılı (yoksa boş bırakın)')
                                        ->numeric()
                                        ->nullable(),
                                ])->columns(3),
                        ])
                        ->columns(1)
                        ->heading('Baba Bilgileri'),
                        
                    // Mother Info / Anne Bilgileri
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('mother_name')
                                        ->label('Anne Adı')
                                        ->placeholder('Annenizin adı')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('mother_surname')
                                        ->label('Anne Soyadı')
                                        ->placeholder('Annenizin soyadı')
                                        ->maxLength(255),
                                ])->columns(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('mother_birth_year')
                                        ->label('Anne Doğum Yılı')
                                        ->placeholder('Annenizin doğum yılı')
                                        ->numeric()
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('mother_birth_place')
                                        ->label('Anne Doğum Yeri')
                                        ->placeholder('Annenizin doğum yeri')
                                        ->maxLength(255),
                                ])->columns(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('mother_occupation')
                                        ->label('Anne Mesleği')
                                        ->placeholder('Annenizin mesleği')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('mother_monthly_income')
                                        ->label('Anne Aylık Net Gelir')
                                        ->placeholder('Annenizin aylık net geliri')
                                        ->prefix('₺')
                                        ->numeric()
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('mother_death_year')
                                        ->label('Anne Vefat Yılı')
                                        ->placeholder('Vefat ettiyse yılı (yoksa boş bırakın)')
                                        ->numeric()
                                        ->nullable(),
                                ])->columns(3),
                        ])
                        ->columns(1)
                        ->heading('Anne Bilgileri'),
                        
                    // Siblings Info / Kardeş Bilgileri
                    Forms\Components\Card::make()
                        ->schema([
                            // First sibling
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
                                        ->numeric()
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('sibling1_education')
                                        ->label('1. Kardeş Eğitim Durumu')
                                        ->placeholder('1. kardeşinizin eğitim durumu')
                                        ->maxLength(255),
                                ])->columns(4),

                            // Second sibling
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
                                        ->numeric()
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('sibling2_education')
                                        ->label('2. Kardeş Eğitim Durumu')
                                        ->placeholder('2. kardeşinizin eğitim durumu')
                                        ->maxLength(255),
                                ])->columns(4),
                                
                            // Third sibling
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
                                        ->numeric() 
                                        ->nullable(),
                                        
                                    Forms\Components\TextInput::make('sibling3_education')
                                        ->label('3. Kardeş Eğitim Durumu')
                                        ->placeholder('3. kardeşinizin eğitim durumu')
                                        ->maxLength(255),
                                ])->columns(4),
                                
                            // Total sibling income
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('sibling_monthly_income')
                                        ->label('Kardeşlerin Toplam Aylık Geliri')
                                        ->placeholder('Kardeşlerin toplam aylık geliri')
                                        ->prefix('₺')
                                        ->numeric()
                                        ->nullable(),
                                ])->columns(1),
                        ])
                        ->columns(1)
                        ->heading('Kardeş Bilgileri'),
                ])
                ->collapsible()
                ->collapsed(true),

                // Residence Information / İkamet Bilgileri
                Forms\Components\Section::make('İkamet Bilgileri')
                ->description('İkamet ettiğiniz yer hakkında bilgileri girin')
                ->icon('heroicon-o-home')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('family_head_dependent_count')
                                ->label('Aile Reisinin Baktığı Kişi Sayısı')
                                ->placeholder('Aile reisinin bakmakla yükümlü olduğu kişi sayısı')
                                ->numeric(),
                                
                            Forms\Components\TextInput::make('family_subsistence_responsibility')
                                ->label('Ailenin Geçim Sorumluluğu')
                                ->placeholder('Ailenin geçimini kim sağlıyor?')
                                ->maxLength(255),
                        ])->columns(2),
                        
                    // Aile İkamet Bilgileri
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Textarea::make('family_residence_address')
                                ->label('Aile İkametgah Adresi')
                                ->placeholder('Ailenizin ikamet ettiği adres')
                                ->rows(2),
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('residence_province')
                                        ->label('İkametgah İli')
                                        ->placeholder('İkamet edilen il')
                                        ->maxLength(255),
                                        
                                    Forms\Components\TextInput::make('residence_district')
                                        ->label('İkametgah İlçesi')
                                        ->placeholder('İkamet edilen ilçe')
                                        ->maxLength(255),
                                        
                                       
                                        PhoneInput::make('family_phone')
                                        ->label('Aile Telefon')
                                        ->placeholder('Ailenizin telefon numarası')
                                        ->defaultCountry('tr')
                                        ->initialCountry('tr')
                                ])->columns(3),
                        ])
                        ->columns(1)
                        ->heading('Aile İkamet Bilgileri'),
                        
                    // Öğrenim İkamet Bilgileri
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\Select::make('resides_with_family')
                                        ->label('Ailenin Yanında Kalarak Okula Devam')
                                        ->placeholder('Ailenizle birlikte mi yaşıyorsunuz?')
                                        ->options([
                                            'Yes' => 'Evet',
                                            'No' => 'Hayır'
                                        ])
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state === 'Yes') {
                                                $set('dormitory_monthly_payment', null);
                                                $set('education_residence_address', null);
                                                $set('education_province', null);
                                                $set('education_district', null);
                                            }
                                        }),
                                        
                                    Forms\Components\TextInput::make('dormitory_monthly_payment')
                                        ->label('Yurtta Kalıyorsa Aylık Ödeme')
                                        ->placeholder('Yurtta kalıyorsanız aylık ödediğiniz tutar')
                                        ->prefix('₺')
                                        ->numeric()
                                        ->nullable()
                                        
                                ])->columns(2),
                                
                            Forms\Components\Textarea::make('education_residence_address')
                                ->label('Öğrenim Sırasında Kaldığı Adres')
                                ->placeholder('Öğrenim sırasında kaldığınız adres')
                                ->rows(2),
                                
                                
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\TextInput::make('education_province')
                                        ->label('Öğrenim İli')
                                        ->placeholder('Öğrenim gördüğünüz il')
                                        ->maxLength(255),
                                       
                                        
                                    Forms\Components\TextInput::make('education_district')
                                        ->label('Öğrenim İlçesi')
                                        ->placeholder('Öğrenim gördüğünüz ilçe')
                                        ->maxLength(255),
                                        
                                ])->columns(2),
                        ])
                        ->columns(1)
                        ->heading('Öğrenim Sırasında İkamet Bilgileri'),
                ])
                ->collapsible()
                ->collapsed(true),
                
                // Financial Information / Mali Bilgiler
                Forms\Components\Section::make('Mali Bilgiler')
                ->description('Mali durumunuz hakkında bilgileri girin')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('spouse_monthly_income')
                                ->label('Eş Aylık Net Gelir')
                                ->placeholder('Evliyseniz eşinizin aylık net geliri')
                                ->prefix('₺')
                                ->numeric()
                                ->nullable(),
                                
                            Forms\Components\TextInput::make('death_benefit_annual_income')
                                ->label('Vefat ile Bağlı Maaş Yıllık Net Gelir')
                                ->placeholder('Vefat ile bağlanan yıllık net maaş geliri')
                                ->prefix('₺')
                                ->numeric()
                                ->nullable(),
                        ])->columns(2),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('family_owns_house')
                                ->label('Ailenin Evi Var mı?')
                                ->placeholder('Ailenize ait ev var mı?')
                                ->options([
                                    'Yes' => 'Evet',
                                    'No' => 'Hayır'
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state === 'Yes') {
                                        $set('rent_payment_amount', null);
                                    }
                                }),
                                
                            Forms\Components\TextInput::make('rent_payment_amount')
                                ->label('Kirada Oturuyor İse Kira Miktarı')
                                ->placeholder('Kirada oturuyorsanız aylık kira miktarı')
                                ->prefix('₺')
                                ->numeric()
                                ->nullable(),
                                
                        ])->columns(2),
                        
                    Forms\Components\Textarea::make('real_estate_value_and_income')
                        ->label('Gayrimenkul Değeri ve Geliri')
                        ->placeholder('Sahip olduğunuz gayrimenkullerin değeri ve geliri')
                        ->rows(2),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('car_model_year')
                                ->label('Otomobil Model Yılı')
                                ->placeholder('Arabanız varsa model yılı (yoksa boş bırakın)')
                                ->numeric()
                                ->nullable(),
                                
                            Forms\Components\TextInput::make('other_income_amount')
                                ->label('Başka Gelir Miktarı')
                                ->placeholder('Varsa diğer gelir miktarınız')
                                ->prefix('₺')
                                ->numeric()
                                ->nullable(),
                        ])->columns(2),
                ])
                ->collapsible()
                ->collapsed(true),
                
                // Other Information / Diğer Bilgiler
                Forms\Components\Section::make('Diğer Bilgiler')
                ->description('Kişisel gelişim ve ilgi alanlarınız hakkında bilgileri girin')
                ->icon('heroicon-o-puzzle-piece')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('field_selection')
                                ->label('Branş Seçimi')
                                ->placeholder('İlgilendiğiniz branşlar')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('club_membership')
                                ->label('Kulüp Üyeliği')
                                ->placeholder('Üye olduğunuz kulüpler')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('library_usage')
                                ->label('Kütüphane Kullanımı')
                                ->placeholder('Kütüphane kullanım alışkanlıklarınız')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('hobby')
                                ->label('Hobi')
                                ->placeholder('Hobileriniz')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('scholarship_commitment')
                                ->label('Burs Verme Sözü')
                                ->placeholder('İleride başka öğrencilere burs vermeyi düşünür müsünüz?')
                                ->options([
                                    'Yes' => 'Evet',
                                    'No' => 'Hayır'
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state === 'No') {
                                        $set('scholarship_commitment_reason', null);
                                    }
                                }),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('social_media_usage')
                                ->label('Sosyal Medya Kullanımı')
                                ->placeholder('Sosyal medya kullanım alışkanlıklarınız')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('social_responsibility_project')
                                ->label('Sosyal Sorumluluk Projesi')
                                ->placeholder('Katıldığınız sosyal sorumluluk projeleri')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('professional_success_opinion')
                                ->label('İş Hayatında Başarı Görüşü')
                                ->placeholder('İş hayatında başarılı olmak için neler gerektiği hakkındaki görüşleriniz')
                                ->rows(2),
                        ])->columns(1),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('post_graduation_goal')
                                ->label('Mezuniyet Sonrası Hedef')
                                ->placeholder('Mezuniyet sonrası hedefleriniz')
                                ->rows(2),
                        ])->columns(1),
                ])
                ->collapsible()
                ->collapsed(true),
                // Reference Information / Referans Bilgileri
                Forms\Components\Section::make('Referans Bilgileri')
                ->description('Sizi tanıyan kişilere ait referans bilgilerini girin')
                ->icon('heroicon-o-identification')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('reference1_name')
                                ->label('1. Referans Adı')
                                ->placeholder('1. referansınızın adı soyadı')
                                ->maxLength(255),
            
                                      
                                PhoneInput::make('reference1_phone')
                                ->label('1. Referans Telefon')
                                ->placeholder('1. referansınızın telefon numarası')
                                ->defaultCountry('tr')
                                ->initialCountry('tr')
                               
                        ])->columns(2),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('reference2_name')
                                ->label('2. Referans Adı')
                                ->placeholder('2. referansınızın adı soyadı (varsa)')
                                ->maxLength(255),
                                
        
                                PhoneInput::make('reference2_phone')
                                ->label('2. Referans Telefon')
                                ->placeholder('2. referansınızın telefon numarası (varsa)')
                                ->defaultCountry('tr')
                                ->required()
                                ->initialCountry('tr')
                               
                        ])->columns(2),
                ])
                ->collapsible()
                ->collapsed(true),
                // Scholarship Information / Burs Bilgileri
                Forms\Components\Section::make('Burs Bilgileri')
                ->description('Burs durumunuz hakkında bilgileri girin')
                ->icon('heroicon-o-currency-dollar')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Select::make('receiving_other_scholarship')
                                ->label('Başka Burs/Kredi Alımı')
                                ->placeholder('Başka bir yerden burs/kredi alıyor musunuz?')
                                ->options([
                                    'Yes' => 'Evet',
                                    'No' => 'Hayır'
                                ])
                                ->live()
                                ->afterStateUpdated(function ($state, $set) {
                                    if ($state === 'No') {
                                        $set('other_scholarship_institution', null);
                                    }
                                }),
                                
                            Forms\Components\TextInput::make('other_scholarship_institution')
                                ->label('Başka Burs/Kredi Kurumu')
                                ->placeholder('Burs/kredi aldığınız kurum')
                                ->maxLength(255)
                        ])->columns(2),
                        
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TextInput::make('iban')
                                ->label('IBAN')
                                ->placeholder('Banka IBAN numaranız')
                                ->maxLength(255),
                        ])->columns(1),
                ])
                ->collapsible()
                ->collapsed(true),
                // Notes / Notlar
                Forms\Components\Section::make('Notlar')
                ->description('Eklemek istediğiniz notları girin')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Textarea::make('notes')
                                ->label('Notlar')
                                ->placeholder('Eklemek istediğiniz notlar')
                                ->rows(3),
                        ])->columns(1),
                ])
                ->collapsible()
                ->collapsed(true),
            ])
      
          
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
                        // English status values
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
                        'danger' => fn ($state) => in_array($state, ['rejected', 'red_edildi', 'Reddedildi']),
                        'success' => fn ($state) => in_array($state, ['accepted', 'kabul_edildi', 'mulakat_tamamlandi', 'interview_completed', 'final_acceptance', 'previous_scholar', 'dogrulama_tamamlandi', 'Kabul Edildi', 'Mülakat Tamamlandı', 'Doğrulama Tamamlandı', 'Kesin Kabul', 'Önceki Burslu']),
                        'purple' => fn ($state) => in_array($state, ['interview_scheduled', 'interview_pool', 'interview_scheduled']),
                        'warning' => fn ($state) => in_array($state, ['awaiting_evaluation', 'belgeler_yuklendi', 'Değerlendirme Bekleniyor', 'Belgeler Yüklendi']),
                        'primary' => fn ($state) => in_array($state, ['scholarship_pool', 'pre_approved', 'awaiting_documents', 'burs_havuzu', 'on_kabul', 'Burs Havuzu', 'Ön Kabul', 'Evrak Bekleniyor']),
                        'secondary' => fn ($state) => in_array($state, ['documents_under_review', 'interview_scheduled', 'mulakat_planlandi', 'Evrak İncelemede', 'Mülakat Planlandı']),
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
                // Tables\Actions\BulkActionGroup::make([
                
                // ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basvuru Bilgileri')
                ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('national_id')
                            ->label('TC Kimlik No'),
                        TextEntry::make('name')
                            ->label('Ad'),
                        TextEntry::make('surname')
                            ->label('Soyad'),
                        TextEntry::make('birth_date')
                            ->label('Doğum Tarihi')
                            ->date(),

                        TextEntry::make('created_at')
                            ->label('Başvuru Tarihi')
                            ->dateTime('d.m.Y H:i'),
                        TextEntry::make('status')
                            ->label('Başvuru Durumu')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                // English status values
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
                                'danger' => fn ($state) => in_array($state, ['rejected', 'red_edildi', 'Reddedildi']),
                                'success' => fn ($state) => in_array($state, ['accepted', 'kabul_edildi', 'mulakat_tamamlandi', 'interview_completed', 'final_acceptance', 'previous_scholar', 'dogrulama_tamamlandi', 'Kabul Edildi', 'Mülakat Tamamlandı', 'Doğrulama Tamamlandı', 'Kesin Kabul', 'Önceki Burslu']),
                                'purple' => fn ($state) => in_array($state, ['interview_scheduled', 'interview_pool', 'interview_scheduled']),
                                'warning' => fn ($state) => in_array($state, ['awaiting_evaluation', 'belgeler_yuklendi', 'Değerlendirme Bekleniyor', 'Belgeler Yüklendi']),
                                'primary' => fn ($state) => in_array($state, ['scholarship_pool', 'pre_approved', 'awaiting_documents', 'burs_havuzu', 'on_kabul', 'Burs Havuzu', 'Ön Kabul', 'Evrak Bekleniyor']),
                                'secondary' => fn ($state) => in_array($state, ['documents_under_review', 'interview_scheduled', 'mulakat_planlandi', 'Evrak İncelemede', 'Mülakat Planlandı']),
                            ]),
                            
                        
                        // Add more fields as needed
                    ])->columns(2),
                
                // Add more sections for other field groups
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
            'view' => Pages\ViewApplication::route('/{record}'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        // Sadece giriş yapmış kullanıcının başvurularını göster
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}






