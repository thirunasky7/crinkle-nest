<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(['email' => 'admin@crinklenest.com'], ['name' => 'Admin', 'password' => 'admin12345']);
        $admin->forceFill(['is_admin' => true])->save();

        // Demo customer to preview the account pages (password: customer123)
        $demo = User::updateOrCreate(['email' => 'ananya@example.com'], [
            'name' => 'Ananya', 'last_name' => 'Sharma', 'phone' => '+91 98765 12345',
            'password' => 'customer123', 'birthday' => '14th August', 'points' => 450,
            'location_label' => 'Bangalore Heritage Quarter', 'avatar' => DatabaseSeeder::img('profile', 1),
        ]);
        $demo->addresses()->delete();
        $demo->addresses()->create(['label' => 'Home', 'icon' => 'home', 'line1' => 'Flat 402, Magnolia Enclave, 7th Main,', 'line2' => 'Heritage Quarter, Bangalore, Karnataka - 560038', 'recipient' => 'Ananya', 'phone' => '+91 98765 12345', 'is_default' => true]);
        $demo->addresses()->create(['label' => 'Design Studio', 'icon' => 'apartment', 'line1' => 'Studio 12, Indiranagar 100ft Road,', 'line2' => 'Near Metro Pillar 84, Bangalore - 560038', 'note' => 'Gate drop-off with security approved']);

        $demo->orders()->delete();
        $find = fn ($name) => Product::where('name', $name)->first();
        $make = function (array $attrs, array $lines, $createdAt) use ($demo) {
            $subtotal = collect($lines)->sum(fn ($l) => $l[1] * $l[2]);
            $order = Order::create($attrs + [
                'order_number' => 'TMP-'.uniqid(), 'user_id' => $demo->id, 'name' => 'Ananya Sharma', 'phone' => '+91 98765 12345',
                'email' => 'ananya@example.com', 'address' => 'Flat 402, Magnolia Enclave, 7th Main Road, Heritage Quarter', 'city' => 'Bangalore - 560038',
                'subtotal' => $subtotal, 'delivery_fee' => 0, 'discount' => 0, 'total' => $subtotal, 'payment_method' => 'whatsapp',
            ]);
            foreach ($lines as [$p, $qty, $price, $detail]) {
                $order->items()->create(['product_id' => $p?->id, 'name' => $p?->name ?? 'Item', 'detail' => $detail, 'image' => $p?->image, 'price' => $price, 'qty' => $qty, 'total' => $price * $qty]);
            }
            $order->forceFill(['created_at' => $createdAt, 'updated_at' => $createdAt])->saveQuietly();

            return $order;
        };

        $make(['status' => 'delivered', 'payment_status' => 'paid', 'payment_note' => 'UPI • GPay', 'rating' => 5, 'review' => 'Flakiest crust ever!', 'delivered_at' => now()->subDays(30)], [
            [$find('Assorted Celebration Dessert Box'), 1, 750, '4x Tarts, 4x Crinkles, 2x Macarons'],
            [$find('Double Chocolate Brownie'), 1, 170, '70% Callebaut dark ganache'],
        ], now()->subDays(30));
        $make(['status' => 'delivered', 'payment_status' => 'paid', 'label' => 'Valentine Hearth Special', 'delivered_at' => now()->subDays(40)], [
            [$find('Red Velvet Brownie'), 4, 170, 'Tangy cream cheese swirl'],
            [$find('Jeera Coin Biscuit'), 1, 130, '200g artisanal jar'],
        ], now()->subDays(40));
        $make(['status' => 'baking', 'payment_status' => 'paid', 'payment_note' => 'UPI • GPay', 'target_arrival' => 'Today by 3:30 PM', 'slot_label' => 'Today - Evening Slot (4:00 PM – 7:00 PM)'], [
            [$find('Signature Crinkle Cookie Box (Pack of 6)'), 1, 380, 'Valrhona dark chocolate'],
            [$find('Fudgy Walnut Brownie Slab'), 1, 420, 'Single-origin roasted walnuts'],
            [$find('Classic Butter Coin Biscuits Tin'), 2, 260, 'Slow-churned butter recipe'],
        ], now()->subHours(2));
    }
}
