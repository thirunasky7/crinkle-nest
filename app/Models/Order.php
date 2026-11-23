<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'custom_date' => 'date',
        'delivered_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public const STATUSES = [
        'confirmed' => 'Order Confirmed',
        'baking' => 'Baking in Hearth',
        'packing' => 'Packing',
        'dispatched' => 'Dispatched',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ];

    public const ACTIVE_STATUSES = ['confirmed', 'baking', 'packing', 'dispatched'];

    public const PAYMENT_STATUSES = ['pending' => 'Pending', 'paid' => 'Paid', 'refunded' => 'Refunded'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    /** 0..3 for the four tracker steps, 4 when delivered. */
    public function stepIndex(): int
    {
        if ($this->status === 'delivered') {
            return 4;
        }
        $i = array_search($this->status, self::ACTIVE_STATUSES, true);

        return $i === false ? 0 : $i;
    }

    /** Customers may cancel only until the bakers start preparing (status moves past "confirmed"). */
    public function canBeCancelled(): bool
    {
        return $this->status === 'confirmed' && setting('allow_customer_cancel', '1') === '1';
    }

    /** True once preparation has started and the order can no longer be cancelled by the customer. */
    public function cancellationLocked(): bool
    {
        return in_array($this->status, ['baking', 'packing', 'dispatched'], true);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    protected static function booted(): void
    {
        static::created(function (Order $order) {
            if (! $order->order_number || str_starts_with($order->order_number, 'TMP-')) {
                $order->order_number = setting('order_prefix', 'CN-').(88000 + $order->id);
                $order->saveQuietly();
            }
        });
    }
}
