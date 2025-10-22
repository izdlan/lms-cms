<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentCertificate;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class StudentCertificateController extends Controller
{
    /**
     * Show the certificate generation page
     */
    public function index()
    {
        $certificates = StudentCertificate::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.student-certificates.index', compact('certificates'));
    }

    /**
     * Show the form to generate certificates from Excel
     */
    public function create()
    {
        return view('admin.student-certificates.create');
    }

    /**
     * Generate certificates from Excel file
     */
    public function generateFromExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            // Ensure temp directory exists
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Get the uploaded file
            $file = $request->file('excel_file');
            
            // Create a unique filename
            $filename = time() . '_' . $file->getClientOriginalName();
            $fullPath = $tempDir . DIRECTORY_SEPARATOR . $filename;
            
            // Move the file to temp directory
            $file->move($tempDir, $filename);

            // Debug: Log the file path
            Log::info('Excel file processing', [
                'original_name' => $file->getClientOriginalName(),
                'filename' => $filename,
                'full_path' => $fullPath,
                'file_exists' => file_exists($fullPath),
                'file_size' => file_exists($fullPath) ? filesize($fullPath) : 0,
                'temp_dir' => $tempDir,
                'temp_dir_exists' => file_exists($tempDir)
            ]);

            // Check if file exists
            if (!file_exists($fullPath)) {
                throw new \Exception("Uploaded file not found at: {$fullPath}");
            }

            // Read Excel file
            try {
                $data = Excel::toArray([], $fullPath);
            } catch (\Exception $e) {
                Log::error('Excel import failed', [
                    'file_path' => $fullPath,
                    'error' => $e->getMessage()
                ]);
                throw new \Exception("Failed to read Excel file: " . $e->getMessage());
            }
            
            if (empty($data) || empty($data[0])) {
                return back()->with('error', 'Excel file is empty or invalid.');
            }

            // Get the first sheet
            $rows = $data[0];
            $header = array_shift($rows); // Remove header row

            // Find the name column (look for common name column headers)
            $nameColumnIndex = $this->findNameColumn($header);
            
            if ($nameColumnIndex === false) {
                return back()->with('error', 'Could not find a name column in the Excel file. Please ensure your Excel has a column with names.');
            }

            $generatedCount = 0;
            $errors = [];

            // Process each row
            foreach ($rows as $index => $row) {
                if (empty($row[$nameColumnIndex])) {
                    continue; // Skip empty rows
                }

                $studentName = trim($row[$nameColumnIndex]);
                
                if (empty($studentName)) {
                    continue;
                }

                try {
                    // Generate certificate for this student
                    $certificate = $this->generateCertificate($studentName);
                    $generatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . " ({$studentName}): " . $e->getMessage();
                    Log::error("Certificate generation failed for {$studentName}", [
                        'error' => $e->getMessage(),
                        'row' => $index + 2
                    ]);
                }
            }

            // Clean up temp file
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $message = "Successfully generated {$generatedCount} certificates.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }

            return back()->with('success', $message)
                        ->with('errors', $errors);

        } catch (\Exception $e) {
            Log::error('Excel certificate generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to process Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Generate certificate for a single student
     */
    private function generateCertificate($studentName)
    {
        // Check if certificate already exists
        $existingCertificate = StudentCertificate::where('student_name', $studentName)
            ->where('template_name', 'E-Certs - Class RM')
            ->first();

        if ($existingCertificate) {
            throw new \Exception("Certificate already exists for {$studentName}");
        }

        // Template path
        $templatePath = storage_path('app/templates/E-Certs - Class RM .docx');
        
        if (!File::exists($templatePath)) {
            throw new \Exception('Certificate template not found. Please ensure the template is in storage/app/templates/');
        }

        // Create certificate record
        $certificate = StudentCertificate::create([
            'student_name' => $studentName,
            'certificate_number' => StudentCertificate::generateCertificateNumber(),
            'template_name' => 'E-Certs - Class RM',
            'status' => 'generated',
            'generated_at' => now(),
            'metadata' => [
                'generated_by' => Auth::check() ? Auth::id() : null,
                'generation_method' => 'excel_import'
            ]
        ]);

        try {
            // Process Word template
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Replace placeholder with student name
            $templateProcessor->setValue('Student name', $studentName);
            
            // Generate output filename
            $outputFileName = 'certificate_' . $certificate->id . '_' . time() . '.docx';
            $outputPath = 'student-certificates/' . $outputFileName;
            $fullOutputPath = storage_path('app/public/' . $outputPath);
            
            // Ensure directory exists
            if (!File::exists(dirname($fullOutputPath))) {
                File::makeDirectory(dirname($fullOutputPath), 0755, true);
            }
            
            // Save the processed document
            $templateProcessor->saveAs($fullOutputPath);
            
            // Update certificate with file path
            $certificate->update([
                'file_path' => $outputPath
            ]);

            return $certificate;

        } catch (\Exception $e) {
            // Delete the certificate record if file generation failed
            $certificate->delete();
            throw $e;
        }
    }

    /**
     * Find the name column in Excel header
     */
    private function findNameColumn($header)
    {
        $nameColumns = ['name', 'student_name', 'student name', 'full_name', 'full name', 'nama', 'nama pelajar'];
        
        foreach ($header as $index => $column) {
            $columnLower = strtolower(trim($column));
            if (in_array($columnLower, $nameColumns)) {
                return $index;
            }
        }
        
        // If no exact match, look for columns containing 'name'
        foreach ($header as $index => $column) {
            if (stripos($column, 'name') !== false) {
                return $index;
            }
        }
        
        return false;
    }

    /**
     * Download a certificate
     */
    public function download($id)
    {
        $certificate = StudentCertificate::findOrFail($id);
        
        if (!$certificate->file_path || !File::exists(storage_path('app/public/' . $certificate->file_path))) {
            return back()->with('error', 'Certificate file not found.');
        }

        // Mark as downloaded
        $certificate->markAsDownloaded();

        return response()->download(
            storage_path('app/public/' . $certificate->file_path),
            $certificate->student_name . '_certificate.docx'
        );
    }

    /**
     * View a certificate
     */
    public function view($id)
    {
        $certificate = StudentCertificate::findOrFail($id);
        
        if (!$certificate->file_path || !File::exists(storage_path('app/public/' . $certificate->file_path))) {
            return back()->with('error', 'Certificate file not found.');
        }

        return response()->file(storage_path('app/public/' . $certificate->file_path));
    }

    /**
     * Delete a certificate
     */
    public function destroy($id)
    {
        $certificate = StudentCertificate::findOrFail($id);
        
        // Delete file if exists
        if ($certificate->file_path && File::exists(storage_path('app/public/' . $certificate->file_path))) {
            File::delete(storage_path('app/public/' . $certificate->file_path));
        }
        
        $certificate->delete();
        
        return back()->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Bulk download certificates
     */
    public function bulkDownload(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:student_certificates,id',
            'format' => 'required|in:word,pdf'
        ]);

        // Allow longer execution for large batches
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $certificates = StudentCertificate::whereIn('id', $request->certificate_ids)->get();
        $format = $request->input('format', 'word');
        
        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates selected.');
        }

        // Create a zip file
        $zipFileName = 'certificates_' . now()->format('Y-m-d_H-i-s') . '_' . strtoupper($format) . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Ensure temp directory exists
        if (!File::exists(dirname($zipPath))) {
            File::makeDirectory(dirname($zipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return back()->with('error', 'Cannot create zip file.');
        }

        $addedFiles = 0;

        if ($format === 'pdf') {
            // Batch convert all Word files in a single LibreOffice call to avoid repeated cold starts
            $libreOfficePath = 'C:\\Program Files\\LibreOffice\\program\\soffice.exe';
            $outputDir = storage_path('app/temp');
            if (!File::exists($outputDir)) {
                File::makeDirectory($outputDir, 0755, true);
            }

            $sourceFiles = [];
            foreach ($certificates as $certificate) {
                if ($certificate->file_path && File::exists(storage_path('app/public/' . $certificate->file_path))) {
                    $sourceFiles[] = storage_path('app/public/' . $certificate->file_path);
                }
            }

            if (!empty($sourceFiles)) {
                // Build a single command: soffice --headless --convert-to pdf --outdir "outputDir" "file1" "file2" ...
                $quotedFiles = array_map(function ($p) { return '"' . $p . '"'; }, $sourceFiles);
                $command = sprintf(
                    '"%s" --headless --convert-to pdf --outdir "%s" %s',
                    $libreOfficePath,
                    $outputDir,
                    implode(' ', $quotedFiles)
                );

                $output = [];
                $returnCode = 0;
                exec($command, $output, $returnCode);

                // After batch conversion, add PDFs to the zip
                foreach ($certificates as $certificate) {
                    if (!$certificate->file_path) { continue; }
                    $sourcePath = storage_path('app/public/' . $certificate->file_path);
                    $expectedPdf = $outputDir . DIRECTORY_SEPARATOR . pathinfo($sourcePath, PATHINFO_FILENAME) . '.pdf';
                    $fileName = $certificate->student_name . '_certificate.pdf';

                    if (File::exists($expectedPdf)) {
                        $zip->addFile($expectedPdf, $fileName);
                        $addedFiles++;
                    } else {
                        // Fallback: try to locate by pattern
                        $pattern = $outputDir . DIRECTORY_SEPARATOR . pathinfo($sourcePath, PATHINFO_FILENAME) . '*.pdf';
                        $candidates = glob($pattern);
                        if (!empty($candidates)) {
                            usort($candidates, function($a, $b) { return filemtime($b) <=> filemtime($a); });
                            $zip->addFile($candidates[0], $fileName);
                            $addedFiles++;
                        }
                    }
                }
            }
        } else {
            // Word format: just add the docx files
            foreach ($certificates as $certificate) {
                if ($certificate->file_path && File::exists(storage_path('app/public/' . $certificate->file_path))) {
                    $sourcePath = storage_path('app/public/' . $certificate->file_path);
                    $fileName = $certificate->student_name . '_certificate.docx';
                    $zip->addFile($sourcePath, $fileName);
                    $addedFiles++;
                }
            }
        }

        $zip->close();

        if ($addedFiles === 0) {
            return back()->with('error', 'No certificate files found to download.');
        }

        // Mark certificates as downloaded
        $certificates->each(function($certificate) {
            $certificate->markAsDownloaded();
        });

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Convert Word document to PDF using LibreOffice
     */
    private function convertWordToPdf($wordPath, $studentName)
    {
        try {
            // LibreOffice writes the PDF using the source DOCX base name
            // into the provided output directory. We therefore compute the
            // expected path from the input Word file name rather than a
            // random name here.
            $outputDir = storage_path('app/temp');
            $expectedPdfName = pathinfo($wordPath, PATHINFO_FILENAME) . '.pdf';
            $expectedPdfPath = $outputDir . DIRECTORY_SEPARATOR . $expectedPdfName;
            
            // Ensure temp directory exists
            if (!File::exists($outputDir)) {
                File::makeDirectory($outputDir, 0755, true);
            }

            // LibreOffice executable path
            $libreOfficePath = 'C:\Program Files\LibreOffice\program\soffice.exe';
            
            // Check if LibreOffice exists
            if (!file_exists($libreOfficePath)) {
                Log::error('LibreOffice not found at expected path', [
                    'expected_path' => $libreOfficePath,
                    'word_path' => $wordPath,
                    'student_name' => $studentName
                ]);
                return null;
            }

            // Prepare the command
            $command = sprintf(
                '"%s" --headless --convert-to pdf --outdir "%s" "%s"',
                $libreOfficePath,
                $outputDir,
                $wordPath
            );

            // Execute the conversion command
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            // Check if conversion was successful
            if ($returnCode === 0 && file_exists($expectedPdfPath)) {
                Log::info('Word to PDF conversion successful', [
                    'word_path' => $wordPath,
                    'pdf_path' => $expectedPdfPath,
                    'student_name' => $studentName
                ]);
                
                return $expectedPdfPath;
            } else {
                // Fallback: try to find a freshly created PDF in the output directory
                $pattern = $outputDir . DIRECTORY_SEPARATOR . pathinfo($wordPath, PATHINFO_FILENAME) . '*.pdf';
                $candidates = glob($pattern);
                if (!empty($candidates)) {
                    // Pick the most recent candidate
                    usort($candidates, function($a, $b) { return filemtime($b) <=> filemtime($a); });
                    return $candidates[0];
                }
                Log::error('Word to PDF conversion failed', [
                    'command' => $command,
                    'return_code' => $returnCode,
                    'output' => $output,
                    'word_path' => $wordPath,
                    'student_name' => $studentName
                ]);
                
                return null;
            }
            
        } catch (\Exception $e) {
            Log::error('Word to PDF conversion exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'word_path' => $wordPath,
                'student_name' => $studentName
            ]);
            
            return null;
        }
    }

    /**
     * Bulk delete certificates
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'certificate_ids' => 'required|array',
            'certificate_ids.*' => 'exists:student_certificates,id'
        ]);

        $certificates = StudentCertificate::whereIn('id', $request->certificate_ids)->get();
        
        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates selected.');
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($certificates as $certificate) {
            try {
                // Delete file if exists
                if ($certificate->file_path && File::exists(storage_path('app/public/' . $certificate->file_path))) {
                    File::delete(storage_path('app/public/' . $certificate->file_path));
                }
                
                $certificate->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $errors[] = "Failed to delete certificate for {$certificate->student_name}: " . $e->getMessage();
                Log::error('Bulk delete certificate failed', [
                    'certificate_id' => $certificate->id,
                    'student_name' => $certificate->student_name,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $message = "Successfully deleted {$deletedCount} certificates.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " errors occurred.";
        }

        return back()->with('success', $message)
                    ->with('errors', $errors);
    }
}