<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamResult extends Model
{
    protected $fillable = [
        'user_id',
        'subject_code',
        'academic_year',
        'semester',
        'class_code',
        'lecturer_id',
        'student_name',
        'student_ic',
        'student_id',
        'assessments',
        'total_marks',
        'percentage',
        'grade',
        'gpa',
        'status',
        'remarks',
        'published_at',
        'finalized_at'
    ];

    protected $casts = [
        'assessments' => 'array',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'gpa' => 'decimal:2',
        'published_at' => 'datetime',
        'finalized_at' => 'datetime'
    ];

    // Relationship with user (student)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with subject
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    // Relationship with lecturer
    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    // Relationship with student enrollment
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class, 'user_id', 'user_id')
            ->where('subject_code', $this->subject_code);
    }

    // Scope for published results
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope for finalized results
    public function scopeFinalized($query)
    {
        return $query->where('status', 'finalized');
    }

    // Scope for current academic year
    public function scopeCurrentYear($query, $academicYear = null)
    {
        $year = $academicYear ?? date('Y');
        return $query->where('academic_year', $year);
    }

    // Scope for current semester
    public function scopeCurrentSemester($query, $semester = null)
    {
        $currentSemester = $semester ?? $this->getCurrentSemester();
        return $query->where('semester', $currentSemester);
    }

    // Get current semester based on month
    private function getCurrentSemester()
    {
        $month = date('n');
        if ($month >= 1 && $month <= 4) {
            return 'Semester 1';
        } elseif ($month >= 5 && $month <= 8) {
            return 'Semester 2';
        } else {
            return 'Semester 3';
        }
    }

    // Calculate GPA based on grade
    public function calculateGpa()
    {
        $gradeGpaMap = [
            'A+' => 4.00,
            'A' => 4.00,
            'A-' => 3.67,
            'B+' => 3.33,
            'B' => 3.00,
            'B-' => 2.67,
            'C+' => 2.33,
            'C' => 2.00,
            'C-' => 1.67,
            'D+' => 1.33,
            'D' => 1.00,
            'F' => 0.00
        ];

        return $gradeGpaMap[$this->grade] ?? 0.00;
    }

    // Calculate grade based on percentage
    public function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 85) return 'A';
        if ($percentage >= 80) return 'A-';
        if ($percentage >= 75) return 'B+';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 65) return 'B-';
        if ($percentage >= 60) return 'C+';
        if ($percentage >= 55) return 'C';
        if ($percentage >= 50) return 'C-';
        if ($percentage >= 45) return 'D+';
        if ($percentage >= 40) return 'D';
        return 'F';
    }

    // Get assessment total
    public function getAssessmentTotal()
    {
        if (!$this->assessments) return 0;
        
        $total = 0;
        foreach ($this->assessments as $assessment) {
            if (isset($assessment['score']) && is_numeric($assessment['score'])) {
                $total += $assessment['score'];
            }
        }
        return $total;
    }

    // Get assessment percentage
    public function getAssessmentPercentage()
    {
        if (!$this->assessments) return 0;
        
        $totalScore = $this->getAssessmentTotal();
        $totalPossible = 0;
        
        foreach ($this->assessments as $assessment) {
            if (isset($assessment['max_score']) && is_numeric($assessment['max_score'])) {
                $totalPossible += $assessment['max_score'];
            }
        }
        
        return $totalPossible > 0 ? ($totalScore / $totalPossible) * 100 : 0;
    }

    // Auto-calculate fields when saving
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($examResult) {
            // Calculate total marks
            $examResult->total_marks = $examResult->getAssessmentTotal();
            
            // Calculate percentage
            $examResult->percentage = $examResult->getAssessmentPercentage();
            
            // Calculate grade
            $examResult->grade = $examResult->calculateGrade($examResult->percentage);
            
            // Calculate GPA
            $examResult->gpa = $examResult->calculateGpa();
        });
    }
}