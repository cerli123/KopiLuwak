@extends('layouts.app')
@section('title', 'Keranjang Belanja')
@section('content')
    <div class="page-wrap">
        <h2 class="page-title">Keranjang Belanja</h2>

        @if ($items->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                <p>Keranjang Anda masih kosong.</p>
                <a href="{{ route('katalog') }}" class="btn btn-primary">Mulai Belanja</a>
            </div>
        @else
            <div class="cart-layout">
                <div class="card">
                    @foreach ($items as $item)
                        <div class="cart-item">
                            <div class="cart-img">
                                @if ($item->produk->gambar)
                                    <img src="{{ \App\Support\Media::url($item->produk->gambar) }}" alt="">
                                @else
                                    <i class="fa-solid fa-mug-hot"></i>
                                @endif
                            </div>
                            <div class="cart-info">
                                <p class="cart-nama">{{ $item->produk->nama_produk }}</p>
                                <p class="cart-harga">Rp {{ number_format($item->produk->harga, 0, ',', '.') }} / pcs</p>
                                <div class="qty-inline">
                                    <form action="{{ route('keranjang.update', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="qty" value="{{ max(1, $item->qty - 1) }}">
                                        <button class="qty-btn" {{ $item->qty <= 1 ? 'disabled' : '' }}><i class="fa-solid fa-minus"></i></button>
                                    </form>
                                    <span class="qty-num">{{ $item->qty }}</span>
                                    <form action="{{ route('keranjang.update', $item->id) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="qty" value="{{ $item->qty + 1 }}">
                                        <button class="qty-btn"><i class="fa-solid fa-plus"></i></button>
                                    </form>
                                    <form action="{{ route('keranjang.hapus', $item->id) }}" method="POST"
                                        style="display:inline;margin-left:8px;">
                                        @csrf @method('DELETE')
                                        <button class="btn-link-danger"
                                            onclick="return confirm('Hapus item ini?')"><i class="fa-solid fa-trash-can"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                            <div class="cart-subtotal">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</div>
                        </div>
                    @endforeach
                </div>

                <div>
                    <div class="card summary-card">
                        <p class="section-label">Ringkasan Pesanan</p>
                        <div class="summary-row"><span>Subtotal ({{ $items->count() }} produk)</span><span>Rp
                                {{ number_format($total, 0, ',', '.') }}</span></div>
                        <div class="summary-row"><span>Ongkos Kirim</span><span>Dihitung saat checkout</span></div>
                        <hr class="summary-hr">
                        <div class="summary-total"><span>Total Sementara</span><span>Rp
                                {{ number_format($total, 0, ',', '.') }}</span></div>
                        <a href="{{ route('checkout') }}" class="btn btn-primary btn-full" style="margin-top:20px;">Lanjut
                            ke Checkout <i class="fa-solid fa-arrow-right"></i></a>
                        <a href="{{ route('katalog') }}" class="btn btn-outline btn-full" style="margin-top:8px;">Lanjut
                            Belanja</a>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
