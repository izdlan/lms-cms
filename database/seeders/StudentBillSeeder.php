<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudentBill;
use App\Models\User;
use Carbon\Carbon;

class StudentBillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some students to create bills for
        $students = User::where('role', 'student')->take(5)->get();
        
        if ($students->isEmpty()) {
            $this->command->warn('No students found. Please create some students first.');
            return;
        }

        $billTypes = [
            StudentBill::TYPE_TUITION_FEE,
            StudentBill::TYPE_EET_FEE,
            StudentBill::TYPE_LIBRARY_FEE,
            StudentBill::TYPE_EXAM_FEE,
            StudentBill::TYPE_REGISTRATION_FEE,
        ];

        $sessions = ['20251', '20252', '20253', '20254', '20255'];

        foreach ($students as $student) {
            // Create 2-4 bills per student
            $billCount = rand(2, 4);
            
            for ($i = 0; $i < $billCount; $i++) {
                $billType = $billTypes[array_rand($billTypes)];
                $session = $sessions[array_rand($sessions)];
                $amount = $this->getAmountForBillType($billType);
                
                // Random status (70% pending, 20% paid, 10% overdue)
                $statusRand = rand(1, 100);
                if ($statusRand <= 70) {
                    $status = StudentBill::STATUS_PENDING;
                } elseif ($statusRand <= 90) {
                    $status = StudentBill::STATUS_PAID;
                } else {
                    $status = StudentBill::STATUS_OVERDUE;
                }

                $billDate = Carbon::now()->subDays(rand(1, 60));
                $dueDate = $billDate->copy()->addDays(30);

                StudentBill::create([
                    'bill_number' => StudentBill::generateBillNumber(),
                    'user_id' => $student->id,
                    'session' => $session,
                    'bill_type' => $billType,
                    'amount' => $amount,
                    'status' => $status,
                    'bill_date' => $billDate,
                    'due_date' => $dueDate,
                    'description' => $this->getDescriptionForBillType($billType),
                    'paid_at' => $status === StudentBill::STATUS_PAID ? $billDate->copy()->addDays(rand(1, 15)) : null,
                ]);
            }
        }

        $this->command->info('Student bills created successfully!');
    }

    private function getAmountForBillType(string $billType): float
    {
        return match($billType) {
            StudentBill::TYPE_TUITION_FEE => rand(500, 1000),
            StudentBill::TYPE_EET_FEE => rand(20, 50),
            StudentBill::TYPE_LIBRARY_FEE => rand(10, 30),
            StudentBill::TYPE_EXAM_FEE => rand(50, 100),
            StudentBill::TYPE_REGISTRATION_FEE => rand(100, 200),
            default => rand(50, 200)
        };
    }

    private function getDescriptionForBillType(string $billType): string
    {
        return match($billType) {
            StudentBill::TYPE_TUITION_FEE => 'Tuition fee for academic semester',
            StudentBill::TYPE_EET_FEE => 'English Enhancement Test fee',
            StudentBill::TYPE_LIBRARY_FEE => 'Library services and resources fee',
            StudentBill::TYPE_EXAM_FEE => 'Examination fee for semester',
            StudentBill::TYPE_REGISTRATION_FEE => 'Student registration and enrollment fee',
            default => 'University fee'
        };
    }
}