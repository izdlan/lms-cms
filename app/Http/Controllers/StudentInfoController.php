<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class StudentInfoController extends Controller
{
    /**
     * Sanitize filename to ensure it's safe for file system
     */
    private function sanitizeFileName($name)
    {
        // Replace spaces with underscores
        $name = str_replace(' ', '_', $name);
        // Remove special characters except underscores, hyphens, and alphanumeric
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);
        return $name;
    }

    /**
     * Get program abbreviation from full program name
     */
    private function getProgramAbbreviation($programName)
    {
        if (empty($programName)) {
            return 'Unknown';
        }

        $program = strtoupper($programName);
        
        // Check for common abbreviations in the program name
        if (preg_match('/(\b\w*E?MBA\w*\b)/i', $program, $matches)) {
            return strtoupper($matches[1]);
        }
        if (preg_match('/(\b\w*E?BBA\w*\b)/i', $program, $matches)) {
            return strtoupper($matches[1]);
        }
        if (preg_match('/(\b\w*E?DBA\w*\b)/i', $program, $matches)) {
            return strtoupper($matches[1]);
        }
        
        // If no abbreviation found, extract first letters or use first 4 characters
        $abbr = preg_replace('/[^A-Z]/', '', $program);
        return !empty($abbr) ? substr($abbr, 0, 4) : 'PROG';
    }

    /**
     * Generate PDF for a single student's information
     */
    public function generateStudentInfoPdf($studentId)
    {
        try {
            $student = User::where('role', 'student')
                          ->where('student_id', $studentId)
                          ->first();

            if (!$student) {
                return response()->json(['error' => 'Student not found'], 404);
            }

            Log::info('Generating student info PDF', [
                'student_id' => $student->student_id,
                'student_name' => $student->name
            ]);

            // Prepare data for PDF
            $data = [
                'student' => $student,
                'lms_link' => 'https://lms.olympia-education.com',
                'username' => $student->ic_passport ?? $student->ic ?? 'Not Available',
                'password' => $student->ic_passport ?? $student->ic ?? 'Not Available',
                'webmail_email' => $student->student_email ?? $student->email ?? 'Not Available',
            ];

            // Generate PDF
            $pdf = Pdf::loadView('student-info.pdf-template', $data);
            $pdf->setPaper('A4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'Arial',
                'isPhpEnabled' => true,
                'dpi' => 150,
                'defaultPaperSize' => 'a4',
                'isFontSubsettingEnabled' => true,
            ]);

            // Create filename with program, student name, and ID
            $programAbbr = $this->getProgramAbbreviation($student->programme_name ?? 'Unknown');
            $sanitizedName = $this->sanitizeFileName($student->name);
            $fileName = $programAbbr . '_' . $sanitizedName . '_' . $student->student_id . '.pdf';

            return $pdf->download($fileName);

        } catch (\Exception $e) {
            Log::error('Student info PDF generation failed', [
                'error' => $e->getMessage(),
                'student_id' => $studentId,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDFs for multiple students (bulk generation)
     */
    public function generateBulkStudentInfoPdf(Request $request)
    {
        try {
            $studentIds = $request->input('student_ids', []);
            $downloadAll = $request->input('download_all', false);
            
            // If download_all is true or no specific IDs provided, get all students
            if ($downloadAll || empty($studentIds)) {
                $students = User::where('role', 'student')
                              ->orderBy('student_id')
                              ->get();
                Log::info('Generating PDFs for ALL students', [
                    'count' => $students->count()
                ]);
            } else {
                $students = User::where('role', 'student')
                              ->whereIn('student_id', $studentIds)
                              ->orderBy('student_id')
                              ->get();
                
                if ($students->isEmpty()) {
                    return response()->json(['error' => 'No students found'], 404);
                }
                
                Log::info('Generating bulk student info PDFs', [
                    'count' => $students->count(),
                    'student_ids' => $studentIds
                ]);
            }

            // Create temporary directory for individual PDFs
            $tempDir = storage_path('app/temp/student_info_' . time());
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $pdfFiles = [];

            // Generate individual PDFs
            foreach ($students as $student) {
                $data = [
                    'student' => $student,
                    'lms_link' => 'https://lms.olympia-education.com',
                    'username' => $student->ic_passport ?? $student->ic ?? 'Not Available',
                    'password' => $student->ic_passport ?? $student->ic ?? 'Not Available',
                    'webmail_email' => $student->student_email ?? $student->email ?? 'Not Available',
                ];

                $pdf = Pdf::loadView('student-info.pdf-template', $data);
                $pdf->setPaper('A4', 'portrait');
                $pdf->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Arial',
                    'isPhpEnabled' => true,
                    'dpi' => 150,
                    'defaultPaperSize' => 'a4',
                    'isFontSubsettingEnabled' => true,
                ]);
                
                // Create filename with program, student name, and ID
                $programAbbr = $this->getProgramAbbreviation($student->programme_name ?? 'Unknown');
                $sanitizedName = $this->sanitizeFileName($student->name);
                $fileName = $programAbbr . '_' . $sanitizedName . '_' . $student->student_id . '.pdf';
                $filePath = $tempDir . '/' . $fileName;
                
                $pdf->save($filePath);
                $pdfFiles[] = $filePath;
            }

            // Create ZIP file
            $zipFileName = 'student_info_bulk_' . time() . '.zip';
            $zipPath = storage_path('app/temp/' . $zipFileName);

            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
                foreach ($pdfFiles as $pdfFile) {
                    $zip->addFile($pdfFile, basename($pdfFile));
                }
                $zip->close();

                // Clean up individual PDF files
                foreach ($pdfFiles as $pdfFile) {
                    unlink($pdfFile);
                }
                rmdir($tempDir);

                return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
            } else {
                return response()->json(['error' => 'Failed to create ZIP file'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Bulk student info PDF generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Bulk PDF generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show student info PDF generation page
     */
    public function index()
    {
        $students = User::where('role', 'student')
                       ->orderBy('student_id')
                       ->paginate(20);

        return view('admin.student-info.index', compact('students'));
    }

    /**
     * Preview student info (for testing)
     */
    public function preview($studentId)
    {
        $student = User::where('role', 'student')
                      ->where('student_id', $studentId)
                      ->first();

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $data = [
            'student' => $student,
            'lms_link' => 'https://lms.olympia-education.com',
            'username' => $student->ic_passport ?? $student->ic ?? 'Not Available',
            'password' => $student->ic_passport ?? $student->ic ?? 'Not Available',
            'webmail_email' => $student->student_email ?? $student->email ?? 'Not Available',
        ];

        return view('student-info.pdf-template', $data);
    }
}
