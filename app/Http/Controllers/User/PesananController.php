<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Pesanan;
use App\Services\MidtransService;
use App\Services\OngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PesananController extends Controller
{
    public function checkout()
    {
        $items = Keranjang::with(['produk.stok'])->where('user_id', Auth::id())->get();

        if ($items->isEmpty()) {
            return redirect()->route('keranjang')->with('error', 'Keranjang kosong.');
        }

        $total = $items->sum(fn ($i) => $i->subtotal());
        $asal  = Pengaturan::asal();

        return view('user.checkout', compact('items', 'total', 'asal'));
    }

    public function store(Request $request, OngkirService $ongkir, MidtransService $midtrans)
    {
        $request->validate([
            'nama_penerima'     => 'required|string|max:100',
            'no_hp_penerima'    => 'required|string|max:20',
            'alamat_pengiriman' => 'required|string|max:500',
            'detail_alamat'     => 'nullable|string|max:200',
            'lat'               => 'required|numeric|between:-90,90',
            'lng'               => 'required|numeric|between:-180,180',
        ], [
            'alamat_pengiriman.required' => 'Alamat lengkap wajib diisi. Klik peta untuk mengisinya otomatis.',
            'lat.required' => 'Tentukan lokasi pengiriman pada peta.',
            'lng.required' => 'Tentukan lokasi pengiriman pada peta.',
        ]);

        $items = Keranjang::with('produk.stok')->where('user_id', Auth::id())->get();

        if ($items->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        foreach ($items as $item) {
            if ($item->qty > $item->produk->sisa_stok) {
                return redirect()->route('keranjang')
                    ->with('error', 'Stok ' . $item->produk->nama_produk . ' tidak mencukupi.');
            }
        }

        try {
            $hitung = $ongkir->hitung((float) $request->lat, (float) $request->lng);
        } catch (\RuntimeException $e) {
            return back()->withInput()->withErrors(['lat' => $e->getMessage()]);
        }

        $subtotal = $items->sum(fn ($i) => $i->subtotal());

        // Alamat dari peta + detail tambahan pelanggan (no. rumah, RT/RW, patokan).
        $alamat = trim(($request->filled('detail_alamat') ? trim($request->detail_alamat) . ', ' : '') . trim($request->alamat_pengiriman));
        $alamat = Str::limit($alamat, 700, '');

        $pesanan = DB::transaction(function () use ($request, $items, $subtotal, $hitung, $alamat) {
            $pesanan = Pesanan::create([
                'user_id'           => Auth::id(),
                'kode_pesanan'      => Pesanan::generateKode(),
                'total_harga'       => $subtotal,
                'status'            => 'menunggu_bayar',
                'nama_penerima'     => $request->nama_penerima,
                'no_hp_penerima'    => $request->no_hp_penerima,
                'alamat_pengiriman' => $alamat,
                'lat'               => $request->lat,
                'lng'               => $request->lng,
                'jarak_km'          => $hitung['jarak_km'],
                'ongkir'            => $hitung['ongkir'],
            ]);

            foreach ($items as $item) {
                DetailPesanan::create([
                    'pesanan_id'   => $pesanan->id,
                    'produk_id'    => $item->produk_id,
                    'qty'          => $item->qty,
                    'harga_satuan' => $item->produk->harga,
                ]);
            }

            Pembayaran::create([
                'pesanan_id'   => $pesanan->id,
                'metode'       => 'midtrans',
                'jumlah_bayar' => $pesanan->total_bayar,
                'status_bayar' => 'pending',
            ]);

            Keranjang::where('user_id', Auth::id())->delete();

            return $pesanan;
        });

        try {
            $midtrans->buatSnapToken($pesanan);
        } catch (\Throwable $e) {
            Log::error('Gagal membuat token Midtrans: ' . $e->getMessage());

            // Pesanan sudah tersimpan; halaman pembayaran akan mencoba membuat sesi pembayaran lagi.
            return redirect()->route('pembayaran.form', $pesanan);
        }

        return redirect()->route('pembayaran.form', $pesanan)->with('success', 'Pesanan berhasil dibuat! Silakan selesaikan pembayaran.');
    }

    public function index()
    {
        $pesanans = Pesanan::with(['detailPesanans.produk', 'pembayaran', 'refund'])
            ->where('user_id', Auth::id())->latest()->paginate(10);

        return view('user.pesanan', compact('pesanans'));
    }

    public function show(Pesanan $pesanan, MidtransService $midtrans)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan = $midtrans->sinkron($pesanan);
        $pesanan->load(['detailPesanans.produk', 'pembayaran', 'refund', 'ulasans']);

        return view('user.pesanan-detail', compact('pesanan'));
    }
}