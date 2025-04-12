<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;
use Illuminate\Support\Str;

class LaravelNotification extends BaseDatabaseNotification
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'laravel_notifications';
    
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
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Generate UUID if not set
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
            
            // Synchronize is_read with read_at
            if (!is_null($model->read_at) && !$model->is_read) {
                $model->is_read = true;
            } elseif (is_null($model->read_at) && $model->is_read) {
                $model->read_at = now();
            }
        });
        
        static::saving(function ($model) {
            // Synchronize is_read with read_at on save
            if (!is_null($model->read_at) && !$model->is_read) {
                $model->is_read = true;
            } elseif (is_null($model->read_at) && $model->is_read) {
                $model->read_at = now();
            }
        });
    }

    /**
     * Mark the notification as read.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill([
                'read_at' => $this->freshTimestamp(),
                'is_read' => true,
            ])->save();
        }
    }

    /**
     * Mark the notification as unread.
     *
     * @return void
     */
    public function markAsUnread()
    {
        if (!is_null($this->read_at)) {
            $this->forceFill([
                'read_at' => null,
                'is_read' => false,
            ])->save();
        }
    }
} 