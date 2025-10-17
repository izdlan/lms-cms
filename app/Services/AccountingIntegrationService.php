<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\User;

class AccountingIntegrationService
{
    protected $accountingApiUrl;
    protected $accountingApiKey;
    protected $enabled;

    public function __construct()
    {
        $this->accountingApiUrl = config('accounting.api_url');
        $this->accountingApiKey = config('accounting.api_key');
        $this->enabled = config('accounting.enabled', false);
    }

    /**
     * Send payment data to accounting system
     */
    public function sendPaymentData(Payment $payment)
    {
        if (!$this->enabled || !$this->accountingApiUrl) {
            Log::info('Accounting integration disabled or not configured');
            return false;
        }

        try {
            $paymentData = $this->formatPaymentData($payment);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accountingApiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->accountingApiUrl . '/api/payments', $paymentData);

            if ($response->successful()) {
                Log::info('Payment data sent to accounting system successfully', [
                    'payment_id' => $payment->id,
                    'response' => $response->json()
                ]);
                
                // Mark as synced to accounting
                $payment->update(['accounting_synced' => true]);
                
                return true;
            } else {
                Log::error('Failed to send payment data to accounting system', [
                    'payment_id' => $payment->id,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Accounting integration error', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Format payment data for accounting system
     */
    protected function formatPaymentData(Payment $payment)
    {
        $user = $payment->user ?? $payment->student;
        
        return [
            'lms_payment_id' => $payment->id,
            'billplz_id' => $payment->billplz_id,
            'student_id' => $user->student_id ?? $user->id,
            'student_name' => $user->name,
            'student_email' => $user->email,
            'student_phone' => $user->phone ?? $user->mobile,
            'amount' => $payment->amount,
            'currency' => $payment->currency ?? 'MYR',
            'payment_status' => $payment->status,
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id,
            'description' => $payment->description,
            'payment_type' => $payment->type,
            'reference_id' => $payment->reference_id,
            'reference_type' => $payment->reference_type,
            'paid_at' => $payment->paid_at?->toISOString(),
            'created_at' => $payment->created_at->toISOString(),
            'payment_details' => $payment->payment_details,
            'billplz_response' => $payment->billplz_response,
        ];
    }

    /**
     * Send batch payment data to accounting system
     */
    public function sendBatchPayments($paymentIds = [])
    {
        if (!$this->enabled || !$this->accountingApiUrl) {
            return false;
        }

        $payments = Payment::whereIn('id', $paymentIds)
            ->where('status', Payment::STATUS_PAID)
            ->where('accounting_synced', false)
            ->get();

        if ($payments->isEmpty()) {
            return true;
        }

        $batchData = $payments->map(function ($payment) {
            return $this->formatPaymentData($payment);
        })->toArray();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accountingApiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->accountingApiUrl . '/api/payments/batch', [
                'payments' => $batchData
            ]);

            if ($response->successful()) {
                // Mark all payments as synced
                Payment::whereIn('id', $payments->pluck('id'))
                    ->update(['accounting_synced' => true]);
                
                Log::info('Batch payment data sent to accounting system', [
                    'count' => $payments->count()
                ]);
                
                return true;
            } else {
                Log::error('Failed to send batch payment data', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Batch accounting integration error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Test connection to accounting system
     */
    public function testConnection()
    {
        if (!$this->enabled || !$this->accountingApiUrl) {
            return [
                'success' => false,
                'message' => 'Accounting integration not configured'
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->accountingApiKey,
                'Accept' => 'application/json',
            ])->get($this->accountingApiUrl . '/api/health');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Connection failed: ' . $response->status(),
                    'data' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get payment data for accounting system (for pull method)
     */
    public function getPaymentData($filters = [])
    {
        $query = Payment::with(['user', 'student'])
            ->where('status', Payment::STATUS_PAID);

        // Apply filters
        if (isset($filters['from_date'])) {
            $query->where('paid_at', '>=', $filters['from_date']);
        }
        
        if (isset($filters['to_date'])) {
            $query->where('paid_at', '<=', $filters['to_date']);
        }
        
        if (isset($filters['payment_type'])) {
            $query->where('type', $filters['payment_type']);
        }
        
        if (isset($filters['accounting_synced'])) {
            $query->where('accounting_synced', $filters['accounting_synced']);
        }

        $payments = $query->orderBy('paid_at', 'desc')->get();

        return $payments->map(function ($payment) {
            return $this->formatPaymentData($payment);
        });
    }
}
