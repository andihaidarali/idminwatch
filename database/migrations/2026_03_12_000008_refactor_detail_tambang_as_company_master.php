<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table) {
            if (!Schema::hasColumn('wilayah_tambang', 'detail_tambang_id')) {
                $table->foreignId('detail_tambang_id')->nullable()->after('id');
            }

            foreach ([
                'dampak_sosial',
                'dampak_sosial_en',
                'dampak_ekonomi',
                'dampak_ekonomi_en',
                'dampak_lingkungan',
                'dampak_lingkungan_en',
            ] as $column) {
                if (!Schema::hasColumn('wilayah_tambang', $column)) {
                    $table->text($column)->nullable();
                }
            }

            if (!Schema::hasColumn('wilayah_tambang', 'dokumentasi')) {
                $table->json('dokumentasi')->nullable();
            }
        });

        $details = DB::table('detail_tambang')
            ->select([
                'id',
                'wilayah_tambang_id',
                'nama_perusahaan',
                'profil_singkat',
                'profil_singkat_en',
                'dampak_sosial',
                'dampak_sosial_en',
                'dampak_ekonomi',
                'dampak_ekonomi_en',
                'dampak_lingkungan',
                'dampak_lingkungan_en',
                'dokumentasi',
            ])
            ->orderBy('id')
            ->get();

        $canonicalIds = [];
        $canonicalRows = [];

        foreach ($details as $detail) {
            $normalizedName = mb_strtolower(trim((string) ($detail->nama_perusahaan ?? '')));
            $companyKey = $normalizedName !== ''
                ? $normalizedName
                : '__detail_' . $detail->id;

            if (!isset($canonicalIds[$companyKey])) {
                $canonicalIds[$companyKey] = $detail->id;
                $canonicalRows[$detail->id] = $detail;
            } else {
                $canonicalId = $canonicalIds[$companyKey];
                $canonicalRow = $canonicalRows[$canonicalId];

                $updates = [];
                foreach (['profil_singkat', 'profil_singkat_en'] as $column) {
                    if (
                        blank($canonicalRow->{$column} ?? null)
                        && filled($detail->{$column} ?? null)
                    ) {
                        $updates[$column] = $detail->{$column};
                        $canonicalRow->{$column} = $detail->{$column};
                    }
                }

                if ($updates !== []) {
                    DB::table('detail_tambang')
                        ->where('id', $canonicalId)
                        ->update($updates);
                }
            }

            $canonicalId = $canonicalIds[$companyKey];

            if ($detail->wilayah_tambang_id) {
                DB::table('wilayah_tambang')
                    ->where('id', $detail->wilayah_tambang_id)
                    ->update([
                        'detail_tambang_id' => $canonicalId,
                        'dampak_sosial' => $detail->dampak_sosial,
                        'dampak_sosial_en' => $detail->dampak_sosial_en,
                        'dampak_ekonomi' => $detail->dampak_ekonomi,
                        'dampak_ekonomi_en' => $detail->dampak_ekonomi_en,
                        'dampak_lingkungan' => $detail->dampak_lingkungan,
                        'dampak_lingkungan_en' => $detail->dampak_lingkungan_en,
                        'dokumentasi' => $detail->dokumentasi,
                    ]);
            }
        }

        if ($canonicalIds !== []) {
            $idsToKeep = array_values(array_unique(array_values($canonicalIds)));
            DB::table('detail_tambang')
                ->whereNotIn('id', $idsToKeep)
                ->delete();
        }

        if (Schema::hasColumn('detail_tambang', 'wilayah_tambang_id')) {
            DB::statement('ALTER TABLE detail_tambang DROP CONSTRAINT IF EXISTS detail_tambang_wilayah_tambang_id_foreign');
            Schema::table('detail_tambang', function (Blueprint $table) {
                $table->dropColumn('wilayah_tambang_id');
            });
        }

        Schema::table('detail_tambang', function (Blueprint $table) {
            foreach ([
                'dampak_sosial',
                'dampak_sosial_en',
                'dampak_ekonomi',
                'dampak_ekonomi_en',
                'dampak_lingkungan',
                'dampak_lingkungan_en',
                'dokumentasi',
            ] as $column) {
                if (Schema::hasColumn('detail_tambang', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('wilayah_tambang', function (Blueprint $table) {
            $table->foreign('detail_tambang_id')
                ->references('id')
                ->on('detail_tambang')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_tambang', function (Blueprint $table) {
            if (!Schema::hasColumn('detail_tambang', 'wilayah_tambang_id')) {
                $table->foreignId('wilayah_tambang_id')->nullable()->constrained('wilayah_tambang')->cascadeOnDelete();
            }

            foreach ([
                'dampak_sosial',
                'dampak_sosial_en',
                'dampak_ekonomi',
                'dampak_ekonomi_en',
                'dampak_lingkungan',
                'dampak_lingkungan_en',
            ] as $column) {
                if (!Schema::hasColumn('detail_tambang', $column)) {
                    $table->text($column)->nullable();
                }
            }

            if (!Schema::hasColumn('detail_tambang', 'dokumentasi')) {
                $table->json('dokumentasi')->nullable();
            }
        });

        Schema::table('wilayah_tambang', function (Blueprint $table) {
            if (Schema::hasColumn('wilayah_tambang', 'detail_tambang_id')) {
                $table->dropForeign(['detail_tambang_id']);
            }

            foreach ([
                'dampak_sosial',
                'dampak_sosial_en',
                'dampak_ekonomi',
                'dampak_ekonomi_en',
                'dampak_lingkungan',
                'dampak_lingkungan_en',
                'dokumentasi',
                'detail_tambang_id',
            ] as $column) {
                if (Schema::hasColumn('wilayah_tambang', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
