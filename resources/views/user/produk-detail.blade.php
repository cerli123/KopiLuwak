@extends('layouts.app')
@section('title', $produk->nama_produk)
@section('content')
    <div class="page-wrap">
        <a href="{{ route('katalog') }}" class="back-link"><i class="fa-solid fa-arrow-left"></i>Kembali ke Katalog</a>

        <div class="detail-grid">
            <div class="detail-img">
                @if ($produk->gambar_url)
                    <img src="{{ $produk->gambar_url }}" alt="{{ $produk->nama_produk }}">
                @else
                    <div class="detail-placeholder"><i class="fa-solid fa-mug-hot"></i></div>
                @endif
            </div>
            <div class="detail-info">
                <p class="produk-kat">{{ $produk->kategori->nama_kategori }}</p>
                <h1 class="detail-nama">{{ $produk->nama_produk }}</h1>
                @if ($jumlahUlasan > 0)
                    <a href="#ulasan" class="detail-rating">
                        @include('partials.bintang', ['nilai' => $rataRating, 'jumlah' => $jumlahUlasan])
                    </a>
                @endif
                <p class="detail-harga">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                <p class="detail-stok {{ $produk->sisa_stok < 10 ? 'low' : '' }}">
                    {{ $produk->sisa_stok > 0 ? "Stok: {$produk->sisa_stok} pcs" : 'Stok Habis' }}
                </p>
                @if ($produk->deskripsi)
                    <p class="detail-desc">{{ $produk->deskripsi }}</p>
                @endif

                @if ($produk->sisa_stok > 0)
                    <form action="{{ route('keranjang.tambah') }}" method="POST" class="detail-form">
                        @csrf
                        <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                        <div class="qty-wrap">
                            <label>Jumlah:</label>
                            <div class="qty-ctrl">
                                <button type="button" class="qty-btn" onclick="qtyChange(-1)">−</button>
                                <input type="number" name="qty" id="qty-input" value="1" min="1"
                                    max="{{ $produk->sisa_stok }}">
                                <button type="button" class="qty-btn" onclick="qtyChange(1)">+</button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full" style="margin-top:16px;">+ Tambah ke
                            Keranjang</button>
                    </form>
                @else
                    <div class="btn btn-disabled btn-full" style="margin-top:16px;">Stok Habis</div>
                @endif
            </div>
        </div>


        <section class="ulasan-section" id="ulasan">
            <h3 class="related-title">Ulasan Pelanggan</h3>

            <div class="ulasan-grid">
                <div class="card ulasan-ringkas">
                    @if ($jumlahUlasan > 0)
                        <p class="ulasan-skor">{{ number_format($rataRating, 1, ',', '.') }}<span>/5</span></p>
                        @include('partials.bintang', ['nilai' => $rataRating])
                        <p class="ulasan-total">{{ $jumlahUlasan }} ulasan</p>
                        <div class="ulasan-dist">
                            @for ($b = 5; $b >= 1; $b--)
                                @php $n = (int) ($perBintang[$b] ?? 0); $persen = $jumlahUlasan ? round($n / $jumlahUlasan * 100) : 0; @endphp
                                <div class="dist-row">
                                    <span>{{ $b }} <i class="fa-solid fa-star"></i></span>
                                    <div class="dist-bar"><div style="width: {{ $persen }}%"></div></div>
                                    <span class="dist-n">{{ $n }}</span>
                                </div>
                            @endfor
                        </div>
                    @else
                        <p class="ulasan-skor" style="font-size:20px;">Belum ada ulasan</p>
                        <p class="ulasan-total">Jadilah yang pertama mengulas produk ini.</p>
                    @endif
                </div>

                <div>
                    @if ($pesananBisaUlas)
                        <div class="card" style="margin-bottom:16px;">
                            <p class="section-label">Tulis Ulasan</p>
                            <p class="form-hint" style="margin-bottom:12px;">Terima kasih sudah membeli! Dari pesanan {{ $pesananBisaUlas->kode_pesanan }}.</p>
                            @include('user.partials.ulasan-form', [
                                'action' => route('ulasan.store', [$pesananBisaUlas, $produk]),
                                'method' => 'POST',
                                'key'    => 'ulasan_'.$pesananBisaUlas->id.'_'.$produk->id,
                            ])
                        </div>
                    @endif

                    @forelse ($ulasans as $u)
                        @include('user.partials.ulasan-item', ['u' => $u, 'bolehUbah' => $u->user_id === auth()->id()])
                    @empty
                        <div class="card" style="text-align:center;color:var(--gray-500);">
                            <i class="fa-regular fa-comments" style="font-size:28px;margin-bottom:8px;"></i>
                            <p>Belum ada ulasan untuk produk ini.</p>
                            @unless ($pesananBisaUlas)
                                <p class="form-hint">Ulasan dapat ditulis setelah pesanan Anda berstatus selesai.</p>
                            @endunless
                        </div>
                    @endforelse

                    <div class="pagination-wrap">{{ $ulasans->links() }}</div>
                </div>
            </div>
        </section>

        @if ($related->isNotEmpty())
            <div class="related-section">
                <h3 class="related-title">Produk Serupa</h3>
                <div class="produk-grid">
                    @foreach ($related as $p)
                        <div class="produk-card">
                            <a href="{{ route('produk.show', $p) }}" class="produk-img-wrap">
                                @if ($p->gambar_url)
                                    <img src="{{ $p->gambar_url }}" alt="{{ $p->nama_produk }}">
                                @else<div class="produk-placeholder"><i class="fa-solid fa-mug-hot"></i></div>
                                @endif
                            </a>
                            <div class="produk-body">
                                <h3 class="produk-nama"><a href="{{ route('produk.show', $p) }}">{{ $p->nama_produk }}</a>
                                </h3>
                                <p class="produk-harga">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                                <form action="{{ route('keranjang.tambah') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="produk_id" value="{{ $p->id }}">
                                    <input type="hidden" name="qty" value="1">
                                    <button type="submit" class="btn btn-outline btn-full btn-sm">+ Keranjang</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @push('scripts')
        <script>
            function qtyChange(d) {
                const el = document.getElementById('qty-input');
                const v = parseInt(el.value) + d;
                if (v >= 1 && v <= parseInt(el.max)) el.value = v;
            }
        </script>
    @endpush
@endsection
