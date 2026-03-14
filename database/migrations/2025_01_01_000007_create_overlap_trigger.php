<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ================================================================
        // SQL FUNCTION: Automatically calculate overlap area (Hektar)
        // between wilayah_tambang and kawasan_hutan using ST_Intersection
        // Uses ::geography cast for accurate area in square meters
        // ================================================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION hitung_luas_overlap()
            RETURNS TRIGGER AS $$
            DECLARE
                total_overlap DOUBLE PRECISION;
            BEGIN
                -- Only calculate if geometry is not null
                IF NEW.geom IS NOT NULL THEN
                    SELECT COALESCE(
                        SUM(
                            ST_Area(
                                ST_Intersection(NEW.geom, kh.geom)::geography
                            ) / 10000.0  -- Convert m² to Hektar
                        ), 0
                    ) INTO total_overlap
                    FROM kawasan_hutan kh
                    WHERE ST_Intersects(NEW.geom, kh.geom) 
                    AND kh.deskripsi NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi');

                    NEW.luas_overlap := ROUND(total_overlap::numeric, 4);
                ELSE
                    NEW.luas_overlap := 0;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // ================================================================
        // TRIGGER: Fire BEFORE INSERT OR UPDATE of geom on wilayah_tambang
        // This ensures luas_overlap is always up-to-date when data is
        // inserted/updated via QGIS or any other method
        // ================================================================
        DB::unprepared("
            CREATE TRIGGER trg_hitung_overlap
            BEFORE INSERT OR UPDATE OF geom ON wilayah_tambang
            FOR EACH ROW
            EXECUTE FUNCTION hitung_luas_overlap();
        ");

        // ================================================================
        // TRIGGER: Recalculate ALL wilayah_tambang overlaps when
        // kawasan_hutan data changes (insert/update/delete)
        // ================================================================
        DB::unprepared("
            CREATE OR REPLACE FUNCTION recalculate_all_overlaps()
            RETURNS TRIGGER AS $$
            BEGIN
                UPDATE wilayah_tambang wt
                SET luas_overlap = COALESCE((
                    SELECT ROUND(
                        (SUM(ST_Area(ST_Intersection(wt.geom, kh.geom)::geography)) / 10000.0)::numeric,
                        4
                    )
                    FROM kawasan_hutan kh
                    WHERE ST_Intersects(wt.geom, kh.geom)
                    AND kh.deskripsi NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi')
                ), 0)
                WHERE wt.geom IS NOT NULL;

                RETURN NULL;
            END;
            $$ LANGUAGE plpgsql;
        ");

        DB::unprepared("
            CREATE TRIGGER trg_recalculate_overlaps_on_hutan_change
            AFTER INSERT OR UPDATE OR DELETE ON kawasan_hutan
            FOR EACH STATEMENT
            EXECUTE FUNCTION recalculate_all_overlaps();
        ");
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_recalculate_overlaps_on_hutan_change ON kawasan_hutan;');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_hitung_overlap ON wilayah_tambang;');
        DB::unprepared('DROP FUNCTION IF EXISTS recalculate_all_overlaps();');
        DB::unprepared('DROP FUNCTION IF EXISTS hitung_luas_overlap();');
    }
};
