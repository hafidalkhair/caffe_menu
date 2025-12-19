<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    // Nama tabel sudah benar (default Laravel adalah plural)
    protected $table = 'tables'; 

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'name',
        'status', 
    ];

    /**
     * Relasi ke pesanan (untuk melihat apakah meja sedang digunakan).
     */
    public function orders(): HasMany
    {
        // Meja memiliki banyak pesanan
        return $this->hasMany(Order::class);
    }
}