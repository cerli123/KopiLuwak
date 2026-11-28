<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasans';

    protected $fillable = [
        'user_id', 'produk_id', 'pesanan_id', 'rating', 'komentar',
        'tampil', 'balasan_admin', 'dibalas_at',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'tampil'     => 'boolean',
        'dibalas_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function scopeTampil($query)
    {
        return $query->where('tampil', true);
    }

    public function getNamaTampilAttribute(): string
    {
        $bagian = preg_split('/\s+/', trim((string) $this->user?->nama)) ?: [];
        $depan  = $bagian[0] ?? 'Pelanggan';

        return count($bagian) > 1
            ? $depan . ' ' . mb_strtoupper(mb_substr(end($bagian), 0, 1)) . '.'
            : $depan;
    }
}
