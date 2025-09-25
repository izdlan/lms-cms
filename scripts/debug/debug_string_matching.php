<?php
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUGGING STRING MATCHING ===\n\n";

$testHeaders = ['NAME', 'PROGRAME NAME', 'PROGRAMME NAME', 'LEARNERS NAME'];

foreach ($testHeaders as $headerName) {
    echo "Testing header: '{$headerName}'\n";
    
    $headerNameLower = strtolower(trim($headerName));
    echo "  Lowercase: '{$headerNameLower}'\n";
    
    $hasName = strpos($headerNameLower, 'name') !== false;
    $hasLearners = strpos($headerNameLower, 'learners') !== false;
    $hasProgramme = strpos($headerNameLower, 'programme') !== false;
    $hasProgram = strpos($headerNameLower, 'program') !== false;
    $hasProgame = strpos($headerNameLower, 'progame') !== false;
    $hasPrograme = strpos($headerNameLower, 'programe') !== false;
    
    echo "  Contains 'name': " . ($hasName ? 'YES' : 'NO') . "\n";
    echo "  Contains 'learners': " . ($hasLearners ? 'YES' : 'NO') . "\n";
    echo "  Contains 'programme': " . ($hasProgramme ? 'YES' : 'NO') . "\n";
    echo "  Contains 'program': " . ($hasProgram ? 'YES' : 'NO') . "\n";
    echo "  Contains 'progame': " . ($hasProgame ? 'YES' : 'NO') . "\n";
    echo "  Contains 'programe': " . ($hasPrograme ? 'YES' : 'NO') . "\n";
    
    $shouldMapToName = ($hasName || $hasLearners) && 
                      !$hasProgramme && 
                      !$hasProgram &&
                      !$hasProgame &&
                      !$hasPrograme;
    
    echo "  Should map to 'name': " . ($shouldMapToName ? 'YES' : 'NO') . "\n";
    echo "\n";
}

echo "=== DEBUG COMPLETED ===\n";
