{{-- Bottom navigation HP untuk panel admin --}}
@php
    $mBadgePesanan = $badgePesanan ?? \App\Models\Pesanan::where('status', 'menunggu_konfirmasi')->count();
    $mBadgeRefund  = $badgeRefund  ?? \App\Models\Refund::where('status', 'diajukan')->count();
    $mTokoBuka     = $tokoBukaNav  ?? \App\Models\Pengaturan::tokoBuka();
    $mNama         = auth()->user()->nama;
@endphp

<nav class="m-nav" aria-label="Menu admin">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
        <i class="fa-solid fa-gauge-high"></i><span class="m-lbl">Dashboard</span>
    </a>
    <a href="{{ route('admin.pesanan.index') }}" class="{{ request()->routeIs('admin.pesanan*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-bag-shopping"></i><span class="m-lbl">Pesanan</span>
        @if ($mBadgePesanan > 0)
            <span class="m-badge">{{ $mBadgePesanan }}</span>
        @endif
    </a>
    <a href="{{ route('admin.produk.index') }}" class="{{ request()->routeIs('admin.produk*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-mug-hot"></i><span class="m-lbl">Produk</span>
    </a>
    <a href="{{ route('admin.refund.index') }}" class="{{ request()->routeIs('admin.refund*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-rotate-left"></i><span class="m-lbl">Refund</span>
        @if ($mBadgeRefund > 0)
            <span class="m-badge">{{ $mBadgeRefund }}</span>
        @endif
    </a>
    <button type="button" data-sheet-open="m-sheet-admin"
        class="{{ request()->routeIs('admin.ulasan*', 'admin.stok*', 'admin.kategori*', 'admin.laporan*', 'admin.banner*', 'admin.galeri*', 'admin.pengaturan*') ? 'is-active' : '' }}">
        <i class="fa-solid fa-grip"></i><span class="m-lbl">Lainnya</span>
    </button>
</nav>

<div class="m-sheet" id="m-sheet-admin" role="dialog" aria-label="Menu lainnya">
    <div class="m-sheet-handle"></div>
    <div class="m-sheet-user">
        <div class="m-sheet-avatar">{{ strtoupper(substr($mNama, 0, 2)) }}</div>
        <div style="min-width:0">
            <p class="m-sheet-name">{{ \Illuminate\Support\Str::limit($mNama, 26) }}</p>
            <p class="m-sheet-sub">Administrator</p>
        </div>
        <a href="{{ route('admin.pengaturan.index') }}" class="toko-pill {{ $mTokoBuka ? 'toko-pill-buka' : 'toko-pill-tutup' }}" style="margin-left:auto">
            <i class="fa-solid {{ $mTokoBuka ? 'fa-store' : 'fa-store-slash' }}"></i>Toko {{ $mTokoBuka ? 'Buka' : 'Tutup' }}
        </a>
    </div>

    <p class="m-sheet-title">Kelola</p>
    <div class="m-grid">
        <a class="m-tile" href="{{ route('admin.ulasan.index') }}"><i class="fa-solid fa-star"></i>Ulasan</a>
        <a class="m-tile" href="{{ route('admin.stok.index') }}"><i class="fa-solid fa-boxes-stacked"></i>Stok</a>
        <a class="m-tile" href="{{ route('admin.kategori.index') }}"><i class="fa-solid fa-tags"></i>Kategori</a>
        <a class="m-tile" href="{{ route('admin.laporan.index') }}"><i class="fa-solid fa-chart-line"></i>Laporan</a>
        <a class="m-tile" href="{{ route('admin.banner.index') }}"><i class="fa-solid fa-images"></i>Banner</a>
        <a class="m-tile" href="{{ route('admin.galeri.index') }}"><i class="fa-solid fa-camera-retro"></i>Galeri</a>
        <a class="m-tile" href="{{ route('admin.pengaturan.index') }}"><i class="fa-solid fa-gear"></i>Pengaturan</a>
    </div>

    <div class="m-list">
        <a href="{{ route('landing') }}" target="_blank" rel="noopener"><i class="fa-solid fa-house-chimney"></i>Cek Landing Page</a>
        <a href="{{ route('galeri') }}" target="_blank" rel="noopener"><i class="fa-solid fa-photo-film"></i>Cek Galeri Publik</a>
        <button type="button" data-pwa-install hidden><i class="fa-solid fa-mobile-screen-button"></i>Pasang Aplikasi Admin di HP</button>
        <hr>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="m-danger"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button>
        </form>
    </div>
</div>

<div class="m-sheet-backdrop"></div>

<div class="m-install" role="dialog" aria-label="Pasang aplikasi admin">
    <img src="{{ asset('icons/admin-192.png') }}" alt="">
    <div class="m-install-text"><strong>Pasang Aplikasi Admin</strong>Pantau pesanan &amp; refund dari layar utama HP.</div>
    <button type="button" class="btn btn-gold btn-sm" data-pwa-install>Pasang</button>
    <button type="button" class="m-install-x" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
</div>
<div class="m-offline" role="status">Tidak ada koneksi internet</div>

<script src="{{ asset('js/mobile.js') }}?v={{ @filemtime(public_path('js/mobile.js')) }}" defer></script>
