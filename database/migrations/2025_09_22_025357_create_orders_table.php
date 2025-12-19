<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained()->onDelete('set null');
            $table->string('order_number', 12)->unique();
            $table->string('customer_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('order_type')->default('dine_in'); // dine_in, take_away
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, canceled
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
