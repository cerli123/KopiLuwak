<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('urutan')->orderBy('id')->get();

        return view('admin.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        $data = $this->data($request, true);
        $data['gambar'] = $request->file('gambar')->store('banner', 'public');

        Banner::create($data);

        return redirect()->route('admin.banner.index')->with('success', 'Banner berhasil ditambahkan!');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $data = $this->data($request, false);

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($banner->gambar);
            $data['gambar'] = $request->file('gambar')->store('banner', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banner.index')->with('success', 'Banner berhasil diperbarui!');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->gambar);
        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }

    private function data(Request $request, bool $wajibGambar): array
    {
        $validated = $request->validate([
            'judul'       => 'required|string|max:120',
            'deskripsi'   => 'nullable|string|max:255',
            'tombol_teks' => 'nullable|string|max:40',
            'tombol_url'  => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/|\/|#)/i'],
            'urutan'      => 'nullable|integer|min:0',
            'gambar'      => [$wajibGambar ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'tombol_url.regex' => 'Link tombol harus diawali http://, https://, / atau #.',
        ]);

        unset($validated['gambar']);

        $validated['urutan'] = $validated['urutan'] ?? 0;
        $validated['aktif']  = $request->boolean('aktif');

        return $validated;
    }
}
