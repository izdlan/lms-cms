<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        // Invoice system fields
        'payment_number',
        'invoice_id',
        'student_bill_id',
        'student_id',
        
        // Billplz system fields
        'billplz_id',
        'billplz_collection_id',
        'user_id',
        'type',
        'reference_id',
        'reference_type',
        
        // Common fields
        'amount',
        'currency',
        'status',
        'payment_method',
        'transaction_id',
        'description',
        'payment_notes',
        'payment_details',
        'billplz_response',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'payment_details' => 'array',
        'billplz_response' => 'array',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Type constants
    const TYPE_COURSE_FEE = 'course_fee';
    const TYPE_GENERAL_FEE = 'general_fee';
    const TYPE_ASSIGNMENT_FEE = 'assignment_fee';
    const TYPE_INVOICE_PAYMENT = 'invoice_payment';

    /**
     * Get the invoice that owns the payment (for invoice system)
     */
    public function studentBill(): BelongsTo
    {
        return $this->belongsTo(\App\Models\StudentBill::class, 'student_bill_id');
    }

    // Backward-compatible accessor
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\App\Models\StudentBill::class, 'student_bill_id');
    }

    /**
     * Get the student that made the payment (for invoice system)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the user that owns the payment (for Billplz system)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the receipt for the payment
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * Get the related course if payment is for course
     */
    public function course()
    {
        if ($this->reference_type === 'course' && class_exists('App\Models\Course')) {
            return $this->belongsTo('App\Models\Course', 'reference_id');
        }
        return null;
    }

    /**
     * Generate unique payment number (for invoice system)
     */
    public static function generatePaymentNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastPayment = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastPayment ? (int)substr($lastPayment->payment_number, -4) + 1 : 1;
        
        return sprintf('PAY%s%s%04d', $year, $month, $sequence);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if payment is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID || $this->status === self::STATUS_COMPLETED;
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    /**
     * Check if payment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Mark payment as completed (for invoice system)
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'paid_at' => now()
        ]);
    }

    /**
     * Mark payment as paid (for Billplz system)
     */
    public function markAsPaid(string $paymentMethod = null, string $transactionId = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }

    /**
     * Mark payment as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'RM ' . number_format((float)$this->amount, 2);
    }

    /**
     * Get payment URL from Billplz response
     */
    public function getPaymentUrlAttribute(): ?string
    {
        return $this->billplz_response['url'] ?? null;
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->whereIn('status', [self::STATUS_PAID, self::STATUS_COMPLETED]);
    }

    /**
     * Scope for failed payments
     */
    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    /**
     * Scope for course payments
     */
    public function scopeCoursePayments($query)
    {
        return $query->where('type', self::TYPE_COURSE_FEE);
    }

    /**
     * Scope for general payments
     */
    public function scopeGeneralPayments($query)
    {
        return $query->where('type', self::TYPE_GENERAL_FEE);
    }

    /**
     * Scope for invoice payments
     */
    public function scopeInvoicePayments($query)
    {
        return $query->whereNotNull('student_bill_id');
    }
}