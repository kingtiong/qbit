<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Change these credentials after first login.
        User::updateOrCreate(
            ['email' => 'root@iqbit.to'],
            [
                'name' => 'Root',
                'password' => Hash::make('Root@Iqbit2025!'),
                'is_admin' => true,
                'is_root' => true,
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin@iqbit.to'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Admin@Iqbit2025!'),
                'is_admin' => true,
                'is_root' => false,
            ],
        );
    }
}
