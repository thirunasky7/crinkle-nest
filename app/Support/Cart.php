<?php

namespace App\Support;

use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session-backed shopping basket.
 * Stored as ['items' => [product_id => qty], 'coupon' => CODE].
 */
class Cart
{
    protected const KEY = 'cart';

    public static function raw(): array
    {
        return session(self::KEY, ['items' => [], 'coupon' => null]);
    }

    protected static function put(array $cart): void
    {
        session([self::KEY => $cart]);
    }

    public static function add(int $productId, int $qty = 1): void
    {
        $cart = static::raw();
        $cart['items'][$productId] = min(99, ($cart['items'][$productId] ?? 0) + max(1, $qty));
        static::put($cart);
    }

    public static function set(int $productId, int $qty): void
    {
        $cart = static::raw();
        if ($qty <= 0) {
            unset($cart['items'][$productId]);
        } else {
            $cart['items'][$productId] = min(99, $qty);
        }
        static::put($cart);
    }

    public static function setCoupon(?string $code): void
    {
        $cart = static::raw();
        $cart['coupon'] = $code ? strtoupper($code) : null;
        static::put($cart);
    }

    public static function clear(): void
    {
        session()->forget(self::KEY);
    }

    public static function count(): int
    {
        return (int) array_sum(static::raw()['items'] ?? []);
    }

    /** @return Collection<int, object{product: Product, qty: int, total: float}> */
    public static function lines(): Collection
    {
        $items = static::raw()['items'] ?? [];
        if (! $items) {
            return collect();
        }
        $products = Product::whereIn('id', array_keys($items))->where('is_active', true)->get()->keyBy('id');

        return collect($items)->filter(fn ($q, $id) => $products->has($id))->map(function ($qty, $id) use ($products) {
            $p = $products[$id];

            return (object) ['product' => $p, 'qty' => (int) $qty, 'total' => (float) $p->price * (int) $qty];
        })->values();
    }

    public static function summary(): array
    {
        $lines = static::lines();
        $subtotal = (float) $lines->sum('total');
        $threshold = (float) setting('free_delivery_threshold', 1500);
        $fee = $subtotal > 0 && ($threshold <= 0 || $subtotal < $threshold) ? (float) setting('delivery_fee', 50) : 0.0;

        $coupon = null;
        $discount = 0.0;
        $couponError = null;
        if ($code = static::raw()['coupon'] ?? null) {
            $coupon = Coupon::where('code', $code)->first();
            if (! $coupon) {
                $couponError = 'Code not found.';
            } elseif ($err = $coupon->problemFor($subtotal)) {
                $couponError = $err;
                $coupon = null;
            } else {
                $discount = $coupon->discountFor($subtotal);
            }
        }

        return [
            'lines' => $lines,
            'count' => (int) $lines->sum('qty'),
            'subtotal' => $subtotal,
            'delivery_fee' => $fee,
            'threshold' => $threshold,
            'remaining_for_free' => max(0, $threshold - $subtotal),
            'progress' => $threshold > 0 ? min(100, (int) round($subtotal / $threshold * 100)) : 100,
            'coupon' => $coupon,
            'coupon_code' => static::raw()['coupon'] ?? null,
            'coupon_error' => $couponError,
            'discount' => $discount,
            'total' => max(0, $subtotal + $fee - $discount),
        ];
    }
}
