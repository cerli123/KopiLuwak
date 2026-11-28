<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $pesanan->kode_pesanan }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            background: #efe9e2;
            font-family: 'Courier New', Courier, monospace;
            color: #111;
            padding: 20px 12px 40px;
        }

        .toolbar {
            max-width: 340px;
            margin: 0 auto 14px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            font-family: Arial, Helvetica, sans-serif;
        }

        .toolbar a,
        .toolbar button {
            flex: 1;
            text-align: center;
            padding: 9px 10px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            border: 1.5px solid #3D1F0D;
            background: #fff;
            color: #3D1F0D;
            text-decoration: none;
            cursor: pointer;
        }

        .toolbar .primary { background: #3D1F0D; color: #fff; }

        .struk {
            position: relative;
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 6mm 5mm 7mm;
            font-size: 12px;
            line-height: 1.45;
            box-shadow: 0 4px 18px rgba(61, 31, 13, .18);
        }

        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .logo { width: 44px; height: 44px; object-fit: contain; display: block; margin: 0 auto 4px; }
        .nama-toko { font-size: 15px; font-weight: bold; letter-spacing: .5px; }
        .kecil { font-size: 11px; color: #333; }
        .garis { border-top: 1px dashed #000; margin: 8px 0; }
        .garis-tebal { border-top: 2px solid #000; margin: 8px 0; }

        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 1px 0; }
        td.k { width: 34%; }
        td.t { width: 3%; }

        .item-nama { font-weight: bold; word-break: break-word; }
        .total-row td { font-size: 14px; font-weight: bold; padding-top: 3px; }

        .lunas {
            display: inline-block;
            border: 2px solid #111;
            padding: 1px 10px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-top: 4px;
        }

        .stempel {
            position: absolute;
            top: 38%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-18deg);
            border: 3px solid #b91c1c;
            color: #b91c1c;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 2px 12px;
            opacity: .75;
            pointer-events: none;
        }

        @page { size: 80mm auto; margin: 0; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .struk { box-shadow: none; width: 80mm; padding: 4mm 4mm 6mm; }
        }
    </style>
</head>

<body>
    <div class="toolbar">
        <a href="{{ $urlKembali }}">&larr; Kembali</a>
        <button type="button" class="primary" onclick="window.print()">Cetak</button>
        <a href="{{ route('struk.pdf', $pesanan) }}">Unduh PDF</a>
    </div>

    <div class="struk">
        @if ($stempel)
            <div class="stempel">{{ $stempel }}</div>
        @endif

        <div class="center">
            <img src="{{ \App\Models\Pengaturan::logoUrl() }}" alt="" class="logo" onerror="this.style.display='none'">
            <div class="nama-toko">{{ strtoupper($toko['nama']) }}</div>
            @if ($toko['kontak']['alamat'])
                <div class="kecil">{{ $toko['kontak']['alamat'] }}</div>
            @endif
            @if ($toko['kontak']['wa'])
                <div class="kecil">WA: {{ $toko['kontak']['wa'] }}</div>
            @endif
            @if ($toko['kontak']['email'])
                <div class="kecil">{{ $toko['kontak']['email'] }}</div>
            @endif
        </div>

        <div class="garis"></div>

        <table>
            <tr><td class="k">No. Pesanan</td><td class="t">:</td><td class="bold">{{ $pesanan->kode_pesanan }}</td></tr>
            <tr><td class="k">Tgl Pesan</td><td class="t">:</td><td>{{ $tglPesan->format('d/m/Y H:i') }}</td></tr>
            <tr><td class="k">Tgl Bayar</td><td class="t">:</td><td>{{ $tglBayar->format('d/m/Y H:i') }}</td></tr>
            <tr><td class="k">Pelanggan</td><td class="t">:</td><td>{{ $pesanan->user->nama ?? '-' }}</td></tr>
        </table>

        <div class="garis"></div>

        @foreach ($items as $d)
            <div class="item-nama">{{ $d->produk->nama_produk ?? 'Produk dihapus' }}</div>
            <table>
                <tr>
                    <td>{{ $d->qty }} x {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($d->subtotal(), 0, ',', '.') }}</td>
                </tr>
            </table>
        @endforeach

        <div class="garis"></div>

        <table>
            <tr><td>Jumlah Item</td><td class="right">{{ $totalQty }}</td></tr>
            <tr><td>Subtotal</td><td class="right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td></tr>
            <tr><td>Ongkir ({{ rtrim(rtrim(number_format((float) $pesanan->jarak_km, 1, ',', '.'), '0'), ',') }} km)</td><td class="right">Rp {{ number_format($pesanan->ongkir, 0, ',', '.') }}</td></tr>
        </table>

        <div class="garis-tebal"></div>

        <table>
            <tr class="total-row"><td>TOTAL BAYAR</td><td class="right">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td></tr>
        </table>

        <div class="garis"></div>

        <table>
            <tr><td class="k">Metode</td><td class="t">:</td><td>{{ $pembayaran->metode_label }}</td></tr>
            @if ($pembayaran->referensi)
                <tr><td class="k">Ref.</td><td class="t">:</td><td style="word-break:break-all;">{{ $pembayaran->referensi }}</td></tr>
            @endif
            <tr><td class="k">Status</td><td class="t">:</td><td class="bold">{{ $pembayaran->status_label }}</td></tr>
        </table>

        <div class="garis"></div>

        <div class="bold">Dikirim ke:</div>
        <div>{{ $pesanan->nama_penerima }} ({{ $pesanan->no_hp_penerima }})</div>
        <div class="kecil">{{ $pesanan->alamat_pengiriman }}</div>

        <div class="garis"></div>

        <div class="center">
            <div class="lunas">LUNAS</div>
            <p style="margin-top:8px;">Terima kasih telah berbelanja!</p>
            <p class="kecil">Simpan struk ini sebagai bukti pembayaran.</p>
            <p class="kecil">Pengajuan refund maks. {{ $batasRefund }} hari.</p>
            @if ($toko['kontak']['jam'])
                <p class="kecil">Jam layanan: {{ $toko['kontak']['jam'] }}</p>
            @endif
            <p class="kecil" style="margin-top:6px;">Dicetak {{ $tglCetak->format('d/m/Y H:i') }} WIB</p>
        </div>
    </div>
    @if (request()->boolean('cetak'))
        <script>
            window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 400); });
        </script>
    @endif
</body>

</html>
