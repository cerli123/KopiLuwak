@php $kontak = \App\Models\Pengaturan::kontak(); @endphp
<footer class="lp-footer {{ $footerClass ?? '' }}">
    <div class="lp-footer-inner">
        <div>
            <a href="{{ route('landing') }}" class="brand">
                <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                <span>Kopiluwak<br>Lereng Muria</span>
            </a>
            <p>Kopi luwak Lereng Muria</p>
        </div>
        <div>
            <h4>Jelajahi</h4>
            <ul>
                <li><a href="{{ route('landing') }}#beranda"><i class="fa-solid fa-house"></i>Beranda</a></li>
                <li><a href="{{ route('landing') }}#tentang"><i class="fa-solid fa-circle-info"></i>Tentang Kami</a></li>
                <li><a href="{{ route('landing') }}#produk"><i class="fa-solid fa-mug-hot"></i>Produk</a></li>
                <li><a href="{{ route('landing') }}#proses"><i class="fa-solid fa-seedling"></i>Proses</a></li>
                <li><a href="{{ route('galeri') }}"><i class="fa-solid fa-images"></i>Galeri</a></li>
            </ul>
        </div>
        <div>
            <h4>Akun</h4>
            <ul>
                @guest
                    <li><a href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket"></i>Masuk</a></li>
                    <li><a href="{{ route('register') }}"><i class="fa-solid fa-user-plus"></i>Daftar</a></li>
                @else
                    @if (auth()->user()->role === 'admin')
                        <li><a href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-gauge-high"></i>Dashboard</a></li>
                    @else
                        <li><a href="{{ route('katalog') }}"><i class="fa-solid fa-store"></i>Katalog</a></li>
                        <li><a href="{{ route('profil') }}"><i class="fa-solid fa-user-pen"></i>Profil Saya</a></li>
                        <li><a href="{{ route('pesanan.index') }}"><i class="fa-solid fa-receipt"></i>Pesanan Saya</a></li>
                    @endif
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i>Keluar</button>
                        </form>
                    </li>
                @endguest
            </ul>
        </div>
        @if ($kontak['wa'] || $kontak['email'] || $kontak['alamat'] || $kontak['jam'])
            <div>
                <h4>Hubungi Kami</h4>
                <ul>
                    @if ($kontak['wa_url'])
                        <li><a href="{{ $kontak['wa_url'] }}" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp"></i>+{{ $kontak['wa'] }}</a></li>
                    @endif
                    @if ($kontak['email'])
                        <li><a href="mailto:{{ $kontak['email'] }}"><i class="fa-solid fa-envelope"></i>{{ $kontak['email'] }}</a></li>
                    @endif
                    @if ($kontak['alamat'])
                        <li class="footer-info"><i class="fa-solid fa-location-dot"></i><span>{{ $kontak['alamat'] }}</span></li>
                    @endif
                    @if ($kontak['jam'])
                        <li class="footer-info"><i class="fa-regular fa-clock"></i><span>{{ $kontak['jam'] }}</span></li>
                    @endif
                </ul>
            </div>
        @endif
    </div>
    <div class="lp-footer-bottom">
        &copy; {{ date('Y') }} Kopiluwak Lereng Muria — Kopi Premium dari Lereng Muria
    </div>
</footer>

@if ($kontak['wa_url'] && ! (auth()->check() && auth()->user()->role === 'admin'))
    <a href="{{ $kontak['wa_url'] }}?text={{ rawurlencode('Halo Kopiluwak Lereng Muria, saya ingin bertanya tentang produk.') }}"
        class="wa-float" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
@endif
