<?php

namespace App\Http\Controllers;

use App\Models\AboutPage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;

class AboutController extends Controller
{
    public function show(Request $request): View
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

        $language = $request->string('lang')->lower()->value();
        if (!in_array($language, ['id', 'en'], true)) {
            $language = $request->cookie('preferred_locale') === 'en' ? 'en' : 'id';
        }

        $pageTitle = $language === 'en'
            ? ($aboutPage->title_en ?: $aboutPage->title)
            : ($aboutPage->title ?: $aboutPage->title_en);

        $pageContent = $language === 'en'
            ? ($aboutPage->content_en ?: $aboutPage->content)
            : ($aboutPage->content ?: $aboutPage->content_en);

        return view('about', [
            'aboutPage' => $aboutPage,
            'pageTitle' => $pageTitle,
            'pageContent' => $pageContent,
            'activeLanguage' => $language,
            'metaDescription' => Str::limit(trim(strip_tags(html_entity_decode((string) $pageContent))), 160)
                ?: ($language === 'en' ? 'Information about Indonesia Mining Watch.' : 'Informasi tentang Indonesia Mining Watch.'),
            'canonicalUrl' => route('about'),
        ]);
    }
}
