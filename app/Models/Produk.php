<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Produk extends Model
{
    protected $fillable = ['kategori_id', 'nama_produk', 'deskripsi', 'harga', 'gambar'];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
    public function stok()
    {
        return $this->hasOne(Stok::class);
    }
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }
    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }
    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    /** Muat rata-rata rating & jumlah ulasan yang tampil (tanpa N+1 di daftar produk). */
    public function scopeDenganRating($query)
    {
        return $query
            ->withAvg(['ulasans' => fn ($q) => $q->where('tampil', true)], 'rating')
            ->withCount(['ulasans' => fn ($q) => $q->where('tampil', true)]);
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar || ! Storage::disk('public')->exists($this->gambar)) {
            return null;
        }
        return url('media/' . ltrim($this->gambar, '/'));
    }

    /** Rata-rata rating (0 bila belum ada ulasan tampil). Memakai hasil withAvg bila sudah dimuat. */
    public function getRataRatingAttribute(): float
    {
        $avg = array_key_exists('ulasans_avg_rating', $this->attributes)
            ? $this->attributes['ulasans_avg_rating']
            : $this->ulasans()->tampil()->avg('rating');

        return round((float) $avg, 1);
    }

    public function getJumlahUlasanAttribute(): int
    {
        return array_key_exists('ulasans_count', $this->attributes)
            ? (int) $this->attributes['ulasans_count']
            : $this->ulasans()->tampil()->count();
    }

    public function getSisaStokAttribute(): int
    {
        return $this->stok?->jumlah ?? 0;
    }
}