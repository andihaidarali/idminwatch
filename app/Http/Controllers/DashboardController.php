<?php

namespace App\Http\Controllers;

use App\Models\WilayahTambang;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function show(): View
    {
        return view('dashboard', $this->buildViewData());
    }

    public function showShared(string $publicUid): View
    {
        $wilayahTambang = WilayahTambang::query()
            ->select([
                'id',
                'public_uid',
                'detail_tambang_id',
                'nomor_sk',
                'dokumentasi',
            ])
            ->with(['detailTambang:id,nama_perusahaan,profil_singkat,profil_singkat_en'])
            ->where('public_uid', $publicUid)
            ->firstOrFail();

        return view('dashboard', $this->buildViewData($wilayahTambang));
    }

    private function buildViewData(?WilayahTambang $wilayahTambang = null): array
    {
        $defaultTitle = 'Indonesia Mining Watch - Dashboard';
        $defaultDescription = 'WebGIS pemantauan wilayah pertambangan - Indonesia Mining Watch.';

        if (!$wilayahTambang) {
            return [
                'sharedTambangUid' => null,
                'pageTitle' => $defaultTitle,
                'metaDescription' => $defaultDescription,
                'canonicalUrl' => route('dashboard'),
                'defaultDashboardTitle' => $defaultTitle,
                'defaultDashboardDescription' => $defaultDescription,
                'ogImage' => null,
            ];
        }

        $detailTambang = $wilayahTambang->detailTambang;
        $profilSingkat = $detailTambang?->profil_singkat ?: $detailTambang?->profil_singkat_en;
        $description = $profilSingkat
            ? Str::limit(trim(strip_tags(html_entity_decode($profilSingkat))), 160)
            : 'Informasi wilayah tambang pada dashboard Indonesia Mining Watch.';
        $titleSource = $detailTambang?->nama_perusahaan ?: ($wilayahTambang->nomor_sk ?: 'Wilayah Tambang');
        $title = "Indonesia Mining Watch - {$titleSource}";
        $ogImage = $this->resolveOgImage($wilayahTambang->dokumentasi);

        return [
            'sharedTambangUid' => $wilayahTambang->public_uid,
            'pageTitle' => $title,
            'metaDescription' => $description,
            'canonicalUrl' => route('dashboard.shared', $wilayahTambang->public_uid),
            'defaultDashboardTitle' => $defaultTitle,
            'defaultDashboardDescription' => $defaultDescription,
            'ogImage' => $ogImage,
        ];
    }

    private function resolveOgImage(mixed $dokumentasi): ?string
    {
        if (is_string($dokumentasi)) {
            $decoded = json_decode($dokumentasi, true);
            $dokumentasi = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($dokumentasi) || $dokumentasi === []) {
            return null;
        }

        foreach ($dokumentasi as $path) {
            if (is_string($path) && $path !== '') {
                return url(Storage::url($path));
            }
        }

        return null;
    }
}
