<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\GeocodingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlamatController extends Controller
{
    public function reverse(Request $request, GeocodingService $geo): JsonResponse
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        try {
            $hasil = $geo->reverse((float) $data['lat'], (float) $data['lng']);
        } catch (\RuntimeException $e) {
            return response()->json(['pesan' => $e->getMessage()], 502);
        }

        if (! $hasil) {
            return response()->json(['pesan' => 'Alamat di titik ini tidak ditemukan. Isi alamat secara manual.'], 404);
        }

        return response()->json($hasil);
    }

    public function cari(Request $request, GeocodingService $geo): JsonResponse
    {
        $data = $request->validate(['q' => 'required|string|min:3|max:150']);

        try {
            $list = $geo->cari($data['q']);
        } catch (\RuntimeException $e) {
            return response()->json(['pesan' => $e->getMessage()], 502);
        }

        if (! $list) {
            return response()->json(['pesan' => 'Lokasi tidak ditemukan. Coba kata kunci lain atau klik langsung di peta.'], 404);
        }

        return response()->json($list[0] + ['hasil' => $list]);
    }
}
