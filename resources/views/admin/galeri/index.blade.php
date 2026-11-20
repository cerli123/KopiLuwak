@extends('layouts.admin')
@section('title', 'Galeri Foto')
@section('page-title', 'Manajemen Galeri')
@section('content')
    <div class="card" style="margin-bottom:24px;">
        <p class="card-title"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Foto Baru</p>
        <form action="{{ route('admin.galeri.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="gambar">Foto <span class="req">*</span></label>
                    <input type="file" id="gambar" name="gambar[]" accept="image/*" multiple required>
                    <p class="form-hint">Bisa pilih banyak foto sekaligus (maks 20). JPG/PNG/WebP, maks 4MB per foto.</p>
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" list="daftar-kategori" maxlength="60"
                        value="{{ old('kategori') }}" placeholder="Pilih atau ketik kategori baru">
                    <datalist id="daftar-kategori">
                        @foreach ($kategoris as $k)
                            <option value="{{ $k }}">
                        @endforeach
                    </datalist>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="judul">Judul (opsional)</label>
                    <input type="text" id="judul" name="judul" value="{{ old('judul') }}" maxlength="120"
                        placeholder="Judul untuk foto yang diupload">
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi (opsional)</label>
                    <input type="text" id="deskripsi" name="deskripsi" value="{{ old('deskripsi') }}" maxlength="600"
                        placeholder="Keterangan singkat foto">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-upload"></i>Upload</button>
        </form>
    </div>

    @if ($galeris->isEmpty())
        <div class="card">
            <div class="empty-state">
                <div class="empty-icon"><i class="fa-solid fa-images"></i></div>
                <p>Belum ada foto di galeri.</p>
            </div>
        </div>
    @else
        <p class="form-hint" style="margin-bottom:12px;">Foto berurutan dari nomor urut terkecil. Centang
            <strong>Unggulan</strong> agar foto tampil di bagian "Galeri Sekilas" pada landing page.</p>
        <div class="galeri-admin-grid">
            @foreach ($galeris as $galeri)
                <div class="galeri-admin-card">
                    <div class="galeri-admin-thumb">
                        <img src="{{ $galeri->url }}" alt="{{ $galeri->judul ?: 'Foto galeri' }}" loading="lazy">
                        @if ($galeri->unggulan)
                            <span class="galeri-flag"><i class="fa-solid fa-star"></i>Unggulan</span>
                        @endif
                    </div>
                    <div class="galeri-admin-body">
                        <form action="{{ route('admin.galeri.update', $galeri) }}" method="POST" class="galeri-admin-form">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="judul" value="{{ $galeri->judul }}" maxlength="120" placeholder="Judul foto">
                            <textarea name="deskripsi" rows="3" maxlength="600" placeholder="Deskripsi foto">{{ $galeri->deskripsi }}</textarea>
                            <input type="text" name="kategori" list="daftar-kategori" value="{{ $galeri->kategori }}" maxlength="60" placeholder="Kategori">
                            <div class="galeri-admin-row">
                                <label class="galeri-urutan">No. urut
                                    <input type="number" name="urutan" min="0" max="9999" value="{{ $galeri->urutan }}">
                                </label>
                                <label class="galeri-check">
                                    <input type="checkbox" name="unggulan" value="1" {{ $galeri->unggulan ? 'checked' : '' }}> Unggulan
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary btn-xs btn-full">
                                <i class="fa-solid fa-floppy-disk"></i>Simpan
                            </button>
                        </form>
                        <form action="{{ route('admin.galeri.destroy', $galeri) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs btn-full"
                                onclick="return confirm('Hapus foto ini?')">
                                <i class="fa-solid fa-trash-can"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="pagination-wrap">{{ $galeris->links() }}</div>
    @endif
@endsection
