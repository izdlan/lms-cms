<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;

class DebugExcel extends Command
{
    protected $signature = 'debug:excel {file}';
    protected $description = 'Debug Excel file structure';

    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error('File not found: '.$file);
            return 1;
        }

        $this->info('Reading Excel file: ' . $file);
        
        try {
            // Read all sheets
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file);
            
            $this->info('Total sheets: ' . count($data));
            
            if (isset($data[0])) {
                $this->info('First sheet has ' . count($data[0]) . ' rows');
                
                // Show first 10 rows
                for ($i = 0; $i < min(10, count($data[0])); $i++) {
                    $this->info("Row " . ($i + 1) . ": " . json_encode($data[0][$i]));
                }
            }
            
        } catch (\Exception $e) {
            $this->error('Error reading Excel: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}