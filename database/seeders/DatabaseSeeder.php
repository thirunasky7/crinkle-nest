<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            MenuSeeder::class,
            SectionSeeder::class,
            CatalogSeeder::class,
            ContentSeeder::class,
            ShopSeeder::class,
            UserSeeder::class,
        ]);
    }

    /** Image URL from the original design, by page and order of appearance. */
    public static function img(string $page, int $i): ?string
    {
        static $images = null;
        $images ??= require __DIR__.'/data/images.php';

        return $images[$page][$i] ?? null;
    }
}
