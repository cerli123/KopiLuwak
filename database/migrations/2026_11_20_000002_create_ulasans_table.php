<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ulasans')) {
            return;
        }

        Schema::create('ulasans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');            // 1 - 5
            $table->text('komentar')->nullable();
            $table->boolean('tampil')->default(true);          // admin bisa menyembunyikan
            $table->text('balasan_admin')->nullable();
            $table->timestamp('dibalas_at')->nullable();
            $table->timestamps();

            // Satu ulasan per produk per pesanan.
            $table->unique(['pesanan_id', 'produk_id']);
            $table->index(['produk_id', 'tampil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};
