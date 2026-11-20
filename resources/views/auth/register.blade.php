<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Daftar — Kopiluwak Lereng Muria</title>
    @include('partials.assets')
    @include('partials.mobile-head')
</head>

<body class="auth-body">
    <div class="auth-shell">
        <aside class="auth-aside">
            <a href="{{ route('landing') }}" class="brand">
                <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                <span>Kopiluwak<br>Lereng Muria</span>
            </a>
            <div class="auth-aside-body">
                <h2>Bergabung dan nikmati <span>kopi terbaik</span> dari Muria</h2>
                <p>Buat akun gratis untuk mulai memesan, menyimpan keranjang, dan melacak setiap pesanan Anda.</p>
                <ul class="auth-points">
                    <li><i class="fa-solid fa-user-check"></i>Pendaftaran gratis dan cepat</li>
                    <li><i class="fa-solid fa-cart-shopping"></i>Belanja mudah lewat keranjang</li>
                    <li><i class="fa-solid fa-wallet"></i>Pilihan transfer bank dan dompet digital</li>
                </ul>
            </div>
            <p class="auth-aside-foot">&copy; {{ date('Y') }} Kopiluwak Lereng Muria</p>
        </aside>

        <main class="auth-main">
            <div class="auth-card auth-card-wide">
                <a href="{{ route('landing') }}" class="auth-mobile-brand brand">
                    <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                    <span>Kopiluwak Lereng Muria</span>
                </a>

                <div class="auth-heading">
                    <h1 class="auth-title">Buat Akun Baru</h1>
                    <p class="auth-sub">Daftar gratis dan mulai belanja kopi pilihan.</p>
                </div>

                @if (session('info'))
                    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i>{{ session('info') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <ul>
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @include('partials.google-button', ['label' => 'Daftar dengan Google'])

                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="nama" name="nama" value="{{ old('nama') }}"
                                placeholder="Nama lengkap" required autofocus>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-icon">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                    placeholder="email@contoh.com" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no_hp">No. HP</label>
                            <div class="input-icon">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <div class="input-icon textarea">
                            <i class="fa-solid fa-location-dot"></i>
                            <textarea id="alamat" name="alamat" rows="2" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-icon has-toggle">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter"
                                    required>
                                <button type="button" class="input-toggle" data-toggle-password="password"
                                    aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <div class="input-icon has-toggle">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Ulangi password" required>
                                <button type="button" class="input-toggle" data-toggle-password="password_confirmation"
                                    aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-full">
                        <i class="fa-solid fa-user-plus"></i>Daftar Sekarang
                    </button>
                </form>

                <p class="auth-link" style="margin-top:22px;">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>

                <a href="{{ route('landing') }}" class="auth-back">
                    <i class="fa-solid fa-arrow-left"></i>Kembali ke beranda
                </a>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
