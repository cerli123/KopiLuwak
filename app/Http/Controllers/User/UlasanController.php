<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UlasanController extends Controller
{
    /** Beri ulasan untuk satu produk dalam satu pesanan milik pelanggan. */
    public function store(Request $request, Pesanan $pesanan, Produk $produk)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        if (! $pesanan->bisaDiulas()) {
            return back()->with('error', 'Ulasan hanya bisa diberikan setelah pesanan selesai.');
        }

        abort_unless($pesanan->detailPesanans()->where('produk_id', $produk->id)->exists(), 404);

        if (Ulasan::where('pesanan_id', $pesanan->id)->where('produk_id', $produk->id)->exists()) {
            return back()->with('error', 'Anda sudah mengulas produk ini untuk pesanan tersebut.');
        }

        $data = $this->validasi($request, 'ulasan_' . $pesanan->id . '_' . $produk->id);

        Ulasan::create([
            'user_id'    => Auth::id(),
            'produk_id'  => $produk->id,
            'pesanan_id' => $pesanan->id,
            'rating'     => $data['rating'],
            'komentar'   => $data['komentar'] ?? null,
            'tampil'     => (bool) config('toko.ulasan.langsung_tampil', true),
        ]);

        $pesan = config('toko.ulasan.langsung_tampil', true)
            ? 'Terima kasih! Ulasan Anda sudah tampil.'
            : 'Terima kasih! Ulasan Anda akan tampil setelah ditinjau admin.';

        return back()->with('success', $pesan);
    }

    /** Ubah ulasan milik sendiri. */
    public function update(Request $request, Ulasan $ulasan)
    {
        abort_if($ulasan->user_id !== Auth::id(), 403);

        $data = $this->validasi($request, 'ulasan_' . $ulasan->pesanan_id . '_' . $ulasan->produk_id);

        $ulasan->update([
            'rating'   => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
        ]);

        return back()->with('success', 'Ulasan Anda diperbarui.');
    }

    /** Hapus ulasan milik sendiri. */
    public function destroy(Ulasan $ulasan)
    {
        abort_if($ulasan->user_id !== Auth::id(), 403);

        $ulasan->delete();

        return back()->with('success', 'Ulasan Anda dihapus.');
    }

    private function validasi(Request $request, string $bag): array
    {
        $maks = (int) config('toko.ulasan.maks_karakter', 1000);

        return $request->validateWithBag($bag, [
            'rating'   => 'required|integer|between:1,5',
            'komentar' => 'nullable|string|max:' . $maks,
        ], [
            'rating.required' => 'Pilih jumlah bintang terlebih dahulu.',
            'rating.between'  => 'Rating harus 1 sampai 5 bintang.',
            'komentar.max'    => 'Komentar maksimal ' . $maks . ' karakter.',
        ]);
    }
}
