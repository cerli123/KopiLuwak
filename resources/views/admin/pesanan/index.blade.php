@extends('layouts.admin')
@section('title', 'Pesanan')
@section('page-title', 'Manajemen Pesanan')
@section('content')
    <div class="tab-bar">
        @foreach (['' => 'Semua', 'menunggu_bayar' => 'Menunggu Bayar', 'menunggu_konfirmasi' => 'Konfirmasi', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $val => $lbl)
            <a href="{{ route('admin.pesanan.index', array_merge(request()->all(), ['status' => $val])) }}"
                class="tab-item {{ request('status') === $val ? 'active' : '' }}">{{ $lbl }}</a>
        @endforeach
    </div>

    <div class="toolbar" style="margin-bottom:16px;">
        <form action="{{ route('admin.pesanan.index') }}" method="GET" class="toolbar-left">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama...">
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesanans as $p)
                        <tr>
                            <td class="fw-600">{{ $p->kode_pesanan }}</td>
                            <td>{{ $p->user->nama }}</td>
                            <td style="font-size:12px;color:#6b7280;">{{ $p->created_at->format('d M Y') }}</td>
                            <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $p->status_color }}">{{ $p->status_label }}</span></td>
                            <td>
                                @if ($p->pembayaran)
                                    <span
                                        class="badge badge-{{ $p->pembayaran->status_bayar === 'dikonfirmasi' ? 'success' : ($p->pembayaran->status_bayar === 'ditolak' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($p->pembayaran->status_bayar) }}
                                    </span>
                                @else
                                    <span style="color:#9ca3af;font-size:12px;">—</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.pesanan.show', $p) }}" class="btn btn-outline btn-xs">Detail</a>
                                @if ($p->pembayaran?->status_bayar === 'dikonfirmasi')
                                    <a href="{{ route('struk.show', $p) }}" target="_blank" rel="noopener" class="btn btn-outline btn-xs"><i class="fa-solid fa-print"></i> Struk</a>
                                @endif
                                @if ($p->status === 'menunggu_konfirmasi' && $p->pembayaran?->status_bayar === 'pending')
                                    <form action="{{ route('admin.pesanan.konfirmasi', $p) }}" method="POST"
                                        style="display:inline">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-primary btn-xs">Konfirmasi</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding:32px;">Tidak ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $pesanans->links() }}</div>
    </div>
@endsection
