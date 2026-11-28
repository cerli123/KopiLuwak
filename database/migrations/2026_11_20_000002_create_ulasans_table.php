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
            $table->unsignedTinyInteger('rating');
            $table->text('komentar')->nullable();
            $table->boolean('tampil')->default(true);
            $table->text('balasan_admin')->nullable();
            $table->timestamp('dibalas_at')->nullable();
            $table->timestamps();

            $table->unique(['pesanan_id', 'produk_id']);
            $table->index(['produk_id', 'tampil']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ulasans');
    }
};
