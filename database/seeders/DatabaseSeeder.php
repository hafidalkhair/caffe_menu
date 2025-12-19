<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // 1. Kategori harus dipanggil duluan
            InitialCategorySeeder::class,
            
            // 2. Baru setelah itu Menu
            MenuSeeder::class,
            
            // 3. Lainnya (Tabel dan Admin)
            TableSeeder::class,
            AdminSeeder::class,
        ]);
    }
}