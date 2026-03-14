<x-admin-layout title="Kelola Halaman About - Admin">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Halaman About</h1>
        <p class="mt-1 text-sm text-gray-400">Konten halaman About publik dapat diedit dari sini.</p>
    </div>

    <form action="{{ route('admin.about.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        @if($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4">
                <ul class="list-inside list-disc space-y-1 text-sm text-red-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-2 xl:items-start">
            <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-800/50 bg-gray-900/60 p-6 backdrop-blur-xl">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-300">
                    <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                    Konten About Bahasa Indonesia
                </h3>

                <div class="space-y-5">
                    <div>
                        <label for="title" class="mb-2 block text-xs text-gray-500">Judul Halaman</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $aboutPage->title) }}" required
                            class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="content" class="mb-2 block text-xs text-gray-500">Isi Halaman</label>
                        <textarea name="content" id="content" rows="12"
                            class="rich-editor w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('content', $aboutPage->content) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="min-w-0 overflow-hidden rounded-2xl border border-gray-800/50 bg-gray-900/60 p-6 backdrop-blur-xl">
                <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-300">
                    <div class="h-2 w-2 rounded-full bg-sky-400"></div>
                    English Version
                </h3>

                <div class="space-y-5">
                    <div>
                        <label for="title_en" class="mb-2 block text-xs text-gray-500">Page Title</label>
                        <input type="text" name="title_en" id="title_en" value="{{ old('title_en', $aboutPage->title_en) }}"
                            class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                    </div>
                    <div class="min-w-0 overflow-hidden">
                        <label for="content_en" class="mb-2 block text-xs text-gray-500">Page Content</label>
                        <textarea name="content_en" id="content_en" rows="12"
                            class="rich-editor w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 resize-none">{{ old('content_en', $aboutPage->content_en) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-medium text-white shadow-lg shadow-emerald-500/20 transition-all hover:from-emerald-500 hover:to-teal-500">
                Simpan Halaman About
            </button>
            <a href="{{ route('about') }}" target="_blank"
                class="px-8 py-3 text-sm text-gray-400 transition-colors hover:text-white">
                Lihat Halaman Publik
            </a>
        </div>
    </form>
</x-admin-layout>
