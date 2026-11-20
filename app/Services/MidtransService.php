<?php

namespace App\Services;

use App\Models\LaporanPenjualan;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Support\CaBundle;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Metode pembayaran yang bisa direfund otomatis lewat API Midtrans.
     * Metode lain (Virtual Account, Mandiri Bill, minimarket) harus ditransfer manual oleh admin.
     */
    private const METODE_REFUND_OTOMATIS = ['credit_card', 'gopay', 'shopeepay', 'qris', 'akulaku', 'kredivo'];

    /** Token Snap berlaku 24 jam; lewat dari ini token dianggap kedaluwarsa dan dibuat ulang. */
    public const TOKEN_KEDALUWARSA_JAM = 25;

    public function __construct()
    {
        Config::$serverKey    = (string) config('toko.midtrans.server_key');
        Config::$isProduction = (bool) config('toko.midtrans.is_production');
        Config::$isSanitized  = true;
        Config::$is3ds        = true;

        // Library Midtrans tidak membawa sertifikat CA. Di Windows (XAMPP/Laragon) tanpa curl.cainfo,
        // pembuatan token gagal dengan "cURL error 60: SSL certificate problem".
        // CURLOPT_HTTPHEADER wajib berisi minimal satu header: library membuang header
        // Authorization bila daftar header di Config::$curlOptions kosong.
        if (function_exists('curl_init')) {
            $opsi = [
                CURLOPT_HTTPHEADER     => ['X-Client: kopiluwak-app'],
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 30,
            ];
            if ($ca = CaBundle::path()) {
                $opsi[CURLOPT_CAINFO] = $ca;
            }
            Config::$curlOptions = $opsi;
        }
    }

    /**
     * Ubah error teknis Midtrans menjadi petunjuk yang bisa dipahami (untuk log & admin / mode debug).
     */
    public static function pesanGalat(\Throwable $e): string
    {
        $m = $e->getMessage();

        return match (true) {
            str_contains($m, 'ServerKey/ClientKey is null'),
            str_contains($m, 'ServerKey/ClientKey is invalid') => 'Server Key Midtrans belum diisi di .env (MIDTRANS_SERVER_KEY).',
            str_contains($m, 'white-space')                    => 'Server Key Midtrans mengandung spasi. Salin ulang tanpa spasi.',
            str_contains($m, 'status code: 401'),
            str_contains($m, 'HTTP status code: 401')          => 'Server Key ditolak Midtrans (401). Pastikan key Sandbox (SB-Mid-server-...) dan MIDTRANS_IS_PRODUCTION=false.',
            str_contains($m, 'error 60'),
            str_contains($m, 'SSL certificate')                => 'Sertifikat SSL PHP belum diatur (cURL error 60). Set curl.cainfo di php.ini atau perbarui aplikasi ini.',
            str_contains($m, 'CURL Error')                     => 'Tidak dapat terhubung ke Midtrans: ' . Str::limit($m, 150, ''),
            default                                            => Str::limit($m, 250, ''),
        };
    }

    /** sandbox | live */
    public function mode(): string
    {
        return config('toko.midtrans.is_production') ? 'live' : 'sandbox';
    }

    /**
     * Ringkasan konfigurasi untuk halaman Pengaturan admin.
     */
    public function ringkasan(): array
    {
        $server = (string) config('toko.midtrans.server_key');
        $client = (string) config('toko.midtrans.client_key');
        $prod   = (bool) config('toko.midtrans.is_production');
        $mode   = $this->mode();

        $peringatan = [];

        if (! function_exists('curl_init')) {
            $peringatan[] = 'Ekstensi PHP cURL belum aktif sehingga Midtrans tidak bisa dihubungi. Aktifkan extension=curl di php.ini lalu restart web server.';
        }

        if ($server === '') {
            $peringatan[] = 'MIDTRANS_SERVER_KEY masih kosong, pembayaran tidak akan bisa dibuat. Isi Server Key di .env lalu jalankan php artisan config:clear.';
        }
        if ($mode === 'sandbox' && $server !== '' && $client !== '' && ! str_starts_with($client, 'SB-')) {
            $peringatan[] = 'Client Key bukan key Sandbox (harus diawali SB-Mid-client-). Server Key dan Client Key harus dari environment yang sama.';
        }
        if ($server !== '' && $client === '') {
            $peringatan[] = 'MIDTRANS_CLIENT_KEY masih kosong, jendela pembayaran tidak akan muncul.';
        }
        if ($prod && str_starts_with($server, 'SB-')) {
            $peringatan[] = 'Mode Production aktif tetapi Server Key adalah key Sandbox (diawali SB-).';
        }
        if (! $prod && $server !== '' && ! str_starts_with($server, 'SB-')) {
            $peringatan[] = 'Server Key tampak seperti key Production, tetapi MIDTRANS_IS_PRODUCTION masih false.';
        }
        if ($prod && ! str_starts_with((string) config('app.url'), 'https://')) {
            $peringatan[] = 'Pembayaran live sebaiknya memakai HTTPS. Set APP_URL ke alamat https:// website Anda.';
        }

        return [
            'mode'         => $mode,
            'label'        => ['sandbox' => 'Sandbox (uji coba)', 'live' => 'Live (uang sungguhan)'][$mode],
            'server_key'   => $server !== '',
            'client_key'   => $client !== '',
            'webhook_url'  => url('midtrans/notifikasi'),
            'peringatan'   => $peringatan,
        ];
    }

    /**
     * Uji Server Key ke Midtrans memakai order palsu: key valid => 404 (order tidak ada), key salah => 401.
     *
     * @return array{0: bool, 1: string}
     */
    public function tesKoneksi(): array
    {
        $key = (string) config('toko.midtrans.server_key');

        if ($key === '') {
            return [false, 'Server Key belum diisi di file .env (MIDTRANS_SERVER_KEY).'];
        }

        $base = config('toko.midtrans.is_production')
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';

        try {
            $res  = Http::withOptions(['verify' => CaBundle::verify()])->withBasicAuth($key, '')->acceptJson()->timeout(10)
                ->get($base . '/v2/tes-koneksi-' . strtolower(Str::random(8)) . '/status');
            $kode = (int) ($res->json('status_code') ?? $res->status());
        } catch (\Throwable $e) {
            return [false, 'Tidak dapat menghubungi Midtrans: ' . Str::limit($e->getMessage(), 120, '')];
        }

        if ($kode === 404) {
            return [true, 'Terhubung ke Midtrans (' . ($this->mode() === 'live' ? 'Production' : 'Sandbox') . '). Server Key valid.'];
        }

        if ($kode === 401) {
            return [false, 'Server Key ditolak Midtrans. Pastikan key sesuai mode (Sandbox/Production) dan tidak ada spasi.'];
        }

        return [false, 'Respons Midtrans tidak terduga (kode ' . $kode . '). Coba lagi beberapa saat.'];
    }

    public function snapUrl(): string
    {
        return config('toko.midtrans.is_production')
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Apakah token Snap pesanan ini perlu dibuat (belum ada / sudah kedaluwarsa).
     */
    public function perluTokenBaru(Pesanan $pesanan): bool
    {
        if (! $pesanan->snap_token) {
            return true;
        }

        // Token 'SIM-...' sisa dari versi lama (mode simulasi); tidak dikenal Midtrans.
        if (str_starts_with((string) $pesanan->snap_token, 'SIM-')) {
            return true;
        }

        return $pesanan->updated_at === null
            || $pesanan->updated_at->lt(now()->subHours(self::TOKEN_KEDALUWARSA_JAM));
    }

    public function buatSnapToken(Pesanan $pesanan): string
    {
        $pesanan->loadMissing(['user', 'detailPesanans.produk']);

        $orderId = $pesanan->kode_pesanan . '-' . strtoupper(Str::random(5));

        if (blank(config('toko.midtrans.server_key'))) {
            throw new \RuntimeException('ServerKey/ClientKey is null: MIDTRANS_SERVER_KEY belum diisi.');
        }

        if (! function_exists('curl_init')) {
            throw new \RuntimeException('Ekstensi PHP cURL belum aktif. Aktifkan extension=curl di php.ini lalu restart web server.');
        }

        $items = [];
        foreach ($pesanan->detailPesanans as $d) {
            $items[] = [
                'id'       => 'P' . $d->produk_id,
                'price'    => (int) round($d->harga_satuan),
                'quantity' => (int) $d->qty,
                'name'     => Str::limit($d->produk->nama_produk, 50, ''),
            ];
        }

        if ($pesanan->ongkir > 0) {
            $items[] = [
                'id'       => 'ONGKIR',
                'price'    => (int) $pesanan->ongkir,
                'quantity' => 1,
                'name'     => 'Ongkos kirim ' . $pesanan->jarak_km . ' km',
            ];
        }

        // gross_amount harus sama persis dengan jumlah item_details, kalau tidak Midtrans menolak.
        $gross = 0;
        foreach ($items as $item) {
            $gross += $item['price'] * $item['quantity'];
        }

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $gross,
            ],
            'item_details'     => $items,
            'customer_details' => [
                'first_name'       => $pesanan->user->nama,
                'email'            => $pesanan->user->email,
                'phone'            => $pesanan->no_hp_penerima,
                'shipping_address' => [
                    'first_name'   => $pesanan->nama_penerima,
                    'phone'        => $pesanan->no_hp_penerima,
                    'address'      => Str::limit($pesanan->alamat_pengiriman, 200, ''),
                    'country_code' => 'IDN',
                ],
            ],
            'expiry'    => ['unit' => 'hours', 'duration' => 24],
            'callbacks' => ['finish' => route('pembayaran.selesai', $pesanan)],
        ];

        $token = Snap::getSnapToken($params);

        $pesanan->update([
            'midtrans_order_id' => $orderId,
            'snap_token'        => $token,
        ]);

        return $token;
    }

    public function sinkron(Pesanan $pesanan): Pesanan
    {
        if (! $pesanan->midtrans_order_id || $pesanan->status !== 'menunggu_bayar') {
            return $pesanan;
        }

        try {
            $data = (array) Transaction::status($pesanan->midtrans_order_id);
        } catch (\Throwable $e) {
            Log::info('Status Midtrans belum tersedia: ' . $e->getMessage());

            return $pesanan;
        }

        return $this->terapkan($pesanan, $data);
    }

    /**
     * Dipanggil oleh webhook Midtrans (signature sudah diverifikasi controller).
     */
    public function tanganiNotifikasi(Pesanan $pesanan, array $payload): void
    {
        $status = $payload['transaction_status'] ?? null;

        if (in_array($status, ['refund', 'partial_refund'], true)) {
            $this->terapkanNotifikasiRefund($pesanan, $payload);

            return;
        }

        // Status pembayaran selalu diverifikasi ulang langsung ke Midtrans, bukan dari isi payload.
        $this->sinkron($pesanan);
    }

    public function terapkan(Pesanan $pesanan, array $data): Pesanan
    {
        $status = $data['transaction_status'] ?? null;
        $fraud  = $data['fraud_status'] ?? null;
        $tipe   = $data['payment_type'] ?? null;
        $ref    = $data['transaction_id'] ?? null;

        $lunas = $status === 'settlement' || ($status === 'capture' && $fraud === 'accept');
        $gagal = in_array($status, ['deny', 'cancel', 'expire', 'failure'], true);

        if ($lunas) {
            $this->tandaiDibayar($pesanan, $tipe, $ref);
        } elseif ($gagal) {
            $this->tandaiGagal($pesanan, $tipe, $ref);
        } elseif ($tipe && $pesanan->pembayaran) {
            $pesanan->pembayaran->update(['metode' => $tipe, 'referensi' => $ref]);
        }

        return $pesanan->refresh();
    }

    /**
     * Mengembalikan dana untuk refund yang sudah disetujui admin.
     *
     * @return array{berhasil: bool, metode: string, referensi: ?string, pesan: string}
     */
    public function prosesRefund(Refund $refund): array
    {
        $refund->loadMissing('pesanan.pembayaran');

        $pesanan    = $refund->pesanan;
        $pembayaran = $pesanan?->pembayaran;
        $tipe       = (string) ($pembayaran?->metode ?? '');
        $referensi  = (string) ($pembayaran?->referensi ?? '');

        // Pembayaran lama hasil simulasi (referensi 'SIM-...') tidak punya transaksi nyata di Midtrans.
        if (str_starts_with($referensi, 'SIM-')) {
            return [
                'berhasil'  => true,
                'metode'    => 'simulasi',
                'referensi' => 'SIM-RF-' . strtoupper(Str::random(10)),
                'pesan'     => 'Refund simulasi berhasil.',
            ];
        }

        if (! $pesanan || ! $pesanan->midtrans_order_id || ! in_array($tipe, self::METODE_REFUND_OTOMATIS, true)) {
            return [
                'berhasil'  => false,
                'metode'    => 'manual',
                'referensi' => null,
                'pesan'     => 'Metode pembayaran ini tidak mendukung refund otomatis. Transfer dana secara manual ke pelanggan.',
            ];
        }

        try {
            $respon = (array) Transaction::refund($pesanan->midtrans_order_id, [
                'refund_key' => $refund->kode_refund,
                'amount'     => (int) $refund->jumlah_refund,
                'reason'     => Str::limit($refund->alasan_label . ': ' . $refund->alasan_detail, 200, ''),
            ]);

            $kode = (string) ($respon['status_code'] ?? '');

            if (in_array($kode, ['200', '201'], true)) {
                $id = $respon['refund_chargeback_id'] ?? $respon['transaction_id'] ?? $refund->kode_refund;

                return [
                    'berhasil'  => true,
                    'metode'    => 'midtrans',
                    'referensi' => (string) $id,
                    'pesan'     => 'Dana berhasil dikembalikan lewat Midtrans.',
                ];
            }

            Log::warning('Refund Midtrans ditolak: ' . json_encode($respon));
        } catch (\Throwable $e) {
            Log::warning('Refund Midtrans gagal: ' . $e->getMessage());
        }

        return [
            'berhasil'  => false,
            'metode'    => 'manual',
            'referensi' => null,
            'pesan'     => 'Midtrans tidak dapat memproses refund otomatis. Transfer dana secara manual ke pelanggan.',
        ];
    }

    /**
     * Notifikasi refund dari Midtrans: tandai refund yang sudah disetujui menjadi selesai.
     */
    private function terapkanNotifikasiRefund(Pesanan $pesanan, array $payload): void
    {
        DB::transaction(function () use ($pesanan, $payload) {
            $refund = Refund::where('pesanan_id', $pesanan->id)->lockForUpdate()->first();

            if (! $refund || $refund->status !== 'disetujui') {
                return;
            }

            $id = $payload['refund_chargeback_id'] ?? $payload['transaction_id'] ?? $refund->kode_refund;

            $refund->update([
                'status'                 => 'selesai',
                'metode_pengembalian'    => 'midtrans',
                'referensi_pengembalian' => is_scalar($id) ? (string) $id : $refund->kode_refund,
                'dikembalikan_pada'      => now(),
            ]);
        });
    }

    private function tandaiDibayar(Pesanan $pesanan, ?string $tipe, ?string $ref): void
    {
        DB::transaction(function () use ($pesanan, $tipe, $ref) {
            $p = Pesanan::whereKey($pesanan->id)->lockForUpdate()->first();

            if (! $p || $p->status !== 'menunggu_bayar') {
                return;
            }

            $pembayaran = $p->pembayaran ?: new Pembayaran(['pesanan_id' => $p->id]);
            $pembayaran->fill([
                'metode'        => $tipe ?: 'midtrans',
                'jumlah_bayar'  => $p->total_bayar,
                'referensi'     => $ref,
                'status_bayar'  => 'dikonfirmasi',
                'tanggal_bayar' => now(),
            ])->save();

            $p->update(['status' => 'diproses']);

            foreach ($p->detailPesanans()->with('produk.stok')->get() as $d) {
                $stok = $d->produk->stok;
                if ($stok) {
                    $stok->update(['jumlah' => max(0, $stok->jumlah - $d->qty)]);
                }
            }

            LaporanPenjualan::create([
                'periode'          => now()->format('Y-m'),
                'total_pendapatan' => $p->total_harga,
                'total_pesanan'    => 1,
                'generated_at'     => now(),
            ]);
        });
    }

    private function tandaiGagal(Pesanan $pesanan, ?string $tipe, ?string $ref): void
    {
        DB::transaction(function () use ($pesanan, $tipe, $ref) {
            $p = Pesanan::whereKey($pesanan->id)->lockForUpdate()->first();

            if (! $p || $p->status !== 'menunggu_bayar') {
                return;
            }

            if ($p->pembayaran) {
                $p->pembayaran->update([
                    'metode'       => $tipe ?: $p->pembayaran->metode,
                    'referensi'    => $ref,
                    'status_bayar' => 'ditolak',
                ]);
            }

            $p->update(['status' => 'dibatalkan']);
        });
    }
}
