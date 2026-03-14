<x-admin-layout title="{{ optional($wilayahTambang->detailTambang)->nama_perusahaan ?? ($wilayahTambang->nomor_sk ?: 'Wilayah Tambang') }} - Wilayah Tambang">
    <div class="mb-8">
        <a href="{{ route('admin.wilayah-tambang.index') }}"
            class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ optional($wilayahTambang->detailTambang)->nama_perusahaan ?? 'Belum ditautkan ke perusahaan' }}</h1>
                <p class="text-sm text-gray-400 mt-1">{{ $wilayahTambang->nomor_sk ?? 'Tanpa Nomor SK' }}</p>
            </div>
            <a href="{{ route('admin.wilayah-tambang.edit', $wilayahTambang) }}"
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
            @if($wilayahTambang->detailTambang)
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500">Nama Perusahaan</p>
                        <p class="text-sm text-white mt-1">{{ optional($wilayahTambang->detailTambang)->nama_perusahaan }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                            <div class="text-sm text-gray-300 leading-relaxed [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5">
                                {!! optional($wilayahTambang->detailTambang)->profil_singkat ?: '-' !!}
                            </div>
                        </div>
                        <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                            <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                            <div class="text-sm text-gray-300 leading-relaxed [&_ol]:list-decimal [&_ol]:pl-5 [&_p]:mb-2 [&_ul]:list-disc [&_ul]:pl-5">
                                {!! optional($wilayahTambang->detailTambang)->profil_singkat_en ?: '-' !!}
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-sm text-gray-500 mb-3">Wilayah ini belum ditautkan ke perusahaan.</p>
                    <a href="{{ route('detail-tambang.index') }}"
                        class="text-sm text-emerald-400 hover:text-emerald-300 underline">Kelola perusahaan</a>
                </div>
            @endif
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                Informasi Wilayah Tambang
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Nomor SK</span>
                    <span class="text-sm text-white">{{ $wilayahTambang->nomor_sk ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Tanggal Berlaku</span>
                    <span class="text-sm text-white">{{ optional($wilayahTambang->tanggal_berlaku)->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Tanggal Berakhir</span>
                    <span class="text-sm text-white">{{ optional($wilayahTambang->tanggal_berakhir)->format('d M Y') ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Nama Provinsi</span>
                    <span class="text-sm text-white">{{ $wilayahTambang->nama_provinsi ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Nama Kabupaten</span>
                    <span class="text-sm text-white">{{ $wilayahTambang->nama_kabupaten ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Jenis Izin</span>
                    <span class="text-sm text-white">{{ $wilayahTambang->jenis_izin ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Jenis Tambang</span>
                    <span class="text-sm text-white">{{ $wilayahTambang->jenis_tambang ?? '-' }}</span>
                </div>
                @if(optional($wilayahTambang->jenisTambangRef)->nama_en)
                    <div class="flex justify-between">
                        <span class="text-xs text-gray-500">Commodity (English)</span>
                        <span class="text-sm text-white">{{ $wilayahTambang->jenisTambangRef->nama_en }}</span>
                    </div>
                @endif
                <div>
                    <span class="text-xs text-gray-500">Lokasi</span>
                    <p class="mt-1 text-sm text-white">{{ $wilayahTambang->lokasi ?? '-' }}</p>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Status</span>
                    @if($wilayahTambang->status === 'aktif')
                        <span class="text-sm text-emerald-400 font-medium">Aktif</span>
                    @else
                        <span class="text-sm text-amber-400 font-medium">{{ ucfirst($wilayahTambang->status) }}</span>
                    @endif
                </div>
                <hr class="border-gray-800/50">
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Luas SK</span>
                    <span class="text-sm font-bold text-white">{{ $wilayahTambang->luas_sk_ha ? number_format($wilayahTambang->luas_sk_ha, 2, ',', '.') : '-' }} Ha</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Total Luas Overlap</span>
                    <span class="px-3 py-1 bg-red-500/10 border border-red-500/20 rounded-lg text-sm font-bold text-red-400">
                        {{ number_format($wilayahTambang->luas_overlap, 4, ',', '.') }} Ha
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                Kegiatan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                    <p class="text-sm text-gray-300">{{ $wilayahTambang->kegiatan ?: '-' }}</p>
                </div>
                <div class="rounded-xl border border-gray-700/40 bg-gray-800/30 p-4">
                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                    <p class="text-sm text-gray-300">{{ $wilayahTambang->kegiatan_en ?: '-' }}</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-red-400"></div>
                Rincian Overlap dengan Kawasan Hutan
            </h3>
            @if(count($overlaps) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($overlaps as $overlap)
                        <div class="bg-red-500/5 border border-red-500/10 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs text-gray-500">{{ $overlap->fungsi }}</span>
                                <span class="text-sm font-bold text-red-400">{{ number_format($overlap->luas_ha, 4, ',', '.') }} Ha</span>
                            </div>
                            @if($overlap->kawasan_nama)
                                <p class="text-xs text-gray-400">{{ $overlap->kawasan_nama }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-6">Tidak ada overlap dengan kawasan hutan.</p>
            @endif
        </div>

        @php
            $hasImpact =
                $wilayahTambang->dampak_lingkungan || $wilayahTambang->dampak_lingkungan_en ||
                $wilayahTambang->dampak_sosial || $wilayahTambang->dampak_sosial_en ||
                $wilayahTambang->dampak_ekonomi || $wilayahTambang->dampak_ekonomi_en;
        @endphp
        @if($hasImpact)
            <div class="lg:col-span-2 bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                    Data Dampak Per Wilayah
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @if($wilayahTambang->dampak_lingkungan || $wilayahTambang->dampak_lingkungan_en)
                        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4">
                            <h4 class="text-xs font-medium text-emerald-400 mb-2">Lingkungan</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_lingkungan ?: '-' !!}</div>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_lingkungan_en ?: '-' !!}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($wilayahTambang->dampak_sosial || $wilayahTambang->dampak_sosial_en)
                        <div class="bg-blue-500/5 border border-blue-500/10 rounded-xl p-4">
                            <h4 class="text-xs font-medium text-blue-400 mb-2">Sosial</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_sosial ?: '-' !!}</div>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_sosial_en ?: '-' !!}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($wilayahTambang->dampak_ekonomi || $wilayahTambang->dampak_ekonomi_en)
                        <div class="bg-amber-500/5 border border-amber-500/10 rounded-xl p-4">
                            <h4 class="text-xs font-medium text-amber-400 mb-2">Ekonomi</h4>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">Bahasa Indonesia</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_ekonomi ?: '-' !!}</div>
                                </div>
                                <div>
                                    <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-2">English</p>
                                    <div class="text-sm text-gray-300">{!! $wilayahTambang->dampak_ekonomi_en ?: '-' !!}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if(!empty($wilayahTambang->dokumentasi) && count($wilayahTambang->dokumentasi) > 0)
            <div class="lg:col-span-2 bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                    Dokumentasi Wilayah
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    @foreach($wilayahTambang->dokumentasi as $foto)
                        <a href="{{ Storage::url($foto) }}" target="_blank"
                            class="block relative w-full aspect-square rounded-xl overflow-hidden hover:ring-2 hover:ring-emerald-500 transition-all shadow-lg group">
                            <img src="{{ Storage::url($foto) }}" alt="Dokumentasi"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-admin-layout>
