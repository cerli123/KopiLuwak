<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('banners')) {
            Schema::create('banners', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->string('deskripsi')->nullable();
                $table->string('gambar');
                $table->string('tombol_teks', 40)->nullable();
                $table->string('tombol_url')->nullable();
                $table->unsignedInteger('urutan')->default(0);
                $table->boolean('aktif')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};