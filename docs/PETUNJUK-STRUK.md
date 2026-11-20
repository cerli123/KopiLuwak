# Cetak Struk setelah bayar

- Setelah pembayaran berhasil, halaman Detail Pesanan menampilkan kotak hijau "Cetak Struk" dan "Unduh PDF".
- Struk juga ada di: Riwayat Pesanan (tombol Struk), Detail Pesanan (kartu Struk Pembayaran), dan Admin > Pesanan.
- Struk berukuran kertas thermal 80 mm; tombol Cetak membuka dialog cetak browser (bisa juga "Simpan sebagai PDF").
- Struk hanya tersedia untuk pesanan yang SUDAH LUNAS. Pelanggan hanya bisa membuka struk pesanannya sendiri; admin bisa semua.
- Tidak ada migrasi database. Setelah menimpa file jalankan: php artisan route:clear && php artisan view:clear
