<x-admin-layout title="Master Jenis Tambang - Admin">
    <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Master Jenis Tambang</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola satu sumber data komoditas untuk seluruh wilayah tambang, termasuk translasi English dan audit keterhubungan.</p>
        </div>
        <form action="{{ route('admin.jenis-tambang.index') }}" method="GET" class="w-full max-w-md">
            <label for="search" class="mb-2 block text-xs text-gray-500">Cari Jenis Tambang</label>
            <div class="flex gap-2">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="Cari nama ID atau EN..."
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                <button type="submit"
                    class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-4 py-3 text-sm font-medium text-emerald-400 transition hover:bg-emerald-500/20">
                    Cari
                </button>
            </div>
        </form>
    </div>

    @if($errors->has('jenis_tambang'))
        <div class="mb-6 rounded-xl border border-red-500/20 bg-red-500/10 p-4 text-sm text-red-400">
            {{ $errors->first('jenis_tambang') }}
        </div>
    @endif

    <div class="mb-8 rounded-2xl border border-gray-800/50 bg-gray-900/60 p-6 backdrop-blur-xl">
        <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-300">
            <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
            Tambah Jenis Tambang Baru
        </h3>
        <form action="{{ route('admin.jenis-tambang.store') }}" method="POST" class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
            @csrf
            <div>
                <label for="create-nama" class="mb-2 block text-xs text-gray-500">Nama (Bahasa Indonesia)</label>
                <input type="text" name="nama" id="create-nama" value="{{ old('nama') }}" required
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>
            <div>
                <label for="create-nama-en" class="mb-2 block text-xs text-gray-500">Translation (English)</label>
                <input type="text" name="nama_en" id="create-nama-en" value="{{ old('nama_en') }}"
                    class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-3 text-sm font-medium text-white shadow-lg shadow-emerald-500/20 transition-all hover:from-emerald-500 hover:to-teal-500 md:w-auto">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-800/50 bg-gray-900/60 backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Nama ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Nama EN</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Dipakai Wilayah</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Diperbarui</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse($commodityTypes as $commodityType)
                        <tr class="align-top transition-colors hover:bg-gray-800/20">
                            <td class="px-6 py-4" colspan="5">
                                <form action="{{ route('admin.jenis-tambang.update', $commodityType) }}" method="POST"
                                    class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1.2fr)_140px_140px_auto] lg:items-end">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <input type="hidden" name="page" value="{{ $commodityTypes->currentPage() }}">

                                    <div>
                                        <label for="nama-{{ $commodityType->id }}" class="mb-2 block text-xs text-gray-500">Nama (ID)</label>
                                        <input type="text" name="nama" id="nama-{{ $commodityType->id }}" value="{{ $commodityType->nama }}" required
                                            class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                    </div>

                                    <div>
                                        <label for="nama-en-{{ $commodityType->id }}" class="mb-2 block text-xs text-gray-500">Translation (EN)</label>
                                        <input type="text" name="nama_en" id="nama-en-{{ $commodityType->id }}" value="{{ $commodityType->nama_en }}"
                                            class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                    </div>

                                    <div>
                                        <p class="mb-2 text-xs text-gray-500">Dipakai</p>
                                        <div class="rounded-xl border border-gray-700/40 bg-gray-800/40 px-4 py-3 text-sm font-medium text-white">
                                            {{ number_format($commodityType->wilayah_tambang_count, 0, ',', '.') }} wilayah
                                        </div>
                                    </div>

                                    <div>
                                        <p class="mb-2 text-xs text-gray-500">Updated</p>
                                        <div class="rounded-xl border border-gray-700/40 bg-gray-800/40 px-4 py-3 text-sm text-gray-300">
                                            {{ $commodityType->updated_at?->format('d M Y') ?? '-' }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-end gap-2">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl px-4 py-3 text-sm text-amber-400 transition-all hover:bg-amber-400/10 hover:text-amber-300">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
                                </form>

                                <div class="mt-3 flex justify-end">
                                    <form action="{{ route('admin.jenis-tambang.destroy', $commodityType) }}" method="POST"
                                        onsubmit="return confirm('Hapus jenis tambang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm text-red-400 transition-all hover:bg-red-500/10 hover:text-red-300 {{ $commodityType->wilayah_tambang_count > 0 ? 'cursor-not-allowed opacity-50' : '' }}"
                                            @disabled($commodityType->wilayah_tambang_count > 0)>
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                Belum ada master jenis tambang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commodityTypes->hasPages())
            <div class="border-t border-gray-800/50 px-6 py-4">
                {{ $commodityTypes->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
