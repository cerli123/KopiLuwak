<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stok;
use Illuminate\Http\Request;

class StokController extends Controller
{
    public function index()
    {
        $stoks = Stok::with(['produk.kategori'])->orderBy('jumlah')->paginate(20);
        return view('admin.stok.index', compact('stoks'));
    }
    public function update(Request $request, Stok $stok)
    {
        $request->validate(['jumlah' => 'required|integer|min:0']);
        $stok->update(['jumlah' => $request->jumlah]);
        return back()->with('success', 'Stok berhasil diperbarui.');
    }
}
