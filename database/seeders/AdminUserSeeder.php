<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gardaslar.az'],
            [
                'name'              => 'Admin',
                'surname'           => 'User',
                'phone'             => '+994501234567',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test',
                'surname'           => 'User',
                'phone'             => '+994501234568',
                'password'          => Hash::make('password'),
                'role'              => 'user',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin Email: admin@gardaslar.az');
        $this->command->info('Test Email: test@example.com');
    }
}
