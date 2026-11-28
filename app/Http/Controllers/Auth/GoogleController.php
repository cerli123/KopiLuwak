<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\CaBundle;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')
                ->with('error', 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env.');
        }

        return $this->driver()->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->filled('error')) {
            return redirect()->route('login')->with('error', 'Login dengan Google dibatalkan.');
        }

        try {
            $google = $this->driver()->user();
        } catch (\Throwable $e) {
            Log::warning('Login Google gagal: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }

        $email = strtolower((string) $google->getEmail());

        if ($email === '') {
            return redirect()->route('login')->with('error', 'Akun Google Anda tidak menyediakan alamat email.');
        }

        $raw = (array) ($google->user ?? []);
        if (array_key_exists('email_verified', $raw) && ! filter_var($raw['email_verified'], FILTER_VALIDATE_BOOL)) {
            return redirect()->route('login')->with('error', 'Email Google Anda belum terverifikasi.');
        }

        $user = User::where('google_id', $google->getId())->first();

        if (! $user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->forceFill([
                    'google_id' => $google->getId(),
                    'avatar'    => $user->avatar ?: $google->getAvatar(),
                ])->save();
            } else {
                $user = User::create([
                    'nama'           => $google->getName() ?: Str::before($email, '@'),
                    'email'          => $email,
                    'password'       => Str::random(40),
                    'role'           => 'user',
                    'google_id'      => $google->getId(),
                    'avatar'         => $google->getAvatar(),
                    'punya_password' => false,
                ]);
            }
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->intended(route('katalog'))
            ->with('success', 'Berhasil masuk dengan Google. Selamat datang, ' . $user->nama . '!');
    }

    private function driver()
    {
        $driver = Socialite::driver('google');

        return $driver->setHttpClient(new Client(['verify' => CaBundle::verify()]));
    }
}
