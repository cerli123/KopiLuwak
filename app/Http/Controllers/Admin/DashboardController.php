<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Pesanan, Produk, User, Pembayaran};

class DashboardController extends Controller
{
    public function index()
    {
        $totalPesanan    = Pesanan::count();
        $menunggu        = Pesanan::whereIn('status', ['menunggu_bayar', 'menunggu_konfirmasi'])->count();
        $totalProduk     = Produk::count();
        $totalUser       = User::where('role', 'user')->count();
        $pendapatanBulan = Pembayaran::where('status_bayar', 'dikonfirmasi')
            ->whereDoesntHave('pesanan.refund', fn($q) => $q->whereIn('status', ['disetujui', 'selesai']))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah_bayar');
        $pesananTerbaru  = Pesanan::with(['user', 'pembayaran'])->latest()->take(8)->get();
        $stokRendah      = Produk::with(['stok', 'kategori'])
            ->whereHas('stok', fn($q) => $q->where('jumlah', '<', 20))
            ->take(6)->get();
        return view('admin.dashboard', compact(
            'totalPesanan',
            'menunggu',
            'totalProduk',
            'totalUser',
            'pendapatanBulan',
            'pesananTerbaru',
            'stokRendah'
        ));
    }
}
