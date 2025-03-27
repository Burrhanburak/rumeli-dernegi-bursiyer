<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScholarshipProgram extends Model
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
        'default_amount',
        'application_start_date',
        'application_end_date',
        'program_start_date',
        'program_end_date',
        'is_active',
        'max_recipients',
        'created_by',
        'requirements',
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'default_amount' => 'decimal:2',
        'application_start_date' => 'date',
        'application_end_date' => 'date',
        'program_start_date' => 'date',
        'program_end_date' => 'date',
        'is_active' => 'boolean',
        'max_recipients' => 'integer',
    ];
    
    /**
     * Get the admin who created this program.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    /**
     * Get the applications for this program.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Applications::class, 'program_id');
    }
    
    /**
     * Get the scholarships for this program.
     */
    public function scholarships(): HasMany
    {
        return $this->hasMany(Scholarships::class, 'program_id');
    }
    
    /**
     * Get the document requirements for this program.
     */
    public function documentRequirements(): HasMany
    {
        return $this->hasMany(ProgramDocumentRequirement::class, 'program_id');
    }
}