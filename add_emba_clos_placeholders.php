<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Program;
use App\Models\CourseLearningOutcome;

echo "=== Adding CLOs for EMBA Subjects ===\n\n";

// Get EMBA program
$program = Program::where('code', 'EMBA')->first();

if (!$program) {
    echo "EMBA program not found!\n";
    exit;
}

echo "Program: " . $program->name . "\n\n";
echo "NOTE: This adds placeholder CLOs. Update with actual course outlines when available.\n\n";

// Define CLOs for EMBA subjects
$subjectsClos = [
    'Strategic Human Resource Management' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Organizational Behaviour' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Strategic Management' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Strategic Marketing' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Accounting and Finance for Decision Making' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Business Analytics' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Business Economics' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Digital Business' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Innovation and Technology Entrepreneurship' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'International Business Management & Policy' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Research Methodology' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ],
    'Project' => [
        ['clo_code' => 'CLO1', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Knowledge & Understanding (C1)', 'mqf_code' => 'C1', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 1],
        ['clo_code' => 'CLO2', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Cognitive Skills (C2)', 'mqf_code' => 'C2', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 2],
        ['clo_code' => 'CLO3', 'description' => 'PLACEHOLDER - To be updated with actual course outline', 'mqf_domain' => 'Practical Skills (C3)', 'mqf_code' => 'C3', 'topics_covered' => json_encode([]), 'assessment_methods' => json_encode([]), 'sort_order' => 3]
    ]
];

// Add CLOs for each subject
foreach ($subjectsClos as $courseName => $clos) {
    echo "Adding CLOs for: " . $courseName . "\n";
    
    foreach ($clos as $cloData) {
        try {
            $program->courseLearningOutcomes()->create(array_merge($cloData, ['course_name' => $courseName]));
            echo "  ✓ " . $cloData['clo_code'] . " added (PLACEHOLDER)\n";
        } catch (Exception $e) {
            echo "  ✗ Error adding " . $cloData['clo_code'] . ": " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

echo "=== All Placeholder CLOs Added Successfully! ===\n";
echo "Total subjects with CLOs: " . count($subjectsClos) . "\n";
echo "Total CLOs added: " . array_sum(array_map('count', $subjectsClos)) . "\n";
echo "\nNOTE: Update these placeholders with actual course outlines via Admin Panel.\n";

