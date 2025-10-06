<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - Page 1 | {{ $exStudent->name }} | Olympia Education</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Times New Roman', serif;
        }
        
        .transcript-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .transcript {
            background: white;
            border: 2px solid #2c3e50;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            min-height: 800px;
        }
        
        .transcript-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .university-logo {
            width: 60px;
            height: 60px;
            background: #2c3e50;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .university-name {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .university-subtitle {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .transcript-title {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .student-info {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 0.95rem;
        }
        
        .info-value {
            color: #495057;
            font-size: 0.95rem;
        }
        
        .academic-records {
            margin-bottom: 30px;
        }
        
        .semester-header {
            background: #2c3e50;
            color: white;
            padding: 10px 15px;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        
        .courses-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        
        .courses-table th,
        .courses-table td {
            border: 1px solid #dee2e6;
            padding: 8px 12px;
            text-align: left;
        }
        
        .courses-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .courses-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .grade-excellent { color: #28a745; font-weight: bold; }
        .grade-good { color: #17a2b8; font-weight: bold; }
        .grade-average { color: #ffc107; font-weight: bold; }
        .grade-poor { color: #dc3545; font-weight: bold; }
        
        .summary-section {
            background: #e8f4fd;
            border: 1px solid #bee5eb;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .summary-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #0c5460;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .summary-item {
            text-align: center;
            padding: 10px;
            background: white;
            border-radius: 5px;
            border: 1px solid #bee5eb;
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: #0c5460;
            font-weight: bold;
        }
        
        .summary-value {
            font-size: 1.1rem;
            color: #2c3e50;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .page-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }
        
        .page-info {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .navigation-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-nav {
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
        }
        
        @media print {
            .print-button,
            .back-button,
            .navigation-buttons {
                display: none;
            }
            
            .transcript-container {
                padding: 0;
            }
            
            .transcript {
                box-shadow: none;
                border: 2px solid #000;
            }
        }
        
        @media (max-width: 768px) {
            .transcript {
                padding: 20px;
            }
            
            .university-name {
                font-size: 1.5rem;
            }
            
            .transcript-title {
                font-size: 1.4rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .courses-table {
                font-size: 0.8rem;
            }
            
            .courses-table th,
            .courses-table td {
                padding: 6px 8px;
            }
        }
    </style>
</head>
<body>
    <div class="transcript-container">
        <!-- Action Buttons -->
        <div class="print-button">
            <button class="btn btn-primary" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
        
        <div class="back-button">
            <a href="{{ route('ex-student.dashboard', ['student_id' => $exStudent->student_id]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="{{ route('ex-student.certificate', ['student_id' => $exStudent->student_id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-certificate"></i> View Certificate
            </a>
        </div>
        
        <!-- Transcript -->
        <div class="transcript">
            <div class="transcript-header">
                <div class="university-logo">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="university-name">Olympia Education</h1>
                <p class="university-subtitle">Excellence in Education Since 1995</p>
                <h2 class="transcript-title">Academic Transcript</h2>
            </div>
            
            <div class="student-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Student Name:</span>
                        <span class="info-value">{{ $exStudent->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Student ID:</span>
                        <span class="info-value">{{ $exStudent->student_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Program:</span>
                        <span class="info-value">{{ $exStudent->program ?? 'Bachelor of Science' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Graduation Date:</span>
                        <span class="info-value">{{ $exStudent->graduation_date }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Transcript Date:</span>
                        <span class="info-value">{{ now()->format('F j, Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Page:</span>
                        <span class="info-value">1 of 2</span>
                    </div>
                </div>
            </div>
            
            <div class="academic-records">
                <!-- Semester 1 -->
                <div class="semester-header">
                    Semester 1 - Academic Year 2021/2022
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CS101</td>
                            <td>Introduction to Programming</td>
                            <td>3</td>
                            <td class="grade-excellent">A</td>
                            <td>4.00</td>
                        </tr>
                        <tr>
                            <td>MATH101</td>
                            <td>Calculus I</td>
                            <td>4</td>
                            <td class="grade-good">B+</td>
                            <td>3.33</td>
                        </tr>
                        <tr>
                            <td>ENG101</td>
                            <td>English Communication</td>
                            <td>3</td>
                            <td class="grade-excellent">A-</td>
                            <td>3.67</td>
                        </tr>
                        <tr>
                            <td>PHY101</td>
                            <td>Physics I</td>
                            <td>4</td>
                            <td class="grade-good">B</td>
                            <td>3.00</td>
                        </tr>
                        <tr>
                            <td>CS102</td>
                            <td>Computer Fundamentals</td>
                            <td>3</td>
                            <td class="grade-excellent">A</td>
                            <td>4.00</td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Semester 2 -->
                <div class="semester-header">
                    Semester 2 - Academic Year 2021/2022
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>CS201</td>
                            <td>Data Structures</td>
                            <td>3</td>
                            <td class="grade-excellent">A</td>
                            <td>4.00</td>
                        </tr>
                        <tr>
                            <td>MATH201</td>
                            <td>Calculus II</td>
                            <td>4</td>
                            <td class="grade-good">B+</td>
                            <td>3.33</td>
                        </tr>
                        <tr>
                            <td>ENG201</td>
                            <td>Technical Writing</td>
                            <td>3</td>
                            <td class="grade-excellent">A-</td>
                            <td>3.67</td>
                        </tr>
                        <tr>
                            <td>PHY201</td>
                            <td>Physics II</td>
                            <td>4</td>
                            <td class="grade-good">B</td>
                            <td>3.00</td>
                        </tr>
                        <tr>
                            <td>CS203</td>
                            <td>Database Systems</td>
                            <td>3</td>
                            <td class="grade-excellent">A</td>
                            <td>4.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="summary-section">
                <div class="summary-title">Academic Summary - Year 1</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Total Credits</div>
                        <div class="summary-value">34</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">GPA Year 1</div>
                        <div class="summary-value">3.67</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Status</div>
                        <div class="summary-value">Good Standing</div>
                    </div>
                </div>
            </div>
            
            <div class="page-footer">
                <div class="page-info">
                    <strong>Page 1 of 2</strong><br>
                    <small>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</small>
                </div>
                <div class="navigation-buttons">
                    <a href="{{ route('ex-student.transcript2', ['student_id' => $exStudent->student_id]) }}" class="btn btn-primary btn-nav">
                        Next Page <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
