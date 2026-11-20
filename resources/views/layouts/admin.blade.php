<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Admin Kopiluwak</title>
    @include('partials.assets')
    @include('partials.mobile-head')
    @stack('styles')
</head>

<body class="admin-body">
    @php
        $badgePesanan = \App\Models\Pesanan::where('status', 'menunggu_konfirmasi')->count();
        $badgeRefund  = \App\Models\Refund::where('status', 'diajukan')->count();
        $tokoBukaNav  = \App\Models\Pengaturan::tokoBuka();
    @endphp
    <div class="admin-shell">

        <aside class="sidebar" id="sidebar" aria-label="Menu admin">
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="brand">
                    <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                    <span>Kopiluwak<small>Panel Admin</small></span>
                </a>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                </a>

                <p class="nav-section">Penjualan</p>
                <a href="{{ route('admin.pesanan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.pesanan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-bag-shopping"></i><span>Pesanan</span>
                    @if ($badgePesanan > 0)
                        <span class="nav-badge">{{ $badgePesanan }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.refund.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.refund*') ? 'active' : '' }}">
                    <i class="fa-solid fa-rotate-left"></i><span>Refund</span>
                    @if ($badgeRefund > 0)
                        <span class="nav-badge">{{ $badgeRefund }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.ulasan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.ulasan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-star"></i><span>Ulasan</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.laporan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-chart-line"></i><span>Laporan</span>
                </a>

                <p class="nav-section">Katalog</p>
                <a href="{{ route('admin.produk.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.produk*') ? 'active' : '' }}">
                    <i class="fa-solid fa-mug-hot"></i><span>Produk</span>
                </a>
                <a href="{{ route('admin.stok.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.stok*') ? 'active' : '' }}">
                    <i class="fa-solid fa-boxes-stacked"></i><span>Stok</span>
                </a>
                <a href="{{ route('admin.kategori.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                    <i class="fa-solid fa-tags"></i><span>Kategori</span>
                </a>

                <p class="nav-section">Konten Website</p>
                <a href="{{ route('admin.banner.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.banner*') ? 'active' : '' }}">
                    <i class="fa-solid fa-images"></i><span>Banner Carousel</span>
                </a>
                <a href="{{ route('admin.galeri.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.galeri*') ? 'active' : '' }}">
                    <i class="fa-solid fa-camera-retro"></i><span>Galeri Foto</span>
                </a>
                <a href="{{ route('admin.pengaturan.index') }}"
                    class="sidebar-link {{ request()->routeIs('admin.pengaturan*') ? 'active' : '' }}">
                    <i class="fa-solid fa-gear"></i><span>Pengaturan</span>
                </a>

                <p class="nav-section">Lihat Website</p>
                <a href="{{ route('landing') }}" target="_blank" rel="noopener" class="sidebar-link">
                    <i class="fa-solid fa-house-chimney"></i><span>Cek Landing Page</span>
                    <i class="fa-solid fa-arrow-up-right-from-square nav-ext"></i>
                </a>
                <a href="{{ route('galeri') }}" target="_blank" rel="noopener" class="sidebar-link">
                    <i class="fa-solid fa-photo-film"></i><span>Cek Galeri Publik</span>
                    <i class="fa-solid fa-arrow-up-right-from-square nav-ext"></i>
                </a>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->nama, 0, 2)) }}</div>
                    <div>
                        <p class="sidebar-uname">{{ \Illuminate\Support\Str::limit(auth()->user()->nama, 22) }}</p>
                        <p class="sidebar-role">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>Keluar
                    </button>
                </form>
            </div>
        </aside>
        <div class="sidebar-backdrop" onclick="document.getElementById('sidebar').classList.remove('open')"></div>

        <div class="admin-main">
            <header class="admin-topbar">
                <button class="menu-toggle" type="button" aria-label="Buka menu"
                    onclick="document.getElementById('sidebar').classList.toggle('open')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h1 class="topbar-title">@yield('page-title', 'Dashboard')</h1>
                <div class="topbar-actions">
                    <a href="{{ route('landing') }}" target="_blank" rel="noopener"
                        class="btn btn-outline btn-sm topbar-landing">
                        <i class="fa-solid fa-eye"></i><span>Cek Landing Page</span>
                    </a>
                    <a href="{{ route('admin.pengaturan.index') }}"
                        class="toko-pill {{ $tokoBukaNav ? 'toko-pill-buka' : 'toko-pill-tutup' }}">
                        <i class="fa-solid {{ $tokoBukaNav ? 'fa-store' : 'fa-store-slash' }}"></i>
                        Toko {{ $tokoBukaNav ? 'Buka' : 'Tutup' }}
                    </a>
                    <span class="topbar-date"><i class="fa-regular fa-calendar"></i>{{ now()->format('d M Y') }}</span>
                </div>
            </header>
            <div class="admin-content">
                @if (session('success'))
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-xmark"></i>{{ session('error') }}</div>
                @endif
                @if (session('info'))
                    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i>{{ session('info') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    @include('partials.mobile-nav-admin')

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>

</html>
