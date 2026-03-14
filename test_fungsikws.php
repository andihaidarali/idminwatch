<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\DB::unprepared("
    UPDATE wilayah_tambang wt
    SET luas_overlap = COALESCE((
        SELECT ROUND(
            (SUM(ST_Area(ST_Intersection(wt.geom, kh.geom)::geography)) / 10000.0)::numeric,
            4
        )
        FROM kawasan_hutan kh
        WHERE ST_Intersects(wt.geom, kh.geom) AND kh.deskripsi NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi')
    ), 0)
    WHERE wt.geom IS NOT NULL;
");
echo "Updated overlaps\n";
