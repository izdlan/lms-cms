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
        'graduation_year',
        'graduation_month',
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
     * Get formatted graduation date
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
            'program' => $data['program'] ?? null,
            'graduation_year' => $data['graduation_year'],
            'graduation_month' => $data['graduation_month'] ?? null,
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