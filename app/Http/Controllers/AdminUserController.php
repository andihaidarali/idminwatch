<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->orderByRaw("CASE WHEN role = 'superadmin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => User::ROLE_ADMIN,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User admin berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:' . User::ROLE_SUPERADMIN . ',' . User::ROLE_ADMIN],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (
            $user->role === User::ROLE_SUPERADMIN
            && $validated['role'] !== User::ROLE_SUPERADMIN
            && User::query()->where('role', User::ROLE_SUPERADMIN)->count() <= 1
        ) {
            return back()
                ->withErrors(['role' => 'Harus ada minimal satu superadmin di sistem.'])
                ->withInput();
        }

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->role !== User::ROLE_ADMIN) {
            return redirect()
                ->route('admin.users.index')
                ->withErrors(['user' => 'Hanya user dengan role admin yang dapat dihapus.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User admin berhasil dihapus.');
    }
}
