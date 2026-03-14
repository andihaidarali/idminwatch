<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('role')->default('admin');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('role')->default('admin')->after('email');
            });
        } else {
            User::query()->delete();
        }

        if (!Schema::hasTable('about_pages')) {
            Schema::create('about_pages', function (Blueprint $table): void {
                $table->id();
                $table->string('slug')->unique();
                $table->string('title');
                $table->longText('content')->nullable();
                $table->string('title_en')->nullable();
                $table->longText('content_en')->nullable();
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('about_pages', 'title_en')) {
            Schema::table('about_pages', function (Blueprint $table): void {
                $table->string('title_en')->nullable()->after('content');
                $table->longText('content_en')->nullable()->after('title_en');
            });
        } else {
            \DB::table('about_pages')->delete();
        }
    }

    public function test_public_about_page_is_accessible(): void
    {
        $response = $this->get('/about');

        $response->assertOk();
        $response->assertSeeText('Tentang Indonesia Mining Watch');
    }

    public function test_authenticated_admin_can_update_about_page(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->put('/admin/about', [
                'title' => 'Tentang Platform',
                'content' => '<p>Konten about baru.</p>',
                'title_en' => 'About the Platform',
                'content_en' => '<p>Updated about content.</p>',
            ]);

        $response->assertRedirect('/admin/about');

        $this->assertDatabaseHas('about_pages', [
            'slug' => 'about',
            'title' => 'Tentang Platform',
            'title_en' => 'About the Platform',
        ]);
    }

    public function test_public_about_page_can_render_english_version(): void
    {
        \DB::table('about_pages')->insert([
            'slug' => 'about',
            'title' => 'Tentang Indonesia Mining Watch',
            'content' => '<p>Konten Indonesia.</p>',
            'title_en' => 'About Indonesia Mining Watch',
            'content_en' => '<p>English content.</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/about?lang=en');

        $response->assertOk();
        $response->assertSeeText('About Indonesia Mining Watch');
        $response->assertSeeText('English content.');
    }

    public function test_public_about_page_uses_saved_language_preference_when_query_is_missing(): void
    {
        \DB::table('about_pages')->insert([
            'slug' => 'about',
            'title' => 'Tentang Indonesia Mining Watch',
            'content' => '<p>Konten Indonesia.</p>',
            'title_en' => 'About Indonesia Mining Watch',
            'content_en' => '<p>English content.</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->withCookie('preferred_locale', 'en')->get('/about');

        $response->assertOk();
        $response->assertSeeText('About Indonesia Mining Watch');
        $response->assertSeeText('English content.');
    }
}
