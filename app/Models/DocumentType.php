<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_required',
        'allowed_file_types',
        'max_file_size',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_required' => 'boolean',
        'max_file_size' => 'integer',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the documents for this document type.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class, 'document_type_id');
    }
    
    /**
     * Get the program document requirements for this document type.
     */
    public function programRequirements(): HasMany
    {
        return $this->hasMany(ProgramDocumentRequirement::class, 'document_type_id');
    }
} 