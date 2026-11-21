<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $guarded = [];

    protected $casts = ['extra' => 'array', 'is_active' => 'boolean'];

    public const PAGES = [
        'home' => 'Home Page',
        'about' => 'About Us Page',
        'contact' => 'Contact Page',
        'menu' => 'Menu Page',
        'cart' => 'Cart & Checkout',
        'orders' => 'My Orders',
        'profile' => 'My Profile',
        'policy' => 'Policy Pages (shared)',
        'global' => 'Global / Layout',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort');
    }

    public function activeItems()
    {
        return $this->items->where('is_active', true)->values();
    }

    /** Read a value from the "extra" key/value bag. */
    public function x(string $key, $default = null)
    {
        $v = ($this->extra ?? [])[$key] ?? null;

        return ($v === null || $v === '') ? $default : $v;
    }

    public static function byKey(string $key): self
    {
        // Loaded once per request (all sections + items in two queries).
        $attrs = request()->attributes;
        if (! $attrs->has('cms.sections')) {
            try {
                $attrs->set('cms.sections', static::with('items')->get()->keyBy('key'));
            } catch (\Throwable $e) {
                $attrs->set('cms.sections', collect());
            }
        }
        $loaded = $attrs->get('cms.sections');

        if ($loaded->has($key)) {
            return $loaded->get($key);
        }

        $empty = new static(['key' => $key, 'is_active' => false, 'extra' => []]);
        $empty->setRelation('items', collect());

        return $empty;
    }
}
