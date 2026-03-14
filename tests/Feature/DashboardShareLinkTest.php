<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardShareLinkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('wilayah_tambang')) {
            Schema::create('wilayah_tambang', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('detail_tambang_id')->nullable();
                $table->string('public_uid')->unique();
                $table->string('nomor_sk')->nullable();
                $table->string('jenis_tambang')->nullable();
                $table->text('dampak_lingkungan')->nullable();
                $table->json('dokumentasi')->nullable();
                $table->string('status')->default('aktif');
                $table->double('luas_sk_ha')->nullable();
                $table->double('luas_overlap')->default(0);
                $table->timestamps();
            });
        } else {
            DB::table('wilayah_tambang')->delete();
        }

        if (!Schema::hasTable('detail_tambang')) {
            Schema::create('detail_tambang', function (Blueprint $table): void {
                $table->id();
                $table->string('nama_perusahaan')->nullable();
                $table->text('profil_singkat')->nullable();
                $table->text('profil_singkat_en')->nullable();
                $table->timestamps();
            });
        } else {
            DB::table('detail_tambang')->delete();
        }
    }

    public function test_shared_mining_area_link_renders_dashboard_with_target_uid(): void
    {
        $companyId = DB::table('detail_tambang')->insertGetId([
            'nama_perusahaan' => 'PT Uji Tambang',
            'profil_singkat' => '<p>Profil singkat uji.</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('wilayah_tambang')->insert([
            'detail_tambang_id' => $companyId,
            'public_uid' => '01jnavshareduidtest0000001',
            'nomor_sk' => 'SK-001',
            'jenis_tambang' => 'Batubara',
            'dampak_lingkungan' => '<p>Dampak lingkungan uji.</p>',
            'status' => 'aktif',
            'luas_sk_ha' => 100.5,
            'luas_overlap' => 12.75,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get('/mining-area/01jnavshareduidtest0000001');

        $response->assertOk();
        $response->assertSee('id="dashboard-root"', false);
        $response->assertSee('data-shared-tambang-uid="01jnavshareduidtest0000001"', false);
        $response->assertSee('Indonesia Mining Watch - PT Uji Tambang');
        $response->assertSee('/mining-area/01jnavshareduidtest0000001', false);
    }
}
