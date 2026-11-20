@extends('layouts.app')
@section('title', 'Riwayat Pesanan')
@section('content')
    <div class="page-wrap page-md">
        <h2 class="page-title">Riwayat Pesanan</h2>

        @forelse($pesanans as $pesanan)
            <div class="order-card">
                <div class="order-head">
                    <div>
                        <p class="order-kode">{{ $pesanan->kode_pesanan }}</p>
                        <p class="order-tgl">{{ $pesanan->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="badge badge-{{ $pesanan->status_color }}">{{ $pesanan->status_label }}</span>
                </div>
                <div class="order-body">
                    <p class="order-items">
                        {{ $pesanan->detailPesanans->map(fn($d) => $d->produk->nama_produk . ' ×' . $d->qty)->join(' · ') }}</p>
                </div>
                <div class="order-foot">
                    <span class="order-total">Total: Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</span>
                    <div class="order-actions">
                        <a href="{{ route('pesanan.show', $pesanan) }}" class="btn btn-outline btn-sm">Lihat Detail</a>
                        @if ($pesanan->status === 'menunggu_bayar')
                            <a href="{{ route('pembayaran.form', $pesanan) }}" class="btn btn-primary btn-sm">Bayar
                                Sekarang</a>
                        @endif
                        @if ($pesanan->pembayaran?->status_bayar === 'dikonfirmasi')
                            <a href="{{ route('struk.show', $pesanan) }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><i class="fa-solid fa-print"></i> Struk</a>
                        @endif
                        @if ($pesanan->refund)
                            <a href="{{ route('refund.show', $pesanan->refund) }}" class="btn btn-outline btn-sm">Refund:
                                {{ $pesanan->refund->status_label }}</a>
                        @elseif ($pesanan->bisaRefund())
                            <a href="{{ route('refund.create', $pesanan) }}" class="btn btn-outline btn-sm">Ajukan Refund</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-receipt"></i></div>
                <p>Belum ada pesanan.</p>
                <a href="{{ route('katalog') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        @endforelse

        <div class="pagination-wrap">{{ $pesanans->links() }}</div>
    </div>
@endsection
