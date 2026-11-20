<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Login dengan Google: simpan ID akun Google, foto profil, dan penanda apakah
 * pengguna sudah punya password sendiri (akun yang dibuat lewat Google belum punya).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar', 500)->nullable()->after('google_id');
            }
            if (! Schema::hasColumn('users', 'punya_password')) {
                $table->boolean('punya_password')->default(true)->after('avatar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'google_id')) {
                $table->dropUnique(['google_id']);
                $table->dropColumn('google_id');
            }
            foreach (['avatar', 'punya_password'] as $kolom) {
                if (Schema::hasColumn('users', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
