<x-admin-layout title="Edit Wilayah Tambang - Admin">
    <div class="mb-8">
        <a href="{{ route('admin.wilayah-tambang.index') }}"
            class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Wilayah Tambang</h1>
        <p class="text-sm text-gray-400 mt-1">{{ optional($wilayahTambang->detailTambang)->nama_perusahaan ?? ($wilayahTambang->nomor_sk ?: 'Wilayah Tambang') }}</p>
    </div>

    <form action="{{ route('admin.wilayah-tambang.update', $wilayahTambang) }}" method="POST" enctype="multipart/form-data"
        class="space-y-8 max-w-6xl">
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
                Perusahaan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-[minmax(0,1fr)_auto] gap-4 items-end">
                <div>
                    <label for="detail_tambang_id" class="block text-xs text-gray-500 mb-2">Perusahaan</label>
                    <select name="detail_tambang_id" id="detail_tambang_id"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <option value="">-- Belum ditautkan --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" @selected((string) old('detail_tambang_id', $wilayahTambang->detail_tambang_id) === (string) $company->id)>
                                {{ $company->nama_perusahaan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <a href="{{ route('detail-tambang.create') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-5 py-3 text-sm font-medium text-emerald-400 transition hover:bg-emerald-500/20">
                    + Tambah Perusahaan
                </a>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-orange-400"></div>
                Informasi Izin dan Wilayah
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nomor_sk" class="block text-xs text-gray-500 mb-2">Nomor SK</label>
                    <input type="text" name="nomor_sk" id="nomor_sk"
                        value="{{ old('nomor_sk', $wilayahTambang->nomor_sk) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="tanggal_berlaku" class="block text-xs text-gray-500 mb-2">Tanggal Berlaku</label>
                    <input type="date" name="tanggal_berlaku" id="tanggal_berlaku"
                        value="{{ old('tanggal_berlaku', optional($wilayahTambang->tanggal_berlaku)->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="tanggal_berakhir" class="block text-xs text-gray-500 mb-2">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" id="tanggal_berakhir"
                        value="{{ old('tanggal_berakhir', optional($wilayahTambang->tanggal_berakhir)->format('Y-m-d')) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="nama_provinsi" class="block text-xs text-gray-500 mb-2">Nama Provinsi</label>
                    <input type="text" name="nama_provinsi" id="nama_provinsi"
                        value="{{ old('nama_provinsi', $wilayahTambang->nama_provinsi) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="nama_kabupaten" class="block text-xs text-gray-500 mb-2">Nama Kabupaten</label>
                    <input type="text" name="nama_kabupaten" id="nama_kabupaten"
                        value="{{ old('nama_kabupaten', $wilayahTambang->nama_kabupaten) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="jenis_izin" class="block text-xs text-gray-500 mb-2">Jenis Izin</label>
                    <input type="text" name="jenis_izin" id="jenis_izin"
                        value="{{ old('jenis_izin', $wilayahTambang->jenis_izin) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="jenis_tambang" class="block text-xs text-gray-500 mb-2">Jenis Tambang</label>
                    <select name="jenis_tambang" id="jenis_tambang" class="hidden" aria-hidden="true" tabindex="-1">
                        <option value="">-- Pilih --</option>
                        @foreach($commodityTypes as $jenis)
                            <option value="{{ $jenis->nama }}" data-translation="{{ $jenis->nama_en }}"
                                @selected(old('jenis_tambang', $wilayahTambang->jenis_tambang) === $jenis->nama)>{{ $jenis->nama }}</option>
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
                    <input type="text" name="jenis_tambang_en" id="jenis_tambang_en"
                        value="{{ old('jenis_tambang_en', optional($wilayahTambang->jenisTambangRef)->nama_en) }}"
                        placeholder="Terjemahan untuk jenis tambang terpilih"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                @include('admin.wilayah-tambang.partials.commodity-modal')
                <div>
                    <label for="status" class="block text-xs text-gray-500 mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                        <option value="aktif" @selected(old('status', $wilayahTambang->status) === 'aktif')>Aktif</option>
                        <option value="expired" @selected(old('status', $wilayahTambang->status) === 'expired')>Expired</option>
                        <option value="ditangguhkan" @selected(old('status', $wilayahTambang->status) === 'ditangguhkan')>Ditangguhkan</option>
                    </select>
                </div>
                <div>
                    <label for="luas_sk_ha" class="block text-xs text-gray-500 mb-2">Luas SK (Ha)</label>
                    <input type="number" step="0.01" name="luas_sk_ha" id="luas_sk_ha"
                        value="{{ old('luas_sk_ha', $wilayahTambang->luas_sk_ha) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div class="md:col-span-2">
                    <label for="lokasi" class="block text-xs text-gray-500 mb-2">Lokasi</label>
                    <textarea name="lokasi" id="lokasi" rows="3"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-y">{{ old('lokasi', $wilayahTambang->lokasi) }}</textarea>
                </div>
                <div class="flex items-end">
                    <div class="bg-red-500/10 rounded-xl p-4 border border-red-500/20 w-full">
                        <p class="text-xs text-red-400/70 mb-1">Luas Overlap (otomatis)</p>
                        <p class="text-sm font-bold text-red-400">{{ number_format($wilayahTambang->luas_overlap, 4, ',', '.') }} Ha</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-cyan-400"></div>
                Kegiatan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="kegiatan" class="block text-xs text-gray-500 mb-2">Kegiatan (Bahasa Indonesia)</label>
                    <input type="text" name="kegiatan" id="kegiatan"
                        value="{{ old('kegiatan', $wilayahTambang->kegiatan) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="kegiatan_en" class="block text-xs text-gray-500 mb-2">Activity (English)</label>
                    <input type="text" name="kegiatan_en" id="kegiatan_en"
                        value="{{ old('kegiatan_en', $wilayahTambang->kegiatan_en) }}"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                Data Dampak Per Wilayah
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 xl:[grid-template-columns:minmax(0,1fr)_minmax(0,1fr)] gap-5 items-start">
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_lingkungan" class="block text-xs text-gray-500 mb-2">Dampak Lingkungan (Bahasa Indonesia)</label>
                        <textarea name="dampak_lingkungan" id="dampak_lingkungan" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_lingkungan', $wilayahTambang->dampak_lingkungan) }}</textarea>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_lingkungan_en" class="block text-xs text-gray-500 mb-2">Environmental Impact (English)</label>
                        <textarea name="dampak_lingkungan_en" id="dampak_lingkungan_en" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_lingkungan_en', $wilayahTambang->dampak_lingkungan_en) }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:[grid-template-columns:minmax(0,1fr)_minmax(0,1fr)] gap-5 items-start">
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_sosial" class="block text-xs text-gray-500 mb-2">Dampak Sosial (Bahasa Indonesia)</label>
                        <textarea name="dampak_sosial" id="dampak_sosial" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_sosial', $wilayahTambang->dampak_sosial) }}</textarea>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_sosial_en" class="block text-xs text-gray-500 mb-2">Social Impact (English)</label>
                        <textarea name="dampak_sosial_en" id="dampak_sosial_en" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_sosial_en', $wilayahTambang->dampak_sosial_en) }}</textarea>
                    </div>
                </div>
                <div class="grid grid-cols-1 xl:[grid-template-columns:minmax(0,1fr)_minmax(0,1fr)] gap-5 items-start">
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_ekonomi" class="block text-xs text-gray-500 mb-2">Dampak Ekonomi (Bahasa Indonesia)</label>
                        <textarea name="dampak_ekonomi" id="dampak_ekonomi" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_ekonomi', $wilayahTambang->dampak_ekonomi) }}</textarea>
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="dampak_ekonomi_en" class="block text-xs text-gray-500 mb-2">Economic Impact (English)</label>
                        <textarea name="dampak_ekonomi_en" id="dampak_ekonomi_en" rows="4"
                            class="rich-editor w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('dampak_ekonomi_en', $wilayahTambang->dampak_ekonomi_en) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-indigo-400"></div>
                Dokumentasi Wilayah
            </h3>

            @if(!empty($wilayahTambang->dokumentasi))
                <div class="mb-6">
                    <p class="text-xs text-gray-500 mb-3">Foto Tersimpan (Pilih untuk menghapus)</p>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        @foreach($wilayahTambang->dokumentasi as $foto)
                            <label class="block relative group cursor-pointer">
                                <input type="checkbox" name="remove_dokumentasi[]" value="{{ $foto }}" class="peer sr-only">
                                <div class="relative w-full aspect-square rounded-xl overflow-hidden border-2 border-transparent peer-checked:border-red-500 peer-checked:opacity-50 transition-all">
                                    <img src="{{ Storage::url($foto) }}" class="w-full h-full object-cover" alt="Dokumentasi">
                                    <div class="absolute inset-0 bg-red-500/20 items-center justify-center opacity-0 peer-checked:opacity-100 flex transition-opacity">
                                        <svg class="w-8 h-8 text-red-500 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-red-400 mt-2">* Foto yang dipilih akan dihapus saat form disimpan.</p>
                </div>
            @endif

            <div>
                @php
                    $existingCount = is_array($wilayahTambang->dokumentasi) ? count($wilayahTambang->dokumentasi) : 0;
                    $remaining = 5 - $existingCount;
                @endphp

                @if($remaining > 0)
                    <label for="dokumentasi" class="block text-xs text-gray-500 mb-2">Upload Foto Baru (Sisa slot: {{ $remaining }} Foto, Max @5MB)</label>
                    <input type="file" name="dokumentasi[]" id="dokumentasi" multiple
                        accept="image/jpeg,image/png,image/jpg"
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20">
                    <p class="mt-2 text-xs text-gray-500">Format: JPG, JPEG, PNG.</p>
                @else
                    <div class="p-4 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-sm rounded-xl">
                        Slot foto sudah penuh (5/5). Hapus foto yang ada untuk mengunggah foto baru.
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="px-8 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.wilayah-tambang.index') }}"
                class="px-8 py-3 text-sm text-gray-400 hover:text-white transition-colors">
                Batal
            </a>
        </div>
    </form>
</x-admin-layout>
