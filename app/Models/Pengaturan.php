<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Pengaturan extends Model
{
    protected $fillable = ['kunci', 'nilai'];

    protected static ?array $cache = null;
    protected static ?string $logo = null;

    public static function ambil(string $kunci, $default = null)
    {
        if (static::$cache === null) {
            static::$cache = Schema::hasTable('pengaturans')
                ? static::pluck('nilai', 'kunci')->all()
                : [];
        }

        return static::$cache[$kunci] ?? $default;
    }

    public static function simpan(string $kunci, ?string $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        static::$cache = null;
        static::$logo = null;
    }

    /** Toko dianggap BUKA kecuali admin menutupnya secara eksplisit. */
    public static function tokoBuka(): bool
    {
        return static::ambil('toko_buka', '1') !== '0';
    }

    public static function pesanTutup(): string
    {
        return (string) (static::ambil('toko_pesan') ?: 'Toko sedang tutup. Silakan kembali lagi nanti.');
    }

    public static function infoBukaKembali(): ?string
    {
        $info = static::ambil('toko_buka_kembali');

        return $info ? (string) $info : null;
    }

    public static function logoUrl(): string
    {
        if (static::$logo !== null) {
            return static::$logo;
        }

        $path = static::ambil('logo');

        if ($path && Storage::disk('public')->exists($path)) {
            return static::$logo = \App\Support\Media::url($path);
        }

        return static::$logo = asset('images/logo.svg');
    }

    /** Tarif ongkir per km yang diatur admin; kosong = pakai nilai bawaan di config/.env. */
    public static function ongkirPerKm(): int
    {
        $nilai = static::ambil('ongkir_per_km');

        if ($nilai === null || $nilai === '') {
            return (int) config('toko.ongkir.tarif_per_km');
        }

        return max(0, (int) $nilai);
    }

    /**
     * Lokasi produksi / titik asal pengiriman.
     * Memakai titik yang diatur admin; bila belum diatur, pakai nilai bawaan di config/toko.php (.env).
     */
    public static function asal(): array
    {
        $cfg = config('toko.asal');
        $lat = static::ambil('asal_lat');
        $lng = static::ambil('asal_lng');

        $kustom = is_numeric($lat) && is_numeric($lng);

        return [
            'nama'   => (string) (static::ambil('asal_nama') ?: $cfg['nama']),
            'alamat' => (string) static::ambil('asal_alamat', ''),
            'lat'    => $kustom ? (float) $lat : (float) $cfg['lat'],
            'lng'    => $kustom ? (float) $lng : (float) $cfg['lng'],
            'kustom' => $kustom,
        ];
    }

    /** Kontak toko yang diisi admin (kosong bila belum diisi). */
    public static function kontak(): array
    {
        $wa = preg_replace('/\D+/', '', (string) static::ambil('kontak_wa', ''));

        if ($wa !== '' && str_starts_with($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }

        return [
            'wa'      => $wa,
            'wa_url'  => $wa !== '' ? 'https://wa.me/' . $wa : null,
            'email'   => (string) static::ambil('kontak_email', ''),
            'alamat'  => (string) static::ambil('kontak_alamat', ''),
            'jam'     => (string) static::ambil('kontak_jam', ''),
        ];
    }
}
