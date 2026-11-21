<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/** Bottom tab bar shown on phones. Safe to re-run: only seeds when empty. */
class MobileTabbarSeeder extends Seeder
{
    public function run(): void
    {
        if (Menu::where('location', 'mobile_tabbar')->exists()) {
            return;
        }
        $tabs = [
            ['Home', 'home', '/', '/'],
            ['Menu', 'restaurant_menu', '/menu', 'menu'],
            ['Basket', 'shopping_basket', '/cart', 'cart'],
            ['Orders', 'receipt_long', '/orders', 'orders'],
            ['Profile', 'person', '/profile', 'profile'],
        ];
        foreach ($tabs as $i => [$label, $icon, $url, $match]) {
            Menu::create(['location' => 'mobile_tabbar', 'label' => $label, 'icon' => $icon, 'url' => $url, 'match' => $match, 'sort' => $i]);
        }
    }
}
