<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\DeliverySlot;
use Illuminate\Database\Seeder;

class ShopSeeder extends Seeder
{
    public function run(): void
    {
        Coupon::query()->delete();
        Coupon::create(['code' => 'SWEETBIRTHDAY', 'description' => 'Complimentary ₹100 off your birthday batch', 'icon' => 'redeem', 'type' => 'fixed', 'value' => 100, 'min_order' => 500]);
        Coupon::create(['code' => 'FLOURPOWER', 'description' => '15% off your next order', 'icon' => 'percent', 'type' => 'percent', 'value' => 15, 'min_order' => 300]);

        DeliverySlot::query()->delete();
        DeliverySlot::create(['day_label' => 'Today', 'title' => 'Evening Slot', 'time_range' => '4:00 PM – 7:00 PM', 'badge' => 'Ovens clearing', 'sort' => 0]);
        DeliverySlot::create(['day_label' => 'Tomorrow', 'title' => 'Dawn Pull', 'time_range' => '9:00 AM – 12:00 PM', 'badge' => 'Breakfast ready', 'sort' => 1]);
        DeliverySlot::create(['day_label' => 'Bespoke', 'title' => 'Custom Day', 'time_range' => 'Pick Weekend/Party', 'badge' => 'Choose date', 'is_custom' => true, 'sort' => 2]);
    }
}
