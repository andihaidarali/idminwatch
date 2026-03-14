<?php

namespace App\Http\Controllers;

use App\Models\JenisTambangRef;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminJenisTambangRefController extends Controller
{
    public function index(Request $request): View
    {
        $commodityTypes = JenisTambangRef::query()
            ->withCount('wilayahTambang')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = Str::lower(trim((string) $request->string('search')));

                $query->where(function ($subQuery) use ($search) {
                    $subQuery
                        ->whereRaw('LOWER(nama) LIKE ?', ["%{$search}%"])
                        ->orWhereRaw('LOWER(COALESCE(nama_en, \'\')) LIKE ?', ["%{$search}%"]);
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('admin.jenis-tambang.index', compact('commodityTypes'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        [$commodityType, $created] = $this->upsertCommodityType($request);

        if (!$request->expectsJson()) {
            return redirect()
                ->route('admin.jenis-tambang.index')
                ->with('success', $created
                    ? 'Jenis tambang berhasil ditambahkan.'
                    : 'Jenis tambang berhasil diperbarui.');
        }

        return response()->json([
            'data' => [
                'nama' => $commodityType->nama,
                'nama_en' => $commodityType->nama_en,
                'created' => $created,
            ],
        ]);
    }

    public function update(Request $request, JenisTambangRef $jenisTambang): RedirectResponse
    {
        [$nama, $namaEn] = $this->validatedCommodityPayload($request, $jenisTambang->id);

        DB::transaction(function () use ($jenisTambang, $nama, $namaEn): void {
            $previousName = $jenisTambang->nama;

            $jenisTambang->update([
                'nama' => $nama,
                'nama_en' => $namaEn,
            ]);

            if ($previousName !== $nama) {
                DB::table('wilayah_tambang')
                    ->where('jenis_tambang', $previousName)
                    ->update([
                        'jenis_tambang' => $nama,
                        'updated_at' => now(),
                    ]);
            }
        });

        return redirect()
            ->route('admin.jenis-tambang.index', $request->only('search', 'page'))
            ->with('success', 'Jenis tambang berhasil diperbarui.');
    }

    public function destroy(JenisTambangRef $jenisTambang): RedirectResponse
    {
        if ($jenisTambang->wilayahTambang()->exists()) {
            return redirect()
                ->route('admin.jenis-tambang.index')
                ->withErrors([
                    'jenis_tambang' => 'Jenis tambang tidak dapat dihapus karena masih terhubung dengan wilayah tambang.',
                ]);
        }

        $jenisTambang->delete();

        return redirect()
            ->route('admin.jenis-tambang.index')
            ->with('success', 'Jenis tambang berhasil dihapus.');
    }

    private function upsertCommodityType(Request $request): array
    {
        [$nama, $namaEn] = $this->validatedCommodityPayload($request, allowExisting: true);

        $commodityType = JenisTambangRef::query()
            ->whereRaw('LOWER(TRIM(nama)) = ?', [Str::lower($nama)])
            ->first();

        $created = false;

        if (!$commodityType) {
            $commodityType = JenisTambangRef::query()->create([
                'nama' => $nama,
                'nama_en' => $namaEn,
            ]);
            $created = true;
        } elseif ($namaEn !== null && $commodityType->nama_en !== $namaEn) {
            $commodityType->update([
                'nama_en' => $namaEn,
            ]);
        }

        return [$commodityType, $created];
    }

    private function validatedCommodityPayload(Request $request, ?int $ignoreId = null, bool $allowExisting = false): array
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nama_en' => 'nullable|string|max:255',
        ]);

        $nama = $this->normalizeValue($validated['nama']);
        $namaEn = $this->normalizeValue($validated['nama_en'] ?? null);

        $duplicateQuery = JenisTambangRef::query()
            ->whereRaw('LOWER(TRIM(nama)) = ?', [Str::lower($nama)]);

        if ($ignoreId !== null) {
            $duplicateQuery->where('id', '!=', $ignoreId);
        }

        if (!$allowExisting && $duplicateQuery->exists()) {
            throw ValidationException::withMessages([
                'nama' => 'Nama jenis tambang sudah digunakan.',
            ]);
        }

        return [$nama, $namaEn];
    }

    private function normalizeValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim(preg_replace('/\s+/', ' ', $value) ?? '');

        return $normalized !== '' ? $normalized : null;
    }
}
