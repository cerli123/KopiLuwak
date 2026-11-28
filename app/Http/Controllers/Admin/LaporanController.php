<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $dari   = $request->get('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $pesanans = Pesanan::with(['user', 'detailPesanans.produk', 'pembayaran'])
            ->whereIn('status', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->latest()->get();

        $totalPendapatan = $pesanans->sum('total_harga');
        $totalPesanan    = $pesanans->count();
        $rataRata        = $totalPesanan > 0 ? $totalPendapatan / $totalPesanan : 0;

        $chartData = $pesanans->groupBy(fn($p) => $p->created_at->format('Y-W'))
            ->map(fn($g) => $g->sum('total_harga'))->take(8);

        return view('admin.laporan.index', compact(
            'pesanans',
            'totalPendapatan',
            'totalPesanan',
            'rataRata',
            'chartData',
            'dari',
            'sampai'
        ));
    }

    public function exportPdf(Request $request)
    {
        $dari   = $request->get('dari',   now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->format('Y-m-d'));

        $pesanans = Pesanan::with(['user', 'detailPesanans.produk'])
            ->whereIn('status', ['diproses', 'dikirim', 'selesai'])
            ->whereBetween('created_at', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])
            ->latest()->get();

        $totalPendapatan = $pesanans->sum('total_harga');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact('pesanans', 'totalPendapatan', 'dari', 'sampai'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("laporan-{$dari}-sd-{$sampai}.pdf");
    }
}
