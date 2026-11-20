<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Produk, Kategori, Stok};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'stok']);
        if ($request->filled('search'))   $query->where('nama_produk', 'like', '%' . $request->search . '%');
        if ($request->filled('kategori')) $query->where('kategori_id', $request->kategori);
        $produks   = $query->latest()->paginate(15)->withQueryString();
        $kategoris = Kategori::all();
        return view('admin.produk.index', compact('produks', 'kategoris'));
    }

    public function create()
    {
        $kategoris = Kategori::all();
        return view('admin.produk.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_produk'  => 'required|string|max:200',
            'kategori_id'  => 'required|exists:kategoris,id',
            'harga'        => 'required|numeric|min:0',
            'stok_awal'    => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(['nama_produk', 'kategori_id', 'harga', 'deskripsi']);
        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }
        $produk = Produk::create($data);
        Stok::create(['produk_id' => $produk->id, 'jumlah' => $request->stok_awal]);
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Produk $produk)
    {
        $kategoris = Kategori::all();
        $produk->load('stok');
        return view('admin.produk.edit', compact('produk', 'kategoris'));
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'nama_produk'  => 'required|string|max:200',
            'kategori_id'  => 'required|exists:kategoris,id',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'gambar'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $data = $request->only(['nama_produk', 'kategori_id', 'harga', 'deskripsi']);
        if ($request->hasFile('gambar')) {
            if ($produk->gambar) Storage::disk('public')->delete($produk->gambar);
            $data['gambar'] = $request->file('gambar')->store('produk', 'public');
        }
        $produk->update($data);
        $produk->stok()->updateOrCreate(['produk_id' => $produk->id], ['jumlah' => $request->stok]);
        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Produk $produk)
    {
        // Produk yang pernah dipesan tidak dihapus agar riwayat pesanan & laporan tetap utuh.
        if ($produk->detailPesanans()->exists()) {
            return back()->with('error', 'Produk ini pernah dipesan sehingga tidak bisa dihapus. Set stok ke 0 untuk menyembunyikannya dari penjualan.');
        }

        if ($produk->gambar) Storage::disk('public')->delete($produk->gambar);
        $produk->delete();
        return back()->with('success', 'Produk berhasil dihapus.');
    }
}
