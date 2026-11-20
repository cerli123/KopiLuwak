<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function index()
    {
        $items = Keranjang::with(['produk.stok'])->where('user_id', Auth::id())->get();
        $total = $items->sum(fn($i) => $i->subtotal());
        return view('user.keranjang', compact('items', 'total'));
    }

    public function tambah(Request $request)
    {
        $request->validate(['produk_id' => 'required|exists:produks,id', 'qty' => 'required|integer|min:1']);
        $produk = Produk::with('stok')->findOrFail($request->produk_id);

        if (!$produk->stok || $produk->stok->jumlah < $request->qty) {
            return back()->with('error', 'Stok tidak mencukupi.');
        }

        $item = Keranjang::firstOrCreate(
            ['user_id' => Auth::id(), 'produk_id' => $request->produk_id],
            ['qty' => 0]
        );

        // Jumlah di keranjang tidak boleh melebihi stok yang tersedia.
        if ((int) $item->qty + (int) $request->qty > $produk->stok->jumlah) {
            if ((int) $item->qty === 0) {
                $item->delete();
            }

            return back()->with('error', 'Stok ' . $produk->nama_produk . ' hanya tersisa ' . $produk->stok->jumlah . ' pcs.');
        }

        $item->increment('qty', $request->qty);

        return back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['qty' => 'required|integer|min:1']);
        $item = Keranjang::with('produk.stok')->where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $stok = $item->produk->stok?->jumlah ?? 0;

        if ($request->qty > $stok) {
            return back()->with('error', 'Stok ' . $item->produk->nama_produk . ' hanya tersisa ' . $stok . ' pcs.');
        }

        $item->update(['qty' => $request->qty]);
        return back()->with('success', 'Keranjang diperbarui.');
    }

    public function hapus(int $id)
    {
        Keranjang::where('id', $id)->where('user_id', Auth::id())->delete();
        return back()->with('success', 'Item dihapus dari keranjang.');
    }
}
