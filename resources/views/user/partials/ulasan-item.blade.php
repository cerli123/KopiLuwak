{{-- Satu kartu ulasan. Variabel: $u (Ulasan), $bolehUbah (bool) --}}
@php $bolehUbah = $bolehUbah ?? false; @endphp
<article class="ulasan-item">
    <div class="ulasan-head">
        <div class="ulasan-avatar">
            @if ($u->user?->avatar)
                <img src="{{ $u->user->avatar }}" alt="" referrerpolicy="no-referrer" loading="lazy">
            @else
                {{ strtoupper(mb_substr($u->user?->nama ?? 'P', 0, 1)) }}
            @endif
        </div>
        <div class="ulasan-meta">
            <p class="ulasan-nama">{{ $u->nama_tampil }}
                @if ($bolehUbah)<span class="badge badge-info">Ulasan Anda</span>@endif
                @unless ($u->tampil)<span class="badge badge-warning">Belum tampil</span>@endunless
            </p>
            <p class="ulasan-tgl">
                @include('partials.bintang', ['nilai' => $u->rating, 'kecil' => true])
                <span>· {{ $u->created_at->translatedFormat('d M Y') }}</span>
            </p>
        </div>
    </div>

    @if ($u->komentar)
        <p class="ulasan-isi">{{ $u->komentar }}</p>
    @endif

    @if ($u->balasan_admin)
        <div class="ulasan-balasan">
            <p><i class="fa-solid fa-store"></i> <strong>Balasan Toko</strong></p>
            <p>{{ $u->balasan_admin }}</p>
        </div>
    @endif

    @if ($bolehUbah)
        <details class="ulasan-ubah" @if (old('_form') === 'ulasan_'.$u->pesanan_id.'_'.$u->produk_id) open @endif>
            <summary><i class="fa-solid fa-pen"></i> Ubah / hapus ulasan</summary>
            @include('user.partials.ulasan-form', [
                'action' => route('ulasan.update', $u),
                'method' => 'PUT',
                'key'    => 'ulasan_'.$u->pesanan_id.'_'.$u->produk_id,
                'ulasan' => $u,
                'tombol' => 'Simpan Perubahan',
            ])
            <form action="{{ route('ulasan.destroy', $u) }}" method="POST" style="margin-top:8px;"
                onsubmit="return confirm('Hapus ulasan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--red-700);border-color:var(--red-700);">
                    <i class="fa-solid fa-trash"></i>Hapus Ulasan
                </button>
            </form>
        </details>
    @endif
</article>
