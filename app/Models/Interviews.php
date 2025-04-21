<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interviews extends Model
{
    use HasFactory;
    
    // Make sure the table name matches what's expected in the database
    protected $table = 'interviews';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'application_id',
        'user_id',
        'interviewer_admin_id',
        'interview_date',
        'scheduled_date',
        'completion_date',
        'location',
        'is_online',
        'meeting_link',
        'status', // scheduled, completed, canceled, rescheduled, no_show, awaiting_schedule
        'notes',
        'feedback',
        'score',
        'interview_score',
        'interview_result', // passed, failed, pending
        'notification_sent_at',
        'reminder_sent_at',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'interview_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'completion_date' => 'datetime',
        'is_online' => 'boolean',
        'notification_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'interview_score' => 'integer',
    ];
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        // Otomatik olarak application_id ataması yapalım
        static::creating(function ($interview) {
            // Eğer application_id zaten ayarlanmışsa, bir şey yapmaya gerek yok
            if (!empty($interview->application_id)) {
                return;
            }
            
            // Eğer user_id varsa, bu kullanıcının bir başvurusunu bulalım
            if (!empty($interview->user_id)) {
                $application = \App\Models\Applications::where('user_id', $interview->user_id)
                    ->latest()
                    ->first();
                
                if ($application) {
                    $interview->application_id = $application->id;
                }
            }
        });
        
        // Güncelleme durumunda da bu kontrolü yapalım
        static::updating(function ($interview) {
            // Eğer application_id zaten ayarlanmışsa, bir şey yapmaya gerek yok
            if (!empty($interview->application_id)) {
                return;
            }
            
            // Eğer user_id varsa, bu kullanıcının bir başvurusunu bulalım
            if (!empty($interview->user_id)) {
                $application = \App\Models\Applications::where('user_id', $interview->user_id)
                    ->latest()
                    ->first();
                
                if ($application) {
                    $interview->application_id = $application->id;
                }
            }
        });
    }
    
    /**
     * Get the user that the interview is for.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the application that the interview is for.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Applications::class, 'application_id');
    }
    
    /**
     * Alias for application() to support plural naming convention
     */
    public function applications(): BelongsTo
    {
        return $this->belongsTo(Applications::class, 'application_id');
    }
    
    /**
     * Get the admin that conducted the interview.
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_admin_id');
    }
    
    /**
     * Define the inverse relationship for the interview_score column.
     *
     * @param string $column
     * @return string|BelongsTo
     */
    public function inverseRelationship(string $column): BelongsTo
    {
        return $this->application();
    }
}
