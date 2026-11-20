<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\Request;

class UlasanController extends Controller
{
    public function index(Request $request)
    {
        $query = Ulasan::with(['user', 'produk', 'pesanan']);

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('komentar', 'like', "%{$q}%")
                    ->orWhereHas('produk', fn ($p) => $p->where('nama_produk', 'like', "%{$q}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('nama', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', (int) $request->rating);
        }

        match ($request->status) {
            'tampil'        => $query->where('tampil', true),
            'disembunyikan' => $query->where('tampil', false),
            'belum_dibalas' => $query->whereNull('balasan_admin'),
            default         => null,
        };

        $ulasans = $query->latest()->paginate(15)->withQueryString();

        $ringkas = [
            'total'         => Ulasan::count(),
            'rata_rata'     => round((float) Ulasan::avg('rating'), 1),
            'belum_dibalas' => Ulasan::whereNull('balasan_admin')->count(),
            'disembunyikan' => Ulasan::where('tampil', false)->count(),
        ];

        return view('admin.ulasan.index', compact('ulasans', 'ringkas'));
    }

    /** Tampilkan / sembunyikan ulasan dari halaman publik. */
    public function tampil(Ulasan $ulasan)
    {
        $ulasan->update(['tampil' => ! $ulasan->tampil]);

        return back()->with('success', $ulasan->tampil ? 'Ulasan ditampilkan.' : 'Ulasan disembunyikan.');
    }

    /** Balas ulasan (kosongkan untuk menghapus balasan). */
    public function balas(Request $request, Ulasan $ulasan)
    {
        $data = $request->validateWithBag('balas_' . $ulasan->id, [
            'balasan_admin' => 'nullable|string|max:1000',
        ]);

        $balasan = trim((string) ($data['balasan_admin'] ?? ''));

        $ulasan->update([
            'balasan_admin' => $balasan !== '' ? $balasan : null,
            'dibalas_at'    => $balasan !== '' ? now() : null,
        ]);

        return back()->with('success', $balasan !== '' ? 'Balasan tersimpan.' : 'Balasan dihapus.');
    }

    public function destroy(Ulasan $ulasan)
    {
        $ulasan->delete();

        return back()->with('success', 'Ulasan dihapus.');
    }
}
