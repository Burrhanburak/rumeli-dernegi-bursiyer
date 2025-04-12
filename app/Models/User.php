<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Arr;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logOnly(['name', 'email', 'is_admin'])
    //         ->logOnlyDirty()
    //         ->dontLogIfAttributesChangedOnly(['updated_at'])
    //         ->dontSubmitEmptyLogs();
    // }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'is_admin',
        'national_id', // tc_kimlik_no
        'name',
        'surname',
        'birth_date', // dogum_tarihi
        'birth_place', // dogum_yeri
        'nationality', // uyrugu
        'gender', // cinsiyet
        'phone',
        'email',
        'address',
        'password_confirmation',
        'city', // sehir
        'postal_code', // posta_kodu
        'physical_disability', // beden_ozru
        'registered_province', // nufusa_kayitli_il
        'registered_district', // nufusa_kayitli_ilce
        'school_name', // okul_adi
        'school_department', // okul_bolumu
        'grade', // sinif
        'enrollment_year', // giris_yili
        'scholarship_rate', // burs_orani
        'university_entrance_score', // universiteye_giris_puani
        'student_id', // ogrenci_numarasi
        'primary_school_name', // ilkokul_adi
        'primary_school_graduation_year', // ilkokul_mezuniyet_yili
        'high_school_name', // lise_adi
        'high_school_graduation_year', // lise_mezuniyet_yili
        'father_name', // baba_adi
        'father_surname', // baba_soyadi
        'father_birth_year', // baba_dogum_yili
        'father_birth_place', // baba_dogum_yeri
        'father_occupation', // baba_meslegi
        'father_death_year', // baba_vefat_yili
        'mother_name', // anne_adi
        'mother_surname', // anne_soyadi
        'mother_birth_year', // anne_dogum_yili
        'mother_birth_place', // anne_dogum_yeri
        'mother_occupation', // anne_meslegi
        'mother_death_year', // anne_vefat_yili
        'sibling1_name', // kardes1_adi
        'sibling1_surname', // kardes1_soyadi
        'sibling1_age', // kardes1_yasi
        'sibling1_education', // kardes1_ogrenim_durumu
        'sibling2_name', // kardes2_adi
        'sibling2_surname', // kardes2_soyadi
        'sibling2_age', // kardes2_yasi
        'sibling2_education', // kardes2_ogrenim_durumu
        'sibling3_name', // kardes3_adi
        'sibling3_surname', // kardes3_soyadi
        'sibling3_age', // kardes3_yasi
        'sibling3_education', // kardes3_ogrenim_durumu
        'sibling4_name', // kardes4_adi
        'sibling4_surname', // kardes4_soyadi
        'sibling4_age', // kardes4_yasi
        'sibling4_education', // kardes4_ogrenim_durumu
        'sibling5_name', // kardes5_adi
        'sibling5_surname', // kardes5_soyadi
        'sibling5_age', // kardes5_yasi
        'sibling5_education', // kardes5_ogrenim_durumu
        'family_head_dependent_count', // aile_reisinin_baktigi_fert_sayisi
        'family_subsistence_responsibility', // ailenin_geçim_sorumlulugu
        'family_residence_address', // aile_ikametgah_adresi
        'residence_province', // ikametgah_ili
        'residence_district', // ikametgah_ilcesi
        'family_phone', // aile_telefon
        'resides_with_family', // ailenin_yaninda_kalarak_okula_devam
        'dormitory_monthly_payment', // yurtta_kaliyorsa_aylik_odeme
        'education_residence_address', // ogrenim_sirasinda_kaldigi_adres
        'education_province', // ogrenim_ili
        'education_district', // ogrenim_ilcesi
        'father_monthly_income', // baba_aylik_net_gelir
        'mother_monthly_income', // anne_aylik_net_gelir
        'sibling_monthly_income', // kardes_aylik_net_gelir
        'spouse_monthly_income', // es_aylik_net_gelir
        'death_benefit_annual_income', // vefat_ile_bagli_maas_yillik_net_gelir
        'family_owns_house', // ailenin_evi_var_mi
        'rent_payment_amount', // kirada_oturuyor_ise_kira_miktari
        'real_estate_value_and_income', // gayrimenkul_degeri_ve_geliri
        'car_model_year', // otomobil_model_yili
        'other_income_amount', // baska_gelir_miktari
        'field_selection', // brans_secimi
        'club_membership', // kulup_uyeligi
        'library_usage', // kutuphane_kullanimi
        'hobby', // hobi
        'scholarship_commitment', // burs_verme_sozu
        'social_media_usage', // sosyal_medya_kullanimi
        'social_responsibility_project', // sosyal_sorumluluk_projesi
        'professional_success_opinion', // is_hayatinda_basari_gorusu
        'post_graduation_goal', // mezuniyet_sonrasi_hedef
        'reference1_name', // referans1_adi
        'reference1_phone', // referans1_tel
        'reference2_name', // referans2_adi
        'reference2_phone', // referans2_tel
        'receiving_other_scholarship', // baska_burs_kredi_alimi
        'other_scholarship_institution', // baska_burs_kredi_kurumu
        'image',
        'status',
        'iban',
        'terms',
        'is_active',
        'password',
        'email_verified_at',
        'otp_code',
        'otp_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'birth_date' => 'date', // dogum_tarihi
            'terms' => 'boolean',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            // If user is an admin and email is not verified, automatically verify it
            if ($user->is_admin && is_null($user->email_verified_at)) {
                $user->email_verified_at = now();
            }
        });
        
        // Clean up expired OTP session flags
        self::cleanupExpiredOtpFlags();
    }

    /**
     * Clean up expired OTP session flags
     */
    protected static function cleanupExpiredOtpFlags(): void
    {
        $session = session();
        
        foreach ($session->all() as $key => $value) {
            if (strpos($key, 'otp_sent_') === 0) {
                $expiresKey = $key . '_expires';
                
                if ($session->has($expiresKey) && $session->get($expiresKey) < now()->timestamp) {
                    $session->forget($key);
                    $session->forget($expiresKey);
                }
            }
        }
    }

    /**
     * Get the applications for the user.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Applications::class, 'user_id');
    }

    /**
     * Get the documents for the user.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class, 'user_id');
    }

    /**
     * Get the interviews for the user.
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interviews::class, 'user_id');
    }

    /**
     * Get the scholarships for the user.
     */
    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarships::class, 'user_id');
    }

    /**
     * Get the entity's notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(\App\Models\LaravelNotification::class, 'notifiable')
                ->orderBy('created_at', 'desc');
    }

    /**
     * Get the entity's custom notifications.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function customNotifications()
    {
        return $this->morphMany(\App\Models\Notifications::class, 'notifiable')
                ->orderBy('created_at', 'desc');
    }

    /**
     * Get the applications reviewed by this admin user.
     */
    public function reviewedApplications(): HasMany
    {
        return $this->hasMany(Applications::class, 'pre_approved_by')
            ->orWhere('rejected_by', $this->id)
            ->orWhere('document_reviewed_by', $this->id)
            ->orWhere('interview_pool_by', $this->id)
            ->orWhere('accepted_by', $this->id)
            ->orWhere('final_acceptance_by', $this->id);
    }

    /**
     * Get the documents reviewed by this admin user.
     */
    public function reviewedDocuments(): HasMany
    {
        return $this->hasMany(Documents::class, 'reviewed_by');
    }

    /**
     * Get the interviews conducted by this admin user.
     */
    public function conductedInterviews(): HasMany
    {
        return $this->hasMany(Interviews::class, 'interviewer_admin_id');
    }

    /**
     * Get the user's full name.
     */
    public function getFullNameAttribute(): string
    {
        // Use name/surname if available
        if (!empty($this->name) && !empty($this->surname)) {
            return "{$this->name} {$this->surname}";
        }
        return "{$this->name} {$this->surname}";
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        // Use name/surname if available, otherwise try ad/soyad
        $fullName = !empty($this->name) ? $this->name . ' ' . $this->surname : $this->ad . ' ' . $this->soyad;
        
        return Str::of($fullName)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Check if the user can access the admin panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->is_admin && $this->hasVerifiedEmail();
        }
    
        return true;
    }
    public function sendEmailVerificationNotification(): void
    {
        // Use a session flag to prevent duplicate emails
        $sessionKey = 'otp_sent_' . $this->id;
        
        if (session()->has($sessionKey)) {
            \Illuminate\Support\Facades\Log::info('Duplicate OTP prevented', [
                'user_id' => $this->id,
                'email' => $this->email
            ]);
            return;
        }
        
        // Debug logging
        \Illuminate\Support\Facades\Log::info('OTP Verification Triggered', [
            'user_id' => $this->id,
            'email' => $this->email,
            'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5)
        ]);
        
        // Generate OTP
        $otp = rand(100000, 999999);
        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(10);
        $this->save();

        // Send OTP notification only
        $this->notify(new \App\Notifications\OtpNotification($otp));
        
        // Set the session flag for 1 minute to prevent duplicate emails
        session()->put($sessionKey, true);
        session()->put($sessionKey . '_expires', now()->addMinute()->timestamp);
    }

    public function verifyOtp($otp)
    {
        if ($this->otp_code === $otp && now()->lessThan($this->otp_expires_at)) {
            $this->email_verified_at = now();
            $this->otp_code = null;
            $this->otp_expires_at = null;
            $this->save();
            return true;
        }
        return false;
    }
    /**
     * Override the default verification notification to use our custom Turkish notification
     */
    // public function sendEmailVerificationNotification(): void
    // {
    //     // Skip if already verified
    //     if ($this->hasVerifiedEmail()) {
    //         return;
    //     }
        
    //     // Set locale to Turkish
    //     app()->setLocale('tr');
        
    //     // Log for debugging purposes
    //     \Illuminate\Support\Facades\Log::info('Sending verification email to: ' . $this->email);
        
    //     // Create and send notification
    //     $notification = app(\App\Notifications\VerifyTestNotification::class, ['token' => '']);
    //     $notification->url = \Filament\Facades\Filament::getVerifyEmailUrl($this);
        
    //     $this->notify($notification);
    // }
}