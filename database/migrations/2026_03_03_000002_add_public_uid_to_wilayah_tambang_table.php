<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wilayah_tambang', function (Blueprint $table) {
            $table->string('public_uid', 26)->nullable()->after('id');
        });

        DB::table('wilayah_tambang')
            ->select('id')
            ->orderBy('id')
            ->chunkById(100, function ($records): void {
                foreach ($records as $record) {
                    DB::table('wilayah_tambang')
                        ->where('id', $record->id)
                        ->update([
                            'public_uid' => (string) Str::lower((string) Str::ulid()),
                        ]);
                }
            });

        DB::statement('ALTER TABLE wilayah_tambang ALTER COLUMN public_uid SET NOT NULL');
        DB::statement('CREATE UNIQUE INDEX wilayah_tambang_public_uid_unique ON wilayah_tambang (public_uid)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS wilayah_tambang_public_uid_unique');

        Schema::table('wilayah_tambang', function (Blueprint $table) {
            $table->dropColumn('public_uid');
        });
    }
};
