<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(Request $request)
    {
        $kategoris = Galeri::siapDetail()
            ? Galeri::whereNotNull('kategori')->where('kategori', '!=', '')
                ->distinct()->orderBy('kategori')->pluck('kategori')
            : collect();

        $query = Galeri::urut();

        if ($request->filled('kategori') && $kategoris->contains($request->kategori)) {
            $query->where('kategori', $request->kategori);
        }

        $galeris = $query->paginate(18)->withQueryString();

        return view('user.galeri', compact('galeris', 'kategoris'));
    }
}
