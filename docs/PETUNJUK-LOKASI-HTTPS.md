# Agar tombol "Lokasi Saya" bisa dipakai

Browser hanya mengizinkan akses lokasi di **https://** atau **localhost / 127.0.0.1**.
Alamat seperti `http://192.168.1.5:8000`, `http://kopiluwak.test`, atau domain tanpa SSL akan diblokir.

## A. Tes di komputer yang sama
Buka `http://localhost:8000` (bukan alamat IP / .test). Tombol Lokasi Saya langsung bisa, klik "Izinkan".

## B. Tes dari HP (atau dibagikan ke orang lain) - paling mudah: Cloudflare Tunnel
1. Jalankan website: `php artisan serve`
2. Di terminal lain: `cloudflared tunnel --url http://localhost:8000`
3. Buka alamat `https://xxxx.trycloudflare.com` yang muncul di HP. (ngrok: `ngrok http 8000` juga bisa.)

## C. Laragon dengan HTTPS
Menu Laragon > Apache > SSL > Enabled, restart Laragon, lalu buka `https://nama-folder.test`.
Ubah `APP_URL=https://nama-folder.test` di `.env`, lalu `php artisan config:clear`.
Bila browser memperingatkan sertifikat, tambahkan sertifikat Laragon ke Trust Store (menu SSL yang sama).

## D. Hosting / domain sungguhan
Aktifkan SSL gratis (Let's Encrypt) di cPanel/hosting, ubah `APP_URL=https://DOMAIN-ANDA.com`, lalu `php artisan config:clear`.

## E. Izin lokasi di browser
Klik ikon gembok di kolom alamat > Izin situs > Lokasi > Izinkan. Pastikan GPS / layanan lokasi HP atau Windows menyala.

Update ini juga membuat link & aset otomatis https bila APP_URL berawalan https:// dan percaya proxy HTTPS (tunnel / hosting).
