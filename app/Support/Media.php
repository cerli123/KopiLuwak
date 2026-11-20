<?php

namespace App\Support;

/**
 * URL file di storage/app/public lewat route /media (MediaController),
 * sehingga gambar tampil tanpa harus menjalankan `php artisan storage:link`.
 */
class Media
{
    public static function url(?string $path): string
    {
        if (! $path) {
            return asset('images/no-image.png');
        }

        return url('media/' . ltrim($path, '/'));
    }
}
