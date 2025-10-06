<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_picture',
        'ic',
        'phone',
        'role',
        'must_reset_password',
        'courses',
        'address',
        'previous_university',
        'col_ref_no',
        'student_id',
        'source_sheet',
        'is_blocked',
        'blocked_at',
        'block_reason',
        // Academic Information
        'category',
        'programme_name',
        'faculty',
        'programme_code',
        'semester_entry',
        'programme_intake',
        'date_of_commencement',
        // Research Information
        'research_title',
        'supervisor',
        'external_examiner',
        'internal_examiner',
        // Student Portal Information
        'student_portal_username',
        'student_portal_password',
        // Additional Dates
        'col_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_reset_password' => 'boolean',
            'courses' => 'array',
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
        ];
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is staff
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff' || $this->role === 'lecturer';
    }

    /**
     * Check if user is lecturer (staff)
     */
    public function isLecturer(): bool
    {
        return $this->role === 'lecturer';
    }

    /**
     * Check if user is finance admin
     */
    public function isFinanceAdmin(): bool
    {
        return $this->role === 'finance_admin';
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked(): bool
    {
        return $this->is_blocked === true;
    }

    /**
     * Relationship with student enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(\App\Models\StudentEnrollment::class);
    }

    /**
     * Get enrolled subjects for a student
     */
    public function enrolledSubjects()
    {
        return $this->enrollments()->with(['subject', 'lecturer', 'classSchedule']);
    }

    /**
     * Relationship with lecturer profile
     */
    public function lecturer()
    {
        return $this->hasOne(\App\Models\Lecturer::class);
    }

    /**
     * Relationship with student profile
     */
    public function student()
    {
        return $this->hasOne(\App\Models\Student::class);
    }

    /**
     * Find a user by their IC number for authentication.
     *
     * @param string $ic
     * @return \App\Models\User|null
     */
    public static function findByIc($ic)
    {
        return static::where('ic', $ic)->first();
    }

    /**
     * Get invoices for the user (if student)
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'student_id');
    }

    /**
     * Get payments made by the user (if student)
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'student_id');
    }

    /**
     * Get receipts for the user (if student)
     */
    public function receipts()
    {
        return $this->hasMany(Receipt::class, 'student_id');
    }
}
