<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      

        DB::table('tables')->insert([
            ['name' => 'M01', 'status' => 'available'],
            ['name' => 'M02', 'status' => 'available'],
            ['name' => 'M03', 'status' => 'available'],
            ['name' => 'M04', 'status' => 'available'],
            ['name' => 'M05', 'status' => 'available'],
            ['name' => 'Bar 1', 'status' => 'available'],
            ['name' => 'Bar 2', 'status' => 'available'],
        ]);
    }
}