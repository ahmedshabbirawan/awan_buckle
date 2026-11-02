<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get / retrieve a system setting by its key from cache.
     *
     * Usage:
     * - setting('business_name') => returns value from cache or null
     * - setting('business_name', 'Default Name') => returns value or default
     * - setting() => returns array of all settings
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting(?string $key = null, $default = null)
    {
        if (is_null($key)) {
            return Setting::getAllSettings();
        }

        return Setting::get($key, $default);
    }
}
