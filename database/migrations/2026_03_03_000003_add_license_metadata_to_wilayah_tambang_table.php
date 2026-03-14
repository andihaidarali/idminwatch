<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table): void {
            $table->date('tanggal_berlaku')->nullable()->after('nomor_sk');
            $table->date('tanggal_berakhir')->nullable()->after('tanggal_berlaku');
            $table->string('kegiatan')->nullable()->after('tanggal_berakhir');
            $table->string('nama_provinsi')->nullable()->after('kegiatan');
            $table->string('nama_kabupaten')->nullable()->after('nama_provinsi');
            $table->text('lokasi')->nullable()->after('nama_kabupaten');
            $table->string('jenis_izin')->nullable()->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table): void {
            $table->dropColumn([
                'tanggal_berlaku',
                'tanggal_berakhir',
                'kegiatan',
                'nama_provinsi',
                'nama_kabupaten',
                'lokasi',
                'jenis_izin',
            ]);
        });
    }
};
