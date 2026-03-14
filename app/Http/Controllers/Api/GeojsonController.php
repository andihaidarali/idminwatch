<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class GeojsonController extends Controller
{
    /**
     * GET /api/geojson/tambang
     * Returns wilayah_tambang as GeoJSON FeatureCollection
     */
    public function tambang(Request $request): JsonResponse
    {
        [$bboxClause, $bboxBindings] = $this->bboxClause($request, 'wt.geom');
        [$filterClause, $filterBindings] = $this->tambangFilterClause($request, 'wt');

        $features = DB::select("
            SELECT jsonb_build_object(
                'type', 'Feature',
                'id', wt.id,
                'geometry', ST_AsGeoJSON(wt.geom)::jsonb,
                'properties', jsonb_build_object(
                    'id', wt.id,
                    'public_uid', wt.public_uid,
                    'nama', COALESCE(NULLIF(dt.nama_perusahaan, ''), wt.nomor_sk, CONCAT('Wilayah Tambang ', wt.id)),
                    'nomor_sk', wt.nomor_sk,
                    'tanggal_berlaku', wt.tanggal_berlaku,
                    'tanggal_berakhir', wt.tanggal_berakhir,
                    'kegiatan', wt.kegiatan,
                    'kegiatan_en', wt.kegiatan_en,
                    'nama_provinsi', wt.nama_provinsi,
                    'nama_kabupaten', wt.nama_kabupaten,
                    'lokasi', wt.lokasi,
                    'jenis_izin', wt.jenis_izin,
                    'jenis_tambang', wt.jenis_tambang,
                    'jenis_tambang_en', jtr.nama_en,
                    'status', wt.status,
                    'luas_sk_ha', wt.luas_sk_ha,
                    'luas_overlap', wt.luas_overlap
                )
            ) AS feature
            FROM wilayah_tambang wt
            LEFT JOIN detail_tambang dt ON dt.id = wt.detail_tambang_id
            LEFT JOIN jenis_tambang_refs jtr ON jtr.nama = wt.jenis_tambang
            WHERE wt.geom IS NOT NULL
            {$filterClause}
            {$bboxClause}
        ", array_merge($filterBindings, $bboxBindings));

        return $this->wrapFeatureCollection($features);
    }

    /**
     * GET /api/tambang-list
     * Returns lightweight tambang data for sidebar list without full geometry payload
     */
    public function tambangList(Request $request): JsonResponse
    {
        $cacheKey = $this->dashboardCacheKey('tambang-list', [
            'provinsi' => $this->normalizedFilterValue($request->query('provinsi')),
            'jenis_tambang' => $this->normalizedFilterValue($request->query('jenis_tambang')),
        ]);

        $items = Cache::remember($cacheKey, $this->dashboardCacheTtl(), function () use ($request) {
            return DB::table('wilayah_tambang as wt')
                ->tap(fn (Builder $query) => $this->applyTambangFilters($query, $request, 'wt'))
                ->whereNotNull('wt.geom')
                ->selectRaw("
                    wt.id,
                    wt.public_uid,
                    COALESCE(NULLIF(dt.nama_perusahaan, ''), wt.nomor_sk, CONCAT('Wilayah Tambang ', wt.id)) AS nama,
                    wt.nomor_sk,
                    wt.kegiatan,
                    wt.kegiatan_en,
                    wt.nama_provinsi,
                    wt.jenis_tambang,
                    jtr.nama_en AS jenis_tambang_en,
                    wt.luas_overlap,
                    ST_X(ST_PointOnSurface(wt.geom)) AS lng,
                    ST_Y(ST_PointOnSurface(wt.geom)) AS lat
                ")
            ->leftJoin('detail_tambang as dt', 'dt.id', '=', 'wt.detail_tambang_id')
            ->leftJoin('jenis_tambang_refs as jtr', 'jtr.nama', '=', 'wt.jenis_tambang')
            ->orderByDesc('wt.created_at')
            ->get()
            ->map(fn ($item) => (array) $item)
            ->all();
        });

        return response()->json([
            'data' => $items,
        ]);
    }

    /**
     * GET /api/geojson/hutan
     * Returns kawasan_hutan as GeoJSON FeatureCollection
     */
    public function hutan(Request $request): JsonResponse
    {
        $nameColumn = $this->kawasanNameColumn();
        $fungsiColumn = $this->kawasanFungsiColumn();
        [$bboxClause, $bboxBindings] = $this->bboxClause($request, 'kh.geom');

        $features = DB::select("
            SELECT jsonb_build_object(
                'type', 'Feature',
                'id', kh.id,
                'geometry', ST_AsGeoJSON(kh.geom)::jsonb,
                'properties', jsonb_build_object(
                    'id', kh.id,
                    'nama', kh.{$nameColumn},
                    'deskripsi', kh.{$nameColumn},
                    'fungsi', kh.{$fungsiColumn}
                )
            ) AS feature
            FROM kawasan_hutan kh
            WHERE kh.geom IS NOT NULL
            AND kh.{$nameColumn} NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi')
            {$bboxClause}
        ", $bboxBindings);

        return $this->wrapFeatureCollection($features);
    }

    /**
     * GET /api/geojson/overlap
     * Returns ONLY the intersection geometries between tambang & hutan
     * This is the key visual layer showing overlap areas in red
     */
    public function overlap(Request $request): JsonResponse
    {
        $nameColumn = $this->kawasanNameColumn();
        $fungsiColumn = $this->kawasanFungsiColumn();
        [$bboxClause, $bboxBindings] = $this->bboxClause($request, 'kh.geom');
        [$filterClause, $filterBindings] = $this->tambangFilterClause($request, 'wt');

        $features = DB::select("
            SELECT jsonb_build_object(
                'type', 'Feature',
                'geometry', ST_AsGeoJSON(ix.geom)::jsonb,
                'properties', jsonb_build_object(
                    'tambang_id', wt.id,
                    'tambang_nama', COALESCE(NULLIF(dt.nama_perusahaan, ''), wt.nomor_sk, CONCAT('Wilayah Tambang ', wt.id)),
                    'nama_provinsi', wt.nama_provinsi,
                    'jenis_tambang', wt.jenis_tambang,
                    'kawasan_fungsi', kh.{$fungsiColumn},
                    'luas_overlap_ha', ROUND(
                        (ST_Area(ix.geom::geography) / 10000.0)::numeric,
                        4
                    )
                )
            ) AS feature
            FROM wilayah_tambang wt
            LEFT JOIN detail_tambang dt ON dt.id = wt.detail_tambang_id
            INNER JOIN kawasan_hutan kh
                ON wt.geom && kh.geom
                AND ST_Intersects(wt.geom, kh.geom)
            CROSS JOIN LATERAL (
                SELECT ST_Intersection(wt.geom, kh.geom) AS geom
            ) ix
            WHERE wt.geom IS NOT NULL
            AND kh.geom IS NOT NULL
            AND NOT ST_IsEmpty(ix.geom)
            AND kh.{$nameColumn} NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi')
            {$filterClause}
            {$bboxClause}
        ", array_merge($filterBindings, $bboxBindings));

        return $this->wrapFeatureCollection($features);
    }

    /**
     * GET /api/tambang/{id}/detail
     * Returns detail info for a specific wilayah_tambang (for popup)
     */
    public function detailTambang(int $id): JsonResponse
    {
        $nameColumn = $this->kawasanNameColumn();
        $fungsiColumn = $this->kawasanFungsiColumn();

        $tambang = DB::selectOne("
            SELECT
                wt.id,
                wt.public_uid,
                COALESCE(NULLIF(dt.nama_perusahaan, ''), wt.nomor_sk, CONCAT('Wilayah Tambang ', wt.id)) AS nama,
                wt.nomor_sk,
                wt.tanggal_berlaku,
                wt.tanggal_berakhir,
                wt.kegiatan,
                wt.kegiatan_en,
                wt.nama_provinsi,
                wt.nama_kabupaten,
                wt.lokasi,
                wt.jenis_izin,
                wt.jenis_tambang,
                jtr.nama_en AS jenis_tambang_en,
                wt.status,
                wt.luas_sk_ha,
                wt.luas_overlap,
                wt.dampak_sosial,
                wt.dampak_sosial_en,
                wt.dampak_ekonomi,
                wt.dampak_ekonomi_en,
                wt.dampak_lingkungan,
                wt.dampak_lingkungan_en,
                wt.dokumentasi
            FROM wilayah_tambang wt
            LEFT JOIN detail_tambang dt ON dt.id = wt.detail_tambang_id
            LEFT JOIN jenis_tambang_refs jtr ON jtr.nama = wt.jenis_tambang
            WHERE wt.id = ?
        ", [$id]);

        if (!$tambang) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $detail = DB::selectOne("
            SELECT
                dt.id,
                dt.nama_perusahaan,
                dt.profil_singkat,
                dt.profil_singkat_en
            FROM wilayah_tambang wt
            LEFT JOIN detail_tambang dt ON dt.id = wt.detail_tambang_id
            WHERE wt.id = ?
        ", [$id]);

        $dokumentasi = $tambang->dokumentasi ?? null;

        if (is_string($dokumentasi)) {
            $decoded = json_decode($dokumentasi, true);
            $dokumentasi = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($dokumentasi)) {
            $dokumentasi = [];
        }

        if (!$detail) {
            $detail = (object) [
                'id' => null,
                'nama_perusahaan' => null,
                'profil_singkat' => null,
                'profil_singkat_en' => null,
            ];
        }

        $detail->dokumentasi = $dokumentasi;
        $detail->dokumentasi_urls = array_map(
            fn (string $path) => Storage::url($path),
            array_values(array_filter($dokumentasi, fn ($path) => is_string($path) && $path !== ''))
        );
        $detail->dampak_sosial = $tambang->dampak_sosial;
        $detail->dampak_sosial_en = $tambang->dampak_sosial_en;
        $detail->dampak_ekonomi = $tambang->dampak_ekonomi;
        $detail->dampak_ekonomi_en = $tambang->dampak_ekonomi_en;
        $detail->dampak_lingkungan = $tambang->dampak_lingkungan;
        $detail->dampak_lingkungan_en = $tambang->dampak_lingkungan_en;

        // Get per-kawasan overlap breakdown
        $overlaps = DB::select("
            SELECT
                kh.{$fungsiColumn} AS fungsi,
                kh.{$nameColumn} AS kawasan_nama,
                ROUND(
                    (ST_Area(ix.geom::geography) / 10000.0)::numeric,
                    4
                ) AS luas_ha
            FROM wilayah_tambang wt
            INNER JOIN kawasan_hutan kh
                ON wt.geom && kh.geom
                AND ST_Intersects(wt.geom, kh.geom)
            CROSS JOIN LATERAL (
                SELECT ST_Intersection(wt.geom, kh.geom) AS geom
            ) ix
            WHERE wt.id = ? AND kh.{$nameColumn} NOT IN ('Areal Penggunaan Lain', 'Tidak Terdefinisi')
            AND NOT ST_IsEmpty(ix.geom)
            ORDER BY luas_ha DESC
        ", [$id]);

        $shareUrl = route('dashboard.shared', $tambang->public_uid);

        return response()->json([
            'tambang' => $tambang,
            'detail' => $detail,
            'overlaps' => $overlaps,
            'public_url' => $shareUrl,
            'share_links' => [
                'whatsapp' => 'https://wa.me/?text=' . rawurlencode("{$tambang->nama}\n\n{$shareUrl}"),
                'email' => 'mailto:?subject=' . rawurlencode("Indonesia Mining Watch: {$tambang->nama}") . '&body=' . rawurlencode($shareUrl),
                'telegram' => 'https://t.me/share/url?url=' . rawurlencode($shareUrl) . '&text=' . rawurlencode($tambang->nama),
                'x' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($shareUrl) . '&text=' . rawurlencode($tambang->nama),
                'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl),
            ],
        ]);
    }

    /**
     * GET /api/statistik
     * Returns summary statistics for dashboard sidebar
     */
    public function statistik(Request $request): JsonResponse
    {
        $cacheKey = $this->dashboardCacheKey('statistik', [
            'provinsi' => $this->normalizedFilterValue($request->query('provinsi')),
            'jenis_tambang' => $this->normalizedFilterValue($request->query('jenis_tambang')),
        ]);

        $stats = Cache::remember($cacheKey, $this->dashboardCacheTtl(), function () use ($request) {
            $result = DB::table('wilayah_tambang')
                ->tap(fn (Builder $query) => $this->applyTambangFilters($query, $request))
                ->selectRaw('
                    COUNT(*) AS total_tambang,
                    COUNT(*) FILTER (WHERE luas_overlap > 0) AS tambang_overlap,
                    COALESCE(ROUND(SUM(luas_overlap)::numeric, 2), 0) AS total_luas_overlap_ha,
                    COALESCE(ROUND(SUM(luas_sk_ha)::numeric, 2), 0) AS total_luas_tambang_ha
                ')
                ->first();

            return (array) $result;
        });

        return response()->json([
            'total_tambang' => $stats['total_tambang'] ?? 0,
            'tambang_overlap' => $stats['tambang_overlap'] ?? 0,
            'total_luas_overlap_ha' => $stats['total_luas_overlap_ha'] ?? 0,
            'total_luas_tambang_ha' => $stats['total_luas_tambang_ha'] ?? 0,
        ]);
    }

    public function filterOptions(Request $request): JsonResponse
    {
        $options = Cache::remember(
            $this->dashboardCacheKey('filter-options'),
            $this->dashboardCacheTtl(),
            function () {
                $provinsi = DB::table('wilayah_tambang')
                    ->whereNotNull('nama_provinsi')
                    ->where('nama_provinsi', '!=', '')
                    ->distinct()
                    ->orderBy('nama_provinsi')
                    ->pluck('nama_provinsi')
                    ->values()
                    ->all();

                $jenisTambang = DB::table('jenis_tambang_refs')
                    ->select(['nama as value', 'nama as label', 'nama_en as label_en'])
                    ->orderBy('nama')
                    ->get()
                    ->map(fn ($item) => (array) $item)
                    ->keyBy('value');

                $missingJenisTambang = DB::table('wilayah_tambang')
                    ->whereNotNull('jenis_tambang')
                    ->where('jenis_tambang', '!=', '')
                    ->whereNotIn('jenis_tambang', function ($query) {
                        $query->select('nama')->from('jenis_tambang_refs');
                    })
                    ->distinct()
                    ->orderBy('jenis_tambang')
                    ->pluck('jenis_tambang');

                foreach ($missingJenisTambang as $missingType) {
                    $jenisTambang->put($missingType, [
                        'value' => $missingType,
                        'label' => $missingType,
                        'label_en' => null,
                    ]);
                }

                return [
                    'provinsi' => $provinsi,
                    'jenis_tambang' => $jenisTambang->values()->all(),
                ];
            }
        );

        return response()->json([
            'provinsi' => $options['provinsi'] ?? [],
            'jenis_tambang' => $options['jenis_tambang'] ?? [],
        ]);
    }

    /**
     * Wrap features into a GeoJSON FeatureCollection
     */
    private function wrapFeatureCollection(array $features): JsonResponse
    {
        $geojson = [
            'type' => 'FeatureCollection',
            'features' => array_map(function ($row) {
                return json_decode($row->feature, true);
            }, $features),
        ];

        return response()->json($geojson, 200, [
            'Content-Type' => 'application/geo+json',
        ]);
    }

    private function bboxClause(Request $request, string $geomColumn): array
    {
        $bbox = $this->parseBBox($request->query('bbox'));

        if (!$bbox) {
            return ['', []];
        }

        return [
            "AND {$geomColumn} && ST_MakeEnvelope(?, ?, ?, ?, 4326)
            AND ST_Intersects({$geomColumn}, ST_MakeEnvelope(?, ?, ?, ?, 4326))",
            [...$bbox, ...$bbox],
        ];
    }

    private function parseBBox(mixed $bbox): ?array
    {
        if (!is_string($bbox)) {
            return null;
        }

        $parts = array_map('trim', explode(',', $bbox));
        if (count($parts) !== 4) {
            return null;
        }

        foreach ($parts as $part) {
            if (!is_numeric($part)) {
                return null;
            }
        }

        $coords = array_map('floatval', $parts);
        [$minLng, $minLat, $maxLng, $maxLat] = $coords;

        if ($minLng >= $maxLng || $minLat >= $maxLat) {
            return null;
        }

        return [$minLng, $minLat, $maxLng, $maxLat];
    }

    private function kawasanNameColumn(): string
    {
        static $column = null;

        if ($column !== null) {
            return $column;
        }

        $column = Schema::hasColumn('kawasan_hutan', 'deskripsi') ? 'deskripsi' : 'nama';

        return $column;
    }

    private function kawasanFungsiColumn(): string
    {
        static $column = null;

        if ($column !== null) {
            return $column;
        }

        $column = Schema::hasColumn('kawasan_hutan', 'fungsikws') ? 'fungsikws' : 'fungsi';

        return $column;
    }

    private function tambangFilterClause(Request $request, string $tableAlias): array
    {
        $clauses = [];
        $bindings = [];

        $provinsi = $this->normalizedFilterValue($request->query('provinsi'));
        $jenisTambang = $this->normalizedFilterValue($request->query('jenis_tambang'));

        if ($provinsi !== null) {
            $clauses[] = "AND {$tableAlias}.nama_provinsi = ?";
            $bindings[] = $provinsi;
        }

        if ($jenisTambang !== null) {
            $clauses[] = "AND {$tableAlias}.jenis_tambang = ?";
            $bindings[] = $jenisTambang;
        }

        return [implode("\n", $clauses), $bindings];
    }

    private function applyTambangFilters(Builder $query, Request $request, string $tableAlias = ''): void
    {
        $columnPrefix = $tableAlias !== '' ? "{$tableAlias}." : '';
        $provinsi = $this->normalizedFilterValue($request->query('provinsi'));
        $jenisTambang = $this->normalizedFilterValue($request->query('jenis_tambang'));

        if ($provinsi !== null) {
            $query->where("{$columnPrefix}nama_provinsi", $provinsi);
        }

        if ($jenisTambang !== null) {
            $query->where("{$columnPrefix}jenis_tambang", $jenisTambang);
        }
    }

    private function normalizedFilterValue(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function dashboardCacheKey(string $prefix, array $params = []): string
    {
        ksort($params);

        return 'dashboard:' . $prefix . ':' . sha1(json_encode($params));
    }

    private function dashboardCacheTtl(): \DateTimeInterface
    {
        return now()->addSeconds(30);
    }
}
