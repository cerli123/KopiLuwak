@extends('layouts.app')
@section('title', 'Ajukan Refund')

@push('scripts')
    <script>
        (function() {
            var radios = document.querySelectorAll('.refund-reason input[type=radio]');
            var bukti = document.getElementById('bukti');
            var tanda = document.getElementById('bukti-wajib');

            function perbarui() {
                var wajib = false;
                radios.forEach(function(r) {
                    r.closest('.refund-reason').classList.toggle('selected', r.checked);
                    if (r.checked && r.getAttribute('data-bukti') === '1') wajib = true;
                });
                bukti.required = wajib;
                tanda.textContent = wajib ? '(wajib)' : '(opsional)';
            }

            radios.forEach(function(r) {
                r.addEventListener('change', perbarui);
            });
            perbarui();
        })();
    </script>
@endpush

@section('content')
    <div class="page-wrap page-md">
        <a href="{{ route('pesanan.show', $pesanan) }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke
            Detail Pesanan</a>
        <h2 class="page-title">Ajukan Refund</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="detail-order-grid">
            <div>
                <form action="{{ route('refund.store', $pesanan) }}" method="POST" enctype="multipart/form-data"
                    class="card">
                    @csrf
                    <p class="section-label">Pilih Alasan Refund</p>

                    <div class="refund-reasons">
                        @foreach ($daftarAlasan as $kode => $label)
                            <label class="refund-reason {{ old('alasan_kategori') === $kode ? 'selected' : '' }}">
                                <input type="radio" name="alasan_kategori" value="{{ $kode }}"
                                    data-bukti="{{ in_array($kode, $wajibBukti, true) ? '1' : '0' }}"
                                    {{ old('alasan_kategori') === $kode ? 'checked' : '' }} required>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="form-group">
                        <label for="alasan_detail">Jelaskan masalahnya</label>
                        <textarea id="alasan_detail" name="alasan_detail" rows="5" minlength="10" maxlength="1000" required
                            placeholder="Ceritakan kondisi yang Anda alami agar admin mudah memeriksa...">{{ old('alasan_detail') }}</textarea>
                        <p class="form-hint">Minimal 10 karakter, maksimal 1000 karakter.</p>
                    </div>

                    <div class="form-group">
                        <label for="bukti">Foto bukti <span id="bukti-wajib">(opsional)</span></label>
                        <input type="file" id="bukti" name="bukti" accept="image/png,image/jpeg,image/webp">
                        <p class="form-hint">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-outline">Batal</a>
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Kirim
                            Pengajuan</button>
                    </div>
                </form>
            </div>

            <div>
                <div class="card" style="margin-bottom:16px;">
                    <p class="section-label">Pesanan yang Direfund</p>
                    <p style="margin-bottom:8px;"><strong>{{ $pesanan->kode_pesanan }}</strong></p>
                    @foreach ($pesanan->detailPesanans as $d)
                        <div class="summary-row">
                            <span>{{ $d->produk->nama_produk }} ×{{ $d->qty }}</span>
                            <span>Rp {{ number_format($d->subtotal(), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="summary-row">
                        <span>Ongkir</span>
                        <span>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
                    </div>
                    <div class="order-total-row">
                        <span>Dana Dikembalikan</span>
                        <span>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="card refund-note">
                    <p class="section-label">Ketentuan</p>
                    <ul>
                        <li>Pengajuan dapat dilakukan maksimal {{ config('toko.refund.batas_hari') }} hari setelah
                            pembayaran atau pesanan selesai.</li>
                        <li>Admin akan menyetujui atau menolak dengan alasan yang bisa Anda lihat di halaman refund.</li>
                        <li>Bila disetujui, pesanan dibatalkan dan dana dikembalikan ke metode pembayaran Anda.</li>
                        <li>Setiap pesanan hanya bisa diajukan refund satu kali.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
