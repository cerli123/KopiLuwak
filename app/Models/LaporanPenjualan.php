<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPenjualan extends Model
{
    protected $fillable = ['periode', 'total_pendapatan', 'total_pesanan', 'generated_at'];
    protected $casts    = ['generated_at' => 'datetime'];
}
