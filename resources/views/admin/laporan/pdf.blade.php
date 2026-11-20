<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #3D1F0D;
            font-size: 18px;
            margin-bottom: 4px;
        }

        .sub {
            text-align: center;
            color: #666;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .sum-box {
            flex: 1;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
        }

        .sum-label {
            font-size: 10px;
            color: #888;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .sum-value {
            font-size: 18px;
            font-weight: bold;
            color: #3D1F0D;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #3D1F0D;
            color: #fff;
            padding: 8px 10px;
            text-align: left;
            font-size: 11px;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) td {
            background: #FAF6F0;
        }

        tfoot td {
            background: #F0E0CC;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Kopiluwak Lereng Muria</h1>
    <p class="sub">Laporan Penjualan Periode: {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} —
        {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</p>
    <p class="sub">Digenerate: {{ now()->format('d M Y, H:i') }} WIB</p>

    <div class="summary">
        <div class="sum-box">
            <div class="sum-label">Total Pesanan</div>
            <div class="sum-value">{{ $pesanans->count() }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Total Pendapatan</div>
            <div class="sum-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="sum-box">
            <div class="sum-label">Rata-rata/Order</div>
            <div class="sum-value">Rp
                {{ number_format($pesanans->count() ? $totalPendapatan / $pesanans->count() : 0, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Pesanan</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Items</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanans as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->kode_pesanan }}</td>
                    <td>{{ $p->user->nama }}</td>
                    <td>{{ $p->created_at->format('d M Y') }}</td>
                    <td>{{ $p->detailPesanans->sum('qty') }} item</td>
                    <td>Rp {{ number_format($p->total_harga, 0, ',', '.') }}</td>
                    <td>{{ $p->status_label }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">TOTAL</td>
                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
