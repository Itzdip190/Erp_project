<?php

namespace App\Models;

use App\Models\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolSetting extends Model
{
    use HasFactory, BelongsToSchool;

    protected $table = 'school_settings';

    protected $fillable = [
        'school_id',
        'group',
        'key',
        'value',
        'type',
    ];

    /**
     * Get setting value by key for a school.
     */
    public static function getValue(string $key, mixed $default = null, ?int $schoolId = null): mixed
    {
        $schoolId = $schoolId ?: (auth()->check() ? auth()->user()->school_id : null);
        if (!$schoolId) {
            return $default;
        }

        $allSettings = static::getAllForSchool($schoolId);

        if (!array_key_exists($key, $allSettings)) {
            return $default;
        }

        return $allSettings[$key];
    }

    /**
     * Get all settings cached for a school.
     */
    public static function getAllForSchool(int $schoolId): array
    {
        $cacheKey = "school_settings_{$schoolId}";

        return Cache::remember($cacheKey, 3600, function () use ($schoolId) {
            $settings = static::where('school_id', $schoolId)->get();
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }
            return $result;
        });
    }

    /**
     * Set a setting value.
     */
    public static function setValue(string $key, mixed $value, string $group = 'system', string $type = 'string', ?int $schoolId = null): void
    {
        $schoolId = $schoolId ?: (auth()->check() ? auth()->user()->school_id : null);
        if (!$schoolId) {
            return;
        }

        $stringValue = static::formatValueForStorage($value, $type);

        static::updateOrCreate(
            ['school_id' => $schoolId, 'key' => $key],
            ['group' => $group, 'value' => $stringValue, 'type' => $type]
        );

        Cache::forget("school_settings_{$schoolId}");
    }

    /**
     * Cast string database value into appropriate PHP type.
     */
    public static function castValue(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => in_array(strtolower($value), ['1', 'true', 'on', 'yes'], true),
            'integer' => (int) $value,
            'float'   => (float) $value,
            'array', 'json' => json_decode($value, true) ?: [],
            default   => $value,
        };
    }

    /**
     * Format PHP value for storage in database text field.
     */
    public static function formatValueForStorage(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'array', 'json' => is_array($value) ? json_encode($value) : $value,
            default => (string) $value,
        };
    }
}
