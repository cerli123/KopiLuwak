<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $fillable = ['produk_id', 'jumlah'];
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
    public function kurangi(int $qty): void
    {
        $this->decrement('jumlah', $qty);
    }
    public function tambah(int $qty): void
    {
        $this->increment('jumlah', $qty);
    }
}
