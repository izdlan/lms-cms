<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Program;
use App\Models\PhdLearningOutcome;

echo "=== Adding EDBA Program Learning Outcomes (PLOs) ===\n\n";

// Get EDBA program
$program = Program::where('code', 'EDBA')->first();

if (!$program) {
    echo "EDBA program not found!\n";
    exit;
}

echo "Program: " . $program->name . "\n\n";

// Define the 8 PLOs for EDBA
$plos = [
    [
        'plo_code' => 'PLO1',
        'description' => 'Demonstrate advanced, integrated, and multidisciplinary knowledge across key business domains and research methodologies.',
        'mqf_domain' => 'C1: Knowledge & Understanding',
        'mqf_code' => 'C1',
        'original_research' => 'Advanced research in multidisciplinary business domains',
        'advanced_research_methods' => 'Doctoral-level research methodologies and theoretical frameworks',
        'theoretical_contribution' => 'Contribute to business knowledge through advanced theoretical understanding',
        'publication_requirements' => 'Doctoral dissertation and potential publication in peer-reviewed journals',
        'supervision_skills' => 'Independent research conduct under doctoral supervision',
        'dissertation_defense' => 'Viva Voce examination and dissertation defense',
        'assessment_methods' => 'Advanced research assignments, comprehensive exams, dissertation defense',
        'mapped_courses' => 'MKT8101E, ECO8101E, ACC8101E',
        'sort_order' => 1
    ],
    [
        'plo_code' => 'PLO2',
        'description' => 'Critically analyze complex business phenomena using strategic, financial, and economic reasoning.',
        'mqf_domain' => 'C2: Cognitive Skills',
        'mqf_code' => 'C2',
        'original_research' => 'Critical analysis of complex business phenomena',
        'advanced_research_methods' => 'Advanced analytical and cognitive research frameworks',
        'theoretical_contribution' => 'Innovative theoretical contributions to business analysis',
        'publication_requirements' => 'Publish research findings in academic or professional journals',
        'supervision_skills' => 'Independent critical thinking and analytical research capabilities',
        'dissertation_defense' => 'Defend analytical approaches and findings',
        'assessment_methods' => 'Critical analysis projects, strategic reports, comprehensive evaluations',
        'mapped_courses' => 'MGT8101E, MGT8103E, RSM8101E',
        'sort_order' => 2
    ],
    [
        'plo_code' => 'PLO3',
        'description' => 'Apply advanced leadership, governance, and decision-making frameworks to address real-world business challenges.',
        'mqf_domain' => 'C3: Practical Skills',
        'mqf_code' => 'C3',
        'original_research' => 'Applied research in leadership and governance',
        'advanced_research_methods' => 'Action research and case study methodologies',
        'theoretical_contribution' => 'Contribute to leadership and governance theory',
        'publication_requirements' => 'Publish applied research in governance and leadership journals',
        'supervision_skills' => 'Independent application of advanced frameworks',
        'dissertation_defense' => 'Defend practical applications and leadership theories',
        'assessment_methods' => 'Leadership projects, governance analyses, case study research',
        'mapped_courses' => 'GOV8101E, MGT8104E, HPM8101E',
        'sort_order' => 3
    ],
    [
        'plo_code' => 'PLO4',
        'description' => 'Design and execute doctoral-level research with methodological rigor and practical relevance.',
        'mqf_domain' => 'C4 & C5: Research & Analytical Skills',
        'mqf_code' => 'C4, C5',
        'original_research' => 'Doctoral-level original research design and execution',
        'advanced_research_methods' => 'Quantitative, qualitative, and mixed-method research designs',
        'theoretical_contribution' => 'Methodological contributions to business research',
        'publication_requirements' => 'Research methodology publication in academic journals',
        'supervision_skills' => 'Independent doctoral research design and execution',
        'dissertation_defense' => 'Defend research methodology and execution',
        'assessment_methods' => 'Research proposals, methodology defense, data analysis projects',
        'mapped_courses' => 'RSM8101E, RSM8102E, RSM8103E',
        'sort_order' => 4
    ],
    [
        'plo_code' => 'PLO5',
        'description' => 'Evaluate and apply ethical and governance principles to promote accountability and sustainable practices.',
        'mqf_domain' => 'C6: Ethics & Professionalism',
        'mqf_code' => 'C6',
        'original_research' => 'Research in ethics and governance frameworks',
        'advanced_research_methods' => 'Ethical research practices and governance analysis',
        'theoretical_contribution' => 'Contributions to ethical business theory',
        'publication_requirements' => 'Ethics and governance research publications',
        'supervision_skills' => 'Independent ethical research conduct',
        'dissertation_defense' => 'Defend ethical frameworks and governance research',
        'assessment_methods' => 'Ethics case studies, governance analyses, ethical frameworks',
        'mapped_courses' => 'GOV8101E, MGT8102E',
        'sort_order' => 5
    ],
    [
        'plo_code' => 'PLO6',
        'description' => 'Demonstrate advanced communication, presentation, and negotiation skills to engage stakeholders effectively.',
        'mqf_domain' => 'C8: Communication Skills',
        'mqf_code' => 'C8',
        'original_research' => 'Research in business communication effectiveness',
        'advanced_research_methods' => 'Advanced presentation and communication research methodologies',
        'theoretical_contribution' => 'Contributions to business communication theory',
        'publication_requirements' => 'Communication research publications',
        'supervision_skills' => 'Independent academic writing and presentation',
        'dissertation_defense' => 'Viva Voce presentation and defense',
        'assessment_methods' => 'Academic writing, presentations, dissertation defense',
        'mapped_courses' => 'RSM8102E, MGT8104E',
        'sort_order' => 6
    ],
    [
        'plo_code' => 'PLO7',
        'description' => 'Utilize digital technologies, analytics, and innovation to enhance business transformation and decision-making.',
        'mqf_domain' => 'C7: Digital Skills',
        'mqf_code' => 'C7',
        'original_research' => 'Digital transformation and innovation research',
        'advanced_research_methods' => 'Digital analytics and innovation research methodologies',
        'theoretical_contribution' => 'Contributions to digital business and innovation theory',
        'publication_requirements' => 'Digital innovation research publications',
        'supervision_skills' => 'Independent digital research and analytics',
        'dissertation_defense' => 'Defend digital research methodologies and applications',
        'assessment_methods' => 'Digital research projects, analytics reports, innovation studies',
        'mapped_courses' => 'MGT8101E, HPM8101E',
        'sort_order' => 7
    ],
    [
        'plo_code' => 'PLO8',
        'description' => 'Contribute to business and policy advancement through research and leadership that promote social and economic value creation.',
        'mqf_domain' => 'PS: Professional Skills & Social Skills',
        'mqf_code' => 'PS',
        'original_research' => 'Applied research in business policy and societal impact',
        'advanced_research_methods' => 'Impact research and policy analysis methodologies',
        'theoretical_contribution' => 'Contributions to business policy and social value theory',
        'publication_requirements' => 'Policy and impact research publications',
        'supervision_skills' => 'Independent policy research and social impact analysis',
        'dissertation_defense' => 'Defend research impact and policy contributions',
        'assessment_methods' => 'Policy research, impact analyses, social value studies',
        'mapped_courses' => 'MGT8103E, RSM8103E',
        'sort_order' => 8
    ]
];

// Add each PLO
echo "Adding PhD Program Learning Outcomes for EDBA...\n\n";

foreach ($plos as $ploData) {
    try {
        $program->phdLearningOutcomes()->create($ploData);
        echo "✓ " . $ploData['plo_code'] . " added successfully\n";
        echo "  " . substr($ploData['description'], 0, 80) . "...\n";
    } catch (Exception $e) {
        echo "✗ Error adding " . $ploData['plo_code'] . ": " . $e->getMessage() . "\n";
    }
}

echo "\n=== All EDBA PLOs Added Successfully! ===\n";
echo "Total PLOs added: " . count($plos) . "\n";
