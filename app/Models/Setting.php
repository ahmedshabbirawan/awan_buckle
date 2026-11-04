<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * Cache key for storing all system settings.
     */
    public const CACHE_KEY = 'system_settings';

    /**
     * Essential system settings keys that should not be deleted.
     */
    public const ESSENTIAL_KEYS = [
        'business_name',
        'business_address',
        'business_phone',
        'business_email',
        'business_logo',
        'business_website',
        'business_tax_number',
        'currency_symbol',
        'currency_code',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Model boot observer to automatically keep cache synchronized.
     */
    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }

    /**
     * Check whether a given setting key or this model's key is essential.
     *
     * @param string|null $key
     * @return bool
     */
    public static function isEssentialKey(?string $key): bool
    {
        if (empty($key)) {
            return false;
        }

        return in_array($key, self::ESSENTIAL_KEYS, true);
    }

    /**
     * Check if the current instance is an essential setting.
     *
     * @return bool
     */
    public function isEssential(): bool
    {
        return self::isEssentialKey($this->key);
    }

    /**
     * Retrieve all system settings as a key-value array from cache (or database).
     *
     * @return array<string, mixed>
     */
    public static function getAllSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::query()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Retrieve a setting value by its key with cache backing.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllSettings();

        if (array_key_exists($key, $settings) && !is_null($settings[$key])) {
            return $settings[$key];
        }

        return $default;
    }

    /**
     * Set a setting value (update or create) and refresh cache.
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public static function set(string $key, $value): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        self::clearCache();

        return $setting;
    }

    /**
     * Clear / invalidate the system settings cache.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
