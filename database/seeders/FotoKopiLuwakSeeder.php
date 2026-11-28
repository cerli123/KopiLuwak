<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Galeri;
use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FotoKopiLuwakSeeder extends Seeder
{
    public function run(): void
    {
        $this->galeri();
        $this->banner();
        $this->produk();
    }

    private function salin(string $file, string $folder): ?string
    {
        $sumber = database_path('seeders/images/foto/' . $file);

        if (! File::exists($sumber)) {
            $this->command?->warn("Foto tidak ditemukan: {$file}");

            return null;
        }

        $tujuan = $folder . '/' . $file;
        Storage::disk('public')->put($tujuan, File::get($sumber));

        return $tujuan;
    }

    private function galeri(): void
    {
        $data = [
            ['03-kaleng-lereng-muria.jpg', 'Kopi Luwak Lereng Muria dalam Kaleng Eksklusif', 'Produk', true,
                'Kemasan kaleng hitam berhias emas yang menjaga aroma dan kesegaran kopi luwak Lereng Muria, siap dikirim atau dijadikan hadiah.'],
            ['07-secangkir-kopi-di-kebun.jpg', 'Secangkir Kopi di Tengah Kebun', 'Produk', true,
                'Menikmati kopi luwak dan ice coffee Lereng Muria di tengah rimbunnya kebun, ditemani kemasan kaleng khas kami.'],
            ['06-biji-bunga-buah-merah.jpg', 'Biji Kopi, Bunga, dan Buah Merah', 'Kebun & Biji', true,
                'Biji kopi luwak berdampingan dengan bunga kopi yang putih dan buah kopi merah matang langsung dari kebun.'],
            ['04-sortir-biji-luwak.jpg', 'Sortir Biji Kopi Luwak Secara Manual', 'Proses', true,
                'Biji kopi luwak dipilah satu per satu dengan tangan agar hanya biji terbaik yang sampai ke cangkir Anda.'],
            ['02-biji-luwak-dalam-genggaman.jpg', 'Biji Kopi Luwak Pilihan', 'Kebun & Biji', true,
                'Segenggam biji kopi luwak yang sudah dibersihkan, dengan bunga kopi di antara dedaunan kebun.'],
            ['05-kopi-bubuk-giling.jpg', 'Kopi Bubuk Hasil Gilingan', 'Produk', false,
                'Kopi bubuk berwarna cokelat pekat dengan aroma kuat, digiling dari biji sangrai pilihan.'],
            ['08-kandang-luwak.jpg', 'Kandang Luwak di Sentra Produksi', 'Sentra Produksi', true,
                'Pengunjung melihat langsung deretan kandang luwak di sentra produksi kami, tempat kopi luwak berawal.'],
            ['13-saung-penyambutan.jpg', 'Saung Penyambutan Sentral Produsen', 'Sentra Produksi', false,
                'Saung bambu bertuliskan "Selamat Datang di Sentral Produsen Kopi Luwak Jepara", tempat tamu disambut dan menikmati kopi.'],
            ['09-kunjungan-sentra-produksi.jpg', 'Kunjungan Tamu Mancanegara', 'Kunjungan', false,
                'Wisatawan mancanegara berfoto bersama pengelola di depan papan informasi sentra produksi.'],
            ['10-foto-bersama-area-kandang.jpg', 'Foto Bersama di Area Kandang', 'Kunjungan', false,
                'Rombongan tamu berfoto bersama di area kandang setelah berkeliling melihat proses produksi.'],
            ['11-bersantai-mencicipi-kopi.jpg', 'Bersantai Sambil Mencicipi Kopi', 'Kunjungan', false,
                'Tamu bersantai di pendopo sambil mencicipi kopi luwak yang baru diseduh.'],
            ['12-pengecekan-produk.jpg', 'Pengecekan Produk Kemasan', 'Kunjungan', false,
                'Tamu memeriksa langsung produk kemasan sebelum dibawa pulang sebagai oleh-oleh.'],
            ['14-kunjungan-keluarga-kebun.jpg', 'Kunjungan Keluarga di Kebun', 'Kunjungan', false,
                'Keluarga dan rombongan berkeliling kebun, mengenal kopi luwak dari dekat.'],
            ['01-kolase-sentral-produsen.jpg', 'Kolase Sentral Produsen Kopi Luwak', 'Sentra Produksi', false,
                'Kumpulan potret kegiatan di Sentral Produsen Kopi Luwak Jepara, dari kebun, pengolahan, hingga kunjungan tamu.'],
        ];

        foreach ($data as $i => [$file, $judul, $kategori, $unggulan, $deskripsi]) {
            $path = $this->salin($file, 'galeri');

            if (! $path) {
                continue;
            }

            Galeri::updateOrCreate(['gambar' => $path], [
                'judul'     => $judul,
                'deskripsi' => $deskripsi,
                'kategori'  => $kategori,
                'urutan'    => $i + 1,
                'unggulan'  => $unggulan,
            ]);
        }
    }

    private function banner(): void
    {
        Banner::whereIn('gambar', [
            'banner/banner-sunrise-muria.jpg',
            'banner/banner-luwak-premium.jpg',
            'banner/banner-secangkir-hangat.jpg',
        ])->update(['aktif' => false]);

        $data = [
            ['03-kaleng-lereng-muria.jpg', 'Kopi Luwak Lereng Muria',
                'Kopi luwak asli dalam kemasan kaleng eksklusif, aroma terjaga dan siap jadi hadiah istimewa.',
                'Lihat Produk', '#produk', 1],
            ['06-biji-bunga-buah-merah.jpg', 'Dari Kebun ke Cangkir Anda',
                'Biji kopi dipanen dari kebun di lereng Gunung Muria, diolah dengan telaten tanpa terburu-buru.',
                'Lihat Proses', '#proses', 2],
            ['04-sortir-biji-luwak.jpg', 'Dipilih Satu per Satu',
                'Setiap biji disortir dengan tangan. Intip proses dan momen kami di galeri.',
                'Buka Galeri', '/galeri', 3],
        ];

        foreach ($data as [$file, $judul, $deskripsi, $teks, $url, $urutan]) {
            $path = $this->salin($file, 'banner');

            if (! $path) {
                continue;
            }

            Banner::updateOrCreate(['gambar' => $path], [
                'judul'       => $judul,
                'deskripsi'   => $deskripsi,
                'tombol_teks' => $teks,
                'tombol_url'  => $url,
                'urutan'      => $urutan,
                'aktif'       => true,
            ]);
        }
    }

    private function produk(): void
    {
        $peta = [
            'Kopi Luwak Premium 100g'    => '03-kaleng-lereng-muria.jpg',
            'Kopi Luwak Wild Roast 50g'  => '02-biji-luwak-dalam-genggaman.jpg',
            'Kopi Bubuk Arabika 200g'    => '05-kopi-bubuk-giling.jpg',
        ];

        foreach ($peta as $nama => $file) {
            $produk = Produk::where('nama_produk', $nama)->first();

            if (! $produk) {
                continue;
            }

            $path = $this->salin($file, 'produk');

            if ($path) {
                $produk->update(['gambar' => $path]);
            }
        }
    }
}
