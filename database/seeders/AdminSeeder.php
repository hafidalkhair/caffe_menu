<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat/Update Akun ADMIN (Kasir/Owner)
        User::updateOrCreate(
            ['email' => 'admin@cafe.com'], // Cek berdasarkan email ini
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Password: password
                'role' => 'admin', // Pastikan role adalah ADMIN
            ]
        );

        // 2. Buat/Update Akun DAPUR (Koki)
        User::updateOrCreate(
            ['email' => 'dapur@cafe.com'], // Cek berdasarkan email ini
            [
                'name' => 'Kepala Dapur',
                'password' => Hash::make('password'), // Password: password
                'role' => 'dapur', // PENTING: Role harus DAPUR
            ]
        );

        // (Opsional) Jika Anda ingin tetap menyimpan akun lama 'admin@example.com'
        // User::updateOrCreate(['email' => 'admin@example.com'], [...]);
    }
}
