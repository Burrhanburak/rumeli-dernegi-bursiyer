<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('is_admin')->label('Admin Bilgisi'),
            ExportColumn::make('national_id')->label('TC No'),
            ExportColumn::make('name')->label('Adı'),
            ExportColumn::make('surname')->label('Soyadı'),
            // ExportColumn::make('terms')->label('Kullanım Sözleşmesi'),
            ExportColumn::make('birth_date')->label('Doğum Tarihi'),
            ExportColumn::make('birth_place')->label('Doğum Yeri'),
            ExportColumn::make('nationality')->label('Uyruk'),
            ExportColumn::make('gender')->label('Cinsiyet'),
            ExportColumn::make('phone')->label('Telefon Numarası'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('address')->label('Adres'),
            ExportColumn::make('city')->label('Şehir'),
            ExportColumn::make('postal_code')->label('Posta Kodu'),
            // ExportColumn::make('physical_disability')->label('Fizik Engellilik'),
            // ExportColumn::make('registered_province')->label('Kayıtlı İl'),
            // ExportColumn::make('registered_district')->label('Kayıtlı İlçe'),
            // ExportColumn::make('school_name')->label('Okul Adı'),
            // ExportColumn::make('school_department')->label('Okul Bölümü'),
            // ExportColumn::make('grade')->label('Sınıf'),
            // ExportColumn::make('enrollment_year')->label('Devam Süresi'),
            // ExportColumn::make('scholarship_rate')->label('Burs Oranı'),
            // ExportColumn::make('university_entrance_score')->label('Üniversite Sınav Puanı'),
            // ExportColumn::make('student_id')->label('Öğrenci Numarası'),
            // ExportColumn::make('primary_school_name')->label('İlkokul Adı'),
            // ExportColumn::make('primary_school_graduation_year')->label('İlkokul Mezuniyet Yılı'),
            // ExportColumn::make('high_school_name')->label('Lise Adı'),
            // ExportColumn::make('high_school_graduation_year')->label('Lise Mezuniyet Yılı'),
            // ExportColumn::make('father_name')->label('Babasının Adı'),
            // ExportColumn::make('father_surname')->label('Babasının Soyadı'),
            // ExportColumn::make('father_birth_year')->label('Babasının Doğum Yılı'),
            // ExportColumn::make('father_birth_place')->label('Babasının Doğum Yeri'),
            // ExportColumn::make('father_occupation')->label('Babasının Mesleği'),
            // ExportColumn::make('father_death_year'),
            // ExportColumn::make('mother_name'),
            // ExportColumn::make('mother_surname'),
            // ExportColumn::make('mother_birth_year'),
            // ExportColumn::make('mother_birth_place'),
            // ExportColumn::make('mother_occupation'),
            // ExportColumn::make('mother_death_year'),
            // ExportColumn::make('sibling1_name'),
            // ExportColumn::make('sibling1_surname'),
            // ExportColumn::make('sibling1_age'),
            // ExportColumn::make('sibling1_education'),
            // ExportColumn::make('sibling2_name'),
            // ExportColumn::make('sibling2_surname'),
            // ExportColumn::make('sibling2_age'),
            // ExportColumn::make('sibling2_education'),
            // ExportColumn::make('sibling3_name'),
            // ExportColumn::make('sibling3_surname'),
            // ExportColumn::make('sibling3_age'),
            // ExportColumn::make('sibling3_education'),
            // ExportColumn::make('sibling4_name'),
            // ExportColumn::make('sibling4_surname'),
            // ExportColumn::make('sibling4_age'),
            // ExportColumn::make('sibling4_education'),
            // ExportColumn::make('sibling5_name'),
            // ExportColumn::make('sibling5_surname'),
            // ExportColumn::make('sibling5_age'),
            // ExportColumn::make('sibling5_education'),
            // ExportColumn::make('family_head_dependent_count'),
            // ExportColumn::make('family_subsistence_responsibility'),
            // ExportColumn::make('family_residence_address'),
            // ExportColumn::make('residence_province'),
            // ExportColumn::make('residence_district'),
            // ExportColumn::make('family_phone'),
            // ExportColumn::make('resides_with_family'),
            // ExportColumn::make('dormitory_monthly_payment'),
            // ExportColumn::make('education_residence_address'),
            // ExportColumn::make('education_province'),
            // ExportColumn::make('education_district'),
            // ExportColumn::make('father_monthly_income'),
            // ExportColumn::make('mother_monthly_income'),
            // ExportColumn::make('sibling_monthly_income'),
            // ExportColumn::make('spouse_monthly_income'),
            // ExportColumn::make('death_benefit_annual_income'),
            // ExportColumn::make('family_owns_house'),
            // ExportColumn::make('rent_payment_amount'),
            // ExportColumn::make('real_estate_value_and_income'),
            // ExportColumn::make('car_model_year'),
            // ExportColumn::make('other_income_amount'),
            // ExportColumn::make('field_selection'),
            // ExportColumn::make('club_membership'),
            // ExportColumn::make('library_usage'),
            // ExportColumn::make('hobby'),
            // ExportColumn::make('scholarship_commitment'),
            // ExportColumn::make('social_media_usage'),
            // ExportColumn::make('social_responsibility_project'),
            // ExportColumn::make('professional_success_opinion'),
            // ExportColumn::make('post_graduation_goal'),
            // ExportColumn::make('reference1_name'),
            // ExportColumn::make('reference1_phone'),
            // ExportColumn::make('reference2_name'),
            // ExportColumn::make('reference2_phone'),
            // ExportColumn::make('receiving_other_scholarship'),
            // ExportColumn::make('other_scholarship_institution'),
            // ExportColumn::make('image'),
            // ExportColumn::make('status'),
            // ExportColumn::make('iban'),
            // ExportColumn::make('is_active'),
            // ExportColumn::make('otp_code'),
            // ExportColumn::make('otp_expires_at'),
            // ExportColumn::make('email_verified_at'),
            // ExportColumn::make('created_at'),
            // ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
