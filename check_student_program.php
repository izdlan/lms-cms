<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Program;

echo "=== Checking Student Program Assignment ===\n\n";

// Get the student
$student = User::where('email', 'like', '%ali%')->orWhere('name', 'like', '%ALI%')->first();

if (!$student) {
    echo "Student not found!\n";
    exit;
}

echo "Student: " . $student->name . "\n";
echo "Email: " . $student->email . "\n";
echo "Current Program Name: " . ($student->programme_name ?? 'Not set') . "\n\n";

// Check what programs exist
$programs = Program::all();

echo "Available Programs:\n";
foreach ($programs as $program) {
    echo "- " . $program->code . ": " . $program->name . "\n";
}

echo "\n=== What program should this student be assigned to? ===\n";
echo "Current: " . ($student->programme_name ?? 'Not set') . "\n";
echo "Should be: EDBA - Executive Doctor in Business Administration\n";

