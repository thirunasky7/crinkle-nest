<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'price' => 'decimal:2',
        'is_trending' => 'boolean',
        'is_recommended' => 'boolean',
        'is_upsell' => 'boolean',
        'is_active' => 'boolean',
    ];

    public const BADGE_STYLES = [
        'secondary' => 'Caramel',
        'secondary-container' => 'Light caramel',
        'tertiary' => 'Dark cocoa',
        'primary' => 'Chocolate',
        'light' => 'Cream',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort');
    }

    public function badgeClasses(): string
    {
        return match ($this->badge_style) {
            'secondary-container' => 'bg-secondary-container text-on-secondary-container font-semibold',
            'tertiary' => 'bg-tertiary text-on-tertiary',
            'primary' => 'bg-primary text-on-primary',
            'light' => 'bg-surface-container-lowest/90 backdrop-blur-sm text-primary font-bold',
            default => 'bg-secondary text-on-secondary',
        };
    }

    public function whatsappLink(): string
    {
        $template = $this->whatsapp_message ?: setting('whatsapp_product_message', 'Hi {site}! I would like to order {product}. Please share the details.');

        return wa_link(strtr($template, ['{site}' => setting('site_name', 'Crinkle Nest'), '{product}' => $this->name]));
    }
}
