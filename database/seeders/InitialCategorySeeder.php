<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Kopi & Espresso',
                'description' => 'Menu kopi berbasis espresso dan manual brew.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Makanan Berat',
                'description' => 'Pilihan menu untuk makan siang dan malam.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Snack & Dessert',
                'description' => 'Camilan dan makanan penutup yang manis.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // KATEGORI BARU
            [
                'name' => 'Minuman Non-Kopi',
                'description' => 'Berbagai pilihan teh, cokelat, dan minuman segar lainnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Signature Dish',
                'description' => 'Menu andalan dan spesialitas dari CafeKU.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}