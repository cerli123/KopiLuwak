@extends('layouts.app')
@section('title', 'Checkout')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
@endpush

@section('content')
    <div class="page-wrap page-md">
        <h2 class="page-title">Checkout</h2>

        <div class="step-bar">
            <div class="step done">
                <div class="step-circle"><i class="fa-solid fa-check"></i></div><span>Keranjang</span>
            </div>
            <div class="step-line active"></div>
            <div class="step active">
                <div class="step-circle">2</div><span>Pengiriman</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-circle">3</div><span>Pembayaran</span>
            </div>
            <div class="step-line"></div>
            <div class="step">
                <div class="step-circle">4</div><span>Selesai</span>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pesanan.store') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="lat" id="lat" value="{{ old('lat') }}">
            <input type="hidden" name="lng" id="lng" value="{{ old('lng') }}">

            <div class="checkout-grid">
                <div>
                    <div class="card" style="margin-bottom:16px;">
                        <p class="section-label"><i class="fa-solid fa-user"></i>Data Penerima</p>
                        <div class="form-group">
                            <label>Nama Penerima <span class="req">*</span></label>
                            <input type="text" name="nama_penerima"
                                value="{{ old('nama_penerima', auth()->user()->nama) }}" required>
                        </div>
                        <div class="form-group">
                            <label>No. HP <span class="req">*</span></label>
                            <input type="text" name="no_hp_penerima"
                                value="{{ old('no_hp_penerima', auth()->user()->no_hp) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat_pengiriman">Alamat Lengkap <span class="req">*</span></label>
                            <textarea name="alamat_pengiriman" id="alamat_pengiriman" rows="3" maxlength="500"
                                placeholder="Terisi otomatis saat Anda klik / geser pin di peta" required>{{ old('alamat_pengiriman', auth()->user()->alamat) }}</textarea>
                            <p class="form-hint" id="alamat-info"><i class="fa-solid fa-circle-info"></i>Klik peta atau geser pin: nama jalan, desa, kecamatan, dan kota terisi otomatis. Boleh Anda ubah.</p>
                        </div>
                        <div class="form-group">
                            <label for="detail_alamat">Detail Tambahan <small>(opsional)</small></label>
                            <input type="text" name="detail_alamat" id="detail_alamat" maxlength="200"
                                value="{{ old('detail_alamat') }}" placeholder="No. rumah, RT/RW, warna pagar, patokan">
                            <p class="form-hint">Tidak ikut terhapus saat pin dipindah.</p>
                        </div>
                    </div>

                    <div class="card">
                        <p class="section-label"><i class="fa-solid fa-location-dot"></i>Titik Pengiriman</p>
                        <p class="form-hint" style="margin-bottom:12px;">Cari alamat, pakai lokasi Anda, atau klik peta untuk menaruh pin. Ongkir dihitung otomatis dari jarak tempuh.</p>

                        <div class="map-search">
                            <input type="text" id="map-query" placeholder="Cari alamat atau nama tempat">
                            <button type="button" class="btn btn-outline btn-sm" id="map-cari">
                                <i class="fa-solid fa-magnifying-glass"></i>Cari
                            </button>
                            <button type="button" class="btn btn-outline btn-sm" id="map-lokasi">
                                <i class="fa-solid fa-crosshairs"></i>Lokasi Saya
                            </button>
                        </div>

                        <div id="map" class="map-box"></div>

                        <p class="map-status" id="map-status"><i class="fa-solid fa-circle-info"></i>Belum ada titik yang dipilih.</p>
                    </div>
                </div>

                <div>
                    <div class="card summary-card">
                        <p class="section-label">Ringkasan Pesanan</p>
                        @foreach ($items as $item)
                            <div class="summary-row">
                                <span>{{ $item->produk->nama_produk }} ×{{ $item->qty }}</span>
                                <span>Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <hr class="summary-hr">
                        <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                        <div class="summary-row"><span>Jarak</span><span id="sum-jarak">-</span></div>
                        <div class="summary-row"><span>Ongkir</span><span id="sum-ongkir">-</span></div>
                        <div class="summary-total"><span>Total</span><span id="sum-total">Rp {{ number_format($total, 0, ',', '.') }}</span></div>

                        <div class="pay-methods">
                            <span><i class="fa-solid fa-building-columns"></i>Transfer Bank</span>
                            <span><i class="fa-solid fa-qrcode"></i>QRIS</span>
                            <span><i class="fa-solid fa-wallet"></i>E-Wallet</span>
                            <span><i class="fa-solid fa-credit-card"></i>Kartu</span>
                        </div>
                        <p class="form-hint" style="margin-top:8px;">Pilih metode pembayaran di langkah berikutnya.</p>

                        <button type="submit" id="btn-pesan" class="btn btn-primary btn-full" style="margin-top:16px;" disabled>
                            Buat Pesanan &amp; Bayar <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        (function () {
            var asal = {!! json_encode(['lat' => $asal['lat'], 'lng' => $asal['lng'], 'nama' => $asal['nama']], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
            var subtotal = {{ (int) $total }};
            var urlHitung = @json(route('ongkir.hitung'));
            var urlReverse = @json(route('alamat.reverse'));
            var urlCari = @json(route('alamat.cari'));
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var oldLat = @json(old('lat'));
            var oldLng = @json(old('lng'));

            var latInput = document.getElementById('lat');
            var lngInput = document.getElementById('lng');
            var alamatEl = document.getElementById('alamat_pengiriman');
            var alamatInfo = document.getElementById('alamat-info');
            var statusEl = document.getElementById('map-status');
            var jarakEl = document.getElementById('sum-jarak');
            var ongkirEl = document.getElementById('sum-ongkir');
            var totalEl = document.getElementById('sum-total');
            var btnPesan = document.getElementById('btn-pesan');
            var btnLokasi = document.getElementById('map-lokasi');

            var seqHitung = 0;
            var seqAlamat = 0;

            function rupiah(n) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
            }

            function postJson(url, body) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify(body)
                }).then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (data) {
                        if (!res.ok) {
                            var pesan = data.pesan || data.message || 'Terjadi kesalahan (' + res.status + ').';
                            if (res.status === 419) pesan = 'Sesi habis. Muat ulang halaman ini.';
                            if (res.status === 429) pesan = 'Terlalu banyak permintaan. Tunggu sebentar lalu coba lagi.';
                            throw new Error(pesan);
                        }
                        return data;
                    });
                });
            }

            L.Icon.Default.imagePath = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/';

            var map = L.map('map').setView([asal.lat, asal.lng], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            L.marker([asal.lat, asal.lng], { title: asal.nama }).addTo(map)
                .bindPopup('<strong>' + asal.nama + '</strong><br>Titik asal pengiriman');

            var tujuan = null;

            function tampilkanGagal(pesan) {
                statusEl.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i>' + pesan;
                statusEl.classList.add('error');
                jarakEl.textContent = '-';
                ongkirEl.textContent = '-';
                totalEl.textContent = rupiah(subtotal);
                btnPesan.disabled = true;
            }

            function infoAlamat(html, galat) {
                alamatInfo.innerHTML = html;
                alamatInfo.style.color = galat ? '#b91c1c' : '';
            }

            // Titik peta -> alamat (nama jalan, desa, kecamatan, kota, dst) -> kolom Alamat Lengkap.
            function isiAlamat(lat, lng, cadangan, catatan) {
                catatan = catatan ? ' ' + catatan : '';
                var no = ++seqAlamat;
                infoAlamat('<i class="fa-solid fa-spinner fa-spin"></i>Mengambil alamat dari titik peta...', false);

                postJson(urlReverse, { lat: lat, lng: lng })
                    .then(function (d) {
                        if (no !== seqAlamat) return;
                        alamatEl.value = String(d.alamat || '').substring(0, 500);
                        infoAlamat('<i class="fa-solid fa-circle-check"></i>Alamat terisi otomatis dari lokasi Anda. Silakan periksa dan lengkapi bila perlu.' + catatan, false);
                    })
                    .catch(function (err) {
                        if (no !== seqAlamat) return;
                        if (cadangan) {
                            alamatEl.value = String(cadangan).substring(0, 500);
                            infoAlamat('<i class="fa-solid fa-circle-check"></i>Alamat terisi dari hasil pencarian. Silakan periksa dan lengkapi bila perlu.', false);
                            return;
                        }
                        infoAlamat('<i class="fa-solid fa-triangle-exclamation"></i>' + err.message + ' Isi alamat secara manual.', true);
                    });
            }

            function hitung(lat, lng) {
                var no = ++seqHitung;
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
                statusEl.classList.remove('error');
                statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>Menghitung ongkir...';
                btnPesan.disabled = true;

                postJson(urlHitung, { lat: lat, lng: lng })
                    .then(function (d) {
                        if (no !== seqHitung) return;
                        jarakEl.textContent = d.jarak_km + ' km';
                        ongkirEl.textContent = rupiah(d.ongkir);
                        totalEl.textContent = rupiah(d.total);
                        statusEl.innerHTML = '<i class="fa-solid fa-circle-check"></i>Jarak tempuh ' + d.jarak_km + ' km'
                            + (d.sumber === 'estimasi' ? ' (estimasi)' : '') + ', ongkir ' + rupiah(d.ongkir) + '.';
                        btnPesan.disabled = false;
                    })
                    .catch(function (err) {
                        if (no !== seqHitung) return;
                        tampilkanGagal(err.message);
                    });
            }

            /**
             * opsi.geser     : pindahkan tampilan peta ke titik
             * opsi.alamat    : false = jangan timpa kolom alamat (dipakai saat memulihkan input lama)
             * opsi.cadangan  : alamat dari hasil pencarian, dipakai bila pengambilan alamat gagal
             */
            function setTujuan(lat, lng, opsi) {
                opsi = opsi || {};

                if (!tujuan) {
                    tujuan = L.marker([lat, lng], { draggable: true }).addTo(map);
                    tujuan.on('dragend', function () {
                        var p = tujuan.getLatLng();
                        setTujuan(p.lat, p.lng, {});
                    });
                } else {
                    tujuan.setLatLng([lat, lng]);
                }
                if (opsi.geser) {
                    map.setView([lat, lng], 17);
                }

                hitung(lat, lng);
                if (opsi.alamat !== false) {
                    isiAlamat(lat, lng, opsi.cadangan, opsi.catatan);
                }
            }

            map.on('click', function (e) {
                setTujuan(e.latlng.lat, e.latlng.lng, {});
            });

            function cari() {
                var q = document.getElementById('map-query').value.trim();
                if (q.length < 3) {
                    tampilkanGagal('Ketik minimal 3 huruf nama jalan / tempat.');
                    return;
                }
                statusEl.classList.remove('error');
                statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>Mencari lokasi...';

                postJson(urlCari, { q: q })
                    .then(function (d) {
                        setTujuan(parseFloat(d.lat), parseFloat(d.lng), { geser: true, cadangan: d.alamat });
                    })
                    .catch(function (err) {
                        tampilkanGagal(err.message);
                    });
            }

            document.getElementById('map-cari').addEventListener('click', cari);
            document.getElementById('map-query').addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    cari();
                }
            });

            // "Lokasi Saya": browser meminta izin -> lokasi dicari otomatis -> pin, ongkir, dan Alamat Lengkap terisi sendiri (tanpa tombol Cari).
            function lokasiGagal(err) {
                btnLokasi.disabled = false;
                tampilkanGagal({
                    1: 'Izin lokasi ditolak. Klik ikon gembok di kolom alamat browser &rsaquo; Izin situs &rsaquo; Lokasi &rsaquo; <strong>Izinkan</strong>, lalu tekan "Lokasi Saya" lagi.',
                    2: 'Lokasi perangkat tidak tersedia. Nyalakan GPS / layanan lokasi dan internet, lalu tekan "Lokasi Saya" lagi.',
                    3: 'Waktu habis saat mencari lokasi. Coba lagi di tempat dengan sinyal lebih baik, atau klik peta.'
                }[err.code] || 'Gagal mengambil lokasi.');
            }

            function lokasiBerhasil(pos) {
                btnLokasi.disabled = false;
                var akurasi = Math.round(pos.coords.accuracy || 0);
                var catatan = akurasi > 300
                    ? '(Akurasi lokasi ± ' + akurasi + ' m. Geser pin ke rumah Anda bila kurang tepat.)'
                    : '';
                setTujuan(pos.coords.latitude, pos.coords.longitude, { geser: true, catatan: catatan });
            }

            btnLokasi.addEventListener('click', function () {
                if (!window.isSecureContext) {
                    var lokal = 'http://localhost' + (location.port ? ':' + location.port : '') + location.pathname;
                    tampilkanGagal('Browser memblokir lokasi karena website dibuka lewat <strong>' + location.host + '</strong> tanpa HTTPS. '
                        + 'Buka lewat <a href="' + lokal + '">' + lokal + '</a> (di komputer yang sama) atau pakai https://. '
                        + 'Sementara itu, pilih titik lewat pencarian atau klik peta.');
                    return;
                }
                if (!navigator.geolocation) {
                    tampilkanGagal('Browser tidak mendukung deteksi lokasi. Pilih titik lewat pencarian atau klik peta.');
                    return;
                }

                btnLokasi.disabled = true;
                statusEl.classList.remove('error');
                statusEl.innerHTML = '<i class="fa-solid fa-location-crosshairs fa-beat"></i>Aktifkan lokasi: pilih <strong>"Izinkan"</strong> pada jendela yang muncul di browser...';

                // Coba GPS / akurasi tinggi dulu; bila gagal (bukan karena ditolak), coba lokasi jaringan.
                navigator.geolocation.getCurrentPosition(lokasiBerhasil, function (err) {
                    if (err.code === 1) {
                        lokasiGagal(err);
                        return;
                    }
                    statusEl.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>Mencari lokasi lewat jaringan...';
                    navigator.geolocation.getCurrentPosition(lokasiBerhasil, lokasiGagal,
                        { enableHighAccuracy: false, timeout: 15000, maximumAge: 60000 });
                }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 });
            });

            // Form dikirim ulang karena validasi gagal: pulihkan pin, jangan timpa alamat yang sudah diketik.
            if (oldLat && oldLng) {
                setTujuan(parseFloat(oldLat), parseFloat(oldLng), { geser: true, alamat: false });
            }

            // Cegah kirim tanpa titik di peta.
            document.getElementById('checkout-form').addEventListener('submit', function (e) {
                if (!latInput.value || !lngInput.value) {
                    e.preventDefault();
                    tampilkanGagal('Tentukan lokasi pengiriman pada peta terlebih dahulu.');
                }
            });
        })();
    </script>
@endpush
