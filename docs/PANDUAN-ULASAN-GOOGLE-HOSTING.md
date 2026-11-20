# Fitur Baru: Ulasan Produk + Login Google + Panduan Hosting

## 1. Isi pembaruan

**Login dengan Google (Gmail asli)**
- Tombol "Masuk/Daftar dengan Google" di halaman `/login` dan `/register`.
- Email Google yang sama dengan akun lama otomatis dihubungkan (tidak membuat akun ganda).
- Akun baru lewat Google dibuat sebagai pelanggan, tanpa password. Di halaman Profil mereka bisa membuat password (opsional).
- Hanya email yang sudah terverifikasi Google yang diterima.
- Login email + password yang lama tetap berfungsi.

**Ulasan produk**
- Pelanggan memberi rating 1-5 bintang + komentar, hanya untuk produk yang ada di pesanannya dan setelah pesanan berstatus **selesai**.
- Satu ulasan per produk per pesanan. Pelanggan bisa mengubah / menghapus ulasannya sendiri.
- Form ulasan ada di **Detail Pesanan** dan **Detail Produk**.
- Detail Produk menampilkan rata-rata rating, grafik sebaran bintang, dan daftar ulasan (5 per halaman). Katalog dan landing menampilkan bintang di kartu produk.
- Nama pelanggan disingkat ("Budi S.") demi privasi.
- Admin: menu **Ulasan** (cari, filter rating/status, balas, sembunyikan, hapus).

Pengaturan di `.env`:

| Kunci | Fungsi |
|---|---|
| `ULASAN_STATUS_BOLEH=selesai` | Status pesanan yang boleh diulas, mis. `dikirim,selesai` |
| `ULASAN_LANGSUNG_TAMPIL=true` | `false` = ulasan harus disetujui admin dulu (admin klik "Tampilkan") |

## 2. Membuat Google Client ID (gratis, sekitar 10 menit)

1. Buka https://console.cloud.google.com lalu buat **Project** baru.
2. Menu **APIs & Services > OAuth consent screen**: pilih **External**, isi nama aplikasi, email dukungan, dan email developer. Scope cukup bawaan (`email`, `profile`, `openid`).
3. Menu **Credentials > Create Credentials > OAuth client ID > Web application**. Isi:
   - **Authorized JavaScript origins**: `https://DOMAIN-ANDA.com` (dan `http://localhost:8000` untuk tes lokal)
   - **Authorized redirect URIs**:
     - `https://DOMAIN-ANDA.com/auth/google/callback`
     - `http://localhost:8000/auth/google/callback`
4. Salin **Client ID** dan **Client Secret** ke `.env`:
   ```
   GOOGLE_CLIENT_ID=xxxxxxxx.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxx
   GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
   ```
5. **Penting:** di OAuth consent screen, ubah status dari **Testing** ke **In production** (tombol *Publish app*). Selama berstatus Testing, hanya email yang didaftarkan sebagai *Test users* yang bisa login. Scope dasar (email/profil) tidak butuh verifikasi Google.

## 3. Menjalankan di komputer sendiri

```bash
# salin .env lama Anda (file .env tidak ikut di zip), tambahkan kunci GOOGLE_* dan ULASAN_*
php artisan migrate        # membuat tabel ulasans + kolom google di users
php artisan config:clear
php artisan serve
```

## 4. Hosting (cPanel / shared hosting)

Syarat: PHP 8.2+, ekstensi `curl`, `mbstring`, `openssl`, `pdo_mysql`, `gd`/`fileinfo`, dan **SSL (https)** aktif. Google dan Midtrans mewajibkan https untuk produksi.

1. **Upload & ekstrak** zip ke folder di atas `public_html` (mis. `/home/akun/kopiluwak`). Folder `vendor` sudah ikut, jadi tidak perlu Composer.
2. **Arahkan domain ke folder `public`**: cPanel > Domains > ubah *Document Root* menjadi `/home/akun/kopiluwak/public`. Bila tidak bisa, isi `public_html` dengan isi folder `public`, lalu ubah dua baris di `public_html/index.php` agar menunjuk ke folder aplikasi:
   ```php
   require __DIR__.'/../kopiluwak/vendor/autoload.php';
   $app = require_once __DIR__.'/../kopiluwak/bootstrap/app.php';
   ```
3. **Database**: cPanel > MySQL Databases, buat database + user, beri *All Privileges*.
4. **File `.env`**: salin `env-production.contoh.txt` menjadi `.env` di folder aplikasi, lalu isi `APP_KEY` (ambil dari `.env` lokal Anda), `APP_URL=https://DOMAIN-ANDA.com`, `DB_*`, `GOOGLE_*`, dan Midtrans.
5. **Tabel database** (pilih salah satu):
   - Ada SSH/Terminal: `php artisan migrate --force` (bila database masih kosong, jalankan juga `php artisan db:seed --force`).
   - Tanpa SSH: impor database lokal Anda lewat phpMyAdmin (Export dari lokal, Import di hosting), lalu jalankan `database/tambah-ulasan-dan-google.sql` **sekali** di tab SQL.
6. **Izin folder**: `storage` dan `bootstrap/cache` harus *writable* (permission 775).
7. **Setelah `.env` berubah**: `php artisan config:clear` (tanpa SSH: hapus file di `bootstrap/cache/` selain `.gitignore`).
8. **Midtrans**: ganti ke key Production, dan di dashboard Midtrans isi *Payment Notification URL* = `https://DOMAIN-ANDA.com/midtrans/notifikasi`.
9. **Google**: pastikan redirect URI produksi sudah didaftarkan (langkah 2) dan consent screen berstatus *In production*.

## 5. Email asli (opsional)

Bawaan `MAIL_MAILER=log` hanya menulis email ke log. Untuk mengirim email sungguhan, pakai `env-production.contoh.txt` (SMTP Gmail dengan *App Password*: Akun Google > Keamanan > Verifikasi 2 Langkah > Sandi aplikasi).
Catatan: proyek ini sengaja tidak memakai verifikasi email OTP (migrasi lama menghapusnya). Pengguna Google otomatis memakai email terverifikasi.

## 6. Masalah umum

| Pesan | Penyebab / solusi |
|---|---|
| `redirect_uri_mismatch` | URI di Google Console tidak sama persis dengan `GOOGLE_REDIRECT_URI` (cek `http` vs `https`, garis miring akhir, `www`). |
| `access_denied` / "app not verified" | Consent screen masih *Testing*: tambahkan email ke Test users, atau Publish app. |
| "Login Google belum dikonfigurasi" | `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` kosong, atau `config:clear` belum dijalankan. |
| `cURL error 60` (Windows lokal) | Sudah ditangani otomatis oleh `app/Support/CaBundle.php`. |
| 419 Page Expired setelah login | Pastikan `APP_URL` benar dan `SESSION_DOMAIN=null`; di https tambahkan `SESSION_SECURE_COOKIE=true`. |
| Halaman 500 | Set `APP_DEBUG=true` sebentar, atau lihat `storage/logs/laravel.log`. |

## 7. Keamanan

- File `.env` **tidak disertakan** di zip ini (berisi key rahasia). Pakai `.env` lokal Anda.
- Zip yang Anda unggah tadi memuat key Midtrans di `.env`. Bila pernah dibagikan ke orang lain, buat ulang key tersebut di dashboard Midtrans.
- Jangan pernah membagikan `GOOGLE_CLIENT_SECRET`.
- Ganti password akun admin bawaan seeder sebelum go-live.

## Daftar file baru / diubah

Baru: `app/Http/Controllers/Auth/GoogleController.php`, `app/Http/Controllers/User/UlasanController.php`, `app/Http/Controllers/Admin/UlasanController.php`, `app/Models/Ulasan.php`, 2 migrasi `2026_11_20_*`, `resources/views/admin/ulasan/index.blade.php`, `resources/views/user/partials/ulasan-*.blade.php`, `resources/views/partials/{bintang,google-button}.blade.php`, `database/tambah-ulasan-dan-google.sql`, `env-production.contoh.txt`.

Diubah: `routes/web.php`, `config/{services,toko}.php`, `.env.example`, `app/Models/{User,Produk,Pesanan}.php`, `app/Http/Controllers/User/{Katalog,Profil,Pesanan}Controller.php`, view login/register/profil/produk-detail/pesanan-detail/katalog/landing, menu admin (`layouts/admin`, `mobile-nav-admin`), `public/css/pro.css`, `public/sw.js`.
