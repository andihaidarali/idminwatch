<x-admin-layout title="Tambah User Admin - Admin">
    <div class="mb-8">
        <a href="{{ route('admin.users.index') }}"
            class="mb-4 inline-flex items-center gap-1 text-sm text-gray-400 transition-colors hover:text-white">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold text-white">Tambah User Admin</h1>
        <p class="mt-1 text-sm text-gray-400">Akun yang dibuat dari halaman ini otomatis memiliki role admin.</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="max-w-3xl space-y-8">
        @csrf

        @if($errors->any())
            <div class="rounded-xl border border-red-500/20 bg-red-500/10 p-4">
                <ul class="list-inside list-disc space-y-1 text-sm text-red-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-2xl border border-gray-800/50 bg-gray-900/60 p-6 backdrop-blur-xl">
            <h3 class="mb-4 flex items-center gap-2 text-sm font-semibold text-gray-300">
                <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                Informasi User
            </h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-xs text-gray-500">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="email" class="mb-2 block text-xs text-gray-500">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="password" class="mb-2 block text-xs text-gray-500">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-xs text-gray-500">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full rounded-xl border border-gray-700/50 bg-gray-800/50 px-4 py-3 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-8 py-3 text-sm font-medium text-white shadow-lg shadow-emerald-500/20 transition-all hover:from-emerald-500 hover:to-teal-500">
                Buat User Admin
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-8 py-3 text-sm text-gray-400 transition-colors hover:text-white">
                Batal
            </a>
        </div>
    </form>
</x-admin-layout>
