@php
    $bag     = $errors->getBag($key);
    $aktif   = old('_form') === $key;
    $nilai   = $aktif ? (int) old('rating') : (int) ($ulasan->rating ?? 0);
    $isi     = $aktif ? old('komentar') : ($ulasan->komentar ?? '');
    $maks    = (int) config('toko.ulasan.maks_karakter', 1000);
@endphp
<form action="{{ $action }}" method="POST" class="ulasan-form">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <input type="hidden" name="_form" value="{{ $key }}">

    <div class="form-group" style="margin-bottom:10px;">
        <label style="margin-bottom:4px;">Rating Anda <span class="req">*</span></label>
        <div class="star-input" role="radiogroup" aria-label="Pilih rating">
            @for ($i = 5; $i >= 1; $i--)
                <input type="radio" name="rating" id="rt-{{ $key }}-{{ $i }}" value="{{ $i }}" @checked($nilai === $i) required>
                <label for="rt-{{ $key }}-{{ $i }}" title="{{ $i }} bintang"><i class="fa-solid fa-star"></i></label>
            @endfor
        </div>
        @if ($bag->has('rating'))
            <p class="form-error">{{ $bag->first('rating') }}</p>
        @endif
    </div>

    <div class="form-group" style="margin-bottom:10px;">
        <label for="km-{{ $key }}">Ulasan (opsional)</label>
        <textarea id="km-{{ $key }}" name="komentar" rows="3" maxlength="{{ $maks }}"
            placeholder="Ceritakan rasa, aroma, kemasan, atau pengalaman Anda...">{{ $isi }}</textarea>
        @if ($bag->has('komentar'))
            <p class="form-error">{{ $bag->first('komentar') }}</p>
        @endif
    </div>

    <button type="submit" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-paper-plane"></i>{{ $tombol ?? 'Kirim Ulasan' }}
    </button>
</form>
