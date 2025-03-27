<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLogs extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'causer_type',
        'log_type_id',
        'document_id',
        'document_type_id',
        'interview_id',
        'scholarship_id',
        'program_id',
        'application_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
    ];

    
 /**
     * İşlemi gerçekleştiren kullanıcıyı getirir.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get the log type.
     */
    public function logType(): BelongsTo
    {
        return $this->belongsTo(LogType::class, 'log_type_id');
    }
}
