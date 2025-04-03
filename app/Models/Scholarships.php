<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Scholarships extends Model
{
    use HasFactory, LogsActivity;
    
    /**
     * Status enum değerleri
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_SUSPENDED = 'suspended';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TERMINATED = 'terminated';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'program_id',
        'application_id',
        'approved_by',
        'last_updated_by',
        'start_date',
        'amount',
        'end_date',
        'status',
        'status_reason',
        'notes',
        'name',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];
    
    /**
     * ActivityLog options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['user_id', 'program_id', 'amount', 'status', 'start_date', 'end_date'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('scholarship');
    }
    
    /**
     * Get the user that received this scholarship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the program that this scholarship belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(ScholarshipProgram::class, 'program_id');
    }
    
    /**
     * Get the application that this scholarship was awarded for.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Applications::class, 'application_id');
    }
    
    /**
     * Get the admin who approved this scholarship.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Get the admin who last updated this scholarship.
     */
    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }
    
    /**
     * Scope a query to only include active scholarships.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
    
    /**
     * Scope a query to only include suspended scholarships.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', self::STATUS_SUSPENDED);
    }
    
    /**
     * Scope a query to only include completed scholarships.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
    
    /**
     * Scope a query to only include terminated scholarships.
     */
    public function scopeTerminated($query)
    {
        return $query->where('status', self::STATUS_TERMINATED);
    }
}
