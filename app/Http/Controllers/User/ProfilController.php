<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $user         = $request->user();
        $totalPesanan = $user->pesanans()->count();

        return view('user.profil', compact('user', 'totalPesanan'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'   => 'required|string|max:100',
            'email'  => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'no_hp'  => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        // Akun yang dibuat lewat Google belum punya password: boleh membuatnya tanpa password lama.
        $perluPasswordLama = (bool) $user->punya_password;

        $request->validate([
            'current_password' => $perluPasswordLama ? 'required|current_password' : 'nullable',
            'password'         => 'required|string|min:6|confirmed',
        ], [
            'current_password.current_password' => 'Password lama tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
            'password.min'                      => 'Password baru minimal 6 karakter.',
        ]);

        $user->update(['password' => $request->password, 'punya_password' => true]);

        return back()->with('success', $perluPasswordLama ? 'Password berhasil diubah!' : 'Password berhasil dibuat! Sekarang Anda juga bisa masuk dengan email & password.');
    }
}
