<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('admin')->after('email');
        });

        $updated = DB::table('users')
            ->where('email', 'admin@minwatch.com')
            ->update(['role' => 'superadmin']);

        if ($updated === 0 && DB::table('users')->count() === 1) {
            DB::table('users')->update(['role' => 'superadmin']);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });
    }
};
