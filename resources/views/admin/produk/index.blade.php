@extends('layouts.admin')
@section('title', 'Produk')
@section('page-title', 'Manajemen Produk')
@section('content')
    <div class="toolbar">
        <form action="{{ route('admin.produk.index') }}" method="GET" class="toolbar-left">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
            <select name="kategori">
                <option value="">Semua Kategori</option>
                @foreach ($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>
                        {{ $kat->nama_kategori }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline">Filter</button>
            @if (request()->hasAny(['search', 'kategori']))
                <a href="{{ route('admin.produk.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.produk.create') }}" class="btn btn-primary">+ Tambah Produk</a>
    </div>
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produks as $produk)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    @if ($produk->gambar_url)
                                        <img src="{{ $produk->gambar_url }}"
                                            style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                    @else
                                        <div
                                            style="width:40px;height:40px;background:#F0E0CC;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                                            <i class="fa-solid fa-mug-hot" style="color:#7B4D2A"></i></div>
                                    @endif
                                    <span class="fw-600">{{ $produk->nama_produk }}</span>
                                </div>
                            </td>
                            <td>{{ $produk->kategori->nama_kategori }}</td>
                            <td>Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                            <td><span
                                    class="{{ $produk->sisa_stok < 10 ? 'text-danger fw-700' : 'text-success fw-700' }}">{{ $produk->sisa_stok }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.produk.edit', $produk) }}" class="btn btn-outline btn-xs">Edit</a>
                                <form action="{{ route('admin.produk.destroy', $produk) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs"
                                        onclick="return confirm('Hapus produk ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center" style="padding:32px;">Tidak ada produk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $produks->links() }}</div>
    </div>
@endsection
