<x-admin-layout title="Edit Perusahaan - Admin">
    <div class="mb-8">
        <a href="{{ route('detail-tambang.index') }}"
            class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Perusahaan</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $detailTambang->nama_perusahaan }}</p>
    </div>

    <form action="{{ route('detail-tambang.update', $detailTambang) }}" method="POST" class="space-y-8 w-full max-w-5xl">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                Profil Perusahaan
            </h3>
            <div class="space-y-4">
                <div>
                    <label for="nama_perusahaan" class="block text-xs text-gray-500 mb-2">Nama Perusahaan *</label>
                    <input type="text" name="nama_perusahaan" id="nama_perusahaan"
                        value="{{ old('nama_perusahaan', $detailTambang->nama_perusahaan) }}" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div class="grid grid-cols-1 xl:[grid-template-columns:minmax(0,1fr)_minmax(0,1fr)] gap-5 items-start">
                    <div class="min-w-0 overflow-hidden">
                        <label for="profil_singkat" class="block text-xs text-gray-500 mb-2">Profil Singkat Perusahaan (Bahasa Indonesia)</label>
                        <textarea name="profil_singkat" id="profil_singkat" rows="6"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('profil_singkat', $detailTambang->profil_singkat) }}</textarea>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="profil_singkat_en" class="block text-xs text-gray-500 mb-2">Short Company Profile (English)</label>
                        <textarea name="profil_singkat_en" id="profil_singkat_en" rows="6"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('profil_singkat_en', $detailTambang->profil_singkat_en) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                Keterhubungan Wilayah
            </h3>
            <p class="text-sm text-gray-400">
                Perusahaan ini saat ini terhubung ke
                <span class="font-semibold text-white">{{ number_format($detailTambang->wilayah_tambang_count) }}</span>
                wilayah tambang.
            </p>
            <p class="mt-2 text-xs text-gray-500">Assignment wilayah dilakukan dari halaman edit masing-masing wilayah tambang.</p>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                Simpan Perubahan
            </button>
            <a href="{{ route('detail-tambang.index') }}"
                class="px-8 py-3 text-sm text-gray-400 hover:text-white transition-colors">
                Batal
            </a>
        </div>
    </form>
</x-admin-layout>
