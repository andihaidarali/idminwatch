<x-admin-layout title="Perusahaan - {{ $detailTambang->nama_perusahaan }}">
    <div class="mb-8">
        <a href="{{ route('detail-tambang.index') }}"
            class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $detailTambang->nama_perusahaan }}</h1>
                <p class="text-sm text-gray-400 mt-1">Master perusahaan yang dapat dipakai di banyak wilayah tambang</p>
            </div>
            <a href="{{ route('detail-tambang.edit', $detailTambang) }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm font-medium rounded-xl hover:bg-amber-500/20 transition-all">
                Edit
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-6xl">
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                Profil Perusahaan
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Nama Perusahaan</p>
                    <p class="text-sm text-white mt-1">{{ $detailTambang->nama_perusahaan }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                        <div class="text-sm text-gray-300 leading-relaxed [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5">
                            {!! $detailTambang->profil_singkat ?: '-' !!}
                        </div>
                    </div>
                    <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                        <div class="text-sm text-gray-300 leading-relaxed [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5">
                            {!! $detailTambang->profil_singkat_en ?: '-' !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                Wilayah Terkait
            </h3>
            @if($detailTambang->wilayahTambang->isEmpty())
                <p class="text-sm text-gray-500">Belum ada wilayah tambang yang terhubung.</p>
            @else
                <div class="space-y-3">
                    @foreach($detailTambang->wilayahTambang as $wilayah)
                        <a href="{{ route('admin.wilayah-tambang.show', $wilayah) }}"
                            class="block rounded-xl border border-gray-700/40 bg-gray-800/30 p-4 transition hover:border-emerald-500/30 hover:bg-gray-800/50">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $wilayah->nomor_sk ?: 'Tanpa SK' }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $wilayah->nama_provinsi ?: '-' }} • {{ $wilayah->jenis_tambang ?: '-' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Overlap</p>
                                    <p class="text-sm font-semibold text-red-400">{{ number_format($wilayah->luas_overlap ?? 0, 2, ',', '.') }} Ha</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
