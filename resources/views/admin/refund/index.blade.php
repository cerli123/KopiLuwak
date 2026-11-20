@extends('layouts.admin')
@section('title', 'Refund')
@section('page-title', 'Manajemen Refund')
@section('content')
    <div class="tab-bar">
        @foreach (['' => 'Semua', 'diajukan' => 'Menunggu', 'disetujui' => 'Dana Diproses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'] as $val => $lbl)
            <a href="{{ route('admin.refund.index', array_merge(request()->except('page'), ['status' => $val])) }}"
                class="tab-item {{ (string) request('status', '') === (string) $val ? 'active' : '' }}">
                {{ $lbl }}
                @if ($val !== '' && ($jumlah[$val] ?? 0) > 0)
                    ({{ $jumlah[$val] }})
                @endif
            </a>
        @endforeach
    </div>

    <div class="toolbar" style="margin-bottom:16px;">
        <form action="{{ route('admin.refund.index') }}" method="GET" class="toolbar-left">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode refund / pesanan / nama...">
            <button type="submit" class="btn btn-outline">Cari</button>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Kode Refund</th>
                        <th>Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Alasan</th>
                        <th>Dana</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($refunds as $r)
                        <tr>
                            <td class="fw-600">{{ $r->kode_refund }}</td>
                            <td>{{ $r->pesanan->kode_pesanan }}</td>
                            <td>{{ $r->user->nama }}</td>
                            <td style="font-size:12px;">{{ $r->alasan_label }}</td>
                            <td>Rp {{ number_format($r->jumlah_refund, 0, ',', '.') }}</td>
                            <td><span class="badge badge-{{ $r->status_color }}">{{ $r->status_label }}</span></td>
                            <td><a href="{{ route('admin.refund.show', $r) }}" class="btn btn-outline btn-xs">{{ $r->status === 'diajukan' ? 'Tinjau' : 'Detail' }}</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center" style="padding:32px;">Tidak ada pengajuan refund.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrap">{{ $refunds->links() }}</div>
    </div>
@endsection
