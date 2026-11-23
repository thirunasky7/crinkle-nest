<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/** Adds the customer order-cancellation settings (Admin → Settings → Checkout & Delivery). */
return new class extends Migration
{
    protected array $rows = [
        ['allow_customer_cancel', 'Let customers cancel orders before preparation starts', '1', 'boolean', 'Customers can cancel only while an order is “Order Confirmed”. Once you set it to Baking or later, cancelling is locked.', 50],
        ['cancel_note', 'Note shown with the Cancel button', 'You can cancel until our bakers start preparing your order.', 'text', null, 51],
        ['cancel_locked_note', 'Note when cancelling is locked', 'Preparation has started — this order can no longer be cancelled.', 'text', null, 52],
    ];

    public function up(): void
    {
        foreach ($this->rows as [$key, $label, $value, $type, $help, $sort]) {
            DB::table('settings')->insertOrIgnore([
                'key' => $key, 'label' => $label, 'value' => $value, 'type' => $type, 'help' => $help,
                'group' => 'checkout', 'sort' => $sort, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
        Cache::forget('site_settings');
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', array_column($this->rows, 0))->delete();
        Cache::forget('site_settings');
    }
};
