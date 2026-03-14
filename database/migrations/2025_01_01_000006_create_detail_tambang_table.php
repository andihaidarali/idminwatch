<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detail_tambang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_tambang_id')
                ->constrained('wilayah_tambang')
                ->cascadeOnDelete();

            // Profil Perusahaan
            $table->string('nama_perusahaan')->nullable();
            $table->string('direktur')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();

            // Dampak Sosial
            $table->text('dampak_sosial')->nullable();

            // Dampak Ekonomi
            $table->text('dampak_ekonomi')->nullable();

            // Dampak Lingkungan
            $table->text('dampak_lingkungan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_tambang');
    }
};
