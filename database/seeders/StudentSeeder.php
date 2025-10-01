<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'ic' => '123456789012',
                'name' => 'Ahmad Bin Ali',
                'email' => 'ahmad.ali@example.com',
                'password' => Hash::make('000000'),
                'phone' => '0123456789',
                'role' => 'student',
                'must_reset_password' => false,
            ],
            [
                'ic' => '987654321098',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@example.com',
                'password' => Hash::make('000000'),
                'phone' => '0198765432',
                'role' => 'student',
                'must_reset_password' => false,
            ],
            [
                'ic' => '112233445566',
                'name' => 'Muhammad Rahman',
                'email' => 'muhammad.rahman@example.com',
                'password' => Hash::make('000000'),
                'phone' => '0134567890',
                'role' => 'student',
                'must_reset_password' => false,
            ],
        ];

        foreach ($students as $student) {
            User::create($student);
        }

        $this->command->info('Student users created successfully!');
        $this->command->info('Student Login Credentials:');
        $this->command->info('IC: 123456789012, Password: 000000');
        $this->command->info('IC: 987654321098, Password: 000000');
        $this->command->info('IC: 112233445566, Password: 000000');
    }
}
