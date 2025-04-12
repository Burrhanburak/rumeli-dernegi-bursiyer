<?php

namespace App\Filament\Exports;

use App\Models\Applications;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ApplicationsExporter extends Exporter
{
    protected static ?string $model = Applications::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user_id')->label('Kullanıcı ID'),
            ExportColumn::make('application_id')->label('Başvuru ID'),
            ExportColumn::make('program_id')->label('Program ID'),
            ExportColumn::make('application_date')->label('Başvuru Tarihi'),
            ExportColumn::make('status')->label('Durum'),
            ExportColumn::make('pre_approved_by')->label('Ön Onaylayan'),
            ExportColumn::make('pre_approved_at')->label('Ön Onay Tarihi'),
            ExportColumn::make('rejected_by')->label('Reddeden'),
            ExportColumn::make('rejected_at')->label('Reddedilme Tarihi'),
            ExportColumn::make('document_reviewed_by')->label('Belgeleri Gözden Geçiren'),
            ExportColumn::make('document_reviewed_at')->label('Belgelerin Gözden Geçirilme Tarihi'),
            ExportColumn::make('interview_pool_by')->label('Rütbe Puanı Hesaplayan'),
            ExportColumn::make('interview_pool_at')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('accepted_by')->label('Kabul Eden'),
            ExportColumn::make('accepted_at')->label('Kabul Tarihi'),
            ExportColumn::make('final_acceptance_by')->label('Son Kabul Eden'),
            ExportColumn::make('final_acceptance_at')->label('Son Kabul Tarihi'),
            ExportColumn::make('reviewed_by')->label('Gözden Geçiren'),
            ExportColumn::make('reviewed_at')->label('Gözden Geçirilme Tarihi'),
            ExportColumn::make('notes')->label('Notlar'),
            ExportColumn::make('rejection_reason')->label('Reddedilme Nedeni'),
            ExportColumn::make('rejection_date')->label('Reddedilme Tarihi'),
            ExportColumn::make('are_documents_approved')->label('Belgeler Onaylandı mı?'),
            ExportColumn::make('is_interview_scheduled')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('is_interview_completed')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('interview_result')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('interview_score')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('approval_date')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('approval_notes')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('scholarship_amount')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('scholarship_start_date')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('scholarship_end_date')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('created_at')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('updated_at')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('image')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('national_id')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('name')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('surname')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('birth_date')->label('Rütbe Puanı Hesaplanma Tarihi'),
            ExportColumn::make('birth_place')->label('Doğum Yeri'),
            ExportColumn::make('nationality')->label('Uyruk'),
            ExportColumn::make('gender')->label('Cinsiyet'),
            ExportColumn::make('phone')->label('Telefon Numarası'),
            ExportColumn::make('email')->label('E-posta Adresi'),
            ExportColumn::make('address')->label('Adres'),
            ExportColumn::make('city')->label('Şehir'),
            ExportColumn::make('postal_code')->label('Posta Kodu'),
            ExportColumn::make('physical_disability')->label('Fiziki Engellilik'),
            ExportColumn::make('registered_province')->label('Kayıtlı İl'),
            ExportColumn::make('registered_district')->label('Kayıtlı İlçe'),
            ExportColumn::make('disability_description')->label('Engellilik Açıklaması'),
            ExportColumn::make('disability_status')->label('Engellilik Durumu'),
            ExportColumn::make('school_name')->label('Okul Adı'),
            ExportColumn::make('school_department')->label('Okul Bölümü'),
            ExportColumn::make('grade')->label('Sınıf'),
            ExportColumn::make('enrollment_year')->label('Devam Etme Yılı'),
            ExportColumn::make('scholarship_rate')->label('Burs Oranı'),
            ExportColumn::make('university_entrance_score')->label('Üniversite Sınav Puanı'),
            ExportColumn::make('student_id')->label('Öğrenci Numarası'),
            ExportColumn::make('primary_school_name')->label('İlköğretim Okulu Adı'),
            ExportColumn::make('primary_school_graduation_year')->label('İlköğretim Mezuniyet Yılı'),
            ExportColumn::make('high_school_name')->label('Lise Adı'),
            ExportColumn::make('high_school_graduation_year')->label('Lise Mezuniyet Yılı'),
            ExportColumn::make('father_name')->label('Babasının Adı'),
            ExportColumn::make('father_surname')->label('Babasının Soyadı'),
            ExportColumn::make('father_birth_year')->label('Babasının Doğum Yılı'),
            ExportColumn::make('father_birth_place')->label('Babasının Doğum Yeri'),
            ExportColumn::make('father_occupation')->label('Babasının Mesleği'),
            ExportColumn::make('father_death_year')->label('Babasının Ölüm Yılı'),
            ExportColumn::make('mother_name')->label('Annenin Adı'),
            ExportColumn::make('mother_surname')->label('Annenin Soyadı'),
            ExportColumn::make('mother_birth_year')->label('Annenin Doğum Yılı'),
            ExportColumn::make('mother_birth_place')->label('Annenin Doğum Yeri'),
            ExportColumn::make('mother_occupation')->label('Annenin Mesleği'),
            ExportColumn::make('mother_death_year')->label('Annenin Ölüm Yılı'),
            ExportColumn::make('sibling1_name')->label('Kardeş 1 Adı'),
            ExportColumn::make('sibling1_surname')->label('Kardeş 1 Soyadı'),
            ExportColumn::make('sibling1_age')->label('Kardeş 1 Yaşı'),
            ExportColumn::make('sibling1_education')->label('Kardeş 1 Eğitimi'),
            ExportColumn::make('sibling2_name')->label('Kardeş 2 Adı'),
            ExportColumn::make('sibling2_surname')->label('Kardeş 2 Soyadı'),
            ExportColumn::make('sibling2_age')->label('Kardeş 2 Yaşı'),
            ExportColumn::make('sibling2_education')->label('Kardeş 2 Eğitimi'),
            ExportColumn::make('sibling3_name')->label('Kardeş 3 Adı'),
            ExportColumn::make('sibling3_surname')->label('Kardeş 3 Soyadı'),
            ExportColumn::make('sibling3_age')->label('Kardeş 3 Yaşı'),
            ExportColumn::make('sibling3_education')->label('Kardeş 3 Eğitimi'),
            ExportColumn::make('sibling4_name')->label('Kardeş 4 Adı'),
            ExportColumn::make('sibling4_surname')->label('Kardeş 4 Soyadı'),
            ExportColumn::make('sibling4_age')->label('Kardeş 4 Yaşı'),
            ExportColumn::make('sibling4_education')->label('Kardeş 4 Eğitimi'),
            ExportColumn::make('sibling5_name')->label('Kardeş 5 Adı'),
            ExportColumn::make('sibling5_surname')->label('Kardeş 5 Soyadı'),
            ExportColumn::make('sibling5_age')->label('Kardeş 5 Yaşı'),
            ExportColumn::make('sibling5_education')->label('Kardeş 5 Eğitimi'),
            ExportColumn::make('family_head_dependent_count')->label('Aile Başı Bağımlı Kişi Sayısı'),
            ExportColumn::make('family_subsistence_responsibility')->label('Aile İçin Yükümlülük'),
            ExportColumn::make('family_residence_address')->label('Aile Ev Adresi'),
            ExportColumn::make('residence_province')->label('İl'),
            ExportColumn::make('residence_district')->label('İlçe'),
            ExportColumn::make('family_phone')->label('Aile Telefon Numarası'),
            ExportColumn::make('resides_with_family')->label('Aileyle Beraber Mi?'),
            ExportColumn::make('dormitory_monthly_payment')->label('Yurt İçi Ödeme'),
            ExportColumn::make('education_residence_address')->label('Eğitim Ev Adresi'),
            ExportColumn::make('education_province')->label('Eğitim İl'),
            ExportColumn::make('education_district')->label('Eğitim İlçe'),
            ExportColumn::make('father_monthly_income')->label('Babasının Aylık Geliri'),
            ExportColumn::make('mother_monthly_income')->label('Annenin Aylık Geliri'),
            ExportColumn::make('sibling_monthly_income')->label('Kardeşlerin Aylık Geliri'),
            ExportColumn::make('spouse_monthly_income')->label('Eşinin Aylık Geliri'),
            ExportColumn::make('death_benefit_annual_income')->label('Ölüm Avantajı Yıllık Gelir'),
            ExportColumn::make('family_owns_house')->label('Aile Ev Sahibi Mi?'),
            ExportColumn::make('rent_payment_amount')->label('Kira Ödemesi Tutarı'),
            ExportColumn::make('real_estate_value_and_income')->label('Gayrimenkul Değeri ve Geliri'),
            ExportColumn::make('car_model_year')->label('Araba Model Yılı'),
            ExportColumn::make('other_income_amount')->label('Diğer Gelir Tutarı'),
            ExportColumn::make('field_selection')->label('Alan Seçimi'),
            ExportColumn::make('club_membership')->label('Kulüb Üyeliği'),
            ExportColumn::make('library_usage')->label('Kütüphane Kullanımı'),
            ExportColumn::make('hobby')->label('Hobi'),
            ExportColumn::make('scholarship_commitment')->label('Burs Sözleşmesi'),
            ExportColumn::make('social_media_usage')->label('Sosyal Medya Kullanımı'),
            ExportColumn::make('social_responsibility_project')->label('Sosyal Sorumluluk Projesi'),
            ExportColumn::make('professional_success_opinion')->label('Profesyonel Başarı İlüşü'),
            ExportColumn::make('post_graduation_goal')->label('Yüksek Lisans Hedefi'),
            ExportColumn::make('reference1_name')->label('Referans 1 Adı'),
            ExportColumn::make('reference1_phone')->label('Referans 1 Telefon Numarası'),
            ExportColumn::make('reference2_name')->label('Referans 2 Adı'),
            ExportColumn::make('reference2_phone')->label('Referans 2 Telefon Numarası'),
            ExportColumn::make('receiving_other_scholarship')->label('Diğer Burs Alıyor Mu?'),
            ExportColumn::make('other_scholarship_institution')->label('Diğer Burs Kurumu'),
            ExportColumn::make('iban')->label('IBAN Numarası'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Başvuru verileriniz için dışa aktarma tamamlandı ve ' . number_format($export->successful_rows) . ' satır başarıyla dışa aktarıldı.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' satır dışa aktarılamadı.';
        }

        return $body;
    }

    public static function getJobStartedNotificationBody(Export $export): string
    {
        return 'Dışa aktarmanız başlamıştır ve ' . number_format($export->total_rows) . ' satır işlenecektir. İşlem tamamlandığında indirme bağlantısı ile bir bildirim alacaksınız.';
    }
}
