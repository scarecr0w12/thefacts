<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidence';

    protected $fillable = [
        'claim_id',
        'url',
        'title',
        'publisher_domain',
        'published_at',
        'stance',
        'excerpt',
        'status',
        'error',
        'created_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function getUserVote($userId)
    {
        return $this->votes()->where('user_id', $userId)->first();
    }

    public function getVoteSum()
    {
        return $this->votes()->sum('value');
    }
}
