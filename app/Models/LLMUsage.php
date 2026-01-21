<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LLMUsage extends Model
{
    use HasFactory;

    protected $table = 'llm_usages';

    protected $fillable = [
        'user_id',
        'provider',
        'model',
        'action',
        'input_tokens',
        'output_tokens',
        'cost',
        'response_time_ms',
        'success',
        'error_message',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'cost' => 'float',
        'response_time_ms' => 'integer',
        'success' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalTokens()
    {
        return $this->input_tokens + $this->output_tokens;
    }
}
