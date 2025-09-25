<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging Student Data Extraction...\n";
echo "===================================\n\n";

$filePath = 'C:/xampp/htdocs/LMS_Olympia/storage/app/students/Enrollment OEM.xlsx';

if (!file_exists($filePath)) {
    echo "File not found: $filePath\n";
    exit(1);
}

// Use the XlsxImportService to get raw data
$service = new App\Services\XlsxImportService();

// We need to access the private method, so let's use reflection
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('importFromXlsx');
$method->setAccessible(true);

// Let's create a simple test to see what data is being extracted
echo "Testing data extraction from Excel file...\n\n";

try {
    $zip = new ZipArchive();
    if ($zip->open($filePath) === TRUE) {
        
        // Get list of available sheets
        $workbookXml = $zip->getFromName('xl/workbook.xml');
        $sheetsToProcess = [];
        if ($workbookXml) {
            $workbook = simplexml_load_string($workbookXml);
            if (isset($workbook->sheets->sheet)) {
                $targetSheets = [11, 12, 13, 14, 15, 16, 17];
                foreach ($workbook->sheets->sheet as $sheet) {
                    $sheetName = (string)$sheet['name'];
                    $sheetId = (string)$sheet['sheetId'];
                    
                    if (in_array($sheetId, $targetSheets)) {
                        $sheetsToProcess[] = [
                            'name' => $sheetName,
                            'id' => $sheetId,
                            'file' => 'xl/worksheets/sheet' . $sheetId . '.xml'
                        ];
                    }
                }
            }
        }
        
        echo "Found " . count($sheetsToProcess) . " sheets to process:\n";
        foreach ($sheetsToProcess as $sheet) {
            echo "- {$sheet['name']} (ID: {$sheet['id']})\n";
        }
        echo "\n";
        
        // Process first sheet to see sample data
        if (!empty($sheetsToProcess)) {
            $firstSheet = $sheetsToProcess[0];
            echo "Processing first sheet: {$firstSheet['name']}\n";
            
            $worksheetXml = $zip->getFromName($firstSheet['file']);
            if ($worksheetXml) {
                $xml = simplexml_load_string($worksheetXml);
                if ($xml && isset($xml->sheetData->row)) {
                    $rows = [];
                    foreach ($xml->sheetData->row as $row) {
                        $rowData = [];
                        foreach ($row->c as $cell) {
                            $value = (string)$cell->v;
                            $rowData[] = $value;
                        }
                        $rows[] = $rowData;
                    }
                    
                    echo "Found " . count($rows) . " rows in sheet\n";
                    
                    // Show first 10 rows
                    echo "\nFirst 10 rows of data:\n";
                    for ($i = 0; $i < min(10, count($rows)); $i++) {
                        echo "Row " . ($i + 1) . ": " . implode(' | ', array_slice($rows[$i], 0, 5)) . "\n";
                    }
                }
            }
        }
        
        $zip->close();
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDebug completed!\n";

