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

    public const KATEGORI = ['Produk', 'Proses', 'Kebun & Biji', 'Sentra Produksi', 'Kunjungan'];

    protected static ?bool $siapDetail = null;

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
