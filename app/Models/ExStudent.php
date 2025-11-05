<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ExStudent extends Model
{
    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'program',
        'program_short',
        'program_full',
        'graduation_year',
        'graduation_month',
        'graduation_day',
        'cgpa',
        'certificate_number',
        'qr_code',
        'academic_records',
        'certificate_data',
        'is_verified',
        'last_accessed',
    ];

    protected $casts = [
        'academic_records' => 'array',
        'certificate_data' => 'array',
        'is_verified' => 'boolean',
        'last_accessed' => 'datetime',
        'cgpa' => 'decimal:2',
    ];

    /**
     * Generate QR code data for the student
     */
    public function generateQrCode(): string
    {
        $qrData = [
            'student_id' => $this->student_id,
            'certificate_number' => $this->certificate_number,
            'verification_url' => route('ex-student.verify', ['qr' => $this->qr_code]),
            'timestamp' => now()->timestamp,
        ];

        return base64_encode(json_encode($qrData));
    }

    /**
     * Get QR code URL for display
     */
    public function getQrCodeUrl(): string
    {
        return route('ex-student.qr', ['student_id' => $this->student_id]);
    }

    /**
     * Get verification URL (direct to certificate)
     */
    public function getVerificationUrl(): string
    {
        return url('/certificates/verify/' . $this->certificate_number);
    }

    /**
     * Mark as verified and update last accessed
     */
    public function markAsVerified(): void
    {
        $this->update([
            'is_verified' => true,
            'last_accessed' => now(),
        ]);
    }

    /**
     * Update last accessed time
     */
    public function updateLastAccessed(): void
    {
        $this->update([
            'last_accessed' => now(),
        ]);
    }

    /**
     * Get formatted graduation date (old format: "June 2011")
     */
    public function getGraduationDateAttribute(): string
    {
        if ($this->graduation_month) {
            return Carbon::createFromFormat('Y-m', $this->graduation_year . '-' . $this->graduation_month)
                ->format('F Y');
        }
        return $this->graduation_year;
    }

    /**
     * Get formatted graduation date in ordinal format (e.g., "Tenth day of June 2011")
     */
    public function getFormattedGraduationDateAttribute(): string
    {
        if (!$this->graduation_year || !$this->graduation_month) {
            return $this->graduation_year ?? 'Unknown';
        }

        $day = (int)($this->graduation_day ?? 1);
        // Handle month as string (e.g., "01", "06") or integer
        $month = (int)$this->graduation_month;
        $year = $this->graduation_year;

        // Validate day range
        if ($day < 1 || $day > 31) {
            $day = 1;
        }

        // Validate month range
        if ($month < 1 || $month > 12) {
            // Try to parse month name if it's not a number
            $monthNames = [
                'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4,
                'may' => 5, 'june' => 6, 'july' => 7, 'august' => 8,
                'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12
            ];
            $monthLower = strtolower(trim($this->graduation_month));
            if (isset($monthNames[$monthLower])) {
                $month = $monthNames[$monthLower];
            } else {
                // Invalid month, return fallback
                return $this->graduation_year ?? 'Unknown';
            }
        }

        // Convert day number to ordinal word
        $ordinalDays = [
            1 => 'First', 2 => 'Second', 3 => 'Third', 4 => 'Fourth', 5 => 'Fifth',
            6 => 'Sixth', 7 => 'Seventh', 8 => 'Eighth', 9 => 'Ninth', 10 => 'Tenth',
            11 => 'Eleventh', 12 => 'Twelfth', 13 => 'Thirteenth', 14 => 'Fourteenth', 15 => 'Fifteenth',
            16 => 'Sixteenth', 17 => 'Seventeenth', 18 => 'Eighteenth', 19 => 'Nineteenth', 20 => 'Twentieth',
            21 => 'Twenty-first', 22 => 'Twenty-second', 23 => 'Twenty-third', 24 => 'Twenty-fourth', 25 => 'Twenty-fifth',
            26 => 'Twenty-sixth', 27 => 'Twenty-seventh', 28 => 'Twenty-eighth', 29 => 'Twenty-ninth', 30 => 'Thirtieth',
            31 => 'Thirty-first'
        ];

        $ordinalDay = $ordinalDays[$day] ?? $day;

        // Convert month number to month name
        $monthNames = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $monthName = $monthNames[$month] ?? 'Unknown';

        return "{$ordinalDay} day of {$monthName} {$year}";
    }

    /**
     * Get short program name (fallback to program if not set)
     */
    public function getShortProgramNameAttribute(): string
    {
        return $this->program_short ?? $this->program ?? 'Not Specified';
    }

    /**
     * Get full program name (fallback to program_short or program if not set)
     */
    public function getFullProgramNameAttribute(): string
    {
        return $this->program_full ?? $this->program_short ?? $this->program ?? 'Not Specified';
    }

    /**
     * Get formatted CGPA
     */
    public function getFormattedCgpaAttribute(): string
    {
        return $this->cgpa ? number_format((float)$this->cgpa, 2) : 'N/A';
    }

    /**
     * Get academic records for transcript
     */
    public function getTranscriptData(): array
    {
        return $this->academic_records ?? [];
    }

    /**
     * Get certificate data
     */
    public function getCertificateData(): array
    {
        return $this->certificate_data ?? [];
    }

    /**
     * Generate certificate number
     */
    public static function generateCertificateNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $sequence = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return "CERT-{$year}{$month}-{$sequence}";
    }

    /**
     * Generate QR code string
     */
    public static function generateQrCodeString(string $studentId): string
    {
        return "STUDENT_VERIFY_{$studentId}_" . time();
    }

    /**
     * Create ex-student record
     */
    public static function createExStudent(array $data): self
    {
        // Generate certificate number
        $certificateNumber = 'CERT-' . now()->format('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
        
        // Generate unique QR code data for each student
        $qrCodeData = 'STUDENT_' . $data['student_id'] . '_' . time() . '_' . rand(1000, 9999);

        // Set default academic records if not provided
        if (!isset($data['academic_records'])) {
            $data['academic_records'] = [
                'year_1' => [
                    'semester_1' => [
                        ['code' => 'CS101', 'name' => 'Programming Fundamentals', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ['code' => 'CS102', 'name' => 'Data Structures', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                        ['code' => 'MATH101', 'name' => 'Calculus I', 'credits' => 4, 'grade' => 'B+', 'points' => 3.33],
                    ],
                    'semester_2' => [
                        ['code' => 'CS201', 'name' => 'Object-Oriented Programming', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ['code' => 'CS202', 'name' => 'Database Systems', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                        ['code' => 'MATH102', 'name' => 'Calculus II', 'credits' => 4, 'grade' => 'B', 'points' => 3.00],
                    ]
                ],
                'year_2' => [
                    'semester_1' => [
                        ['code' => 'CS301', 'name' => 'Software Engineering', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ['code' => 'CS302', 'name' => 'Computer Networks', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                        ['code' => 'CS303', 'name' => 'Operating Systems', 'credits' => 3, 'grade' => 'B+', 'points' => 3.33],
                    ],
                    'semester_2' => [
                        ['code' => 'CS401', 'name' => 'Web Development', 'credits' => 3, 'grade' => 'A', 'points' => 4.00],
                        ['code' => 'CS402', 'name' => 'Mobile Development', 'credits' => 3, 'grade' => 'A-', 'points' => 3.67],
                        ['code' => 'CS403', 'name' => 'Project Management', 'credits' => 3, 'grade' => 'B+', 'points' => 3.33],
                    ]
                ]
            ];
        }

        // Set default certificate data if not provided
        if (!isset($data['certificate_data'])) {
            $data['certificate_data'] = [
                'degree' => $data['program'] ?? 'Bachelor of Computer Science',
                'honors' => $data['cgpa'] >= 3.7 ? 'Cum Laude' : ($data['cgpa'] >= 3.5 ? 'Magna Cum Laude' : ($data['cgpa'] >= 3.0 ? 'Summa Cum Laude' : null)),
                'issue_date' => now()->format('Y-m-d'),
                'university' => 'University of Technology Malaysia',
                'conferral_date' => now()->format('F j, Y')
            ];
        }

        return static::create([
            'student_id' => $data['student_id'],
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'program' => $data['program'] ?? $data['program_full'] ?? null,
            'program_short' => $data['program_short'] ?? null,
            'program_full' => $data['program_full'] ?? null,
            'graduation_year' => $data['graduation_year'],
            'graduation_month' => $data['graduation_month'] ?? null,
            'graduation_day' => $data['graduation_day'] ?? 1,
            'cgpa' => $data['cgpa'] ?? null,
            'certificate_number' => $certificateNumber,
            'qr_code' => $qrCodeData,
            'academic_records' => $data['academic_records'],
            'certificate_data' => $data['certificate_data'],
            'is_verified' => false,
        ]);
    }

    /**
     * Find by QR code
     */
    public static function findByQrCode(string $qrCode): ?self
    {
        return self::where('qr_code', $qrCode)->first();
    }

    /**
     * Find by student ID
     */
    public static function findByStudentId(string $studentId): ?self
    {
        return self::where('student_id', $studentId)->first();
    }

    /**
     * Scope for verified students
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for recent graduates
     */
    public function scopeRecentGraduates($query, int $years = 5)
    {
        $cutoffYear = date('Y') - $years;
        return $query->where('graduation_year', '>=', $cutoffYear);
    }
}