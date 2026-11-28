@extends('layouts.app')
@section('title', 'Pembayaran')

@push('scripts')
    <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    <script>
        (function () {
            var tokenAwal = @json($pesanan->snap_token);
            var urlToken = @json(route('pembayaran.token', $pesanan));
            var selesai = @json(route('pembayaran.selesai', $pesanan));
            var detail = @json(route('pesanan.show', $pesanan));
            var csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var tombol = document.getElementById('btn-bayar');
            var pesanEl = document.getElementById('pay-msg');
            var sibuk = false;

            function info(teks, galat) {
                pesanEl.textContent = teks || '';
                pesanEl.style.color = galat ? '#b91c1c' : '';
                pesanEl.style.display = teks ? 'block' : 'none';
            }

            function ambilToken() {
                return fetch(urlToken, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                }).then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (d) {
                        if (res.status === 409) { window.location.href = detail; throw new Error('Pesanan sudah tidak menunggu pembayaran.'); }
                        if (!res.ok || !d.token) { throw new Error(d.pesan || 'Sesi pembayaran belum bisa dibuat.'); }
                        return d.token;
                    });
                });
            }

            function bayar() {
                if (sibuk) return;
                if (!window.snap) {
                    info('Jendela pembayaran Midtrans gagal dimuat. Periksa koneksi internet / Client Key, lalu muat ulang halaman.', true);
                    return;
                }
                sibuk = true;
                tombol.disabled = true;
                info('Menyiapkan pembayaran...', false);

                ambilToken().catch(function (err) {
                    if (tokenAwal) return tokenAwal;
                    throw err;
                }).then(function (token) {
                    info('', false);
                    window.snap.pay(token, {
                        onSuccess: function () { window.location.href = selesai; },
                        onPending: function () { window.location.href = selesai; },
                        onError: function () { info('Pembayaran gagal. Silakan coba lagi.', true); },
                        onClose: function () { info('Jendela pembayaran ditutup. Tekan "Bayar Sekarang" untuk melanjutkan.', false); }
                    });
                }).catch(function (err) {
                    info(err.message, true);
                }).then(function () {
                    sibuk = false;
                    tombol.disabled = false;
                });
            }

            tombol.addEventListener('click', bayar);

            @if (session('success'))
                window.addEventListener('load', bayar);
            @endif
        })();
    </script>
@endpush

@section('content')
    <div class="page-wrap page-sm">
        <a href="{{ route('pesanan.show', $pesanan) }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Detail Pesanan</a>
        <h2 class="page-title">Pembayaran</h2>

        <div class="card" style="margin-bottom:16px;">
            <p class="section-label"><i class="fa-solid fa-receipt"></i>Rincian Pesanan</p>
            <p style="margin-bottom:10px;"><strong>Kode:</strong> {{ $pesanan->kode_pesanan }}</p>
            @foreach ($pesanan->detailPesanans as $d)
                <div class="summary-row">
                    <span>{{ $d->produk->nama_produk }} ×{{ $d->qty }}</span>
                    <span>Rp {{ number_format($d->subtotal(), 0, ',', '.') }}</span>
                </div>
            @endforeach
            <div class="summary-row"><span>Ongkir ({{ $pesanan->jarak_km }} km)</span><span>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span></div>
            <div class="summary-total"><span>Total Bayar</span><span>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span></div>
        </div>

            <div class="card">
                <p class="section-label"><i class="fa-solid fa-shield-halved"></i>Pembayaran Otomatis</p>
                <p class="form-hint" style="margin-bottom:14px;">Pilih metode pembayaran di jendela pembayaran. Status pesanan diperbarui otomatis setelah pembayaran berhasil, tanpa perlu upload bukti.</p>
                <div class="pay-methods" style="margin-bottom:16px;">
                    <span><i class="fa-solid fa-building-columns"></i>Virtual Account</span>
                    <span><i class="fa-solid fa-qrcode"></i>QRIS</span>
                    <span><i class="fa-solid fa-wallet"></i>E-Wallet</span>
                    <span><i class="fa-solid fa-credit-card"></i>Kartu</span>
                    <span><i class="fa-solid fa-store"></i>Minimarket</span>
                </div>
                <button type="button" id="btn-bayar" class="btn btn-primary btn-lg btn-full">
                    <i class="fa-solid fa-lock"></i>Bayar Sekarang
                </button>
                <p id="pay-msg" class="form-hint" style="display:none;margin-top:10px;text-align:center;"></p>
                @unless (config('toko.midtrans.is_production'))
                    <p class="form-hint" style="margin-top:10px;text-align:center;">Mode uji coba (sandbox): pembayaran tidak memakai uang sungguhan.</p>
                @endunless
            </div>
    </div>
@endsection