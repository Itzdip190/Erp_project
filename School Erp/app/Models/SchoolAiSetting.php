<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolAiSetting extends Model
{
    use BelongsToSchool;

    protected $table = 'school_ai_settings';

    protected $fillable = [
        'school_id',
        'enabled',
        'api_key',
        'ai_model',
        'chatbot_name',
        'ai_provider',
        'max_tokens',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'max_tokens' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get masked API key for display.
     */
    public function getMaskedKeyAttribute(): string
    {
        if (!$this->api_key) return '';
        $key = $this->api_key;
        if (strlen($key) <= 8) return str_repeat('*', strlen($key));
        return substr($key, 0, 4) . str_repeat('*', strlen($key) - 8) . substr($key, -4);
    }
}
