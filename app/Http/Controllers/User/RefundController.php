<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Refund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $refunds = Refund::with('pesanan')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        if ($request->expectsJson()) {
            return response()->json($refunds);
        }

        return view('user.refund-index', compact('refunds'));
    }

    public function create(Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $pesanan->load(['detailPesanans.produk', 'pembayaran', 'refund']);

        if ($pesan = $pesanan->alasanTidakBisaRefund()) {
            return redirect()->route('pesanan.show', $pesanan)->with('error', $pesan);
        }

        return view('user.refund-form', [
            'pesanan'     => $pesanan,
            'daftarAlasan' => config('toko.refund.alasan'),
            'wajibBukti'  => config('toko.refund.wajib_bukti'),
        ]);
    }

    public function store(Request $request, Pesanan $pesanan)
    {
        abort_if($pesanan->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'alasan_kategori' => ['required', Rule::in(array_keys(config('toko.refund.alasan')))],
            'alasan_detail'   => ['required', 'string', 'min:10', 'max:1000'],
            'bukti'           => [
                Rule::requiredIf(in_array($request->input('alasan_kategori'), config('toko.refund.wajib_bukti'), true)),
                'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048',
            ],
        ], [
            'alasan_kategori.required' => 'Pilih alasan refund.',
            'alasan_kategori.in'       => 'Alasan refund tidak valid.',
            'alasan_detail.required'   => 'Jelaskan alasan refund Anda.',
            'alasan_detail.min'        => 'Penjelasan minimal 10 karakter.',
            'alasan_detail.max'        => 'Penjelasan maksimal 1000 karakter.',
            'bukti.required'           => 'Foto bukti wajib dilampirkan untuk alasan ini.',
            'bukti.image'              => 'Bukti harus berupa gambar.',
            'bukti.mimes'              => 'Format bukti harus JPG, PNG, atau WEBP.',
            'bukti.max'                => 'Ukuran foto bukti maksimal 2 MB.',
        ]);

        $path = $request->hasFile('bukti') ? $request->file('bukti')->store('refund', 'public') : null;

        try {
            $refund = DB::transaction(function () use ($pesanan, $data, $path) {
                $p = Pesanan::with(['pembayaran', 'refund'])
                    ->whereKey($pesanan->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($pesan = $p->alasanTidakBisaRefund()) {
                    throw new \RuntimeException($pesan);
                }

                return Refund::create([
                    'pesanan_id'      => $p->id,
                    'user_id'         => Auth::id(),
                    'kode_refund'     => Refund::generateKode(),
                    'alasan_kategori' => $data['alasan_kategori'],
                    'alasan_detail'   => $data['alasan_detail'],
                    'bukti'           => $path,
                    'jumlah_refund'   => $p->total_bayar,
                    'status'          => 'diajukan',
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            if ($request->expectsJson()) {
                return response()->json(['pesan' => $e->getMessage()], 422);
            }

            return redirect()->route('pesanan.show', $pesanan)->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $e;
        }

        if ($request->expectsJson()) {
            return response()->json(['pesan' => 'Pengajuan refund terkirim.', 'refund' => $refund], 201);
        }

        return redirect()->route('refund.show', $refund)
            ->with('success', 'Pengajuan refund terkirim. Admin akan meninjau dan memberi keputusan.');
    }

    public function show(Request $request, Refund $refund)
    {
        abort_if($refund->user_id !== Auth::id(), 403);

        $refund->load(['pesanan.detailPesanans.produk', 'pesanan.pembayaran']);

        if ($request->expectsJson()) {
            return response()->json($refund);
        }

        return view('user.refund-detail', compact('refund'));
    }

    public function batal(Request $request, Refund $refund)
    {
        abort_if($refund->user_id !== Auth::id(), 403);

        $pesanan = $refund->pesanan;

        $dihapus = DB::transaction(function () use ($refund) {
            $r = Refund::whereKey($refund->id)->lockForUpdate()->first();

            if (! $r || $r->status !== 'diajukan') {
                return false;
            }

            if ($r->bukti) {
                Storage::disk('public')->delete($r->bukti);
            }

            $r->delete();

            return true;
        });

        if (! $dihapus) {
            if ($request->expectsJson()) {
                return response()->json(['pesan' => 'Pengajuan sudah diproses admin dan tidak bisa dibatalkan.'], 409);
            }

            return redirect()->route('refund.show', $refund)
                ->with('error', 'Pengajuan sudah diproses admin dan tidak bisa dibatalkan.');
        }

        if ($request->expectsJson()) {
            return response()->json(['pesan' => 'Pengajuan refund dibatalkan.']);
        }

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Pengajuan refund dibatalkan.');
    }
}
