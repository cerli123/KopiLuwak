@extends('layouts.admin')
@section('title', 'Tambah Produk')
@section('page-title', 'Tambah Produk Baru')
@section('content')
    <div class="card form-card">
        <form action="{{ route('admin.produk.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Produk <span class="req">*</span></label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk') }}" required>
                </div>
                <div class="form-group">
                    <label>Kategori <span class="req">*</span></label>
                    <select name="kategori_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Harga (Rp) <span class="req">*</span></label>
                    <input type="number" name="harga" value="{{ old('harga') }}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Stok Awal <span class="req">*</span></label>
                    <input type="number" name="stok_awal" value="{{ old('stok_awal', 0) }}" min="0" required>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" accept="image/*" id="gambar-input" onchange="previewImg(this)">
                <p class="form-hint">JPG/PNG/WebP, maks 2MB</p>
                <img id="gambar-preview" alt="Preview"
                    style="display:none;width:120px;height:120px;object-fit:cover;border-radius:8px;margin-top:8px;">
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
    @push('scripts')
        <script>
            function previewImg(input) {
                const preview = document.getElementById('gambar-preview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection
