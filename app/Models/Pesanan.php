<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Pesanan extends Model
{
    protected $fillable = [
        'user_id',
        'kode_pesanan',
        'total_harga',
        'status',
        'alamat_pengiriman',
        'nama_penerima',
        'no_hp_penerima',
        'lat',
        'lng',
        'jarak_km',
        'ongkir',
        'midtrans_order_id',
        'snap_token',
    ];

    protected $casts = [
        'jarak_km' => 'float',
        'ongkir'   => 'integer',
    ];

    public function getTotalBayarAttribute(): int
    {
        return (int) round($this->total_harga) + (int) $this->ongkir;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
    public function refund()
    {
        return $this->hasOne(Refund::class);
    }
    public function ulasans()
    {
        return $this->hasMany(Ulasan::class);
    }

    /** Pesanan ini sudah boleh diulas (status sesuai config/toko.php > ulasan.status_boleh). */
    public function bisaDiulas(): bool
    {
        return in_array($this->status, config('toko.ulasan.status_boleh', ['selesai']), true);
    }

    public static function generateKode(): string
    {
        $last = static::max('id') ?? 0;
        return 'KLM-' . date('Y') . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mengembalikan pesan alasan bila pesanan TIDAK bisa diajukan refund,
     * atau null bila boleh diajukan.
     */
    public function alasanTidakBisaRefund(): ?string
    {
        if ($this->refund) {
            return 'Pesanan ini sudah memiliki pengajuan refund.';
        }

        if (! in_array($this->status, ['diproses', 'dikirim', 'selesai'], true)) {
            return 'Refund hanya bisa diajukan untuk pesanan yang sudah dibayar dan belum dibatalkan.';
        }

        $pembayaran = $this->pembayaran;

        if (! $pembayaran || $pembayaran->status_bayar !== 'dikonfirmasi') {
            return 'Pembayaran pesanan ini belum terkonfirmasi.';
        }

        $batas = (int) config('toko.refund.batas_hari', 7);
        $acuan = $this->status === 'selesai'
            ? $this->updated_at
            : ($pembayaran->tanggal_bayar ?? $this->created_at);

        if ($acuan && Carbon::parse($acuan)->addDays($batas)->isPast()) {
            return 'Batas waktu pengajuan refund (' . $batas . ' hari) sudah lewat.';
        }

        return null;
    }

    public function bisaRefund(): bool
    {
        return $this->alasanTidakBisaRefund() === null;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bayar'        => 'Menunggu Bayar',
            'menunggu_konfirmasi'   => 'Menunggu Konfirmasi',
            'diproses'              => 'Diproses',
            'dikirim'               => 'Dikirim',
            'selesai'               => 'Selesai',
            'dibatalkan'            => 'Dibatalkan',
            default                 => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu_bayar', 'menunggu_konfirmasi' => 'warning',
            'diproses', 'dikirim'                   => 'info',
            'selesai'                              => 'success',
            'dibatalkan'                           => 'danger',
            default                                => 'secondary',
        };
    }
}
