<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifications extends Model
{
    use HasFactory;
    
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
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
    ];
    
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
}
