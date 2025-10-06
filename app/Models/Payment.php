<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_number',
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'paid_at',
        'payment_notes',
        'payment_details'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_details' => 'array'
    ];

    /**
     * Get the invoice that owns the payment
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the student that made the payment
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the receipt for the payment
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * Generate unique payment number
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
     * Mark payment as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now()
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed'
        ]);
    }
}