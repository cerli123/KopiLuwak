<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Biji Kopi', 'Kopi Bubuk', 'Kopi Luwak', 'Paket Hadiah'] as $nama) {
            Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
