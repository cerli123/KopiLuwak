<div class="form-group">
    <label for="judul">Judul <span class="req">*</span></label>
    <input type="text" id="judul" name="judul" value="{{ old('judul', $banner->judul ?? '') }}" maxlength="120" required>
</div>
<div class="form-group">
    <label for="deskripsi">Deskripsi</label>
    <input type="text" id="deskripsi" name="deskripsi" value="{{ old('deskripsi', $banner->deskripsi ?? '') }}"
        maxlength="255" placeholder="Kalimat singkat di bawah judul">
</div>
<div class="form-row">
    <div class="form-group">
        <label for="tombol_teks">Teks Tombol</label>
        <input type="text" id="tombol_teks" name="tombol_teks" value="{{ old('tombol_teks', $banner->tombol_teks ?? '') }}"
            maxlength="40" placeholder="Contoh: Belanja Sekarang">
    </div>
    <div class="form-group">
        <label for="tombol_url">Link Tombol</label>
        <input type="text" id="tombol_url" name="tombol_url" value="{{ old('tombol_url', $banner->tombol_url ?? '') }}"
            placeholder="/register atau https://...">
    </div>
</div>
<div class="form-row">
    <div class="form-group">
        <label for="urutan">Urutan Tampil</label>
        <input type="number" id="urutan" name="urutan" min="0" value="{{ old('urutan', $banner->urutan ?? 0) }}">
        <p class="form-hint">Angka kecil tampil lebih dulu.</p>
    </div>
    <div class="form-group">
        <label>Status</label>
        <label class="form-check" style="margin-top:10px;">
            <input type="checkbox" name="aktif" value="1" {{ old('aktif', $banner->aktif ?? true) ? 'checked' : '' }}>
            Tampilkan di landing page
        </label>
    </div>
</div>
<div class="form-group">
    <label for="gambar">Gambar Banner @unless (isset($banner))<span class="req">*</span>@endunless</label>
    <input type="file" id="gambar" name="gambar" accept="image/*" data-preview="#banner-preview"
        @unless (isset($banner)) required @endunless>
    <p class="form-hint">JPG/PNG/WebP, maks 4MB. Ukuran disarankan 1600 x 600 px.</p>
    <img id="banner-preview" class="banner-preview" alt="Pratinjau"
        @if (isset($banner)) src="{{ \App\Support\Media::url($banner->gambar) }}" @else style="display:none;" @endif>
</div>
