<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $billplzService;

    public function __construct(BillplzService $billplzService)
    {
        $this->billplzService = $billplzService;
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
            $course = class_exists('App\Models\Course') ? \App\Models\Course::findOrFail($request->course_id) : (object)['id' => $request->course_id, 'name' => 'Course'];
            
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
            $signature = $request->header('X-Billplz-Signature');
            $payload = $request->getContent();
            
            // Verify webhook signature
            if (!$this->billplzService->verifyWebhook($signature, $payload)) {
                Log::warning('Invalid Billplz webhook signature');
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            $data = $request->all();
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
     */
    public function billplzRedirect(Request $request)
    {
        $billId = $request->get('billplz', [])['id'] ?? null;
        
        if (!$billId) {
            return redirect()->route('student.payment.failed')
                ->with('error', 'Invalid payment response');
        }

        $payment = Payment::where('billplz_id', $billId)->first();
        
        if (!$payment) {
            return redirect()->route('student.payment.failed')
                ->with('error', 'Payment not found');
        }

        // Check payment status
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
}