<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Galeri;
use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KatalogController extends Controller
{
    public function landing(Request $request)
    {
        $query = Produk::with(['kategori', 'stok'])->denganRating();

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        $produks   = $query->latest()->paginate(12)->withQueryString();
        $kategoris = Kategori::all();
        $banners   = Banner::aktif()->get();

        $galeriUnggulan = Galeri::siapDetail()
            ? Galeri::where('unggulan', true)->urut()->take(6)->get()
            : collect();
        $fotoMap = Galeri::pluck('gambar')->mapWithKeys(fn ($p) => [basename($p) => $p])->all();

        return view('user.landing', compact('produks', 'kategoris', 'banners', 'galeriUnggulan', 'fotoMap'));
    }

    public function index(Request $request)
    {
        $query = Produk::with(['kategori', 'stok'])->denganRating();

        if ($request->filled('kategori')) {
            $query->where('kategori_id', $request->kategori);
        }

        if ($request->filled('search')) {
            $query->where('nama_produk', 'like', '%' . $request->search . '%');
        }

        $produks   = $query->latest()->paginate(12)->withQueryString();
        $kategoris = Kategori::all();

        return view('user.katalog', compact('produks', 'kategoris'));
    }

    public function show(Produk $produk)
    {
        $produk->load(['kategori', 'stok']);

        $related = Produk::with('stok')
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->take(4)
            ->get();

        $userId = Auth::id();

        $perBintang = Ulasan::tampil()
            ->where('produk_id', $produk->id)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');

        $jumlahUlasan = (int) $perBintang->sum();
        $rataRating   = $jumlahUlasan > 0
            ? round($perBintang->map(fn ($total, $rating) => $total * $rating)->sum() / $jumlahUlasan, 1)
            : 0.0;

        $ulasans = Ulasan::with('user')
            ->where('produk_id', $produk->id)
            ->where(fn ($q) => $q->where('tampil', true)->orWhere('user_id', $userId))
            ->latest()
            ->paginate(5, ['*'], 'ulasan_page')
            ->withQueryString()
            ->fragment('ulasan');

        $pesananBisaUlas = Pesanan::where('user_id', $userId)
            ->whereIn('status', config('toko.ulasan.status_boleh', ['selesai']))
            ->whereHas('detailPesanans', fn ($q) => $q->where('produk_id', $produk->id))
            ->whereDoesntHave('ulasans', fn ($q) => $q->where('produk_id', $produk->id))
            ->latest()
            ->first();

        return view('user.produk-detail', compact(
            'produk', 'related', 'perBintang', 'jumlahUlasan', 'rataRating', 'ulasans', 'pesananBisaUlas'
        ));
    }
}
