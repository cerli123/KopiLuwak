<?php

return [
    'asal' => [
        'nama' => env('TOKO_NAMA_LOKASI', 'Kopiluwak Lereng Muria'),
        'lat'  => (float) env('TOKO_LAT', -6.6300),
        'lng'  => (float) env('TOKO_LNG', 110.8750),
    ],

    'ongkir' => [
        'tarif_dasar'  => (int) env('ONGKIR_TARIF_DASAR', 10000),
        'km_dasar'     => (float) env('ONGKIR_KM_DASAR', 3),
        'tarif_per_km' => (int) env('ONGKIR_TARIF_PER_KM', 2500),
        'maks_km'      => (float) env('ONGKIR_MAKS_KM', 150),
        'pembulatan'   => (int) env('ONGKIR_PEMBULATAN', 500),
        'faktor_jalan' => (float) env('ONGKIR_FAKTOR_JALAN', 1.3),
    ],

    // Pencarian alamat & "klik peta -> alamat" (OpenStreetMap Nominatim, dipanggil lewat server).
    'geocoding' => [
        'url'        => env('NOMINATIM_URL', 'https://nominatim.openstreetmap.org'),
        // Nominatim mewajibkan User-Agent yang jelas. Boleh diganti lewat NOMINATIM_USER_AGENT di .env.
        'user_agent' => env('NOMINATIM_USER_AGENT', 'KopiluwakLerengMuria/1.0 (' . env('MAIL_FROM_ADDRESS', 'admin@kopiluwak.id') . ')'),
    ],

    'midtrans' => [
        'server_key'    => env('MIDTRANS_SERVER_KEY'),
        'client_key'    => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'refund' => [
        // Batas hari pengajuan refund (dihitung dari tanggal bayar, atau dari pesanan selesai).
        'batas_hari' => (int) env('REFUND_BATAS_HARI', 7),

        // Pilihan alasan yang tampil di form pelanggan.
        'alasan' => [
            'produk_rusak'        => 'Produk rusak / pecah saat diterima',
            'produk_tidak_sesuai' => 'Produk tidak sesuai pesanan',
            'jumlah_kurang'       => 'Jumlah / isi produk kurang',
            'kemasan_rusak'       => 'Kemasan rusak atau sudah terbuka',
            'kualitas_buruk'      => 'Kualitas tidak sesuai deskripsi',
            'terlambat'           => 'Pesanan terlambat / tidak sampai',
            'salah_pesan'         => 'Salah memesan / berubah pikiran',
            'lainnya'             => 'Alasan lainnya',
        ],

        // Alasan yang wajib disertai foto bukti.
        'wajib_bukti' => [
            'produk_rusak',
            'produk_tidak_sesuai',
            'jumlah_kurang',
            'kemasan_rusak',
            'kualitas_buruk',
        ],
    ],

    'ulasan' => [
        // Status pesanan yang boleh memberi ulasan (pisahkan koma di .env). Bawaan: hanya pesanan yang sudah selesai.
        'status_boleh' => array_values(array_filter(array_map('trim', explode(',', (string) env('ULASAN_STATUS_BOLEH', 'selesai'))))),
        // Ulasan baru langsung tampil. Isi false bila ingin admin menyetujui dulu (ULASAN_LANGSUNG_TAMPIL=false).
        'langsung_tampil' => filter_var(env('ULASAN_LANGSUNG_TAMPIL', true), FILTER_VALIDATE_BOOL),
        'maks_karakter'   => 1000,
    ],
];
