<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table): void {
            $table->string('kegiatan_en')->nullable()->after('kegiatan');
        });

        Schema::create('jenis_tambang_refs', function (Blueprint $table): void {
            $table->id();
            $table->string('nama')->unique();
            $table->string('nama_en')->nullable();
            $table->timestamps();
        });

        $now = now();
        $existingTypes = DB::table('wilayah_tambang')
            ->whereNotNull('jenis_tambang')
            ->where('jenis_tambang', '!=', '')
            ->distinct()
            ->orderBy('jenis_tambang')
            ->pluck('jenis_tambang');

        foreach ($existingTypes as $jenisTambang) {
            DB::table('jenis_tambang_refs')->updateOrInsert(
                ['nama' => $jenisTambang],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_tambang_refs');

        Schema::table('wilayah_tambang', function (Blueprint $table): void {
            $table->dropColumn('kegiatan_en');
        });
    }
};
