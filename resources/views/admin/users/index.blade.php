<x-admin-layout title="Manajemen User - Admin">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Manajemen User Admin</h1>
            <p class="mt-1 text-sm text-gray-400">Superadmin dapat membuat dan melihat akun admin.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-emerald-500/20 transition-all hover:from-emerald-500 hover:to-teal-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Admin
        </a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-800/50 bg-gray-900/60 backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-800/50">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Nama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-400">Dibuat</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/30">
                    @forelse($users as $user)
                        <tr class="transition-colors hover:bg-gray-800/20">
                            <td class="px-6 py-4 text-sm font-medium text-white">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if($user->role === \App\Models\User::ROLE_SUPERADMIN)
                                    <span class="inline-flex items-center rounded-lg border border-emerald-500/20 bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-400">
                                        Superadmin
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-lg border border-blue-500/20 bg-blue-500/10 px-2.5 py-1 text-xs font-medium text-blue-400">
                                        Admin
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-300">{{ $user->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-amber-400 transition-all hover:bg-amber-400/10 hover:text-amber-300">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </a>

                                    @if($user->role === \App\Models\User::ROLE_ADMIN)
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Hapus user admin ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-400 transition-all hover:bg-red-500/10 hover:text-red-300">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                Belum ada user admin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-gray-800/50 px-6 py-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</x-admin-layout>
