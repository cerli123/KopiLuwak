<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('galeris', function (Blueprint $table) {
            if (! Schema::hasColumn('galeris', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('judul');
            }
            if (! Schema::hasColumn('galeris', 'kategori')) {
                $table->string('kategori', 60)->nullable()->after('deskripsi');
            }
            if (! Schema::hasColumn('galeris', 'urutan')) {
                $table->unsignedInteger('urutan')->default(0)->after('kategori');
            }
            if (! Schema::hasColumn('galeris', 'unggulan')) {
                $table->boolean('unggulan')->default(false)->after('urutan');
            }
        });
    }

    public function down(): void
    {
        $kolom = array_values(array_filter(
            ['deskripsi', 'kategori', 'urutan', 'unggulan'],
            fn ($nama) => Schema::hasColumn('galeris', $nama)
        ));

        if ($kolom) {
            Schema::table('galeris', function (Blueprint $table) use ($kolom) {
                $table->dropColumn($kolom);
            });
        }
    }
};
