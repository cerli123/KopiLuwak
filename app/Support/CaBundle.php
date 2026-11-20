<?php

namespace App\Support;

/**
 * Sertifikat CA untuk koneksi HTTPS keluar (Midtrans, OpenStreetMap, OSRM).
 *
 * Di Windows (XAMPP/Laragon) PHP sering belum punya curl.cainfo sehingga semua
 * request HTTPS gagal dengan "cURL error 60: SSL certificate problem".
 * Kelas ini memakai bundel CA bawaan library Midtrans hanya pada kondisi itu.
 * Di Linux/hosting tidak berubah (memakai CA bawaan server).
 */
class CaBundle
{
    /** Lokasi file CA yang perlu dipakai, atau null bila cukup memakai bawaan PHP/server. */
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

    /** Nilai opsi 'verify' untuk Guzzle / Http::withOptions(). */
    public static function verify(): bool|string
    {
        return self::path() ?? true;
    }
}
