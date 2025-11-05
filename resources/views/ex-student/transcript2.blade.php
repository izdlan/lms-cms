<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Transcript - Page 2 | {{ $exStudent->name }} | Olympia Education</title>
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
        
        .final-summary {
            background: #e8f4fd;
            border: 2px solid #bee5eb;
            border-radius: 10px;
            padding: 25px;
            margin-top: 30px;
        }
        
        .summary-title {
            font-size: 1.4rem;
            font-weight: bold;
            color: #0c5460;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #bee5eb;
        }
        
        .summary-label {
            font-size: 0.9rem;
            color: #0c5460;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .summary-value {
            font-size: 1.3rem;
            color: #2c3e50;
            font-weight: bold;
        }
        
        .cgpa-highlight {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
        }
        
        .honors-section {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: center;
        }
        
        .honors-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        
        .honors-text {
            color: #856404;
            font-size: 1.1rem;
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
        
        @php 
            $candidates = [];
            $ic = isset($exStudent->ic) ? $exStudent->ic : (isset($exStudent->ic_passport) ? $exStudent->ic_passport : '');
            $studentIdRaw = (string)($exStudent->student_id ?? '');
            $icRaw = (string)$ic;
            $icDigits = preg_replace('/\D/', '', $icRaw);
            $sidDigits = preg_replace('/\D/', '', $studentIdRaw);
            // Only use digits-only names
            foreach ([$icDigits, $sidDigits] as $base) {
                if ($base && !in_array($base, $candidates, true)) $candidates[] = $base;
            }
            $imagePath = null;
            $exts = ['png','jpg','jpeg'];
            foreach ($candidates as $name) {
                foreach ($exts as $ext) {
                    $rel = 'assets/default/img/transcript/' . $name . '.' . $ext;
                    if (file_exists(public_path($rel))) { $imagePath = asset($rel); break 2; }
                }
            }
        @endphp

        @if($imagePath)
        <!-- Static PNG transcript (single-image mode) -->
        <div class="text-center mb-4">
            <img src="{{ $imagePath }}" alt="Transcript Image" style="width:100%; max-width:1000px; height:auto; border:1px solid #ddd; border-radius:6px;"/>
        </div>
        @else
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
                        <span class="info-value">2 of 2</span>
                    </div>
                </div>
            </div>
            
            @php
                $records = $exStudent->academic_records ?? [];
                $yearKeys = array_keys($records);
                $secondYearKey = $yearKeys[1] ?? ($yearKeys[0] ?? 'Year 2');
                $thirdYearKey = $yearKeys[2] ?? null;
                $sem1 = $records[$secondYearKey]['semester_1'] ?? ($records[$secondYearKey]['year_1'] ?? []);
                $sem2 = $records[$secondYearKey]['semester_2'] ?? ($records[$secondYearKey]['year_2'] ?? []);
                $y2Credits = 0; $y2Weighted = 0;
                foreach (($sem1 ?? []) as $r) { $y2Credits += (int)($r['credits'] ?? 0); $y2Weighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                foreach (($sem2 ?? []) as $r) { $y2Credits += (int)($r['credits'] ?? 0); $y2Weighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                $y2Gpa = $y2Credits > 0 ? number_format($y2Weighted / $y2Credits, 2) : 'N/A';

                // Year 3 (if exists)
                $y3Sem1 = $thirdYearKey ? ($records[$thirdYearKey]['semester_1'] ?? ($records[$thirdYearKey]['year_1'] ?? [])) : [];
                $y3Sem2 = $thirdYearKey ? ($records[$thirdYearKey]['semester_2'] ?? ($records[$thirdYearKey]['year_2'] ?? [])) : [];
                $y3Credits = 0; $y3Weighted = 0;
                foreach (($y3Sem1 ?? []) as $r) { $y3Credits += (int)($r['credits'] ?? 0); $y3Weighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                foreach (($y3Sem2 ?? []) as $r) { $y3Credits += (int)($r['credits'] ?? 0); $y3Weighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                $y3Gpa = $y3Credits > 0 ? number_format($y3Weighted / $y3Credits, 2) : 'N/A';

                // Cumulative across all years
                $cumCredits = $y2Credits + $y3Credits; $cumWeighted = $y2Weighted + $y3Weighted;
                if (!empty($yearKeys)) {
                    // include first year from transcript1 as well if available in records
                    $y1Key = $yearKeys[0];
                    $y1Sem1 = $records[$y1Key]['semester_1'] ?? ($records[$y1Key]['year_1'] ?? []);
                    $y1Sem2 = $records[$y1Key]['semester_2'] ?? ($records[$y1Key]['year_2'] ?? []);
                    foreach (($y1Sem1 ?? []) as $r) { $cumCredits += (int)($r['credits'] ?? 0); $cumWeighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                    foreach (($y1Sem2 ?? []) as $r) { $cumCredits += (int)($r['credits'] ?? 0); $cumWeighted += ((float)($r['points'] ?? 0)) * (int)($r['credits'] ?? 0); }
                }
                $cgpa = $cumCredits > 0 ? number_format($cumWeighted / $cumCredits, 2) : 'N/A';
            @endphp
            <div class="academic-records">
                <div class="semester-header">
                    Semester 1 - Academic Year {{ is_string($secondYearKey) ? $secondYearKey : 'Year 2' }}
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($sem1 ?? []) as $row)
                        <tr>
                            <td>{{ $row['code'] ?? '' }}</td>
                            <td>{{ $row['name'] ?? '' }}</td>
                            <td>{{ $row['credits'] ?? '' }}</td>
                            <td>{{ $row['mark'] ?? '' }}</td>
                            <td class="{{ (($row['points'] ?? 0) >= 3.67) ? 'grade-excellent' : ((($row['points'] ?? 0) >= 3.0) ? 'grade-good' : 'grade-average') }}">{{ $row['grade'] ?? '' }}</td>
                            <td>{{ number_format((float)($row['points'] ?? 0), 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No subjects recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="semester-header">
                    Semester 2 - Academic Year {{ is_string($secondYearKey) ? $secondYearKey : 'Year 2' }}
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($sem2 ?? []) as $row)
                        <tr>
                            <td>{{ $row['code'] ?? '' }}</td>
                            <td>{{ $row['name'] ?? '' }}</td>
                            <td>{{ $row['credits'] ?? '' }}</td>
                            <td>{{ $row['mark'] ?? '' }}</td>
                            <td class="{{ (($row['points'] ?? 0) >= 3.67) ? 'grade-excellent' : ((($row['points'] ?? 0) >= 3.0) ? 'grade-good' : 'grade-average') }}">{{ $row['grade'] ?? '' }}</td>
                            <td>{{ number_format((float)($row['points'] ?? 0), 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted">No subjects recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($thirdYearKey)
                <div class="semester-header">
                    Semester 1 - Academic Year {{ $thirdYearKey }}
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($y3Sem1 ?? []) as $row)
                        <tr>
                            <td>{{ $row['code'] ?? '' }}</td>
                            <td>{{ $row['name'] ?? '' }}</td>
                            <td>{{ $row['credits'] ?? '' }}</td>
                            <td>{{ $row['mark'] ?? '' }}</td>
                            <td class="{{ (($row['points'] ?? 0) >= 3.67) ? 'grade-excellent' : ((($row['points'] ?? 0) >= 3.0) ? 'grade-good' : 'grade-average') }}">{{ $row['grade'] ?? '' }}</td>
                            <td>{{ number_format((float)($row['points'] ?? 0), 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No subjects recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="semester-header">
                    Semester 2 - Academic Year {{ $thirdYearKey }}
                </div>
                <table class="courses-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Credit Hours</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Grade Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($y3Sem2 ?? []) as $row)
                        <tr>
                            <td>{{ $row['code'] ?? '' }}</td>
                            <td>{{ $row['name'] ?? '' }}</td>
                            <td>{{ $row['credits'] ?? '' }}</td>
                            <td>{{ $row['mark'] ?? '' }}</td>
                            <td class="{{ (($row['points'] ?? 0) >= 3.67) ? 'grade-excellent' : ((($row['points'] ?? 0) >= 3.0) ? 'grade-good' : 'grade-average') }}">{{ $row['grade'] ?? '' }}</td>
                            <td>{{ number_format((float)($row['points'] ?? 0), 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted">No subjects recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @endif
            </div>
            
            <div class="final-summary">
                <div class="summary-title">Cumulative Grade Point Average</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Total Credits</div>
                        <div class="summary-value">{{ $cumCredits }}</div>
                    </div>
                    <div class="summary-item cgpa-highlight">
                        <div class="summary-label">CGPA</div>
                        <div class="summary-value">{{ $cgpa }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Graduation Status</div>
                        <div class="summary-value">Graduated</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Honors</div>
                        <div class="summary-value">Cum Laude</div>
                    </div>
                </div>
                
                <div class="honors-section">
                    <div class="honors-title">
                        <i class="fas fa-medal"></i> Academic Honors
                    </div>
                    <div class="honors-text">
                        This student has graduated with <strong>Cum Laude</strong> honors, 
                        achieving a cumulative grade point average of {{ $exStudent->formatted_cgpa }} 
                        on a 4.0 scale.
                    </div>
                </div>
            </div>
            
            <div class="page-footer">
                <div class="page-info">
                    <strong>Page 2 of 2</strong><br>
                    <small>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</small>
                </div>
                <div class="navigation-buttons">
                    <a href="{{ route('ex-student.transcript1', ['student_id' => $exStudent->student_id]) }}" class="btn btn-secondary btn-nav">
                        <i class="fas fa-arrow-left"></i> Previous Page
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
