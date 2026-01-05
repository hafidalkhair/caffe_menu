<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Tambahkan ini agar rapi

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo_path',
        'store_id', // Pastikan ini ada (Sudah benar)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * --- RELASI KE TABEL STORE (GERAI) ---
     * Bagian ini yang sebelumnya HILANG dan menyebabkan error.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * --- FUNGSI TAMBAHAN UNTUK CEK ROLE ---
     */

    // Cek apakah user adalah Admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Cek apakah user adalah Dapur (Kitchen)
    public function isKitchen(): bool
    {
        return $this->role === 'dapur';
    }
}
