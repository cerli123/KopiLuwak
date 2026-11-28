<?php

namespace App\Http\Middleware;

use App\Models\Pengaturan;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokoBuka
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Pengaturan::tokoBuka() || $request->user()?->role === 'admin') {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'pesan'        => Pengaturan::pesanTutup(),
                'toko_buka'    => false,
                'buka_kembali' => Pengaturan::infoBukaKembali(),
            ], 503);
        }

        return redirect()->route('toko.tutup');
    }
}
