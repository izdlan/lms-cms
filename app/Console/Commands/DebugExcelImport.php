<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class DebugExcelImport extends Command
{
    protected $signature = 'debug:excel-import {file}';
    protected $description = 'Debug Excel file structure for import';

    public function handle()
    {
        $filePath = $this->argument('file');
        
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("Debugging Excel file: {$filePath}");
        
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath) === TRUE) {
                // Read shared strings
                $sharedStrings = [];
                $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
                if ($sharedStringsXml) {
                    $xml = simplexml_load_string($sharedStringsXml);
                    if ($xml && isset($xml->si)) {
                        foreach ($xml->si as $si) {
                            $sharedStrings[] = (string)$si->t;
                        }
                    }
                }

                // Read worksheet
                $worksheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
                if ($worksheetXml) {
                    $xml = simplexml_load_string($worksheetXml);
                    if ($xml && isset($xml->sheetData->row)) {
                        $rows = [];
                        foreach ($xml->sheetData->row as $row) {
                            $rowData = [];
                            if (isset($row->c)) {
                                foreach ($row->c as $cell) {
                                    $value = '';
                                    if (isset($cell->v)) {
                                        $cellValue = (string)$cell->v;
                                        if (isset($cell['t']) && $cell['t'] == 's') {
                                            $value = isset($sharedStrings[$cellValue]) ? $sharedStrings[$cellValue] : '';
                                        } else {
                                            $value = $cellValue;
                                        }
                                    }
                                    $rowData[] = $value;
                                }
                            }
                            $rows[] = $rowData;
                        }

                        $this->info("Total rows found: " . count($rows));
                        
                        // Show first 10 rows
                        for ($i = 0; $i < min(10, count($rows)); $i++) {
                            $this->line("Row " . ($i + 1) . ": " . json_encode($rows[$i]));
                        }
                        
                        if (count($rows) > 10) {
                            $this->line("... and " . (count($rows) - 10) . " more rows");
                        }
                    }
                }
                $zip->close();
            } else {
                $this->error("Could not open Excel file");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

