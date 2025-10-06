<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'student_id',
        'created_by',
        'bill_type',
        'session',
        'amount',
        'invoice_date',
        'due_date',
        'status',
        'description',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the student that owns the invoice
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the finance admin who created the invoice
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the payments for the invoice
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the receipts for the invoice
     */
    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date < now()->toDateString();
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->amount - $this->total_paid;
    }

    /**
     * Check if invoice is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastInvoice = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (int)substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('%s%s%04d', $year, $month, $sequence);
    }
}