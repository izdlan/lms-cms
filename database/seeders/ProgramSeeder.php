<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'code' => 'EMBA',
                'name' => 'EXECUTIVE MASTER IN BUSINESS ADMINISTRATION',
                'description' => 'A comprehensive executive program designed for working professionals seeking advanced business knowledge and leadership skills.',
                'level' => 'master',
                'duration_months' => 12,
                'is_active' => true
            ]
        ];

        foreach ($programs as $program) {
            Program::updateOrCreate(
                ['code' => $program['code']],
                $program
            );
        }
    }
}
