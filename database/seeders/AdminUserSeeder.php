<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@lms-olympia.com',
            'password' => Hash::make('000000'),
            'role' => 'admin',
            'must_reset_password' => false,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@lms-olympia.com');
        $this->command->info('Password: 000000');
    }
}