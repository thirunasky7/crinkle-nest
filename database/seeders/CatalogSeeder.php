<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Video;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn ($p, $i) => DatabaseSeeder::img($p, $i);
        Product::query()->delete();
        Category::query()->delete();
        Video::query()->delete();

        $cat = function (array $d, int $sort) {
            return Category::create($d + ['slug' => Str::slug($d['name']), 'sort' => $sort]);
        };

        // Carousel sections on the home page
        $brownie = $cat(['name' => 'Brownie', 'eyebrow' => 'Handcrafted Indulgence', 'eyebrow_icon' => 'cake', 'subtitle' => 'Dense, fudgy, decadent handcrafted brownies baked fresh every morning.', 'show_on_home' => true, 'show_in_collections' => false, 'price_label' => 'Price', 'image' => $img('home', 11), 'card_description' => 'Fudgy, walnut, Oreo & red velvet'], 1);
        $coin = $cat(['name' => 'Coin Biscuit', 'eyebrow' => 'Bite-Sized Nostalgia', 'eyebrow_icon' => 'stars', 'subtitle' => 'Tiny golden discs of nostalgic joy, baked till perfectly crisp.', 'show_on_home' => true, 'show_in_collections' => false, 'price_label' => 'Price (200g)', 'image' => $img('home', 17), 'card_description' => 'Butter, chocolate, jeera & milk'], 2);

        // "More Bakery Collections" cards
        $wa = fn ($what) => '{whatsapp:Hi Crinkle Nest! I would like to explore your '.$what.'.}';
        $cat(['name' => 'Cookies', 'eyebrow' => 'Crackle & Crunch', 'eyebrow_icon' => 'cookie', 'subtitle' => 'Chunky NY style, brookies & classic crinkles.', 'image' => $img('home', 22), 'badge' => '12 varieties', 'card_description' => 'Chunky NY style, Brookies & Classic Crinkles', 'link' => $wa('Cookies collection')], 3);
        $cat(['name' => 'Cakes', 'eyebrow' => 'Celebration Bakes', 'eyebrow_icon' => 'cake', 'subtitle' => 'Bento cakes, layered gateaux & naked sponge cakes.', 'image' => $img('home', 23), 'badge' => '8 varieties', 'card_description' => 'Bento cakes, layered gateaux & naked sponge cakes', 'link' => $wa('Cakes collection')], 4);
        $cat(['name' => 'Cupcakes', 'eyebrow' => 'Swirled & Frosted', 'eyebrow_icon' => 'icecream', 'subtitle' => 'Whipped buttercream tops & hidden fruit compote.', 'image' => $img('home', 24), 'badge' => '10 varieties', 'card_description' => 'Whipped buttercream tops & hidden fruit compote', 'link' => $wa('Cupcakes collection')], 5);
        $cat(['name' => 'Muffins', 'eyebrow' => 'Morning Bakes', 'eyebrow_icon' => 'breakfast_dining', 'subtitle' => 'Blueberry crumb, banana walnut & double choco.', 'image' => $img('home', 25), 'badge' => '6 varieties', 'card_description' => 'Blueberry crumb, banana walnut & double choco', 'link' => $wa('Muffins collection')], 6);
        $cat(['name' => 'Dessert Boxes', 'eyebrow' => 'Gifting', 'eyebrow_icon' => 'redeem', 'subtitle' => 'Customizable gift hampers & festive celebratory boxes.', 'image' => $img('home', 26), 'badge' => '5 curated boxes', 'card_description' => 'Customizable gift hampers & festive celebratory boxes', 'link' => $wa('Dessert Boxes')], 7);

        // Hidden helper categories (menu page only)
        $signature = $cat(['name' => 'Signature Boxes', 'eyebrow' => 'Best Sellers', 'eyebrow_icon' => 'trending_up', 'subtitle' => 'Our most-loved boxes, slabs and tins.', 'show_in_collections' => false, 'image' => $img('home', 7)], 0);
        $pantry = $cat(['name' => 'Pastries & Pantry', 'eyebrow' => 'From The Cooling Racks', 'eyebrow_icon' => 'bakery_dining', 'subtitle' => 'Madeleines, brioche, brittle and spreads that pair beautifully with every batch.', 'show_in_collections' => false, 'image' => $img('cart', 5)], 8);

        $p = function (array $d, int $sort) {
            return Product::create($d + ['slug' => Str::slug($d['name']), 'sort' => $sort]);
        };

        // Trending
        $p(['category_id' => $signature->id, 'name' => 'Signature Crinkle Cookie Box (Pack of 6)', 'short_description' => 'Crisp exterior with melt-in-mouth fudgy chocolate core, hand-dusted with powdered sugar.', 'detail_line' => 'Box of 6 • Belgian 70% Dark Chocolate • Valrhona Cocoa', 'image' => $img('home', 7), 'price' => 380, 'badge' => 'Signature', 'badge_style' => 'secondary', 'tag' => 'Eggless', 'tag_note' => 'Crackled sugar crust', 'image_tag' => 'Oven 7:30 AM', 'is_trending' => true], 1);
        $p(['category_id' => $signature->id, 'name' => 'Fudgy Walnut Brownie Slab', 'short_description' => 'Loaded with roasted Kashmiri walnuts and 70% dark Belgian cocoa with thick shiny crackly crust.', 'detail_line' => '350g Slab • Single-origin Roasted Walnuts • Sea Salt Flakes', 'image' => $img('home', 8), 'price' => 420, 'badge' => 'Bestseller', 'badge_style' => 'secondary', 'tag' => 'Gourmet Nut Crust', 'tag_note' => 'Oven-reheatable container', 'image_tag' => 'Warm Packed', 'is_trending' => true], 2);
        $p(['category_id' => $signature->id, 'name' => 'Classic Butter Coin Biscuits Tin', 'short_description' => 'Nostalgic golden bite-sized melt-in-mouth crunch baked with pure dairy butter and warm vanilla notes.', 'detail_line' => '200g Artisanal Glass Jar • Slow-churned Cultured Butter', 'image' => $img('home', 9), 'price' => 260, 'badge' => 'Nostalgia', 'badge_style' => 'secondary-container', 'tag' => 'Eggless', 'tag_note' => 'Recyclable Glass Keepsake', 'is_trending' => true], 3);
        $p(['category_id' => $signature->id, 'name' => 'Assorted Celebration Dessert Box', 'short_description' => 'Handcrafted bundle of brownies, cookies & coin biscuits packed in our signature blush gift container.', 'detail_line' => '4x Tarts, 4x Crinkles, 2x Macarons', 'image' => $img('home', 10), 'price' => 750, 'badge' => 'Celebration', 'badge_style' => 'tertiary', 'is_trending' => true], 4);

        // Brownies
        $brownies = [
            ['Classic Chocolate Brownie', 'Pure Belgian dark chocolate indulgence with crisp crackly crust.', 140],
            ['Fudgy Walnut Brownie', 'Gooey chocolate brownie studded with crunchy golden roasted walnuts.', 160],
            ['Double Chocolate Brownie', 'Rich chocolate base packed with melted bittersweet chocolate chunks.', 170],
            ['Choco Chip Brownie', 'Chewy brownie topped generously with premium dark and milk choco chips.', 150],
            ['Oreo Brownie', 'Fudgy brownie baked with whole crushed Oreo biscuits and cream layers.', 165],
            ['Red Velvet Brownie', 'Velvety cocoa crimson cake with a swirl of rich cream cheese frosting.', 180],
        ];
        foreach ($brownies as $i => [$name, $desc, $price]) {
            $p(['category_id' => $brownie->id, 'name' => $name, 'short_description' => $desc, 'detail_line' => $desc, 'image' => $img('home', 11 + $i), 'price' => $price], $i);
        }

        // Coin biscuits
        $coins = [
            ['Classic Coin Biscuit', 'Traditional crisp golden buttery discs with delicate sweetness.', 120],
            ['Chocolate Coin Biscuit', 'Infused with rich cocoa and a dusting of brown sugar for an earthy crunch.', 140],
            ['Butter Coin Biscuit', 'Made with 100% farm-fresh butter that melts in your mouth instantly.', 150],
            ['Jeera Coin Biscuit', 'Slightly sweet and savory crunch with aromatic roasted cumin seeds.', 130],
            ['Milk Coin Biscuit', 'Rich condensed milk flavor, perfect for your evening chai or coffee.', 125],
        ];
        foreach ($coins as $i => [$name, $desc, $price]) {
            $p(['category_id' => $coin->id, 'name' => $name, 'short_description' => $desc, 'detail_line' => '200g artisanal jar', 'image' => $img('home', 17 + $i), 'price' => $price, 'whatsapp_message' => 'Hi {site}! I would like to order {product} (200g). Please share the details.'], $i);
        }

        // Cart pairings & upsell
        $p(['category_id' => $pantry->id, 'name' => 'Wildflower Honey Madeleines', 'short_description' => 'Shell-shaped butter cakes glazed with wildflower honey and fresh lemon zest.', 'detail_line' => 'Pack of 4 • Fresh lemon zest', 'image' => $img('cart', 5), 'price' => 240, 'badge' => 'Honey Glaze', 'badge_style' => 'light', 'reco_note' => 'Bakes in 15 mins', 'is_recommended' => true], 1);
        $p(['category_id' => $pantry->id, 'name' => 'Ceylon Cinnamon Brioche Knot', 'short_description' => 'A single giant brioche bun knotted with Ceylon cinnamon and cardamom sugar.', 'detail_line' => 'Single giant bun • Cardamom sugar', 'image' => $img('cart', 6), 'price' => 190, 'badge' => 'Chef Pick', 'badge_style' => 'secondary', 'reco_note' => 'Warm pull ready', 'is_recommended' => true], 2);
        $p(['category_id' => $pantry->id, 'name' => 'Sea Salt Almond Brittle', 'short_description' => 'Caramelised Mamra almonds snapped into shards with flaky sea salt.', 'detail_line' => '150g pouch • Caramelised Mamra nuts', 'image' => $img('cart', 7), 'price' => 310, 'badge' => 'Crisp Crunch', 'badge_style' => 'light', 'reco_note' => 'Pantry stable', 'is_recommended' => true], 3);
        $p(['category_id' => $pantry->id, 'name' => 'Salted Sea-Caramel Spread', 'short_description' => 'Warm mini 80g glass jar made with Normandy cream', 'detail_line' => '80g glass jar • Normandy cream', 'image' => $img('cart', 1), 'price' => 150, 'icon' => 'liquor', 'is_upsell' => true], 4);

        // Shorts
        $videos = [
            ['The Signature Crinkle Cookie Crackle', '0:34', '14.8k views'],
            ['Swirling Double Dark Fudgy Brownie Batter', '0:45', '21.2k views'],
            ['Golden Coin Biscuits Coming Out Hot!', '0:28', '9.4k views'],
            ['Dusting Snow Sugar on Red Velvet', '0:39', '18.6k views'],
            ['Packing Happiness in Our Pastel Gift Boxes', '0:52', '26.5k views'],
        ];
        foreach ($videos as $i => [$title, $dur, $views]) {
            Video::create(['title' => $title, 'thumbnail' => $img('home', 2 + $i), 'duration' => $dur, 'views_label' => $views, 'sort' => $i]);
        }
    }
}
