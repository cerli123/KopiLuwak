<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('pesanans')) {
            Schema::create('pesanans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('kode_pesanan')->unique();
                $table->decimal('total_harga', 12, 2);
                $table->enum('status', ['menunggu_bayar', 'menunggu_konfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('menunggu_bayar');
                $table->text('alamat_pengiriman');
                $table->string('nama_penerima');
                $table->string('no_hp_penerima');
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};
