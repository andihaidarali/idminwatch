<x-admin-layout title="Upload GeoJSON Wilayah Tambang - Admin">
    <div class="mb-8">
        <a href="{{ route('admin.wilayah-tambang.index') }}"
            class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Upload GeoJSON Wilayah Tambang</h1>
        <p class="text-sm text-gray-400 mt-1">Upload file GeoJSON berisi polygon wilayah pertambangan. Overlap dengan
            kawasan hutan akan dihitung otomatis.</p>
    </div>

    <form action="{{ route('admin.wilayah-tambang.upload-geojson') }}" method="POST" enctype="multipart/form-data"
        class="space-y-8 max-w-3xl">
        @csrf

        {{-- Errors --}}
        @if($errors->any())
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
                <ul class="list-disc list-inside text-sm text-red-400 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400">
                {{ session('error') }}
            </div>
        @endif

        {{-- File Upload --}}
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                File GeoJSON
            </h3>

            <div class="space-y-4">
                {{-- Drag & Drop Area --}}
                <div id="drop-zone"
                    class="relative border-2 border-dashed border-gray-700/50 rounded-xl p-8 text-center hover:border-emerald-500/50 transition-colors cursor-pointer">
                    <input type="file" name="geojson_file" id="geojson_file" accept=".json,.geojson" required
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div id="drop-content">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-sm text-gray-400 mb-1">Drag & drop file GeoJSON atau <span
                                class="text-emerald-400 underline">klik untuk browse</span></p>
                        <p class="text-xs text-gray-600">Format: .json atau .geojson (Max: 50MB)</p>
                    </div>
                    <div id="file-selected" class="hidden">
                        <svg class="w-10 h-10 mx-auto mb-3 text-emerald-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p id="file-name" class="text-sm text-emerald-400 font-medium"></p>
                        <p id="file-size" class="text-xs text-gray-500 mt-1"></p>
                    </div>
                </div>

                {{-- Info Box --}}
                <div class="bg-blue-500/5 border border-blue-500/10 rounded-xl p-4">
                    <p class="text-xs text-blue-400/80 font-medium mb-2">ℹ️ Format GeoJSON yang didukung:</p>
                    <ul class="text-xs text-gray-400 space-y-1 list-disc list-inside ml-1">
                        <li>Type: <code class="text-blue-300">FeatureCollection</code> atau single <code
                                class="text-blue-300">Feature</code></li>
                        <li>Geometry: <code class="text-blue-300">Polygon</code> atau <code
                                class="text-blue-300">MultiPolygon</code></li>
                        <li>CRS: <code class="text-blue-300">EPSG:4326</code> (WGS84)</li>
                        <li>Properties yang dibaca otomatis: <code class="text-blue-300">nama_usaha</code>, <code
                                class="text-blue-300">sk_iup</code>, <code
                                class="text-blue-300">luas_sk</code>, <code
                                class="text-blue-300">tgl_berlak</code>, <code
                                class="text-blue-300">tgl_akhir</code>, <code
                                class="text-blue-300">kegiatan</code>, <code
                                class="text-blue-300">nama_prov</code>, <code
                                class="text-blue-300">nama_kab</code>, <code
                                class="text-blue-300">lokasi</code>, <code
                                class="text-blue-300">jenis_izin</code></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Default Values --}}
        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                Default Values (Opsional)
            </h3>
            <p class="text-xs text-gray-500 mb-4">Jika <code class="text-amber-300">nama_usaha</code> ditemukan,
                sistem akan mencari perusahaan dengan nama yang sama lalu langsung menghubungkannya. Jika belum ada,
                sistem akan membuat perusahaan baru otomatis. Anda juga bisa memilih jenis tambang yang sudah ada,
                memperbarui translasi English-nya, atau menambahkan jenis baru. Nilai default dipakai jika property tersebut tidak tersedia.
            </p>

            <div class="space-y-5">
                <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] gap-5 items-start">
                    <div class="rounded-xl border border-gray-800/50 bg-gray-950/30 p-5">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500 mb-4">Default Perusahaan</p>
                        <label for="nama_default" class="block text-xs text-gray-500 mb-2">Nama Perusahaan Default</label>
                        <input type="text" name="nama_default" id="nama_default" value="{{ old('nama_default') }}"
                            placeholder="Dipakai jika property nama_usaha tidak ada"
                            class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <p class="mt-3 text-xs text-gray-500">Nilai ini hanya dipakai sebagai fallback saat GeoJSON tidak memiliki <code class="text-amber-300">nama_usaha</code>.</p>
                    </div>

                    <div class="rounded-xl border border-gray-800/50 bg-gray-950/30 p-5 space-y-4">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-gray-500">Default Komoditas</p>
                        <div>
                            <label for="jenis_tambang" class="block text-xs text-gray-500 mb-2">Jenis Tambang</label>
                            <select name="jenis_tambang" id="jenis_tambang" class="hidden" aria-hidden="true" tabindex="-1">
                                <option value="">-- Pilih --</option>
                                @foreach($commodityTypes as $commodityType)
                                    <option value="{{ $commodityType->nama }}" data-translation="{{ $commodityType->nama_en }}"
                                        @selected(old('jenis_tambang') == $commodityType->nama)>{{ $commodityType->nama }}</option>
                                @endforeach
                            </select>
                            <div class="relative">
                                <input type="text" id="jenis_tambang_combobox"
                                    placeholder="Cari atau pilih satu jenis tambang..."
                                    autocomplete="off"
                                    class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                                <div id="jenis_tambang_options"
                                    class="absolute left-0 right-0 top-[calc(100%+0.5rem)] z-30 hidden max-h-64 overflow-y-auto rounded-xl border border-gray-700/60 bg-slate-900/95 p-2 shadow-2xl shadow-black/30 backdrop-blur">
                                </div>
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Satu wilayah tambang hanya dapat memiliki satu jenis tambang.</p>
                        </div>

                        <div>
                            <label for="jenis_tambang_en" class="block text-xs text-gray-500 mb-2">Commodity Translation (English)</label>
                            <input type="text" name="jenis_tambang_en" id="jenis_tambang_en" value="{{ old('jenis_tambang_en') }}"
                                placeholder="Otomatis mengikuti jenis tambang terpilih"
                                readonly
                                class="w-full cursor-not-allowed px-4 py-3 bg-gray-900/70 border border-gray-700/50 rounded-xl text-sm text-gray-400 focus:outline-none">
                            <p class="mt-2 text-xs text-gray-500">Field ini diisi otomatis dari master jenis tambang atau dari modal tambah/edit komoditas.</p>
                        </div>
                    </div>
                </div>

                @include('admin.wilayah-tambang.partials.commodity-modal')
            </div>
        </div>

        {{-- Overlap Info --}}
        <div class="bg-emerald-500/5 border border-emerald-500/10 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="text-sm text-emerald-400 font-medium">Perhitungan Overlap Otomatis</p>
                    <p class="text-xs text-gray-400 mt-1">Setelah upload, sistem akan otomatis menghitung luas overlap
                        (dalam Hektar) antara setiap polygon tambang dengan kawasan hutan yang ada di database
                        menggunakan fungsi spasial <code class="text-emerald-300">ST_Intersection</code>.</p>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                Upload & Import
            </button>
            <a href="{{ route('admin.wilayah-tambang.index') }}"
                class="px-8 py-3 text-sm text-gray-400 hover:text-white transition-colors">
                Batal
            </a>
        </div>
    </form>

    @push('scripts')
        <script>
            // File input UI feedback
            const fileInput = document.getElementById('geojson_file');
            const dropContent = document.getElementById('drop-content');
            const fileSelected = document.getElementById('file-selected');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    const file = e.target.files[0];
                    dropContent.classList.add('hidden');
                    fileSelected.classList.remove('hidden');
                    fileName.textContent = file.name;
                    fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                } else {
                    dropContent.classList.remove('hidden');
                    fileSelected.classList.add('hidden');
                }
            });
        </script>
    @endpush
</x-admin-layout>
