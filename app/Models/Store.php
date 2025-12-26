<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    // Izinkan kolom 'name' diisi
    protected $fillable = ['name'];

    // Relasi: Satu Store punya banyak User (Pegawai)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Relasi: Satu Store punya banyak Menu
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
