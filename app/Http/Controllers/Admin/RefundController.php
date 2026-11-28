<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $query = Refund::with(['pesanan', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $cari = $request->search;

            $query->where(function ($q) use ($cari) {
                $q->where('kode_refund', 'like', '%' . $cari . '%')
                    ->orWhereHas('pesanan', fn ($p) => $p->where('kode_pesanan', 'like', '%' . $cari . '%'))
                    ->orWhereHas('user', fn ($u) => $u->where('nama', 'like', '%' . $cari . '%'));
            });
        }

        $refunds = $query->latest()->paginate(15)->withQueryString();

        $jumlah = Refund::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        if ($request->expectsJson()) {
            return response()->json($refunds);
        }

        return view('admin.refund.index', compact('refunds', 'jumlah'));
    }

    public function show(Request $request, Refund $refund)
    {
        $refund->load(['pesanan.detailPesanans.produk', 'pesanan.pembayaran', 'user', 'admin']);

        if ($request->expectsJson()) {
            return response()->json($refund);
        }

        return view('admin.refund.show', compact('refund'));
    }

    public function setujui(Request $request, Refund $refund, MidtransService $midtrans)
    {
        $data = $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ], [
            'catatan_admin.max' => 'Catatan maksimal 500 karakter.',
        ]);

        try {
            DB::transaction(function () use ($refund, $data) {
                $r = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();

                if ($r->status !== 'diajukan') {
                    throw new \RuntimeException('Pengajuan ini sudah diproses sebelumnya.');
                }

                $pesanan = Pesanan::with('detailPesanans.produk.stok')
                    ->whereKey($r->pesanan_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pesanan->status === 'diproses') {
                    foreach ($pesanan->detailPesanans as $detail) {
                        $detail->produk?->stok?->tambah($detail->qty);
                    }
                }

                if ($pesanan->status !== 'dibatalkan') {
                    $pesanan->update(['status' => 'dibatalkan']);
                }

                $r->update([
                    'status'        => 'disetujui',
                    'catatan_admin' => $data['catatan_admin'] ?? null,
                    'diproses_oleh' => Auth::id(),
                    'diproses_pada' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return $this->gagal($request, $e->getMessage(), 409);
        }

        $refund->refresh();
        $hasil = $midtrans->prosesRefund($refund);

        if ($hasil['berhasil']) {
            $refund->update([
                'status'                 => 'selesai',
                'metode_pengembalian'    => $hasil['metode'],
                'referensi_pengembalian' => $hasil['referensi'],
                'dikembalikan_pada'      => now(),
            ]);

            $pesan = 'Refund disetujui. ' . $hasil['pesan'];
        } else {
            $refund->update(['metode_pengembalian' => 'manual']);

            $pesan = 'Refund disetujui. ' . $hasil['pesan'] . ' Setelah transfer, klik "Tandai Dana Sudah Dikembalikan".';
        }

        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan, 'refund' => $refund->fresh()]);
        }

        return redirect()->route('admin.refund.show', $refund)->with('success', $pesan);
    }

    public function tolak(Request $request, Refund $refund)
    {
        $data = $request->validate([
            'catatan_admin' => 'required|string|min:10|max:500',
        ], [
            'catatan_admin.required' => 'Alasan penolakan wajib diisi.',
            'catatan_admin.min'      => 'Alasan penolakan minimal 10 karakter.',
            'catatan_admin.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        try {
            DB::transaction(function () use ($refund, $data) {
                $r = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();

                if ($r->status !== 'diajukan') {
                    throw new \RuntimeException('Pengajuan ini sudah diproses sebelumnya.');
                }

                $r->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => $data['catatan_admin'],
                    'diproses_oleh' => Auth::id(),
                    'diproses_pada' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return $this->gagal($request, $e->getMessage(), 409);
        }

        if ($request->expectsJson()) {
            return response()->json(['pesan' => 'Pengajuan refund ditolak.', 'refund' => $refund->fresh()]);
        }

        return redirect()->route('admin.refund.show', $refund)->with('success', 'Pengajuan refund ditolak. Alasan akan dilihat pelanggan.');
    }

    public function selesai(Request $request, Refund $refund)
    {
        $data = $request->validate([
            'referensi_pengembalian' => 'required|string|max:100',
        ], [
            'referensi_pengembalian.required' => 'Isi nomor referensi / keterangan transfer.',
            'referensi_pengembalian.max'      => 'Referensi maksimal 100 karakter.',
        ]);

        try {
            DB::transaction(function () use ($refund, $data) {
                $r = Refund::whereKey($refund->id)->lockForUpdate()->firstOrFail();

                if ($r->status !== 'disetujui') {
                    throw new \RuntimeException('Hanya refund berstatus "disetujui" yang bisa ditandai selesai.');
                }

                $r->update([
                    'status'                 => 'selesai',
                    'metode_pengembalian'    => $r->metode_pengembalian ?: 'manual',
                    'referensi_pengembalian' => $data['referensi_pengembalian'],
                    'dikembalikan_pada'      => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return $this->gagal($request, $e->getMessage(), 409);
        }

        if ($request->expectsJson()) {
            return response()->json(['pesan' => 'Refund ditandai selesai.', 'refund' => $refund->fresh()]);
        }

        return redirect()->route('admin.refund.show', $refund)->with('success', 'Refund ditandai selesai. Pelanggan akan melihat statusnya.');
    }

    private function gagal(Request $request, string $pesan, int $kode)
    {
        if ($request->expectsJson()) {
            return response()->json(['pesan' => $pesan], $kode);
        }

        return back()->with('error', $pesan);
    }
}
