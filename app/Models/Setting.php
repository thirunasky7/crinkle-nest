<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $guarded = [];

    public const CACHE_KEY = 'site_settings';

    public const GROUPS = [
        'general' => 'General & Branding',
        'contact' => 'Contact & Location',
        'whatsapp' => 'WhatsApp',
        'social' => 'Social Links',
        'footer' => 'Footer',
        'checkout' => 'Checkout & Delivery',
        'rewards' => 'Rewards & Loyalty',
        'theme' => 'Theme Colors',
        'seo' => 'SEO & Meta',
    ];

    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return static::query()->pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    public static function get(string $key, $default = null)
    {
        $value = static::allCached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flush());
        static::deleted(fn () => static::flush());
    }
}
