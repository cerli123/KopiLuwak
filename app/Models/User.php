<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'role',
        'google_id',
        'avatar',
        'punya_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password'       => 'hashed',
        'punya_password' => 'boolean',
    ];

    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }

    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
