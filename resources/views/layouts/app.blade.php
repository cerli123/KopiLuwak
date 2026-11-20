<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kopiluwak Lereng Muria')</title>
    <meta name="description" content="@yield('description', 'Kopi luwak dan kopi premium dari lereng Gunung Muria.')">
    @include('partials.assets')
    @include('partials.mobile-head')
    @stack('styles')
</head>

<body>

    <nav class="navbar">
        <div class="navbar-inner">
            <a href="{{ route('katalog') }}" class="brand">
                <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                <span>Kopiluwak Lereng Muria</span>
            </a>
            <div class="nav-right">
                <a href="{{ route('landing') }}" class="nav-link {{ request()->routeIs('landing') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i><span>Beranda</span>
                </a>
                <a href="{{ route('katalog') }}" class="nav-link {{ request()->routeIs('katalog', 'produk.show') ? 'active' : '' }}">
                    <i class="fa-solid fa-store"></i><span>Katalog</span>
                </a>
                <a href="{{ route('galeri') }}" class="nav-link {{ request()->routeIs('galeri') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i><span>Galeri</span>
                </a>
                <a href="{{ route('pesanan.index') }}"
                    class="nav-link {{ request()->routeIs('pesanan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-receipt"></i><span>Pesanan Saya</span>
                </a>
                <a href="{{ route('keranjang') }}" class="nav-cart" aria-label="Keranjang">
                    <i class="fa-solid fa-cart-shopping"></i>
                    @php $cartCount = \App\Models\Keranjang::where('user_id', auth()->id())->count(); @endphp
                    @if ($cartCount > 0)
                        <span class="cart-badge">{{ $cartCount }}</span>
                    @endif
                </a>
                <div class="user-menu" id="userMenu">
                    <button class="user-btn" type="button" aria-haspopup="true" aria-expanded="false"
                        aria-label="Menu akun">
                        <span class="user-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</span>
                        <span
                            class="user-short">{{ \Illuminate\Support\Str::before(auth()->user()->nama, ' ') }}</span>
                        <i class="fa-solid fa-chevron-down user-caret"></i>
                    </button>
                    <div class="user-dropdown">
                        <div class="user-dropdown-inner">
                            <a href="{{ route('profil') }}" class="user-info">
                                <p class="user-name">{{ auth()->user()->nama }}</p>
                                <p class="user-email">{{ auth()->user()->email }}</p>
                            </a>
                            <a href="{{ route('profil') }}" class="dd-item">
                                <i class="fa-solid fa-user-pen"></i>Profil Saya
                            </a>
                            <a href="{{ route('pesanan.index') }}" class="dd-item">
                                <i class="fa-solid fa-receipt"></i>Pesanan Saya
                            </a>
                            <a href="{{ route('refund.index') }}" class="dd-item">
                                <i class="fa-solid fa-rotate-left"></i>Refund Saya
                            </a>
                            <hr class="dd-divider">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dd-item dd-danger">
                                    <i class="fa-solid fa-right-from-bracket"></i>Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main>
        @unless (\App\Models\Pengaturan::tokoBuka())
            <div class="toko-banner">
                <i class="fa-solid fa-store-slash"></i>
                <span><strong>Toko sedang tutup.</strong> {{ \App\Models\Pengaturan::pesanTutup() }}
                    @if (\App\Models\Pengaturan::infoBukaKembali())
                        Buka kembali: {{ \App\Models\Pengaturan::infoBukaKembali() }}.
                    @endif
                </span>
            </div>
        @endunless
        @if (session('success'))
            <div class="toast toast-success" id="toast-msg">
                <i class="fa-solid fa-circle-check"></i>{{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="toast toast-error" id="toast-msg">
                <i class="fa-solid fa-circle-xmark"></i>{{ session('error') }}
            </div>
        @elseif(session('info'))
            <div class="toast toast-info" id="toast-msg">
                <i class="fa-solid fa-circle-info"></i>{{ session('info') }}
            </div>
        @endif
        @yield('content')
    </main>

    @include('partials.footer', ['footerClass' => 'in-app'])

    @include('partials.mobile-nav-user')

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
