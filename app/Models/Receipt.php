<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'payment_id',
        'invoice_id',
        'student_id',
        'amount',
        'payment_method',
        'payment_date',
        'receipt_notes',
        'pdf_path'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    /**
     * Get the payment that owns the receipt
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get the invoice that owns the receipt
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the student that owns the receipt
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Generate unique receipt number
     */
    public static function generateReceiptNumber(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastReceipt = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastReceipt ? (int)substr($lastReceipt->receipt_number, -4) + 1 : 1;
        
        return sprintf('RCP%s%s%04d', $year, $month, $sequence);
    }
}