<?php

namespace App\Support;

class CaBundle
{
    public static function path(): ?string
    {
        if (PHP_OS_FAMILY !== 'Windows') {
            return null;
        }

        $cainfo = (string) ini_get('curl.cainfo');
        if ($cainfo !== '' && is_file($cainfo)) {
            return null;
        }

        $bundel = base_path('vendor/midtrans/midtrans-php/data/cacert.pem');

        return is_file($bundel) ? $bundel : null;
    }

    public static function verify(): bool|string
    {
        return self::path() ?? true;
    }
}
