<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Refund extends Model
{
    protected $fillable = [
        'pesanan_id',
        'user_id',
        'kode_refund',
        'alasan_kategori',
        'alasan_detail',
        'bukti',
        'jumlah_refund',
        'status',
        'catatan_admin',
        'diproses_oleh',
        'diproses_pada',
        'metode_pengembalian',
        'referensi_pengembalian',
        'dikembalikan_pada',
    ];

    protected $casts = [
        'jumlah_refund'     => 'integer',
        'diproses_pada'     => 'datetime',
        'dikembalikan_pada' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    public static function generateKode(): string
    {
        do {
            $kode = 'RF-' . date('Ymd') . '-' . strtoupper(Str::random(5));
        } while (static::where('kode_refund', $kode)->exists());

        return $kode;
    }

    public function getAlasanLabelAttribute(): string
    {
        return (string) config('toko.refund.alasan.' . $this->alasan_kategori, 'Alasan lainnya');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui - Dana Diproses',
            'selesai'   => 'Refund Selesai',
            'ditolak'   => 'Ditolak',
            default     => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'diajukan'  => 'warning',
            'disetujui' => 'info',
            'selesai'   => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }

    public function getMetodePengembalianLabelAttribute(): string
    {
        return match ($this->metode_pengembalian) {
            'midtrans' => 'Dikembalikan otomatis lewat Midtrans',
            'manual'   => 'Transfer manual oleh admin',
            'simulasi' => 'Simulasi (mode uji coba)',
            default    => '-',
        };
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if (! $this->bukti || ! Storage::disk('public')->exists($this->bukti)) {
            return null;
        }

        return url('media/' . ltrim($this->bukti, '/'));
    }

    public function sudahDiputuskan(): bool
    {
        return $this->status !== 'diajukan';
    }
}
