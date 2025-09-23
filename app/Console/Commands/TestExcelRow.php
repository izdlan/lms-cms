<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class TestExcelRow extends Command
{
    protected $signature = 'test:excel-row {file}';
    protected $description = 'Test specific Excel row structure';

    public function handle()
    {
        $file = $this->argument('file');
        
        try {
            $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
                public function array(array $array): array
                {
                    return $array;
                }
            }, $file, 'DHU LMS');
            
            if (isset($data[0])) {
                $this->info('Row 7 (Headers): ' . json_encode($data[0][6]));
                $this->info('Row 8 (Data): ' . json_encode($data[0][7]));
                $this->info('Row 9 (Data): ' . json_encode($data[0][8]));
                $this->info('Row 10 (Data): ' . json_encode($data[0][9]));
            }
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}