<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'normalized_text', 'context_url', 'created_by', 'verdict', 'confidence'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evidence(): HasMany
    {
        return $this->hasMany(Evidence::class);
    }

    public function getReadyEvidenceAttribute()
    {
        return $this->evidence()->where('status', 'READY')->get();
    }
}
