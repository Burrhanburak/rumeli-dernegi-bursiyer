<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documents extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'document_type_id',
        'application_id',
        'name',
        'description',
        'file_path',
        'status',
        'reviewed_at',
        'reviewed_by',
        'reason',
        'admin_comment',
        'is_verified',
        'verification_date',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
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
        static::creating(function ($document) {
            // Eğer application_id zaten ayarlanmışsa, bir şey yapmaya gerek yok
            if (!empty($document->application_id)) {
                return;
            }
            
            // Eğer user_id varsa, bu kullanıcının bir başvurusunu bulalım
            if (!empty($document->user_id)) {
                $application = \App\Models\Applications::where('user_id', $document->user_id)
                    ->latest()
                    ->first();
                
                if ($application) {
                    $document->application_id = $application->id;
                }
            }
        });
        
        // Güncelleme durumunda da bu kontrolü yapalım
        static::updating(function ($document) {
            // Eğer application_id zaten ayarlanmışsa, bir şey yapmaya gerek yok
            if (!empty($document->application_id)) {
                return;
            }
            
            // Eğer user_id varsa, bu kullanıcının bir başvurusunu bulalım
            if (!empty($document->user_id)) {
                $application = \App\Models\Applications::where('user_id', $document->user_id)
                    ->latest()
                    ->first();
                
                if ($application) {
                    $document->application_id = $application->id;
                }
            }
        });
    }
    
    /**
     * Get the user that owns the document.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Get the document type that the document belongs to.
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
    
    /**
     * Get the application that the document belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(Applications::class, 'application_id');
    }
    
    /**
     * Get the admin that reviewed the document.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
