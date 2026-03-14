<x-admin-layout title="Wilayah Tambang - Admin">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Wilayah Tambang</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola data wilayah pertambangan — upload GeoJSON untuk menambahkan
                data spasial</p>
        </div>
        <a href="{{ route('admin.wilayah-tambang.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Upload GeoJSON
        </a>
    </div>

    {{-- Import Errors --}}
    @if(session('import_errors'))
        <div class="mb-6 p-4 bg-amber-500/10 border border-amber-500/20 rounded-xl">
            <p class="text-sm text-amber-400 font-medium mb-2">Peringatan saat import:</p>
            <ul class="list-disc list-inside text-xs text-amber-300/70 space-y-1">
                @foreach(session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.wilayah-tambang.index') }}"
        class="mb-6 rounded-2xl border border-gray-800/50 bg-gray-900/50 p-5">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-5">
            <div>
                <label for="provinsi" class="mb-2 block text-xs text-gray-500">Provinsi</label>
                <select name="provinsi" id="provinsi"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinsiOptions as $provinsi)
                        <option value="{{ $provinsi }}" @selected(request('provinsi') === $provinsi)>{{ $provinsi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="kabupaten" class="mb-2 block text-xs text-gray-500">Kabupaten</label>
                <select name="kabupaten" id="kabupaten"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Semua Kabupaten</option>
                    @foreach($kabupatenOptions as $kabupaten)
                        <option value="{{ $kabupaten }}" @selected(request('kabupaten') === $kabupaten)>{{ $kabupaten }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="jenis_tambang" class="mb-2 block text-xs text-gray-500">Jenis Tambang / Komoditas</label>
                <select name="jenis_tambang" id="jenis_tambang"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Semua Komoditas</option>
                    @foreach($jenisTambangOptions as $jenisTambang)
                        <option value="{{ $jenisTambang->nama }}" @selected(request('jenis_tambang') === $jenisTambang->nama)>{{ $jenisTambang->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="detail_status" class="mb-2 block text-xs text-gray-500">Status Detail</label>
                <select name="detail_status" id="detail_status"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    <option value="">Semua</option>
                    <option value="ada" @selected(request('detail_status') === 'ada')>Sudah Ada</option>
                    <option value="tidak_ada" @selected(request('detail_status') === 'tidak_ada')>Belum Ada</option>
                </select>
            </div>
            <div class="flex items-end gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-500">
                    Filter
                </button>
                <a href="{{ route('admin.wilayah-tambang.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-700/50 px-5 py-3 text-sm text-gray-300 transition hover:bg-gray-800/60 hover:text-white">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Perusahaan</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Nomor SK</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Kegiatan</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Provinsi</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Jenis Izin</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Jenis (Komoditas)</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Luas SK</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Overlap</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Detail</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Status</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse($tambang as $wt)
                        <tr class="hover:bg-gray-800/20 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-white">{{ optional($wt->detailTambang)->nama_perusahaan ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $wt->nomor_sk ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $wt->kegiatan ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $wt->nama_provinsi ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $wt->jenis_izin ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $wt->jenis_tambang ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">
                                {{ $wt->luas_sk_ha ? number_format($wt->luas_sk_ha, 2, ',', '.') . ' Ha' : '-' }}</td>
                            <td class="px-6 py-4">
                                @if($wt->luas_overlap > 0)
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-500/10 text-red-400 text-xs font-medium rounded-lg border border-red-500/20">
                                        ⚠️ {{ number_format($wt->luas_overlap, 4, ',', '.') }} Ha
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($wt->detail_tambang_exists)
                                    <span class="text-xs text-emerald-400">✓ Ada</span>
                                @else
                                    <a href="{{ route('admin.wilayah-tambang.edit', $wt) }}"
                                        class="text-xs text-amber-400 hover:text-amber-300 underline">+ Tautkan</a>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($wt->status === 'aktif')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-400 text-xs font-medium rounded-lg border border-emerald-500/20">Aktif</span>
                                @elseif($wt->status === 'expired')
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-gray-500/10 text-gray-400 text-xs font-medium rounded-lg border border-gray-500/20">Expired</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-amber-500/10 text-amber-400 text-xs font-medium rounded-lg border border-amber-500/20">{{ ucfirst($wt->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.wilayah-tambang.show', $wt) }}"
                                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all"
                                        title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.wilayah-tambang.edit', $wt) }}"
                                        class="p-2 text-gray-400 hover:text-amber-400 hover:bg-amber-400/10 rounded-lg transition-all"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.wilayah-tambang.destroy', $wt) }}" method="POST"
                                        onsubmit="return confirm('Hapus wilayah tambang ini beserta semua data terkait?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all"
                                            title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-700" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 21a9 9 0 100-18 9 9 0 000 18z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3.6 9h16.8M3.6 15h16.8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 3a15.3 15.3 0 014 9 15.3 15.3 0 01-4 9 15.3 15.3 0 01-4-9 15.3 15.3 0 014-9z" />
                                </svg>
                                <p class="text-gray-500 text-sm">Belum ada data wilayah tambang.</p>
                                <a href="{{ route('admin.wilayah-tambang.create') }}"
                                    class="inline-flex items-center gap-1 mt-3 text-sm text-emerald-400 hover:text-emerald-300">
                                    Upload GeoJSON pertama →
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tambang->hasPages())
            <div class="px-6 py-4 border-t border-gray-800/50">
                {{ $tambang->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
