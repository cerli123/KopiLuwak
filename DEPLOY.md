# Panduan Hosting cPanel via GitHub (dari nol)

Persyaratan hosting: PHP 8.2+, MySQL, **Git Version Control** di cPanel, dan sebaiknya **Terminal/SSH**.
Proyek ini TIDAK memakai Vite/Tailwind di tampilan, jadi tidak perlu `npm`.

## TAHAP 1 - GitHub
1. Buat repo baru di github.com (kosong, tanpa README).
2. Ekstrak zip repo ini, lalu di folder hasil ekstrak (yang berisi `artisan`, `app`, `public`):
   ```
   git init
   git add .
   git commit -m "versi awal"
   git branch -M main
   git remote add origin https://github.com/USERNAME/NAMA-REPO.git
   git push -u origin main
   ```
3. Cek di GitHub: file `artisan` dan folder `app` harus langsung terlihat di halaman depan.
   Folder `vendor` dan file `.env` memang TIDAK ikut (disengaja).

## TAHAP 2 - cPanel
4. **PHP**: *Select PHP Version* / *MultiPHP Manager* -> pilih 8.2 atau 8.3. Aktifkan ekstensi:
   mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, fileinfo, bcmath, curl, zip, gd.
5. **Database**: *MySQL Database Wizard* -> buat database + user -> ALL PRIVILEGES. Catat nama DB, user, password.
6. **Clone**: *Git Version Control -> Create*
   - Clone URL: alamat repo GitHub
   - Repository Path: `/home/CPUSER/kopiluwak-app` (JANGAN di public_html)
   - Repo private? pakai SSH key (SSH Access -> Generate Key -> Authorize), tambahkan public key di GitHub
     (Repo -> Settings -> Deploy keys), lalu pakai URL `git@github.com:USERNAME/NAMA-REPO.git`.
7. **Domain ke folder public**
   - Cara A (disarankan): *Domains* -> buat domain/subdomain, Document Root = `/home/CPUSER/kopiluwak-app/public`
   - Cara B (domain utama): Terminal ->
     ```
     mv ~/public_html ~/public_html_lama
     ln -s ~/kopiluwak-app/public ~/public_html
     ```
8. **HTTPS**: *SSL/TLS Status* -> Run AutoSSL (wajib untuk fitur peta/lokasi).

## TAHAP 3 - Pengaturan di server (Terminal)
```
cd ~/kopiluwak-app
cp .env.production.example .env
nano .env        # isi APP_URL, DB_*, key Midtrans sandbox. Simpan: Ctrl+O, Enter, Ctrl+X
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force --seed
chmod -R 775 storage bootstrap/cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
- `--seed` hanya dipakai SEKALI (mengisi akun, produk, banner, galeri contoh). Jangan diulang di update berikutnya.
- Login awal: `admin@kopiluwak.id` / `password` -> **langsung ganti password**.
- `composer` tidak dikenali? pakai `/opt/cpanel/composer/bin/composer`.

## TAHAP 4 - Midtrans Sandbox
Dashboard Sandbox -> Settings -> Configuration -> **Payment Notification URL**:
`https://DOMAIN-ANDA/midtrans/notifikasi`

## UPDATE di kemudian hari
1. Edit kode di laptop -> `git add . && git commit -m "..." && git push`
2. cPanel -> Git Version Control -> Manage -> Pull or Deploy -> **Update from Remote** -> **Deploy HEAD Commit**
   (sebelumnya edit `.cpanel.yml`: ganti `CPUSER` dengan username cPanel, commit, push).
   Atau manual di Terminal: `git pull`, lalu `composer install --no-dev`, `php artisan migrate --force`,
   `php artisan config:clear && php artisan config:cache`.

## Masalah umum
| Gejala | Solusi |
|---|---|
| Error 500 / putih | lihat `storage/logs/laravel.log`; cek chmod 775; cek `.env` dan `APP_KEY` terisi |
| "No application encryption key" | `php artisan key:generate` lalu `php artisan config:cache` |
| Gambar tidak muncul | pastikan `php artisan migrate --seed` jalan; gambar disajikan lewat `/media/...` (tanpa storage:link) |
| Perubahan .env tidak terbaca | `php artisan config:clear && php artisan config:cache` |
| Deploy gagal "working tree not clean" | jangan ubah file langsung di server; ubah di laptop lalu push |
| Peta/lokasi tidak jalan | domain harus HTTPS |
