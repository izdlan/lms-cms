<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $staffUsers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@olympia.edu',
                'phone' => '012-3456789',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@olympia.edu',
                'phone' => '012-3456790',
                'role' => 'lecturer',
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@olympia.edu',
                'phone' => '012-3456791',
                'role' => 'lecturer',
            ],
        ];

        foreach ($staffUsers as $staffData) {
            User::updateOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'phone' => $staffData['phone'],
                    'password' => Hash::make('000000'),
                    'role' => $staffData['role'],
                    'must_reset_password' => true,
                ]
            );
        }
    }
}
