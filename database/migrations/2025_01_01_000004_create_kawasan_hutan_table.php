<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kawasan_hutan', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('fungsi');  // Hutan Lindung, Hutan Produksi Terbatas, Hutan Produksi Tetap, Konservasi, dll
            $table->string('sumber_data')->nullable();
            $table->timestamps();
        });

        // Add geometry column with SRID 4326 (WGS84)
        DB::statement("SELECT AddGeometryColumn('public', 'kawasan_hutan', 'geom', 4326, 'MULTIPOLYGON', 2);");

        // Create spatial index for fast spatial queries
        DB::statement("CREATE INDEX idx_kawasan_hutan_geom ON kawasan_hutan USING GIST (geom);");
    }

    public function down(): void
    {
        Schema::dropIfExists('kawasan_hutan');
    }
};
