@extends('layouts.app')
@section('title', 'Detail Refund')
@section('content')
    <div class="page-wrap page-md">
        <a href="{{ route('refund.index') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i> Kembali ke Refund Saya</a>

        <div class="detail-order-grid">
            <div>
                <div class="card" style="margin-bottom:16px;">
                    <p class="section-label">Pengajuan Refund</p>
                    <table class="info-tbl">
                        <tr>
                            <td>Kode Refund</td>
                            <td><strong>{{ $refund->kode_refund }}</strong></td>
                        </tr>
                        <tr>
                            <td>Pesanan</td>
                            <td><a href="{{ route('pesanan.show', $refund->pesanan) }}"><strong>{{ $refund->pesanan->kode_pesanan }}</strong></a></td>
                        </tr>
                        <tr>
                            <td>Diajukan</td>
                            <td>{{ $refund->created_at->format('d M Y, H:i') }} WIB</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td><span class="badge badge-{{ $refund->status_color }}">{{ $refund->status_label }}</span></td>
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
                            <td>Jumlah Dana</td>
                            <td><strong>Rp {{ number_format($refund->jumlah_refund, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>

                    @if ($refund->bukti_url)
                        <div style="margin-top:12px;">
                            <p class="form-hint" style="margin-bottom:6px;">Foto bukti:</p>
                            <a href="{{ $refund->bukti_url }}" target="_blank" rel="noopener">
                                <img src="{{ $refund->bukti_url }}" alt="Bukti refund"
                                    style="max-width:280px;border-radius:8px;border:1px solid #e5e7eb;">
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div>
                @if ($refund->status === 'diajukan')
                    <div class="card refund-status refund-status-wait">
                        <p class="refund-status-title"><i class="fa-solid fa-hourglass-half"></i> Menunggu Keputusan Admin</p>
                        <p>Pengajuan Anda sedang ditinjau. Anda masih bisa membatalkan pengajuan ini selama belum diputuskan.</p>
                        <form action="{{ route('refund.batal', $refund) }}" method="POST" style="margin-top:14px;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-outline btn-full"
                                onclick="return confirm('Batalkan pengajuan refund ini?')">
                                <i class="fa-solid fa-xmark"></i> Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                @elseif ($refund->status === 'disetujui')
                    <div class="card refund-status refund-status-ok">
                        <p class="refund-status-title"><i class="fa-solid fa-circle-check"></i> Refund Disetujui</p>
                        <p>Pengajuan Anda disetujui. Dana sedang diproses dan akan dikembalikan ke Anda.</p>
                        @if ($refund->catatan_admin)
                            <p class="refund-admin-note"><strong>Catatan admin:</strong> {{ $refund->catatan_admin }}</p>
                        @endif
                    </div>
                @elseif ($refund->status === 'selesai')
                    <div class="card refund-status refund-status-ok">
                        <p class="refund-status-title"><i class="fa-solid fa-circle-check"></i> Dana Sudah Dikembalikan</p>
                        <p>{{ $refund->metode_pengembalian_label }}.</p>
                        @if ($refund->dikembalikan_pada)
                            <p class="form-hint">Pada {{ $refund->dikembalikan_pada->format('d M Y, H:i') }} WIB</p>
                        @endif
                        @if ($refund->referensi_pengembalian)
                            <p class="form-hint">Referensi: {{ $refund->referensi_pengembalian }}</p>
                        @endif
                        @if ($refund->catatan_admin)
                            <p class="refund-admin-note"><strong>Catatan admin:</strong> {{ $refund->catatan_admin }}</p>
                        @endif
                    </div>
                @elseif ($refund->status === 'ditolak')
                    <div class="card refund-status refund-status-no">
                        <p class="refund-status-title"><i class="fa-solid fa-circle-xmark"></i> Refund Ditolak</p>
                        <p>Mohon maaf, pengajuan refund Anda tidak dapat disetujui.</p>
                        <p class="refund-admin-note"><strong>Alasan penolakan:</strong> {{ $refund->catatan_admin }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
