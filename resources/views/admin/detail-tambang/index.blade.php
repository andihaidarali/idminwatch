<x-admin-layout title="Perusahaan - Admin">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Perusahaan</h1>
            <p class="text-sm text-gray-400 mt-1">Kelola profil perusahaan. Satu perusahaan dapat terhubung ke banyak wilayah tambang.</p>
        </div>
        <a href="{{ route('detail-tambang.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
            + Tambah Perusahaan
        </a>
    </div>

    <form action="{{ route('detail-tambang.index') }}" method="GET"
        class="mb-6 rounded-2xl border border-gray-800/50 bg-gray-900/50 p-5">
        <div class="flex flex-col gap-4 md:flex-row md:items-end">
            <div class="flex-1">
                <label for="search" class="mb-2 block text-xs text-gray-500">Cari Perusahaan / SK / Provinsi</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"
                    placeholder="Masukkan nama perusahaan, nomor SK, atau provinsi...">
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-5 py-3 text-sm font-medium text-white transition hover:bg-emerald-500">
                    Cari
                </button>
                <a href="{{ route('detail-tambang.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-700/50 px-5 py-3 text-sm text-gray-300 transition hover:bg-gray-800/60 hover:text-white">
                    Reset
                </a>
            </div>
        </div>
    </form>

    <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Perusahaan</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Profil Singkat</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jumlah Wilayah</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse($companies as $company)
                        <tr class="hover:bg-gray-800/20 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-white">{{ $company->nama_perusahaan }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-300 line-clamp-2">{{ \Illuminate\Support\Str::limit(trim(strip_tags(html_entity_decode($company->profil_singkat ?? ''))), 120) ?: '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400">
                                    {{ number_format($company->wilayah_tambang_count) }} wilayah
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('detail-tambang.show', $company) }}"
                                        class="p-2 text-gray-400 hover:text-white hover:bg-gray-700/50 rounded-lg transition-all"
                                        title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('detail-tambang.edit', $company) }}"
                                        class="p-2 text-gray-400 hover:text-amber-400 hover:bg-amber-400/10 rounded-lg transition-all"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('detail-tambang.destroy', $company) }}" method="POST"
                                        onsubmit="return confirm('Hapus perusahaan ini?')">
                                        @csrf
                                        @method('DELETE')
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
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                Belum ada perusahaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="px-6 py-4 border-t border-gray-800/50">
                {{ $companies->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
