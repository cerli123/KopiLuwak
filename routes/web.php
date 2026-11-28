<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\StrukController;
use App\Http\Controllers\User\AlamatController;
use App\Http\Controllers\User\OngkirController;
use App\Http\Controllers\User\KatalogController;
use App\Http\Controllers\User\KeranjangController;
use App\Http\Controllers\User\PesananController;
use App\Http\Controllers\User\PembayaranController;
use App\Http\Controllers\User\GaleriController;
use App\Http\Controllers\User\ProfilController;
use App\Http\Controllers\User\RefundController;
use App\Http\Controllers\User\UlasanController;
use App\Http\Controllers\User\TokoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\StokController;
use App\Http\Controllers\Admin\PesananAdminController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\GaleriController as GaleriAdminController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\RefundController as RefundAdminController;
use App\Http\Controllers\Admin\UlasanController as UlasanAdminController;

Route::get('/media/{path}', [MediaController::class, 'show'])->where('path', '.*')->name('media');

Route::get('/', [KatalogController::class, 'landing'])->name('landing');
Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri');

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/auth/google',          [GoogleController::class, 'redirect'])->middleware('throttle:20,1')->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

Route::post('/midtrans/notifikasi', [MidtransController::class, 'notifikasi'])->name('midtrans.notifikasi');

Route::middleware('auth')->group(function () {
    Route::get('/struk/{pesanan}',     [StrukController::class, 'show'])->name('struk.show');
    Route::get('/struk/{pesanan}/pdf', [StrukController::class, 'pdf'])->name('struk.pdf');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


Route::get('/toko-tutup', [TokoController::class, 'tutup'])->middleware('auth')->name('toko.tutup');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/profil',            [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil',            [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/password',   [ProfilController::class, 'updatePassword'])->name('profil.password');

    Route::get('/pesanan',           [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');

    Route::get('/pembayaran/{pesanan}/selesai', [PembayaranController::class, 'selesai'])->name('pembayaran.selesai');
    Route::get('/pembayaran/{pesanan}/status',  [PembayaranController::class, 'status'])->name('pembayaran.status');

    Route::get('/refund',            [RefundController::class, 'index'])->name('refund.index');
    Route::get('/refund/{refund}',   [RefundController::class, 'show'])->name('refund.show');
    Route::delete('/refund/{refund}', [RefundController::class, 'batal'])->name('refund.batal');

    Route::post('/pesanan/{pesanan}/ulasan/{produk}', [UlasanController::class, 'store'])->middleware('throttle:20,1')->name('ulasan.store');
    Route::put('/ulasan/{ulasan}',                    [UlasanController::class, 'update'])->middleware('throttle:20,1')->name('ulasan.update');
    Route::delete('/ulasan/{ulasan}',                 [UlasanController::class, 'destroy'])->name('ulasan.destroy');

    Route::middleware('toko.buka')->group(function () {
        Route::get('/katalog',           [KatalogController::class, 'index'])->name('katalog');
        Route::get('/produk/{produk}',   [KatalogController::class, 'show'])->name('produk.show');

        Route::get('/keranjang',         [KeranjangController::class, 'index'])->name('keranjang');
        Route::post('/keranjang',        [KeranjangController::class, 'tambah'])->name('keranjang.tambah');
        Route::patch('/keranjang/{id}',  [KeranjangController::class, 'update'])->name('keranjang.update');
        Route::delete('/keranjang/{id}', [KeranjangController::class, 'hapus'])->name('keranjang.hapus');

        Route::post('/ongkir',           [OngkirController::class, 'hitung'])->middleware('throttle:30,1')->name('ongkir.hitung');

        Route::post('/alamat/reverse',   [AlamatController::class, 'reverse'])->middleware('throttle:60,1')->name('alamat.reverse');
        Route::post('/alamat/cari',      [AlamatController::class, 'cari'])->middleware('throttle:30,1')->name('alamat.cari');

        Route::get('/checkout',          [PesananController::class, 'checkout'])->name('checkout');
        Route::post('/pesanan',          [PesananController::class, 'store'])->name('pesanan.store');

        Route::get('/pembayaran/{pesanan}',           [PembayaranController::class, 'form'])->name('pembayaran.form');
        Route::post('/pembayaran/{pesanan}/token',    [PembayaranController::class, 'token'])->middleware('throttle:20,1')->name('pembayaran.token');

        Route::get('/pesanan/{pesanan}/refund',  [RefundController::class, 'create'])->name('refund.create');
        Route::post('/pesanan/{pesanan}/refund', [RefundController::class, 'store'])->middleware('throttle:10,1')->name('refund.store');
    });
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/',  [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('produk',   ProdukController::class);
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::resource('banner', BannerController::class)->except(['show']);
    Route::resource('galeri', GaleriAdminController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('pengaturan',         [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::post('pengaturan/logo',   [PengaturanController::class, 'updateLogo'])->name('pengaturan.logo');
    Route::post('pengaturan/toko',   [PengaturanController::class, 'updateToko'])->name('pengaturan.toko');
    Route::post('pengaturan/ongkir', [PengaturanController::class, 'updateOngkir'])->name('pengaturan.ongkir');
    Route::delete('pengaturan/logo', [PengaturanController::class, 'resetLogo'])->name('pengaturan.logo.reset');
    Route::post('pengaturan/kontak',        [PengaturanController::class, 'updateKontak'])->name('pengaturan.kontak');
    Route::post('pengaturan/pembayaran/tes', [PengaturanController::class, 'tesPembayaran'])->middleware('throttle:10,1')->name('pengaturan.pembayaran.tes');

    Route::get('stok',          [StokController::class, 'index'])->name('stok.index');
    Route::patch('stok/{stok}', [StokController::class, 'update'])->name('stok.update');

    Route::get('pesanan',                        [PesananAdminController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/{pesanan}',              [PesananAdminController::class, 'show'])->name('pesanan.show');
    Route::patch('pesanan/{pesanan}/konfirmasi', [PesananAdminController::class, 'konfirmasi'])->name('pesanan.konfirmasi');
    Route::patch('pesanan/{pesanan}/status',     [PesananAdminController::class, 'updateStatus'])->name('pesanan.status');

    Route::get('refund',                       [RefundAdminController::class, 'index'])->name('refund.index');
    Route::get('refund/{refund}',              [RefundAdminController::class, 'show'])->name('refund.show');
    Route::patch('refund/{refund}/setujui',    [RefundAdminController::class, 'setujui'])->name('refund.setujui');
    Route::patch('refund/{refund}/tolak',      [RefundAdminController::class, 'tolak'])->name('refund.tolak');
    Route::patch('refund/{refund}/selesai',    [RefundAdminController::class, 'selesai'])->name('refund.selesai');

    Route::get('ulasan',                  [UlasanAdminController::class, 'index'])->name('ulasan.index');
    Route::patch('ulasan/{ulasan}/tampil', [UlasanAdminController::class, 'tampil'])->name('ulasan.tampil');
    Route::patch('ulasan/{ulasan}/balas',  [UlasanAdminController::class, 'balas'])->name('ulasan.balas');
    Route::delete('ulasan/{ulasan}',       [UlasanAdminController::class, 'destroy'])->name('ulasan.destroy');

    Route::get('laporan',            [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
});
