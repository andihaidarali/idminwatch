<?php

namespace App\Http\Controllers;

use App\Models\DetailTambang;
use App\Models\JenisTambangRef;
use App\Models\WilayahTambang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WilayahTambangController extends Controller
{
    /**
     * Display listing of all wilayah tambang
     */
    public function index(Request $request)
    {
        $tambangQuery = WilayahTambang::query()
            ->select([
                'id',
                'detail_tambang_id',
                'nomor_sk',
                'kegiatan',
                'nama_provinsi',
                'jenis_izin',
                'jenis_tambang',
                'luas_sk_ha',
                'luas_overlap',
                'status',
                'created_at',
            ])
            ->withExists('detailTambang')
            ->with([
                'detailTambang:id,nama_perusahaan',
                'jenisTambangRef:id,nama,nama_en',
            ])
            ->when($request->filled('provinsi'), fn ($query) => $query->where('nama_provinsi', $request->string('provinsi')->toString()))
            ->when($request->filled('kabupaten'), fn ($query) => $query->where('nama_kabupaten', $request->string('kabupaten')->toString()))
            ->when($request->filled('jenis_tambang'), fn ($query) => $query->where('jenis_tambang', $request->string('jenis_tambang')->toString()))
            ->when(
                $request->string('detail_status')->toString() === 'ada',
                fn ($query) => $query->whereHas('detailTambang')
            )
            ->when(
                $request->string('detail_status')->toString() === 'tidak_ada',
                fn ($query) => $query->whereDoesntHave('detailTambang')
            );

        $tambang = $tambangQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $provinsiOptions = WilayahTambang::query()
            ->whereNotNull('nama_provinsi')
            ->where('nama_provinsi', '!=', '')
            ->distinct()
            ->orderBy('nama_provinsi')
            ->pluck('nama_provinsi');

        $kabupatenOptions = WilayahTambang::query()
            ->whereNotNull('nama_kabupaten')
            ->where('nama_kabupaten', '!=', '')
            ->when($request->filled('provinsi'), fn ($query) => $query->where('nama_provinsi', $request->string('provinsi')->toString()))
            ->distinct()
            ->orderBy('nama_kabupaten')
            ->pluck('nama_kabupaten');

        $jenisTambangOptions = $this->commodityTypeOptions();

        return view('admin.wilayah-tambang.index', compact(
            'tambang',
            'provinsiOptions',
            'kabupatenOptions',
            'jenisTambangOptions'
        ));
    }

    /**
     * Show the GeoJSON upload form
     */
    public function create()
    {
        $commodityTypes = $this->commodityTypeOptions();

        return view('admin.wilayah-tambang.create', compact('commodityTypes'));
    }

    /**
     * Show detail of a single wilayah tambang
     */
    public function show(WilayahTambang $wilayahTambang)
    {
        $wilayahTambang->load(['detailTambang', 'jenisTambangRef']);
        $nameColumn = Schema::hasColumn('kawasan_hutan', 'deskripsi') ? 'deskripsi' : 'nama';
        $fungsiColumn = Schema::hasColumn('kawasan_hutan', 'fungsikws') ? 'fungsikws' : 'fungsi';

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
        ", [$wilayahTambang->id]);

        return view('admin.wilayah-tambang.show', compact('wilayahTambang', 'overlaps'));
    }

    /**
     * Edit form for wilayah tambang attributes (non-geometry)
     */
    public function edit(WilayahTambang $wilayahTambang)
    {
        $wilayahTambang->load(['detailTambang', 'jenisTambangRef']);
        $companies = DetailTambang::query()
            ->select(['id', 'nama_perusahaan'])
            ->orderBy('nama_perusahaan')
            ->get();
        $commodityTypes = $this->commodityTypeOptions();

        return view('admin.wilayah-tambang.edit', compact('wilayahTambang', 'companies', 'commodityTypes'));
    }

    /**
     * Update wilayah tambang attributes
     */
    public function update(Request $request, WilayahTambang $wilayahTambang)
    {
        $validated = $request->validate([
            'detail_tambang_id' => 'nullable|exists:detail_tambang,id',
            'nomor_sk' => 'nullable|string|max:255',
            'tanggal_berlaku' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_berlaku',
            'kegiatan' => 'nullable|string|max:255',
            'kegiatan_en' => 'nullable|string|max:255',
            'nama_provinsi' => 'nullable|string|max:255',
            'nama_kabupaten' => 'nullable|string|max:255',
            'lokasi' => 'nullable|string',
            'jenis_izin' => 'nullable|string|max:255',
            'jenis_tambang' => 'nullable|string|max:255',
            'jenis_tambang_en' => 'nullable|string|max:255',
            'new_jenis_tambang' => 'nullable|string|max:255',
            'new_jenis_tambang_en' => 'nullable|string|max:255',
            'status' => 'required|string|in:aktif,expired,ditangguhkan',
            'luas_sk_ha' => 'nullable|numeric|min:0',
            'dampak_sosial' => 'nullable|string',
            'dampak_sosial_en' => 'nullable|string',
            'dampak_ekonomi' => 'nullable|string',
            'dampak_ekonomi_en' => 'nullable|string',
            'dampak_lingkungan' => 'nullable|string',
            'dampak_lingkungan_en' => 'nullable|string',
            'dokumentasi' => 'nullable|array|max:5',
            'dokumentasi.*' => 'image|mimes:jpeg,png,jpg|max:5120',
            'remove_dokumentasi' => 'nullable|array',
        ]);

        $existingDok = $wilayahTambang->dokumentasi ?? [];
        if ($request->filled('remove_dokumentasi')) {
            foreach ($request->remove_dokumentasi as $path) {
                if (($key = array_search($path, $existingDok, true)) !== false) {
                    Storage::disk('public')->delete($path);
                    unset($existingDok[$key]);
                }
            }
            $existingDok = array_values($existingDok);
        }

        if ($request->hasFile('dokumentasi')) {
            $availableSlots = 5 - count($existingDok);
            $files = array_slice($request->file('dokumentasi'), 0, $availableSlots);

            foreach ($files as $file) {
                $existingDok[] = $file->store('dokumentasi', 'public');
            }
        }

        $validated['jenis_tambang'] = $this->resolveCommodityType(
            $request->input('jenis_tambang'),
            $request->input('jenis_tambang_en'),
            $request->input('new_jenis_tambang'),
            $request->input('new_jenis_tambang_en'),
        );
        unset(
            $validated['jenis_tambang_en'],
            $validated['new_jenis_tambang'],
            $validated['new_jenis_tambang_en']
        );

        $validated['dokumentasi'] = $existingDok;
        $wilayahTambang->update($validated);

        return redirect()->route('admin.wilayah-tambang.index')
            ->with('success', 'Wilayah tambang berhasil diperbarui.');
    }

    /**
     * Delete a wilayah tambang
     */
    public function destroy(WilayahTambang $wilayahTambang)
    {
        $wilayahTambang->delete();

        return redirect()->route('admin.wilayah-tambang.index')
            ->with('success', 'Wilayah tambang berhasil dihapus.');
    }

    /**
     * Handle GeoJSON file upload
     * Reads FeatureCollection, inserts each Feature into wilayah_tambang
     * The PostgreSQL trigger auto-calculates luas_overlap on INSERT
     */
    public function uploadGeojson(Request $request)
    {
        $request->validate([
            'geojson_file' => 'required|file|mimes:json,geojson|max:51200', // Max 50MB
            'nama_default' => 'nullable|string|max:255',
            'jenis_tambang' => 'nullable|string|max:255',
            'jenis_tambang_en' => 'nullable|string|max:255',
            'new_jenis_tambang' => 'nullable|string|max:255',
            'new_jenis_tambang_en' => 'nullable|string|max:255',
        ]);

        $file = $request->file('geojson_file');
        $content = file_get_contents($file->getRealPath());
        $geojson = json_decode($content, true);

        if (!$geojson || !isset($geojson['type'])) {
            return back()->with('error', 'File GeoJSON tidak valid.');
        }

        // Normalize: support both FeatureCollection and single Feature
        $features = [];
        if ($geojson['type'] === 'FeatureCollection') {
            $features = $geojson['features'] ?? [];
        } elseif ($geojson['type'] === 'Feature') {
            $features = [$geojson];
        } else {
            return back()->with('error', 'Format GeoJSON harus FeatureCollection atau Feature.');
        }

        if (empty($features)) {
            return back()->with('error', 'Tidak ada features dalam file GeoJSON.');
        }

        $imported = 0;
        $linkedCompanies = 0;
        $createdCompanies = 0;
        $errors = [];
        $selectedCommodityType = $this->resolveCommodityType(
            $request->input('jenis_tambang'),
            $request->input('jenis_tambang_en'),
            $request->input('new_jenis_tambang'),
            $request->input('new_jenis_tambang_en'),
        );

        DB::beginTransaction();

        try {
            foreach ($features as $index => $feature) {
                $geometry = $feature['geometry'] ?? null;
                $properties = $feature['properties'] ?? [];

                if (!$geometry) {
                    $errors[] = "Feature #{$index}: Tidak memiliki geometry.";
                    continue;
                }

                // Validate geometry type
                $geomType = strtoupper($geometry['type'] ?? '');
                if (!in_array($geomType, ['POLYGON', 'MULTIPOLYGON'])) {
                    $errors[] = "Feature #{$index}: Tipe geometry '{$geomType}' tidak didukung (harus Polygon/MultiPolygon).";
                    continue;
                }

                $geomJson = json_encode($geometry);

                $companyName = $this->normalizeImportedCompanyName(
                    $properties['nama_usaha'] ?? $properties['NAMA_USAHA'] ?? $request->input('nama_default')
                );

                [$detailTambangId, $wasCreated] = $this->resolveImportedCompany($companyName);

                if ($detailTambangId !== null) {
                    $linkedCompanies++;
                    if ($wasCreated) {
                        $createdCompanies++;
                    }
                }

                // Keep legacy nama filled for compatibility, but company identity now uses detail_tambang_id.
                $nama = $companyName ?: ('Tambang ' . ($index + 1));

                $nomorSk = $properties['sk_iup'] ?? $properties['nomor_sk'] ?? $properties['SK'] ?? $properties['sk']
                    ?? $properties['no_sk'] ?? $properties['NO_SK'] ?? null;
                $tanggalBerlaku = $this->normalizeGeojsonDate($properties['tgl_berlak'] ?? null);
                $tanggalBerakhir = $this->normalizeGeojsonDate($properties['tgl_akhir'] ?? null);
                $kegiatan = $properties['kegiatan'] ?? null;
                $namaProvinsi = $properties['nama_prov'] ?? null;
                $namaKabupaten = $properties['nama_kab'] ?? null;
                $lokasi = $properties['lokasi'] ?? null;
                $jenisIzin = $properties['jenis_izin'] ?? null;

                $luasSk = $properties['luas_sk'] ?? $properties['luas_sk_ha'] ?? $properties['luas'] ?? $properties['LUAS']
                    ?? $properties['area_ha'] ?? null;

                // Insert using raw SQL so the trigger fires properly
                // ST_GeomFromGeoJSON handles the geometry conversion
                // ST_Multi ensures we always store as MULTIPOLYGON
                DB::statement("
                    INSERT INTO wilayah_tambang (
                        public_uid,
                        detail_tambang_id,
                        nama,
                        nomor_sk,
                        tanggal_berlaku,
                        tanggal_berakhir,
                        kegiatan,
                        nama_provinsi,
                        nama_kabupaten,
                        lokasi,
                        jenis_izin,
                        jenis_tambang,
                        status,
                        luas_sk_ha,
                        geom,
                        created_at,
                        updated_at
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'aktif', ?, ST_Multi(ST_SetSRID(ST_GeomFromGeoJSON(?), 4326)), NOW(), NOW())
                ", [
                    (string) Str::lower((string) Str::ulid()),
                    $detailTambangId,
                    $nama,
                    $nomorSk,
                    $tanggalBerlaku,
                    $tanggalBerakhir,
                    $kegiatan,
                    $namaProvinsi,
                    $namaKabupaten,
                    $lokasi,
                    $jenisIzin,
                    $selectedCommodityType,
                    $luasSk,
                    $geomJson,
                ]);

                $imported++;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('GeoJSON upload error: ' . $e->getMessage());
            return back()->with('error', 'Gagal import GeoJSON: ' . $e->getMessage());
        }

        $message = "Berhasil mengimport {$imported} wilayah tambang.";
        if ($linkedCompanies > 0) {
            $message .= " {$linkedCompanies} wilayah terhubung ke perusahaan.";
        }
        if ($createdCompanies > 0) {
            $message .= " {$createdCompanies} perusahaan baru dibuat otomatis.";
        }
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' feature dilewati.';
        }

        return redirect()->route('admin.wilayah-tambang.index')
            ->with('success', $message)
            ->with('import_errors', $errors);
    }

    /**
     * Store — not used directly (use uploadGeojson instead)
     */
    public function store(Request $request)
    {
        return $this->uploadGeojson($request);
    }

    private function resolveImportedCompany(?string $companyName): array
    {
        if ($companyName === null) {
            return [null, false];
        }

        $existingCompany = DetailTambang::query()
            ->select(['id'])
            ->whereRaw('LOWER(TRIM(nama_perusahaan)) = ?', [Str::lower($companyName)])
            ->first();

        if ($existingCompany) {
            return [$existingCompany->id, false];
        }

        $company = DetailTambang::query()->create([
            'nama_perusahaan' => $companyName,
        ]);

        return [$company->id, true];
    }

    private function resolveCommodityType(
        mixed $selectedName,
        mixed $selectedNameEn,
        mixed $newName,
        mixed $newNameEn,
    ): ?string {
        $selectedName = $this->normalizeImportedCompanyName($selectedName);
        $selectedNameEn = $this->normalizeImportedCompanyName($selectedNameEn);
        $newName = $this->normalizeImportedCompanyName($newName);
        $newNameEn = $this->normalizeImportedCompanyName($newNameEn);

        $finalName = $newName ?: $selectedName;

        if ($finalName === null) {
            return null;
        }

        $finalNameEn = $newName ? $newNameEn : $selectedNameEn;
        $commodityType = JenisTambangRef::query()->firstOrNew([
            'nama' => $finalName,
        ]);

        if ($finalNameEn !== null) {
            $commodityType->nama_en = $finalNameEn;
        }

        $commodityType->save();

        return $finalName;
    }

    private function commodityTypeOptions()
    {
        $missingTypes = WilayahTambang::query()
            ->whereNotNull('jenis_tambang')
            ->where('jenis_tambang', '!=', '')
            ->whereNotIn('jenis_tambang', function ($query) {
                $query->select('nama')->from('jenis_tambang_refs');
            })
            ->distinct()
            ->pluck('jenis_tambang');

        foreach ($missingTypes as $missingType) {
            JenisTambangRef::query()->firstOrCreate([
                'nama' => $missingType,
            ]);
        }

        return JenisTambangRef::query()
            ->select(['nama', 'nama_en'])
            ->orderBy('nama')
            ->get();
    }

    private function normalizeImportedCompanyName(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $value !== '' ? $value : null;
    }

    private function normalizeGeojsonDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $formats = [
            'Y-m-d',
            'Y/m/d',
            'd/m/Y',
            'd-m-Y',
            'm/d/Y',
            'm-d-Y',
            'd.m.Y',
            'Ymd',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->format('Y-m-d');
            } catch (\Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
