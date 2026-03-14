<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAboutController extends Controller
{
    public function edit(): View
    {
        $aboutPage = AboutPage::query()->firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'Tentang Indonesia Mining Watch',
                'content' => '<p>Isi halaman about dapat diedit melalui panel admin.</p>',
                'title_en' => 'About Indonesia Mining Watch',
                'content_en' => '<p>The about page content can be edited from the admin panel.</p>',
            ]
        );

        return view('admin.about.edit', compact('aboutPage'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'content_en' => ['nullable', 'string'],
        ]);

        $aboutPage = AboutPage::query()->firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => $validated['title'],
                'content' => $validated['content'] ?? null,
                'title_en' => $validated['title_en'] ?? null,
                'content_en' => $validated['content_en'] ?? null,
            ]
        );
        $aboutPage->update($validated);

        return redirect()
            ->route('admin.about.edit')
            ->with('success', 'Halaman About berhasil diperbarui.');
    }
}
