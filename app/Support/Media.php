<?php

namespace App\Support;

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
