<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(false);
            $table->string('national_id', 11)->nullable()->unique();  // tc_kimlik_no
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('password_confirmation')->nullable();
            $table->boolean('terms')->default(false);
            $table->date('birth_date')->nullable();  // dogum_tarihi
            $table->string('birth_place')->nullable();  // dogum_yeri
            $table->string('nationality')->nullable();  // uyrugu
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();  // cinsiyet
            $table->string('phone', 15)->nullable();
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('city')->nullable();  // sehir
            $table->string('postal_code')->nullable();  // posta_kodu
            $table->text('physical_disability')->nullable();  // beden_ozru
            $table->string('registered_province')->nullable();  // nufusa_kayitli_il
            $table->string('registered_district')->nullable();  // nufusa_kayitli_ilce
            $table->string('school_name')->nullable();  // okul_adi
            $table->string('school_department')->nullable();  // okul_bolumu
            $table->string('grade')->nullable();  // sinif
            $table->year('enrollment_year')->nullable();  // giris_yili
            $table->string('scholarship_rate')->nullable();  // burs_orani
            $table->decimal('university_entrance_score', 10, 3)->nullable();  // universiteye_giris_puani
            $table->string('student_id')->nullable()->unique();  // ogrenci_numarasi
            $table->string('primary_school_name')->nullable();  // ilkokul_adi
            $table->year('primary_school_graduation_year')->nullable();  // ilkokul_mezuniyet_yili
            $table->string('high_school_name')->nullable();  // lise_adi
            $table->year('high_school_graduation_year')->nullable();  // lise_mezuniyet_yili
            $table->string('father_name')->nullable();  // baba_adi
            $table->string('father_surname')->nullable();  // baba_soyadi
            $table->year('father_birth_year')->nullable();  // baba_dogum_yili
            $table->string('father_birth_place')->nullable();  // baba_dogum_yeri
            $table->string('father_occupation')->nullable();  // baba_meslegi
            $table->year('father_death_year')->nullable();  // baba_vefat_yili
            $table->string('mother_name')->nullable();  // anne_adi
            $table->string('mother_surname')->nullable();  // anne_soyadi
            $table->year('mother_birth_year')->nullable();  // anne_dogum_yili
            $table->string('mother_birth_place')->nullable();  // anne_dogum_yeri
            $table->string('mother_occupation')->nullable();  // anne_meslegi
            $table->year('mother_death_year')->nullable();  // anne_vefat_yili
            $table->string('sibling1_name')->nullable();  // kardes1_adi
            $table->string('sibling1_surname')->nullable();  // kardes1_soyadi
            $table->integer('sibling1_age')->nullable();  // kardes1_yasi
            $table->string('sibling1_education')->nullable();  // kardes1_ogrenim_durumu
            $table->string('sibling2_name')->nullable();  // kardes2_adi
            $table->string('sibling2_surname')->nullable();  // kardes2_soyadi
            $table->integer('sibling2_age')->nullable();  // kardes2_yasi
            $table->string('sibling2_education')->nullable();  // kardes2_ogrenim_durumu
            $table->string('sibling3_name')->nullable();  // kardes3_adi
            $table->string('sibling3_surname')->nullable();  // kardes3_soyadi
            $table->integer('sibling3_age')->nullable();  // kardes3_yasi
            $table->string('sibling3_education')->nullable();  // kardes3_ogrenim_durumu
            $table->string('sibling4_name')->nullable();  // kardes4_adi
            $table->string('sibling4_surname')->nullable();  // kardes4_soyadi
            $table->integer('sibling4_age')->nullable();  // kardes4_yasi
            $table->string('sibling4_education')->nullable();  // kardes4_ogrenim_durumu
            $table->string('sibling5_name')->nullable();  // kardes5_adi
            $table->string('sibling5_surname')->nullable();  // kardes5_soyadi
            $table->integer('sibling5_age')->nullable();  // kardes5_yasi
            $table->string('sibling5_education')->nullable();  // kardes5_ogrenim_durumu
            $table->integer('family_head_dependent_count')->nullable();  // aile_reisinin_baktigi_fert_sayisi
            $table->string('family_subsistence_responsibility')->nullable();  // ailenin_geçim_sorumlulugu
            $table->text('family_residence_address')->nullable();  // aile_ikametgah_adresi
            $table->string('residence_province')->nullable();  // ikametgah_ili
            $table->string('residence_district')->nullable();  // ikametgah_ilcesi
            $table->string('family_phone', 15)->nullable();  // aile_telefon
            $table->enum('resides_with_family', ['Yes', 'No'])->nullable();  // ailenin_yaninda_kalarak_okula_devam
            $table->decimal('dormitory_monthly_payment', 10, 2)->nullable();  // yurtta_kaliyorsa_aylik_odeme
            $table->text('education_residence_address')->nullable();  // ogrenim_sirasinda_kaldigi_adres
            $table->string('education_province')->nullable();  // ogrenim_ili
            $table->string('education_district')->nullable();  // ogrenim_ilcesi
            $table->decimal('father_monthly_income', 10, 2)->nullable();  // baba_aylik_net_gelir
            $table->decimal('mother_monthly_income', 10, 2)->nullable();  // anne_aylik_net_gelir
            $table->decimal('sibling_monthly_income', 10, 2)->nullable();  // kardes_aylik_net_gelir
            $table->decimal('spouse_monthly_income', 10, 2)->nullable();  // es_aylik_net_gelir
            $table->decimal('death_benefit_annual_income', 10, 2)->nullable();  // vefat_ile_bagli_maas_yillik_net_gelir
            $table->enum('family_owns_house', ['Yes', 'No'])->nullable();  // ailenin_evi_var_mi
            $table->decimal('rent_payment_amount', 10, 2)->nullable();  // kirada_oturuyor_ise_kira_miktari
            $table->text('real_estate_value_and_income')->nullable();  // gayrimenkul_degeri_ve_geliri
            $table->year('car_model_year')->nullable();  // otomobil_model_yili
            $table->decimal('other_income_amount', 10, 2)->nullable();  // baska_gelir_miktari
            $table->text('field_selection')->nullable();  // brans_secimi
            $table->text('club_membership')->nullable();  // kulup_uyeligi
            $table->text('library_usage')->nullable();  // kutuphane_kullanimi
            $table->text('hobby')->nullable();  // hobi
            $table->enum('scholarship_commitment', ['Yes', 'No'])->nullable();  // burs_verme_sozu
            $table->text('social_media_usage')->nullable();  // sosyal_medya_kullanimi
            $table->text('social_responsibility_project')->nullable();  // sosyal_sorumluluk_projesi
            $table->text('professional_success_opinion')->nullable();  // is_hayatinda_basari_gorusu
            $table->text('post_graduation_goal')->nullable();  // mezuniyet_sonrasi_hedef
            $table->string('reference1_name')->nullable();  // referans1_adi
            $table->string('reference1_phone', 15)->nullable();  // referans1_tel
            $table->string('reference2_name')->nullable();  // referans2_adi
            $table->string('reference2_phone', 15)->nullable();  // referans2_tel
            $table->enum('receiving_other_scholarship', ['Yes', 'No'])->nullable();  // baska_burs_kredi_alimi
            $table->string('other_scholarship_institution')->nullable();  // baska_burs_kredi_kurumu
            $table->string('image')->nullable();
            $table->enum('status', [
                'interview_pool',       // mulakata_havuzu
                'scholarship_pool',     // burs_havuzu
                'pre_approved',         // on_kabul
                'rejected',             // red_edildi
                'accepted',             // kabul_edildi
                'final_acceptance',     // kesin_kabul
                'previous_scholar'      // onceki_burslu
            ])->nullable();
            $table->string('iban')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            $table->rememberToken();
            $table->timestamps();
       
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
