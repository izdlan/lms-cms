<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentCertificate extends Model
{
    protected $fillable = [
        'student_name',
        'certificate_number',
        'template_name',
        'file_path',
        'status',
        'generated_at',
        'downloaded_at',
        'metadata'
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'metadata' => 'array'
    ];

    /**
     * Generate a unique certificate number
     */
    public static function generateCertificateNumber()
    {
        $prefix = 'CERT';
        $year = date('Y');
        $month = date('m');
        
        // Get the last certificate number for this month
        $lastCertificate = self::where('certificate_number', 'like', $prefix . $year . $month . '%')
            ->orderBy('certificate_number', 'desc')
            ->first();
        
        if ($lastCertificate) {
            $lastNumber = (int) substr($lastCertificate->certificate_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Mark certificate as downloaded
     */
    public function markAsDownloaded()
    {
        $this->update([
            'status' => 'downloaded',
            'downloaded_at' => now()
        ]);
    }
}
