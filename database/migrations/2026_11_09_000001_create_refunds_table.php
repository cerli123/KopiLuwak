<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('refunds')) {
            Schema::create('refunds', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->unique()->constrained('pesanans')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('kode_refund')->unique();

                $table->string('alasan_kategori', 40);
                $table->text('alasan_detail');
                $table->string('bukti')->nullable();
                $table->decimal('jumlah_refund', 12, 2);

                $table->string('status', 20)->default('diajukan');

                $table->text('catatan_admin')->nullable();
                $table->foreignId('diproses_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('diproses_pada')->nullable();

                $table->string('metode_pengembalian', 30)->nullable();
                $table->string('referensi_pengembalian')->nullable();
                $table->timestamp('dikembalikan_pada')->nullable();

                $table->timestamps();
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
