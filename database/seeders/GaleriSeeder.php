<?php

namespace Database\Seeders;

use App\Models\Galeri;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class GaleriSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'kebun-kopi.jpg'            => 'Kebun Kopi Lereng Muria',
            'pagi-di-atas-awan.jpg'     => 'Pagi di Atas Awan',
            'panen-biji-merah.jpg'      => 'Panen Biji Merah',
            'biji-kopi-pilihan.jpg'     => 'Biji Kopi Pilihan',
            'proses-sangrai.jpg'        => 'Proses Sangrai',
            'kopi-bubuk-segar.jpg'      => 'Kopi Bubuk Segar',
            'secangkir-kopi-hangat.jpg' => 'Secangkir Kopi Hangat',
            'kemasan-kopiluwak.jpg'     => 'Kemasan Kopiluwak',
        ];

        foreach ($data as $file => $judul) {
            $sumber = database_path('seeders/images/galeri/' . $file);

            if (! File::exists($sumber)) {
                continue;
            }

            $tujuan = 'galeri/' . $file;
            Storage::disk('public')->put($tujuan, File::get($sumber));

            Galeri::updateOrCreate(['gambar' => $tujuan], ['judul' => $judul]);
        }
    }
}
