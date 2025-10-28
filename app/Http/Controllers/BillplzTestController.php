<?php

namespace App\Http\Controllers;

use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillplzTestController extends Controller
{
    protected $billplz;

    public function __construct(BillplzService $billplz)
    {
        $this->billplz = $billplz;
    }

    /**
     * Show test page
     */
    public function index()
    {
        return view('billplz-test');
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $result = $this->billplz->getWebhookRank();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connected successfully!',
                    'data' => [
                        'rank' => $result['data']['rank'],
                        'api_version' => 'v4',
                        'sandbox' => config('billplz.sandbox', true)
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection failed',
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get collection info
     */
    public function testCollection()
    {
        try {
            $collectionId = config('billplz.collection_id');
            $result = $this->billplz->getCollection($collectionId);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Collection found',
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Collection not found',
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create test payment
     */
    public function createTestPayment(Request $request)
    {
        try {
            // Validate request
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'email' => 'required|email',
                'name' => 'required|string|max:255',
                'mobile' => 'nullable|string',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();

            // Check if API credentials are configured
            $apiKey = config('billplz.api_key');
            $collectionId = config('billplz.collection_id');

            if (empty($apiKey) || empty($collectionId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Billplz credentials not configured. Please add BILLPLZ_API_KEY and BILLPLZ_COLLECTION_ID to your .env file.'
                ], 400);
            }

            $result = $this->billplz->createBill([
                'email' => $data['email'],
                'name' => $data['name'],
                'mobile' => $data['mobile'] ?? null,
                'amount' => $data['amount'],
                'description' => $data['description'] ?? 'Test Payment from Sandbox',
                'reference_1' => 'TEST-' . now()->format('YmdHis'),
            ]);

            if ($result['success']) {
                Log::info('Billplz test payment created', [
                    'payment_url' => $result['data']['url'],
                    'email' => $data['email']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment created successfully',
                    'payment_url' => $result['data']['url'],
                    'data' => $result['data']
                ]);
            } else {
                Log::error('Billplz payment creation failed', [
                    'error' => $result['error'],
                    'data' => $data
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create payment with Billplz',
                    'error' => $result['error'],
                    'debug' => [
                        'api_key_configured' => !empty($apiKey),
                        'collection_id_configured' => !empty($collectionId),
                        'collection_id' => $collectionId,
                        'sandbox_mode' => config('billplz.sandbox', true)
                    ]
                ], 400);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Billplz test payment error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : 'Hidden in production'
            ], 500);
        }
    }

    /**
     * Get payment gateways list
     */
    public function getPaymentGateways()
    {
        try {
            $result = $this->billplz->getPaymentGateways();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}

