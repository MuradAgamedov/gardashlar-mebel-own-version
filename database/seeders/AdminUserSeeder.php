<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user if it doesn't exist
        User::firstOrCreate(
            ['email' => 'admin@gardaslar.az'],
            [
                'full_name' => 'Admin User',
                'phone' => '+994501234567',
                'email' => 'admin@gardaslar.az',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // Also create a test user
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'full_name' => 'Test User',
                'phone' => '+994501234568',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin users created successfully!');
        $this->command->info('Admin Email: admin@gardaslar.az');
        $this->command->info('Admin Password: admin123');
        $this->command->info('Test Email: test@example.com');
        $this->command->info('Test Password: password');
    }
}
