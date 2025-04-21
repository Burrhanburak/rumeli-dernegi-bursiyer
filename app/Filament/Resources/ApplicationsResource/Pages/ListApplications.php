<?php

namespace App\Filament\Resources\ApplicationsResource\Pages;

use App\Filament\Resources\ApplicationsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Filament\Exports\ApplicationsExporter;
use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;


class ListApplications extends ListRecords
{
    protected static string $resource = ApplicationsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Yeni Başvuru'),

                ExportAction::make()
                ->label('İndir')
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                        ->withColumns([
                            // Personal Information
                            Column::make('id')->heading('ID'),
                            Column::make('name')->heading('Ad'),
                            Column::make('surname')->heading('Soyad'),
                            Column::make('email')->heading('E-posta'),
                            Column::make('phone')->heading('Telefon Numarası'),
                            Column::make('national_id')->heading('T.C. Kimlik No'),
                            Column::make('birth_date')->heading('Doğum Tarihi'),
                            Column::make('birth_place')->heading('Doğum Yeri'),
                            Column::make('nationality')->heading('Uyruk'),
                            Column::make('gender')->heading('Cinsiyet'),
                            Column::make('address')->heading('Adres'),
                            Column::make('city')->heading('Şehir'),
                            Column::make('postal_code')->heading('Posta Kodu'),
                            Column::make('disability_status')->heading('Engel Durumu'),
                            Column::make('physical_disability')->heading('Fiziksel Engel'),
                            Column::make('disability_description')->heading('Engel Açıklaması'),
                            
                            // Registration Information
                            Column::make('registered_province')->heading('Nüfusa Kayıtlı İl'),
                            Column::make('registered_district')->heading('Nüfusa Kayıtlı İlçe'),
                            
                            // Education Information
                            Column::make('school_name')->heading('Okul Adı'),
                            Column::make('school_department')->heading('Bölüm'),
                            Column::make('grade')->heading('Sınıf'),
                            Column::make('enrollment_year')->heading('Giriş Yılı'),
                            Column::make('student_id')->heading('Öğrenci Numarası'),
                            Column::make('scholarship_rate')->heading('Burs Oranı'),
                            Column::make('university_entrance_score')->heading('Üniversite Giriş Puanı'),
                            Column::make('primary_school_name')->heading('İlkokul Adı'),
                            Column::make('primary_school_graduation_year')->heading('İlkokul Mezuniyet Yılı'),
                            Column::make('high_school_name')->heading('Lise Adı'),
                            Column::make('high_school_graduation_year')->heading('Lise Mezuniyet Yılı'),
                            
                            // Family Information
                            Column::make('father_name')->heading('Baba Adı'),
                            Column::make('father_surname')->heading('Baba Soyadı'),
                            Column::make('father_birth_year')->heading('Baba Doğum Yılı'),
                            Column::make('father_birth_place')->heading('Baba Doğum Yeri'),
                            Column::make('father_occupation')->heading('Baba Mesleği'),
                            Column::make('father_monthly_income')->heading('Baba Aylık Net Gelir'),
                            Column::make('father_death_year')->heading('Baba Vefat Yılı'),
                            
                            Column::make('mother_name')->heading('Anne Adı'),
                            Column::make('mother_surname')->heading('Anne Soyadı'),
                            Column::make('mother_birth_year')->heading('Anne Doğum Yılı'),
                            Column::make('mother_birth_place')->heading('Anne Doğum Yeri'),
                            Column::make('mother_occupation')->heading('Anne Mesleği'),
                            Column::make('mother_monthly_income')->heading('Anne Aylık Net Gelir'),
                            Column::make('mother_death_year')->heading('Anne Vefat Yılı'),
                            
                            Column::make('sibling1_name')->heading('1. Kardeş Adı'),
                            Column::make('sibling1_surname')->heading('1. Kardeş Soyadı'),
                            Column::make('sibling1_age')->heading('1. Kardeş Yaşı'),
                            Column::make('sibling1_education')->heading('1. Kardeş Eğitim Durumu'),
                            
                            Column::make('sibling2_name')->heading('2. Kardeş Adı'),
                            Column::make('sibling2_surname')->heading('2. Kardeş Soyadı'),
                            Column::make('sibling2_age')->heading('2. Kardeş Yaşı'),
                            Column::make('sibling2_education')->heading('2. Kardeş Eğitim Durumu'),
                            
                            Column::make('sibling3_name')->heading('3. Kardeş Adı'),
                            Column::make('sibling3_surname')->heading('3. Kardeş Soyadı'),
                            Column::make('sibling3_age')->heading('3. Kardeş Yaşı'),
                            Column::make('sibling3_education')->heading('3. Kardeş Eğitim Durumu'),
                            
                            Column::make('sibling_monthly_income')->heading('Kardeşlerin Toplam Aylık Geliri'),
                            
                            // Residence Information
                            Column::make('family_head_dependent_count')->heading('Aile Reisinin Baktığı Kişi Sayısı'),
                            Column::make('family_subsistence_responsibility')->heading('Ailenin Geçim Sorumluluğu'),
                            Column::make('family_residence_address')->heading('Aile İkametgah Adresi'),
                            Column::make('residence_province')->heading('İkametgah İli'),
                            Column::make('residence_district')->heading('İkametgah İlçesi'),
                            Column::make('family_phone')->heading('Aile Telefon'),
                            Column::make('resides_with_family')->heading('Ailenin Yanında Kalarak Okula Devam'),
                            Column::make('dormitory_monthly_payment')->heading('Yurtta Kalıyorsa Aylık Ödeme'),
                            Column::make('education_residence_address')->heading('Öğrenim Sırasında Kaldığı Adres'),
                            Column::make('education_province')->heading('Öğrenim İli'),
                            Column::make('education_district')->heading('Öğrenim İlçesi'),
                            
                            // Financial Information
                            Column::make('spouse_monthly_income')->heading('Eş Aylık Net Gelir'),
                            Column::make('death_benefit_annual_income')->heading('Vefat ile Bağlı Maaş Yıllık Net Gelir'),
                            Column::make('family_owns_house')->heading('Ailenin Evi Var mı?'),
                            Column::make('rent_payment_amount')->heading('Kirada Oturuyor İse Kira Miktarı'),
                            Column::make('real_estate_value_and_income')->heading('Gayrimenkul Değeri ve Geliri'),
                            Column::make('car_model_year')->heading('Otomobil Model Yılı'),
                            Column::make('other_income_amount')->heading('Başka Gelir Miktarı'),
                            
                            // Other Information
                            Column::make('field_selection')->heading('Branş Seçimi'),
                            Column::make('club_membership')->heading('Kulüp Üyeliği'),
                            Column::make('library_usage')->heading('Kütüphane Kullanımı'),
                            Column::make('hobby')->heading('Hobi'),
                            Column::make('scholarship_commitment')->heading('Burs Verme Sözü'),
                            Column::make('social_media_usage')->heading('Sosyal Medya Kullanımı'),
                            Column::make('social_responsibility_project')->heading('Sosyal Sorumluluk Projesi'),
                            Column::make('professional_success_opinion')->heading('İş Hayatında Başarı Görüşü'),
                            Column::make('post_graduation_goal')->heading('Mezuniyet Sonrası Hedef'),
                            
                            // Reference Information
                            Column::make('reference1_name')->heading('1. Referans Adı'),
                            Column::make('reference1_phone')->heading('1. Referans Telefon'),
                            Column::make('reference2_name')->heading('2. Referans Adı'),
                            Column::make('reference2_phone')->heading('2. Referans Telefon'),
                            
                            // Scholarship Information
                            Column::make('receiving_other_scholarship')->heading('Başka Burs/Kredi Alımı'),
                            Column::make('other_scholarship_institution')->heading('Başka Burs/Kredi Kurumu'),
                            Column::make('iban')->heading('IBAN'),
                            
                            // Notes
                            Column::make('notes')->heading('Notlar'),
                            
                            // Status and Dates
                            Column::make('status')->heading('Durum'),
                            Column::make('program_id')->heading('Program ID'),
                            Column::make('user_id')->heading('Kullanıcı ID'),
                            Column::make('created_at')->heading('Oluşturulma Tarihi'),
                            Column::make('updated_at')->heading('Güncellenme Tarihi'),
                            Column::make('application_date')->heading('Başvuru Tarihi'),
                        ])
                ]),
              
        ];
    }

    protected static ?string $title = 'Başvurular';

    protected static ?string $breadcrumb = 'Başvurular listesi';

    protected static ?string $breadcrumbParent = 'Başvurular';

    protected static ?string $createButtonLabel = 'Yeni Başvuru';

    protected static ?string $createButtonIcon = 'heroicon-o-plus';

    protected static ?string $createButtonColor = 'success';

    public function getTabs(): array
    {
        return [
            'Tüm Başvurular' => Tab::make()
                ->label('Tüm Başvurular')
                ->icon('heroicon-o-document-text')
                ->badge(ApplicationsResource::getModel()::count())
                ->badgeColor('gray')
                ,
            'Bekleyen Başvurular' => Tab::make()
                ->label('Bekleyen Başvurular')
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'awaiting_evaluation')
                    ->orWhere('status', 'bekliyor')
                    ->orWhere('status', 'beklemede'))
                ->badge(ApplicationsResource::getModel()::where('status', 'awaiting_evaluation')
                    ->orWhere('status', 'bekliyor')
                    ->orWhere('status', 'beklemede')->count())
                ->badgeColor('gray')
                ,
            'Onaylanan Başvurular' => Tab::make()
                ->label('Onaylanan Başvurular')
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'accepted')
                    ->orWhere('status', 'kabul_edildi')
                    ->orWhere('status', 'onaylandi')
                    ->orWhere('status', 'dogrulama_tamamlandi'))
                ->badge(ApplicationsResource::getModel()::where('status', 'accepted')
                    ->orWhere('status', 'kabul_edildi')
                    ->orWhere('status', 'onaylandi')
                    ->orWhere('status', 'dogrulama_tamamlandi')->count())
                ->badgeColor('gray')
                ,

            'Reddedilen Başvurular' => Tab::make()
                ->label('Reddedilen Başvurular')
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'rejected')
                    ->orWhere('status', 'reddedildi')
                    ->orWhere('status', 'red')
                    ->orWhere('status', 'red_edildi'))
                ->badge(ApplicationsResource::getModel()::where('status', 'rejected')
                    ->orWhere('status', 'reddedildi')
                    ->orWhere('status', 'red')
                    ->orWhere('status', 'red_edildi')->count())
                ->badgeColor('gray')
                ,
          
              
        ];

   
    }


  
}
