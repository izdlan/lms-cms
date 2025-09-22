<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SimpleExcelDebug extends Command
{
    protected $signature = 'debug:simple-excel {file}';
    protected $description = 'Simple Excel file debug without Laravel Excel';

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Reading Excel file: {$filePath}");
        
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheetNames = $spreadsheet->getSheetNames();
            
            $this->info("Available sheets: " . implode(', ', $sheetNames));
            
            foreach ($sheetNames as $sheetName) {
                $this->info("\n=== Sheet: {$sheetName} ===");
                $sheet = $spreadsheet->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                $this->info("Dimensions: {$highestColumn}{$highestRow} (rows: {$highestRow}, cols: " . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn) . ")");
                
                // Show first 10 rows
                for ($row = 1; $row <= min(10, $highestRow); $row++) {
                    $rowData = [];
                    for ($col = 1; $col <= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn); $col++) {
                        $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getCalculatedValue();
                        $rowData[] = $cellValue;
                    }
                    $this->info("Row {$row}: " . json_encode($rowData));
                }
            }
            
        } catch (\Exception $e) {
            $this->error("Error reading Excel: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
