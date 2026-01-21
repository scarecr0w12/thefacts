<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LLMConfig extends Model
{
    use HasFactory;

    protected $table = 'llm_configs';

    protected $fillable = [
        'provider',
        'api_key',
        'model',
        'enabled',
        'max_tokens',
        'temperature',
        'system_prompt',
        'cost_per_1k_tokens',
    ];

    protected $hidden = ['api_key'];

    protected $casts = [
        'enabled' => 'boolean',
        'max_tokens' => 'integer',
        'temperature' => 'float',
        'cost_per_1k_tokens' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function usages()
    {
        return $this->hasMany(LLMUsage::class, 'provider', 'provider');
    }
}
