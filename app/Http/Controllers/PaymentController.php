<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\BillplzService;
use App\Services\AccountingIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $billplzService;
    protected $accountingService;

    public function __construct(BillplzService $billplzService, AccountingIntegrationService $accountingService)
    {
        $this->billplzService = $billplzService;
        $this->accountingService = $accountingService;
    }

    /**
     * Show payment page for students
     */
    public function index()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('student.payments', compact('payments'));
    }

    /**
     * Create payment for course fee
     */
    public function createCoursePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
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

        try {
            $user = Auth::user();
            
            // Handle Course model safely
            $course = (object)['id' => $request->course_id, 'name' => 'Course'];
            
            // Try to load actual course if model exists
            if (class_exists('App\Models\Course')) {
                try {
                    $courseModelName = 'App\\Models\\Course';
                    $foundCourse = $courseModelName::find($request->course_id);
                    if ($foundCourse) {
                        $course = $foundCourse;
                    }
                } catch (\Exception $e) {
                    // Use fallback object
                }
            }
            
            // Check if there's already a pending payment for this course
            $existingPayment = Payment::where('user_id', $user->id)
                ->where('type', Payment::TYPE_COURSE_FEE)
                ->where('reference_id', $course->id)
                ->where('status', Payment::STATUS_PENDING)
                ->first();

            if ($existingPayment && !$existingPayment->isExpired()) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $existingPayment->payment_url,
                    'message' => 'Existing payment found'
                ]);
            }

            // Create new payment
            $result = $this->billplzService->createCoursePayment(
                $user,
                $course,
                $request->amount,
                $request->description
            );

            if ($result['success']) {
                $billplzData = $result['data'];
                
                $payment = Payment::create([
                    'billplz_id' => $billplzData['id'],
                    'billplz_collection_id' => $billplzData['collection_id'],
                    'user_id' => $user->id,
                    'type' => Payment::TYPE_COURSE_FEE,
                    'reference_id' => $course->id,
                    'reference_type' => 'course',
                    'amount' => $request->amount,
                    'description' => $request->description ?? "Payment for {$course->name}",
                    'billplz_response' => $billplzData,
                    'expires_at' => now()->addMinutes(config('billplz.timeout', 30)),
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $billplzData['url'],
                    'payment_id' => $payment->id,
                    'message' => 'Payment created successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Course payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'course_id' => $request->course_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Create general payment
     */
    public function createGeneralPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            $result = $this->billplzService->createGeneralPayment(
                $user,
                $request->amount,
                $request->description,
                $request->reference
            );

            if ($result['success']) {
                $billplzData = $result['data'];
                
                $payment = Payment::create([
                    'billplz_id' => $billplzData['id'],
                    'billplz_collection_id' => $billplzData['collection_id'],
                    'user_id' => $user->id,
                    'type' => Payment::TYPE_GENERAL_FEE,
                    'reference_id' => $request->reference,
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'billplz_response' => $billplzData,
                    'expires_at' => now()->addMinutes(config('billplz.timeout', 30)),
                ]);

                return response()->json([
                    'success' => true,
                    'payment_url' => $billplzData['url'],
                    'payment_id' => $payment->id,
                    'message' => 'Payment created successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('General payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus($paymentId)
    {
        try {
            $payment = Payment::where('id', $paymentId)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Get latest status from Billplz
            $result = $this->billplzService->getBillStatus($payment->billplz_id);
            
            if ($result['success']) {
                $billplzData = $result['data'];
                
                // Update payment status if changed
                if ($billplzData['state'] === 'paid' && $payment->isPending()) {
                    $payment->markAsPaid(
                        $billplzData['payment_method'] ?? null,
                        $billplzData['transaction_id'] ?? null
                    );
                } elseif ($billplzData['state'] === 'cancelled' && $payment->isPending()) {
                    $payment->markAsCancelled();
                }
                
                $payment->refresh();
            }

            return response()->json([
                'success' => true,
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'amount' => $payment->formatted_amount,
                    'description' => $payment->description,
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s'),
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'payment_url' => $payment->payment_url,
                    'is_expired' => $payment->isExpired(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get payment status failed', [
                'error' => $e->getMessage(),
                'payment_id' => $paymentId,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment status'
            ], 500);
        }
    }

    /**
     * Billplz callback (webhook)
     */
    public function billplzCallback(Request $request)
    {
        try {
            // Get X-Signature from header (Billplz uses X-Signature header)
            $signature = $request->header('X-Signature', $request->header('X-Billplz-Signature'));
            $data = $request->all();
            
            // Verify webhook signature
            if (!$this->billplzService->verifyWebhook($signature, $data)) {
                Log::warning('Invalid Billplz webhook signature', [
                    'signature' => $signature,
                    'data' => $data
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }
            $billId = $data['id'] ?? null;
            
            if (!$billId) {
                return response()->json(['error' => 'Missing bill ID'], 400);
            }

            $payment = Payment::where('billplz_id', $billId)->first();
            
            if (!$payment) {
                Log::warning('Payment not found for Billplz ID: ' . $billId);
                return response()->json(['error' => 'Payment not found'], 404);
            }

            // Update payment status based on Billplz response
            if ($data['state'] === 'paid') {
                $payment->markAsPaid(
                    $data['payment_method'] ?? null,
                    $data['transaction_id'] ?? null
                );
                
                // Update related bill status if this is a bill payment
                if ($payment->reference_type === 'bill') {
                    $bill = \App\Models\StudentBill::find($payment->reference_id);
                    if ($bill) {
                        $bill->markAsPaid($payment);
                    }
                }
                
                // Send payment data to accounting system
                if (config('accounting.auto_sync', true)) {
                    $this->accountingService->sendPaymentData($payment);
                }
                
                Log::info('Payment marked as paid', [
                    'payment_id' => $payment->id,
                    'billplz_id' => $billId,
                    'amount' => $payment->amount
                ]);
            } elseif ($data['state'] === 'cancelled') {
                $payment->markAsCancelled();
                
                Log::info('Payment marked as cancelled', [
                    'payment_id' => $payment->id,
                    'billplz_id' => $billId
                ]);
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Billplz callback error', [
                'error' => $e->getMessage(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Billplz redirect (after payment)
     * Verifies X-Signature from redirect parameters (billplz[id], billplz[paid], etc.)
     */
    public function billplzRedirect(Request $request)
    {
        $billplzParams = $request->get('billplz', []);
        $billId = $billplzParams['id'] ?? null;
        $xSignature = $billplzParams['x_signature'] ?? null;
        
        if (!$billId) {
            return redirect()->route('student.payment.failed')
                ->with('error', 'Invalid payment response');
        }

        // Verify X-Signature if provided (for redirect URLs with X-Signature enabled)
        if ($xSignature) {
            $isValid = $this->billplzService->verifyRedirectSignature($xSignature, $billplzParams);
            
            if (!$isValid) {
                Log::warning('Invalid Billplz redirect X-Signature', [
                    'bill_id' => $billId,
                    'signature' => $xSignature,
                    'params' => $billplzParams
                ]);
                return redirect()->route('student.payment.failed')
                    ->with('error', 'Invalid payment signature');
            }
        }

        $payment = Payment::where('billplz_id', $billId)->first();
        
        if (!$payment) {
            return redirect()->route('student.payment.failed')
                ->with('error', 'Payment not found');
        }

        // Use redirect data if X-Signature verified, otherwise check with API
        if ($xSignature && isset($billplzParams['paid'])) {
            // X-Signature verified, use redirect data directly
            if ($billplzParams['paid'] === 'true' || $billplzParams['paid'] === true) {
                $payment->markAsPaid(
                    $billplzParams['transaction_status'] ?? null,
                    $billplzParams['transaction_id'] ?? null
                );
                
                return redirect()->route('student.payment.success')
                    ->with('success', 'Payment completed successfully!');
            }
        } else {
            // No X-Signature or not verified, check with API
            $result = $this->billplzService->getBillStatus($billId);
            
            if ($result['success']) {
                $billplzData = $result['data'];
                
                if ($billplzData['state'] === 'paid') {
                    $payment->markAsPaid(
                        $billplzData['payment_method'] ?? null,
                        $billplzData['transaction_id'] ?? null
                    );
                    
                    return redirect()->route('student.payment.success')
                        ->with('success', 'Payment completed successfully!');
                } elseif ($billplzData['state'] === 'cancelled') {
                    $payment->markAsCancelled();
                    
                    return redirect()->route('student.payment.failed')
                        ->with('error', 'Payment was cancelled');
                }
            }
        }

        return redirect()->route('student.payment.pending')
            ->with('info', 'Payment is being processed');
    }

    /**
     * Payment success page
     */
    public function paymentSuccess()
    {
        return view('student.payment-success');
    }

    /**
     * Payment failed page
     */
    public function paymentFailed()
    {
        return view('student.payment-failed');
    }

    /**
     * Payment pending page
     */
    public function paymentPending()
    {
        return view('student.payment-pending');
    }

    /**
     * Get payment data for accounting system (API endpoint)
     */
    public function getAccountingData(Request $request)
    {
        // Validate API key or implement proper authentication
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || $apiKey !== config('accounting.api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $filters = $request->only(['from_date', 'to_date', 'payment_type', 'accounting_synced']);
            $paymentData = $this->accountingService->getPaymentData($filters);

            return response()->json([
                'success' => true,
                'data' => $paymentData,
                'count' => $paymentData->count(),
                'filters' => $filters
            ]);
        } catch (\Exception $e) {
            Log::error('Get accounting data failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment data'
            ], 500);
        }
    }

    /**
     * Test accounting system connection
     */
    public function testAccountingConnection()
    {
        try {
            $result = $this->accountingService->testConnection();
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync payments to accounting system
     */
    public function syncToAccounting(Request $request)
    {
        $request->validate([
            'payment_ids' => 'nullable|array',
            'payment_ids.*' => 'integer|exists:payments,id'
        ]);

        try {
            $paymentIds = $request->get('payment_ids', []);
            
            if (empty($paymentIds)) {
                // Sync all unsynced payments
                $paymentIds = Payment::where('status', Payment::STATUS_PAID)
                    ->where('accounting_synced', false)
                    ->pluck('id')
                    ->toArray();
            }

            $result = $this->accountingService->sendBatchPayments($paymentIds);

            return response()->json([
                'success' => $result,
                'message' => $result ? 'Payments synced successfully' : 'Failed to sync payments',
                'synced_count' => count($paymentIds)
            ]);
        } catch (\Exception $e) {
            Log::error('Sync to accounting failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment statistics for accounting
     */
    public function getPaymentStatistics(Request $request)
    {
        // Validate API key
        $apiKey = $request->header('X-API-Key');
        if (!$apiKey || $apiKey !== config('accounting.api_key')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $fromDate = $request->get('from_date', now()->startOfMonth());
            $toDate = $request->get('to_date', now()->endOfMonth());

            $stats = Payment::where('status', Payment::STATUS_PAID)
                ->whereBetween('paid_at', [$fromDate, $toDate])
                ->selectRaw('
                    COUNT(*) as total_payments,
                    SUM(amount) as total_amount,
                    AVG(amount) as average_amount,
                    COUNT(CASE WHEN accounting_synced = 1 THEN 1 END) as synced_count,
                    COUNT(CASE WHEN accounting_synced = 0 THEN 1 END) as unsynced_count
                ')
                ->first();

            $paymentTypes = Payment::where('status', Payment::STATUS_PAID)
                ->whereBetween('paid_at', [$fromDate, $toDate])
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('type')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'payment_types' => $paymentTypes,
                    'period' => [
                        'from' => $fromDate,
                        'to' => $toDate
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Get payment statistics failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve statistics'
            ], 500);
        }
    }
}