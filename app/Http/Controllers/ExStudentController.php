<?php

namespace App\Http\Controllers;

use App\Models\ExStudent;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExStudentController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Show ex-student login page
     */
    public function login()
    {
        return view('ex-student.login');
    }

    /**
     * Process QR code verification
     */
    public function verifyQr(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code data'
            ], 400);
        }

        try {
            // Validate QR code data
            $qrData = $this->qrCodeService->validateQrCode($request->qr_data);
            
            if (!$qrData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR code format'
                ], 400);
            }

            // Find ex-student by student ID
            $exStudent = ExStudent::findByStudentId($qrData['student_id']);
            
            if (!$exStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student record not found'
                ], 404);
            }

            // Verify QR code matches
            if ($exStudent->qr_code !== $request->qr_data) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR code verification failed'
                ], 400);
            }

            // Mark as verified and update access time
            $exStudent->markAsVerified();

            // Store in session
            session(['ex_student_verified' => true, 'ex_student_id' => $exStudent->id]);

            return response()->json([
                'success' => true,
                'message' => 'Verification successful',
                'redirect_url' => route('ex-student.dashboard')
            ]);

        } catch (\Exception $e) {
            Log::error('QR verification failed', [
                'error' => $e->getMessage(),
                'qr_data' => $request->qr_data
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Verify ex-student by QR code or student ID (direct URL access)
     */
    public function verify(Request $request)
    {
        $qrCode = $request->get('qr');
        $studentId = $request->get('student_id');
        
        if (!$qrCode && !$studentId) {
            return redirect()->route('ex-student.login')
                ->with('error', 'Invalid verification link');
        }

        $exStudent = null;
        
        if ($qrCode) {
            $exStudent = ExStudent::findByQrCode($qrCode);
        } elseif ($studentId) {
            $exStudent = ExStudent::findByStudentId($studentId);
        }
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')
                ->with('error', 'Student record not found');
        }

        // Mark as verified and update access time
        $exStudent->markAsVerified();

        // Redirect directly to certificate with student ID in URL
        return redirect()->route('ex-student.certificate', ['student_id' => $exStudent->student_id])
            ->with('success', 'Verification successful! Welcome back, ' . $exStudent->name);
    }

    /**
     * Show ex-student dashboard
     */
    public function dashboard(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return redirect()->route('ex-student.login')->with('error', 'Student ID required.');
        }
        
        $exStudent = ExStudent::findByStudentId($studentId);
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')->with('error', 'Student record not found.');
        }
        
        return view('ex-student.dashboard', compact('exStudent'));
    }

    /**
     * Show certificate page
     */
    public function certificate(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return redirect()->route('ex-student.login')->with('error', 'Student ID required.');
        }
        
        $exStudent = ExStudent::findByStudentId($studentId);
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')->with('error', 'Student record not found.');
        }

        // Mark as verified when they access their certificate (via QR code scan)
        $exStudent->markAsVerified();

        // Generate QR code that points to this specific student's certificate
        $certificateUrl = route('ex-student.certificate', ['student_id' => $exStudent->student_id]);
        $qrCodePath = $this->qrCodeService->generateCertificateQrCode($exStudent);
        
        return view('ex-student.certificate', compact('exStudent', 'qrCodePath'));
    }

    /**
     * Show transcript page 1
     */
    public function transcript1(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return redirect()->route('ex-student.login')->with('error', 'Student ID required.');
        }
        
        $exStudent = ExStudent::findByStudentId($studentId);
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')->with('error', 'Student record not found.');
        }

        return view('ex-student.transcript1', compact('exStudent'));
    }

    /**
     * Show transcript page 2
     */
    public function transcript2(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return redirect()->route('ex-student.login')->with('error', 'Student ID required.');
        }
        
        $exStudent = ExStudent::findByStudentId($studentId);
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')->with('error', 'Student record not found.');
        }

        return view('ex-student.transcript2', compact('exStudent'));
    }

    /**
     * Generate QR code for student
     */
    public function generateQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|exists:ex_students,student_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid student ID'
            ], 400);
        }

        try {
            $exStudent = ExStudent::findByStudentId($request->student_id);
            
            if (!$exStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Generate QR code
            $qrCodePath = $this->qrCodeService->generateQrCode($exStudent);
            $qrCodeUrl = $this->qrCodeService->getQrCodeUrl($qrCodePath);

            return response()->json([
                'success' => true,
                'qr_code_url' => $qrCodeUrl,
                'verification_url' => $exStudent->getVerificationUrl(),
                'student_id' => $exStudent->student_id
            ]);

        } catch (\Exception $e) {
            Log::error('QR code generation failed', [
                'error' => $e->getMessage(),
                'student_id' => $request->student_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate QR code'
            ], 500);
        }
    }

    /**
     * Logout ex-student
     */
    public function logout()
    {
        session()->forget(['ex_student_verified', 'ex_student_id']);
        
        return redirect()->route('ex-student.login')
            ->with('success', 'You have been logged out successfully');
    }
}