# Paket Tampilan HP / Android — Kopiluwak Lereng Muria

Tampilan mobile untuk **pengguna** dan **admin**, bisa dipasang sebagai aplikasi Android (PWA).
Desktop tidak berubah: semua gaya hanya aktif di layar <= 768px.

## Cara pasang (3 langkah)

1. **Ekstrak zip ini ke folder root proyek Laravel** (yang berisi `artisan`), pilih "Timpa/Replace".
   Folder `public/` dan `resources/views/` akan bergabung dengan yang sudah ada.
2. **Bersihkan cache view:**
   ```bash
   php artisan view:clear
   ```
3. Buka situs dari HP (Chrome Android). Menu "Pasang aplikasi" / "Tambahkan ke layar utama" akan muncul.

> Syarat PWA: situs harus **HTTPS** (atau localhost) dan diakses dari **root domain**
> (mis. `https://kopiluwak.com`, bukan `https://domain.com/kopiluwak/public`).

## Isi paket

| File | Fungsi |
|---|---|
| `public/css/mobile.css` | Seluruh gaya HP: bottom nav, tabel jadi kartu, grid produk 2 kolom, form, sheet |
| `public/js/mobile.js` | Label tabel otomatis, bottom sheet, tombol pasang aplikasi, indikator offline |
| `public/sw.js` | Service worker (hanya cache aset statis; halaman/pembayaran selalu live) |
| `public/manifest.webmanifest` | Aplikasi **pengguna** (mulai di `/katalog`) |
| `public/manifest-admin.webmanifest` | Aplikasi **admin** terpisah (mulai di `/admin`) |
| `public/offline.html` | Halaman saat tidak ada internet |
| `public/icons/*` | Ikon 192/512, maskable, apple-touch, versi admin |
| `resources/views/partials/mobile-head.blade.php` | Meta + manifest + css (otomatis pilih admin/user) |
| `resources/views/partials/mobile-nav-user.blade.php` | Menu bawah tamu & pelanggan + sheet Akun |
| `resources/views/partials/mobile-nav-admin.blade.php` | Menu bawah admin + sheet "Lainnya" |

## File view yang SUDAH ditambal (menimpa file lama)

`layouts/app`, `layouts/admin`, `layouts/public`, `user/landing`, `auth/login`, `auth/register`.
Perubahannya hanya 3 hal pada tiap file:

1. `viewport` ditambah `, viewport-fit=cover`
2. Setelah `@include('partials.assets')` ditambah `@include('partials.mobile-head')`
3. Sebelum `<script src="{{ asset('js/app.js') }}">` ditambah
   `@include('partials.mobile-nav-user')` (admin: `mobile-nav-admin`). Halaman login/register tidak memakai nav.

Jika Anda sudah mengubah file-file itu, **jangan timpa**; cukup tambahkan 3 baris di atas secara manual.

## Yang Anda dapatkan di HP

**Pengguna:** bottom nav (Beranda · Katalog · Keranjang + badge · Pesanan · Akun), kategori bisa digeser,
grid produk 2 kolom, tombol "Tambah ke Keranjang" menempel di bawah pada detail produk,
sheet Akun (Profil, Refund, Galeri, Keluar), toast & tombol WhatsApp di atas nav.

**Admin:** topbar ringkas + status toko, bottom nav (Dashboard · Pesanan + badge · Produk · Refund + badge · Lainnya),
semua **tabel otomatis jadi kartu** (tanpa edit view), tab status bisa digeser,
tombol simpan form menempel di bawah, sheet "Lainnya" (Stok, Kategori, Laporan, Banner, Galeri, Pengaturan, Keluar).

## Catatan server

- Apache: pastikan `.webmanifest` bermime `application/manifest+json`
  (`AddType application/manifest+json .webmanifest` di `.htaccess`) bila ikon/manifest tidak terbaca.
- Setelah mengubah `sw.js`, naikkan `VERSION` di dalamnya agar cache HP diperbarui.
- Ingin jadi file **APK/Play Store**? Bungkus URL situs ini memakai Bubblewrap (TWA) atau Capacitor;
  manifest & ikon di paket ini sudah siap dipakai.
