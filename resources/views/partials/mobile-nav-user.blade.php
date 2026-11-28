@if (auth()->check() && auth()->user()->role === 'admin')
    @include('partials.mobile-nav-admin')
@else
@php
    $mCart = auth()->check() ? \App\Models\Keranjang::where('user_id', auth()->id())->count() : 0;
    $mNama = auth()->check() ? auth()->user()->nama : '';
@endphp

<nav class="m-nav" aria-label="Menu utama">
    @guest
        <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'is-active' : '' }}">
            <i class="fa-solid fa-house"></i><span class="m-lbl">Beranda</span>
        </a>
        <a href="{{ route('landing') }}#produk">
            <i class="fa-solid fa-mug-hot"></i><span class="m-lbl">Produk</span>
        </a>
        <a href="{{ route('galeri') }}" class="{{ request()->routeIs('galeri') ? 'is-active' : '' }}">
            <i class="fa-solid fa-images"></i><span class="m-lbl">Galeri</span>
        </a>
        <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">
            <i class="fa-solid fa-right-to-bracket"></i><span class="m-lbl">Masuk</span>
        </a>
        <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'is-active' : '' }}">
            <i class="fa-solid fa-user-plus"></i><span class="m-lbl">Daftar</span>
        </a>
    @else
        <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'is-active' : '' }}">
            <i class="fa-solid fa-house"></i><span class="m-lbl">Beranda</span>
        </a>
        <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog', 'produk.show') ? 'is-active' : '' }}">
            <i class="fa-solid fa-store"></i><span class="m-lbl">Katalog</span>
        </a>
        <a href="{{ route('keranjang') }}" class="{{ request()->routeIs('keranjang', 'checkout') ? 'is-active' : '' }}">
            <i class="fa-solid fa-cart-shopping"></i><span class="m-lbl">Keranjang</span>
            @if ($mCart > 0)
                <span class="m-badge gold">{{ $mCart }}</span>
            @endif
        </a>
        <a href="{{ route('pesanan.index') }}" class="{{ request()->routeIs('pesanan*', 'pembayaran*') ? 'is-active' : '' }}">
            <i class="fa-solid fa-receipt"></i><span class="m-lbl">Pesanan</span>
        </a>
        <button type="button" data-sheet-open="m-sheet-akun" class="{{ request()->routeIs('profil*', 'refund*', 'galeri') ? 'is-active' : '' }}">
            <i class="fa-solid fa-user"></i><span class="m-lbl">Akun</span>
        </button>
    @endguest
</nav>

@auth
    <div class="m-sheet" id="m-sheet-akun" role="dialog" aria-label="Menu akun">
        <div class="m-sheet-handle"></div>
        <div class="m-sheet-user">
            <div class="m-sheet-avatar">{{ strtoupper(substr($mNama, 0, 2)) }}</div>
            <div style="min-width:0">
                <p class="m-sheet-name">{{ $mNama }}</p>
                <p class="m-sheet-sub">{{ auth()->user()->email }}</p>
            </div>
        </div>
        <div class="m-list">
            <a href="{{ route('profil') }}"><i class="fa-solid fa-user-pen"></i>Profil Saya</a>
            <a href="{{ route('pesanan.index') }}"><i class="fa-solid fa-receipt"></i>Riwayat Pesanan</a>
            <a href="{{ route('refund.index') }}"><i class="fa-solid fa-rotate-left"></i>Refund Saya</a>
            <a href="{{ route('galeri') }}"><i class="fa-solid fa-images"></i>Galeri Foto</a>
            <button type="button" data-pwa-install hidden><i class="fa-solid fa-mobile-screen-button"></i>Pasang Aplikasi di HP</button>
            <hr>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="m-danger"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button>
            </form>
        </div>
    </div>
@endauth

<div class="m-sheet-backdrop"></div>

<div class="m-install" role="dialog" aria-label="Pasang aplikasi">
    <img src="{{ asset('icons/icon-192.png') }}" alt="">
    <div class="m-install-text"><strong>Pasang Aplikasi Kopiluwak</strong>Belanja lebih cepat langsung dari layar utama HP.</div>
    <button type="button" class="btn btn-gold btn-sm" data-pwa-install>Pasang</button>
    <button type="button" class="m-install-x" aria-label="Tutup"><i class="fa-solid fa-xmark"></i></button>
</div>
<div class="m-offline" role="status">Tidak ada koneksi internet</div>

<script src="{{ asset('js/mobile.js') }}?v={{ @filemtime(public_path('js/mobile.js')) }}" defer></script>
@endif
