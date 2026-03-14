<?php

namespace App\Http\Controllers;

use App\Models\DetailTambang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DetailTambangController extends Controller
{
    /**
     * Display a listing of company profiles.
     */
    public function index(Request $request): View
    {
        $query = DetailTambang::query()
            ->select([
                'id',
                'nama_perusahaan',
                'profil_singkat',
                'profil_singkat_en',
                'created_at',
            ])
            ->withCount('wilayahTambang')
            ->orderBy('nama_perusahaan');

        if ($request->filled('search')) {
            $search = strtolower(trim((string) $request->search));

            $query->where(function ($subQuery) use ($search) {
                $subQuery
                    ->whereRaw('LOWER(nama_perusahaan) LIKE ?', ["%{$search}%"])
                    ->orWhereHas('wilayahTambang', function ($wilayahQuery) use ($search) {
                        $wilayahQuery
                            ->whereRaw('LOWER(nomor_sk) LIKE ?', ["%{$search}%"])
                            ->orWhereRaw('LOWER(nama_provinsi) LIKE ?', ["%{$search}%"]);
                    });
            });
        }

        $companies = $query
            ->paginate(10)
            ->withQueryString();

        return view('admin.detail-tambang.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company profile.
     */
    public function create(): View
    {
        return view('admin.detail-tambang.create');
    }

    /**
     * Store a newly created company profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'profil_singkat' => 'nullable|string',
            'profil_singkat_en' => 'nullable|string',
        ]);

        DetailTambang::create($validated);

        return redirect()
            ->route('detail-tambang.index')
            ->with('success', 'Perusahaan berhasil ditambahkan.');
    }

    /**
     * Display the specified company profile.
     */
    public function show(DetailTambang $detailTambang): View
    {
        $detailTambang->load([
            'wilayahTambang' => function ($query) {
                $query->select([
                    'id',
                    'detail_tambang_id',
                    'public_uid',
                    'nomor_sk',
                    'nama_provinsi',
                    'jenis_tambang',
                    'status',
                    'luas_sk_ha',
                    'luas_overlap',
                ])->orderByDesc('created_at');
            },
        ]);

        return view('admin.detail-tambang.show', compact('detailTambang'));
    }

    /**
     * Show the form for editing the specified company profile.
     */
    public function edit(DetailTambang $detailTambang): View
    {
        $detailTambang->loadCount('wilayahTambang');

        return view('admin.detail-tambang.edit', compact('detailTambang'));
    }

    /**
     * Update the specified company profile.
     */
    public function update(Request $request, DetailTambang $detailTambang): RedirectResponse
    {
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'profil_singkat' => 'nullable|string',
            'profil_singkat_en' => 'nullable|string',
        ]);

        $detailTambang->update($validated);

        return redirect()
            ->route('detail-tambang.index')
            ->with('success', 'Perusahaan berhasil diperbarui.');
    }

    /**
     * Remove the specified company profile.
     */
    public function destroy(DetailTambang $detailTambang): RedirectResponse
    {
        if ($detailTambang->wilayahTambang()->exists()) {
            return redirect()
                ->route('detail-tambang.index')
                ->withErrors([
                    'detail_tambang' => 'Perusahaan tidak dapat dihapus karena masih terhubung dengan wilayah tambang.',
                ]);
        }

        $detailTambang->delete();

        return redirect()
            ->route('detail-tambang.index')
            ->with('success', 'Perusahaan berhasil dihapus.');
    }
}
