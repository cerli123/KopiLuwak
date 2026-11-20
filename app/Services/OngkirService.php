<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Support\CaBundle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OngkirService
{
    public function hitung(float $lat, float $lng): array
    {
        $cfg  = config('toko.ongkir');
        $asal = Pengaturan::asal();

        [$jarak, $sumber] = $this->jarakJalan((float) $asal['lat'], (float) $asal['lng'], $lat, $lng);

        if ($jarak > $cfg['maks_km']) {
            throw new \RuntimeException('Lokasi di luar jangkauan pengiriman (maksimal ' . $cfg['maks_km'] . ' km).');
        }

        $biaya  = $cfg['tarif_dasar'] + max(0, $jarak - $cfg['km_dasar']) * Pengaturan::ongkirPerKm();
        $bulat  = max(1, $cfg['pembulatan']);
        $ongkir = (int) (ceil($biaya / $bulat) * $bulat);

        return [
            'jarak_km' => round($jarak, 1),
            'ongkir'   => $ongkir,
            'sumber'   => $sumber,
        ];
    }

    private function jarakJalan(float $lat1, float $lng1, float $lat2, float $lng2): array
    {
        $key = 'jarak:' . md5(round($lat1, 4) . ',' . round($lng1, 4) . '|' . round($lat2, 4) . ',' . round($lng2, 4));

        $tersimpan = Cache::get($key);
        if (is_array($tersimpan)) {
            return $tersimpan;
        }

        $hasil = $this->dariOsrm($lat1, $lng1, $lat2, $lng2);

        if ($hasil !== null) {
            $data = [$hasil, 'rute'];
            Cache::put($key, $data, now()->addDay());

            return $data;
        }

        $data = [$this->garisLurus($lat1, $lng1, $lat2, $lng2) * config('toko.ongkir.faktor_jalan'), 'estimasi'];
        Cache::put($key, $data, now()->addMinutes(10));

        return $data;
    }

    private function dariOsrm(float $lat1, float $lng1, float $lat2, float $lng2): ?float
    {
        try {
            $url = "https://router.project-osrm.org/route/v1/driving/{$lng1},{$lat1};{$lng2},{$lat2}";
            $res = Http::withOptions(['verify' => CaBundle::verify()])->timeout(6)->acceptJson()->get($url, ['overview' => 'false']);

            if ($res->ok() && $res->json('code') === 'Ok' && $res->json('routes.0.distance') !== null) {
                return $res->json('routes.0.distance') / 1000;
            }
        } catch (\Throwable $e) {
            Log::warning('OSRM gagal: ' . $e->getMessage());
        }

        return null;
    }

    private function garisLurus(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $r    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
