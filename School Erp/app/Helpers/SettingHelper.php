<?php

use App\Services\SettingService;

if (!function_exists('setting')) {
    /**
     * Get or set setting value.
     */
    function setting(string $key, mixed $default = null, ?int $schoolId = null): mixed
    {
        return SettingService::get($key, $default, $schoolId);
    }
}
