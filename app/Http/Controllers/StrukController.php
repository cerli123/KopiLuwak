<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class StrukController extends Controller
{
    public function show(Pesanan $pesanan, MidtransService $midtrans)
    {
        $data = $this->siapkan($pesanan, $midtrans);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        return view('struk.show', $data);
    }

    public function pdf(Pesanan $pesanan, MidtransService $midtrans)
    {
        $data = $this->siapkan($pesanan, $midtrans);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $pdf = Pdf::loadView('struk.pdf', $data)
            ->setPaper([0, 0, 226.77, $this->perkirakanTinggi($data)], 'portrait');

        return $pdf->download('struk-' . $pesanan->kode_pesanan . '.pdf');
    }

    private function siapkan(Pesanan $pesanan, MidtransService $midtrans)
    {
        $user    = Auth::user();
        $isAdmin = $user->isAdmin();

        abort_unless($isAdmin || $pesanan->user_id === $user->id, 403);

        if (! $isAdmin) {
            $pesanan = $midtrans->sinkron($pesanan);
        }

        $pesanan->load(['user', 'detailPesanans.produk', 'pembayaran', 'refund']);

        $pembayaran = $pesanan->pembayaran;

        if (! $pembayaran || $pembayaran->status_bayar !== 'dikonfirmasi') {
            $tujuan = $isAdmin
                ? route('admin.pesanan.show', $pesanan)
                : route('pesanan.show', $pesanan);

            return redirect($tujuan)->with('error', 'Struk hanya tersedia untuk pesanan yang sudah dibayar.');
        }

        $stempel = null;
        if ($pesanan->refund && $pesanan->refund->status === 'selesai') {
            $stempel = 'DIREFUND';
        } elseif ($pesanan->status === 'dibatalkan') {
            $stempel = 'DIBATALKAN';
        }

        $zona = 'Asia/Jakarta';

        return [
            'pesanan'     => $pesanan,
            'pembayaran'  => $pembayaran,
            'items'       => $pesanan->detailPesanans,
            'totalQty'    => (int) $pesanan->detailPesanans->sum('qty'),
            'toko'        => [
                'nama'   => (string) config('toko.asal.nama'),
                'kontak' => Pengaturan::kontak(),
            ],
            'tglPesan'    => $pesanan->created_at->copy()->timezone($zona),
            'tglBayar'    => ($pembayaran->tanggal_bayar ?? $pesanan->updated_at)->copy()->timezone($zona),
            'tglCetak'    => now()->timezone($zona),
            'dicetakOleh' => $user->nama,
            'stempel'     => $stempel,
            'batasRefund' => (int) config('toko.refund.batas_hari', 7),
            'isAdmin'     => $isAdmin,
            'urlKembali'  => $isAdmin ? route('admin.pesanan.show', $pesanan) : route('pesanan.show', $pesanan),
        ];
    }

    private function perkirakanTinggi(array $data): float
    {
        $tinggi = 430;

        foreach ($data['items'] as $d) {
            $tinggi += 30 + (ceil(mb_strlen((string) $d->produk?->nama_produk) / 30) * 11);
        }

        $tinggi += ceil(mb_strlen((string) $data['pesanan']->alamat_pengiriman) / 32) * 11;
        $tinggi += ceil(mb_strlen((string) ($data['toko']['kontak']['alamat'] ?? '')) / 32) * 11;

        return (float) $tinggi;
    }
}
