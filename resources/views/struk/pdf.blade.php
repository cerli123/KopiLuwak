<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Struk {{ $pesanan->kode_pesanan }}</title>
    <style>
        @page { margin: 10pt 12pt; }
        * { margin: 0; padding: 0; }
        body { font-family: 'Courier', monospace; font-size: 9pt; color: #000; line-height: 1.3; }
        table { width: 100%; border-collapse: collapse; }
        td { vertical-align: top; padding: 1pt 0; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .nama-toko { font-size: 12pt; font-weight: bold; }
        .kecil { font-size: 8pt; }
        .garis { border-top: 1px dashed #000; margin: 6pt 0; height: 0; }
        .garis-tebal { border-top: 2px solid #000; margin: 6pt 0; height: 0; }
        .total td { font-size: 11pt; font-weight: bold; }
        .lunas { border: 2px solid #000; padding: 1pt 10pt; font-weight: bold; letter-spacing: 2pt; display: inline-block; }
        .stempel { border: 2px solid #b91c1c; color: #b91c1c; font-weight: bold; font-size: 14pt; padding: 1pt 8pt; display: inline-block; letter-spacing: 2pt; }
    </style>
</head>

<body>
    <div class="center">
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
        @if ($stempel)
            <div style="margin-top:4pt;"><span class="stempel">{{ $stempel }}</span></div>
        @endif
    </div>

    <div class="garis"></div>

    <table>
        <tr><td width="32%">No. Pesanan</td><td width="3%">:</td><td class="bold">{{ $pesanan->kode_pesanan }}</td></tr>
        <tr><td>Tgl Pesan</td><td>:</td><td>{{ $tglPesan->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Tgl Bayar</td><td>:</td><td>{{ $tglBayar->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Pelanggan</td><td>:</td><td>{{ $pesanan->user->nama ?? '-' }}</td></tr>
    </table>

    <div class="garis"></div>

    @foreach ($items as $d)
        <div class="bold">{{ $d->produk->nama_produk ?? 'Produk dihapus' }}</div>
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
        <tr class="total"><td>TOTAL BAYAR</td><td class="right">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td></tr>
    </table>

    <div class="garis"></div>

    <table>
        <tr><td width="32%">Metode</td><td width="3%">:</td><td>{{ $pembayaran->metode_label }}</td></tr>
        @if ($pembayaran->referensi)
            <tr><td>Ref.</td><td>:</td><td>{{ $pembayaran->referensi }}</td></tr>
        @endif
        <tr><td>Status</td><td>:</td><td class="bold">{{ $pembayaran->status_label }}</td></tr>
    </table>

    <div class="garis"></div>

    <div class="bold">Dikirim ke:</div>
    <div>{{ $pesanan->nama_penerima }} ({{ $pesanan->no_hp_penerima }})</div>
    <div class="kecil">{{ $pesanan->alamat_pengiriman }}</div>

    <div class="garis"></div>

    <div class="center">
        <div class="lunas">LUNAS</div>
        <p style="margin-top:6pt;">Terima kasih telah berbelanja!</p>
        <p class="kecil">Simpan struk ini sebagai bukti pembayaran.</p>
        <p class="kecil">Pengajuan refund maks. {{ $batasRefund }} hari.</p>
        @if ($toko['kontak']['jam'])
            <p class="kecil">Jam layanan: {{ $toko['kontak']['jam'] }}</p>
        @endif
        <p class="kecil" style="margin-top:4pt;">Dicetak {{ $tglCetak->format('d/m/Y H:i') }} WIB</p>
    </div>
</body>

</html>
