<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create staff users
        $staffUsers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@olympia.edu',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '+60123456789',
                'must_reset_password' => false,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@olympia.edu',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '+60123456790',
                'must_reset_password' => false,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@olympia.edu',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '+60123456791',
                'must_reset_password' => false,
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily.davis@olympia.edu',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'phone' => '+60123456792',
                'must_reset_password' => false,
            ],
        ];

        foreach ($staffUsers as $staff) {
            User::updateOrCreate(
                ['email' => $staff['email']],
                $staff
            );
        }

        $this->command->info('Staff users created successfully!');
    }
}
