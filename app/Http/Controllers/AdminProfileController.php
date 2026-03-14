<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class AdminProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('admin.profile.show', [
            'user' => $request->user(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();
        $user->password = $validated['password'];
        $user->save();

        $request->session()->regenerate();

        return redirect()
            ->route('admin.profile.show')
            ->with('success', 'Password berhasil diperbarui.');
    }
}
