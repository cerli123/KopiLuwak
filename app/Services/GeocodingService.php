<?php

namespace App\Services;

use App\Support\CaBundle;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    private const BUKAN_TEMPAT = [
        'road', 'highway', 'neighbourhood', 'quarter', 'suburb', 'hamlet', 'village', 'city_district',
        'district', 'subdistrict', 'city', 'town', 'municipality', 'county', 'state_district', 'state',
        'region', 'postcode', 'country', 'island', 'administrative', 'locality',
    ];

    public function reverse(float $lat, float $lng): ?array
    {
        $key = 'geo:rev:' . md5(round($lat, 5) . ',' . round($lng, 5));

        $tersimpan = Cache::get($key);
        if (is_array($tersimpan)) {
            return $tersimpan ?: null;
        }

        $data = $this->panggil('/reverse', [
            'format'         => 'jsonv2',
            'lat'            => $lat,
            'lon'            => $lng,
            'zoom'           => 18,
            'addressdetails' => 1,
            'namedetails'    => 1,
            'accept-language' => 'id',
        ]);

        if (! is_array($data) || isset($data['error']) || empty($data['address'] ?? $data['display_name'] ?? null)) {
            Cache::put($key, [], now()->addMinutes(10));

            return null;
        }

        $hasil = $this->susun($data);
        Cache::put($key, $hasil, now()->addDays(30));

        return $hasil;
    }

    public function cari(string $kata): array
    {
        $key = 'geo:cari:' . md5(mb_strtolower(trim($kata)));

        $tersimpan = Cache::get($key);
        if (is_array($tersimpan)) {
            return $tersimpan;
        }

        $list = $this->panggil('/search', [
            'format'          => 'jsonv2',
            'q'               => $kata,
            'limit'           => 5,
            'countrycodes'    => 'id',
            'addressdetails'  => 1,
            'namedetails'     => 1,
            'accept-language' => 'id',
        ]);

        $hasil = [];
        foreach ((array) $list as $d) {
            if (! is_array($d) || ! isset($d['lat'], $d['lon'])) {
                continue;
            }
            $hasil[] = [
                'lat'    => (float) $d['lat'],
                'lng'    => (float) $d['lon'],
                'alamat' => $this->susun($d)['alamat'],
            ];
        }

        Cache::put($key, $hasil, now()->addDay());

        return $hasil;
    }

    private function susun(array $d): array
    {
        $a = (array) ($d['address'] ?? []);

        $jalan = $this->ambil($a, ['road', 'pedestrian', 'footway', 'path', 'residential', 'living_street', 'service', 'track']);
        $nomor = $this->ambil($a, ['house_number']);

        $tipe = (string) ($d['addresstype'] ?? $d['type'] ?? '');
        $nama = trim((string) ($d['namedetails']['name'] ?? $d['name'] ?? ''));
        $namaTempat = ($nama !== '' && ! in_array($tipe, self::BUKAN_TEMPAT, true)) ? $nama : null;

        $bagian = [];
        if ($namaTempat) {
            $bagian[] = $namaTempat;
        }
        if ($jalan) {
            $bagian[] = $nomor ? $jalan . ' No. ' . $nomor : $jalan;
        } elseif ($nomor) {
            $bagian[] = 'No. ' . $nomor;
        }

        foreach (['hamlet', 'neighbourhood', 'quarter', 'village', 'suburb', 'city_district', 'district', 'subdistrict', 'city', 'town', 'municipality', 'county', 'state_district', 'state', 'postcode'] as $k) {
            if (! empty($a[$k])) {
                $bagian[] = (string) $a[$k];
            }
        }

        $bagian = $this->unik($bagian);

        if (count($bagian) <= 1 && ! empty($d['display_name'])) {
            $bagian = $this->unik(array_map('trim', explode(',', (string) $d['display_name'])));
            $bagian = array_values(array_filter($bagian, fn ($b) => strcasecmp($b, 'Indonesia') !== 0));
        }

        return [
            'alamat'      => mb_substr(implode(', ', $bagian), 0, 500),
            'jalan'       => $jalan,
            'nama_tempat' => $namaTempat,
            'sumber'      => 'OpenStreetMap',
        ];
    }

    private function ambil(array $a, array $kunci): ?string
    {
        foreach ($kunci as $k) {
            if (! empty($a[$k])) {
                return (string) $a[$k];
            }
        }

        return null;
    }

    private function unik(array $bagian): array
    {
        $lihat = [];
        $hasil = [];
        foreach ($bagian as $b) {
            $b = trim((string) $b);
            $n = mb_strtolower($b);
            if ($b === '' || isset($lihat[$n])) {
                continue;
            }
            $lihat[$n] = true;
            $hasil[]   = $b;
        }

        return $hasil;
    }

    private function panggil(string $path, array $query): mixed
    {
        $base = rtrim((string) config('toko.geocoding.url'), '/');
        $ua   = (string) config('toko.geocoding.user_agent');

        try {
            $res = Http::withOptions(['verify' => CaBundle::verify()])
                ->withHeaders(['User-Agent' => $ua, 'Accept-Language' => 'id,en;q=0.5'])
                ->acceptJson()
                ->timeout(8)
                ->get($base . $path, $query);
        } catch (\Throwable $e) {
            Log::warning('Geocoding gagal: ' . $e->getMessage());
            throw new \RuntimeException('Layanan peta tidak dapat dihubungi.');
        }

        if (! $res->successful()) {
            Log::warning('Geocoding HTTP ' . $res->status());
            throw new \RuntimeException('Layanan peta sedang sibuk. Coba lagi sebentar.');
        }

        return $res->json();
    }
}
