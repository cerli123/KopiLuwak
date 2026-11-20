@extends('layouts.app')
@section('title', 'Profil Saya')
@section('content')
    <div class="page-wrap">
        <p class="section-label"><i class="fa-solid fa-user"></i>Akun</p>
        <h2 class="page-title">Profil Saya</h2>

        <div class="profile-grid">
            <aside class="card profile-summary">
                <div class="profile-avatar">
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="" referrerpolicy="no-referrer" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                    @else
                        {{ strtoupper(substr($user->nama, 0, 2)) }}
                    @endif
                </div>
                <h3 class="profile-name">{{ $user->nama }}</h3>
                <p class="profile-email">{{ $user->email }}</p>
                <div class="profile-stats">
                    <div><strong>{{ $totalPesanan }}</strong><span>Pesanan</span></div>
                    <div><strong>{{ $user->created_at->format('M Y') }}</strong><span>Bergabung</span></div>
                </div>
                <a href="{{ route('pesanan.index') }}" class="btn btn-outline btn-full btn-sm">
                    <i class="fa-solid fa-receipt"></i>Lihat Pesanan
                </a>
            </aside>

            <div class="profile-forms">
                <div class="card">
                    <p class="card-title"><i class="fa-solid fa-id-card"></i> Data Diri</p>
                    <form action="{{ route('profil.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span class="req">*</span></label>
                            <div class="input-icon">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                            </div>
                            @error('nama')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email <span class="req">*</span></label>
                                <div class="input-icon">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label for="no_hp">No. HP</label>
                                <div class="input-icon">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                                        placeholder="08xxxxxxxxxx">
                                </div>
                                @error('no_hp')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <div class="input-icon textarea">
                                <i class="fa-solid fa-location-dot"></i>
                                <textarea id="alamat" name="alamat" rows="3" placeholder="Alamat lengkap">{{ old('alamat', $user->alamat) }}</textarea>
                            </div>
                            @error('alamat')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <p class="card-title"><i class="fa-solid fa-lock"></i> {{ $user->punya_password ? 'Ganti Password' : 'Buat Password' }}</p>
                    @unless ($user->punya_password)
                        <p class="form-hint" style="margin-bottom:12px;"><i class="fa-brands fa-google"></i> Akun Anda masuk lewat Google. Password bersifat opsional, buat bila ingin bisa masuk dengan email &amp; password juga.</p>
                    @endunless
                    <form action="{{ route('profil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        @if ($user->punya_password)
                        <div class="form-group">
                            <label for="current_password">Password Lama <span class="req">*</span></label>
                            <div class="input-icon has-toggle">
                                <i class="fa-solid fa-key"></i>
                                <input type="password" id="current_password" name="current_password" required>
                                <button type="button" class="input-toggle" data-toggle-password="current_password"
                                    aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                            </div>
                            @error('current_password')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        @endif
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password Baru <span class="req">*</span></label>
                                <div class="input-icon has-toggle">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                                    <button type="button" class="input-toggle" data-toggle-password="password"
                                        aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                                </div>
                                @error('password')<p class="form-error">{{ $message }}</p>@enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Password <span class="req">*</span></label>
                                <div class="input-icon has-toggle">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                                    <button type="button" class="input-toggle" data-toggle-password="password_confirmation"
                                        aria-label="Tampilkan password"><i class="fa-solid fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-shield-halved"></i>Ubah Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
