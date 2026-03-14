<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProfileTest extends TestCase
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
    }

    public function test_guest_cannot_access_admin_profile_page(): void
    {
        $this->get('/admin/profile')->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_open_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/profile')
            ->assertOk()
            ->assertSeeText('Profil Admin');
    }

    public function test_authenticated_admin_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->actingAs($user)
            ->put('/admin/profile/password', [
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect('/admin/profile');

        $user->refresh();

        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_password_change_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $response = $this->actingAs($user)
            ->from('/admin/profile')
            ->put('/admin/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect('/admin/profile');
        $response->assertSessionHasErrors('current_password');
    }
}
