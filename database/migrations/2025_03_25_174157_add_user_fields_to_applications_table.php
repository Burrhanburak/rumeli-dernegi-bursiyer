<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First check if the table exists
        if (!Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        Schema::table('applications', function (Blueprint $table) {
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('applications', 'image')) {
                // Personal Information
                // Avoid re-adding the id column
                // $table->id()->after('id'); - This would be an error
                
                // Check before adding each column to avoid duplicates
                if (!Schema::hasColumn('applications', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->after('id');
                }
                
                if (!Schema::hasColumn('applications', 'application_id')) {
                    $table->string('application_id')->nullable()->unique()->after('user_id');
                }
                
                if (!Schema::hasColumn('applications', 'application_date')) {
                    $table->date('application_date')->nullable()->after('application_id');
                }
                
                if (!Schema::hasColumn('applications', 'status')) {
                    $table->string('status')->default('awaiting_evaluation')->after('application_date');
                }
                
                if (!Schema::hasColumn('applications', 'notes')) {
                    $table->text('notes')->nullable()->after('status');
                }
                
                if (!Schema::hasColumn('applications', 'are_documents_approved')) {
                    $table->boolean('are_documents_approved')->default(false)->after('notes');
                }
                
                if (!Schema::hasColumn('applications', 'is_interview_completed')) {
                    $table->boolean('is_interview_completed')->default(false)->after('are_documents_approved');
                }
                
                if (!Schema::hasColumn('applications', 'approval_date')) {
                    $table->date('approval_date')->nullable()->after('is_interview_completed');
                }
                
                // Add remaining fields
                $table->string('image')->nullable();
                $table->string('national_id')->nullable();                // tc_kimlik_no
                $table->string('name')->nullable();
                $table->string('surname')->nullable();
                $table->date('birth_date')->nullable();                   // dogum_tarihi
                $table->string('birth_place')->nullable();                // dogum_yeri
                $table->string('nationality')->nullable();                // uyrugu
                $table->string('gender')->nullable();                     // cinsiyet
                $table->string('phone', 15)->nullable();
                $table->string('email')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();                       // sehir
                $table->string('postal_code')->nullable();                // posta_kodu
                $table->text('physical_disability')->nullable();          // beden_ozru

                $table->string('registered_province')->nullable();        // nufusa_kayitli_il
                $table->string('registered_district')->nullable();        // nufusa_kayitli_ilce
                $table->text('disability_description')->nullable();       // ozur_aciklama
                $table->string('disability_status')->nullable();          // ozur_durumu

                // Education Information
                $table->string('school_name')->nullable();                // okul_adi
                $table->string('school_department')->nullable();          // okul_bolumu
                $table->string('grade')->nullable();                      // sinif
                $table->integer('enrollment_year')->nullable();           // giris_yili
                $table->string('scholarship_rate')->nullable();           // burs_orani
                $table->decimal('university_entrance_score', 10, 3)->nullable(); // universiteye_giris_puani
                $table->string('student_id')->nullable();                 // ogrenci_numarasi
                $table->string('primary_school_name')->nullable();        // ilkokul_adi
                $table->integer('primary_school_graduation_year')->nullable(); // ilkokul_mezuniyet_yili
                $table->string('high_school_name')->nullable();           // lise_adi
                $table->integer('high_school_graduation_year')->nullable(); // lise_mezuniyet_yili
                
                // Family Information
                $table->string('father_name')->nullable();                // baba_adi
                $table->string('father_surname')->nullable();             // baba_soyadi
                $table->integer('father_birth_year')->nullable();         // baba_dogum_yili
                $table->string('father_birth_place')->nullable();         // baba_dogum_yeri
                $table->string('father_occupation')->nullable();          // baba_meslegi
                $table->integer('father_death_year')->nullable();         // baba_vefat_yili
                $table->string('mother_name')->nullable();                // anne_adi
                $table->string('mother_surname')->nullable();             // anne_soyadi
                $table->integer('mother_birth_year')->nullable();         // anne_dogum_yili
                $table->string('mother_birth_place')->nullable();         // anne_dogum_yeri
                $table->string('mother_occupation')->nullable();          // anne_meslegi
                $table->integer('mother_death_year')->nullable();         // anne_vefat_yili
                
                // Sibling Information
                $table->string('sibling1_name')->nullable();              // kardes1_adi
                $table->string('sibling1_surname')->nullable();           // kardes1_soyadi
                $table->integer('sibling1_age')->nullable();              // kardes1_yasi
                $table->string('sibling1_education')->nullable();         // kardes1_ogrenim_durumu
                $table->string('sibling2_name')->nullable();              // kardes2_adi
                $table->string('sibling2_surname')->nullable();           // kardes2_soyadi
                $table->integer('sibling2_age')->nullable();              // kardes2_yasi
                $table->string('sibling2_education')->nullable();         // kardes2_ogrenim_durumu
                $table->string('sibling3_name')->nullable();              // kardes3_adi
                $table->string('sibling3_surname')->nullable();           // kardes3_soyadi
                $table->integer('sibling3_age')->nullable();              // kardes3_yasi
                $table->string('sibling3_education')->nullable();         // kardes3_ogrenim_durumu
                $table->string('sibling4_name')->nullable();              // kardes4_adi
                $table->string('sibling4_surname')->nullable();           // kardes4_soyadi
                $table->integer('sibling4_age')->nullable();              // kardes4_yasi
                $table->string('sibling4_education')->nullable();         // kardes4_ogrenim_durumu
                $table->string('sibling5_name')->nullable();              // kardes5_adi
                $table->string('sibling5_surname')->nullable();           // kardes5_soyadi
                $table->integer('sibling5_age')->nullable();              // kardes5_yasi
                $table->string('sibling5_education')->nullable();         // kardes5_ogrenim_durumu
                
                // Residence Information
                $table->integer('family_head_dependent_count')->nullable(); // aile_reisinin_baktigi_fert_sayisi
                $table->string('family_subsistence_responsibility')->nullable(); // ailenin_geçim_sorumlulugu
                $table->text('family_residence_address')->nullable();     // aile_ikametgah_adresi
                $table->string('residence_province')->nullable();         // ikametgah_ili
                $table->string('residence_district')->nullable();         // ikametgah_ilcesi
                $table->string('family_phone', 15)->nullable();           // aile_telefon
                $table->string('resides_with_family')->nullable();        // ailenin_yaninda_kalarak_okula_devam
                $table->decimal('dormitory_monthly_payment', 10, 2)->nullable(); // yurtta_kaliyorsa_aylik_odeme
                $table->text('education_residence_address')->nullable();  // ogrenim_sirasinda_kaldigi_adres
                $table->string('education_province')->nullable();         // ogrenim_ili
                $table->string('education_district')->nullable();         // ogrenim_ilcesi
                
                // Financial Information
                $table->decimal('father_monthly_income', 10, 2)->nullable(); // baba_aylik_net_gelir
                $table->decimal('mother_monthly_income', 10, 2)->nullable(); // anne_aylik_net_gelir
                $table->decimal('sibling_monthly_income', 10, 2)->nullable(); // kardes_aylik_net_gelir
                $table->decimal('spouse_monthly_income', 10, 2)->nullable(); // es_aylik_net_gelir
                $table->decimal('death_benefit_annual_income', 10, 2)->nullable(); // vefat_ile_bagli_maas_yillik_net_gelir
                $table->string('family_owns_house')->nullable();          // ailenin_evi_var_mi
                $table->decimal('rent_payment_amount', 10, 2)->nullable(); // kirada_oturuyor_ise_kira_miktari
                $table->text('real_estate_value_and_income')->nullable(); // gayrimenkul_degeri_ve_geliri
                $table->integer('car_model_year')->nullable();            // otomobil_model_yili
                $table->decimal('other_income_amount', 10, 2)->nullable(); // baska_gelir_miktari
                
                // Other Information
                $table->text('field_selection')->nullable();              // brans_secimi
                $table->text('club_membership')->nullable();              // kulup_uyeligi
                $table->text('library_usage')->nullable();                // kutuphane_kullanimi
                $table->text('hobby')->nullable();                        // hobi
                $table->string('scholarship_commitment')->nullable();     // burs_verme_sozu
                $table->text('social_media_usage')->nullable();           // sosyal_medya_kullanimi
                $table->text('social_responsibility_project')->nullable(); // sosyal_sorumluluk_projesi
                $table->text('professional_success_opinion')->nullable(); // is_hayatinda_basari_gorusu
                $table->text('post_graduation_goal')->nullable();         // mezuniyet_sonrasi_hedef
                
                // Reference Information
                $table->string('reference1_name')->nullable();            // referans1_adi
                $table->string('reference1_phone', 15)->nullable();       // referans1_tel
                $table->string('reference2_name')->nullable();            // referans2_adi
                $table->string('reference2_phone', 15)->nullable();       // referans2_tel
                
                // Scholarship Information
                $table->string('receiving_other_scholarship')->nullable(); // baska_burs_kredi_alimi
                $table->string('other_scholarship_institution')->nullable(); // baska_burs_kredi_kurumu
                $table->string('iban')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First check if the table exists
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                // Only drop columns that actually exist
                $columns = [
                    // Don't drop primary columns
                    // 'id', 
                    'image',
                    'national_id',                  // tc_kimlik_no
                    'name',
                    'surname',
                    'birth_date',                   // dogum_tarihi
                    'birth_place',                  // dogum_yeri
                    'nationality',                  // uyrugu
                    'gender',                       // cinsiyet
                    'phone',
                    'email',
                    'address',
                    'city',                         // sehir
                    'postal_code',                  // posta_kodu
                    'physical_disability',          // beden_ozru
                    'registered_province',          // nufusa_kayitli_il
                    'registered_district',          // nufusa_kayitli_ilce
                    'disability_description',       // ozur_aciklama
                    'disability_status',            // ozur_durumu
                    'school_name',                  // okul_adi
                    'school_department',            // okul_bolumu
                    'grade',                        // sinif
                    'enrollment_year',              // giris_yili
                    'scholarship_rate',             // burs_orani
                    'university_entrance_score',    // universiteye_giris_puani
                    'student_id',                   // ogrenci_numarasi
                    'primary_school_name',          // ilkokul_adi
                    'primary_school_graduation_year', // ilkokul_mezuniyet_yili
                    'high_school_name',             // lise_adi
                    'high_school_graduation_year',  // lise_mezuniyet_yili
                    'father_name',                  // baba_adi
                    'father_surname',               // baba_soyadi
                    'father_birth_year',            // baba_dogum_yili
                    'father_birth_place',           // baba_dogum_yeri
                    'father_occupation',            // baba_meslegi
                    'father_death_year',            // baba_vefat_yili
                    'mother_name',                  // anne_adi
                    'mother_surname',               // anne_soyadi
                    'mother_birth_year',            // anne_dogum_yili
                    'mother_birth_place',           // anne_dogum_yeri
                    'mother_occupation',            // anne_meslegi
                    'mother_death_year',            // anne_vefat_yili
                    'sibling1_name',                // kardes1_adi
                    'sibling1_surname',             // kardes1_soyadi
                    'sibling1_age',                 // kardes1_yasi
                    'sibling1_education',           // kardes1_ogrenim_durumu
                    'sibling2_name',                // kardes2_adi
                    'sibling2_surname',             // kardes2_soyadi
                    'sibling2_age',                 // kardes2_yasi
                    'sibling2_education',           // kardes2_ogrenim_durumu
                    'sibling3_name',                // kardes3_adi
                    'sibling3_surname',             // kardes3_soyadi
                    'sibling3_age',                 // kardes3_yasi
                    'sibling3_education',           // kardes3_ogrenim_durumu
                    'sibling4_name',                // kardes4_adi
                    'sibling4_surname',             // kardes4_soyadi
                    'sibling4_age',                 // kardes4_yasi
                    'sibling4_education',           // kardes4_ogrenim_durumu
                    'sibling5_name',                // kardes5_adi
                    'sibling5_surname',             // kardes5_soyadi
                    'sibling5_age',                 // kardes5_yasi
                    'sibling5_education',           // kardes5_ogrenim_durumu
                    'family_head_dependent_count',  // aile_reisinin_baktigi_fert_sayisi
                    'family_subsistence_responsibility', // ailenin_geçim_sorumlulugu
                    'family_residence_address',     // aile_ikametgah_adresi
                    'residence_province',           // ikametgah_ili
                    'residence_district',           // ikametgah_ilcesi
                    'family_phone',                 // aile_telefon
                    'resides_with_family',          // ailenin_yaninda_kalarak_okula_devam
                    'dormitory_monthly_payment',    // yurtta_kaliyorsa_aylik_odeme
                    'education_residence_address',  // ogrenim_sirasinda_kaldigi_adres
                    'education_province',           // ogrenim_ili
                    'education_district',           // ogrenim_ilcesi
                    'father_monthly_income',        // baba_aylik_net_gelir
                    'mother_monthly_income',        // anne_aylik_net_gelir
                    'sibling_monthly_income',       // kardes_aylik_net_gelir
                    'spouse_monthly_income',        // es_aylik_net_gelir
                    'death_benefit_annual_income',  // vefat_ile_bagli_maas_yillik_net_gelir
                    'family_owns_house',            // ailenin_evi_var_mi
                    'rent_payment_amount',          // kirada_oturuyor_ise_kira_miktari
                    'real_estate_value_and_income', // gayrimenkul_degeri_ve_geliri
                    'car_model_year',               // otomobil_model_yili
                    'other_income_amount',          // baska_gelir_miktari
                    'field_selection',              // brans_secimi
                    'club_membership',              // kulup_uyeligi
                    'library_usage',                // kutuphane_kullanimi
                    'hobby',                        // hobi
                    'scholarship_commitment',       // burs_verme_sozu
                    'social_media_usage',           // sosyal_medya_kullanimi
                    'social_responsibility_project', // sosyal_sorumluluk_projesi
                    'professional_success_opinion', // is_hayatinda_basari_gorusu
                    'post_graduation_goal',         // mezuniyet_sonrasi_hedef
                    'reference1_name',              // referans1_adi
                    'reference1_phone',             // referans1_tel
                    'reference2_name',              // referans2_adi
                    'reference2_phone',             // referans2_tel
                    'receiving_other_scholarship',  // baska_burs_kredi_alimi
                    'other_scholarship_institution', // baska_burs_kredi_kurumu
                    'iban',
                ];
                
                // Only try to drop columns that actually exist
                foreach ($columns as $column) {
                    if (Schema::hasColumn('applications', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
