<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->text('profil_singkat')->nullable()->after('nama_perusahaan');
        });
    }

    public function down(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->dropColumn('profil_singkat');
        });
    }
};
