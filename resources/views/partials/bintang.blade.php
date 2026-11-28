@php
    $nilai  = (float) ($nilai ?? 0);
    $kecil  = $kecil ?? false;
@endphp
<span class="bintang {{ $kecil ? 'bintang-kecil' : '' }}" role="img" aria-label="Rating {{ number_format($nilai, 1) }} dari 5">
    @for ($i = 1; $i <= 5; $i++)
        @if ($nilai >= $i - 0.25)
            <i class="fa-solid fa-star"></i>
        @elseif ($nilai >= $i - 0.75)
            <i class="fa-solid fa-star-half-stroke"></i>
        @else
            <i class="fa-regular fa-star"></i>
        @endif
    @endfor
    @isset($jumlah)
        <span class="bintang-teks">{{ $nilai > 0 ? number_format($nilai, 1, ',', '.') : '' }} <span>({{ $jumlah }})</span></span>
    @endisset
</span>
