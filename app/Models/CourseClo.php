<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClo extends Model
{
    protected $fillable = [
        'subject_code',
        'clo_code',
        'description',
        'mqf_alignment',
        'order'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    public function topics()
    {
        return $this->hasMany(CourseTopic::class, 'subject_code', 'subject_code')
                    ->where('clo_code', $this->clo_code);
    }
}

