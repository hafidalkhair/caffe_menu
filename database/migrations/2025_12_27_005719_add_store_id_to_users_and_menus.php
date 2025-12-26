<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Hubungkan User ke Gerai
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('set null')->after('role');
        });

        // Hubungkan Menu ke Gerai
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained('stores')->onDelete('cascade')->after('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_and_menus', function (Blueprint $table) {
            //
        });
    }
};
