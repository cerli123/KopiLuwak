<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Masuk — Kopiluwak Lereng Muria</title>
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
                <h2>Kopi premium langsung dari <span>lereng Gunung Muria</span></h2>
                <p>Masuk untuk melanjutkan belanja, memantau pesanan, dan menikmati biji kopi pilihan kami.</p>
                <ul class="auth-points">
                    <li><i class="fa-solid fa-seedling"></i>Biji kopi pilihan berkualitas</li>
                    <li><i class="fa-solid fa-mountain"></i>Dipanen langsung dari lereng Muria</li>
                    <li><i class="fa-solid fa-shield-halved"></i>Rasa autentik, kualitas terjamin</li>
                </ul>
            </div>
            <p class="auth-aside-foot">&copy; {{ date('Y') }} Kopiluwak Lereng Muria</p>
        </aside>

        <main class="auth-main">
            <div class="auth-card">
                <a href="{{ route('landing') }}" class="auth-mobile-brand brand">
                    <img src="{{ $siteLogo }}" alt="Logo Kopiluwak" class="brand-logo">
                    <span>Kopiluwak Lereng Muria</span>
                </a>

                <div class="auth-heading">
                    <h1 class="auth-title">Selamat Datang Kembali</h1>
                    <p class="auth-sub">Masuk ke akun Anda untuk melanjutkan.</p>
                </div>

                @if (session('info'))
                    <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i>{{ session('info') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first() }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i>{{ session('error') }}</div>
                @endif

                @include('partials.google-button', ['label' => 'Masuk dengan Google'])

                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-icon">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="email@contoh.com" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-icon has-toggle">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                            <button type="button" class="input-toggle" data-toggle-password="password"
                                aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-full">
                        <i class="fa-solid fa-right-to-bracket"></i>Masuk
                    </button>
                </form>

                <p class="auth-link" style="margin-top:22px;">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>

                <a href="{{ route('landing') }}" class="auth-back">
                    <i class="fa-solid fa-arrow-left"></i>Kembali ke beranda
                </a>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
