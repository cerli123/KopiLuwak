<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Services\OngkirService;
use Illuminate\Http\Request;

class OngkirController extends Controller
{
    public function hitung(Request $request, OngkirService $ongkir)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        try {
            $hasil = $ongkir->hitung((float) $data['lat'], (float) $data['lng']);
        } catch (\RuntimeException $e) {
            return response()->json(['pesan' => $e->getMessage()], 422);
        }

        $subtotal = Keranjang::with('produk')
            ->where('user_id', $request->user()->id)
            ->get()
            ->sum(fn ($item) => $item->subtotal());

        return response()->json($hasil + [
            'subtotal' => (int) $subtotal,
            'total'    => (int) $subtotal + $hasil['ongkir'],
        ]);
    }
}
