<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->dropColumn(['direktur', 'alamat', 'telepon', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            $table->string('direktur')->nullable()->after('profil_singkat');
            $table->text('alamat')->nullable()->after('direktur');
            $table->string('telepon')->nullable()->after('alamat');
            $table->string('email')->nullable()->after('telepon');
        });
    }
};
