@extends('layouts.admin')
@section('title', 'Stok')
@section('page-title', 'Kelola Stok')
@section('content')
    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stoks as $stok)
                        <tr>
                            <td class="fw-600">{{ $stok->produk->nama_produk }}</td>
                            <td>{{ $stok->produk->kategori->nama_kategori }}</td>
                            <td><span
                                    class="fw-700 {{ $stok->jumlah === 0 ? 'text-danger' : ($stok->jumlah < 10 ? 'text-danger' : ($stok->jumlah < 20 ? 'text-warning' : 'text-success')) }}">{{ $stok->jumlah }}
                                    pcs</span></td>
                            <td>
                                @if ($stok->jumlah === 0)
                                    <span class="badge badge-danger">Habis</span>
                                @elseif($stok->jumlah < 10)
                                    <span class="badge badge-danger">Kritis</span>
                                @elseif($stok->jumlah < 20)
                                    <span class="badge badge-warning">Rendah</span>
                                @else
                                    <span class="badge badge-success">Aman</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.stok.update', $stok) }}" method="POST"
                                    style="display:flex;gap:8px;align-items:center;">
                                    @csrf @method('PATCH')
                                    <input type="number" name="jumlah" value="{{ $stok->jumlah }}" min="0"
                                        style="width:80px;padding:6px 8px;border:1.5px solid #e5e7eb;border-radius:6px;font-size:14px;">
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $stoks->links() }}</div>
    </div>
@endsection
