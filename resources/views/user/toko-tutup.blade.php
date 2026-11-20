@extends('layouts.app')
@section('title', 'Toko Sedang Tutup')
@section('content')
    <div class="page-wrap page-sm">
        <div class="card toko-tutup-card">
            <div class="toko-tutup-icon"><i class="fa-solid fa-store-slash"></i></div>
            <h2 class="toko-tutup-title">Toko Sedang Tutup</h2>
            <p class="toko-tutup-pesan">{{ $pesan }}</p>
            @if ($bukaKembali)
                <p class="toko-tutup-buka"><i class="fa-regular fa-clock"></i> Buka kembali: <strong>{{ $bukaKembali }}</strong></p>
            @endif
            <p class="form-hint" style="margin:16px 0;">
                Selama toko tutup, belanja, keranjang, checkout, dan pembayaran dinonaktifkan.
                Anda tetap bisa melihat riwayat pesanan, status refund, dan profil.
            </p>
            <div class="toko-tutup-aksi">
                <a href="{{ route('pesanan.index') }}" class="btn btn-primary"><i class="fa-solid fa-receipt"></i> Pesanan Saya</a>
                <a href="{{ route('refund.index') }}" class="btn btn-outline"><i class="fa-solid fa-rotate-left"></i> Refund Saya</a>
                <a href="{{ route('profil') }}" class="btn btn-outline"><i class="fa-solid fa-user-pen"></i> Profil</a>
            </div>
        </div>
    </div>
@endsection
