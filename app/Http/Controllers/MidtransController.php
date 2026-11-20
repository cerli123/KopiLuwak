<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    /**
     * Webhook (HTTP Notification) Midtrans.
     * Daftarkan URL berikut di Dashboard Midtrans > Settings > Configuration > Payment Notification URL:
     *   https://DOMAIN-ANDA/midtrans/notifikasi
     */
    public function notifikasi(Request $request, MidtransService $midtrans)
    {
        $serverKey = (string) config('toko.midtrans.server_key');

        // Tanpa server key signature tidak bisa diverifikasi, jadi webhook ditolak.
        if ($serverKey === '') {
            return response()->json(['status' => 'server key belum diisi'], 503);
        }

        $ambil = function (string $kunci) use ($request): string {
            $nilai = $request->input($kunci);

            return is_scalar($nilai) ? (string) $nilai : '';
        };

        $orderId   = $ambil('order_id');
        $signature = hash('sha512', $orderId . $ambil('status_code') . $ambil('gross_amount') . $serverKey);

        if (! hash_equals($signature, $ambil('signature_key'))) {
            return response()->json(['status' => 'signature tidak valid'], 403);
        }

        $pesanan = $orderId !== '' ? Pesanan::where('midtrans_order_id', $orderId)->first() : null;

        if (! $pesanan) {
            // Contoh: tombol "Test notification" di dashboard Midtrans memakai order_id palsu.
            return response()->json(['status' => 'diabaikan']);
        }

        $midtrans->tanganiNotifikasi($pesanan, $request->all());

        return response()->json(['status' => 'ok']);
    }
}
