<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index(MidtransService $midtrans)
    {
        return view('admin.pengaturan.index', [
            'logoUrl'    => Pengaturan::logoUrl(),
            'logoKustom' => (bool) Pengaturan::ambil('logo'),
            'tokoBuka'   => Pengaturan::tokoBuka(),
            'pesanToko'  => Pengaturan::ambil('toko_pesan'),
            'bukaLagi'   => Pengaturan::ambil('toko_buka_kembali'),
            'kontak'     => Pengaturan::kontak(),
            'asal'       => Pengaturan::asal(),
            'bayar'      => $midtrans->ringkasan(),
            'ongkir'     => [
                'per_km'  => Pengaturan::ongkirPerKm(),
                'kustom'  => Pengaturan::ambil('ongkir_per_km') !== null && Pengaturan::ambil('ongkir_per_km') !== '',
                'bawaan'  => (int) config('toko.ongkir.tarif_per_km'),
                'dasar'   => (int) config('toko.ongkir.tarif_dasar'),
                'km_dasar' => (float) config('toko.ongkir.km_dasar'),
                'bulat'   => max(1, (int) config('toko.ongkir.pembulatan')),
            ],
        ]);
    }

    public function updateToko(Request $request)
    {
        $data = $request->validate([
            'status'       => 'required|in:buka,tutup',
            'pesan'        => 'nullable|string|max:300',
            'buka_kembali' => 'nullable|string|max:100',
        ], [
            'status.required' => 'Pilih status toko.',
            'status.in'       => 'Status toko tidak valid.',
            'pesan.max'       => 'Pesan maksimal 300 karakter.',
            'buka_kembali.max' => 'Keterangan buka kembali maksimal 100 karakter.',
        ]);

        Pengaturan::simpan('toko_buka', $data['status'] === 'buka' ? '1' : '0');
        Pengaturan::simpan('toko_pesan', $data['pesan'] ?? null);
        Pengaturan::simpan('toko_buka_kembali', $data['buka_kembali'] ?? null);

        return back()->with('success', $data['status'] === 'buka'
            ? 'Toko sekarang BUKA. Pelanggan bisa berbelanja kembali.'
            : 'Toko sekarang TUTUP. Aktivitas belanja pelanggan dibatasi.');
    }

    public function updateOngkir(Request $request)
    {
        $data = $request->validate([
            'tarif_per_km' => 'nullable|integer|min:0|max:1000000',
        ], [
            'tarif_per_km.integer' => 'Tarif per km harus berupa angka bulat (tanpa titik atau koma).',
            'tarif_per_km.min'     => 'Tarif per km tidak boleh negatif.',
            'tarif_per_km.max'     => 'Tarif per km terlalu besar (maksimal Rp 1.000.000).',
        ]);

        $nilai = $data['tarif_per_km'] ?? null;

        Pengaturan::simpan('ongkir_per_km', $nilai === null ? null : (string) $nilai);

        return back()->with('success', $nilai === null
            ? 'Tarif ongkir per km dikembalikan ke nilai bawaan (Rp ' . number_format((int) config('toko.ongkir.tarif_per_km'), 0, ',', '.') . ').'
            : 'Tarif ongkir diperbarui: Rp ' . number_format((int) $nilai, 0, ',', '.') . ' per km.');
    }

    public function updateAsal(Request $request)
    {
        $data = $request->validate([
            'asal_nama'   => 'required|string|max:100',
            'asal_alamat' => 'nullable|string|max:250',
            'lat'         => 'required|numeric|between:-11.5,6.5',
            'lng'         => 'required|numeric|between:94,141.5',
        ], [
            'asal_nama.required' => 'Isi nama lokasi produksi.',
            'lat.required'       => 'Tentukan titik lokasi pada peta (atau tekan "Aktifkan Lokasi Saya").',
            'lng.required'       => 'Tentukan titik lokasi pada peta (atau tekan "Aktifkan Lokasi Saya").',
            'lat.between'        => 'Titik lokasi harus berada di wilayah Indonesia.',
            'lng.between'        => 'Titik lokasi harus berada di wilayah Indonesia.',
        ]);

        Pengaturan::simpan('asal_nama', $data['asal_nama']);
        Pengaturan::simpan('asal_alamat', $data['asal_alamat'] ?? null);
        Pengaturan::simpan('asal_lat', (string) round((float) $data['lat'], 7));
        Pengaturan::simpan('asal_lng', (string) round((float) $data['lng'], 7));

        return back()->with('success', 'Lokasi produksi disimpan. Ongkir pesanan baru dihitung dari titik ini.');
    }

    public function resetAsal()
    {
        foreach (['asal_nama', 'asal_alamat', 'asal_lat', 'asal_lng'] as $kunci) {
            Pengaturan::simpan($kunci, null);
        }

        return back()->with('success', 'Lokasi produksi dikembalikan ke titik bawaan.');
    }

    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $lama = Pengaturan::ambil('logo');
        $baru = $request->file('logo')->store('pengaturan', 'public');

        Pengaturan::simpan('logo', $baru);

        if ($lama) {
            Storage::disk('public')->delete($lama);
        }

        return back()->with('success', 'Logo berhasil diperbarui!');
    }

    public function resetLogo()
    {
        $lama = Pengaturan::ambil('logo');

        if ($lama) {
            Storage::disk('public')->delete($lama);
        }

        Pengaturan::simpan('logo', null);

        return back()->with('success', 'Logo dikembalikan ke logo bawaan.');
    }

    public function updateKontak(Request $request)
    {
        $data = $request->validate([
            'kontak_wa'     => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\s\-]*$/'],
            'kontak_email'  => 'nullable|email|max:120',
            'kontak_alamat' => 'nullable|string|max:250',
            'kontak_jam'    => 'nullable|string|max:120',
        ], [
            'kontak_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda + atau -.',
        ]);

        foreach (['kontak_wa', 'kontak_email', 'kontak_alamat', 'kontak_jam'] as $kunci) {
            Pengaturan::simpan($kunci, $data[$kunci] ?? null);
        }

        return back()->with('success', 'Informasi kontak berhasil disimpan.');
    }

    public function tesPembayaran(MidtransService $midtrans)
    {
        [$ok, $pesan] = $midtrans->tesKoneksi();

        return back()->with($ok ? 'success' : 'error', $pesan);
    }
}
