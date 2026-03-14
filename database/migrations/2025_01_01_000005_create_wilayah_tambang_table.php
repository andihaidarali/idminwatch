<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wilayah_tambang', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                          // Nama perusahaan / blok tambang
            $table->string('nomor_sk')->nullable();          // Nomor SK Izin
            $table->string('jenis_tambang')->nullable();     // Batubara, Nikel, Emas, dll
            $table->string('status')->default('aktif');      // aktif, expired, ditangguhkan
            $table->double('luas_sk_ha')->nullable();        // Luas sesuai SK (Hektar)
            $table->double('luas_overlap')->default(0);      // AUTO-CALCULATED by trigger (Hektar)
            $table->timestamps();
        });

        // Add geometry column with SRID 4326 (WGS84)
        DB::statement("SELECT AddGeometryColumn('public', 'wilayah_tambang', 'geom', 4326, 'MULTIPOLYGON', 2);");

        // Create spatial index
        DB::statement("CREATE INDEX idx_wilayah_tambang_geom ON wilayah_tambang USING GIST (geom);");
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_tambang');
    }
};
