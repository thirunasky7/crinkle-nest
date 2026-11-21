<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    protected $casts = ['show_on_home' => 'boolean', 'show_in_collections' => 'boolean', 'is_active' => 'boolean'];

    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('sort');
    }

    public function activeProducts()
    {
        return $this->hasMany(Product::class)->where('is_active', true)->orderBy('sort');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort');
    }

    public function href(): string
    {
        return $this->link ? link_to_url($this->link) : route('menu.category', $this->slug);
    }
}
