<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GaleriController extends Controller
{
    public function index()
    {
        $galeris   = Galeri::urut()->paginate(18);
        $kategoris = collect(Galeri::KATEGORI)
            ->merge(Galeri::siapDetail() ? Galeri::whereNotNull('kategori')->distinct()->pluck('kategori') : [])
            ->unique()->values();

        return view('admin.galeri.index', compact('galeris', 'kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'nullable|string|max:120',
            'deskripsi' => 'nullable|string|max:600',
            'kategori'  => 'nullable|string|max:60',
            'gambar'    => 'required|array|max:20',
            'gambar.*'  => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $files = $request->file('gambar');

        foreach ($files as $file) {
            Galeri::create([
                'judul'     => $request->judul,
                'deskripsi' => $request->deskripsi,
                'kategori'  => $request->kategori ?: null,
                'unggulan'  => false,
                'urutan'    => 0,
                'gambar'    => $file->store('galeri', 'public'),
            ]);
        }

        return back()->with('success', count($files) . ' foto berhasil ditambahkan!');
    }

    public function update(Request $request, Galeri $galeri)
    {
        $data = $request->validate([
            'judul'     => 'nullable|string|max:120',
            'deskripsi' => 'nullable|string|max:600',
            'kategori'  => 'nullable|string|max:60',
            'urutan'    => 'nullable|integer|min:0|max:9999',
        ]);

        $data['urutan']   = $data['urutan'] ?? 0;
        $data['kategori'] = $data['kategori'] ?: null;
        $data['unggulan'] = $request->boolean('unggulan');

        $galeri->update($data);

        return back()->with('success', 'Foto berhasil diperbarui.');
    }

    public function destroy(Galeri $galeri)
    {
        Storage::disk('public')->delete($galeri->gambar);
        $galeri->delete();

        return back()->with('success', 'Foto berhasil dihapus.');
    }
}
