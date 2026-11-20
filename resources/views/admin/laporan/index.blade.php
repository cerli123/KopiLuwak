@extends('layouts.admin')
@section('title', 'Laporan')
@section('page-title', 'Laporan Penjualan')
@section('content')
    <div class="card" style="margin-bottom:20px;">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="filter-form">
            <div class="form-group" style="margin:0;">
                <label>Dari Tanggal</label>
                <input type="date" name="dari" value="{{ $dari }}">
            </div>
            <div class="form-group" style="margin:0;">
                <label>Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ $sampai }}">
            </div>
            <div style="align-self:flex-end;display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('admin.laporan.pdf', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn btn-outline"
                    target="_blank">Export PDF</a>
            </div>
        </form>
    </div>

    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card stat-green">
            <p class="stat-label">Total Pendapatan</p>
            <p class="stat-value sm">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="stat-card stat-brown">
            <p class="stat-label">Total Pesanan</p>
            <p class="stat-value">{{ $totalPesanan }}</p>
        </div>
        <div class="stat-card stat-blue">
            <p class="stat-label">Rata-rata per Order</p>
            <p class="stat-value sm">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $p)
                        <tr>
                            <td class="fw-600">{{ $p->kode_pesanan }}</td>
                            <td>{{ $p->user->nama }}</td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td style="font-size:12px;color:#6b7280;">{{ $p->detailPesanans->sum('qty') }} item</td>
                            <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center" style="padding:32px;">Belum ada data pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($pesanans->isNotEmpty())
                    <tfoot>
                        <tr style="background:#F0E0CC;">
                            <td colspan="4" class="fw-700">Total Pendapatan</td>
                            <td class="fw-700">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection
