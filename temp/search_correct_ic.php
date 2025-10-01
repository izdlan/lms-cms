<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Searching for student with correct IC: 821219-08-5351...\n";

// Search in all possible fields
$fields = ['ic', 'student_id', 'col_ref_no', 'phone', 'address'];

foreach ($fields as $field) {
    echo "\nSearching in field '$field':\n";
    $students = App\Models\User::where($field, 'LIKE', '%821219%')
        ->orWhere($field, 'LIKE', '%08-5351%')
        ->get();
    
    if ($students->count() > 0) {
        foreach ($students as $student) {
            echo "Found in $field: ID " . $student->id . " - " . $student->name . " - $field: " . $student->$field . "\n";
        }
    } else {
        echo "No matches in $field\n";
    }
}

// Also search for the student by name to see all their data
echo "\nSearching for MOHD SYAHRIL to see all data:\n";
$mohdStudents = App\Models\User::where('name', 'LIKE', '%MOHD SYAHRIL%')->get();

foreach ($mohdStudents as $student) {
    echo "\nStudent ID: " . $student->id . "\n";
    echo "Name: " . $student->name . "\n";
    echo "IC: " . $student->ic . "\n";
    echo "Student ID: " . $student->student_id . "\n";
    echo "Col Ref No: " . $student->col_ref_no . "\n";
    echo "Phone: " . $student->phone . "\n";
    echo "Address: " . substr($student->address, 0, 100) . "...\n";
    echo "Source Sheet: " . $student->source_sheet . "\n";
}



