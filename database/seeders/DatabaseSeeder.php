<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ServicePackageSeeder::class);

        User::query()->firstOrCreate(
            ['email' => 'admin@mail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('123456'),
                'role' => UserRole::Admin,
            ],
        );

        User::query()->firstOrCreate(
            ['email' => 'user@mail.com'],
            [
                'name' => 'User',
                'password' => Hash::make('123456'),
                'role' => UserRole::Client,
            ],
        );
    }
}
