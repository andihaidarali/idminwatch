<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
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

    public function test_superadmin_can_open_admin_user_management_page(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSeeText('Manajemen User Admin');
    }

    public function test_admin_cannot_access_admin_user_management_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_superadmin_can_create_admin_user(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->post('/admin/users', [
                'name' => 'Admin Baru',
                'email' => 'admin.baru@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'admin.baru@example.com',
            'role' => 'admin',
        ]);
    }

    public function test_superadmin_can_edit_user_and_change_password_without_old_password(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $admin = User::factory()->admin()->create([
            'password' => 'password123',
        ]);

        $response = $this->actingAs($superadmin)
            ->put("/admin/users/{$admin->id}", [
                'name' => 'Admin Update',
                'email' => 'admin.update@example.com',
                'role' => 'superadmin',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response->assertRedirect('/admin/users');

        $admin->refresh();

        $this->assertSame('Admin Update', $admin->name);
        $this->assertSame('admin.update@example.com', $admin->email);
        $this->assertSame('superadmin', $admin->role);
        $this->assertTrue(Hash::check('newpassword123', $admin->password));
    }

    public function test_last_superadmin_cannot_be_changed_to_admin(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->from("/admin/users/{$superadmin->id}/edit")
            ->put("/admin/users/{$superadmin->id}", [
                'name' => $superadmin->name,
                'email' => $superadmin->email,
                'role' => 'admin',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertRedirect("/admin/users/{$superadmin->id}/edit");
        $response->assertSessionHasErrors('role');

        $superadmin->refresh();

        $this->assertSame('superadmin', $superadmin->role);
    }

    public function test_superadmin_can_delete_admin_user(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($superadmin)
            ->delete("/admin/users/{$admin->id}");

        $response->assertRedirect('/admin/users');

        $this->assertDatabaseMissing('users', [
            'id' => $admin->id,
        ]);
    }

    public function test_superadmin_cannot_delete_superadmin_user(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $otherSuperadmin = User::factory()->superadmin()->create();

        $response = $this->actingAs($superadmin)
            ->from('/admin/users')
            ->delete("/admin/users/{$otherSuperadmin->id}");

        $response->assertRedirect('/admin/users');
        $response->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', [
            'id' => $otherSuperadmin->id,
            'role' => 'superadmin',
        ]);
    }
}
