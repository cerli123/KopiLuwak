<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Galeri extends Model
{
    protected $fillable = ['judul', 'deskripsi', 'kategori', 'urutan', 'unggulan', 'gambar'];

    protected $casts = [
        'unggulan' => 'boolean',
        'urutan'   => 'integer',
    ];

    /** Kategori bawaan yang disarankan di form admin. */
    public const KATEGORI = ['Produk', 'Proses', 'Kebun & Biji', 'Sentra Produksi', 'Kunjungan'];

    protected static ?bool $siapDetail = null;

    /**
     * Apakah kolom detail galeri (deskripsi, kategori, urutan, unggulan) sudah ada di database.
     * Bila migrasi `php artisan migrate` belum dijalankan, halaman tetap tampil (tanpa error SQL)
     * dan hanya fitur detail yang belum aktif.
     */
    public static function siapDetail(): bool
    {
        if (static::$siapDetail === null) {
            try {
                static::$siapDetail = Schema::hasColumn('galeris', 'unggulan')
                    && Schema::hasColumn('galeris', 'kategori')
                    && Schema::hasColumn('galeris', 'urutan');
            } catch (\Throwable $e) {
                static::$siapDetail = false;
            }
        }

        return static::$siapDetail;
    }

    /** Urutan tampil: nomor urut kecil dulu, lalu yang terbaru. */
    public function scopeUrut($query)
    {
        if (! static::siapDetail()) {
            return $query->orderByDesc('id');
        }

        return $query->orderBy('urutan')->orderByDesc('id');
    }

    public function getUrlAttribute(): string
    {
        return Media::url($this->gambar);
    }
}
