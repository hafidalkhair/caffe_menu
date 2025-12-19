<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import ini
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    // Pastikan 'category_id' ada di sini
    protected $fillable = ['name', 'description', 'price', 'image', 'category_id'];

    /**
     * Dapatkan item pesanan untuk menu ini.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Dapatkan kategori yang dimiliki menu.
     * Ini adalah method yang hilang/salah.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
