@extends('layouts.admin')
@section('title', 'Banner Carousel')
@section('page-title', 'Banner Carousel')
@section('content')
    <div class="toolbar">
        <p class="form-hint" style="margin:0;">Banner aktif tampil bergantian di bagian atas landing page. Jika tidak ada banner aktif, tampilan bawaan dipakai.</p>
        <a href="{{ route('admin.banner.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i>Tambah Banner</a>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Tombol</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($banners as $banner)
                        <tr>
                            <td><img src="{{ \App\Support\Media::url($banner->gambar) }}" alt="{{ $banner->judul }}" class="thumb-wide"></td>
                            <td>
                                <span class="fw-600">{{ $banner->judul }}</span>
                                @if ($banner->deskripsi)
                                    <div class="form-hint">{{ \Illuminate\Support\Str::limit($banner->deskripsi, 60) }}</div>
                                @endif
                            </td>
                            <td>{{ $banner->tombol_teks ?: '-' }}</td>
                            <td>{{ $banner->urutan }}</td>
                            <td>
                                <span class="badge {{ $banner->aktif ? 'badge-success' : 'badge-danger' }}">
                                    {{ $banner->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.banner.edit', $banner) }}" class="btn btn-outline btn-xs">
                                    <i class="fa-solid fa-pen"></i>Edit
                                </a>
                                <form action="{{ route('admin.banner.destroy', $banner) }}" method="POST" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-xs" onclick="return confirm('Hapus banner ini?')">
                                        <i class="fa-solid fa-trash-can"></i>Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding:32px;">Belum ada banner.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
