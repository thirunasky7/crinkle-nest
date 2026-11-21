<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = [];

    protected $casts = ['show_in_profile' => 'boolean', 'is_active' => 'boolean', 'expires_at' => 'date', 'value' => 'decimal:2', 'min_order' => 'decimal:2'];

    /** Returns an error message, or null when the coupon can be applied. */
    public function problemFor(float $subtotal): ?string
    {
        if (! $this->is_active) {
            return 'This code is no longer active.';
        }
        if ($this->expires_at && $this->expires_at->endOfDay()->isPast()) {
            return 'This code has expired.';
        }
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return 'This code has reached its usage limit.';
        }
        if ($subtotal < (float) $this->min_order) {
            return 'Add items worth '.money($this->min_order).' or more to use this code.';
        }

        return null;
    }

    public function discountFor(float $subtotal): float
    {
        $d = $this->type === 'percent' ? round($subtotal * (float) $this->value / 100) : (float) $this->value;

        return min($d, $subtotal);
    }
}
