<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    public function form(Pesanan $pesanan, MidtransService $midtrans)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan = $midtrans->sinkron($pesanan);

        if ($pesanan->status !== 'menunggu_bayar') {
            return redirect()->route('pesanan.show', $pesanan);
        }

        if ($midtrans->perluTokenBaru($pesanan)) {
            try {
                $midtrans->buatSnapToken($pesanan);
                $pesanan->refresh();
            } catch (\Throwable $e) {
                Log::error('Gagal membuat token Midtrans: ' . $e->getMessage());
                $sebab = config('app.debug') ? ' (' . MidtransService::pesanGalat($e) . ')' : '';

                return redirect()->route('pesanan.show', $pesanan)
                    ->with('error', 'Sesi pembayaran belum bisa dibuat. Silakan coba lagi beberapa saat atau hubungi admin.' . $sebab);
            }
        }

        return view('user.pembayaran', [
            'pesanan'   => $pesanan,
            'snapUrl'   => $midtrans->snapUrl(),
            'clientKey' => config('toko.midtrans.client_key'),
        ]);
    }

    public function status(Pesanan $pesanan, MidtransService $midtrans): JsonResponse
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan = $midtrans->sinkron($pesanan);
        $pesanan->load(['pembayaran', 'refund']);

        return response()->json([
            'kode_pesanan'   => $pesanan->kode_pesanan,
            'status'         => $pesanan->status,
            'status_label'   => $pesanan->status_label,
            'total_bayar'    => $pesanan->total_bayar,
            'lunas'          => $pesanan->pembayaran?->status_bayar === 'dikonfirmasi',
            'status_bayar'   => $pesanan->pembayaran?->status_bayar,
            'metode'         => $pesanan->pembayaran?->metode,
            'tanggal_bayar'  => $pesanan->pembayaran?->tanggal_bayar?->toIso8601String(),
            'refund'         => $pesanan->refund ? [
                'kode'   => $pesanan->refund->kode_refund,
                'status' => $pesanan->refund->status,
            ] : null,
        ]);
    }

    public function token(Pesanan $pesanan, MidtransService $midtrans): JsonResponse
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan = $midtrans->sinkron($pesanan);

        if ($pesanan->status !== 'menunggu_bayar') {
            return response()->json(['pesan' => 'Pesanan ini tidak sedang menunggu pembayaran.'], 409);
        }

        try {
            if ($midtrans->perluTokenBaru($pesanan)) {
                $midtrans->buatSnapToken($pesanan);
                $pesanan->refresh();
            }
        } catch (\Throwable $e) {
            Log::error('Gagal membuat token Midtrans: ' . $e->getMessage());

            $sebab = config('app.debug') ? ' (' . MidtransService::pesanGalat($e) . ')' : '';

            return response()->json(['pesan' => 'Sesi pembayaran belum bisa dibuat. Silakan coba lagi beberapa saat.' . $sebab], 502);
        }

        return response()->json([
            'token'      => $pesanan->snap_token,
            'order_id'   => $pesanan->midtrans_order_id,
            'snap_url'   => $midtrans->snapUrl(),
            'client_key' => config('toko.midtrans.client_key'),
        ]);
    }

    public function selesai(Pesanan $pesanan, MidtransService $midtrans)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan = $midtrans->sinkron($pesanan);

        $pesan = $pesanan->status === 'menunggu_bayar'
            ? ['info', 'Pembayaran Anda sedang diproses. Status akan diperbarui otomatis.']
            : ['success', 'Terima kasih! Status pesanan Anda sudah diperbarui.'];

        $pesanan->load('pembayaran');
        $lunas = $pesanan->pembayaran?->status_bayar === 'dikonfirmasi';

        return redirect()->route('pesanan.show', $pesanan)->with([$pesan[0] => $pesan[1], 'struk_baru' => $lunas]);
    }
}
