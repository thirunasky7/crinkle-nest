<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::truncate();
        $menus = [
            'header' => [
                ['Home', '/', '/'],
                ['Menu', '/menu', 'menu'],
                ['Trending', '/#trending-section', null],
                ['About Us', '/about', 'about'],
                ['Contact', '/contact', 'contact'],
                ['Orders', '/orders', 'orders'],
            ],
            'footer_bakes' => [
                ['Signature Crinkle Cookies', '/menu/signature-boxes'],
                ['Fudgy Brownies', '/menu/brownie'],
                ['Coin Biscuits', '/menu/coin-biscuit'],
                ['Bespoke Celebration Cakes', '{whatsapp:Hi Crinkle Nest! I would like to order a bespoke celebration cake.}', null, true],
                ['Pastries & Pantry', '/menu/pastries-pantry'],
            ],
            'footer_quick' => [
                ['Our Philosophy', '/about'],
                ['Order History & Tracking', '/orders'],
                ['My Account & Preferences', '/profile'],
                ['Contact & Custom Inquiry', '/contact'],
                ['Bakehouse FAQs & Terms', '/page/terms-and-conditions'],
            ],
            'footer_bottom' => [
                ['Terms & Conditions', '/page/terms-and-conditions'],
                ['Privacy Policy', '/page/privacy-policy'],
                ['FSSAI Certified', '/page/terms-and-conditions#section-5'],
            ],
        ];
        foreach ($menus as $location => $items) {
            foreach ($items as $i => $m) {
                Menu::create(['location' => $location, 'label' => $m[0], 'url' => $m[1], 'match' => $m[2] ?? null, 'new_tab' => $m[3] ?? false, 'sort' => $i]);
            }
        }
    }
}
