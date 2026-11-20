@extends('layouts.admin')
@section('title', 'Kategori')
@section('page-title', 'Kelola Kategori')
@section('content')
    <div class="admin-two-col">
        <div class="card">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kategoris as $kat)
                        <tr>
                            <td class="fw-600">{{ $kat->nama_kategori }}</td>
                            <td>{{ $kat->produks_count }} produk</td>
                            <td>
                                <form action="{{ route('admin.kategori.destroy', $kat) }}" method="POST"
                                    style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs"
                                        onclick="return confirm('Hapus kategori {{ $kat->nama_kategori }}?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center" style="padding:24px;">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3 class="card-title">Tambah Kategori</h3>
            <form action="{{ route('admin.kategori.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Kategori <span class="req">*</span></label>
                    <input type="text" name="nama_kategori" placeholder="Contoh: Arabika" required>
                </div>
                <button type="submit" class="btn btn-primary btn-full">Tambah Kategori</button>
            </form>
        </div>
    </div>
@endsection
