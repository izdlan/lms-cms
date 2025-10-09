<?php

namespace App\Http\Controllers;

use App\Models\ExStudent;
use App\Services\CertificateService;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    protected $certificateService;
    protected $qrCodeService;

    public function __construct(CertificateService $certificateService, QrCodeService $qrCodeService)
    {
        $this->certificateService = $certificateService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Generate and download Word certificate
     */
    public function generateWordCertificate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:ex_students,student_id',
        ]);

        try {
            $exStudent = ExStudent::findByStudentId($request->student_id);
            
            if (!$exStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Generate Word certificate
            $filename = $this->certificateService->generateWordCertificate($exStudent);
            $filepath = storage_path('app/public/' . $filename);

            if (!file_exists($filepath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate generation failed'
                ], 500);
            }

            // Clean up temp files
            $this->certificateService->cleanupTempFiles();

            return response()->download($filepath, "Certificate_{$exStudent->name}.docx")->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error("Certificate generation error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Certificate generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and download PDF certificate
     */
    public function generatePdfCertificate(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:ex_students,student_id',
        ]);

        try {
            $exStudent = ExStudent::findByStudentId($request->student_id);
            
            if (!$exStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            // Generate PDF certificate
            $filename = $this->certificateService->generatePdfCertificate($exStudent);
            $filepath = storage_path('app/public/' . $filename);

            if (!file_exists($filepath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Certificate generation failed'
                ], 500);
            }

            return response()->download($filepath, "Certificate_{$exStudent->name}.pdf")->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error("PDF Certificate generation error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Certificate generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview certificate (web view)
     */
    public function previewCertificate(Request $request)
    {
        $studentId = $request->get('student_id');
        
        if (!$studentId) {
            return redirect()->route('ex-student.login')->with('error', 'Student ID required.');
        }
        
        $exStudent = ExStudent::findByStudentId($studentId);
        
        if (!$exStudent) {
            return redirect()->route('ex-student.login')->with('error', 'Student record not found.');
        }

        // Generate QR codes for preview
        $qrCodePath1 = $this->qrCodeService->generateCertificateQrCode($exStudent);
        $qrCodePath2 = $this->qrCodeService->generateSimpleQrCode($exStudent->student_id);

        return view('ex-student.certificate-preview', compact('exStudent', 'qrCodePath1', 'qrCodePath2'));
    }

    /**
     * Generate certificate template (for testing)
     */
    public function generateTemplate(Request $request)
    {
        // Create a sample ex-student for template generation
        $sampleStudent = new ExStudent([
            'student_id' => 'SAMPLE001',
            'name' => 'ASMAWI BIN ASA',
            'email' => 'sample@example.com',
            'phone' => '0123456789',
            'program' => 'SARJANA MUDA EKSEKUTIF PENTADBIRAN PERNIAGAAN',
            'graduation_year' => '2025',
            'graduation_month' => '08',
            'cgpa' => 3.50,
            'certificate_number' => 'CERT-20250829-0001',
            'qr_code' => 'SAMPLE_QR_CODE',
            'is_verified' => true,
        ]);

        try {
            // Generate Word certificate template
            $filename = $this->certificateService->generateWordCertificate($sampleStudent);
            $filepath = storage_path('app/public/' . $filename);

            if (!file_exists($filepath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Template generation failed'
                ], 500);
            }

            // Clean up temp files
            $this->certificateService->cleanupTempFiles();

            return response()->download($filepath, "Certificate_Template.docx")->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            \Log::error("Template generation error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Template generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get certificate data for API
     */
    public function getCertificateData(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:ex_students,student_id',
        ]);

        try {
            $exStudent = ExStudent::findByStudentId($request->student_id);
            
            if (!$exStudent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'student_id' => $exStudent->student_id,
                    'name' => $exStudent->name,
                    'program' => $exStudent->program,
                    'graduation_date' => $exStudent->getFormattedGraduationDate(),
                    'certificate_number' => $exStudent->certificate_number,
                    'cgpa' => $exStudent->getFormattedCgpaAttribute(),
                    'verification_url' => $exStudent->getVerificationUrl(),
                    'qr_code_url' => $exStudent->getQrCodeUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error("Certificate data retrieval error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve certificate data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk generate certificates
     */
    public function bulkGenerateCertificates(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'string|exists:ex_students,student_id',
            'format' => 'in:word,pdf',
        ]);

        $format = $request->get('format', 'word');
        $studentIds = $request->get('student_ids');
        $results = [];

        foreach ($studentIds as $studentId) {
            try {
                $exStudent = ExStudent::findByStudentId($studentId);
                
                if (!$exStudent) {
                    $results[] = [
                        'student_id' => $studentId,
                        'success' => false,
                        'message' => 'Student not found'
                    ];
                    continue;
                }

                if ($format === 'word') {
                    $filename = $this->certificateService->generateWordCertificate($exStudent);
                } else {
                    $filename = $this->certificateService->generatePdfCertificate($exStudent);
                }

                $results[] = [
                    'student_id' => $studentId,
                    'success' => true,
                    'filename' => $filename,
                    'download_url' => route('certificate.download', ['filename' => $filename])
                ];

            } catch (\Exception $e) {
                $results[] = [
                    'student_id' => $studentId,
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }

        // Clean up temp files
        $this->certificateService->cleanupTempFiles();

        return response()->json([
            'success' => true,
            'results' => $results,
            'total' => count($studentIds),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
        ]);
    }

    /**
     * Download generated certificate
     */
    public function downloadCertificate(Request $request, $filename)
    {
        $filepath = storage_path('app/public/certificates/' . $filename);
        
        if (!file_exists($filepath)) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate file not found'
            ], 404);
        }

        return response()->download($filepath)->deleteFileAfterSend(true);
    }
}
