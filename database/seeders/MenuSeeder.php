<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil ID Kategori
        $kopiId = Category::where('name', 'Kopi & Espresso')->first()->id ?? null;
        $makananId = Category::where('name', 'Makanan Berat')->first()->id ?? null;
        $snackId = Category::where('name', 'Snack & Dessert')->first()->id ?? null;
        $nonKopiId = Category::where('name', 'Minuman Non-Kopi')->first()->id ?? null; // ID BARU
        $signatureId = Category::where('name', 'Signature Dish')->first()->id ?? null; // ID BARU

        DB::table('menus')->insert([
            // =========================================================================================
            // A. KOPI & ESPRESSO (5 Menu)
            // =========================================================================================
            [ 'category_id' => $kopiId, 'name' => 'Kopi Latte', 'description' => 'Espresso dengan susu steamed dan foam tipis.', 'price' => 25000, 'image' => 'images/menus/latte.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $kopiId, 'name' => 'Kopi Americano', 'description' => 'Espresso shot dengan air panas.', 'price' => 20000, 'image' => 'images/menus/americano.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $kopiId, 'name' => 'Kopi Cappuccino', 'description' => 'Perpaduan sempurna espresso, susu, dan busa tebal.', 'price' => 28000, 'image' => 'images/menus/cappuccino.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $kopiId, 'name' => 'Caramel Macchiato', 'description' => 'Latte dengan sirup vanilla dan saus karamel.', 'price' => 35000, 'image' => 'images/menus/macchiato.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $kopiId, 'name' => 'Manual Brew V60', 'description' => 'Kopi single origin yang diseduh dengan metode V60.', 'price' => 30000, 'image' => 'images/menus/v60.jpg', 'created_at' => now(), 'updated_at' => now() ],
            
            // =========================================================================================
            // B. MAKANAN BERAT (5 Menu)
            // =========================================================================================
            [ 'category_id' => $makananId, 'name' => 'Nasi Goreng Spesial', 'description' => 'Nasi goreng dengan ayam, telur, dan acar.', 'price' => 45000, 'image' => 'images/menus/nasi-goreng.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $makananId, 'name' => 'Spaghetti Bolognese', 'description' => 'Spaghetti dengan saus daging bolognese.', 'price' => 55000, 'image' => 'images/menus/spaghetti.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $makananId, 'name' => 'Chicken Katsu Curry', 'description' => 'Dada ayam crispy dengan saus kari Jepang dan nasi hangat.', 'price' => 60000, 'image' => 'images/menus/katsu-curry.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $makananId, 'name' => 'Mie Goreng Seafood', 'description' => 'Mie goreng dengan udang, cumi, dan sayuran segar.', 'price' => 50000, 'image' => 'images/menus/mie-goreng.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $makananId, 'name' => 'Sop Buntut', 'description' => 'Sop buntut sapi dengan kuah kaldu kaya rempah.', 'price' => 75000, 'image' => 'images/menus/sop-buntut.jpg', 'created_at' => now(), 'updated_at' => now() ],

            // =========================================================================================
            // C. SNACK & DESSERT (5 Menu)
            // =========================================================================================
            [ 'category_id' => $snackId, 'name' => 'Kue Cokelat Lava', 'description' => 'Kue cokelat yang lembut dengan lelehan cokelat panas.', 'price' => 35000, 'image' => 'images/menus/kue-cokelat.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $snackId, 'name' => 'French Fries', 'description' => 'Kentang goreng renyah dengan saus pilihan.', 'price' => 25000, 'image' => 'images/menus/fries.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $snackId, 'name' => 'Churros Cinnamon', 'description' => 'Churros renyah disajikan dengan saus cokelat.', 'price' => 30000, 'image' => 'images/menus/churros.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $snackId, 'name' => 'Waffle Ice Cream', 'description' => 'Waffle hangat dengan es krim vanilla dan sirup maple.', 'price' => 40000, 'image' => 'images/menus/waffle.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $snackId, 'name' => 'Onion Rings', 'description' => 'Cincin bawang bombay crispy yang gurih.', 'price' => 28000, 'image' => 'images/menus/onion-rings.jpg', 'created_at' => now(), 'updated_at' => now() ],

            // =========================================================================================
            // D. MINUMAN NON-KOPI (3 Menu) - BARU
            // =========================================================================================
            [ 'category_id' => $nonKopiId, 'name' => 'Green Tea Latte', 'description' => 'Matcha premium dengan susu segar.', 'price' => 35000, 'image' => 'images/menus/matcha.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $nonKopiId, 'name' => 'Fresh Lemon Tea', 'description' => 'Teh hitam dengan perasan lemon asli.', 'price' => 22000, 'image' => 'images/menus/lemon-tea.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $nonKopiId, 'name' => 'Chocolate Dingin', 'description' => 'Cokelat Belgia dingin yang kental dan pekat.', 'price' => 38000, 'image' => 'images/menus/hot-chocolate.jpg', 'created_at' => now(), 'updated_at' => now() ],

            // =========================================================================================
            // E. SIGNATURE DISH (2 Menu) - BARU
            // =========================================================================================
            [ 'category_id' => $signatureId, 'name' => 'CafeKU Beef Bowl', 'description' => 'Nasi dengan irisan daging sapi saus teriyaki spesial.', 'price' => 65000, 'image' => 'images/menus/beef-bowl.jpg', 'created_at' => now(), 'updated_at' => now() ],
            [ 'category_id' => $signatureId, 'name' => 'Red Velvet Croissant', 'description' => 'Croissant merah dengan isian cream cheese yang lembut.', 'price' => 40000, 'image' => 'images/menus/red-velvet.jpg', 'created_at' => now(), 'updated_at' => now() ],

        ]);
    }
}