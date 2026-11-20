<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('produks')->get();
        return view('admin.kategori.index', compact('kategoris'));
    }
    public function store(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100|unique:kategoris']);
        Kategori::create($request->only('nama_kategori'));
        return back()->with('success','Kategori berhasil ditambahkan.');
    }
    public function update(Request $request, Kategori $kategori)
    {
        $request->validate(['nama_kategori' => 'required|string|max:100']);
        $kategori->update($request->only('nama_kategori'));
        return back()->with('success','Kategori berhasil diperbarui.');
    }
    public function destroy(Kategori $kategori)
    {
        if ($kategori->produks()->exists()) {
            return back()->with('error','Tidak bisa hapus kategori yang masih punya produk.');
        }
        $kategori->delete();
        return back()->with('success','Kategori berhasil dihapus.');
    }
}
