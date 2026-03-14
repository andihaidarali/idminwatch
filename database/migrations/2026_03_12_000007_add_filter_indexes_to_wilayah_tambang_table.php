<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table) {
            $table->index('nama_provinsi', 'wilayah_tambang_nama_provinsi_idx');
            $table->index('nama_kabupaten', 'wilayah_tambang_nama_kabupaten_idx');
            $table->index('jenis_tambang', 'wilayah_tambang_jenis_tambang_idx');
            $table->index(
                ['nama_provinsi', 'jenis_tambang'],
                'wilayah_tambang_provinsi_jenis_tambang_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table) {
            $table->dropIndex('wilayah_tambang_provinsi_jenis_tambang_idx');
            $table->dropIndex('wilayah_tambang_jenis_tambang_idx');
            $table->dropIndex('wilayah_tambang_nama_kabupaten_idx');
            $table->dropIndex('wilayah_tambang_nama_provinsi_idx');
        });
    }
};
