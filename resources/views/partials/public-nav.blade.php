<nav class="lp-nav" id="lpNav">
    <div class="lp-nav-inner">
        <a href="{{ route('landing') }}" class="brand">
            <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
            <span>Kopiluwak<br>Lereng Muria</span>
        </a>
        <div class="lp-menu" id="lpMenu">
            <a href="{{ route('landing') }}#beranda">Beranda</a>
            <a href="{{ route('landing') }}#tentang">Tentang</a>
            <a href="{{ route('landing') }}#produk">Produk</a>
            <a href="{{ route('landing') }}#proses">Proses</a>
            <a href="{{ route('galeri') }}" class="{{ request()->routeIs('galeri') ? 'active' : '' }}">Galeri</a>
            <div class="lp-actions">
                @unless (\App\Models\Pengaturan::tokoBuka())
                    <span class="toko-pill toko-pill-tutup"><i class="fa-solid fa-store-slash"></i>Toko Tutup</span>
                @endunless
                @guest
                    <a href="{{ route('login') }}" class="btn btn-light-outline btn-sm">
                        <i class="fa-solid fa-right-to-bracket"></i>Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-gold btn-sm">
                        <i class="fa-solid fa-user-plus"></i>Daftar
                    </a>
                @else
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-gold btn-sm">
                            <i class="fa-solid fa-gauge-high"></i>Kembali ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('katalog') }}" class="btn btn-gold btn-sm">
                            <i class="fa-solid fa-store"></i>Ke Katalog
                        </a>
                    @endif
                @endguest
            </div>
        </div>
        <button type="button" class="lp-toggle" id="lpToggle" aria-label="Menu">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>
</nav>
