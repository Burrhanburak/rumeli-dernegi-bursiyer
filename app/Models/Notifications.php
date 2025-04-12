<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Notifications extends Model
{
    use HasFactory;
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
  
        'notifiable_id',
        'notifiable_type',
        'title',
        'message',
        'type',
        'read_at',
        'is_read',
        'application_id',
        'document_id',
        'interview_id',
        'email_sent',
        'email_sent_at',
        'data',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];
    
    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
    
    /**
     * Get the parent notifiable model (user or admin).
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the application that the notification is about.
     */
    public function application()
    {
        return $this->belongsTo(Applications::class, 'application_id');
    }
    
    /**
     * Get the document that the notification is about.
     */
    public function document()
    {
        return $this->belongsTo(Documents::class, 'document_id');
    }
    
    /**
     * Get the interview that the notification is about.
     */
    public function interview()
    {
        return $this->belongsTo(Interviews::class, 'interview_id');
    }
    
    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }
}
