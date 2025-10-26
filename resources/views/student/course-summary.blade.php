@extends('layouts.app')

@section('title', $program->name . ' | Student | Olympia Education')

@section('content')
<div class="student-dashboard">
    <!-- Student Navigation Bar -->
    @include('student.partials.student-navbar')
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            @include('student.partials.sidebar')

            <!-- Main Content -->
            <div class="main-content">
                <div class="course-summary-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1>{{ $program->name }}</h1>
                            <p class="text-muted">{{ $program->code }} - {{ ucfirst($program->level) }} Program</p>
                        </div>
                        <div class="program-info">
                            <span class="badge bg-primary">12-24 months</span>
                        </div>
                    </div>
                </div>

                <!-- Program Information Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Program Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <tbody>
                                    <tr>
                                        <td class="info-label"><strong>Program Name</strong></td>
                                        <td class="info-value" colspan="3">{{ $program->name }} ({{ $program->code }})</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Teaching Mode</strong></td>
                                        <td class="info-value">Online - ODL</td>
                                        <td class="info-label"><strong>Duration</strong></td>
                                        <td class="info-value">12-24 Months</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Numbers of Subject</strong></td>
                                        <td class="info-value">55</td>
                                        <td class="info-label"><strong>Credit Hours</strong></td>
                                        <td class="info-value">122</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Teaching Hours per subject</strong></td>
                                        <td class="info-value">16-Hour Intensive + 8-Hours Online Support</td>
                                        <td class="info-label"><strong>No. of Class per subject</strong></td>
                                        <td class="info-value">2 Classes + Online Support</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Short Quiz Part 1</strong></td>
                                        <td class="info-value">4 Hour</td>
                                        <td class="info-label"><strong>Short Quiz Part 2</strong></td>
                                        <td class="info-value">4 Hour</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Assignment/Study Case</strong></td>
                                        <td class="info-value">4 Hour</td>
                                        <td class="info-label"><strong>Case Study Presentation</strong></td>
                                        <td class="info-value">4 Hour</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label"><strong>Int'l Qualification Framework</strong></td>
                                        <td class="info-value">Bachelor Level 6</td>
                                        <td class="info-label"><strong>Module Type</strong></td>
                                        <td class="info-value">Mandatory Core Module</td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                    </div>
                </div>


                <!-- Program Synopsis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Program Synopsis</h3>
                    </div>
                    <div class="card-body">
                        <h4 class="mb-3">{{ $program->name }}</h4>
                        <p class="lead">
                            @if(strpos($user->programme_name, 'EBBA') !== false)
                                The Executive Bachelor in Business Administration (EBBA) is crafted for mid-level professionals, aspiring managers, and adult learners seeking to enhance their managerial capabilities and business knowledge. This program bridges practical experience with foundational and applied business theory, equipping learners with the competencies required to thrive in today's dynamic and competitive business environment. The EBBA integrates essential undergraduate-level modules covering key areas such as strategic management, human resource management, marketing, finance, and digital transformation. Designed with a strong emphasis on real-world application, the program fosters critical thinking, effective decision-making, and leadership development across diverse industries.
                            @elseif(strpos($user->programme_name, 'EMBA') !== false)
                                The Executive Master in Business Administration (EMBA) is designed for senior executives and professionals who aspire to deepen their strategic thinking, leadership, and business acumen. This programme integrates advanced postgraduate-level modules that deliver both academic excellence and practical career advancement opportunities. Each module is developed to reflect the evolving demands of global business, equipping learners with the knowledge and tools to lead effectively in complex, fast-paced environments.
                            @else
                                This program is designed to provide comprehensive business education and professional development opportunities. The curriculum is structured to meet the evolving demands of the business world and equip learners with essential knowledge and skills for career advancement.
                            @endif
                        </p>
                        <p>
                            @if(strpos($user->programme_name, 'EBBA') !== false)
                                The EBBA follows a Dual Award or Triple Award pathway (depending on the selected track), with the option to earn the Level 6 – Advanced Diploma in Business Administration awarded by the Chartered Management Institute (CMI), UK. Successful graduates may also qualify for the Chartered Manager (ChMgr) designation, positioning them as globally recognised, professionally certified managers. Delivered through a flexible, blended-learning format, the EBBA accommodates working professionals by offering part-time, modular, and remote study options. Learners benefit from expert instruction by seasoned academics and industry practitioners, supported by interactive, case-based, and experiential learning approaches.
                            @elseif(strpos($user->programme_name, 'EMBA') !== false)
                                The EMBA is delivered by a team of highly qualified academics and accomplished business practitioners, ensuring a rich, diverse, and engaging learning experience across multiple instructional methodologies.
                            @else
                                The program is delivered by qualified academics and industry professionals, ensuring a comprehensive and practical learning experience.
                            @endif
                        </p>
                        <p>
                            @if(strpos($user->programme_name, 'EMBA') !== false)
                                This programme is a Triple Award pathway, including the prestigious Level 7 – Professional Diploma in Business Administration awarded by the Chartered Management Institute (CMI), UK. Upon successful completion, graduates are eligible to receive the Chartered Manager (ChMgr) designation—recognised as the gold standard for professional managers in the UK and internationally.
                            @else
                                Upon successful completion, graduates will receive a recognized qualification that enhances their professional credentials and career prospects.
                            @endif
                        </p>
                        <p>
                            @if(strpos($user->programme_name, 'EBBA') !== false)
                                The EBBA offers a flexible, accessible format designed to accommodate working professionals' schedules, enabling participants to study at their own pace while maintaining their career commitments.
                            @elseif(strpos($user->programme_name, 'EMBA') !== false)
                                The EMBA offers a flexible, accessible format designed to accommodate demanding professional schedules, enabling participants to study at their own pace, from anywhere in the world.
                            @else
                                The program offers a flexible, accessible format designed to accommodate professional schedules, enabling participants to study at their own pace.
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Program Learning Outcomes Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Program Learning Outcomes (PLOs) - Aligned with Malaysia Qualification Framework 2.0 Domains</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 8%;">PLO</th>
                                        <th style="width: 45%;">Description</th>
                                        <th style="width: 20%;">MQF 2.0 Domain</th>
                                        <th style="width: 27%;">Mapped Courses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>PLO1</strong></td>
                                        <td>Demonstrate comprehensive and integrated knowledge of strategic business functions and leadership in diverse organizational contexts.</td>
                                        <td><strong>C1: Knowledge & Understanding</strong></td>
                                        <td>Strategic Management, Strategic HRM, Strategic Marketing</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO2</strong></td>
                                        <td>Analyze complex business problems and make strategic decisions using critical, analytical, and evidence-based approaches.</td>
                                        <td><strong>C2: Cognitive Skills</strong></td>
                                        <td>Business Analytics, Accounting & Finance for Decision Making, Business Economics</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO3</strong></td>
                                        <td>Apply advanced managerial and entrepreneurial skills to formulate strategies for innovation, technology adoption, and business growth.</td>
                                        <td><strong>C3: Practical Skills</strong></td>
                                        <td>Innovation & Technology Entrepreneurship, Digital Business, Strategic Marketing</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO4</strong></td>
                                        <td>Interpret and use financial and economic data for strategic planning, budgeting, and decision-making.</td>
                                        <td><strong>C4: Numerical & Analytical Skills</strong></td>
                                        <td>Accounting & Finance for Decision Making, Business Economics</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO5</strong></td>
                                        <td>Collaborate effectively in teams and lead diverse stakeholders to achieve common business goals.</td>
                                        <td><strong>C5: Interpersonal Skills & Responsibility</strong></td>
                                        <td>Organizational Behaviour, International Business Management & Policy</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO6</strong></td>
                                        <td>Uphold ethical, legal, and professional standards in decision-making processes at national and global levels.</td>
                                        <td><strong>C6: Ethics & Professionalism</strong></td>
                                        <td>Strategic HRM, International Business Management & Policy</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO7</strong></td>
                                        <td>Leverage digital tools and emerging technologies to solve business problems and enhance operational efficiency.</td>
                                        <td><strong>C7: Digital Skills</strong></td>
                                        <td>Digital Business, Business Analytics</td>
                                    </tr>
                                    <tr>
                                        <td><strong>PLO8</strong></td>
                                        <td>Communicate effectively through structured reports, presentations, and dialogue with both technical and non-technical stakeholders.</td>
                                        <td><strong>C8: Communication Skills</strong></td>
                                        <td>Research Methodology, Project, Strategic Management</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- Subject Details Display -->
                <div id="subjectDetailsSection" class="card mb-4" style="display: none;">
                    <div class="card-header">
                        <h3><span id="subjectTitle">Subject Details</span></h3>
                        <button type="button" class="btn-close" onclick="closeSubjectDetails()" aria-label="Close"></button>
                    </div>
                    <div class="card-body">
                        <div id="subjectDetailsContent">
                            <!-- Subject details will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="/maintenance" class="btn btn-primary btn-lg w-100 mb-2">
                                    Announcements
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="/maintenance" class="btn btn-success btn-lg w-100 mb-2">
                                    Course Content
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="/maintenance" class="btn btn-warning btn-lg w-100 mb-2">
                                    Assignments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Override Bootstrap with higher specificity */
.student-dashboard .course-summary-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    color: white !important;
    padding: 0.5rem 0.75rem !important;
    border-radius: 4px !important;
    margin-bottom: 0.25rem !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

.student-dashboard .course-summary-header h1 {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    margin-bottom: 0.1rem !important;
    line-height: 1.2 !important;
    color: white !important;
}

.student-dashboard .course-summary-header p {
    font-size: 0.75rem !important;
    opacity: 0.9 !important;
    margin: 0 !important;
    line-height: 1.3 !important;
    color: white !important;
}

.student-dashboard .card {
    border: none !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    margin-bottom: 1rem !important;
    background: white !important;
}

.student-dashboard .card:first-of-type {
    margin-top: 0.25rem !important;
}

.student-dashboard .card-header {
    background: #f8f9fa !important;
    border-bottom: 1px solid #e9ecef !important;
    border-radius: 8px 8px 0 0 !important;
    padding: 0.75rem 1rem !important;
}

.student-dashboard .card-header h3 {
    font-size: 1.1rem !important;
    font-weight: 600 !important;
    margin: 0 !important;
    color: #495057 !important;
}

.student-dashboard .card-body {
    padding: 1rem !important;
}

.student-dashboard .table td {
    padding: 0.75rem 1rem !important;
    vertical-align: middle !important;
    border: none !important;
}

.student-dashboard .table-striped tbody tr {
    border-bottom: 1px solid #f1f3f4 !important;
}

.student-dashboard .table-striped tbody tr:hover {
    background-color: #f8f9fa !important;
}

.student-dashboard .subject-code {
    font-weight: 600 !important;
    color: #2c3e50 !important;
    font-size: 0.9rem !important;
    margin-bottom: 0.25rem !important;
    display: block !important;
}

.student-dashboard .subject-title {
    color: #495057 !important;
    font-size: 0.85rem !important;
    margin-bottom: 0.25rem !important;
    display: block !important;
    line-height: 1.3 !important;
}

.student-dashboard .lecturer-name {
    color: #0056d2 !important;
    font-size: 0.8rem !important;
    text-decoration: none !important;
    display: block !important;
    margin-bottom: 0.25rem !important;
}

.student-dashboard .lecturer-name:hover {
    text-decoration: underline !important;
}

.student-dashboard .class-code {
    background: #e8f5e8 !important;
    color: #2d5a2d !important;
    padding: 0.2rem 0.5rem !important;
    border-radius: 4px !important;
    font-size: 0.75rem !important;
    font-weight: 500 !important;
    display: inline-block !important;
}

/* Mobile responsive overrides */
@media (max-width: 768px) {
    .student-dashboard .course-summary-header {
        padding: 0.4rem 0.6rem !important;
        margin-bottom: 0.15rem !important;
    }
    
    .student-dashboard .card:first-of-type {
        margin-top: 0.15rem !important;
    }
    
    .student-dashboard .course-summary-header h1 {
        font-size: 1rem !important;
    }
    
    .student-dashboard .course-summary-header p {
        font-size: 0.7rem !important;
    }
    
    .student-dashboard .table td {
        padding: 0.5rem 0.75rem !important;
    }
    
    .student-dashboard .subject-code {
        font-size: 0.85rem !important;
    }
    
    .student-dashboard .subject-title {
        font-size: 0.8rem !important;
    }
    
    .student-dashboard .lecturer-name {
        font-size: 0.75rem !important;
    }
    
    .student-dashboard .class-code {
        font-size: 0.7rem !important;
        padding: 0.15rem 0.4rem !important;
    }
}

/* Information Table Styles */
.info-label {
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 12px 16px;
    border-right: 1px solid #dee2e6;
    width: 25%;
    vertical-align: middle;
}

.info-value {
    padding: 12px 16px;
    color: #212529;
    font-weight: 500;
    vertical-align: middle;
}

.table-bordered td {
    border: 1px solid #dee2e6;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}



.subject-row:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px);
    transition: all 0.3s ease;
    cursor: pointer;
}

.table {
    margin-bottom: 0;
}

.table th {
    border-top: none;
    font-weight: 600;
    background-color: #f8f9fa;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 1rem;
}

.table td {
    vertical-align: middle;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.badge {
    font-size: 0.8rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.badge.bg-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.badge.bg-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%) !important;
    color: #2d3748 !important;
}

.badge.bg-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
    color: white !important;
}

.badge.bg-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
    color: white !important;
}

.card-header h3 {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
}

.card-header h3 i {
    margin-right: 0.5rem;
    color: #667eea;
}

.lead {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #495057;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    border: none;
    color: #2d3748;
    box-shadow: 0 4px 15px rgba(86, 171, 47, 0.3);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(86, 171, 47, 0.4);
    color: #2d3748;
}

.btn-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border: none;
    box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
}

.btn-warning:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(240, 147, 251, 0.4);
}

.btn i {
    margin-right: 0.5rem;
}

/* Subject Details Section */
#subjectDetailsSection .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

#subjectDetailsSection .btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
    transition: color 0.3s ease;
}

#subjectDetailsSection .btn-close:hover {
    color: #dc3545;
}

#subjectDetailsContent h5 {
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    color: #2d3748;
    font-weight: 600;
}

#subjectDetailsContent h5:first-child {
    margin-top: 0;
}

#subjectDetailsContent ul li {
    margin-bottom: 0.5rem;
    padding-left: 0.5rem;
}

/* Program Details Container Styling - Matching Picture Layout */
.program-details-container {
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.program-detail-row {
    display: flex;
    border-bottom: 1px solid #dee2e6;
    min-height: 50px;
    align-items: center;
}

.program-detail-row:last-child {
    border-bottom: none;
}

.program-detail-row:nth-child(even) {
    background-color: #f8f9fa;
}

.program-detail-row:hover {
    background-color: #e9ecef;
    transition: background-color 0.2s ease;
}

.detail-label {
    flex: 0 0 45%;
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
    padding: 12px 16px;
    border-right: 1px solid #dee2e6;
    font-size: 14px;
    display: flex;
    align-items: center;
    min-height: 50px;
}

.detail-value {
    flex: 1;
    padding: 12px 16px;
    color: #212529;
    font-weight: 500;
    font-size: 14px;
    display: flex;
    align-items: center;
    min-height: 50px;
}

    
    .info-label,
    .info-value {
        padding: 8px 12px;
        font-size: 0.9rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .table th,
    .table td {
        padding: 0.75rem;
    }
    
    .program-detail-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .detail-label {
        flex: none;
        border-right: none;
        border-bottom: 1px solid #dee2e6;
        min-height: 40px;
        font-size: 13px;
    }
    
    .detail-value {
        flex: none;
        min-height: 40px;
        font-size: 13px;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Map subjects to their detailed information
const subjectDetailsMap = {
    'EMBA7101': {
        name: 'Strategic Human Resource Management',
        description: 'This course provides an advanced understanding of Strategic Human Resource Management (SHRM) and its role in driving organizational performance. It emphasizes alignment between HR strategies and business goals, exploring contemporary challenges, employment legislation, talent management, and HR analytics. Participants will gain practical skills in designing, implementing, and evaluating HR strategies for sustainable competitive advantage in complex business environments.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Critically evaluate the strategic roles and functions of HRM in achieving organizational effectiveness.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Design strategic human resource plans that align with organizational vision, mission, and business strategies.',
                mqf: 'Cognitive Skills (C2); Digital Skills (C7)'
            },
            {
                clo: 'CLO3',
                description: 'Apply relevant employment laws and regulatory frameworks in strategic HR decision-making.',
                mqf: 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6)'
            },
            {
                clo: 'CLO4',
                description: 'Use HR metrics and analytics tools to assess workforce performance and support strategic HR decisions.',
                mqf: 'Numerical & Analytical Skills (C4); Communication (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'The Strategic Role of HRM in Business Transformation'
            },
            {
                clo: 'CLO2',
                topic: 'Strategic Human Resource Planning and Alignment with Business Strategy'
            },
            {
                clo: 'CLO3',
                topic: 'Employment Law, Industrial Relations & Regulatory Compliance'
            },
            {
                clo: 'CLO4',
                topic: 'HR Analytics and Performance Metrics in Strategic Decision-Making'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7102': {
        name: 'Organisational Behaviour',
        description: 'This course explores the psychological, social, and structural dimensions of behaviour within organizations. It aims to equip learners with the ability to analyze individual, group, and organizational dynamics to improve workplace effectiveness. Emphasis is placed on leadership, motivation, communication, organizational culture, and change management. Learners will apply behavioural theories and models to real-world organizational issues to enhance leadership capacity and employee engagement in dynamic business environments.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Analyze the impact of individual behavior, group dynamics, and organizational systems on performance.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Apply motivation and leadership theories to improve team and organizational effectiveness.',
                mqf: 'Cognitive Skills (C2); Ethics & Professionalism (C6)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate strategies for managing diversity, communication, and conflict in the workplace.',
                mqf: 'Interpersonal Skills & Responsibility (C5); Communication (C8)'
            },
            {
                clo: 'CLO4',
                description: 'Propose evidence-based interventions for organizational development and change management.',
                mqf: 'Numerical & Analytical Skills (C4); Digital Skills (C7)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Understanding Individual and Group Behavior in Organizations'
            },
            {
                clo: 'CLO2',
                topic: 'Motivation, Leadership, and Power in Organizational Settings'
            },
            {
                clo: 'CLO3',
                topic: 'Communication, Conflict, and Diversity Management'
            },
            {
                clo: 'CLO4',
                topic: 'Organizational Change, Culture, and Development Strategies'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7103': {
        name: 'Strategic Management',
        description: 'This course focuses on the formulation, implementation, and evaluation of strategic decisions that enable organizations to achieve sustainable competitive advantage. Learners will explore strategic thinking, industry and internal analysis, strategic choice, and execution in various business contexts. Emphasis is placed on strategic leadership, corporate governance, and responding to global business challenges. By applying strategic tools and frameworks, learners will be prepared to lead organizations in a complex, fast-changing environment.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Analyze internal and external environments using strategic management tools to assess organizational competitiveness.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Formulate strategic plans that align with organizational goals and respond to industry trends and disruptions.',
                mqf: 'Cognitive Skills (C2); Digital Skills (C7)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate the ethical, governance, and leadership implications in strategic decision-making processes.',
                mqf: 'Ethics & Professionalism (C6); Interpersonal Skills & Responsibility (C5)'
            },
            {
                clo: 'CLO4',
                description: 'Communicate and defend strategic recommendations through evidence-based analysis and reporting.',
                mqf: 'Numerical & Analytical Skills (C4); Communication Skills (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Analyze internal and external environments using strategic management tools to assess organizational competitiveness.'
            },
            {
                clo: 'CLO2',
                topic: 'Formulate strategic plans that align with organizational goals and respond to industry trends and disruptions.'
            },
            {
                clo: 'CLO3',
                topic: 'Evaluate the ethical, governance, and leadership implications in strategic decision-making processes.'
            },
            {
                clo: 'CLO4',
                topic: 'Communicate and defend strategic recommendations through evidence-based analysis and reporting.'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '6 weeks'
    },
    'EMBA7104': {
        name: 'Strategic Marketing',
        description: 'This course explores advanced marketing strategies that drive organizational competitiveness in dynamic and global markets. Learners will critically examine market orientation, segmentation, positioning, branding, and value creation strategies. The course also integrates digital marketing, customer analytics, and innovation in strategic decision-making. Emphasis is placed on aligning marketing strategies with overall business objectives and managing marketing performance in complex environments.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Analyze the external marketing environment and internal capabilities to develop customer-centric strategies.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Design integrated marketing strategies that align with organizational goals and competitive positioning.',
                mqf: 'Cognitive Skills (C2); Digital Skills (C7)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate ethical, cultural, and sustainability considerations in strategic marketing decisions.',
                mqf: 'Ethics & Professionalism (C6); Interpersonal Skills & Responsibility (C5)'
            },
            {
                clo: 'CLO4',
                description: 'Utilize marketing analytics and metrics to measure effectiveness and enhance decision-making.',
                mqf: 'Numerical & Analytical Skills (C4); Communication Skills (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Strategic Market Analysis: Segmentation, Targeting & Positioning (STP)'
            },
            {
                clo: 'CLO2',
                topic: 'Strategic Marketing Mix: Product, Price, Place, Promotion Integration'
            },
            {
                clo: 'CLO3',
                topic: 'Global Marketing, Sustainability, and Ethical Marketing Considerations'
            },
            {
                clo: 'CLO4',
                topic: 'Marketing Metrics, ROI, and Digital Marketing Performance Analytics'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7105': {
        name: 'Accounting & Finance for Decision Making',
        description: 'This course equips learners with the essential financial and accounting knowledge to support strategic business decisions. It integrates financial reporting, management accounting, and corporate finance principles to evaluate business performance, investments, and risk. Emphasis is placed on interpreting financial statements, budgeting, capital structure, and using financial data for evidence-based decision-making in a dynamic business environment.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Interpret and analyze financial statements for evaluating organizational performance.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Apply management accounting techniques for planning, control, and strategic decision-making.',
                mqf: 'Cognitive Skills (C2); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate investment and financing decisions using appropriate financial tools and frameworks.',
                mqf: 'Cognitive Skills (C2); Digital Skills (C7)'
            },
            {
                clo: 'CLO4',
                description: 'Demonstrate ethical and professional accountability in financial decision-making and reporting.',
                mqf: 'Ethics & Professionalism (C6); Interpersonal Skills & Responsibility (C5)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Understanding and Interpreting Financial Statements (Balance Sheet, Income Statement, Cash Flow)'
            },
            {
                clo: 'CLO2',
                topic: 'Budgeting, Cost-Volume-Profit Analysis, and Variance Analysis'
            },
            {
                clo: 'CLO3',
                topic: 'Capital Budgeting, Working Capital Management & Financial Ratios'
            },
            {
                clo: 'CLO4',
                topic: 'Ethical Issues in Financial Reporting and Corporate Governance'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7106': {
        name: 'Business Analytics',
        description: 'This course introduces learners to data-driven decision-making through business analytics. It covers descriptive, predictive, and prescriptive analytics to support strategic and operational decisions. Emphasis is placed on data visualization, statistical tools, business intelligence systems, and real-time dashboards. The course aims to provide learners with practical experience in using analytics software to solve real-world business problems and communicate data insights effectively to stakeholders.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Demonstrate understanding of business analytics concepts, tools, and their application in decision-making.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Apply descriptive, predictive, and prescriptive analytics techniques to real-world business data.',
                mqf: 'Cognitive Skills (C2); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO3',
                description: 'Utilize digital tools and analytics software to generate actionable business insights.',
                mqf: 'Digital Skills (C7); Practical Skills (C3)'
            },
            {
                clo: 'CLO4',
                description: 'Communicate complex data insights clearly and effectively to diverse stakeholders.',
                mqf: 'Communication Skills (C8); Interpersonal Skills & Responsibility (C5)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Introduction to Business Analytics and Data-Driven Decision-Making'
            },
            {
                clo: 'CLO2',
                topic: 'Descriptive, Predictive, and Prescriptive Analytics Models'
            },
            {
                clo: 'CLO3',
                topic: 'Tools for Business Intelligence: Excel, Power BI, and Data Visualization'
            },
            {
                clo: 'CLO4',
                topic: 'Communicating Analytical Insights: Storytelling with Data and Dashboards'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7107': {
        name: 'Business Economics',
        description: 'This course provides a foundation in microeconomic and macroeconomic principles relevant to managerial decision-making. It equips learners with tools to analyze market structures, consumer behavior, pricing strategies, and economic policy impacts. Emphasis is placed on applying economic models to strategic business planning in domestic and global environments to enable informed economic decisions in complex markets.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Demonstrate understanding of key economic concepts and their relevance to business strategy.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Analyze market forces and consumer behavior using microeconomic tools.',
                mqf: 'Cognitive Skills (C2); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate the impact of macroeconomic indicators and policy on business environments.',
                mqf: 'Ethics & Professionalism (C6); Interpersonal Skills & Responsibility (C5)'
            },
            {
                clo: 'CLO4',
                description: 'Apply economic reasoning and data to solve business problems and support strategic decisions.',
                mqf: 'Digital Skills (C7); Communication Skills (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Core Concepts of Demand, Supply, Elasticity & Cost Structures'
            },
            {
                clo: 'CLO2',
                topic: 'Market Competition, Pricing Strategies & Game Theory'
            },
            {
                clo: 'CLO3',
                topic: 'National Income, Inflation, Interest Rates & Fiscal/Monetary Policies'
            },
            {
                clo: 'CLO4',
                topic: 'Data-Driven Economic Analysis & Business Forecasting Tools'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7108': {
        name: 'Digital Business',
        description: 'This course explores the transformation of traditional business models through digital technologies, platforms, and innovation. Learners will examine how businesses create value through digital channels, manage digital operations, and respond to digital disruption. The course covers e-business strategies, digital marketing, data-driven decision-making, and emerging technologies such as AI, blockchain, and IoT. Students will learn to lead digital initiatives that support innovation and organizational agility.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Demonstrate knowledge of digital business models, platforms, and transformation strategies.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Analyze the impact of digital disruption on value chains, customer engagement, and competitive advantage.',
                mqf: 'Cognitive Skills (C2); Ethics & Professionalism (C6)'
            },
            {
                clo: 'CLO3',
                description: 'Apply digital tools and technologies to design customer-centric digital business strategies.',
                mqf: 'Digital Skills (C7); Communication Skills (C8)'
            },
            {
                clo: 'CLO4',
                description: 'Evaluate the risks, governance, and cybersecurity considerations in digital business environments.',
                mqf: 'Numerical & Analytical Skills (C4); Interpersonal Skills & Responsibility (C5)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Digital Business Models, Ecosystems & Platform Economy'
            },
            {
                clo: 'CLO2',
                topic: 'Digital Disruption, Agile Strategies & Competitive Advantage'
            },
            {
                clo: 'CLO3',
                topic: 'Designing Digital Customer Journeys & Omni-Channel Strategies'
            },
            {
                clo: 'CLO4',
                topic: 'Cybersecurity, Risk Management & Digital Governance Frameworks'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7109': {
        name: 'Innovation and Technology Entrepreneurship',
        description: 'This course explores the intersection of innovation, entrepreneurship, and emerging technologies in creating new business ventures and transforming existing organizations. Learners will understand the innovation lifecycle, entrepreneurial mindset, opportunity identification, and technology commercialization. The course focuses on strategic thinking, lean startup methods, innovation ecosystems, and intellectual property management. It prepares learners to lead innovation-driven initiatives and manage entrepreneurial ventures in a digital economy.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Demonstrate an understanding of innovation frameworks and entrepreneurial strategies in technology-driven environments.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Analyze opportunities, risks, and value propositions in innovation and technology ventures.',
                mqf: 'Cognitive Skills (C2); Ethics & Professionalism (C6)'
            },
            {
                clo: 'CLO3',
                description: 'Develop a lean business model and technology roadmap for a new or existing venture.',
                mqf: 'Digital Skills (C7); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO4',
                description: 'Communicate and pitch innovative ideas effectively to stakeholders, investors, and partners.',
                mqf: 'Communication Skills (C8); Interpersonal Skills & Responsibility (C5)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Innovation Theories, Disruption Models & Entrepreneurship Ecosystems'
            },
            {
                clo: 'CLO2',
                topic: 'Opportunity Recognition, Risk Evaluation & Strategic Fit'
            },
            {
                clo: 'CLO3',
                topic: 'Lean Startup, Business Model Canvas & Technology Commercialization'
            },
            {
                clo: 'CLO4',
                topic: 'Pitching, Storytelling, and Securing Funding for Innovation Ventures'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7110': {
        name: 'International Business Management & Policy',
        description: 'This course provides an in-depth understanding of global business dynamics and the policy frameworks affecting international operations. Learners will explore cross-border strategies, global market entry, international trade agreements, foreign investment policies, and global governance institutions. Emphasis is placed on analyzing the influence of political, legal, economic, and cultural environments on business decisions. The course also discusses corporate diplomacy, compliance, and sustainability in the international context.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Demonstrate knowledge of international business theories, institutions, and policy frameworks.',
                mqf: 'Knowledge & Understanding (C1); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Analyze how global political, economic, and regulatory environments impact business strategy.',
                mqf: 'Cognitive Skills (C2); Ethics & Professionalism (C6)'
            },
            {
                clo: 'CLO3',
                description: 'Evaluate strategies for international market entry, global expansion, and cross-cultural management.',
                mqf: 'Numerical & Analytical Skills (C4); Interpersonal Skills & Responsibility (C5)'
            },
            {
                clo: 'CLO4',
                description: 'Communicate international business strategies and policy considerations effectively to diverse stakeholders.',
                mqf: 'Communication Skills (C8); Digital Skills (C7)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Globalization, Trade Agreements & International Business Institutions (e.g., WTO, IMF)'
            },
            {
                clo: 'CLO2',
                topic: 'Political Risk, Legal Systems & Foreign Direct Investment Policies'
            },
            {
                clo: 'CLO3',
                topic: 'International Market Entry Strategies, Cultural Intelligence & Global HR'
            },
            {
                clo: 'CLO4',
                topic: 'Corporate Governance, Global Ethics & International Business Communication'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7111': {
        name: 'Research Methodology',
        description: 'This course equips learners with the fundamental knowledge and skills required to conduct rigorous business research. It covers research design, data collection methods (qualitative and quantitative), data analysis techniques, and ethical considerations in research. Emphasis is placed on formulating research questions, selecting appropriate methodologies, and communicating research findings effectively.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Formulate clear research questions and design appropriate research methodologies for business problems.',
                mqf: 'Cognitive Skills (C2); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Apply various data collection and analysis techniques (qualitative and quantitative) to gather and interpret research data.',
                mqf: 'Practical Skills (C3); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO3',
                description: 'Critically evaluate research findings and communicate them effectively in a structured report.',
                mqf: 'Cognitive Skills (C2); Communication Skills (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Foundations of Business Research: Problem Definition, Literature Review, Theoretical Frameworks'
            },
            {
                clo: 'CLO2',
                topic: 'Data Collection & Analysis: Survey Design, Interviews, Statistical Analysis, Qualitative Methods'
            },
            {
                clo: 'CLO3',
                topic: 'Research Reporting & Evaluation: Academic Writing, Referencing, Validity and Reliability'
            }
        ],
        assessment: 'Attendances & Participation (30%) + Short Quiz Part 1&2 (40%) + Reflection Report/Presentation (30%)',
        duration: '4 weeks'
    },
    'EMBA7112': {
        name: 'Strategic Capstone Project (Independent + Supervised Research & Application)',
        description: 'This capstone project subject enables learners to demonstrate mastery of business administration concepts by solving real-world business challenges through an integrative and strategic approach. Students will select an issue, opportunity, or innovation relevant to their industry or organization and apply cross-disciplinary knowledge—ranging from strategic HR and finance to innovation and digital transformation—to develop a research-backed, actionable business solution. The project culminates in a comprehensive Final Strategic Report, Reflective Learning Report, and a Presentation/Defense, simulating executive-level consulting or strategic planning deliverables.',
        clos: [
            {
                clo: 'CLO1',
                description: 'Formulate strategic business problems or opportunities through systematic research and analysis.',
                mqf: 'Knowledge & Understanding (C1); Cognitive Skills (C2); Practical Skills (C3)'
            },
            {
                clo: 'CLO2',
                description: 'Integrate knowledge from HR, strategy, marketing, finance, economics, analytics, digital innovation, and international business to propose viable solutions.',
                mqf: 'Knowledge & Understanding (C1); Cognitive Skills (C2); Numerical & Analytical Skills (C4)'
            },
            {
                clo: 'CLO3',
                description: 'Demonstrate effective project planning, data collection, and evidence-based decision-making.',
                mqf: 'Practical Skills (C3); Numerical & Analytical Skills (C4); Digital Skills (C7)'
            },
            {
                clo: 'CLO4',
                description: 'Communicate findings, recommendations, and personal reflections effectively through a professional report and presentation.',
                mqf: 'Interpersonal Skills & Responsibility (C5); Ethics & Professionalism (C6); Communication Skills (C8)'
            }
        ],
        topics: [
            {
                clo: 'CLO1',
                topic: 'Strategic Problem Identification & Research Design'
            },
            {
                clo: 'CLO2',
                topic: 'Integrated Subject Knowledge Application & Cross-Disciplinary Analysis'
            },
            {
                clo: 'CLO3',
                topic: 'Data Collection, Analysis & Evidence-Based Decision Making'
            },
            {
                clo: 'CLO4',
                topic: 'Professional Communication & Reflective Learning'
            }
        ],
        assessment: 'Proposal Document (10%) + Literature & Industry Review (15%) + Data Collection & Analysis (20%) + Strategic Recommendation (25%) + Reflective Report (15%) + Final Presentation/Viva (15%)',
        duration: '12 weeks'
    }
};


function viewSubject(subjectCode) {
    const subjectData = subjectDetailsMap[subjectCode];
    if (subjectData) {
        // Update the subject title
        document.getElementById('subjectTitle').textContent = subjectData.name;
        
        // Create detailed content
        let content = `
            <div class="row">
                <div class="col-12">
                    <h5><i class="fas fa-info-circle text-primary"></i> Course Description</h5>
                    <p>${subjectData.description}</p>
                </div>
            </div>
        `;

        // Add CLOs table if available
        if (subjectData.clos && subjectData.clos.length > 0) {
            content += `
                <div class="row mt-4">
                    <div class="col-12">
                        <h5><i class="fas fa-graduation-cap text-success"></i> Course Learning Outcomes (CLOs)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 10%;">CLO</th>
                                        <th style="width: 60%;">Learning Outcome Description</th>
                                        <th style="width: 30%;">MQF2.0 Alignment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${subjectData.clos.map(clo => `
                                        <tr>
                                            <td><strong>${clo.clo}</strong></td>
                                            <td>${clo.description}</td>
                                            <td><strong>${clo.mqf}</strong></td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        // Add Topics table if available
        if (subjectData.topics && subjectData.topics.length > 0) {
            content += `
                <div class="row mt-4">
                    <div class="col-12">
                        <h5><i class="fas fa-book text-info"></i> Topics Covered According to CLOs and Assessment Methods</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 10%;">CLO</th>
                                        <th style="width: 70%;">Topic</th>
                                        <th style="width: 20%;">Assessment Methods</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${subjectData.topics.map((topic, index) => `
                                        <tr>
                                            <td><strong>${topic.clo}</strong></td>
                                            <td>${index + 1}. ${topic.topic}</td>
                                            <td>${index === 0 ? subjectData.assessment : ''}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
        }

        
        // Update the content
        document.getElementById('subjectDetailsContent').innerHTML = content;
        
        // Show the subject details section
        document.getElementById('subjectDetailsSection').style.display = 'block';
        
        // Scroll to the details section
        document.getElementById('subjectDetailsSection').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    } else {
        // Fallback to maintenance page if subject not found
        window.location.href = '/maintenance?subject=' + subjectCode;
    }
}

function closeSubjectDetails() {
    document.getElementById('subjectDetailsSection').style.display = 'none';
}

// Add event listeners for subject rows
document.addEventListener('DOMContentLoaded', function() {
    const subjectRows = document.querySelectorAll('.subject-row');
    subjectRows.forEach(row => {
        row.addEventListener('click', function() {
            const subjectCode = this.getAttribute('data-subject-code');
            viewSubject(subjectCode);
        });
    });
});
</script>
@endpush
