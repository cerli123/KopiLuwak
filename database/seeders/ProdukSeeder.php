<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Stok;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(KategoriSeeder::class);

        $kategori = Kategori::pluck('id', 'nama_kategori');

        foreach ($this->data() as $item) {
            $produk = Produk::updateOrCreate(
                ['nama_produk' => $item['nama']],
                [
                    'kategori_id' => $kategori[$item['kategori']],
                    'deskripsi'   => $item['deskripsi'],
                    'harga'       => $item['harga'],
                    'gambar'      => $this->salinGambar($item['file']),
                ]
            );

            Stok::firstOrCreate(['produk_id' => $produk->id], ['jumlah' => $item['stok']]);
        }
    }

    private function salinGambar(string $file): ?string
    {
        $sumber = database_path('seeders/images/produk/' . $file);

        if (! File::exists($sumber)) {
            return null;
        }

        $tujuan = 'produk/' . $file;
        Storage::disk('public')->put($tujuan, File::get($sumber));

        return $tujuan;
    }

    private function data(): array
    {
        return [
            [
                'nama'      => 'Arabika Muria Biji 250g',
                'kategori'  => 'Biji Kopi',
                'harga'     => 65000,
                'stok'      => 60,
                'file'      => 'arabika-muria-biji.jpg',
                'deskripsi' => 'Biji kopi arabika dari lereng Gunung Muria dengan aroma floral dan rasa asam yang lembut. Cocok untuk seduh manual maupun espresso.',
            ],
            [
                'nama'      => 'Robusta Muria Biji 250g',
                'kategori'  => 'Biji Kopi',
                'harga'     => 48000,
                'stok'      => 80,
                'file'      => 'robusta-muria-biji.jpg',
                'deskripsi' => 'Biji kopi robusta dengan body tebal, rasa pahit khas, dan crema pekat. Pilihan tepat untuk kopi tubruk dan kopi susu.',
            ],
            [
                'nama'      => 'Kopi Bubuk Arabika 200g',
                'kategori'  => 'Kopi Bubuk',
                'harga'     => 55000,
                'stok'      => 45,
                'file'      => 'arabika-bubuk.jpg',
                'deskripsi' => 'Kopi arabika yang sudah digiling halus dan siap diseduh. Rasa seimbang dengan aroma harum yang tahan lama.',
            ],
            [
                'nama'      => 'Kopi Bubuk Robusta Gold 200g',
                'kategori'  => 'Kopi Bubuk',
                'harga'     => 42000,
                'stok'      => 8,
                'file'      => 'robusta-gold-bubuk.jpg',
                'deskripsi' => 'Kopi bubuk robusta sangrai medium-dark dengan rasa mantap dan kental. Cocok untuk penikmat kopi pekat.',
            ],
            [
                'nama'      => 'Kopi Luwak Premium 100g',
                'kategori'  => 'Kopi Luwak',
                'harga'     => 350000,
                'stok'      => 20,
                'file'      => 'kopi-luwak-premium.jpg',
                'deskripsi' => 'Kopi luwak pilihan dengan rasa halus, aroma khas, dan sedikit rasa pahit. Dikemas eksklusif, cocok untuk hadiah atau koleksi sendiri.',
            ],
            [
                'nama'      => 'Kopi Luwak Wild Roast 50g',
                'kategori'  => 'Kopi Luwak',
                'harga'     => 195000,
                'stok'      => 6,
                'file'      => 'kopi-luwak-wild-roast.jpg',
                'deskripsi' => 'Kemasan kecil kopi luwak sangrai khusus untuk mencoba. Karakter rasa lembut dengan aftertaste panjang.',
            ],
            [
                'nama'      => 'Paket Hadiah Kopi Muria',
                'kategori'  => 'Paket Hadiah',
                'harga'     => 185000,
                'stok'      => 15,
                'file'      => 'paket-hadiah-kopi-muria.jpg',
                'deskripsi' => 'Paket hadiah berisi pilihan kopi Muria dalam kotak eksklusif. Pas untuk hadiah ulang tahun, hari raya, atau kolega.',
            ],
            [
                'nama'      => 'Drip Bag Arabika Muria (10 sachet)',
                'kategori'  => 'Paket Hadiah',
                'harga'     => 59000,
                'stok'      => 50,
                'file'      => 'drip-bag-arabika.jpg',
                'deskripsi' => 'Kopi arabika praktis dalam kemasan drip bag. Cukup seduh dengan air panas, tanpa alat tambahan, nikmat di rumah maupun di kantor.',
            ],
        ];
    }
}
