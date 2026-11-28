<?php

use App\Http\Controllers\Admin\PengaturanController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('pengaturan/asal',   [PengaturanController::class, 'updateAsal'])->name('pengaturan.asal');
    Route::delete('pengaturan/asal', [PengaturanController::class, 'resetAsal'])->name('pengaturan.asal.reset');
});
