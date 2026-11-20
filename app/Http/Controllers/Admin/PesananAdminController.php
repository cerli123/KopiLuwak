<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Pesanan, LaporanPenjualan};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with(['user', 'pembayaran']);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('kode_pesanan', 'like', '%' . $request->search . '%')
                    ->orWhereHas('user', fn($u) => $u->where('nama', 'like', '%' . $request->search . '%'));
            });
        }
        $pesanans = $query->latest()->paginate(15)->withQueryString();
        return view('admin.pesanan.index', compact('pesanans'));
    }

    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'detailPesanans.produk', 'pembayaran', 'refund']);
        return view('admin.pesanan.show', compact('pesanan'));
    }

    public function konfirmasi(Pesanan $pesanan)
    {
        abort_if($pesanan->status !== 'menunggu_konfirmasi' || !$pesanan->pembayaran || $pesanan->pembayaran->status_bayar !== 'pending', 403);

        DB::transaction(function () use ($pesanan) {
            $pesanan->pembayaran->update(['status_bayar' => 'dikonfirmasi']);
            $pesanan->update(['status' => 'diproses']);

            foreach ($pesanan->detailPesanans()->with('produk.stok')->get() as $detail) {
                $detail->produk->stok?->kurangi($detail->qty);
            }

            LaporanPenjualan::create([
                'periode'          => now()->format('Y-m'),
                'total_pendapatan' => $pesanan->total_harga,
                'total_pesanan'    => 1,
                'generated_at'     => now(),
            ]);
        });

        return back()->with('success', 'Pembayaran dikonfirmasi. Pesanan sedang diproses.');
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate(['status' => 'required|in:dikirim,selesai,dibatalkan']);

        $alur = [
            'menunggu_bayar'      => ['dibatalkan'],
            'menunggu_konfirmasi' => ['dibatalkan'],
            'diproses'            => ['dikirim', 'dibatalkan'],
            'dikirim'             => ['selesai'],
        ];

        if (!in_array($request->status, $alur[$pesanan->status] ?? [], true)) {
            return back()->with('error', 'Perubahan status tidak diperbolehkan dari status saat ini.');
        }

        DB::transaction(function () use ($request, $pesanan) {
            if ($request->status === 'dibatalkan' && $pesanan->status === 'diproses') {
                foreach ($pesanan->detailPesanans()->with('produk.stok')->get() as $detail) {
                    $detail->produk->stok?->tambah($detail->qty);
                }
            }

            $pesanan->update(['status' => $request->status]);
        });

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
