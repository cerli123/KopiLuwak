@extends('layouts.admin')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan')
@section('content')
    <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>

    <div class="detail-admin-grid">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <p class="section-label">Informasi Pesanan</p>
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
                        <td><span class="badge badge-{{ $pesanan->status_color }}">{{ $pesanan->status_label }}</span></td>
                    </tr>
                    <tr>
                        <td>Pelanggan</td>
                        <td>{{ $pesanan->user->nama }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>{{ $pesanan->user->email }}</td>
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
                    @if ($pesanan->lat && $pesanan->lng)
                        <tr>
                            <td>Titik Peta</td>
                            <td>
                                <a href="https://www.google.com/maps?q={{ $pesanan->lat }},{{ $pesanan->lng }}" target="_blank"
                                    rel="noopener" class="btn btn-outline btn-xs">
                                    <i class="fa-solid fa-location-dot"></i>Buka di Peta
                                </a>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

            <div class="card" style="margin-bottom:16px;">
                <p class="section-label">Item Pesanan</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesanan->detailPesanans as $d)
                            <tr>
                                <td>{{ $d->produk->nama_produk }}</td>
                                <td>Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                <td>{{ $d->qty }}</td>
                                <td>Rp {{ number_format($d->subtotal(), 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Subtotal</td>
                            <td>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3">Ongkir</td>
                            <td>Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="fw-700">Total Bayar</td>
                            <td class="fw-700">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if ($pesanan->pembayaran)
                <div class="card">
                    <p class="section-label">Pembayaran</p>
                    <table class="info-tbl">
                        <tr>
                            <td>Metode</td>
                            <td>{{ $pesanan->pembayaran->metode_label }}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Bayar</td>
                            <td>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span
                                    class="badge badge-{{ $pesanan->pembayaran->status_bayar === 'dikonfirmasi' ? 'success' : 'warning' }}">{{ $pesanan->pembayaran->status_label }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td>Tgl Bayar</td>
                            <td>{{ $pesanan->pembayaran->tanggal_bayar?->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                    @if ($pesanan->pembayaran->bukti_transfer)
                        <div style="margin-top:12px;">
                            <img src="{{ \App\Support\Media::url($pesanan->pembayaran->bukti_transfer) }}"
                                style="max-width:300px;border-radius:8px;border:1px solid #e5e7eb;">
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div>
            @if ($pesanan->pembayaran?->status_bayar === 'dikonfirmasi')
                <div class="card" style="margin-bottom:16px;">
                    <p class="section-label">Struk Pembayaran</p>
                    <a href="{{ route('struk.show', $pesanan) }}" target="_blank" rel="noopener" class="btn btn-primary btn-full"><i class="fa-solid fa-print"></i> Lihat &amp; Cetak Struk</a>
                    <a href="{{ route('struk.pdf', $pesanan) }}" class="btn btn-outline btn-full" style="margin-top:8px;"><i class="fa-solid fa-file-pdf"></i> Unduh PDF</a>
                </div>
            @endif

            @if ($pesanan->refund)
                <div class="card" style="margin-bottom:16px;border:1.5px solid #f59e0b;">
                    <p class="section-label">Refund</p>
                    <p><span class="badge badge-{{ $pesanan->refund->status_color }}">{{ $pesanan->refund->status_label }}</span></p>
                    <p class="form-hint" style="margin:8px 0 12px;">{{ $pesanan->refund->kode_refund }} · {{ $pesanan->refund->alasan_label }}</p>
                    <a href="{{ route('admin.refund.show', $pesanan->refund) }}" class="btn btn-outline btn-full">
                        {{ $pesanan->refund->status === 'diajukan' ? 'Tinjau Pengajuan Refund' : 'Lihat Detail Refund' }}
                    </a>
                </div>
            @endif

            @if ($pesanan->status === 'menunggu_konfirmasi' && $pesanan->pembayaran?->status_bayar === 'pending')
                <div class="card" style="margin-bottom:16px;border:1.5px solid #22c55e;">
                    <p class="section-label">Konfirmasi Pembayaran</p>
                    <form action="{{ route('admin.pesanan.konfirmasi', $pesanan) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-primary btn-full"
                            onclick="return confirm('Konfirmasi pembayaran ini? Stok akan dikurangi otomatis.')">
                            <i class="fa-solid fa-circle-check"></i> Konfirmasi Pembayaran
                        </button>
                    </form>
                </div>
            @endif

            @php
                $alur = [
                    'menunggu_bayar'      => ['dibatalkan'],
                    'menunggu_konfirmasi' => ['dibatalkan'],
                    'diproses'            => ['dikirim', 'dibatalkan'],
                    'dikirim'             => ['selesai'],
                ];
                $labelStatus = ['dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
                $pilihan = $alur[$pesanan->status] ?? [];
            @endphp
            <div class="card">
                <p class="section-label">Update Status Pesanan</p>
                @if (count($pilihan) > 0)
                    <form action="{{ route('admin.pesanan.status', $pesanan) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <select name="status">
                                @foreach ($pilihan as $opsi)
                                    <option value="{{ $opsi }}">{{ $labelStatus[$opsi] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Update Status</button>
                    </form>
                @else
                    <p class="form-hint">Status pesanan ini sudah final dan tidak bisa diubah lagi.</p>
                @endif
                @if (in_array($pesanan->status, ['diproses', 'dikirim', 'selesai'], true))
                    <p class="form-hint" style="margin-top:10px;">Untuk mengembalikan dana pelanggan, gunakan menu Refund (bukan membatalkan pesanan).</p>
                @endif
            </div>
        </div>
    </div>
@endsection
