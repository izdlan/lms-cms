<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateDefaultPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating default password for all users to 000000...');

        // Update all existing users
        $users = User::all();
        $updatedCount = 0;

        foreach ($users as $user) {
            $user->update([
                'password' => Hash::make('000000'),
                'must_reset_password' => true // Force password reset on next login
            ]);
            $updatedCount++;
        }

        $this->command->info("✅ Updated password for {$updatedCount} users");
        $this->command->info("Default password for all users: 000000");
        $this->command->info("All users are required to change their password on first login.");
    }
}
