<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommodityTypeManagementTest extends TestCase
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

        if (!Schema::hasTable('jenis_tambang_refs')) {
            Schema::create('jenis_tambang_refs', function (Blueprint $table): void {
                $table->id();
                $table->string('nama')->unique();
                $table->string('nama_en')->nullable();
                $table->timestamps();
            });
        } else {
            DB::table('jenis_tambang_refs')->delete();
        }

        if (!Schema::hasTable('wilayah_tambang')) {
            Schema::create('wilayah_tambang', function (Blueprint $table): void {
                $table->id();
                $table->string('nama');
                $table->string('jenis_tambang')->nullable();
                $table->timestamps();
            });
        } else {
            DB::table('wilayah_tambang')->delete();
        }
    }

    public function test_authenticated_admin_can_open_master_commodity_page(): void
    {
        $user = User::factory()->admin()->create();

        DB::table('jenis_tambang_refs')->insert([
            'nama' => 'Batubara',
            'nama_en' => 'Coal',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/jenis-tambang')
            ->assertOk()
            ->assertSeeText('Master Jenis Tambang')
            ->assertSee('value="Batubara"', false);
    }

    public function test_authenticated_admin_can_create_commodity_type_via_ajax(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)
            ->postJson('/admin/jenis-tambang', [
                'nama' => 'Pasir Silika',
                'nama_en' => 'Silica Sand',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.nama', 'Pasir Silika')
            ->assertJsonPath('data.nama_en', 'Silica Sand')
            ->assertJsonPath('data.created', true);

        $this->assertDatabaseHas('jenis_tambang_refs', [
            'nama' => 'Pasir Silika',
            'nama_en' => 'Silica Sand',
        ]);
    }

    public function test_existing_commodity_type_reuses_existing_record_and_updates_translation(): void
    {
        $user = User::factory()->admin()->create();

        DB::table('jenis_tambang_refs')->insert([
            'nama' => 'Batubara',
            'nama_en' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->postJson('/admin/jenis-tambang', [
                'nama' => '  batubara  ',
                'nama_en' => 'Coal',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.nama', 'Batubara')
            ->assertJsonPath('data.nama_en', 'Coal')
            ->assertJsonPath('data.created', false);

        $this->assertSame(1, DB::table('jenis_tambang_refs')->count());
        $this->assertDatabaseHas('jenis_tambang_refs', [
            'nama' => 'Batubara',
            'nama_en' => 'Coal',
        ]);
    }

    public function test_admin_can_update_commodity_type_and_cascade_to_linked_wilayah(): void
    {
        $user = User::factory()->admin()->create();
        $commodityId = DB::table('jenis_tambang_refs')->insertGetId([
            'nama' => 'Pasir Kuarsa',
            'nama_en' => 'Quartz Sand',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wilayah_tambang')->insert([
            'nama' => 'Wilayah Uji',
            'jenis_tambang' => 'Pasir Kuarsa',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->put("/admin/jenis-tambang/{$commodityId}", [
                'nama' => 'Pasir Silika',
                'nama_en' => 'Silica Sand',
            ]);

        $response->assertRedirect('/admin/jenis-tambang');

        $this->assertDatabaseHas('jenis_tambang_refs', [
            'id' => $commodityId,
            'nama' => 'Pasir Silika',
            'nama_en' => 'Silica Sand',
        ]);

        $this->assertDatabaseHas('wilayah_tambang', [
            'nama' => 'Wilayah Uji',
            'jenis_tambang' => 'Pasir Silika',
        ]);
    }

    public function test_admin_cannot_delete_commodity_type_that_is_still_used(): void
    {
        $user = User::factory()->admin()->create();
        $commodityId = DB::table('jenis_tambang_refs')->insertGetId([
            'nama' => 'Nikel',
            'nama_en' => 'Nickel',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wilayah_tambang')->insert([
            'nama' => 'Wilayah Nikel',
            'jenis_tambang' => 'Nikel',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($user)
            ->from('/admin/jenis-tambang')
            ->delete("/admin/jenis-tambang/{$commodityId}");

        $response->assertRedirect('/admin/jenis-tambang');
        $response->assertSessionHasErrors('jenis_tambang');

        $this->assertDatabaseHas('jenis_tambang_refs', [
            'id' => $commodityId,
        ]);
    }
}
