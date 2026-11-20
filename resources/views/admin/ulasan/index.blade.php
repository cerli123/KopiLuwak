@extends('layouts.admin')
@section('title', 'Ulasan')
@section('page-title', 'Ulasan Produk')
@section('content')
    <div class="stats-grid" style="margin-bottom:16px;">
        <div class="stat-card"><p class="stat-label">Total Ulasan</p><p class="stat-value">{{ $ringkas['total'] }}</p></div>
        <div class="stat-card">
            <p class="stat-label">Rata-rata Rating</p>
            <p class="stat-value">{{ $ringkas['rata_rata'] > 0 ? number_format($ringkas['rata_rata'], 1, ',', '.') : '-' }} <i class="fa-solid fa-star" style="color:var(--gold);font-size:18px;"></i></p>
        </div>
        <div class="stat-card"><p class="stat-label">Belum Dibalas</p><p class="stat-value">{{ $ringkas['belum_dibalas'] }}</p></div>
        <div class="stat-card"><p class="stat-label">Disembunyikan</p><p class="stat-value">{{ $ringkas['disembunyikan'] }}</p></div>
    </div>

    <div class="toolbar" style="margin-bottom:16px;">
        <form action="{{ route('admin.ulasan.index') }}" method="GET" class="toolbar-left" style="flex-wrap:wrap;gap:8px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk / pelanggan / isi ulasan...">
            <select name="rating">
                <option value="">Semua rating</option>
                @for ($i = 5; $i >= 1; $i--)
                    <option value="{{ $i }}" @selected((string) request('rating') === (string) $i)>{{ $i }} bintang</option>
                @endfor
            </select>
            <select name="status">
                <option value="">Semua status</option>
                <option value="tampil" @selected(request('status') === 'tampil')>Tampil</option>
                <option value="disembunyikan" @selected(request('status') === 'disembunyikan')>Disembunyikan</option>
                <option value="belum_dibalas" @selected(request('status') === 'belum_dibalas')>Belum dibalas</option>
            </select>
            <button type="submit" class="btn btn-outline">Terapkan</button>
            @if (request()->hasAny(['q', 'rating', 'status']))
                <a href="{{ route('admin.ulasan.index') }}" class="btn btn-outline">Reset</a>
            @endif
        </form>
    </div>

    @forelse ($ulasans as $u)
        <div class="card ulasan-admin" style="margin-bottom:12px;">
            <div class="ulasan-admin-top">
                <div>
                    <p class="fw-600">{{ $u->produk?->nama_produk ?? 'Produk dihapus' }}</p>
                    <p style="font-size:12.5px;color:var(--gray-500);">
                        {{ $u->user?->nama ?? 'Pengguna dihapus' }} · {{ $u->pesanan?->kode_pesanan }} · {{ $u->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div style="text-align:right;">
                    @include('partials.bintang', ['nilai' => $u->rating, 'kecil' => true])
                    <p style="margin-top:4px;">
                        <span class="badge badge-{{ $u->tampil ? 'success' : 'secondary' }}">{{ $u->tampil ? 'Tampil' : 'Disembunyikan' }}</span>
                    </p>
                </div>
            </div>

            @if ($u->komentar)
                <p class="ulasan-isi" style="margin:10px 0;">{{ $u->komentar }}</p>
            @else
                <p class="form-hint" style="margin:10px 0;"><em>Pelanggan hanya memberi rating.</em></p>
            @endif

            @php $bag = $errors->getBag('balas_' . $u->id); @endphp
            <details class="ulasan-ubah" @if ($u->balasan_admin || $bag->any()) open @endif>
                <summary><i class="fa-solid fa-reply"></i> {{ $u->balasan_admin ? 'Balasan Toko' : 'Balas ulasan' }}</summary>
                <form action="{{ route('admin.ulasan.balas', $u) }}" method="POST" style="margin-top:8px;">
                    @csrf @method('PATCH')
                    <textarea name="balasan_admin" rows="2" maxlength="1000" placeholder="Tulis balasan untuk pelanggan...">{{ old('balasan_admin', $u->balasan_admin) }}</textarea>
                    @if ($bag->has('balasan_admin'))<p class="form-error">{{ $bag->first('balasan_admin') }}</p>@endif
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:8px;">Simpan Balasan</button>
                </form>
            </details>

            <div class="ulasan-admin-aksi">
                <form action="{{ route('admin.ulasan.tampil', $u) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-outline btn-xs">
                        <i class="fa-solid {{ $u->tampil ? 'fa-eye-slash' : 'fa-eye' }}"></i> {{ $u->tampil ? 'Sembunyikan' : 'Tampilkan' }}
                    </button>
                </form>
                <form action="{{ route('admin.ulasan.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline btn-xs" style="color:var(--red-700);border-color:var(--red-700);"><i class="fa-solid fa-trash"></i> Hapus</button>
                </form>
            </div>
        </div>
    @empty
        <div class="card text-center" style="padding:32px;">Belum ada ulasan.</div>
    @endforelse

    <div class="pagination-wrap">{{ $ulasans->links() }}</div>
@endsection
