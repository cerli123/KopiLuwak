<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $fillable = ['pesanan_id', 'produk_id', 'qty', 'harga_satuan'];
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
    public function subtotal(): float
    {
        return $this->qty * $this->harga_satuan;
    }
}
