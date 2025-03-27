<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Interviews extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'application_id',
        'interviewer_admin_id',
        'interview_date',
        'location',
        'notes',
        'interview_questions',
        'interview_answers',
        'interview_score',
        'interview_result',
        'status',
        'is_online',
        'meeting_link',
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
        'is_online' => 'boolean',
        'notification_sent_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'interview_score' => 'integer',
    ];
    
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
     * Get the admin that conducted the interview.
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'interviewer_admin_id');
    }
}
