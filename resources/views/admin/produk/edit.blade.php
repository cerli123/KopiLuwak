@extends('layouts.admin')
@section('title', 'Edit Produk')
@section('page-title', 'Edit Produk')
@section('content')
    <div class="card form-card">
        <form action="{{ route('admin.produk.update', $produk) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label>Nama Produk <span class="req">*</span></label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required>
                </div>
                <div class="form-group">
                    <label>Kategori <span class="req">*</span></label>
                    <select name="kategori_id" required>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $produk->kategori_id == $kat->id ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Harga (Rp) <span class="req">*</span></label>
                    <input type="number" name="harga" value="{{ old('harga', $produk->harga) }}" min="0" required>
                </div>
                <div class="form-group">
                    <label>Stok Saat Ini <span class="req">*</span></label>
                    <input type="number" name="stok" value="{{ old('stok', $produk->sisa_stok) }}" min="0"
                        required>
                </div>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" rows="4">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                @if ($produk->gambar_url)
                    <div style="margin-bottom:8px;"><img src="{{ $produk->gambar_url }}"
                            style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                        <p class="form-hint">Gambar saat ini. Upload baru untuk mengganti.</p>
                    </div>
                @endif
                <input type="file" name="gambar" accept="image/*">
                <p class="form-hint">JPG/PNG/WebP, maks 2MB</p>
            </div>
            <div class="form-actions">
                <a href="{{ route('admin.produk.index') }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
