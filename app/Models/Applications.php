<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Documents;
use App\Models\Interviews;
use App\Models\ScholarshipPrograms;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Applications extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'user_id',
        'program_id',
        'application_id',
        'application_date',
        'status',
        'notes',
        'are_documents_approved',
        'is_interview_completed',
        'approval_date',
        'image',
        'name',
        'surname',
        'birth_date',               // dogum_tarihi
        'birth_place',              // dogum_yeri
        'nationality',              // uyrugu
        'gender',                   // cinsiyet
        'phone',
        'email',
        'physical_disability',      // beden_ozru
        'national_id',              // tc_kimlik_no 
        'registered_province',      // nufusa_kayitli_il
        'registered_district',      // nufusa_kayitli_ilce
        'school_name',              // okul_adi
        'school_department',        // okul_bolumu
        'grade',                    // sinif
        'enrollment_year',          // giris_yili
        'scholarship_rate',         // burs_orani
        'university_entrance_score', // universiteye_giris_puani
        'student_id',               // ogrenci_numarasi
        'primary_school_name',      // ilkokul_adi
        'primary_school_graduation_year', // ilkokul_mezuniyet_yili
        'high_school_name',         // lise_adi
        'high_school_graduation_year', // lise_mezuniyet_yili
        // Family Information
        'father_name',              // baba_adi
        'father_surname',           // baba_soyadi
        'father_birth_year',        // baba_dogum_yili
        'father_birth_place',       // baba_dogum_yeri
        'father_occupation',        // baba_meslegi
        'father_death_year',        // baba_vefat_yili
        'father_monthly_income',    // baba_aylik_net_gelir
        'mother_name',              // anne_adi
        'mother_surname',           // anne_soyadi
        'mother_birth_year',        // anne_dogum_yili
        'mother_birth_place',       // anne_dogum_yeri
        'mother_occupation',        // anne_meslegi
        'mother_death_year',        // anne_vefat_yili
        'mother_monthly_income',    // anne_aylik_net_gelir
        // Sibling Information
        'sibling1_name',            // kardes1_adi
        'sibling1_surname',         // kardes1_soyadi
        'sibling1_age',             // kardes1_yasi
        'sibling1_education',       // kardes1_ogrenim_durumu
        'sibling2_name',            // kardes2_adi
        'sibling2_surname',         // kardes2_soyadi
        'sibling2_age',             // kardes2_yasi
        'sibling2_education',       // kardes2_ogrenim_durumu
        'sibling3_name',            // kardes3_adi
        'sibling3_surname',         // kardes3_soyadi
        'sibling3_age',             // kardes3_yasi
        'sibling3_education',       // kardes3_ogrenim_durumu
        'sibling4_name',            // kardes4_adi
        'sibling4_surname',         // kardes4_soyadi
        'sibling4_age',             // kardes4_yasi
        'sibling4_education',       // kardes4_ogrenim_durumu
        'sibling5_name',            // kardes5_adi
        'sibling5_surname',         // kardes5_soyadi
        'sibling5_age',             // kardes5_yasi
        'sibling5_education',       // kardes5_ogrenim_durumu
        'sibling_monthly_income',   // kardes_aylik_net_gelir
        // Residence Information
        'family_head_dependent_count', // aile_reisinin_baktigi_fert_sayisi
        'family_subsistence_responsibility', // ailenin_geçim_sorumlulugu
        'family_residence_address', // aile_ikametgah_adresi
        'residence_province',       // ikametgah_ili
        'residence_district',       // ikametgah_ilcesi
        'family_phone',             // aile_telefon
        'resides_with_family',      // ailenin_yaninda_kalarak_okula_devam
        'dormitory_monthly_payment', // yurtta_kaliyorsa_aylik_odeme
        'education_residence_address', // ogrenim_sirasinda_kaldigi_adres
        'education_province',       // ogrenim_ili
        'education_district',       // ogrenim_ilcesi
        // Financial Information
        'family_owns_house',        // ailenin_evi_var_mi
        'rent_payment_amount',      // kirada_oturuyor_ise_kira_miktari
        'real_estate_value_and_income', // gayrimenkul_degeri_ve_geliri
        'car_model_year',           // otomobil_model_yili
        'other_income_amount',      // baska_gelir_miktari
        // Other Information
        'field_selection',          // brans_secimi
        'club_membership',          // kulup_uyeligi
        'library_usage',            // kutuphane_kullanimi
        'hobby',                    // hobi
        'scholarship_commitment',   // burs_verme_sozu
        'social_media_usage',       // sosyal_medya_kullanimi
        'social_responsibility_project', // sosyal_sorumluluk_projesi
        'professional_success_opinion', // is_hayatinda_basari_gorusu
        'post_graduation_goal',     // mezuniyet_sonrasi_hedef
        // Reference Information
        'reference1_name',          // referans1_adi
        'reference1_phone',         // referans1_tel
        'reference2_name',          // referans2_adi
        'reference2_phone',         // referans2_tel
        // Scholarship Information
        'receiving_other_scholarship', // baska_burs_kredi_alimi
        'other_scholarship_institution', // baska_burs_kredi_kurumu
        'iban',
        'address',               // Add this field
        'city',                  // Add this field
        'postal_code',           // Add this field
        'disability_description', // Add this field
        'disability_status',     // Add this field
        'pre_approved_by',
        'pre_approved_at',
        'rejected_by',
        'rejected_at',
        'document_reviewed_by',
        'document_reviewed_at',
        'interview_pool_by',
        'interview_pool_at',
        'accepted_by',
        'accepted_at',
        'final_acceptance_by',
        'final_acceptance_at',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'approval_notes',
        'scholarship_amount',
        'scholarship_start_date',
        'scholarship_end_date',
    ];
    
    // Specify which attributes cannot be mass assigned
    protected $guarded = ['id'];
    
    // Define date fields
    protected $dates = [
        'application_date',
        'birth_date',              // dogum_tarihi
        'approval_date',
        'created_at',
        'updated_at'
    ];
    
    // Cast attributes to native types
    protected $casts = [
        'are_documents_approved' => 'boolean',
        'is_interview_completed' => 'boolean',
        'father_birth_year' => 'integer',
        'mother_birth_year' => 'integer',
        'father_death_year' => 'integer',
        'mother_death_year' => 'integer',
        'sibling1_age' => 'integer',
        'sibling2_age' => 'integer',
        'sibling3_age' => 'integer',
        'sibling4_age' => 'integer',
        'sibling5_age' => 'integer',
        'enrollment_year' => 'integer',
        'university_entrance_score' => 'decimal:3', // Changed from integer to decimal:3 to match migration
        'primary_school_graduation_year' => 'integer',
        'high_school_graduation_year' => 'integer',
        'family_head_dependent_count' => 'integer',
        'father_monthly_income' => 'decimal:2',
        'mother_monthly_income' => 'decimal:2',
        'sibling_monthly_income' => 'decimal:2',
        'spouse_monthly_income' => 'decimal:2',
        'death_benefit_annual_income' => 'decimal:2',
        'rent_payment_amount' => 'decimal:2',
        'car_model_year' => 'integer',
        'other_income_amount' => 'decimal:2',
        'dormitory_monthly_payment' => 'decimal:2',
    ];
    
    // Define default attribute values
    protected $attributes = [
        'are_documents_approved' => false,
        'is_interview_completed' => false,
        'status' => 'awaiting_evaluation',
        'application_date' => null, // Will be set in boot method
    ];
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        // Set default application_date to current date when model is created
        static::creating(function ($application) {
            if (empty($application->application_date)) {
                $application->application_date = now()->format('Y-m-d');
            }
            
            // Ensure required fields are never NULL
            $application->are_documents_approved = $application->are_documents_approved ?? false;
            $application->is_interview_completed = $application->is_interview_completed ?? false;
            $application->status = $application->status ?? 'awaiting_evaluation';
            
            // Generate a unique application ID if needed
            if (empty($application->application_id)) {
                $application->application_id = uniqid('APP-', true);
            }
        });
        
        // Also ensure fields are set when saving
        static::saving(function ($application) {
            // Ensure application_date is set
            if (empty($application->application_date)) {
                $application->application_date = now()->format('Y-m-d');
            }
            
            // Generate a unique application ID if needed
            if (empty($application->application_id)) {
                $application->application_id = uniqid('APP-', true);
            }
        });
    }
    
    /**
     * Get the user that owns the application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the scholarship program associated with this application.
     */
    public function program()
    {
        return $this->belongsTo(ScholarshipProgram::class, 'program_id');
    }
    
    /**
     * Get the admin who pre-approved this application.
     */
    public function preApprovedBy()
    {
        return $this->belongsTo(User::class, 'pre_approved_by');
    }
    
    /**
     * Get the admin who rejected this application.
     */
    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
    
    /**
     * Get the admin who reviewed the documents for this application.
     */
    public function documentReviewedBy()
    {
        return $this->belongsTo(User::class, 'document_reviewed_by');
    }
    
    /**
     * Get the admin who moved this application to the interview pool.
     */
    public function interviewPoolBy()
    {
        return $this->belongsTo(User::class, 'interview_pool_by');
    }
    
    /**
     * Get the admin who accepted this application.
     */
    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
    
    /**
     * Get the admin who gave final acceptance to this application.
     */
    public function finalAcceptanceBy()
    {
        return $this->belongsTo(User::class, 'final_acceptance_by');
    }
    
    /**
     * Get the reviewer of this application.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
    
    /**
     * Get the documents associated with this application.
     */
    public function documents()
    {
        return $this->hasMany(Documents::class, 'application_id');
    }
    
    /**
     * Get the interviews associated with this application.
     */
    public function interviews()
    {
        return $this->hasMany(Interviews::class, 'application_id');
    }
    
    /**
     * Get the scholarships associated with this application.
     */
    public function scholarships()
    {
        return $this->hasMany(Scholarships::class, 'application_id');
    }
    
    /**
     * Check if this application has a valid user relationship
     * 
     * @return bool
     */
    public function getHasValidUserAttribute()
    {
        return !is_null($this->user_id) && User::where('id', $this->user_id)->exists();
    }
    
    /**
     * Get user data as a single string
     *
     * @return string
     */
    public function getUserInfoAttribute()
    {
        if ($this->user) {
            return $this->user->name . ' ' . $this->user->surname . ' (' . $this->user->email . ')';
        }
        
        return 'User Not Found (ID: ' . $this->user_id . ')';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'user_id', 'program_id', 'application_date', 'scholarship_amount'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('application');
    }
}
