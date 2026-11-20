<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Setiap kolom dicek dulu supaya migrasi aman dijalankan ulang
        // walau sebagian kolom sudah ada di database.
        Schema::table('pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanans', 'lat')) {
                $table->decimal('lat', 10, 7)->nullable()->after('no_hp_penerima');
            }
            if (! Schema::hasColumn('pesanans', 'lng')) {
                $table->decimal('lng', 10, 7)->nullable()->after('lat');
            }
            if (! Schema::hasColumn('pesanans', 'jarak_km')) {
                $table->decimal('jarak_km', 8, 2)->default(0)->after('lng');
            }
            if (! Schema::hasColumn('pesanans', 'ongkir')) {
                $table->unsignedInteger('ongkir')->default(0)->after('jarak_km');
            }
            if (! Schema::hasColumn('pesanans', 'midtrans_order_id')) {
                $table->string('midtrans_order_id')->nullable()->unique()->after('ongkir');
            }
            if (! Schema::hasColumn('pesanans', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('midtrans_order_id');
            }
        });

        Schema::table('pembayarans', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayarans', 'referensi')) {
                $table->string('referensi')->nullable()->after('bukti_transfer');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pembayarans', 'referensi')) {
            Schema::table('pembayarans', function (Blueprint $table) {
                $table->dropColumn('referensi');
            });
        }

        $kolom = array_values(array_filter(
            ['lat', 'lng', 'jarak_km', 'ongkir', 'midtrans_order_id', 'snap_token'],
            fn ($nama) => Schema::hasColumn('pesanans', $nama)
        ));

        if ($kolom) {
            Schema::table('pesanans', function (Blueprint $table) use ($kolom) {
                $table->dropColumn($kolom);
            });
        }
    }
};
