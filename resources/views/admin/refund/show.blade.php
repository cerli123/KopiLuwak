@extends('layouts.admin')
@section('title', 'Detail Refund')
@section('page-title', 'Detail Refund')
@section('content')
    <a href="{{ route('admin.refund.index') }}" class="btn btn-outline btn-sm" style="margin-bottom:20px;"><i class="fa-solid fa-arrow-left"></i> Kembali</a>

    <div class="detail-admin-grid">
        <div>
            <div class="card" style="margin-bottom:16px;">
                <p class="section-label">Pengajuan Pelanggan</p>
                <table class="info-tbl">
                    <tr>
                        <td>Kode Refund</td>
                        <td><strong>{{ $refund->kode_refund }}</strong></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><span class="badge badge-{{ $refund->status_color }}">{{ $refund->status_label }}</span></td>
                    </tr>
                    <tr>
                        <td>Pelanggan</td>
                        <td>{{ $refund->user->nama }} ({{ $refund->user->email }})</td>
                    </tr>
                    <tr>
                        <td>Diajukan</td>
                        <td>{{ $refund->created_at->format('d M Y, H:i') }} WIB</td>
                    </tr>
                    <tr>
                        <td>Alasan</td>
                        <td>{{ $refund->alasan_label }}</td>
                    </tr>
                    <tr>
                        <td>Penjelasan</td>
                        <td>{{ $refund->alasan_detail }}</td>
                    </tr>
                    <tr>
                        <td>Dana Dikembalikan</td>
                        <td><strong>Rp {{ number_format($refund->jumlah_refund, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>

                @if ($refund->bukti_url)
                    <div style="margin-top:12px;">
                        <p class="form-hint" style="margin-bottom:6px;">Foto bukti:</p>
                        <a href="{{ $refund->bukti_url }}" target="_blank" rel="noopener">
                            <img src="{{ $refund->bukti_url }}" alt="Bukti refund"
                                style="max-width:300px;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    </div>
                @else
                    <p class="form-hint" style="margin-top:12px;">Pelanggan tidak melampirkan foto bukti.</p>
                @endif
            </div>

            <div class="card" style="margin-bottom:16px;">
                <p class="section-label">Pesanan Terkait</p>
                <table class="info-tbl">
                    <tr>
                        <td>Kode Pesanan</td>
                        <td><a href="{{ route('admin.pesanan.show', $refund->pesanan) }}"><strong>{{ $refund->pesanan->kode_pesanan }}</strong></a></td>
                    </tr>
                    <tr>
                        <td>Status Pesanan</td>
                        <td><span class="badge badge-{{ $refund->pesanan->status_color }}">{{ $refund->pesanan->status_label }}</span></td>
                    </tr>
                    <tr>
                        <td>Metode Bayar</td>
                        <td>{{ $refund->pesanan->pembayaran?->metode_label ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Bayar</td>
                        <td>{{ $refund->pesanan->pembayaran?->tanggal_bayar?->format('d M Y, H:i') ?? '-' }}</td>
                    </tr>
                </table>
                @foreach ($refund->pesanan->detailPesanans as $d)
                    <div class="summary-row" style="margin-top:6px;">
                        <span>{{ $d->produk->nama_produk }} ×{{ $d->qty }}</span>
                        <span>Rp {{ number_format($d->subtotal(), 0, ',', '.') }}</span>
                    </div>
                @endforeach
                <div class="summary-row">
                    <span>Ongkir</span>
                    <span>Rp {{ number_format($refund->pesanan->ongkir, 0, ',', '.') }}</span>
                </div>
                <div class="order-total-row">
                    <span>Total Dibayar</span>
                    <span>Rp {{ number_format($refund->pesanan->total_bayar, 0, ',', '.') }}</span>
                </div>
            </div>

            @if ($refund->sudahDiputuskan())
                <div class="card">
                    <p class="section-label">Keputusan Admin</p>
                    <table class="info-tbl">
                        <tr>
                            <td>Diputuskan oleh</td>
                            <td>{{ $refund->admin?->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>{{ $refund->diproses_pada?->format('d M Y, H:i') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>{{ $refund->status === 'ditolak' ? 'Alasan penolakan' : 'Catatan' }}</td>
                            <td>{{ $refund->catatan_admin ?: '-' }}</td>
                        </tr>
                        @if ($refund->status !== 'ditolak')
                            <tr>
                                <td>Pengembalian dana</td>
                                <td>{{ $refund->metode_pengembalian_label }}</td>
                            </tr>
                            <tr>
                                <td>Referensi</td>
                                <td>{{ $refund->referensi_pengembalian ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Dikembalikan pada</td>
                                <td>{{ $refund->dikembalikan_pada?->format('d M Y, H:i') ?? 'Belum' }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            @endif
        </div>

        <div>
            @if ($refund->status === 'diajukan')
                <div class="card" style="margin-bottom:16px;border:1.5px solid #22c55e;">
                    <p class="section-label">Setujui Refund</p>
                    <p class="form-hint" style="margin-bottom:10px;">
                        Pesanan akan dibatalkan.
                        @if ($refund->pesanan->status === 'diproses')
                            Stok produk dikembalikan otomatis karena pesanan belum dikirim.
                        @else
                            Stok tidak dikembalikan otomatis karena pesanan sudah dikirim.
                        @endif
                    </p>
                    <form action="{{ route('admin.refund.setujui', $refund) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label for="catatan_setuju">Catatan untuk pelanggan (opsional)</label>
                            <textarea id="catatan_setuju" name="catatan_admin" rows="3" maxlength="500">{{ old('catatan_admin') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full"
                            onclick="return confirm('Setujui refund Rp {{ number_format($refund->jumlah_refund, 0, ',', '.') }}? Pesanan akan dibatalkan.')">
                            <i class="fa-solid fa-circle-check"></i> Setujui Refund
                        </button>
                    </form>
                </div>

                <div class="card" style="border:1.5px solid #ef4444;">
                    <p class="section-label">Tolak Refund</p>
                    <form action="{{ route('admin.refund.tolak', $refund) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label for="catatan_tolak">Alasan penolakan (wajib, dilihat pelanggan)</label>
                            <textarea id="catatan_tolak" name="catatan_admin" rows="4" minlength="10" maxlength="500" required
                                placeholder="Contoh: Foto bukti tidak menunjukkan kerusakan pada produk.">{{ old('catatan_admin') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-danger btn-full"
                            onclick="return confirm('Tolak pengajuan refund ini?')">
                            <i class="fa-solid fa-circle-xmark"></i> Tolak Refund
                        </button>
                    </form>
                </div>
            @elseif ($refund->status === 'disetujui')
                <div class="card" style="border:1.5px solid #3b82f6;">
                    <p class="section-label">Pengembalian Dana Manual</p>
                    <p class="form-hint" style="margin-bottom:10px;">
                        Dana belum bisa dikembalikan otomatis. Transfer
                        <strong>Rp {{ number_format($refund->jumlah_refund, 0, ',', '.') }}</strong>
                        ke pelanggan, lalu isi referensi transfer di bawah.
                    </p>
                    <form action="{{ route('admin.refund.selesai', $refund) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="form-group">
                            <label for="referensi">Referensi / keterangan transfer</label>
                            <input type="text" id="referensi" name="referensi_pengembalian" maxlength="100" required
                                value="{{ old('referensi_pengembalian') }}" placeholder="Contoh: Transfer BCA 09 Nov 2026">
                        </div>
                        <button type="submit" class="btn btn-primary btn-full"
                            onclick="return confirm('Tandai dana sudah dikembalikan ke pelanggan?')">
                            <i class="fa-solid fa-money-bill-transfer"></i> Tandai Dana Sudah Dikembalikan
                        </button>
                    </form>
                </div>
            @else
                <div class="card">
                    <p class="section-label">Status Final</p>
                    <p class="form-hint">Pengajuan ini sudah selesai diproses dan tidak bisa diubah lagi.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
