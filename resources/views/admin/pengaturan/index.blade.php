@extends('layouts.admin')
@section('title', 'Pengaturan Toko')
@section('page-title', 'Pengaturan Toko')
@section('content')
    <div class="card form-card" style="margin-bottom:20px;">
        <p class="card-title"><i class="fa-solid fa-store"></i> Status Toko</p>
        <p style="margin-bottom:14px;">
            Saat ini toko:
            <span class="badge badge-{{ $tokoBuka ? 'success' : 'danger' }}">{{ $tokoBuka ? 'BUKA' : 'TUTUP' }}</span>
        </p>

        <form action="{{ route('admin.pengaturan.toko') }}" method="POST">
            @csrf
            @php $statusPilih = old('status', $tokoBuka ? 'buka' : 'tutup'); @endphp
            <div class="toko-opsi-grid">
                <label class="toko-opsi">
                    <input type="radio" name="status" value="buka" {{ $statusPilih === 'buka' ? 'checked' : '' }}>
                    <span><i class="fa-solid fa-door-open"></i> Buka Toko</span>
                </label>
                <label class="toko-opsi toko-opsi-tutup">
                    <input type="radio" name="status" value="tutup" {{ $statusPilih === 'tutup' ? 'checked' : '' }}>
                    <span><i class="fa-solid fa-door-closed"></i> Tutup Toko</span>
                </label>
            </div>

            <div class="form-group">
                <label for="pesan">Pesan untuk pelanggan (saat tutup)</label>
                <textarea id="pesan" name="pesan" rows="3" maxlength="300"
                    placeholder="Contoh: Toko libur Lebaran, pesanan diproses kembali setelah libur.">{{ old('pesan', $pesanToko) }}</textarea>
            </div>
            <div class="form-group">
                <label for="buka_kembali">Buka kembali (opsional)</label>
                <input type="text" id="buka_kembali" name="buka_kembali" maxlength="100"
                    value="{{ old('buka_kembali', $bukaLagi) }}" placeholder="Contoh: Senin, 10 Nov 2026 pukul 08.00 WIB">
                <p class="form-hint">Saat toko tutup, pelanggan tidak bisa membuka katalog, keranjang, checkout, dan pembayaran.
                    Riwayat pesanan, refund, dan profil tetap bisa dilihat. Pesanan yang sudah dibayar tetap bisa Anda proses.</p>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Status Toko</button>
            </div>
        </form>
    </div>

    <div class="card form-card" style="margin-bottom:20px;">
        <p class="card-title"><i class="fa-solid fa-truck-fast"></i> Ongkos Kirim</p>
        <p style="margin-bottom:14px;">
            Tarif saat ini:
            <strong>Rp {{ number_format($ongkir['per_km'], 0, ',', '.') }} / km</strong>
            <span class="badge badge-{{ $ongkir['kustom'] ? 'success' : 'secondary' }}">{{ $ongkir['kustom'] ? 'Diatur admin' : 'Bawaan' }}</span>
        </p>

        <form action="{{ route('admin.pengaturan.ongkir') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="tarif_per_km">Biaya ongkir per kilometer</label>
                <div class="input-prefix">
                    <span>Rp</span>
                    <input type="number" id="tarif_per_km" name="tarif_per_km" min="0" max="1000000" step="1"
                        value="{{ old('tarif_per_km', $ongkir['kustom'] ? $ongkir['per_km'] : '') }}"
                        placeholder="Bawaan: {{ $ongkir['bawaan'] }}">
                </div>
                <p class="form-hint">
                    Rumus: tarif dasar Rp {{ number_format($ongkir['dasar'], 0, ',', '.') }} untuk
                    {{ rtrim(rtrim(number_format($ongkir['km_dasar'], 1, ',', '.'), '0'), ',') }} km pertama,
                    lalu setiap km berikutnya dikali tarif di atas, dibulatkan ke atas per
                    Rp {{ number_format($ongkir['bulat'], 0, ',', '.') }}.
                    Kosongkan kolom lalu simpan untuk kembali ke tarif bawaan.
                    Berlaku untuk pesanan baru; pesanan yang sudah dibuat tidak berubah.
                </p>
            </div>

            <div class="ongkir-contoh">
                <table>
                    <thead><tr><th>Jarak</th><th>Estimasi ongkir</th></tr></thead>
                    <tbody>
                        @foreach ([3, 5, 10, 25] as $km)
                            @php
                                $biaya = $ongkir['dasar'] + max(0, $km - $ongkir['km_dasar']) * $ongkir['per_km'];
                                $biaya = (int) (ceil($biaya / $ongkir['bulat']) * $ongkir['bulat']);
                            @endphp
                            <tr><td>{{ $km }} km</td><td>Rp {{ number_format($biaya, 0, ',', '.') }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Tarif Ongkir</button>
            </div>
        </form>
    </div>

    <div class="card form-card" style="margin-bottom:20px;">
        <p class="card-title"><i class="fa-solid fa-location-dot"></i> Lokasi Produksi / Asal Pengiriman</p>
        <p style="margin-bottom:14px;">
            Titik asal saat ini:
            <strong>{{ $asal['nama'] }}</strong>
            <span class="badge badge-{{ $asal['kustom'] ? 'success' : 'secondary' }}">{{ $asal['kustom'] ? 'Diatur admin' : 'Bawaan' }}</span>
        </p>

        <form action="{{ route('admin.pengaturan.asal') }}" method="POST" id="form-asal">
            @csrf
            <div class="form-group">
                <label for="asal_nama">Nama lokasi produksi <span class="req">*</span></label>
                <input type="text" id="asal_nama" name="asal_nama" maxlength="100" required
                    value="{{ old('asal_nama', $asal['nama']) }}" placeholder="Contoh: Sentra Produksi Kopiluwak Lereng Muria">
            </div>
            <div class="form-group">
                <label for="asal_alamat">Alamat lokasi (opsional)</label>
                <input type="text" id="asal_alamat" name="asal_alamat" maxlength="250"
                    value="{{ old('asal_alamat', $asal['alamat']) }}" placeholder="Terisi otomatis saat Anda memilih titik di peta">
            </div>

            <div class="map-search">
                <input type="text" id="asal-query" placeholder="Cari alamat atau nama tempat">
                <button type="button" class="btn btn-outline btn-sm" id="asal-cari">
                    <i class="fa-solid fa-magnifying-glass"></i>Cari
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="asal-lokasi">
                    <i class="fa-solid fa-crosshairs"></i>Aktifkan Lokasi Saya
                </button>
            </div>

            <div id="asal-map" class="map-box" style="height:340px;"></div>

            <input type="hidden" name="lat" id="asal-lat" value="{{ old('lat', $asal['kustom'] ? $asal['lat'] : '') }}">
            <input type="hidden" name="lng" id="asal-lng" value="{{ old('lng', $asal['kustom'] ? $asal['lng'] : '') }}">

            <p class="map-status" id="asal-status"><i class="fa-solid fa-circle-info"></i>
                @if ($asal['kustom'])
                    Titik tersimpan: {{ number_format($asal['lat'], 6, '.', '') }}, {{ number_format($asal['lng'], 6, '.', '') }}
                @else
                    Belum ada titik yang diatur admin. Pakai titik bawaan sampai Anda menyimpan lokasi baru.
                @endif
            </p>
            <p class="form-hint">
                Tekan <strong>Aktifkan Lokasi Saya</strong> saat Anda berada di lokasi produksi (izinkan akses lokasi di browser),
                atau cari alamat / klik peta / geser pin. Titik ini dipakai sebagai asal perhitungan ongkir dan tampil di peta checkout pelanggan.
                Berlaku untuk pesanan baru; pesanan yang sudah dibuat tidak berubah.
                Akses lokasi browser hanya berfungsi di alamat <code>https://</code> (atau <code>localhost</code>).
            </p>

            <div class="form-actions" style="margin-top:16px;">
                <button type="submit" class="btn btn-primary" id="asal-simpan"><i class="fa-solid fa-floppy-disk"></i>Simpan Lokasi Produksi</button>
            </div>
        </form>

        @if ($asal['kustom'])
            <form action="{{ route('admin.pengaturan.asal.reset') }}" method="POST" style="margin-top:12px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm" onclick="return confirm('Kembalikan lokasi produksi ke titik bawaan?')">
                    <i class="fa-solid fa-rotate-left"></i>Kembalikan ke Titik Bawaan
                </button>
            </form>
        @endif
    </div>

    <div class="card form-card" style="margin-bottom:20px;">
        <p class="card-title"><i class="fa-solid fa-credit-card"></i> Pembayaran (Midtrans)</p>
        <p style="margin-bottom:14px;">
            Mode saat ini:
            <span class="badge badge-{{ ['sandbox' => 'info', 'live' => 'success'][$bayar['mode']] }}">{{ $bayar['label'] }}</span>
        </p>

        <div class="pay-status-grid">
            <div class="pay-status {{ $bayar['server_key'] ? 'ok' : 'no' }}">
                <i class="fa-solid {{ $bayar['server_key'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                <span>Server Key {{ $bayar['server_key'] ? 'terisi' : 'belum diisi' }}</span>
            </div>
            <div class="pay-status {{ $bayar['client_key'] ? 'ok' : 'no' }}">
                <i class="fa-solid {{ $bayar['client_key'] ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                <span>Client Key {{ $bayar['client_key'] ? 'terisi' : 'belum diisi' }}</span>
            </div>
        </div>

        @foreach ($bayar['peringatan'] as $w)
            <div class="alert alert-info" style="margin:12px 0 0;"><i class="fa-solid fa-triangle-exclamation"></i>{{ $w }}</div>
        @endforeach

        <div class="form-group" style="margin-top:16px;">
            <label>URL Notifikasi (webhook) untuk dashboard Midtrans</label>
            <input type="text" value="{{ $bayar['webhook_url'] }}" readonly onclick="this.select()">
            <p class="form-hint">Salin ke Midtrans &rsaquo; Settings &rsaquo; Configuration &rsaquo; Payment Notification URL.
                Pelanggan yang sudah membayar akan otomatis berstatus "Diproses" lewat URL ini.</p>
        </div>

        <details class="pay-howto" {{ $bayar['mode'] === 'sandbox' ? 'open' : '' }}>
            <summary>Cara mengaktifkan Midtrans Sandbox (uji coba tanpa uang)</summary>
            <ol>
                <li>Masuk ke <strong>dashboard.sandbox.midtrans.com</strong> (daftar gratis), pilih menu <em>Settings &rsaquo; Access Keys</em>, salin <em>Server Key</em> (<code>SB-Mid-server-...</code>) dan <em>Client Key</em> (<code>SB-Mid-client-...</code>).</li>
                <li>Buka file <code>.env</code>, isi <code>MIDTRANS_SERVER_KEY</code> dan <code>MIDTRANS_CLIENT_KEY</code>, lalu set <code>MIDTRANS_IS_PRODUCTION=false</code> lalu simpan.</li>
                <li>Jalankan <code>php artisan config:clear</code>, lalu tekan tombol <em>Tes Koneksi Midtrans</em> di bawah. Hasilnya harus &quot;Terhubung ke Midtrans (Sandbox)&quot;.</li>
                <li>Uji pembayaran dengan kartu <code>4811 1111 1111 1114</code> (exp bebas di masa depan, CVV <code>123</code>, OTP <code>112233</code>), atau simulator di <strong>simulator.sandbox.midtrans.com</strong> untuk Virtual Account / QRIS / GoPay.</li>
                <li>Status pesanan diperbarui saat pelanggan kembali ke website. Agar otomatis lewat webhook, pasang URL notifikasi di atas pada Midtrans Sandbox &rsaquo; Settings &rsaquo; Configuration &rsaquo; Payment Notification URL (di komputer lokal gunakan ngrok / Cloudflare Tunnel karena localhost tidak bisa dijangkau Midtrans).</li>
            </ol>
        </details>

        <details class="pay-howto" style="margin-top:10px;">
            <summary>Cara mengaktifkan pembayaran sungguhan (Production)</summary>
            <ol>
                <li>Daftar &amp; lengkapi verifikasi bisnis di <strong>dashboard.midtrans.com</strong>, lalu ambil <em>Server Key</em> dan <em>Client Key</em> (menu Settings &rsaquo; Access Keys) untuk mode <strong>Production</strong>.</li>
                <li>Buka file <code>.env</code>, isi <code>MIDTRANS_SERVER_KEY</code>, <code>MIDTRANS_CLIENT_KEY</code>, lalu set <code>MIDTRANS_IS_PRODUCTION=true</code>.</li>
                <li>Pastikan <code>APP_URL</code> berisi alamat <code>https://</code> website Anda, <code>APP_ENV=production</code>, dan <code>APP_DEBUG=false</code>.</li>
                <li>Jalankan <code>php artisan config:clear</code>, lalu tekan tombol <em>Tes Koneksi</em> di bawah.</li>
                <li>Pasang URL notifikasi di atas pada dashboard Midtrans, lakukan satu transaksi nyata bernilai kecil untuk memastikan.</li>
            </ol>
        </details>

        <form action="{{ route('admin.pengaturan.pembayaran.tes') }}" method="POST" style="margin-top:16px;">
            @csrf
            <button type="submit" class="btn btn-outline"><i class="fa-solid fa-plug-circle-check"></i>Tes Koneksi Midtrans</button>
        </form>
    </div>

    <div class="card form-card" style="margin-bottom:20px;">
        <p class="card-title"><i class="fa-solid fa-address-book"></i> Informasi Kontak</p>
        <form action="{{ route('admin.pengaturan.kontak') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="kontak_wa">Nomor WhatsApp</label>
                    <input type="text" id="kontak_wa" name="kontak_wa" maxlength="20" value="{{ old('kontak_wa', $kontak['wa']) }}" placeholder="Contoh: 081234567890">
                </div>
                <div class="form-group">
                    <label for="kontak_email">Email</label>
                    <input type="email" id="kontak_email" name="kontak_email" maxlength="120" value="{{ old('kontak_email', $kontak['email']) }}" placeholder="halo@tokoanda.com">
                </div>
            </div>
            <div class="form-group">
                <label for="kontak_alamat">Alamat</label>
                <input type="text" id="kontak_alamat" name="kontak_alamat" maxlength="250" value="{{ old('kontak_alamat', $kontak['alamat']) }}" placeholder="Alamat sentra produksi / toko">
            </div>
            <div class="form-group">
                <label for="kontak_jam">Jam layanan</label>
                <input type="text" id="kontak_jam" name="kontak_jam" maxlength="120" value="{{ old('kontak_jam', $kontak['jam']) }}" placeholder="Contoh: Setiap hari, 08.00 - 17.00 WIB">
                <p class="form-hint">Tampil di footer website. Bila nomor WhatsApp diisi, tombol chat WhatsApp melayang muncul di halaman pelanggan.</p>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Kontak</button>
            </div>
        </form>
    </div>

    <div class="card form-card">
        <p class="card-title"><i class="fa-solid fa-image"></i> Logo Saat Ini</p>
        <div class="logo-preview-row">
            <div class="logo-preview dark"><img src="{{ $logoUrl }}" alt="Logo"></div>
            <div class="logo-preview light"><img src="{{ $logoUrl }}" alt="Logo"></div>
        </div>
        <p class="form-hint" style="margin-bottom:20px;">
            {{ $logoKustom ? 'Memakai logo yang Anda upload.' : 'Memakai logo bawaan.' }}
            Logo tampil di navbar, footer, halaman login/register, sidebar admin, dan tab browser.
        </p>

        <form action="{{ route('admin.pengaturan.logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="logo">Upload Logo Baru <span class="req">*</span></label>
                <input type="file" id="logo" name="logo" accept="image/*" data-preview="#logo-preview" required>
                <p class="form-hint">PNG transparan disarankan, rasio persegi (min. 256 x 256 px). JPG/PNG/WebP, maks 2MB.</p>
                <img id="logo-preview" class="logo-new-preview" alt="Pratinjau logo baru" style="display:none;">
            </div>
            <div class="form-actions" style="justify-content:space-between;">
                <span></span>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i>Simpan Logo</button>
            </div>
        </form>

        @if ($logoKustom)
            <form action="{{ route('admin.pengaturan.logo.reset') }}" method="POST" style="margin-top:16px;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm"
                    onclick="return confirm('Kembalikan ke logo bawaan?')">
                    <i class="fa-solid fa-rotate-left"></i>Kembalikan ke Logo Bawaan
                </button>
            </form>
        @endif
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        (function () {
            var awal = {!! json_encode(['lat' => $asal['lat'], 'lng' => $asal['lng'], 'nama' => $asal['nama'], 'kustom' => $asal['kustom']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};

            var latInput = document.getElementById('asal-lat');
            var lngInput = document.getElementById('asal-lng');
            var alamatInput = document.getElementById('asal_alamat');
            var statusEl = document.getElementById('asal-status');
            var btnLokasi = document.getElementById('asal-lokasi');
            var btnSimpan = document.getElementById('asal-simpan');

            L.Icon.Default.imagePath = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/';

            var map = L.map('asal-map').setView([awal.lat, awal.lng], awal.kustom ? 16 : 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var marker = null;

            function info(html, galat) {
                statusEl.innerHTML = html;
                statusEl.classList.toggle('error', !!galat);
            }

            function isiAlamat(lat, lng) {
                fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&accept-language=id&lat=' + lat + '&lon=' + lng, {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (d) {
                        if (d && d.display_name && alamatInput) {
                            alamatInput.value = d.display_name.substring(0, 250);
                        }
                    })
                    .catch(function () {  });
            }

            function setTitik(lat, lng, opsi) {
                opsi = opsi || {};
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);

                if (!marker) {
                    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                    marker.on('dragend', function () {
                        var p = marker.getLatLng();
                        setTitik(p.lat, p.lng, { alamat: true });
                    });
                } else {
                    marker.setLatLng([lat, lng]);
                }

                if (opsi.geser) {
                    map.setView([lat, lng], 17);
                }
                if (opsi.alamat) {
                    isiAlamat(lat, lng);
                }

                info('<i class="fa-solid fa-circle-check"></i>Titik dipilih: ' + lat.toFixed(6) + ', ' + lng.toFixed(6)
                    + (opsi.akurasi ? ' (akurasi ± ' + Math.round(opsi.akurasi) + ' m)' : '')
                    + '. Tekan "Simpan Lokasi Produksi" untuk menyimpan.', false);
            }

            if (latInput.value && lngInput.value) {
                marker = null;
                setTitik(parseFloat(latInput.value), parseFloat(lngInput.value), {});
                info('<i class="fa-solid fa-circle-info"></i>Titik: ' + parseFloat(latInput.value).toFixed(6) + ', ' + parseFloat(lngInput.value).toFixed(6), false);
            }

            map.on('click', function (e) {
                setTitik(e.latlng.lat, e.latlng.lng, { alamat: true });
            });

            btnLokasi.addEventListener('click', function () {
                if (!window.isSecureContext) {
                    var lokal = 'http://localhost' + (location.port ? ':' + location.port : '') + location.pathname;
                    info('<i class="fa-solid fa-triangle-exclamation"></i>Browser memblokir lokasi karena website dibuka lewat <strong>' + location.host + '</strong> tanpa HTTPS. Buka lewat <a href="' + lokal + '">' + lokal + '</a> (di komputer yang sama) atau pakai https://. Sementara itu, pilih titik lewat pencarian / klik peta.', true);
                    return;
                }
                if (!navigator.geolocation) {
                    info('<i class="fa-solid fa-triangle-exclamation"></i>Browser ini tidak mendukung akses lokasi.', true);
                    return;
                }

                btnLokasi.disabled = true;
                info('<i class="fa-solid fa-spinner fa-spin"></i>Meminta izin lokasi... pilih "Izinkan" pada browser.', false);

                navigator.geolocation.getCurrentPosition(function (pos) {
                    btnLokasi.disabled = false;
                    setTitik(pos.coords.latitude, pos.coords.longitude, {
                        geser: true, alamat: true, akurasi: pos.coords.accuracy
                    });
                }, function (err) {
                    btnLokasi.disabled = false;
                    var pesan = {
                        1: 'Izin lokasi ditolak. Aktifkan izin lokasi untuk website ini di pengaturan browser, lalu coba lagi.',
                        2: 'Lokasi perangkat tidak tersedia. Nyalakan GPS / koneksi internet, atau pilih titik lewat peta.',
                        3: 'Waktu habis saat mengambil lokasi. Coba lagi.'
                    }[err.code] || 'Gagal mengambil lokasi.';
                    info('<i class="fa-solid fa-triangle-exclamation"></i>' + pesan, true);
                }, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
            });

            document.getElementById('asal-cari').addEventListener('click', cari);
            document.getElementById('asal-query').addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); cari(); }
            });

            function cari() {
                var q = document.getElementById('asal-query').value.trim();
                if (!q) return;
                info('<i class="fa-solid fa-spinner fa-spin"></i>Mencari lokasi...', false);
                fetch('https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=id&q=' + encodeURIComponent(q), {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function (res) { return res.json(); })
                    .then(function (list) {
                        if (!list.length) {
                            info('<i class="fa-solid fa-triangle-exclamation"></i>Lokasi tidak ditemukan. Coba kata kunci lain atau klik peta.', true);
                            return;
                        }
                        setTitik(parseFloat(list[0].lat), parseFloat(list[0].lon), { geser: true, alamat: true });
                    })
                    .catch(function () {
                        info('<i class="fa-solid fa-triangle-exclamation"></i>Pencarian gagal. Klik langsung pada peta untuk memilih titik.', true);
                    });
            }

            document.getElementById('form-asal').addEventListener('submit', function (e) {
                if (!latInput.value || !lngInput.value) {
                    e.preventDefault();
                    info('<i class="fa-solid fa-triangle-exclamation"></i>Tentukan titik lokasi dulu (Aktifkan Lokasi Saya, cari alamat, atau klik peta).', true);
                }
            });
        })();
    </script>
@endpush
