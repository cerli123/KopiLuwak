<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;

class TokoController extends Controller
{
    /** Halaman yang dilihat pelanggan saat toko tutup. */
    public function tutup()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (Pengaturan::tokoBuka()) {
            return redirect()->route('katalog');
        }

        return view('user.toko-tutup', [
            'pesan'      => Pengaturan::pesanTutup(),
            'bukaKembali' => Pengaturan::infoBukaKembali(),
        ]);
    }
}
