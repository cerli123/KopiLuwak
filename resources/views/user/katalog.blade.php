@extends('layouts.app')
@section('title', 'Katalog Produk')
@section('content')
    <div class="page-wrap">
        <div class="page-header">
            <div>
                <p class="section-label">Koleksi Pilihan</p>
                <h2 class="page-title">Kopi Segar Lereng Muria</h2>
            </div>
            <form action="{{ route('katalog') }}" method="GET" class="search-form">
                @if (request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
                <button type="submit" class="btn btn-outline">Cari</button>
                @if (request('search') || request('kategori'))
                    <a href="{{ route('katalog') }}" class="btn btn-ghost">Reset</a>
                @endif
            </form>
        </div>

        <div class="chip-bar">
            <a href="{{ route('katalog', request()->except('kategori')) }}"
                class="chip {{ !request('kategori') ? 'active' : '' }}">Semua</a>
            @foreach ($kategoris as $kat)
                <a href="{{ route('katalog', array_merge(request()->all(), ['kategori' => $kat->id])) }}"
                    class="chip {{ request('kategori') == $kat->id ? 'active' : '' }}">
                    {{ $kat->nama_kategori }}
                </a>
            @endforeach
        </div>

        @if ($produks->isEmpty())
            <div class="empty-state">
                <p class="empty-icon"><i class="fa-solid fa-box-open"></i></p>
                <p>Produk tidak ditemukan.</p>
                <a href="{{ route('katalog') }}" class="btn btn-outline">Lihat Semua Produk</a>
            </div>
        @else
            <div class="produk-grid">
                @foreach ($produks as $produk)
                    <div class="produk-card">
                        <a href="{{ route('produk.show', $produk) }}" class="produk-img-wrap">
                            @if ($produk->gambar_url)
                                <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_produk }}">
                            @else
                                <div class="produk-placeholder"><i class="fa-solid fa-mug-hot"></i></div>
                            @endif
                            @if ($produk->sisa_stok < 10 && $produk->sisa_stok > 0)
                                <span class="badge-stok">Stok Terbatas</span>
                            @elseif($produk->sisa_stok === 0)
                                <span class="badge-habis">Habis</span>
                            @endif
                        </a>
                        <div class="produk-body">
                            <p class="produk-kat">{{ $produk->kategori->nama_kategori }}</p>
                            <h3 class="produk-nama">
                                <a href="{{ route('produk.show', $produk) }}">{{ $produk->nama_produk }}</a>
                            </h3>
                            @if ($produk->jumlah_ulasan > 0)
                                <div class="produk-rating">@include('partials.bintang', ['nilai' => $produk->rata_rating, 'jumlah' => $produk->jumlah_ulasan, 'kecil' => true])</div>
                            @endif
                            <p class="produk-harga">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                            <p class="produk-stok {{ $produk->sisa_stok < 10 ? 'low' : '' }}">
                                Stok: {{ $produk->sisa_stok }} pcs
                            </p>
                            @if ($produk->sisa_stok > 0)
                                <form action="{{ route('keranjang.tambah') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-primary btn-full btn-sm">+ Keranjang</button>
                                </form>
                            @else
                                <button class="btn btn-disabled btn-full btn-sm" disabled>Stok Habis</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination-wrap">{{ $produks->links() }}</div>
        @endif
    </div>
@endsection
