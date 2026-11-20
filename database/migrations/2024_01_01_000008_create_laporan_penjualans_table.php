<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('laporan_penjualans')) {
            Schema::create('laporan_penjualans', function (Blueprint $table) {
                $table->id();
                $table->string('periode');
                $table->decimal('total_pendapatan', 12, 2);
                $table->integer('total_pesanan');
                $table->timestamp('generated_at')->useCurrent();
                $table->timestamps();
            });
        }
    }
    public function down(): void
    {
        Schema::dropIfExists('laporan_penjualans');
    }
};
