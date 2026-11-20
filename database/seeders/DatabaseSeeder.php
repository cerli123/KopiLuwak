<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            KategoriSeeder::class,
            ProdukSeeder::class,
            BannerSeeder::class,
            // Foto asli (galeri + banner + foto produk). Menggantikan GaleriSeeder contoh
            // supaya galeri tidak berisi foto ganda. Aman dijalankan berulang kali.
            FotoKopiLuwakSeeder::class,
        ]);
    }
}
