<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('pembayarans')) {
            Schema::create('pembayarans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->string('metode');
                $table->decimal('jumlah_bayar', 12, 2);
                $table->string('bukti_transfer')->nullable();
                $table->enum('status_bayar', ['pending', 'dikonfirmasi', 'ditolak'])->default('pending');
                $table->timestamp('tanggal_bayar')->nullable();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
