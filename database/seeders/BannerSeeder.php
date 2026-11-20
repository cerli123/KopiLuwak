<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'judul'       => 'Kopi Segar dari Lereng Muria',
                'deskripsi'   => 'Biji pilihan dipanen langsung dari kebun Gunung Muria, dengan rasa autentik di setiap tegukan.',
                'tombol_teks' => 'Lihat Produk',
                'tombol_url'  => '#produk',
                'file'        => 'banner-sunrise-muria.jpg',
                'urutan'      => 1,
            ],
            [
                'judul'       => 'Kopi Luwak Premium Pilihan',
                'deskripsi'   => 'Rasa halus dan aroma khas dalam kemasan eksklusif, cocok untuk hadiah istimewa.',
                'tombol_teks' => 'Lihat Koleksi',
                'tombol_url'  => '#produk',
                'file'        => 'banner-luwak-premium.jpg',
                'urutan'      => 2,
            ],
            [
                'judul'       => 'Secangkir Hangat untuk Harimu',
                'deskripsi'   => 'Intip kebun, proses, dan momen kopi kami di galeri Kopiluwak Lereng Muria.',
                'tombol_teks' => 'Buka Galeri',
                'tombol_url'  => '/galeri',
                'file'        => 'banner-secangkir-hangat.jpg',
                'urutan'      => 3,
            ],
        ];

        foreach ($data as $item) {
            $sumber = database_path('seeders/images/banner/' . $item['file']);

            if (! File::exists($sumber)) {
                continue;
            }

            $tujuan = 'banner/' . $item['file'];
            Storage::disk('public')->put($tujuan, File::get($sumber));

            Banner::updateOrCreate(
                ['gambar' => $tujuan],
                [
                    'judul'       => $item['judul'],
                    'deskripsi'   => $item['deskripsi'],
                    'tombol_teks' => $item['tombol_teks'],
                    'tombol_url'  => $item['tombol_url'],
                    'urutan'      => $item['urutan'],
                    'aktif'       => true,
                ]
            );
        }
    }
}
