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

    protected $xSignatureKey;

    public function __construct()
    {
        $this->apiKey = config('billplz.api_key');
        $this->collectionId = config('billplz.collection_id');
        $this->sandboxMode = config('billplz.sandbox', true);
        $this->xSignatureKey = config('billplz.x_signature_key', config('billplz.webhook_key'));
        // Use API v4 as per latest documentation
        $this->baseUrl = $this->sandboxMode ? 'https://www.billplz-sandbox.com/api/v4' : 'https://www.billplz.com/api/v4';
    }

    /**
     * Create a new bill
     */
    public function createBill($data)
    {
        try {
            $postData = [
                'collection_id' => $this->collectionId,
                'email' => $data['email'],
                'mobile' => $data['mobile'] ?? null,
                'name' => $data['name'],
                'amount' => $data['amount'] * 100, // Convert to cents
                'description' => $data['description'],
                'callback_url' => $data['callback_url'] ?? route('billplz.callback'),
                'redirect_url' => $data['redirect_url'] ?? route('billplz.redirect'),
            ];

            // Set reference fields
            if (isset($data['reference_1_label']) && isset($data['reference_1'])) {
                $postData['reference_1_label'] = $data['reference_1_label'];
                $postData['reference_1'] = $data['reference_1'];
            }

            if (isset($data['reference_2_label']) && isset($data['reference_2'])) {
                $postData['reference_2_label'] = $data['reference_2_label'];
                $postData['reference_2'] = $data['reference_2'];
            }

            // Support for direct payment gateway (bank code)
            if (isset($data['bank_code'])) {
                $postData['reference_1_label'] = 'Bank Code';
                $postData['reference_1'] = $data['bank_code'];
            }

            // Note: Bills endpoint is still v3, other endpoints use v4
            $billsBaseUrl = $this->sandboxMode ? 'https://www.billplz-sandbox.com/api/v3' : 'https://www.billplz.com/api/v3';
            
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($billsBaseUrl . '/bills', $postData);

            if ($response->successful()) {
                $billData = $response->json();
                
                // Add auto_submit URL for direct payment gateway if bank_code was provided
                if (isset($data['bank_code']) && isset($billData['url'])) {
                    $billData['direct_url'] = $this->getDirectPaymentUrl($billData['url'], $data['bank_code']);
                }
                
                Log::info('Billplz bill created successfully', $billData);
                return [
                    'success' => true,
                    'data' => $billData
                ];
            } else {
                $errorResponse = $response->json();
                $errorMessage = 'Failed to create bill';
                
                if (isset($errorResponse['error'])) {
                    if (is_array($errorResponse['error'])) {
                        $errorMessage = $errorResponse['error']['message'] ?? json_encode($errorResponse['error']);
                    } else {
                        $errorMessage = $errorResponse['error'];
                    }
                }
                
                Log::error('Billplz bill creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'error' => $errorMessage
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'http_status' => $response->status(),
                    'response' => $errorResponse
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
            // Bills endpoint uses v3
            $billsBaseUrl = $this->sandboxMode ? 'https://www.billplz-sandbox.com/api/v3' : 'https://www.billplz.com/api/v3';
            
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($billsBaseUrl . '/bills/' . $billId);

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
     * Verify webhook signature (X-Signature verification)
     * Billplz X-Signature Calculation:
     * 1. Extract all key-value pair parameters except x_signature
     * 2. Construct source string from each key-value pair
     * 3. Sort in ascending order, case-insensitive
     * 4. Combine with "|" (pipe) character
     * 5. Compute HMAC-SHA256 with X-Signature Key
     */
    public function verifyWebhook($signature, $data)
    {
        // If signature is not provided, log warning but don't fail (for development)
        if (empty($signature)) {
            Log::warning('Billplz webhook signature missing');
            return config('billplz.sandbox', true); // Allow in sandbox mode
        }

        if (empty($this->xSignatureKey)) {
            Log::error('X-Signature key not configured');
            return false;
        }

        // Remove x_signature from data if present
        $dataWithoutSignature = $data;
        unset($dataWithoutSignature['x_signature']);
        
        // Step 1 & 2: Construct source string from each key-value pair
        $sourceArray = [];
        foreach ($dataWithoutSignature as $key => $value) {
            // Handle null/empty values
            $value = $value ?? '';
            // Convert boolean to string
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $sourceArray[] = $key . $value;
        }
        
        // Step 3: Sort in ascending order, case-insensitive
        usort($sourceArray, function($a, $b) {
            return strcasecmp($a, $b);
        });
        
        // Step 4: Combine with "|" character
        $signatureString = implode('|', $sourceArray);
        
        // Step 5: Compute HMAC-SHA256
        $expectedSignature = hash_hmac('sha256', $signatureString, $this->xSignatureKey);
        
        Log::info('Webhook signature verification', [
            'provided' => $signature,
            'expected' => $expectedSignature,
            'match' => hash_equals($expectedSignature, $signature),
            'data' => $dataWithoutSignature
        ]);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify redirect X-Signature
     * For redirect URLs, parameters come as billplz[id], billplz[paid], etc.
     */
    public function verifyRedirectSignature($signature, $billplzParams)
    {
        if (empty($signature)) {
            return config('billplz.sandbox', true);
        }

        if (empty($this->xSignatureKey)) {
            return false;
        }

        // Build source array from billplz parameters
        $sourceArray = [];
        foreach ($billplzParams as $key => $value) {
            if ($key !== 'x_signature') {
                // Convert 'billplz[id]' format to source string
                $paramKey = 'billplz' . str_replace(['billplz[', ']'], '', $key);
                $sourceArray[] = $paramKey . ($value ?? '');
            }
        }

        // Sort in ascending order
        usort($sourceArray, function($a, $b) {
            return strcasecmp($a, $b);
        });

        // Combine with "|"
        $signatureString = implode('|', $sourceArray);

        // Compute HMAC-SHA256
        $expectedSignature = hash_hmac('sha256', $signatureString, $this->xSignatureKey);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Get X-Signature key
     */
    public function getXSignatureKey()
    {
        return $this->xSignatureKey;
    }

    /**
     * Generate bill URL with auto-submit parameter for direct payment gateway
     * This bypasses the Billplz payment selection page
     */
    public function getDirectPaymentUrl($billUrl, $bankCode = null)
    {
        $url = $billUrl;
        
        if ($bankCode) {
            // If bank code is provided, URL already has the reference_1 set
            $url .= '?auto_submit=true';
        }
        
        return $url;
    }

    /**
     * Get available payment gateways
     */
    public function getPaymentGateways()
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/payment_gateways');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get payment gateways'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz get payment gateways error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create collection (API v4 with split payments support)
     * @param string $title Collection title
     * @param array $splitPayments Array of split payment rules (max 2)
     * @param bool $splitHeader Show split header on templates
     * @return array
     */
    public function createCollection($title, $splitPayments = [], $splitHeader = false)
    {
        try {
            $postData = ['title' => $title];
            
            // Add split payments if provided (V4 supports up to 2 split rules)
            if (!empty($splitPayments)) {
                foreach ($splitPayments as $index => $split) {
                    $postData["split_payments[{$index}][email]"] = $split['email'];
                    if (isset($split['fixed_cut'])) {
                        $postData["split_payments[{$index}][fixed_cut]"] = $split['fixed_cut'];
                    }
                    if (isset($split['variable_cut'])) {
                        $postData["split_payments[{$index}][variable_cut]"] = $split['variable_cut'];
                    }
                    if (isset($split['stack_order'])) {
                        $postData["split_payments[{$index}][stack_order]"] = $split['stack_order'];
                    } else {
                        $postData["split_payments[{$index}][stack_order]"] = $index;
                    }
                }
                $postData['split_header'] = $splitHeader;
            }

            $response = Http::withBasicAuth($this->apiKey, '')
                ->asForm()
                ->post($this->baseUrl . '/collections', $postData);

            if ($response->successful()) {
                $collectionData = $response->json();
                Log::info('Billplz collection created successfully', $collectionData);
                return [
                    'success' => true,
                    'data' => $collectionData
                ];
            } else {
                Log::error('Billplz collection creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
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
     * Get collection index (list all collections)
     * @param int $page Page number
     * @param string|null $status Filter by status (active/inactive)
     * @return array
     */
    public function getCollectionIndex($page = 1, $status = null)
    {
        try {
            $url = $this->baseUrl . '/collections?page=' . $page;
            if ($status) {
                $url .= '&status=' . $status;
            }

            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get collections'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz get collection index error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Create open collection (payment form) - API v4
     * @param string $title Collection title
     * @param string $description Description
     * @param int $amount Amount in cents (required if fixed_amount is true)
     * @param array $options Optional parameters
     * @return array
     */
    public function createOpenCollection($title, $description, $amount, $options = [])
    {
        try {
            $postData = [
                'title' => $title,
                'description' => $description,
                'amount' => $amount,
            ];

            // Add optional fields
            if (isset($options['fixed_amount'])) {
                $postData['fixed_amount'] = $options['fixed_amount'] ? 'true' : 'false';
            }
            if (isset($options['fixed_quantity'])) {
                $postData['fixed_quantity'] = $options['fixed_quantity'] ? 'true' : 'false';
            }
            if (isset($options['payment_button'])) {
                $postData['payment_button'] = $options['payment_button'];
            }
            if (isset($options['reference_1_label'])) {
                $postData['reference_1_label'] = $options['reference_1_label'];
            }
            if (isset($options['reference_2_label'])) {
                $postData['reference_2_label'] = $options['reference_2_label'];
            }
            if (isset($options['email_link'])) {
                $postData['email_link'] = $options['email_link'];
            }
            if (isset($options['tax'])) {
                $postData['tax'] = $options['tax'];
            }
            if (isset($options['redirect_uri'])) {
                $postData['redirect_uri'] = $options['redirect_uri'];
            }

            // Add split payments if provided
            if (isset($options['split_payments']) && !empty($options['split_payments'])) {
                $postData['split_header'] = $options['split_header'] ?? false;
                foreach ($options['split_payments'] as $index => $split) {
                    $postData["split_payments[{$index}][email]"] = $split['email'];
                    if (isset($split['fixed_cut'])) {
                        $postData["split_payments[{$index}][fixed_cut]"] = $split['fixed_cut'];
                    }
                    if (isset($split['variable_cut'])) {
                        $postData["split_payments[{$index}][variable_cut]"] = $split['variable_cut'];
                    }
                    if (isset($split['stack_order'])) {
                        $postData["split_payments[{$index}][stack_order]"] = $split['stack_order'];
                    } else {
                        $postData["split_payments[{$index}][stack_order]"] = $index;
                    }
                }
            }

            $response = Http::withBasicAuth($this->apiKey, '')
                ->asForm()
                ->post($this->baseUrl . '/open_collections', $postData);

            if ($response->successful()) {
                $collectionData = $response->json();
                Log::info('Billplz open collection created successfully', $collectionData);
                return [
                    'success' => true,
                    'data' => $collectionData
                ];
            } else {
                Log::error('Billplz open collection creation failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to create open collection'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz open collection creation error', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Control customer receipt delivery for collection
     * @param string $collectionId Collection ID
     * @param string $action One of: 'activate', 'deactivate', 'global'
     * @return array
     */
    public function controlCustomerReceiptDelivery($collectionId, $action = 'global')
    {
        try {
            $url = $this->baseUrl . "/collections/{$collectionId}/customer_receipt_delivery/{$action}";
            
            $response = Http::withBasicAuth($this->apiKey, '')
                ->post($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to control receipt delivery'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz customer receipt delivery error', [
                'error' => $e->getMessage(),
                'collection_id' => $collectionId,
                'action' => $action
            ]);
            return [
                'success' => false,
                'error' => 'Service error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get customer receipt delivery status
     * @param string $collectionId Collection ID
     * @return array
     */
    public function getCustomerReceiptDeliveryStatus($collectionId)
    {
        try {
            $url = $this->baseUrl . "/collections/{$collectionId}/customer_receipt_delivery";
            
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($url);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'error' => $response->json()['error'] ?? 'Failed to get receipt delivery status'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz get customer receipt delivery error', [
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
     * Get webhook rank
     * @return array
     */
    public function getWebhookRank()
    {
        try {
            $response = Http::withBasicAuth($this->apiKey, '')
                ->get($this->baseUrl . '/webhook_rank');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                $errorResponse = $response->json();
                $errorMessage = 'Failed to get webhook rank';
                
                if (isset($errorResponse['error'])) {
                    if (is_array($errorResponse['error'])) {
                        $errorMessage = $errorResponse['error']['message'] ?? json_encode($errorResponse['error']);
                    } else {
                        $errorMessage = $errorResponse['error'];
                    }
                }
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                    'http_status' => $response->status(),
                    'response' => $errorResponse
                ];
            }
        } catch (\Exception $e) {
            Log::error('Billplz webhook rank error', [
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

    /**
     * Create payment link for student bills
     */
    public function createBillPayment($student, $bill, $amount = null)
    {
        $amount = $amount ?? $bill->amount;
        
        return $this->createBill([
            'email' => $student->email,
            'mobile' => $student->phone ?? $student->mobile,
            'name' => $student->name,
            'amount' => $amount,
            'description' => "Payment for {$bill->bill_type} - {$bill->bill_number}",
            'reference_1' => $student->student_id ?? $student->id,
            'reference_2' => $bill->bill_number,
            'reference_1_label' => 'Student ID',
            'reference_2_label' => 'Bill Number',
            'callback_url' => route('billplz.callback'),
            'redirect_url' => route('student.payment.success'),
        ]);
    }
}
