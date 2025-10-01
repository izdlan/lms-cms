<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\CourseClo;
use App\Models\CourseTopic;

class CourseDetailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update subjects with descriptions and assessment methods
        $subjects = [
            'EMBA7101' => [
                'description' => 'This course provides an advanced understanding of Strategic Human Resource Management (SHRM) and its role in driving organizational performance. It emphasizes alignment between HR strategies and business goals, exploring contemporary challenges, employment legislation, talent management, and HR analytics. Participants will gain practical skills in designing, implementing, and evaluating HR strategies for sustainable competitive advantage in complex business environments.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Critically evaluate the strategic roles and functions of HRM in achieving organizational effectiveness.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Practical Skills (C3)',
                        'order' => 1,
                        'topics' => [
                            'The Strategic Role of HRM in Business Transformation'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design strategic human resource plans that align with organizational vision, mission, and business strategies.',
                        'mqf_alignment' => 'Cognitive Skills (C2); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Strategic Human Resource Planning and Alignment with Business Strategy'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Apply relevant employment laws and regulatory frameworks in strategic HR decision-making.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 3,
                        'topics' => [
                            'Employment Law, Industrial Relations & Regulatory Compliance'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Use HR metrics and analytics tools to assess workforce performance and support strategic HR decisions.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 4,
                        'topics' => [
                            'HR Analytics and Performance Metrics in Strategic Decision-Making'
                        ]
                    ]
                ]
            ],
            'EMBA7102' => [
                'description' => 'This course explores the fundamental principles of organizational behavior and their application in modern business environments. Students will examine individual, group, and organizational dynamics, leadership theories, motivation, communication, and change management. The course emphasizes practical application of behavioral concepts to enhance organizational effectiveness and employee performance.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze individual behavior patterns and their impact on organizational performance.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Individual Behavior and Personality in Organizations'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Evaluate group dynamics and team effectiveness in diverse organizational contexts.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Communication (C8)',
                        'order' => 2,
                        'topics' => [
                            'Group Dynamics and Team Building'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Apply leadership theories and practices to enhance organizational performance.',
                        'mqf_alignment' => 'Practical Skills (C3); Ethics & Professionalism (C6)',
                        'order' => 3,
                        'topics' => [
                            'Leadership Styles and Organizational Culture'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Design change management strategies to support organizational transformation.',
                        'mqf_alignment' => 'Digital Skills (C7); Numerical & Analytical Skills (C4)',
                        'order' => 4,
                        'topics' => [
                            'Change Management and Organizational Development'
                        ]
                    ]
                ]
            ],
            'EMBA7103' => [
                'description' => 'This course provides comprehensive coverage of strategic management principles and practices. Students will learn to analyze competitive environments, formulate strategic options, and implement strategies effectively. The course covers strategic thinking, competitive analysis, corporate strategy, and strategic implementation across various industries and organizational contexts.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze external and internal environments to identify strategic opportunities and threats.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Strategic Analysis and Environmental Scanning'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Formulate comprehensive strategic plans aligned with organizational objectives.',
                        'mqf_alignment' => 'Practical Skills (C3); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Strategy Formulation and Strategic Planning'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Evaluate strategic options using appropriate analytical frameworks and tools.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Competitive Strategy and Strategic Positioning'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Design implementation strategies for effective strategic execution.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Strategy Implementation and Change Management'
                        ]
                    ]
                ]
            ],
            'EMBA7104' => [
                'description' => 'This course examines strategic marketing concepts and their application in competitive business environments. Students will learn to develop marketing strategies, analyze market opportunities, and create value propositions. The course covers market research, consumer behavior, brand management, digital marketing, and marketing analytics for strategic decision-making.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze market opportunities and develop comprehensive marketing strategies.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Strategic Marketing Planning and Market Analysis'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design integrated marketing communications and brand positioning strategies.',
                        'mqf_alignment' => 'Practical Skills (C3); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Brand Management and Integrated Marketing Communications'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Evaluate consumer behavior and market research data for strategic insights.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Consumer Behavior and Market Research'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Implement digital marketing strategies and measure marketing performance.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Digital Marketing and Marketing Analytics'
                        ]
                    ]
                ]
            ],
            'EMBA7105' => [
                'description' => 'This course provides essential knowledge of accounting and financial principles for strategic decision-making. Students will learn to interpret financial statements, analyze financial performance, and make informed investment decisions. The course covers financial analysis, budgeting, capital investment decisions, and financial risk management.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Interpret financial statements and analyze organizational financial performance.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Numerical & Analytical Skills (C4)',
                        'order' => 1,
                        'topics' => [
                            'Financial Statement Analysis and Interpretation'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Apply financial analysis techniques for strategic decision-making.',
                        'mqf_alignment' => 'Cognitive Skills (C2); Practical Skills (C3)',
                        'order' => 2,
                        'topics' => [
                            'Financial Planning and Budgeting'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Evaluate investment opportunities and capital budgeting decisions.',
                        'mqf_alignment' => 'Digital Skills (C7); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Capital Investment and Project Evaluation'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Design financial risk management strategies and control systems.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Financial Risk Management and Internal Controls'
                        ]
                    ]
                ]
            ],
            'EMBA7106' => [
                'description' => 'This course introduces students to business analytics tools and techniques for data-driven decision making. Students will learn to collect, analyze, and interpret business data using statistical methods and analytical software. The course covers descriptive, predictive, and prescriptive analytics applications in various business contexts.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Apply statistical methods and analytical tools to business data analysis.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Numerical & Analytical Skills (C4)',
                        'order' => 1,
                        'topics' => [
                            'Descriptive Analytics and Data Visualization'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design data collection and analysis frameworks for business insights.',
                        'mqf_alignment' => 'Cognitive Skills (C2); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Statistical Analysis and Hypothesis Testing'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Interpret analytical results and communicate findings effectively.',
                        'mqf_alignment' => 'Communication (C8); Practical Skills (C3)',
                        'order' => 3,
                        'topics' => [
                            'Predictive Modeling and Forecasting'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Implement predictive models for strategic business forecasting.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Business Intelligence and Decision Support Systems'
                        ]
                    ]
                ]
            ],
            'EMBA7107' => [
                'description' => 'This course examines economic principles and their application to business decision-making. Students will learn to analyze market structures, economic indicators, and policy impacts on business operations. The course covers microeconomics, macroeconomics, international economics, and economic forecasting for strategic planning.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze market structures and competitive dynamics in business environments.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Microeconomics and Market Analysis'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Evaluate macroeconomic factors and their impact on business strategy.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Practical Skills (C3)',
                        'order' => 2,
                        'topics' => [
                            'Macroeconomics and Economic Indicators'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Apply economic forecasting techniques for strategic planning.',
                        'mqf_alignment' => 'Digital Skills (C7); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Economic Forecasting and Business Planning'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Assess international economic trends and global business opportunities.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'International Economics and Global Markets'
                        ]
                    ]
                ]
            ],
            'EMBA7108' => [
                'description' => 'This course explores the transformation of business through digital technologies and platforms. Students will learn about digital business models, e-commerce strategies, digital marketing, and technology adoption. The course covers digital transformation, online business operations, and the impact of emerging technologies on traditional business practices.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze digital business models and their competitive advantages.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Digital Skills (C7)',
                        'order' => 1,
                        'topics' => [
                            'Digital Business Models and E-commerce'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design digital transformation strategies for traditional businesses.',
                        'mqf_alignment' => 'Cognitive Skills (C2); Practical Skills (C3)',
                        'order' => 2,
                        'topics' => [
                            'Digital Transformation and Change Management'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Implement e-commerce and digital marketing strategies.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Digital Marketing and Online Customer Engagement'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Evaluate emerging technologies and their business applications.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Emerging Technologies and Future Business Trends'
                        ]
                    ]
                ]
            ],
            'EMBA7109' => [
                'description' => 'This course focuses on innovation management and technology entrepreneurship in modern business environments. Students will learn to identify opportunities, develop innovative solutions, and create technology-based ventures. The course covers innovation processes, intellectual property, startup development, and technology commercialization strategies.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Identify innovation opportunities and develop creative business solutions.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Innovation Management and Creative Problem Solving'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design technology-based business models and venture strategies.',
                        'mqf_alignment' => 'Practical Skills (C3); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Technology Entrepreneurship and Startup Development'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Evaluate intellectual property and technology commercialization options.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Intellectual Property and Technology Commercialization'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Implement innovation management processes and startup development strategies.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Innovation Ecosystems and Technology Transfer'
                        ]
                    ]
                ]
            ],
            'EMBA7110' => [
                'description' => 'This course examines international business operations and global management strategies. Students will learn about cross-cultural management, international trade, global supply chains, and international business law. The course covers global market entry strategies, international negotiations, and managing multinational operations.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Analyze international business environments and global market opportunities.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Global Business Environment and Market Analysis'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Design global market entry strategies and international business plans.',
                        'mqf_alignment' => 'Practical Skills (C3); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'International Market Entry Strategies'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Evaluate cross-cultural management challenges and solutions.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Cross-Cultural Management and Global Leadership'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Apply international business law and policy frameworks.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'International Business Law and Policy Compliance'
                        ]
                    ]
                ]
            ],
            'EMBA7111' => [
                'description' => 'This course provides comprehensive training in business research methods and academic writing. Students will learn to design research studies, collect and analyze data, and present research findings. The course covers quantitative and qualitative research methods, statistical analysis, and research ethics for business applications.',
                'assessment_methods' => 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
                'duration' => '4 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Design rigorous research studies using appropriate methodologies.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Research Design and Methodology Selection'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Apply quantitative and qualitative research methods effectively.',
                        'mqf_alignment' => 'Practical Skills (C3); Numerical & Analytical Skills (C4)',
                        'order' => 2,
                        'topics' => [
                            'Data Collection and Sampling Techniques'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Analyze research data using statistical and analytical tools.',
                        'mqf_alignment' => 'Digital Skills (C7); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Statistical Analysis and Data Interpretation'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Present research findings and adhere to academic integrity standards.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Research Ethics and Academic Writing'
                        ]
                    ]
                ]
            ],
            'EMBA7112' => [
                'description' => 'This capstone course integrates all EMBA learning through a comprehensive strategic project. Students will conduct independent research, apply strategic management concepts, and develop solutions to real business challenges. The course emphasizes practical application, critical thinking, and professional presentation of strategic recommendations.',
                'assessment_methods' => 'Project Proposal (20%) + Research Progress (30%) + Final Presentation (30%) + Written Report (20%)',
                'duration' => '8 weeks',
                'clos' => [
                    [
                        'clo_code' => 'CLO1',
                        'description' => 'Integrate knowledge from all EMBA courses in a comprehensive strategic analysis.',
                        'mqf_alignment' => 'Knowledge & Understanding (C1); Cognitive Skills (C2)',
                        'order' => 1,
                        'topics' => [
                            'Strategic Analysis and Integration of EMBA Concepts'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO2',
                        'description' => 'Conduct independent research and apply strategic management frameworks.',
                        'mqf_alignment' => 'Practical Skills (C3); Digital Skills (C7)',
                        'order' => 2,
                        'topics' => [
                            'Independent Research and Data Collection'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO3',
                        'description' => 'Develop innovative solutions to complex business challenges.',
                        'mqf_alignment' => 'Numerical & Analytical Skills (C4); Communication (C8)',
                        'order' => 3,
                        'topics' => [
                            'Strategic Solution Development and Innovation'
                        ]
                    ],
                    [
                        'clo_code' => 'CLO4',
                        'description' => 'Present strategic recommendations with professional standards and ethical considerations.',
                        'mqf_alignment' => 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)',
                        'order' => 4,
                        'topics' => [
                            'Professional Presentation and Strategic Implementation'
                        ]
                    ]
                ]
            ]
        ];

        foreach ($subjects as $subjectCode => $data) {
            // Update subject
            $subject = Subject::where('code', $subjectCode)->first();
            if ($subject) {
                $subject->update([
                    'description' => $data['description'],
                    'assessment_methods' => $data['assessment_methods'],
                    'duration' => $data['duration']
                ]);

                // Add CLOs
                foreach ($data['clos'] as $cloData) {
                    $clo = CourseClo::create([
                        'subject_code' => $subjectCode,
                        'clo_code' => $cloData['clo_code'],
                        'description' => $cloData['description'],
                        'mqf_alignment' => $cloData['mqf_alignment'],
                        'order' => $cloData['order']
                    ]);

                    // Add topics for this CLO
                    foreach ($cloData['topics'] as $topicTitle) {
                        CourseTopic::create([
                            'subject_code' => $subjectCode,
                            'clo_code' => $cloData['clo_code'],
                            'topic_title' => $topicTitle,
                            'order' => 1
                        ]);
                    }
                }
            }
        }
    }
}
