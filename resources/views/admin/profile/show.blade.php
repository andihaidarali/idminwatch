<x-admin-layout title="Profil Admin - Indonesia Mining Watch">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Profil Admin</h1>
        <p class="text-sm text-gray-400 mt-1">Kelola akun dan ubah password admin.</p>
    </div>

    @if($errors->any())
        <div class="mb-6 max-w-3xl p-4 bg-red-500/10 border border-red-500/20 rounded-xl">
            <ul class="list-disc list-inside text-sm text-red-400 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-5xl">
        <div class="lg:col-span-1 bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                Informasi Akun
            </h2>

            <div class="space-y-3 text-sm">
                <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                    <p class="text-xs text-gray-500 mb-1">Nama</p>
                    <p class="text-gray-100 font-medium">{{ $user->name }}</p>
                </div>
                <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                    <p class="text-xs text-gray-500 mb-1">Email</p>
                    <p class="text-gray-100 font-medium break-all">{{ $user->email }}</p>
                </div>
                <div class="bg-gray-800/40 rounded-xl p-4 border border-gray-700/30">
                    <p class="text-xs text-gray-500 mb-1">Terdaftar Sejak</p>
                    <p class="text-gray-100 font-medium">{{ $user->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-gray-900/60 backdrop-blur-xl border border-gray-800/50 rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                Ganti Password
            </h2>

            <form method="POST" action="{{ route('admin.profile.password.update') }}" class="space-y-5 max-w-2xl">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-xs text-gray-500 mb-2">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40">
                </div>

                <div>
                    <label for="password" class="block text-xs text-gray-500 mb-2">Password Baru</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40">
                    <p class="text-xs text-gray-500 mt-2">Minimal 8 karakter.</p>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs text-gray-500 mb-2">Konfirmasi Password
                        Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 bg-gray-800/50 border border-gray-700/50 rounded-xl text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500/40">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-sm font-medium rounded-xl shadow-lg shadow-emerald-500/20 transition-all">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
