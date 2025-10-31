<?php
/**
 * Model: StudentBill
 * Purpose: Represents a bill/invoice issued to a student, with status lifecycle
 *          (pending, paid, overdue, cancelled) and compatibility helpers for legacy
 *          invoice references.
 * Relations: belongsTo User, belongsTo/hasMany Payment (single and multiple payments).
 * Utilities: Number generation, status helpers, UI accessors, and query scopes.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class StudentBill extends Model
{
    protected $fillable = [
        'bill_number',
        'user_id',
        'session',
        'bill_type',
        'amount',
        'currency',
        'status',
        'bill_date',
        'due_date',
        'description',
        'metadata',
        'payment_id',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bill_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    /* === Compatibility accessors for legacy Invoice usage === */
    public function getInvoiceNumberAttribute(): string
    {
        return $this->bill_number;
    }

    public function getInvoiceDateAttribute()
    {
        return $this->bill_date;
    }

    public function getStudentIdAttribute(): int
    {
        return (int) $this->user_id;
    }

    // Status constants
    const STATUS_UNPAID = 'unpaid';
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_OVERDUE = 'overdue';
    const STATUS_CANCELLED = 'cancelled';

    // Bill type constants
    const TYPE_TUITION_FEE = 'Tuition Fee';
    const TYPE_EET_FEE = 'EET Fee';
    const TYPE_LIBRARY_FEE = 'Library Fee';
    const TYPE_EXAM_FEE = 'Exam Fee';
    const TYPE_REGISTRATION_FEE = 'Registration Fee';
    const TYPE_LATE_FEE = 'Late Fee';

    /**
     * Get the user that owns the bill
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payment associated with this bill
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Payments associated with this bill
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'student_bill_id');
    }

    /**
     * Compatibility: compute total paid amount from related payments
     */
    public function getTotalPaidAttribute(): float
    {
        if (method_exists($this, 'payments')) {
            return (float) ($this->payments()->paid()->sum('amount'));
        }
        return 0.0;
    }

    /**
     * Compatibility: determine if bill is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->total_paid >= (float) $this->amount;
    }

    /**
     * Check if bill is unpaid
     */
    public function isUnpaid(): bool
    {
        return $this->status === self::STATUS_UNPAID;
    }

    /**
     * Check if bill is pending
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if bill is paid
     */
    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    /**
     * Check if bill is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_OVERDUE || 
               ($this->status === self::STATUS_PENDING && $this->due_date->isPast());
    }

    /**
     * Check if bill is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Mark bill as paid
     */
    public function markAsPaid(Payment $payment = null): void
    {
        $this->update([
            'status' => self::STATUS_PAID,
            'payment_id' => $payment?->id,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark bill as overdue
     */
    public function markAsOverdue(): void
    {
        if ($this->isPending()) {
            $this->update([
                'status' => self::STATUS_OVERDUE,
            ]);
        }
    }

    /**
     * Mark bill as cancelled
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
     * Get days until due
     */
    public function getDaysUntilDueAttribute(): int
    {
        return now()->diffInDays($this->due_date, false);
    }

    /**
     * Get status badge class for UI
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PAID => 'bg-success',
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_UNPAID => 'bg-secondary',
            self::STATUS_OVERDUE => 'bg-danger',
            self::STATUS_CANCELLED => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    /**
     * Mark bill as pending (when payment is initiated)
     */
    public function markAsPending(): void
    {
        if ($this->status === self::STATUS_UNPAID) {
            $this->update([
                'status' => self::STATUS_PENDING,
            ]);
        }
    }

    /**
     * Scope for unpaid bills
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /**
     * Scope for pending bills
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for paid bills
     */
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    /**
     * Scope for overdue bills
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', self::STATUS_OVERDUE)
                    ->orWhere(function($q) {
                        $q->where('status', self::STATUS_PENDING)
                          ->where('due_date', '<', now());
                    });
    }

    /**
     * Scope for bills by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('bill_type', $type);
    }

    /**
     * Scope for bills by session
     */
    public function scopeBySession($query, string $session)
    {
        return $query->where('session', $session);
    }

    /**
     * Generate unique bill number
     */
    public static function generateBillNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        
        // Get the last bill number for today
        $lastBill = self::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastBill ? (intval(substr($lastBill->bill_number, -4)) + 1) : 1;
        
        return $year . $month . $day . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new bill for a student
     */
    public static function createBill(array $data): self
    {
        $data['bill_number'] = $data['bill_number'] ?? self::generateBillNumber();
        
        return self::create($data);
    }
}