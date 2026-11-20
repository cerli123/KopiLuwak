@extends('layouts.app')
@section('title', 'Refund Saya')
@section('content')
    <div class="page-wrap page-md">
        <h2 class="page-title">Refund Saya</h2>

        @forelse ($refunds as $refund)
            <div class="order-card">
                <div class="order-head">
                    <div>
                        <p class="order-kode">{{ $refund->kode_refund }}</p>
                        <p class="order-tgl">Pesanan {{ $refund->pesanan->kode_pesanan }} · diajukan {{ $refund->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                    <span class="badge badge-{{ $refund->status_color }}">{{ $refund->status_label }}</span>
                </div>
                <div class="order-body">
                    <p class="order-items">{{ $refund->alasan_label }}</p>
                </div>
                <div class="order-foot">
                    <span class="order-total">Dana: Rp {{ number_format($refund->jumlah_refund, 0, ',', '.') }}</span>
                    <div class="order-actions">
                        <a href="{{ route('refund.show', $refund) }}" class="btn btn-outline btn-sm">Lihat Detail</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-rotate-left"></i></div>
                <p>Belum ada pengajuan refund.</p>
                <a href="{{ route('pesanan.index') }}" class="btn btn-primary">Lihat Pesanan Saya</a>
            </div>
        @endforelse

        <div class="pagination-wrap">{{ $refunds->links() }}</div>
    </div>
@endsection
