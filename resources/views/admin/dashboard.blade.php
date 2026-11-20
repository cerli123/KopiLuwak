@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('content')

    <div class="stats-grid">
        <div class="stat-card">
            <p class="stat-label">Total Pesanan</p>
            <p class="stat-value">{{ $totalPesanan }}</p>
            <p class="stat-sub">{{ $menunggu }} menunggu konfirmasi</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Pendapatan Bulan Ini</p>
            <p class="stat-value sm">Rp {{ number_format($pendapatanBulan, 0, ',', '.') }}</p>
            <p class="stat-sub">{{ now()->format('F Y') }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Menunggu Konfirmasi</p>
            <p class="stat-value">{{ $menunggu }}</p>
            <p class="stat-sub">Perlu ditindaklanjuti</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Produk</p>
            <p class="stat-value">{{ $totalProduk }}</p>
            <p class="stat-sub">{{ $totalUser }} user terdaftar</p>
        </div>
    </div>

    <div class="dash-grid">
        <div class="card">
            <div class="card-head">
                <h3 class="card-title">Pesanan Terbaru</h3>
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pesananTerbaru as $p)
                            <tr>
                                <td class="fw-600">{{ $p->kode_pesanan }}</td>
                                <td>{{ $p->user->nama }}</td>
                                <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                                <td><span class="badge badge-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                                <td>
                                    <a href="{{ route('admin.pesanan.show', $p) }}"
                                        class="btn btn-outline btn-xs">Detail</a>
                                    @if ($p->status === 'menunggu_konfirmasi')
                                        <form action="{{ route('admin.pesanan.konfirmasi', $p) }}" method="POST"
                                            style="display:inline">
                                            @csrf @method('PATCH')
                                            <button class="btn btn-primary btn-xs">Konfirmasi</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title"> Stok Hampir Habis</h3>
            @foreach ($stokRendah as $prod)
                <div class="stok-alert {{ $prod->sisa_stok < 10 ? 'stok-kritis' : 'stok-warn' }}">
                    <div>
                        <p class="stok-nama">{{ $prod->nama_produk }}</p>
                        <p class="stok-kat">{{ $prod->kategori->nama_kategori }}</p>
                    </div>
                    <span class="stok-val">{{ $prod->sisa_stok }} pcs</span>
                </div>
            @endforeach
            <a href="{{ route('admin.stok.index') }}" class="btn btn-outline btn-full" style="margin-top:12px;">Kelola
                Stok</a>
        </div>
    </div>
@endsection
