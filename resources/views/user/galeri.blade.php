@extends(auth()->check() && auth()->user()->role === 'user' ? 'layouts.app' : 'layouts.public')
@section('title', 'Galeri — Kopiluwak Lereng Muria')
@section('description', 'Galeri foto kebun, proses pengolahan, produk, dan kunjungan tamu di Sentral Produsen Kopi Luwak Lereng Muria.')

@section('content')
    <header class="page-banner">
        <p class="section-label"><i class="fa-solid fa-images"></i>Galeri</p>
        <h1 class="page-banner-title">Momen dari Lereng Muria</h1>
        <p class="page-banner-sub">Intip kebun, proses pengolahan, produk, dan kunjungan tamu di sentra produksi kami.</p>
    </header>

    <section class="lp-section galeri-section">
        @if ($kategoris->isNotEmpty())
            <div class="chip-bar galeri-filter">
                <a href="{{ route('galeri') }}" class="chip {{ ! request('kategori') ? 'active' : '' }}">Semua</a>
                @foreach ($kategoris as $kat)
                    <a href="{{ route('galeri', ['kategori' => $kat]) }}"
                        class="chip {{ request('kategori') === $kat ? 'active' : '' }}">{{ $kat }}</a>
                @endforeach
            </div>
        @endif

        @if ($galeris->isEmpty())
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-images"></i></div>
                <p>Belum ada foto di galeri.</p>
                <a href="{{ route('landing') }}" class="btn btn-outline">Kembali ke Beranda</a>
            </div>
        @else
            <div class="galeri-grid">
                @foreach ($galeris as $galeri)
                    <button type="button" class="galeri-item" data-lightbox="{{ $galeri->url }}"
                        data-caption="{{ $galeri->judul }}" data-desc="{{ $galeri->deskripsi }}">
                        <img src="{{ $galeri->url }}" alt="{{ $galeri->judul ?: 'Foto galeri' }}" loading="lazy">
                        <span class="galeri-zoom"><i class="fa-solid fa-magnifying-glass-plus"></i></span>
                        @if ($galeri->kategori)
                            <span class="galeri-tag">{{ $galeri->kategori }}</span>
                        @endif
                        @if ($galeri->judul)
                            <span class="galeri-caption">{{ $galeri->judul }}</span>
                        @endif
                    </button>
                @endforeach
            </div>
            <div class="pagination-wrap">{{ $galeris->links() }}</div>
        @endif
    </section>

    @include('partials.lightbox')
@endsection
