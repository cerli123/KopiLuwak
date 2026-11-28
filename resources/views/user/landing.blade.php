<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Kopiluwak Lereng Muria</title>
    <meta name="description" content="Kopi luwak dan kopi premium dari lereng Gunung Muria. Lihat proses, galeri, dan pesan online dengan pembayaran mudah.">
    @include('partials.assets')
    @include('partials.mobile-head')
</head>

<body>
    @php
        $foto = fn (string $f) => isset($fotoMap[$f]) ? \App\Support\Media::url($fotoMap[$f]) : null;
    @endphp

    @guest
        <div class="lp-topbar">
            <i class="fa-solid fa-bag-shopping"></i>
            <span class="lp-topbar-long">Ingin memesan? Login atau daftar gratis untuk mulai belanja.</span>
            <span class="lp-topbar-short">Mau belanja? Daftar gratis</span>
            <a href="{{ route('register') }}">Daftar</a>
        </div>
    @endguest

    @include('partials.public-nav')

    @if (session('info'))
        <div class="toast toast-info" id="toast-msg">
            <i class="fa-solid fa-circle-info"></i>{{ session('info') }}
        </div>
    @endif

    @if ($banners->isNotEmpty())
        <section class="lp-carousel-wrap" id="beranda">
            <div class="lp-carousel" data-carousel>
                <div class="lp-slides">
                    @foreach ($banners as $i => $banner)
                        <div class="lp-slide {{ $i === 0 ? 'active' : '' }}">
                            <img src="{{ \App\Support\Media::url($banner->gambar) }}" alt="{{ $banner->judul }}">
                            <div class="lp-slide-overlay"></div>
                            <div class="lp-slide-content">
                                <h2 class="lp-slide-title">{{ $banner->judul }}</h2>
                                @if ($banner->deskripsi)
                                    <p class="lp-slide-desc">{{ $banner->deskripsi }}</p>
                                @endif
                                @if ($banner->tombol_teks && $banner->tombol_url)
                                    <a href="{{ $banner->tombol_url }}" class="btn btn-gold btn-lg">
                                        {{ $banner->tombol_teks }}<i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @if ($banners->count() > 1)
                    <button type="button" class="lp-car-btn prev" data-dir="-1" aria-label="Sebelumnya">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button type="button" class="lp-car-btn next" data-dir="1" aria-label="Berikutnya">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                    <div class="lp-dots">
                        @foreach ($banners as $i => $banner)
                            <button type="button" class="lp-dot {{ $i === 0 ? 'active' : '' }}"
                                data-index="{{ $i }}" aria-label="Slide {{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="lp-counter">
                        <strong class="lp-counter-now">01</strong>
                        <span>/ {{ str_pad($banners->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="lp-progress"><span></span></div>
                @endif
            </div>
        </section>
    @else
        <header class="lp-hero" id="beranda">
            <div class="lp-hero-inner">
                <div>
                    <span class="lp-eyebrow"><i class="fa-solid fa-mountain"></i>Kopi Luwak Lereng Muria</span>
                    <h1 class="lp-title">Nikmatkan Setiap Tegukan <span>Dari Alam Muria</span></h1>
                    <p class="lp-desc">Biji kopi pilihan, dipanen langsung dari lereng Gunung Muria. Rasa autentik,
                        kualitas terjamin.</p>
                    <div class="lp-hero-actions">
                        <a href="{{ route('register') }}" class="btn btn-gold btn-lg">
                            <i class="fa-solid fa-bag-shopping"></i>Daftar &amp; Belanja Sekarang
                        </a>
                        <a href="#produk" class="btn btn-light-outline btn-lg">
                            Lihat Produk<i class="fa-solid fa-arrow-down"></i>
                        </a>
                    </div>
                    <div class="lp-trust">
                        <div><i class="fa-solid fa-seedling"></i>Biji pilihan</div>
                        <div><i class="fa-solid fa-medal"></i>Kualitas terjamin</div>
                        <div><i class="fa-solid fa-wallet"></i>Bayar mudah</div>
                    </div>
                </div>
                <div class="lp-visual">
                    <div class="lp-orb">
                        <img src="{{ $siteLogo }}" alt="Logo Kopiluwak Lereng Muria">
                        <div class="lp-chip c1">
                            <i class="fa-solid fa-mountain"></i>
                            <div><strong>Lereng Muria</strong><small>Asal biji kopi</small></div>
                        </div>
                        <div class="lp-chip c2">
                            <i class="fa-solid fa-mug-hot"></i>
                            <div><strong>Rasa Autentik</strong><small>Di setiap tegukan</small></div>
                        </div>
                        <div class="lp-chip c3">
                            <i class="fa-solid fa-leaf"></i>
                            <div><strong>Biji Pilihan</strong><small>Segar dan berkualitas</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
    @endif

    <section class="lp-features {{ $banners->isNotEmpty() ? 'plain' : '' }}" id="keunggulan">
        <div class="lp-feature-grid">
            <div class="lp-feature">
                <span class="lp-feature-icon"><i class="fa-solid fa-seedling"></i></span>
                <h3>Biji Pilihan</h3>
                <p>Dipilih dengan cermat dari kebun di lereng Gunung Muria.</p>
            </div>
            <div class="lp-feature">
                <span class="lp-feature-icon"><i class="fa-solid fa-mountain-sun"></i></span>
                <h3>Langsung dari Sumbernya</h3>
                <p>Dipanen langsung dari lereng Muria agar aroma tetap terjaga.</p>
            </div>
            <div class="lp-feature">
                <span class="lp-feature-icon"><i class="fa-solid fa-medal"></i></span>
                <h3>Kualitas Terjamin</h3>
                <p>Rasa autentik dengan kualitas konsisten di setiap kemasan.</p>
            </div>
            <div class="lp-feature">
                <span class="lp-feature-icon"><i class="fa-solid fa-wallet"></i></span>
                <h3>Pesan dengan Mudah</h3>
                <p>Checkout cepat dengan pilihan transfer bank dan dompet digital.</p>
            </div>
        </div>
    </section>

    <section class="lp-section about" id="tentang">
        <div class="about-grid">
            <div class="about-photos">
                @if ($u = $foto('07-secangkir-kopi-di-kebun.jpg'))
                    <img class="about-main" src="{{ $u }}" alt="Menikmati secangkir kopi Lereng Muria di tengah kebun" loading="lazy">
                @endif
                @if ($u = $foto('02-biji-luwak-dalam-genggaman.jpg'))
                    <img class="about-sub" src="{{ $u }}" alt="Biji kopi luwak pilihan dalam genggaman" loading="lazy">
                @endif
            </div>
            <div class="about-text">
                <p class="section-label"><i class="fa-solid fa-mountain"></i>Tentang Kami</p>
                <h2 class="lp-section-title" style="text-align:left;">Sentral Produsen Kopi Luwak Lereng Muria</h2>
                <p>Kami mengolah kopi luwak dan kopi pilihan dari lereng Gunung Muria. Biji disortir dengan tangan,
                    diolah dengan telaten, lalu dikemas dalam kaleng eksklusif agar aroma dan rasanya tetap terjaga
                    sampai di tangan Anda.</p>
                <ul class="about-list">
                    <li><i class="fa-solid fa-circle-check"></i>Biji kopi luwak dipilah satu per satu</li>
                    <li><i class="fa-solid fa-circle-check"></i>Kemasan kaleng eksklusif, cocok untuk hadiah</li>
                    <li><i class="fa-solid fa-circle-check"></i>Sentra produksi terbuka untuk dikunjungi tamu</li>
                </ul>
                <div class="lp-hero-actions">
                    <a href="#produk" class="btn btn-primary btn-lg"><i class="fa-solid fa-mug-hot"></i>Lihat Produk</a>
                    <a href="{{ route('galeri') }}" class="btn btn-outline btn-lg"><i class="fa-solid fa-images"></i>Lihat Galeri</a>
                </div>
            </div>
        </div>
    </section>

    <section class="lp-section lp-produk" id="produk">
        <div class="lp-section-head">
            <p class="section-label"><i class="fa-solid fa-mug-hot"></i>Koleksi Pilihan</p>
            <h2 class="lp-section-title">Kopi Segar Lereng Muria</h2>
            <p class="lp-section-sub">Temukan biji kopi terbaik pilihan kami, siap dikirim ke rumah Anda.</p>
        </div>

        <div class="lp-toolbar">
            <div class="chip-bar">
                <a href="{{ route('landing') }}#produk" class="chip {{ !request('kategori') ? 'active' : '' }}">Semua</a>
                @foreach ($kategoris as $kat)
                    <a href="{{ route('landing', ['kategori' => $kat->id]) }}#produk"
                        class="chip {{ request('kategori') == $kat->id ? 'active' : '' }}">
                        {{ $kat->nama_kategori }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($produks->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <p>Produk tidak ditemukan.</p>
                <a href="{{ route('landing') }}#produk" class="btn btn-outline">Lihat Semua Produk</a>
            </div>
        @else
            @if ($produks->count() > 2)
                <p class="rail-hint" aria-hidden="true">
                    Geser untuk melihat produk lainnya <i class="fa-solid fa-arrow-right-long"></i>
                </p>
            @endif
            <div class="produk-grid">
                @foreach ($produks as $produk)
                    <div class="produk-card">
                        <div class="produk-img-wrap">
                            @if ($produk->gambar)
                                <img src="{{ \App\Support\Media::url($produk->gambar) }}" alt="{{ $produk->nama_produk }}">
                            @else
                                <div class="produk-placeholder"><i class="fa-solid fa-mug-hot"></i></div>
                            @endif
                            @if ($produk->sisa_stok < 10 && $produk->sisa_stok > 0)
                                <span class="badge-stok"><i class="fa-solid fa-fire"></i>Stok Terbatas</span>
                            @elseif($produk->sisa_stok === 0)
                                <span class="badge-habis"><i class="fa-solid fa-ban"></i>Habis</span>
                            @endif
                        </div>
                        <div class="produk-body">
                            <p class="produk-kat">{{ $produk->kategori->nama_kategori }}</p>
                            <h3 class="produk-nama">{{ $produk->nama_produk }}</h3>
                            @if ($produk->jumlah_ulasan > 0)
                                <div class="produk-rating">@include('partials.bintang', ['nilai' => $produk->rata_rating, 'jumlah' => $produk->jumlah_ulasan, 'kecil' => true])</div>
                            @endif
                            <p class="produk-harga">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                            <p class="produk-stok {{ $produk->sisa_stok < 10 ? 'low' : '' }}">
                                <i class="fa-solid fa-box"></i>Stok: {{ $produk->sisa_stok }} pcs
                            </p>
                            @if ($produk->sisa_stok > 0)
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-full btn-sm">
                                        <i class="fa-solid fa-cart-plus"></i>Keranjang
                                    </a>
                                @else
                                    @if (auth()->user()->role === 'admin')
                                        <a href="{{ route('admin.produk.index') }}" class="btn btn-outline btn-full btn-sm">
                                            <i class="fa-solid fa-pen-to-square"></i>Kelola Produk
                                        </a>
                                    @else
                                        <form action="{{ route('keranjang.tambah') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                            <input type="hidden" name="qty" value="1">
                                            <button type="submit" class="btn btn-primary btn-full btn-sm">
                                                <i class="fa-solid fa-cart-plus"></i>Keranjang
                                            </button>
                                        </form>
                                    @endif
                                @endguest
                            @else
                                <button class="btn btn-disabled btn-full btn-sm" disabled>Stok Habis</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="pagination-wrap">{{ $produks->links() }}</div>
        @endif
    </section>

    <section class="lp-section" id="proses">
        <div class="lp-section-head">
            <p class="section-label"><i class="fa-solid fa-seedling"></i>Proses Kami</p>
            <h2 class="lp-section-title">Dari Kebun hingga Kemasan</h2>
            <p class="lp-section-sub">Empat tahap sederhana yang kami jaga agar kopi sampai ke Anda dalam kondisi terbaik.</p>
        </div>
        @php
            $langkah = [
                ['06-biji-bunga-buah-merah.jpg', 'Panen & Pengumpulan', 'Buah kopi matang dan biji pilihan dikumpulkan dari kebun di lereng Muria.'],
                ['04-sortir-biji-luwak.jpg', 'Sortir Manual', 'Biji dipilah satu per satu dengan tangan untuk menjaga mutu.'],
                ['05-kopi-bubuk-giling.jpg', 'Sangrai & Giling', 'Biji diolah menjadi kopi bubuk dengan warna dan aroma yang pekat.'],
                ['03-kaleng-lereng-muria.jpg', 'Kemas & Kirim', 'Dikemas dalam kaleng eksklusif, siap dikirim atau dijadikan hadiah.'],
            ];
        @endphp
        <div class="proses-grid">
            @foreach ($langkah as $i => [$file, $judul, $isi])
                <article class="proses-card">
                    <div class="proses-img">
                        @if ($u = $foto($file))
                            <img src="{{ $u }}" alt="{{ $judul }}" loading="lazy">
                        @else
                            <div class="produk-placeholder"><i class="fa-solid fa-mug-hot"></i></div>
                        @endif
                        <span class="proses-no">{{ $i + 1 }}</span>
                    </div>
                    <h3>{{ $judul }}</h3>
                    <p>{{ $isi }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if ($galeriUnggulan->isNotEmpty())
        <section class="lp-section" id="galeri-sekilas">
            <div class="lp-section-head">
                <p class="section-label"><i class="fa-solid fa-camera-retro"></i>Galeri</p>
                <h2 class="lp-section-title">Momen dari Lereng Muria</h2>
                <p class="lp-section-sub">Kebun, proses, produk, dan kunjungan tamu di sentra produksi kami.</p>
            </div>
            <div class="galeri-sekilas">
                @foreach ($galeriUnggulan as $g)
                    <button type="button" class="galeri-item" data-lightbox="{{ $g->url }}"
                        data-caption="{{ $g->judul }}" data-desc="{{ $g->deskripsi }}">
                        <img src="{{ $g->url }}" alt="{{ $g->judul ?: 'Foto galeri' }}" loading="lazy">
                        <span class="galeri-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                        @if ($g->judul)
                            <span class="galeri-caption">{{ $g->judul }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
            <div style="text-align:center;margin-top:28px;">
                <a href="{{ route('galeri') }}" class="btn btn-outline btn-lg">
                    Lihat Semua Foto<i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </section>
    @endif

    @include('partials.footer')
    @include('partials.lightbox')

    @include('partials.mobile-nav-user')

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
