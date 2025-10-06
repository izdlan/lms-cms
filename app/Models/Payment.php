<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Payment extends Model
{
    protected $fillable = [
        'billplz_id',
        'billplz_collection_id',
        'user_id',
        'type',
        'reference_id',
        'reference_type',
        'amount',
        'currency',
        'status',
        'payment_method',
        'transaction_id',
        'description',
        'billplz_response',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'billplz_response' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    // Type constants
    const TYPE_COURSE_FEE = 'course_fee';
    const TYPE_GENERAL_FEE = 'general_fee';
    const TYPE_ASSIGNMENT_FEE = 'assignment_fee';

    /**
     * Get the user that owns the payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        return $this->status === self::STATUS_PAID;
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
     * Mark payment as paid
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
        return $query->where('status', self::STATUS_PAID);
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
}
