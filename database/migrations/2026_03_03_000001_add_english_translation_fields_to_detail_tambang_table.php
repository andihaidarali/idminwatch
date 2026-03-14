<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->text('profil_singkat_en')->nullable()->after('profil_singkat');
            $table->text('dampak_sosial_en')->nullable()->after('dampak_sosial');
            $table->text('dampak_ekonomi_en')->nullable()->after('dampak_ekonomi');
            $table->text('dampak_lingkungan_en')->nullable()->after('dampak_lingkungan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->dropColumn([
                'profil_singkat_en',
                'dampak_sosial_en',
                'dampak_ekonomi_en',
                'dampak_lingkungan_en',
            ]);
        });
    }
};
