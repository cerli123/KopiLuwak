<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $kolom = array_values(array_filter(
            ['email_verified_at', 'kode_verifikasi', 'kode_verifikasi_kadaluarsa', 'kode_percobaan'],
            fn ($nama) => Schema::hasColumn('users', $nama)
        ));

        if ($kolom) {
            Schema::table('users', function (Blueprint $table) use ($kolom) {
                $table->dropColumn($kolom);
            });
        }
    }

    public function down(): void
    {
    }
};
