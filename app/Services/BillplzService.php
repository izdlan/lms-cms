<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class BillplzService
{
    protected $apiKey;
    protected $collectionId;
    protected $sandboxMode;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('billplz.api_key');
        $this->collectionId = config('billplz.collection_id');
        $this->sandboxMode = config('billplz.sandbox', true);
        $this->baseUrl = $this->sandboxMode ? 'https://www.billplz-sandbox.com/api/v3' : 'https://www.billplz.com/api/v3';
    }

    /**
     * Create a new bill
     */
    public function createBill($data)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($this->baseUrl . '/bills', [
                    'collection_id' => $this->collectionId,
                    'email' => $data['email'],
                    'mobile' => $data['mobile'] ?? null,
                    'name' => $data['name'],
                    'amount' => $data['amount'] * 100, // Convert to cents
                    'description' => $data['description'],
                    'callback_url' => $data['callback_url'] ?? route('billplz.callback'),
                    'redirect_url' => $data['redirect_url'] ?? route('billplz.redirect'),
                    'reference_1_label' => $data['reference_1_label'] ?? 'Student ID',
                    'reference_1' => $data['reference_1'] ?? null,
                    'reference_2_label' => $data['reference_2_label'] ?? 'Course',
                    'reference_2' => $data['reference_2'] ?? null,
                ]);

            if ($response->successful()) {
                $billData = $response->json();
                Log::info('Billplz bill created successfully', $billData);
                return [
                    'success' => true,
                    'data' => $billData
                ];
            } else {
                Log::error('Billplz bill creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to create bill'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz service error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get bill status
     */
    public function getBillStatus($billId)
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/bills/' . $billId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get bill status'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz get bill status error', [
                'error' => $e->getMessage(),
                'bill_id' => $billId
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhook($signature, $payload)
    {
        $expectedSignature = hash_hmac('sha256', $payload, config('billplz.webhook_key'));
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Create collection (one-time setup)
     */
    public function createCollection($name, $description = '')
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($this->baseUrl . '/collections', [
                    'title' => $name,
                    'description' => $description,
                    'logo' => null
                ]);

            if ($response->successful()) {
                $collectionData = $response->json();
                Log::info('Billplz collection created successfully', $collectionData);
                return [
                    'success' => true,
                    'data' => $collectionData
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to create collection'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz collection creation error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get collection details
     */
    public function getCollection($collectionId = null)
    {
        $collectionId = $collectionId ?? $this->collectionId;
        
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/collections/' . $collectionId);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get collection'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz get collection error', [
                'error' => $e->getMessage(),
                'collection_id' => $collectionId
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create payment link for course fees
     */
    public function createCoursePayment($student, $course, $amount, $description = null)
    {
        $description = $description ?? "Payment for {$course->name} - {$student->name}";
        
        return $this->createBill([
            'email' => $student->email,
            'mobile' => $student->phone,
            'name' => $student->name,
            'amount' => $amount,
            'description' => $description,
            'reference_1' => $student->student_id ?? $student->id,
            'reference_2' => $course->name ?? $course->id,
            'callback_url' => route('billplz.callback'),
            'redirect_url' => route('student.payment.success'),
        ]);
    }

    /**
     * Create payment link for general fees
     */
    public function createGeneralPayment($student, $amount, $description, $reference = null)
    {
        return $this->createBill([
            'email' => $student->email,
            'mobile' => $student->phone,
            'name' => $student->name,
            'amount' => $amount,
            'description' => $description,
            'reference_1' => $student->student_id ?? $student->id,
            'reference_2' => $reference,
            'callback_url' => route('billplz.callback'),
            'redirect_url' => route('student.payment.success'),
        ]);
    }
}
