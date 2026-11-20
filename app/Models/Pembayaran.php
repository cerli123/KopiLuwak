<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $fillable = ['pesanan_id', 'metode', 'jumlah_bayar', 'bukti_transfer', 'referensi', 'status_bayar', 'tanggal_bayar'];
    protected $casts    = ['tanggal_bayar' => 'datetime'];
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function getMetodeLabelAttribute(): string
    {
        return match ($this->metode) {
            'midtrans'      => 'Belum dipilih',
            'bank_transfer' => 'Transfer Bank (Virtual Account)',
            'echannel'      => 'Mandiri Bill',
            'qris'          => 'QRIS',
            'gopay'         => 'GoPay',
            'shopeepay'     => 'ShopeePay',
            'credit_card'   => 'Kartu Kredit/Debit',
            'cstore'        => 'Minimarket',
            default         => strtoupper($this->metode),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status_bayar) {
            'dikonfirmasi' => 'Lunas',
            'ditolak'      => 'Gagal',
            default        => 'Menunggu Pembayaran',
        };
    }
}
