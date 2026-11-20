@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
    <div class="page-wrap page-md">
        <a href="{{ route('pesanan.index') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat</a>

        @if (session('struk_baru') && $pesanan->pembayaran?->status_bayar === 'dikonfirmasi')
            <div class="card" style="margin-bottom:16px;background:#f0fdf4;border:1.5px solid #22c55e;">
                <p style="font-weight:700;margin-bottom:6px;"><i class="fa-solid fa-circle-check"></i> Pembayaran berhasil, terima kasih!</p>
                <p style="font-size:13px;margin-bottom:12px;">Struk pembayaran Anda sudah siap. Cetak sekarang atau simpan sebagai PDF.</p>
                <a href="{{ route('struk.show', ['pesanan' => $pesanan, 'cetak' => 1]) }}" target="_blank" rel="noopener" class="btn btn-primary"><i class="fa-solid fa-print"></i> Cetak Struk</a>
                <a href="{{ route('struk.pdf', $pesanan) }}" class="btn btn-outline"><i class="fa-solid fa-file-pdf"></i> Unduh PDF</a>
            </div>
        @endif

        <div class="detail-order-grid">
            <div>
                <div class="card" style="margin-bottom:16px;">
                    <p class="section-label">Info Pesanan</p>
                    <table class="info-tbl">
                        <tr>
                            <td>Kode Pesanan</td>
                            <td><strong>{{ $pesanan->kode_pesanan }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>{{ $pesanan->created_at->format('d M Y, H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span class="badge badge-{{ $pesanan->status_color }}">{{ $pesanan->status_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Penerima</td>
                            <td>{{ $pesanan->nama_penerima }}</td>
                        </tr>
                        <tr>
                            <td>No. HP</td>
                            <td>{{ $pesanan->no_hp_penerima }}</td>
                        </tr>
                        <tr>
                            <td>Alamat</td>
                            <td>{{ $pesanan->alamat_pengiriman }}</td>
                        </tr>
                        <tr>
                            <td>Jarak Kirim</td>
                            <td>{{ $pesanan->jarak_km }} km</td>
                        </tr>
                    </table>
                </div>

                <div class="card" style="margin-bottom:16px;">
                    <p class="section-label">Item Pesanan</p>
                    @foreach ($pesanan->detailPesanans as $d)
                        <div class="order-item-row">
                            <div class="order-item-info">
                                <p class="item-produk-nama">{{ $d->produk->nama_produk }}</p>
                                <p class="item-produk-harga">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }} ×
                                    {{ $d->qty }}</p>
                            </div>
                            <span class="item-produk-sub">Rp {{ number_format($d->subtotal(), 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="summary-row" style="margin-top:8px;">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Ongkir ({{ $pesanan->jarak_km }} km)</span>
                        <span>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</span>
                    </div>
                    <div class="order-total-row">
                        <span>Total Bayar</span>
                        <span>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if ($pesanan->bisaDiulas())
                    @php $ulasanMap = $pesanan->ulasans->keyBy('produk_id'); @endphp
                    <div class="card" style="margin-bottom:16px;" id="ulasan">
                        <p class="section-label">Ulasan Produk</p>
                        <p class="form-hint" style="margin-bottom:12px;">Bagikan pengalaman Anda agar membantu pelanggan lain.</p>
                        @foreach ($pesanan->detailPesanans->unique('produk_id') as $d)
                            @php $ul = $ulasanMap->get($d->produk_id); @endphp
                            <div class="ulasan-pesanan-item">
                                <p class="item-produk-nama">
                                    <a href="{{ route('produk.show', $d->produk) }}">{{ $d->produk->nama_produk }}</a>
                                </p>
                                @if ($ul)
                                    @include('user.partials.ulasan-item', ['u' => $ul, 'bolehUbah' => true])
                                @else
                                    @include('user.partials.ulasan-form', [
                                        'action' => route('ulasan.store', [$pesanan, $d->produk]),
                                        'method' => 'POST',
                                        'key'    => 'ulasan_'.$pesanan->id.'_'.$d->produk_id,
                                    ])
                                @endif
                            </div>
                        @endforeach
                    </div>
                @elseif (in_array($pesanan->status, ['diproses', 'dikirim'], true))
                    <p class="form-hint" style="margin-bottom:16px;"><i class="fa-regular fa-star"></i> Anda bisa memberi ulasan produk setelah pesanan berstatus selesai.</p>
                @endif

                @if ($pesanan->pembayaran)
                    <div class="card">
                        <p class="section-label">Status Pembayaran</p>
                        <table class="info-tbl">
                            <tr>
                                <td>Metode</td>
                                <td>{{ $pesanan->pembayaran->metode_label }}</td>
                            </tr>
                            <tr>
                                <td>Jumlah</td>
                                <td>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>{{ $pesanan->pembayaran->status_label }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Bayar</td>
                                <td>{{ $pesanan->pembayaran->tanggal_bayar?->format('d M Y, H:i') }}</td>
                            </tr>
                        </table>
                        @if ($pesanan->pembayaran->bukti_transfer)
                            <div style="margin-top:12px;">
                                <p style="font-size:13px;color:#6b7280;margin-bottom:6px;">Bukti Transfer:</p>
                                <img src="{{ \App\Support\Media::url($pesanan->pembayaran->bukti_transfer) }}"
                                    style="max-width:280px;border-radius:8px;border:1px solid #e5e7eb;">
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                @if ($pesanan->status === 'menunggu_bayar')
                    <div class="card" style="background:#fffbeb;border:1.5px solid #fbbf24;">
                        <p style="font-weight:700;margin-bottom:8px;"> Segera Lakukan Pembayaran</p>
                        <p style="font-size:13px;margin-bottom:16px;">Selesaikan pembayaran agar pesanan Anda segera
                            diproses. Status diperbarui otomatis setelah pembayaran berhasil.</p>
                        <a href="{{ route('pembayaran.form', $pesanan) }}" class="btn btn-primary btn-full">
                            <i class="fa-solid fa-lock"></i>Bayar Sekarang</a>
                    </div>
                @elseif($pesanan->status === 'menunggu_konfirmasi')
                    <div class="card" style="background:#eff6ff;border:1.5px solid #3b82f6;">
                        <p style="font-weight:700;margin-bottom:8px;"> Menunggu Konfirmasi</p>
                        <p style="font-size:13px;">Bukti pembayaran Anda sedang diverifikasi oleh admin. Proses ini biasanya
                            1×24 jam.</p>
                    </div>
                @elseif($pesanan->status === 'diproses')
                    <div class="card" style="background:#f0fdf4;border:1.5px solid #22c55e;">
                        <p style="font-weight:700;margin-bottom:8px;"> Pesanan Diproses</p>
                        <p style="font-size:13px;">Pesanan Anda sedang dikemas dan akan segera dikirim.</p>
                    </div>
                @elseif($pesanan->status === 'dikirim')
                    <div class="card" style="background:#f0fdf4;border:1.5px solid #22c55e;">
                        <p style="font-weight:700;margin-bottom:8px;"> Pesanan Dikirim</p>
                        <p style="font-size:13px;">Pesanan Anda sedang dalam perjalanan.</p>
                    </div>
                @endif

                @if ($pesanan->pembayaran?->status_bayar === 'dikonfirmasi')
                    <div class="card" style="margin-top:16px;">
                        <p class="section-label">Struk Pembayaran</p>
                        <p class="form-hint" style="margin-bottom:12px;">Bukti pembayaran pesanan ini. Bisa dicetak atau diunduh sebagai PDF.</p>
                        <a href="{{ route('struk.show', $pesanan) }}" target="_blank" rel="noopener" class="btn btn-primary btn-full"><i class="fa-solid fa-print"></i> Lihat &amp; Cetak Struk</a>
                        <a href="{{ route('struk.pdf', $pesanan) }}" class="btn btn-outline btn-full" style="margin-top:8px;"><i class="fa-solid fa-file-pdf"></i> Unduh PDF</a>
                    </div>
                @endif

                @if ($pesanan->refund)
                    <div class="card" style="margin-top:16px;">
                        <p class="section-label">Refund</p>
                        <p><span class="badge badge-{{ $pesanan->refund->status_color }}">{{ $pesanan->refund->status_label }}</span></p>
                        <p class="form-hint" style="margin:8px 0 12px;">{{ $pesanan->refund->kode_refund }} · {{ $pesanan->refund->alasan_label }}</p>
                        <a href="{{ route('refund.show', $pesanan->refund) }}" class="btn btn-outline btn-full">Lihat Detail Refund</a>
                    </div>
                @elseif ($pesanan->bisaRefund())
                    <div class="card" style="margin-top:16px;">
                        <p class="section-label">Ada Masalah dengan Pesanan?</p>
                        <p class="form-hint" style="margin-bottom:12px;">Ajukan refund maksimal {{ config('toko.refund.batas_hari') }} hari setelah pembayaran atau pesanan selesai. Admin akan meninjau pengajuan Anda.</p>
                        <a href="{{ route('refund.create', $pesanan) }}" class="btn btn-outline btn-full"><i class="fa-solid fa-rotate-left"></i> Ajukan Refund</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
