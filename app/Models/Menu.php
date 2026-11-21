<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];

    protected $casts = ['new_tab' => 'boolean', 'is_active' => 'boolean'];

    public const LOCATIONS = [
        'header' => 'Header navigation',
        'footer_bakes' => 'Footer: Curated Bakes column',
        'footer_quick' => 'Footer: Quick Links column',
        'footer_bottom' => 'Footer: bottom bar links',
    ];

    public static function at(string $location)
    {
        $attrs = request()->attributes;
        if (! $attrs->has('cms.menus')) {
            try {
                $attrs->set('cms.menus', static::where('is_active', true)->orderBy('sort')->get()->groupBy('location'));
            } catch (\Throwable $e) {
                $attrs->set('cms.menus', collect());
            }
        }

        return $attrs->get('cms.menus')->get($location, collect());
    }

    public function href(): string
    {
        return link_to_url($this->url);
    }

    public function isCurrent(): bool
    {
        $match = $this->match;
        if (! $match) {
            if (str_contains((string) $this->url, '#')) {
                return false;
            }
            $match = trim(parse_url((string) $this->url, PHP_URL_PATH) ?? '', '/');
        }
        if ($match === '' || $match === '/') {
            return request()->path() === '/';
        }

        return request()->is($match) || request()->is($match.'/*');
    }
}
