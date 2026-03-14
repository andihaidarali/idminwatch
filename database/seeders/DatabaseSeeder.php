<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@minwatch.com'],
            [
                'name' => 'Superadmin MinWatch',
                'role' => User::ROLE_SUPERADMIN,
                'password' => 'password123',
            ]
        );
    }
}
