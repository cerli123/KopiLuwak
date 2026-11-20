# Update Checkout: alamat otomatis dari peta + Midtrans Sandbox

## Cara pasang
1. Ekstrak zip ini ke folder utama proyek (satu folder dengan `artisan`, `app`, `resources`).
   Pilih "Replace / Timpa" bila diminta. File `.env` TIDAK ikut ditimpa.
2. Buka `.env`, ganti blok Midtrans dengan isi `env-midtrans-sandbox.txt`, isi dua key Sandbox Anda.
3. Jalankan:
   ```
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   php artisan cache:clear
   ```
   Tidak ada migrasi database baru.
4. Admin > Pengaturan > Pembayaran > "Tes Koneksi Midtrans" harus menampilkan "Terhubung ke Midtrans (Sandbox)".

## Yang berubah
- Checkout: klik peta / geser pin / pencarian / "Lokasi Saya" -> kolom Alamat Lengkap terisi otomatis
  (nama tempat, jalan, desa, kecamatan, kota, provinsi, kode pos). Ada kolom "Detail Tambahan" (no. rumah, RT/RW, patokan)
  yang tidak terhapus saat pin dipindah; keduanya digabung saat pesanan dibuat.
- Pencarian & alamat peta kini lewat server (`/alamat/reverse`, `/alamat/cari`), hasil di-cache.
- Midtrans: token sisa simulasi (`SIM-...`) otomatis dibuat ulang saat pindah ke Sandbox; sertifikat SSL untuk Windows;
  token pembayaran selalu diambil terbaru; pesan galat jelas (saat APP_DEBUG=true); peringatan konfigurasi di Pengaturan admin.
