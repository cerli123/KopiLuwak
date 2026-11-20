<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

/**
 * Menyajikan file dari storage/app/public (gambar produk, banner, galeri,
 * logo, bukti transfer) langsung lewat Laravel, sehingga TIDAK bergantung
 * pada symlink `public/storage` (php artisan storage:link).
 */
class MediaController extends Controller
{
    public function show(string $path)
    {
        $disk = Storage::disk('public');

        // Tolak path berbahaya (path traversal) dan file yang tidak ada.
        if ($path === '' || str_contains($path, '..') || ! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path, null, [
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
